<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->addWishes(50,$manager);

    }

    public function addWishes(int $number, ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($i = 0 ; $i < $number; $i++){
            $wish = new Wish();
            $wish
                ->setTitle($faker->realText(50))
                ->setAuthor($faker->firstName)
                ->setPublished($faker->boolean(60)) //60% true
                ->setDescription($faker->realText(1000))
                ->setDateCreated($faker->dateTimeBetween("-2 year"))
                ->setDateUpdated($faker->dateTimeBetween());

            $manager->persist($wish);
        }

        $manager->flush();
    }
}

