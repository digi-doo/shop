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

use JMS\Serializer\Annotation\Groups;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * GoogleTaxonomy
 */
class GoogleTaxonomy implements ResourceInterface
{
    /**
     * @var int
     * @Groups({"Autocomplete"})
     */
    private $id;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     * @Groups({"Autocomplete"})
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $dateImported;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param int $code
     *
     * @return GoogleTaxonomy
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return GoogleTaxonomy
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dateImported
     *
     * @param \DateTime $dateImported
     *
     * @return GoogleTaxonomy
     */
    public function setDateImported($dateImported)
    {
        $this->dateImported = $dateImported;

        return $this;
    }

    /**
     * Get dateImported
     *
     * @return \DateTime
     */
    public function getDateImported()
    {
        return $this->dateImported;
    }
}
