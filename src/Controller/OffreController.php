<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/offre')]

class OffreController extends AbstractController
{
    
    #[Route('/', name: 'offre_index')]
    public function index(OffreRepository $offreRepository): Response
    {
        return $this->render('offre/index.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }

    
    #[Route('/new', name: 'offre_new')]
    public function new(Request $request): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('offre_index');
        }

        return $this->render('offre/NewOffre.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

   
    #[Route('/{id}', name: 'offre_show')]
    public function show(Offre $offre): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
        ]);
    }


    #[Route('/{id}/edit', name: 'offre_edit')]
    public function edit($id,OffreRepository $repository,Request  $request, ManagerRegistry $managerRegistry)
    {
        $offre= $repository->find($id) ;
       $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offre_index');
        }
       return $this->render('offre/EditOffre.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
       ]);
    } 

    #[Route('/{id}/delete', name: 'offre_delete')]
    public function delete($id,OffreRepository $repository,Request  $request, ManagerRegistry $managerRegistry): Response
    {

        $offre= $repository->find($id);
        $em = $managerRegistry->getManager();
        $em->remove($offre);
        $em->flush();

        return $this->redirectToRoute('offre_index');
    }
}
