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

namespace Plugin\FacebookIntegration\Controller\Admin;

use Eccube\Controller\AbstractController;
use Eccube\Util\StringUtil;
use Plugin\FacebookIntegration\Entity\Config;
use Plugin\FacebookIntegration\Form\Type\Admin\ConfigType;
use Plugin\FacebookIntegration\Repository\ConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * ConfigController constructor.
     *
     * @param ConfigRepository $configRepository
     */
    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/facebook_integration/config", name="facebook_integration_admin_config")
     * @Template("@FacebookIntegration/admin/config.twig")
     */
    public function index(Request $request)
    {
        $Config = $this->configRepository->get();
        $form = $this->createForm(ConfigType::class, $Config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Config $Config */
            $Config = $form->getData();
            $this->entityManager->persist($Config);
            $this->entityManager->flush();

            $envFile = $this->getParameter('kernel.project_dir') . '/.env';
            $env = file_get_contents($envFile);

            $env = StringUtil::replaceOrAddEnv($env, [
                'OAUTH_FACEBOOK_CLIENT_ID' => $Config->getClientId(),
                'OAUTH_FACEBOOK_CLIENT_SECRET' => $Config->getClientSecret(),
            ]);

            file_put_contents($envFile, $env);

            $this->addSuccess('登録しました。', 'admin');

            return $this->redirectToRoute('facebook_integration_admin_config');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
