<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\EmailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        EmailVerificationService $emailVerificationService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // Generate verification token
            $verificationToken = Uuid::v4()->toRfc4122();
            $user->setVerificationToken($verificationToken);

            // Persist user entity
            $entityManager->persist($user);
            $entityManager->flush();

            // Send email verification
            $emailVerificationService->sendVerificationEmail($user);

            // Redirect to login page after successful registration
            return $this->redirectToRoute('app_login');
        }

        // Error flash message
        $this->addFlash('error', 'User registration failed.');

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
