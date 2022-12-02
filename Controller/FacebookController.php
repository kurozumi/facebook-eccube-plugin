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

namespace Plugin\FacebookIntegration\Controller;

use Eccube\Controller\AbstractController;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Plugin\FacebookIntegration\Entity\Config;
use Plugin\FacebookIntegration\Repository\ConfigRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/facebook")
 */
class FacebookController extends AbstractController
{
    /**
     * @param ClientRegistry $clientRegistry
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/connect", name="facebook_connect")
     */
    public function connect(ClientRegistry $clientRegistry, ConfigRepository $configRepository)
    {
        /** @var Config $Config */
        $Config = $configRepository->get();
        if (!$Config) {
            throw new NotFoundHttpException();
        }

        if (!$Config->getClientId() || !$Config->getClientSecret()) {
            throw new NotFoundHttpException();
        }

        return $clientRegistry
            ->getClient('facebook')
            ->redirect([
                'public_profile',
                'email',
            ]);
    }

    /**
     * @return void
     *
     * @Route("/connect/callback", name="facebook_connect_callback")
     */
    public function callback()
    {
    }
}
