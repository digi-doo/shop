<?php

/*
 * This file is part of the Digi Doo s.r.o. sshop project.
 *
 * (c) Digi Doo s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Entity;

use Sylius\Component\Core\Model\Address as BaseAddress;

/**
 * AppBundle extended sylius address entity
 *
 * @author Jan Czernin <jan.czernin@autodevelo.cz>
 */
class Address extends BaseAddress
{
    /**
     * @var string
     */
    private $streetNumber;

    /**
     * @var string|null
     */
    private $ic;

    /**
     * @var string|null
     */
    private $dic;

    /**
     * @var string|null
     */
    private $title;

    /**
     * {@inheritdoc}
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getIc(): ?string
    {
        return $this->ic;
    }

    /**
     * {@inheritdoc}
     */
    public function setIc(?string $ic): void
    {
        $this->ic = $ic;
    }

    /**
     * {@inheritdoc}
     */
    public function getDic(): ?string
    {
        return $this->dic;
    }

    /**
     * {@inheritdoc}
     */
    public function setDic(?string $dic): void
    {
        $this->dic = $dic;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setStreetNumber(?string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }
}
