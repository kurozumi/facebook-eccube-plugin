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

namespace Plugin\FacebookIntegration\Tests\Web;

use Eccube\Tests\Web\AbstractWebTestCase;

class FacebookControllerTest extends AbstractWebTestCase
{
    public function testFacebookの設定をしていなかったらNotFound()
    {
        $this->client->request('GET', $this->generateUrl('facebook_connect'));
        self::assertTrue($this->client->getResponse()->isNotFound());
    }

    public function testFacebookの設定をしていたらリダイレクト()
    {
        self::markTestIncomplete();
    }
}
