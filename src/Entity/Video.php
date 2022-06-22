<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 * @Vich\Uploadable()
 */
class Video
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
     * @Vich\UploadableField(mapping="video", fileNameProperty="nombreVideo", size="tamanio")
     * @var File|null
     */
    private $videoFile;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $tamanio;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $nombreVideo;

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
    public function getVideoFile(): ?File
    {
        return $this->videoFile;
    }

    /**
     * @param File|null $videoFile
     * @return Video
     */
    public function setVideoFile(?File $videoFile): Video
    {
        $this->videoFile = $videoFile;
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
     * @return Video
     */
    public function setTamanio(int $tamanio): Video
    {
        $this->tamanio = $tamanio;
        return $this;
    }

    /**
     * @return string
     */
    public function getNombreVideo(): ?string
    {
        return $this->nombreVideo;
    }

    /**
     * @param string $nombreVideo
     * @return Video
     */
    public function setNombreVideo(string $nombreVideo): Video
    {
        $this->nombreVideo = $nombreVideo;
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
