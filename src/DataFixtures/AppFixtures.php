<?php

namespace App\DataFixtures;

use App\Factory\DragonTreasureFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // esto me creara solamente 10 objetos user
        // y cuando llegue al dragon treasure me usara uno de los usuarios ya creados
        // asi podemos evitar el crear un campo owner en el Dragon Treasure Factory
        UserFactory::createMany(10);
        DragonTreasureFactory::createMany(40, function () {
            return [
                'owner' => UserFactory::random(),
            ];
        });
    }
}
