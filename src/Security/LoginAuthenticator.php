<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Security\UserProvider;
use DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Outils;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private $entityManager;
    private $outils;

    public function __construct(private UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em, Outils $outils)
    {
        $this->entityManager = $em;
        $this->outils = $outils;
    }

    public function authenticate(Request $request): Passport
    {
        $id = $request->request->get('id', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $id);

        return new Passport(
            new UserBadge($id),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // quand l'utilisateur se login, on vérifie la validité de son abonnement actuel
        // et on met le prochain
        $user = $token->getUser();
        if ($user->getAbonnement() && $user->getAbonnement()->getNom() == "Admin") {
            return new RedirectResponse($this->urlGenerator->generate('app_admin'));
        }
        $date = new DateTime();

        # Si son abonnement expire
        if ($user->getAbonnement() != null) {
            $nextDateAbo = $user->getDateAbonnement();
            $nextDateAbo->modify('+1 year');
            $nextAbo = $user->getNextAbonnement();
            if ($nextDateAbo < $date) {
                $user->setAbonnement($nextAbo);
                $user->setDateAbonnement($nextAbo != null ? DateTime::createFromInterface($nextDateAbo) : null);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        # Si une transaction se termine, on change le statut, et envoie une notification
        foreach ($user->getDemandes() as $t) {
            if ($t->getStatutTransaction() == "Terminer") {
                $lastDate = $t->getLastDate();
                if ($lastDate < $date) {
                    $t->setStatutTransaction("FINI");
                    $mess = "Transaction clôturée, vous ne pouvez plus communiquer. En espérant que votre échange ait été positif !";
                    $this->outils->envoieNotificationA($this->entityManager, $mess, $t->getClient(), $lastDate);
                    $this->outils->envoieNotificationA($this->entityManager, $mess, $t->getAnnonce()->getPosteur(), $lastDate);
                    $this->entityManager->persist($t);
                    $this->entityManager->flush();
                }
            }
        }
        
        if ($user->isSleepMode()) {
            return new RedirectResponse($this->urlGenerator->generate('app_sleep_mode'));
        }
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home_page'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
