<?php

namespace App\Entity;

use App\Repository\RecursoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass=RecursoRepository::class)
 */
class Recurso
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $extension;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fichero;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="recursos")
     */
    private $propietario;

    /**
     * @ORM\ManyToMany(targetEntity=Usuario::class, inversedBy="favoritos")
     * @JoinTable(name="favoritos")
     */
    private $favorito;

    /**
     * @ORM\ManyToMany(targetEntity=Usuario::class, inversedBy="recursosAccesibles")
     * @JoinTable(name="acceso")
     */
    private $usuarios;

    public function __construct()
    {
        $this->favorito = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function isFichero(): ?bool
    {
        return $this->fichero;
    }

    public function setFichero(bool $fichero): self
    {
        $this->fichero = $fichero;

        return $this;
    }

    public function getPropietario(): ?Usuario
    {
        return $this->propietario;
    }

    public function setPropietario(?Usuario $propietario): self
    {
        $this->propietario = $propietario;

        return $this;
    }

    /**
     * @return Collection<int, Usuario>
     */
    public function getFavorito(): Collection
    {
        return $this->favorito;
    }

    public function addFavorito(Usuario $favorito): self
    {
        if (!$this->favorito->contains($favorito)) {
            $this->favorito[] = $favorito;
        }

        return $this;
    }

    public function removeFavorito(Usuario $favorito): self
    {
        $this->favorito->removeElement($favorito);

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
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        $this->usuarios->removeElement($usuario);

        return $this;
    }
}
