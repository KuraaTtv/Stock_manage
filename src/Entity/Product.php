<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column]
    private ?float $Price = null;

    #[ORM\Column]
    private ?int $Stock_qu = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    #[ORM\ManyToOne(inversedBy: 'category')]
    private ?Category $category = null;

    /**
     * @var Collection<int, Orders>
     */
    #[ORM\ManyToMany(targetEntity: Orders::class, inversedBy: 'Orders')]
    private Collection $orders;

    /**
     * @var Collection<int, OrderItems>
     */
    #[ORM\OneToMany(targetEntity: OrderItems::class, mappedBy: 'Prod_id',cascade: ["remove"])]
    private Collection $Prod_id;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->Prod_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->Price;
    }

    public function setPrice(float $Price): static
    {
        $this->Price = $Price;

        return $this;
    }

    public function getStockQu(): ?int
    {
        return $this->Stock_qu;
    }

    public function setStockQu(int $Stock_qu): static
    {
        $this->Stock_qu = $Stock_qu;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
        }

        return $this;
    }

    public function removeOrder(Orders $order): static
    {
        $this->orders->removeElement($order);

        return $this;
    }

    /**
     * @return Collection<int, OrderItems>
     */
    public function getProdId(): Collection
    {
        return $this->Prod_id;
    }

    public function addProdId(OrderItems $prodId): static
    {
        if (!$this->Prod_id->contains($prodId)) {
            $this->Prod_id->add($prodId);
            $prodId->setProdId($this);
        }

        return $this;
    }

    public function removeProdId(OrderItems $prodId): static
    {
        if ($this->Prod_id->removeElement($prodId)) {
            // set the owning side to null (unless already changed)
            if ($prodId->getProdId() === $this) {
                $prodId->setProdId(null);
            }
        }

        return $this;
    }
}
