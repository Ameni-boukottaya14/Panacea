<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Consultation;
use App\Entity\Medecin;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/consultation')]
class ConsultationController extends AbstractController
{
    #[Route('/', name: 'app_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultationRepository->findAll(),
        ]);
    }

    #[Route('/list/{id}', name: 'app_consultation_list', methods: ['GET'])]
    public function list(Client $client, ConsultationRepository $consultationRepository): Response
    {
        return $this->render('consultation/frontindex.html.twig', [
            'consultations' => $consultationRepository->findBy(['Client' => $client]),
        ]);
    }

    #[Route('/new/{id}', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(Medecin $medecin, Request $request, EntityManagerInterface $entityManager): Response
    {
        $consultation = new Consultation();
        $id_client = 1;
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);
        $client = $entityManager->getRepository(Client::class)->findOneBy(['id' => $id_client]);

        if ($form->isSubmitted() && $form->isValid()) {
            $consultation->setPrix(70);
            $consultation->setMedecin($medecin);
            $consultation->setClient($client);
            $entityManager->persist($consultation);
            $entityManager->flush();

            return $this->redirectToRoute('app_medecin_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('consultation/new.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation): Response
    {
        return $this->render('consultation/show.html.twig', [
            'consultation' => $consultation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('consultation/edit.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_delete', methods: ['POST'])]
    public function delete(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$consultation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($consultation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
    }
}
