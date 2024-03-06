<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\EmailVerificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class Authenticator extends AbstractLoginFormAuthenticator
{
    private $urlGenerator;
    private $userRepository;
    private $emailVerificationService;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, EmailVerificationService $emailVerificationService)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->emailVerificationService = $emailVerificationService;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        // Retrieve the user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        // Check if the user exists
        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }

        // Check if the user is verified
        if (!$user->isVerified()) {
            // Send a verification email
            $this->emailVerificationService->sendVerificationEmail($user);

            throw new CustomUserMessageAuthenticationException('Your email is not verified. We have sent you a new verification email.');
        }

        // Check if the user account is deactivated
        if ($user->isStatus()) {
            throw new CustomUserMessageAuthenticationException('Votre compte est désactivé.');
        }

        // Create a user badge
        $userBadge = new UserBadge($email);

        // Create password credentials
        $passwordCredentials = new PasswordCredentials($password);

        // Create an array of badges including CSRF token badge
        $badges = [
            $userBadge,
            $passwordCredentials,
            new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token'))
        ];

        return new Passport($userBadge, $passwordCredentials, $badges);
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        $user = $token->getUser();

        // Check the role
        $id = $user->getId();
        if ($id === 1) {
            return new RedirectResponse($this->urlGenerator->generate('work'));
        } else {
            return new RedirectResponse($this->urlGenerator->generate('home'));
        }
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
