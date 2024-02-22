<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        return $this->render('back.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }

    #[Route('/work', name: 'work')]
    public function dashboard_profile(): Response
    {
        $user = $this->getUser();

        // Access user data, for example:
        $username = $user->getUsername();
        $email = $user->getEmail();

        // Pass user data to the template
        return $this->render('user/work.html.twig', [
            'username' => $username,
            'email' => $email,
            'user' =>   $user,
              ]);
    }

}
