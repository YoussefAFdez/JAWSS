<?php

namespace App\Entity;

use App\Repository\Modelo3DRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=Modelo3DRepository::class)
 */
class Modelo3D
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
    private $material;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $relleno;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resolucion;

    /**
     * @ORM\Column(type="boolean")
     */
    private $soportes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\OneToOne(targetEntity=Recurso::class, cascade={"persist", "remove"})
     */
    private $recurso;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(string $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getRelleno(): ?int
    {
        return $this->relleno;
    }

    public function setRelleno(?int $relleno): self
    {
        $this->relleno = $relleno;

        return $this;
    }

    public function getResolucion(): ?string
    {
        return $this->resolucion;
    }

    public function setResolucion(?string $resolucion): self
    {
        $this->resolucion = $resolucion;

        return $this;
    }

    public function isSoportes(): ?bool
    {
        return $this->soportes;
    }

    public function setSoportes(bool $soportes): self
    {
        $this->soportes = $soportes;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getRecurso(): ?Recurso
    {
        return $this->recurso;
    }

    public function setRecurso(?Recurso $recurso): self
    {
        $this->recurso = $recurso;

        return $this;
    }
}
