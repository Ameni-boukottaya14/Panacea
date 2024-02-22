<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/list', name: 'user_list')]
    public function list(UserRepository $UserRepository): Response
    {
        return $this->render('User/listUser.html.twig', [
            'users' => $UserRepository->findAll(),
        ]);
    }

    
    #[Route('/add', name: 'user_add')]
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $hashedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Add success message to flash bag
            $this->addFlash('success', 'User added successfully!');
    
            return $this->redirectToRoute('user_list');
        }
    
        return $this->render('user/addUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/edit', name: 'user_edit')]
    public function edit($id, UserRepository $repository, Request $request, ManagerRegistry $managerRegistry, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $repository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $hashedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
    
            $this->getDoctrine()->getManager()->flush();
    
            return $this->redirectToRoute('user_list');
        }
    
        return $this->render('user/editUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'user_delete')]
    public function delete($id,UserRepository $repository,Request  $request, ManagerRegistry $managerRegistry): Response
    {

        $user= $repository->find($id);
        $em = $managerRegistry->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user_list');
    }

}
