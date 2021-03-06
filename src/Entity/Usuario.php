<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 * @method string getUserIdentifier()
 */
#[UniqueEntity(fields: ['email'], message: 'Ya existe una cuenta con ese correo electrónico')]
#[UniqueEntity(fields: ['nombreUsuario'], message: 'Ya existe una cuenta con ese nombre de usuario')]
class Usuario implements PasswordAuthenticatedUserInterface, UserInterface
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
     * @Assert\NotBlank(message="El campo apellidos no puede estar vacío")
     */
    private $apellidos;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Debes introducir un nombre de usuario")
     * @Assert\Length(min=3, minMessage="El nombre de usuario debe contener al menos 3 caracteres", max=20, maxMessage="El nombre de usuario no puede tener más de 20 caracteres")
     */
    private $nombreUsuario;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $clave;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $administrador;

    /**
     * @var string
     * @ORM\Column(type="bigint")
     */
    private $espacioUtilizado;

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
     * @ORM\ManyToMany(targetEntity=Recurso::class, mappedBy="favorito")
     * @JoinTable(name="favoritos")
     */
    private $favoritos;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;
    public function __construct()
    {
        $this->recursos = new ArrayCollection();
        $this->favoritos = new ArrayCollection();
    }
    public function __toString()
    {
        return $this->getNombreUsuario();
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
    /**
     * @return string
     */
    public function getClave(): ?string
    {
        return $this->clave;
    }
    /**
     * @param string $clave
     * @return Usuario
     */
    public function setClave(string $clave): Usuario
    {
        $this->clave = $clave;
        return $this;
    }
    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
    /**
     * @param string $email
     * @return Usuario
     */
    public function setEmail(string $email): Usuario
    {
        $this->email = $email;
        return $this;
    }
    /**
     * @return bool
     */
    public function isAdministrador(): ?bool
    {
        return $this->administrador;
    }
    /**
     * @param bool $administrador
     * @return Usuario
     */
    public function setAdministrador(bool $administrador): Usuario
    {
        $this->administrador = $administrador;
        return $this;
    }
    /**
     * @return string
     */
    public function getEspacioUtilizado(): ?string
    {
        return $this->espacioUtilizado;
    }
    /**
     * @param string $espacioUtilizado
     * @return Usuario
     */
    public function setEspacioUtilizado(string $espacioUtilizado): Usuario
    {
        $this->espacioUtilizado = $espacioUtilizado;
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
    public function getRoles()
    {
        $roles = [];
        $roles[] = 'ROLE_USER';

        if ($this->isAdministrador()) $roles[] = 'ROLE_ADMIN';

        return $roles;
    }
    public function getPassword(): ?string
    {
        return $this->getClave();
    }
    public function getSalt()
    {
        return null;
    }
    public function eraseCredentials()
    {
    }
    public function getUsername()
    {
        return $this->getEmail();
    }
    public function __call(string $name, array $arguments)
    {
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
