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

namespace Plugin\FacebookIntegration\Tests\Security\Authenticator;

use Eccube\Entity\Customer;
use Eccube\Tests\EccubeTestCase;
use KnpU\OAuth2ClientBundle\Security\Exception\FinishRegistrationException;
use Plugin\FacebookIntegration\Security\Authenticator\FacebookAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class FacebookAuthenticatorTest extends EccubeTestCase
{
    /**
     * @var FacebookAuthenticator
     */
    protected $authenticator;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var SessionInterface
     */
    protected $session;

    public function setUp(): void
    {
        parent::setUp();

        $this->authenticator = new FacebookAuthenticator(
            static::getContainer()->get('knpu.oauth2.registry'),
            static::getContainer()->get('doctrine.orm.default_entity_manager'),
            static::getContainer()->get('router'),
            static::getContainer()->get('session')
        );

        $this->router = static::getContainer()->get('router');
        $this->session = static::getContainer()->get('session');
    }

    public function testStart()
    {
        $response = $this->authenticator->start(new Request());
        self::assertTrue($response->isRedirect($this->router->generate('facebook_connect')));
    }

    public function testSupport()
    {
        $request = new Request([], [], ['_route' => 'facebook_connect_callback']);
        self::assertTrue($this->authenticator->supports($request));
    }

    public function testOnAuthenticationSuccess()
    {
        $response = $this->authenticator->onAuthenticationSuccess(new Request(), new UsernamePasswordToken(new Customer(), 'customer', ['ROLE_USER']), 'customer');
        self::assertTrue($response->isRedirect($this->router->generate('mypage')));
    }

    public function testOnAuthenticationFailureFinishRegistrationException()
    {
        $requerst = new Request();
        $requerst->setSession($this->session);

        $response = $this->authenticator->onAuthenticationFailure($requerst, new FinishRegistrationException([]));
        self::assertTrue($response->isRedirect($this->router->generate('entry')));
    }

    public function testOnAuthenticationFailureAuthenticationException()
    {
        $request = new Request();
        $request->setSession($this->session);

        $response = $this->authenticator->onAuthenticationFailure($request, new AuthenticationException());
        self::assertTrue($response->isRedirect($this->router->generate('mypage_login')));
    }
}
