<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface; // Import correct namespace
use Psr\Log\LoggerInterface; // Import LoggerInterface
use App\Entity\User;
use Symfony\Component\Mime\Address;
class EmailVerificationService
{
    private $mailer;
    private $urlGenerator;
    private $logger; // Define logger property

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, LoggerInterface $logger) // Use correct typehint
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger; // Inject logger
    }

    public function sendVerificationEmail(User $user): void
    {
        $email = (new Email())
        ->from(new Address('no-reply@panacea.com', 'panacea'))
        ->to($user->getEmail())
            ->subject('Please verify your email')
            ->html($this->generateVerificationEmailContent($user));

        try {
            $this->mailer->send($email);
            $this->logger->info('Verification email sent to ' . $user->getEmail());
        } catch (\Exception $e) {
            $this->logger->error('Failed to send verification email to ' . $user->getEmail() . ': ' . $e->getMessage());
        }
    }

    private function generateVerificationEmailContent(User $user): string
    {
        $verificationLink = $this->urlGenerator->generate('app_verify_email', [
            'token' => $user->getVerificationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // You can customize the email content here
        $content = "Hello " . $user->getUsername() . ",<br><br>";
        $content .= "Please click the following link to verify your email address:<br>";
        $content .= "<a href='{$verificationLink}'>Verify Email</a><br><br>";
        $content .= "If you didn't register on our website, please ignore this email.<br><br>";
        $content .= "Best regards,<br>";
        $content .= "Your Website Team";

        return $content;
    }
}
