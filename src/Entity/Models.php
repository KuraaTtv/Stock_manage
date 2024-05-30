<?php

namespace App\Entity;

use App\Repository\ModelsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModelsRepository::class)]
class Models
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $icon = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'Models')]
    private Collection $user_role;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Title = null;

    public function __construct()
    {
        $this->user_role = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserRole(): Collection
    {
        return $this->user_role;
    }

    public function addUserRole(User $userRole): static
    {
        if (!$this->user_role->contains($userRole)) {
            $this->user_role->add($userRole);
            $userRole->addModel($this);
        }

        return $this;
    }

    public function removeUserRole(User $userRole): static
    {
        if ($this->user_role->removeElement($userRole)) {
            $userRole->removeModel($this);
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(?string $Title): static
    {
        $this->Title = $Title;

        return $this;
    }
}
