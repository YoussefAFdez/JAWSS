<?php

namespace App\Entity;

use App\Repository\AudioRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AudioRepository::class)
 */
class Audio
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
    private $duracion;

    /**
     * @ORM\OneToOne(targetEntity=Recurso::class, cascade={"persist", "remove"})
     */
    private $recurso;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuracion(): ?string
    {
        return $this->duracion;
    }

    public function setDuracion(string $duracion): self
    {
        $this->duracion = $duracion;

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
