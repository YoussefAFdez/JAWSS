<?php

namespace App\Entity;

use App\Repository\TierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TierRepository::class)
 */
class Tier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=50, maxMessage="El nombre del plan no puede tener mas de 50 caracteres.")
     */
    private $nombre;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\Range(max=4192, maxMessage="El almacenamiento no puede ser superior a 4192MB", min="100", minMessage="El almacenamiento debe de ser como minimo de 100Mb")
     */
    private $almacenamiento;

    /**
     * @ORM\OneToMany(targetEntity=Usuario::class, mappedBy="tier")
     */
    private $usuarios;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getAlmacenamiento(): ?string
    {
        return $this->almacenamiento;
    }

    public function setAlmacenamiento(string $almacenamiento): self
    {
        $this->almacenamiento = $almacenamiento;

        return $this;
    }

    /**
     * @return Collection<int, Usuario>
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setTier($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getTier() === $this) {
                $usuario->setTier(null);
            }
        }

        return $this;
    }
}
