<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etats = ["En Création", "Ouverte", "Clôturée", "En cours",
            "Terminée", "Annulée", "Historisée"];
        foreach ($etats as $etat) {
            $e = new Etat();
            $e->setLibelle($etat);
            $manager->persist($e);
        }
        $manager->flush();
    }
}
