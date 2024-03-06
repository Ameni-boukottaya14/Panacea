<?php

namespace App\Controller;

use App\Entity\Pharmacie;
use App\Form\PharmacieType;
use App\Repository\PharmacieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PharmacieController extends AbstractController
{
    #[Route('/pharmacie', name: 'app_pharmacie')]
    public function index(): Response
    {
        return $this->render('pharmacie/index.html.twig', [
            'controller_name' => 'PharmacieController',
        ]);
    }

    #[Route('/list', name: 'pharmacie_list')]
    public function list(PharmacieRepository $pharmacieRepository): Response
    {
        // Fetch all pharmacies
        $pharmacies = $pharmacieRepository->findAll();
    
        // Calculate pharmacies per address
        $pharmaciesPerAddress = [];
        foreach ($pharmacies as $pharmacy) {
            $address = $pharmacy->getAdress();
            if (!isset($pharmaciesPerAddress[$address])) {
                $pharmaciesPerAddress[$address] = 1;
            } else {
                $pharmaciesPerAddress[$address]++;
            }
            
        }
        // Render the template with pharmacies and pharmaciesPerAddress
        return $this->render('pharmacie/listPharmacie.html.twig', [
            'pharmacies' => $pharmacies,
            'pharmaciesPerAddress' => $pharmaciesPerAddress,
        ]);
    }
    

    #[Route('/pharmacie_front', name: 'pharmacie_front')]
    public function listPharmacie(PharmacieRepository $pharmacieRepository): Response
    {
        return $this->render('front/frontPharmacie.html.twig', [
            'pharmacies' => $pharmacieRepository->findAll(),
        ]);
    }
    

    #[Route('/add', name: 'pharmacie_new')]
    public function new(Request $request): Response
    {
        $pharmacie = new Pharmacie();
        $form = $this->createForm(PharmacieType::class, $pharmacie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pharmacie);
            $entityManager->flush();

            $this->addFlash('success', 'Pharmacie ajoutée avec succès.');
            return $this->redirectToRoute('pharmacie_list');
        }

        return $this->render('pharmacie/addPharmacie.html.twig', [
            'pharmacie' => $pharmacie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'pharmacie_edit')]
    public function edit($id, PharmacieRepository $repository, Request $request)
    {
        $pharmacie = $repository->find($id);
        $form = $this->createForm(PharmacieType::class, $pharmacie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Pharmacie modifiée avec succès.');
            return $this->redirectToRoute('pharmacie_list');
        }

        return $this->render('pharmacie/editPharmacie.html.twig', [
            'pharmacie' => $pharmacie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'pharmacie_delete')]
    public function delete($id, PharmacieRepository $repository): Response
    {
        $pharmacie = $repository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($pharmacie);
        $entityManager->flush();
        return $this->redirectToRoute('pharmacie_list');
    }
  
}
