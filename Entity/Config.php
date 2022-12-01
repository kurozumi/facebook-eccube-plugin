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

namespace Plugin\FacebookIntegration\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Config::class, false)) {
    /**
     * Config
     *
     * @ORM\Table(name="plg_facebook_integration_config")
     * @ORM\Entity(repositoryClass="Plugin\FacebookIntegration\Repository\ConfigRepository")
     */
    class Config
    {
        /**
         * @ORM\Column(type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $appId;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $appSecret;

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @return string|null
         */
        public function getAppId(): ?string
        {
            return $this->appId;
        }

        /**
         * @param string $appId
         * @return $this
         */
        public function setAppId(string $appId): self
        {
            $this->appId = $appId;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getAppSecret(): ?string
        {
            return $this->appSecret;
        }

        /**
         * @param string $appSecret
         * @return $this
         */
        public function setAppSecret(string $appSecret): self
        {
            $this->appSecret = $appSecret;

            return $this;
        }
    }
}
