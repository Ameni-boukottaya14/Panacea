<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/back', name: 'app_home_back')]
    public function indexBack(): Response
    {
        return $this->render('back.html.twig');
    }
}
