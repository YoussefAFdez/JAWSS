<?php

namespace App\Entity;

use App\Repository\ImagenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ImagenRepository::class)
 * @Vich\Uploadable
 */
class Imagen
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="imagen", fileNameProperty="nombreImagen", size="tamanio")
     * @var File|null
     * @Assert\File(maxSize="31457280")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $resolucion;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $nombreImagen;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $tamanio;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $ultimaModificacion;

    /**
     * @ORM\OneToOne(targetEntity=Recurso::class, cascade={"persist", "remove"})
     */
    private $recurso;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getResolucion()
    {
        return $this->resolucion;
    }

    /**
     * @param string $resolucion
     * @return Imagen
     */
    public function setResolucion(?string $resolucion)
    {
        $this->resolucion = $resolucion;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Imagen
     */
    public function setImageFile(?File $imageFile): Imagen
    {
        $this->imageFile = $imageFile;

        //Permite actualizar la imagen
        if (null !== $imageFile) {
            $this->ultimaModificacion = new \DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getNombreImagen(): ?string
    {
        return $this->nombreImagen;
    }

    /**
     * @param string $nombreImagen
     * @return Imagen
     */
    public function setNombreImagen(string $nombreImagen): Imagen
    {
        $this->nombreImagen = $nombreImagen;
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
     * @return \DateTime
     */
    public function getUltimaModificacion(): ?\DateTime
    {
        return $this->ultimaModificacion;
    }

    /**
     * @param \DateTime $ultimaModificacion
     * @return Imagen
     */
    public function setUltimaModificacion(\DateTime $ultimaModificacion): Imagen
    {
        $this->ultimaModificacion = $ultimaModificacion;
        return $this;
    }

    /**
     * @param int $tamanio
     * @return Imagen
     */
    public function setTamanio(int $tamanio): Imagen
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
