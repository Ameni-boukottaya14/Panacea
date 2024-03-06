<?php

namespace App\Controller;

use App\Entity\Ordonnance;
use App\Form\OrdonnanceType;
use App\Repository\OrdonnanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PharmacieRepository;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient as NotifierRecipient;
use Endroid\QrCode\QrCode; // Import the QrCode class
use App\Service\QrCodeGenerator;

class OrdonnanceController extends AbstractController
{
    private $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    #[Route('/ordonnance', name: 'app_ordonnance')]
    public function index(): Response
    {
        return $this->render('ordonnance/index.html.twig', [
            'controller_name' => 'OrdonnanceController',
        ]);
    }

    #[Route('/ordonnance/list', name: 'ordonnance_list')]
    public function list(OrdonnanceRepository $ordonnanceRepository, PharmacieRepository $pharmacieRepository): Response
    {
        // Fetch all pharmacies and ordonnances
        $pharmacies = $pharmacieRepository->findAll();
        $ordonnances = $ordonnanceRepository->findAll();

        
        // Calculate ordonnances per pharmacy
        $ordonnancesPerPharmacy = [];
        foreach ($pharmacies as $pharmacie) {
            $pharmacyId = $pharmacie->getId();
            $ordonnancesPerPharmacy[$pharmacyId] = 0; // Initialize the count
        }

        foreach ($ordonnances as $ordonnance) {
            $pharmacyId = $ordonnance->getPharmacie()->getId();
            $ordonnancesPerPharmacy[$pharmacyId]++;
        }

        // Render the template with pharmacies, ordonnances, and ordonnancesPerPharmacy
        return $this->render('ordonnance/listOrdonnance.html.twig', [
            'pharmacies' => $pharmacies,
            'ordonnances' => $ordonnances,
            'ordonnancesPerPharmacy' => $ordonnancesPerPharmacy,
        ]);
    }

    #[Route('/ordonnance/qrlist', name: 'ordonnance_qrlist')] // Changed route name to 'ordonnance_qrlist'
    public function listOrdonnance(OrdonnanceRepository $ordonnanceRepository, QrCodeGenerator $qrCodeGenerator): Response
    {
        // Fetch all ordonnances
        $ordonnances = $ordonnanceRepository->findAll();
    
        // Initialize an array to hold QR codes for each ordonnance
        $qrCodes = [];
    
        // Generate QR code for each ordonnance
        foreach ($ordonnances as $ordonnance) {
            $qrCode = $qrCodeGenerator->createQrCode($ordonnance);
            $qrCodes[] = $qrCode->getDataUri(); // Correct method name
        }
    
        // Render the template with ordonnances and QR codes
        return $this->render('front/frontOrdonnance.html.twig', [
            'ordonnances' => $ordonnances,
            'qrCodes' => $qrCodes,
        ]);
    }


    #[Route('/ordonnance/add', name: 'ordonnance_new')]
    public function new(Request $request): Response
    {
        $ordonnance = new Ordonnance();
        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ordonnance);
            $entityManager->flush();

            $this->addFlash('success', 'Ordonnance ajoutée avec succès.');
            return $this->redirectToRoute('ordonnance_list');
        }

        return $this->render('ordonnance/addOrdonnance.html.twig', [
            'ordonnance' => $ordonnance,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ordonnance/{id}/edit', name: 'ordonnance_edit')]
    public function edit($id, OrdonnanceRepository $repository, Request $request)
    {
        $ordonnance = $repository->find($id);
        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the ordonnance state changed from "en attente" to "servis"
            $originalState = $ordonnance->getEtat();
            $newState = $ordonnance->getEtat();

            if ($originalState === 'en attente' && $newState === 'servis') {
                // Send notification
                $notification = (new Notification('Ordonnance servis', ['email']))->content('L\'ordonnance a été servis.');
                $recipient = new Recipient($this->getUser()->getEmail());
                $this->notifier->send($notification, $recipient);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Ordonnance modifiée avec succès.');
            return $this->redirectToRoute('ordonnance_list');
        }

        return $this->render('ordonnance/editOrdonnance.html.twig', [
            'ordonnance' => $ordonnance,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ordonnance/{id}/delete', name: 'ordonnance_delete')]
    public function delete($id, OrdonnanceRepository $repository): Response
    {
        $ordonnance = $repository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ordonnance);
        $entityManager->flush();
        $this->addFlash('success', 'Ordonnance supprimée avec succès.');
        return $this->redirectToRoute('ordonnance_list');
    }
}

