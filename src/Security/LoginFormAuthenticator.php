<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        #Cette ligne extrait l'adresse e-mail saisie par l'utilisateur dans le formulaire de connexion.
        $mail = $request->getPayload()->getString('mail');

        #Cette ligne enregistre l'e-mail de l'utilisateur dans la session sous la clé Security::LAST_USERNAME. Ceci permet de pré-remplir le champ d'e-mail
        # du formulaire de connexion en cas d'erreur d'authentification, pour que l'utilisateur n'ait pas à le retaper.
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $mail);
        #Cette partie crée un objet Passport qui contient les informations nécessaires pour authentifier l'utilisateur :

        #UserBadge : Il identifie l'utilisateur à partir de l'adresse e-mail.
        #PasswordCredentials : Il contient le mot de passe saisi par l'utilisateur.
        #CsrfTokenBadge : Il vérifie le jeton CSRF pour prévenir les attaques de type Cross-Site Request Forgery.
        #RememberMeBadge : Il permet de se souvenir de l'utilisateur si l'option "Remember me" est activée.
        return new Passport(
            new UserBadge($mail),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:

        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
