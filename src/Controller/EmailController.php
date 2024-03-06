<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class EmailController extends AbstractController
{
    #[Route('/email', name: 'app_email')]
    public function index(): Response
    {
        return $this->render('email/index.html.twig', [
            'controller_name' => 'EmailController',
        ]);
    }

    public function sendEmail(Request $request, \Swift_Mailer $mailer)
    {
        // handle form submission
        // ...
    
        // send email
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('boukottayaamani@gmail.com')
            ->setTo($request->request->get('email'))
            ->setBody(
                $this->renderView(
                    'emails/email.html.twig',
                    ['message' => $request->request->get('message')]
                ),
                'text/html'
            );
        $mailer->send($message);
    
        $sent = true;
    
        return $this->render('email_form.html.twig', [
            'sent' => $sent
        ]);
    }
    
}