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

namespace Plugin\FacebookIntegration;

use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'customer' => [
                'children' => [
                    'auth0_config' => [
                        'name' => 'Facebook設定',
                        'url' => 'facebook_integration_admin_config',
                    ],
                ],
            ],
        ];
    }
}
