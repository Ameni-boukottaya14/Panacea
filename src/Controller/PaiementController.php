<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Shoppingcart;
use App\Entity\Client;
use Stripe\Stripe;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ClientRepository;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementController extends AbstractController
{
    #[Route('/paiement/{id}', name: 'app_paiement')]
    public function index($id,EntityManagerInterface $em,ManagerRegistry $doctrine): Response
    {
        $client = $em->getRepository(Client::class)->find(4);
 

        return $this->render('paiement/Pindex.html.twig', [
            'controller_name' => 'PaiementController',
            'money'=> $id,
        ]);
    }

    #[Route('/checkout/{id}}', name: 'app_checkout')]
    public function checkout($id,SessionInterface $session1,ManagerRegistry $doctrine,EntityManagerInterface $em,): Response //we go to stripe checkout url
    {
        Stripe::setApiKey('sk_test_51OokuXGbd7kl7rP9ILUGvS9YXXA9Se35ZkPjl1jWjrQpXzlFhw57fuAdUGtri6p6APwHmUaVjMiY8EAMyybdsSvE004vnayWqt');
        $client = $em->getRepository(Client::class)->find(4);
 



        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'USD',
                        'product_data' => [
                            'name' => ' Amount :',
                        ],
                        'unit_amount'  => $id*100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
 
}

#[Route('/success-url', name: 'success_url')]
public function successUrl(SessionInterface $session1,EntityManagerInterface $entityManager): Response
{
  

    return $this->render('paiement/success.html.twig', []);
}


#[Route('/cancel-url', name: 'cancel_url')]
public function cancelUrl(): Response
{
    return $this->render('paiement/cancel.html.twig', []);
}
}