<?php

namespace App\Controller;

use App\Entity\Ordonnance;
use App\Form\OrdonnanceType;
use App\Repository\OrdonnanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrdonnanceController extends AbstractController
{
    #[Route('/ordonnance', name: 'app_ordonnance')]
    public function index(): Response
    {
        return $this->render('ordonnance/index.html.twig', [
            'controller_name' => 'OrdonnanceController',
        ]);
    }

    #[Route('/ordonnance/list', name: 'ordonnance_list')]
    public function list(OrdonnanceRepository $ordonnanceRepository): Response
    {
        return $this->render('ordonnance/listOrdonnance.html.twig', [
            'ordonnances' => $ordonnanceRepository->findAll(),
        ]);
    }

    #[Route('/ordonnance_front', name: 'ordonnance_front')]
    public function listOrdonnance(OrdonnanceRepository $ordonnanceRepository): Response
    {
        return $this->render('front/frontOrdonnance.html.twig', [
            'ordonnances' => $ordonnanceRepository->findAll(),
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
