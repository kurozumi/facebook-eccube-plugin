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
        private $clientId;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $clientSecret;

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
        public function getClientId(): ?string
        {
            return $this->clientId;
        }

        /**
         * @param string $clientId
         * @return $this
         */
        public function setClientId(string $clientId): self
        {
            $this->clientId = $clientId;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getClientSecret(): ?string
        {
            return $this->clientSecret;
        }

        /**
         * @param string $clientSecret
         * @return $this
         */
        public function setClientSecret(string $clientSecret): self
        {
            $this->clientSecret = $clientSecret;

            return $this;
        }
    }
}
