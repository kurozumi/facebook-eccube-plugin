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

namespace Plugin\FacebookIntegration\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use KnpU\OAuth2ClientBundle\Security\Helper\FinishRegistrationBehavior;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class EntryListener implements EventSubscriberInterface
{
    use FinishRegistrationBehavior;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        SessionInterface       $session,
        EntityManagerInterface $entityManager
    )
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            EccubeEvents::FRONT_ENTRY_INDEX_COMPLETE => 'onFrontEntryIndexComplete',
        ];
    }

    public function onFrontEntryIndexComplete(EventArgs $args)
    {
        $request = $args->getRequest();
        if (null === $request) {
            return;
        }

        /** @var Customer $customer */
        $customer = $args->getArgument('Customer');

        /** @var FacebookUser $userInfo */
        $userInfo = $this->getUserInfoFromSession($request);
        if ($userInfo) {
            $customer->setFacebookId($userInfo->getId());
            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            // 会員登録完了時にFacebookUserのセッション削除
            $this->session->remove('guard.finish_registration.user_information');
        }
    }
}
