<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Consultation;
use App\Entity\Medecin;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

#[Route('/consultation')]
class ConsultationController extends AbstractController
{
    private $mailer;

    public function __construct()
    {
        // Create a transport object
        $transport = Transport::fromDsn('smtp://pro.panacea24@gmail.com:hphkmicxcabivyao@smtp.gmail.com:587');

        // Inject the transport into the mailer
        $this->mailer = new Mailer($transport);
    }

    #[Route('/index', name: 'app_consultation_index', methods: ['GET'])]
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

    #[Route('/confirm/{id}', name: 'app_consultation_confirm', methods: ['GET'])]
    public function confirm(Consultation $consultation, ConsultationRepository $consultationRepository,EntityManagerInterface $entityManager): Response
    {
        if ($consultation->getStatus() == "En Attente") {
            $consultation->setStatus("Confirmée");
            $this->sendConfirmEmail($consultation);
        }else {
            $consultation->setStatus("En Attente");
            $this->sendCancelEmail($consultation);
        }

        $entityManager->flush();
        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultationRepository->findAll(),
        ]);
    }

    #[Route('/refuse/{id}', name: 'app_consultation_refuse', methods: ['GET'])]
    public function refuse(Consultation $consultation, ConsultationRepository $consultationRepository,EntityManagerInterface $entityManager): Response
    {
            $consultation->setStatus("Refusée");
            $this->sendRefuseEmail($consultation);

        $entityManager->flush();
        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultationRepository->findAll(),
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
            $consultation->setStatus("En Attente");
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

    private function sendConfirmEmail(Consultation $cons)
    {
        $formattedDate = $cons->getDate()->format('d/m/Y H:i');
        $email = (new TemplatedEmail())
            ->from('pro.panacea24@gmail.com')
            ->to($cons->getClient()->getEmail())
            ->subject('Confirmation de la consultation')
            ->htmlTemplate('email/emailConfirmation.html.twig')
            ->context([
                'ClientName' => $cons->getClient()->getPrenom().' '.$cons->getClient()->getNom(),
                'MedecinName' => $cons->getMedecin()->getPrenom().' '.$cons->getMedecin()->getNom(),
                'ConsDate' => $formattedDate,
                'ConsPrix' => $cons->getPrix().' TND',
            ]);
            $loader = new FilesystemLoader(__DIR__.'/../../templates');
            $twigEnv = new Environment($loader);
            $twigBodyRenderer = new BodyRenderer($twigEnv);
            $twigBodyRenderer->render($email);

        $this->mailer->send($email);
    }

    private function sendCancelEmail(Consultation $cons)
    {
        $formattedDate = $cons->getDate()->format('d/m/Y H:i');
        $email = (new TemplatedEmail())
            ->from('pro.panacea24@gmail.com')
            ->to($cons->getClient()->getEmail())
            ->subject('Annulation de la consultation')
            ->htmlTemplate('email/emailAnnulation.html.twig')
            ->context([
                'ClientName' => $cons->getClient()->getPrenom().' '.$cons->getClient()->getNom(),
                'MedecinName' => $cons->getMedecin()->getPrenom().' '.$cons->getMedecin()->getNom(),
                'ConsDate' => $formattedDate,
                'ConsPrix' => $cons->getPrix().' TND',
            ]);
            $loader = new FilesystemLoader(__DIR__.'/../../templates');
            $twigEnv = new Environment($loader);
            $twigBodyRenderer = new BodyRenderer($twigEnv);
            $twigBodyRenderer->render($email);

        $this->mailer->send($email);
    }

    private function sendRefuseEmail(Consultation $cons)
    {
        $formattedDate = $cons->getDate()->format('d/m/Y H:i');
        $email = (new TemplatedEmail())
            ->from('pro.panacea24@gmail.com')
            ->to($cons->getClient()->getEmail())
            ->subject('Refus de la consultation')
            ->htmlTemplate('email/emailRefuse.html.twig')
            ->context([
                'ClientName' => $cons->getClient()->getPrenom().' '.$cons->getClient()->getNom(),
                'MedecinName' => $cons->getMedecin()->getPrenom().' '.$cons->getMedecin()->getNom(),
                'ConsDate' => $formattedDate,
                'ConsPrix' => $cons->getPrix().' TND',
            ]);
            $loader = new FilesystemLoader(__DIR__.'/../../templates');
            $twigEnv = new Environment($loader);
            $twigBodyRenderer = new BodyRenderer($twigEnv);
            $twigBodyRenderer->render($email);

        $this->mailer->send($email);
    }
}
