<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $villes = $manager->getRepository(Ville::class)->findAll();

        for ($i = 0; $i < 30; $i++) {
            $lieu = new Lieu();
            $lieu->setName($faker->company());
            $lieu->setStreet($faker->streetAddress());

            // recuperation des villes
            $ville = $faker->randomElement($villes);
            $lieu->setVille($ville);

            $manager->persist($lieu);
        }
        $manager->flush();
    }
}
