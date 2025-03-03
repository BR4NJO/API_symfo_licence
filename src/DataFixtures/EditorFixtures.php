<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;

class EditorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Création de quelques éditeurs
        for ($i = 0; $i < 3; $i++) {
            $editor = new Editor();
            $editor->setName($faker->company)
                   ->setCountry($faker->country);
            $manager->persist($editor);
        }

        $manager->flush();
    }
}
