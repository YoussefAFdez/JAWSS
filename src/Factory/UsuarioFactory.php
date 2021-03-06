<?php

namespace App\Factory;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Usuario>
 *
 * @method static Usuario|Proxy createOne(array $attributes = [])
 * @method static Usuario[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Usuario|Proxy find(object|array|mixed $criteria)
 * @method static Usuario|Proxy findOrCreate(array $attributes)
 * @method static Usuario|Proxy first(string $sortedField = 'id')
 * @method static Usuario|Proxy last(string $sortedField = 'id')
 * @method static Usuario|Proxy random(array $attributes = [])
 * @method static Usuario|Proxy randomOrCreate(array $attributes = [])
 * @method static Usuario[]|Proxy[] all()
 * @method static Usuario[]|Proxy[] findBy(array $attributes)
 * @method static Usuario[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Usuario[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static UsuarioRepository|RepositoryProxy repository()
 * @method Usuario|Proxy create(array|callable $attributes = [])
 */
final class UsuarioFactory extends ModelFactory
{
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
        $this->userPasswordHasher = $userPasswordHasher;

    }

    protected function getDefaults(): array
    {
        return [
            'nombre' => self::faker()->firstName(),
            'apellidos' => self::faker()->lastName() . ' ' . self::faker()->lastName(),
            'nombreUsuario' => self::faker()->userName(),
            'clave' => $this->userPasswordHasher->hashPassword(new Usuario(), 'oretania'),
            'email' => self::faker()->email(),
            'administrador' => self::faker()->boolean(10),
            'espacioUtilizado' => 0,
            'isVerified' => true,
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Usuario $usuario): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Usuario::class;
    }
}
