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
use Symfony\Component\HttpFoundation\Session\SessionInterface;



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

    #[Route('/Home', name: 'offre_Home_index')]
    public function indexH(OffreRepository $offreRepository): Response
    {
        return $this->render('base.html.twig', [
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

            $request->getSession()->getFlashBag()->add('success', 'Offre added successfully.');

            return $this->redirectToRoute('offre_index');
        }

        return $this->render('offre/NewOffre.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
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

            $request->getSession()->getFlashBag()->add('success', 'Offre Edited successfully.');

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

        $request->getSession()->getFlashBag()->add('success', 'Offre deleted successfully.');


        return $this->redirectToRoute('offre_index');
    }

    // Here starts the cart controler ^^ 
    #[Route('/cartindex', name: 'CartList')]
    public function card(SessionInterface $session, OffreRepository $OffreRepository)
    {
        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;
        foreach ($panier as $id){
            $Offre = $OffreRepository->find($id);
            $dataPanier[] = [
                "Offre" => $Offre,
            ];
            $total += $Offre ->getPrixO();
        }   

        return $this->render('cart/index.html.twig', compact("dataPanier","total"));
    }

    #[Route('/addcard/{id}', name: 'addcard')]
    public function addcard($id,Offre $Offre, SessionInterface $session)
    {
        $session->start();
        // On récupère le panier actuel
        $panier = $session->get("panier",[]);
        $index = array_search($id, $panier);

        if ($index == false) {
            $panier[]=($id);
        }
        

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        $smsController = new \App\Controller\SMSController();
        $smsController->index('HI Admin offer is added !');

        return $this->redirectToRoute('home');

    }

    #[Route('/deletecard/{id}', name: 'CartDelete')]
    public function deleteC($id,Offre $Offre, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier",[]);

        // Recherche l'index de l'élément à supprimer
        $index = array_search($id, $panier);

        if ($index !== false) {
            // Supprime l'élément du panier
            unset($panier[$index]);
            // Réindexe le tableau pour éviter les clés manquantes
            $panier = array_values($panier);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("CartList");
    }

    #[Route('/deleteallcards', name: 'CartDeleteAll')]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");

        return $this->redirectToRoute("home");
    }


}
