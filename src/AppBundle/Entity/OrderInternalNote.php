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

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class OrderInternalNote implements ResourceInterface
{
    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;
    /**
     * @var int
     */
    private $id;

    /**
     * @var AdminUserInterface
     */
    private $createdBy;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var string
     */
    private $note;

    /**
     * @var AdminUserInterface
     */
    private $approvedBy;

    /**
     * @var \DateTime|null
     */
    private $approvedAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface|null $createdAt
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param Order $order
     */
    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param AdminUserInterface $createdBy
     */
    public function setCreatedBy(AdminUserInterface $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return AdminUserInterface
     */
    public function getCreatedBy(): AdminUserInterface
    {
        return $this->createdBy;
    }

    /**
     * @param string $note
     */
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param AdminUserInterface|null $approvedBy
     */
    public function setApprovedBy(?AdminUserInterface $approvedBy): void
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return AdminUserInterface|null
     */
    public function getApprovedBy(): ?AdminUserInterface
    {
        return $this->approvedBy;
    }

    /**
     * @param \DateTimeInterface|null $approvedAt
     */
    public function setApprovedAt(?\DateTimeInterface $approvedAt)
    {
        $this->approvedAt = $approvedAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getApprovedAt(): ?\DateTimeInterface
    {
        return $this->approvedAt;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approvedBy && $this->approvedAt ? true : false;
    }
}
