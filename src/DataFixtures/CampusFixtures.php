<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $array =['Nord', 'Sud', 'Est', 'Ouest', 'Centre'];
//        $faker = \Faker\Factory::create('fr_FR');
        foreach($array as $value){
            $campus = new Campus();
            $campus->setName($value);
            $manager->persist($campus);
        }
        $manager->flush();
    }
}
