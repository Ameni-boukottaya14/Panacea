<?php

namespace App\Controller;

use App\Form\ClientType;
use App\Form\SignupType;
use App\Form\Type;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/', name: 'client_list')]
    public function list(ClientRepository $clientRepository): Response
    {
        return $this->render('client/listClient.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    #[Route('/add', name: 'client_new')]
    public function new(Request $request): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();

           return $this->redirectToRoute('client_list');
        }

        return $this->render('client/addClient.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }
   
    #[Route('/{id}/edit', name: 'client_edit')]
    public function edit($id,ClientRepository $repository,Request  $request, ManagerRegistry $managerRegistry)
    {
        $client= $repository->find($id) ;
       $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_list');
        }
       return $this->render('client/editClient.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
       ]);
    }
    #[Route('/{id}/delete', name: 'client_delete')]
    public function delete($id,ClientRepository $repository,Request  $request, ManagerRegistry $managerRegistry): Response
    {

        $client= $repository->find($id);
        $em = $managerRegistry->getManager();
        $em->remove($client);
        $em->flush();

        return $this->redirectToRoute('client_list');
    }
    //AUTHENTIFICATION

//signup
#[Route('/signup', name: 'client_signup')]
public function signup(Request $request): Response
{
    $client = new Client();
    $form = $this->createForm(SignupType::class, $client);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($client);
        $entityManager->flush();

       return $this->redirectToRoute('front');
    }

    return $this->render('Authentification/login.html.twig', [
        'client' => $client,
        'form' => $form->createView(),
    ]);
}

//login
}
