<?php

namespace App\DataFixtures;

use App\Entity\VideoGame;
use App\Entity\Category;
use App\Entity\Editor;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;

class VideoGameFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Récupérer les éditeurs et les catégories existants
        $editors = $manager->getRepository(Editor::class)->findAll();
        $categories = $manager->getRepository(Category::class)->findAll();

        // Créer des jeux vidéo
        for ($i = 0; $i < 10; $i++) {
            $videoGame = new VideoGame();
            $videoGame->setTitle($faker->sentence(3))
                      ->setDescription($faker->paragraph)
                      ->setReleaseDate($faker->dateTimeThisDecade)
                      ->setEditor($faker->randomElement($editors));

            // Ajouter des catégories au jeu vidéo
            $randomCategories = $faker->randomElements($categories, $faker->numberBetween(1, 3));
            foreach ($randomCategories as $category) {
                $videoGame->addCategory($category);
            }

            $manager->persist($videoGame);
        }

        $manager->flush();
    }
}
