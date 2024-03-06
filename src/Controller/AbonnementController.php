<?php

namespace App\Controller;

use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Entity\Abonnement;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


#[Route('/abonnement')]
class AbonnementController extends AbstractController
{
    #[Route('/', name: 'abonnement_index', methods: ['GET'])]
    public function index(AbonnementRepository $abonnementRepository): Response
    {
        return $this->render('abonnement/ListAbonnement.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }

#[Route('/NewAbonnement', name: 'abonnement_new')]
public function new(Request $request): Response
{
    $abonnement = new Abonnement();

    // Set DateC to the current date
    $dateC = new DateTime();
    $abonnement->setDateC($dateC);

    // Set DateE to DateC + 1 year
    $dateE = clone $dateC;
    $dateE->modify('+1 year');
    $abonnement->setDateE($dateE);

    $form = $this->createForm(AbonnementType::class, $abonnement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($abonnement);
        $entityManager->flush();

        $this->addFlash('success', 'Abonnement ajoutée avec succès.');
        return $this->redirectToRoute('abonnement_index');
    }

    return $this->render('abonnement/NewAbonnement.html.twig', [
        'abonnement' => $abonnement,
        'form' => $form->createView(),
    ]);
}


   /* #[Route('/{id}', name: 'abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }*/

    #[Route('/{id}/edit', name: 'abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('abonnement_index');
        }

        return $this->render('abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement): Response
    {
        if ($this->isCsrfTokenValid('delete' . $abonnement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('abonnement_index');
    }

    #[Route('/NewAbonnementcart', name: 'abonnement_newcart')]
    public function newCA(Request $request,OffreRepository $offreRepository, ClientRepository $clientRepository, SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);

        foreach ($panier as $id){
            $abonnement = new Abonnement();

            // Set DateC to the current date
            $dateC = new DateTime();
            $abonnement->setDateC($dateC);

            // Set DateE to DateC + 1 year
            $dateE = clone $dateC;
            $dateE->modify('+1 year');
            $abonnement->setDateE($dateE);
            
            $client=$clientRepository->find(6);
            $abonnement->setClient($client);
            $offre=$offreRepository->find($id);
            $abonnement->setOffre($offre);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($abonnement);
            $entityManager->flush();
        }


        $smsController = new \App\Controller\SMSController();
        $smsController->index('HI Admin user :get payment success');
        return $this->redirectToRoute('CartDeleteAll');

    }
}
