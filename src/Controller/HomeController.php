<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\QrCodeGenerator;
use App\Form\EditProfileType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Form\ChangePasswordType;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(OffreRepository $offreRepository): Response
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
                'offres' => $offreRepository->findAll(),
                // other variables
            ]);
        } else {
            // User is not logged in, redirect to the login page
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/profile', name: 'profile')]
    public function profile(QrCodeGenerator $qrCodeGenerator): Response
    {
        // Get the current user object
        $user = $this->getUser();

        // Generate the QR code for the user
        $qrCode = $qrCodeGenerator->createQrCode($user);

        // Render the QR code as SVG
        $qrCodeSvg = $qrCode->getString();

        // Pass user data and the SVG QR code to the template
        return $this->render('user/Profile.html.twig', [
            'user' => $user,
            'qrCode' => $qrCodeSvg,
        ]);
    }

    #[Route('/{id}/editProfile', name: 'edit_profile')]
    public function editProfile($id, UserRepository $repository, Request $request, ManagerRegistry $managerRegistry, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $repository->find($id);
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        return $this->render('user/editProfile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/change-password', name: 'change_password')]
public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
{
    // Get the current user
    $user = $this->getUser();

    // Create a form for changing the password
    $form = $this->createForm(ChangePasswordType::class);
    $form->handleRequest($request);

    // If the form is submitted and valid
    if ($form->isSubmitted() && $form->isValid()) {
        // Get the data from the form
        $formData = $form->getData();
        $currentPassword = $formData['currentPassword'];
        $newPassword = $formData['newPassword'];

        // Check if the current password matches the user's password
        if ($passwordEncoder->isPasswordValid($user, $currentPassword)) {
            // Encode and set the new password
            $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encodedPassword);

            // Persist the changes to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to the profile page with a success message
            $this->addFlash('success', 'Password changed successfully.');
            return $this->redirectToRoute('profile');
        } else {
            // If the current password is incorrect, add an error message and re-render the form
            $this->addFlash('error', 'Incorrect current password.');
            return $this->redirectToRoute('change_password');
        }
    }

    // Render the form template
    return $this->render('user/changePassword.html.twig', [
        'form' => $form->createView(),
        'user' => $user,
    ]);
}


//change nom
 #[Route('/change-name', name: 'change_name')]
public function changeName(Request $request): Response
{
    // Get the current user
    $user = $this->getUser();

    // Get the new name from the form submission
    $newName = $request->request->get('newName');

    // Update the user's name
    $user->setNom($newName);

    // Persist the changes to the database
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($user);
    $entityManager->flush();

    // Redirect to the profile page
    $this->addFlash('success', 'Nom changed successfully.');
    return $this->redirectToRoute('profile');
}
//change prenom
#[Route('/change-prenom', name: 'change_prenom')]
public function changePrenom(Request $request): Response
{
    // Get the current user
    $user = $this->getUser();

    // Get the new prenom from the form submission
    $newPrenom = $request->request->get('newPrenom');

    // Update the user's prenom
    $user->setPrenom($newPrenom);

    // Persist the changes to the database
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($user);
    $entityManager->flush();

    // Redirect to the profile page
    $this->addFlash('success', 'Prenom changed successfully.');
    return $this->redirectToRoute('profile');
}

//change telephone 
#[Route('/change-telephone', name: 'change_telephone')]
public function changeTelephone(Request $request): Response
{
    // Get the current user
    $user = $this->getUser();

    // Get the new telephone from the form submission
    $newTelephone = $request->request->get('newTelephone');

    // Update the user's telephone
    $user->setTelephone($newTelephone);

    // Persist the changes to the database
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($user);
    $entityManager->flush();

    // Redirect to the profile page
    $this->addFlash('success', 'Telephone changed successfully.');
    return $this->redirectToRoute('profile');
}




}
