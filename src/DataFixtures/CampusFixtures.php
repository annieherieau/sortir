<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $campus = new Campus();
            $campus->setName("Campus: ".$faker->colorName());
            $manager->persist($campus);
        }
        $manager->flush();
    }
}
