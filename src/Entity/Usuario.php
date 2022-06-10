<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 */
class Usuario
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apellidos;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombreUsuario;

    /**
     * @ORM\ManyToOne(targetEntity=Tier::class, inversedBy="usuarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tier;

    /**
     * @ORM\OneToMany(targetEntity=Recurso::class, mappedBy="propietario")
     */
    private $recursos;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Recurso", mappedBy="usuarios")
     * @JoinTable(name="acceso")
     */
    private $recursosAccesibles;

    /**
     * @ORM\ManyToMany(targetEntity=Recurso::class, mappedBy="favoritos")
     * @JoinTable(name="favoritos")
     */
    private $favoritos;

    public function __construct()
    {
        $this->recursos = new ArrayCollection();
        $this->favoritos = new ArrayCollection();
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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): self
    {
        $this->nombreUsuario = $nombreUsuario;

        return $this;
    }

    public function getTier(): ?Tier
    {
        return $this->tier;
    }

    public function setTier(?Tier $tier): self
    {
        $this->tier = $tier;

        return $this;
    }

    /**
     * @return Collection<int, Recurso>
     */
    public function getRecursos(): Collection
    {
        return $this->recursos;
    }

    public function addRecurso(Recurso $recurso): self
    {
        if (!$this->recursos->contains($recurso)) {
            $this->recursos[] = $recurso;
            $recurso->setPropietario($this);
        }

        return $this;
    }

    public function removeRecurso(Recurso $recurso): self
    {
        if ($this->recursos->removeElement($recurso)) {
            // set the owning side to null (unless already changed)
            if ($recurso->getPropietario() === $this) {
                $recurso->setPropietario(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecursosAccesibles()
    {
        return $this->recursosAccesibles;
    }

    /**
     * @param mixed $recursosAccesibles
     * @return Usuario
     */
    public function setRecursosAccesibles($recursosAccesibles)
    {
        $this->recursosAccesibles = $recursosAccesibles;
        return $this;
    }

    /**
     * @return Collection<int, Recurso>
     */
    public function getFavoritos(): Collection
    {
        return $this->favoritos;
    }

    public function addFavorito(Recurso $favorito): self
    {
        if (!$this->favoritos->contains($favorito)) {
            $this->favoritos[] = $favorito;
            $favorito->addFavorito($this);
        }

        return $this;
    }

    public function removeFavorito(Recurso $favorito): self
    {
        if ($this->favoritos->removeElement($favorito)) {
            $favorito->removeFavorito($this);
        }

        return $this;
    }
}
