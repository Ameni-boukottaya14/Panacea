<?php

namespace App\Controller;

use App\Form\ClientType;
use App\Form\SignupType;
use App\Form\LoginType;
use App\Form\Type;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
#[Route('/connexion', name: 'client_connexion')]
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

    return $this->render('Auth/login.html.twig', [
        'client' => $client,
        'form' => $form->createView(),
    ]);
}
//AUTHENTIFICATION
//signup
 #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une instance de l'entité Utilisateur
        $client = new Client(); // Fixed lowercase 'client'

        // Créer le formulaire à partir de la classe RegisterType
        $form = $this->createForm(RegisterType::class, $client, [
            'csrf_protection' => false,
        ]);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire est valide, faire quelque chose avec les données
            // Persist the entity using EntityManager
            $entityManager->persist($client);
            $entityManager->flush();

            // Rediriger l'utilisateur vers la page de connexion
          return $this->redirectToRoute('front', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->render('Auth/register.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    //login 
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
{
    $form = $this->createForm(LoginType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();

        // Retrieve email and password from the form
        $email = $data['email'];
        $password = $data['motdepasse'];

        // Find the client by email
        $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $email]);

        // If client found and password matches
        if ($client) {
            // Check if the password matches using Symfony's security system
            if ($passwordEncoder->isPasswordValid($client, $password)) {
                // Redirect to main interface passing client information
                return $this->redirectToRoute('front', ['id' => $client->getId()]);
            } else {
                // Password does not match, handle accordingly
                $this->addFlash('error', 'Invalid email or password');
                return $this->redirectToRoute('login');
            }
        } else {
            // Client not found, handle accordingly
            $this->addFlash('error', 'Invalid email or password');
            return $this->redirectToRoute('login');
        }
    }

    return $this->render('Auth/login.html.twig', [
        'form' => $form->createView(),
    ]);
}
    
}