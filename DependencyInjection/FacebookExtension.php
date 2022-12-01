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

namespace Plugin\FacebookIntegration\DependencyInjection;

use Plugin\FacebookIntegration\Security\Authenticator\FacebookAuthenticator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class FacebookExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $plugins = $container->getParameter('eccube.plugins.enabled');

        if (!in_array('FacebookIntegration', $plugins)) {
            return;
        }

        $extensionConfigsRefl = new \ReflectionProperty(ContainerBuilder::class, 'extensionConfigs');
        $extensionConfigsRefl->setAccessible(true);
        $extensionConfigs = $extensionConfigsRefl->getValue($container);

        foreach ($extensionConfigs['security'] as $key => $security) {
            if (isset($security['firewalls'])) {
//                $extensionConfigs['security'][$key]['firewalls']['customer']['entry_point'] = FacebookAuthenticator::class;
                $extensionConfigs['security'][$key]['firewalls']['customer']['custom_authenticators'][] = FacebookAuthenticator::class;
            }
        }
    }

    public function load(array $configs, ContainerBuilder $container)
    {
    }
}
