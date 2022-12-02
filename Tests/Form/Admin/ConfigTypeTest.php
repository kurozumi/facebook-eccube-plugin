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

namespace Plugin\FacebookIntegration\Tests\Form\Admin;

use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Plugin\FacebookIntegration\Form\Type\Admin\ConfigType;
use Symfony\Component\Form\FormInterface;

class ConfigTypeTest extends AbstractTypeTestCase
{
    /**
     * @var FormInterface
     */
    protected $form;

    protected $formData = [
        'clientId' => 'dummy',
        'clientSecret' => 'dummy',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(ConfigType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function test正しいデータ()
    {
        $this->form->submit($this->formData);
        self::assertTrue($this->form->isValid());
    }

    public function testClientIdが空白のときはエラー()
    {
        $this->formData['clientId'] = '';

        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }

    public function testClientSecretが空白のときはエラー()
    {
        $this->formData['clientSecret'] = '';

        $this->form->submit($this->formData);
        self::assertFalse($this->form->isValid());
    }
}
