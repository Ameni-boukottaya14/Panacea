<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ReactiveType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\QrCodeGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
            $request->getSession()->getFlashBag()->add('success', 'User added successfully.');
    
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
            $request->getSession()->getFlashBag()->add('success', 'User edited successfully.');

            return $this->redirectToRoute('user_list');
        }
    
        return $this->render('user/editUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/desactiver', name: 'user_desactiver')]
    public function delete($id, UserRepository $repository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $user = $repository->find($id);
        
        // Check if user exists
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
        
        // Set status to false instead of removing the user
        $user->setStatus(true);
        
        // Persist the changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Add a success message 
        $this->addFlash('success', 'User deactivated successfully.');

        return $this->redirectToRoute('user_list');
    }

    #[Route('/{id}/desactiverProfile', name: 'user_desactiver_profile')]
    public function desactiver($id, UserRepository $repository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $user = $repository->find($id);
        
        // Check if user exists
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
        
        // Set status to false instead of removing the user
        $user->setStatus(true);
        
        // Persist the changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_logout');
    }

    #[Route('/{id}/activer', name: 'user_activer')]
    public function activer($id, UserRepository $repository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $user = $repository->find($id);
        
        // Check if user exists
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
        
        // Set status to false instead of removing the user
        $user->setStatus(false);
        $user->setRequest(false);
        // Persist the changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Add a success message
        $this->addFlash('success', 'User activated successfully.');

        return $this->redirectToRoute('user_list');
    }


    #[Route(path: '/request', name: 'user_active_request')]
public function request(Request $request, UserRepository $userRepository): Response
{
    $form = $this->createForm(ReactiveType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $email = $data['email'];

        $user = $userRepository->findOneBy(['email' => $email]);

        // Check if user exists
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Set status to true to request activation
        $user->setRequest(true);

        // Persist the changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Add a success message
        $this->addFlash('success', 'Request sent successfully.');

        return $this->redirectToRoute('app_login');
    }

    return $this->render('user/requestActive.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
  
