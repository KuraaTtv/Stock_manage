<?php
namespace App\Entity;

use App\Repository\OrderItemsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemsRepository::class)]
class OrderItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Quantity = null;

    #[ORM\Column]
    private ?int $Total = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems' , cascade:['persist','remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Orders $OrderId = null;
// when Delete product 
    #[ORM\ManyToOne(inversedBy: 'orderItems', cascade: ["remove"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $Prod_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(int $Quantity): static
    {
        $this->Quantity = $Quantity;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->Total;
    }

    public function setTotal(int $Total): static
    {
        $this->Total = $Total;

        return $this;
    }

    public function getOrderId(): ?Orders
    {
        return $this->OrderId;
    }

    public function setOrderId(?Orders $OrderId): static
    {
        $this->OrderId = $OrderId;

        return $this;
    }

    public function getProdId(): ?Product
    {
        return $this->Prod_id;
    }

    public function setProdId(?Product $Prod_id): static
    {
        $this->Prod_id = $Prod_id;

        return $this;
    }
}
