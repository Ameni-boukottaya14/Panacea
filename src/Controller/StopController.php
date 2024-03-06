<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StopController extends AbstractController
{
    #[Route('/stop', name: 'app_stop')]
    public function index(): Response
    {
        return $this->render('stop/index.html.twig', [
            'controller_name' => 'StopController',
        ]);
    }
}
