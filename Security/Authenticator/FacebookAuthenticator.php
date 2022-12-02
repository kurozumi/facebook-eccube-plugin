<?php

/**
 * This file is part of FacebookIntegration
 *
 * Copyright(c) Akira Kurozumi <info@a-zumi.net>
 *
 *  https://a-zumi.net
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\FacebookIntegration\Security\Authenticator;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use KnpU\OAuth2ClientBundle\Security\Exception\FinishRegistrationException;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class FacebookAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        ClientRegistry         $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface        $router,
        SessionInterface       $session
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->session = $session;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate('facebook_connect'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'facebook_connect_callback';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('facebook');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var FacebookUser $user */
                $user = $client->fetchUserFromToken($accessToken);

                $customer = $this->entityManager->getRepository(Customer::class)
                    ->findOneBy(['facebookId' => $user->getId()]);

                // 連携済みの場合
                if ($customer instanceof Customer) {
                    // 本会員の場合、会員情報を返す
                    if ($customer->getStatus()->getId() === CustomerStatus::REGULAR) {
                        return $customer;
                    } else {
                        throw new AuthenticationException();
                    }
                }

                /** @var Customer $customer */
                $customer = $this->entityManager->getRepository(Customer::class)
                    ->findOneBy(['email' => $user->getEmail()]);

                // 会員登録していない場合、会員登録ページへ
                if (null === $customer) {
                    throw new FinishRegistrationException($user);
                }

                // 会員登録済みの場合はユーザー識別子を保存
                if (null === $customer->getFacebookId()) {
                    $customer->setFacebookId($user->getId());
                    $this->entityManager->persist($customer);
                    $this->entityManager->flush();
                }

                return $customer;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('mypage');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($exception instanceof FinishRegistrationException) {
            $this->saveUserInfoToSession($request, $exception);

            return new RedirectResponse($this->router->generate('entry'));
        } else {
            $this->saveAuthenticationErrorToSession($request, $exception);
            $this->session->remove('access_token');

            return new RedirectResponse($this->router->generate('mypage_login'));
        }
    }
}
