<?php

namespace App\DataFixtures;

use App\Factory\TierFactory;
use App\Factory\UsuarioFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $basico = TierFactory::new()->create(function() {
            return [
                'nombre' => 'Básico',
                'almacenamiento' => 536870912
            ];
        });

        $intermedio = TierFactory::new()->create(function() {
            return [
                'nombre' => 'Intermedio',
                'almacenamiento' => 1073741824
            ];
        });

        $premium = TierFactory::new()->create(function() {
            return [
                'nombre' => 'Premium',
                'almacenamiento' => 2147483648
            ];
        });

        $admin = UsuarioFactory::new()->create(function() use ($premium) {
            return [
                'email' => 'chuck@norris.com',
                'nombreUsuario' => 'chucknorris',
                'administrador' => 'true',
                'tier' => $premium,
            ];
        });

        UsuarioFactory::createMany(10, function() use ($basico) {
           return [
               'tier' => $basico,
           ];
        });

        $manager->flush();
    }
}
