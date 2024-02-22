<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();
if ($user) {
    // User is logged in, retrieve user data
    $username = $user->getUsername();
    $email = $user->getEmail();
    // Pass user data to the template
    return $this->render('user/HomePage.html.twig', [
        'username' => $username,
        'user' => $user,
        // other variables
    ]);
} else {
    // User is not logged in, redirect to the login page
    return $this->redirectToRoute('app_login');
}

}

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        // Get the current user object
        $user = $this->getUser();

        // Access user data, for example:
        $username = $user->getUsername();
        $email = $user->getEmail();

        // Pass user data to the template
        return $this->render('user/Profile.html.twig', [
            'username' => $username,
            'email' => $email,
            'user' =>   $user,
              ]);
    }
}
