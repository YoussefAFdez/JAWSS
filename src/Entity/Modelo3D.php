<?php

namespace App\Entity;

use App\Repository\Modelo3DRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=Modelo3DRepository::class)
 * @Vich\Uploadable
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
     * @Vich\UploadableField(mapping="modelo3d", fileNameProperty="nombreModelo", size="tamanio")
     * @var File|Null
     */
    private $modeloFile;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $nombreModelo;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $tamanio;

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

    /**
     * @return File|Null
     */
    public function getModeloFile(): ?File
    {
        return $this->modeloFile;
    }

    /**
     * @param File|Null $modeloFile
     * @return Modelo3D
     */
    public function setModeloFile(?File $modeloFile): Modelo3D
    {
        $this->modeloFile = $modeloFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getNombreModelo(): ?string
    {
        return $this->nombreModelo;
    }

    /**
     * @param string $nombreModelo
     * @return Modelo3D
     */
    public function setNombreModelo(string $nombreModelo): Modelo3D
    {
        $this->nombreModelo = $nombreModelo;
        return $this;
    }

    /**
     * @return int
     */
    public function getTamanio(): ?int
    {
        return $this->tamanio;
    }

    /**
     * @param int $tamanio
     * @return Modelo3D
     */
    public function setTamanio(int $tamanio): Modelo3D
    {
        $this->tamanio = $tamanio;
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
