<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $PaymentAmount = null;

    #[ORM\Column(length: 255)]
    private ?string $PaymentMethode = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $PaymentDate = null;

    #[ORM\ManyToOne(inversedBy: 'userid')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\Column(length: 255)]
    private ?string $CardOwner = null;

    #[ORM\Column]
    private ?int $CardNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ExpirationDate = null;

    #[ORM\Column]
    private ?int $Cvv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentAmount(): ?int
    {
        return $this->PaymentAmount;
    }

    public function setPaymentAmount(int $PaymentAmount): static
    {
        $this->PaymentAmount = $PaymentAmount;

        return $this;
    }

    public function getPaymentMethode(): ?string
    {
        return $this->PaymentMethode;
    }

    public function setPaymentMethode(string $PaymentMethode): static
    {
        $this->PaymentMethode = $PaymentMethode;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->PaymentDate;
    }

    public function setPaymentDate(\DateTimeInterface $PaymentDate): static
    {
        $this->PaymentDate = $PaymentDate;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getCardOwner(): ?string
    {
        return $this->CardOwner;
    }

    public function setCardOwner(string $CardOwner): static
    {
        $this->CardOwner = $CardOwner;

        return $this;
    }

    public function getCardNumber(): ?int
    {
        return $this->CardNumber;
    }

    public function setCardNumber(int $CardNumber): static
    {
        $this->CardNumber = $CardNumber;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->ExpirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $ExpirationDate): static
    {
        $this->ExpirationDate = $ExpirationDate;

        return $this;
    }

    public function getCvv(): ?int
    {
        return $this->Cvv;
    }

    public function setCvv(int $Cvv): static
    {
        $this->Cvv = $Cvv;

        return $this;
    }
}
