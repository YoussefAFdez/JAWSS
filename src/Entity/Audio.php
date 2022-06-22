<?php

namespace App\Entity;

use App\Repository\AudioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=AudioRepository::class)
 * @Vich\Uploadable
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $duracion;

    /**
     * @Vich\UploadableField(mapping="audio", fileNameProperty="nombreAudio", size="tamanio")
     * @var File|null
     */
    private $audioFile;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $nombreAudio;

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

    public function getDuracion(): ?string
    {
        return $this->duracion;
    }

    public function setDuracion(?string $duracion): self
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getAudioFile(): ?File
    {
        return $this->audioFile;
    }

    /**
     * @param File|null $audioFile
     * @return Audio
     */
    public function setAudioFile(?File $audioFile): Audio
    {
        $this->audioFile = $audioFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getNombreAudio(): ?string
    {
        return $this->nombreAudio;
    }

    /**
     * @param string $nombreAudio
     * @return Audio
     */
    public function setNombreAudio(string $nombreAudio): Audio
    {
        $this->nombreAudio = $nombreAudio;
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
     * @return Audio
     */
    public function setTamanio(int $tamanio): Audio
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
