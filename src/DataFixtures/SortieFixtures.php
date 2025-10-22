<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $participants = $manager->getRepository(Participant::class)->findAll();
        $lieux = $manager->getRepository(Lieu::class)->findAll();
        $etats = $manager->getRepository(Etat::class)->findAll();

        $sortie = new Sortie();
        $sortie->setName("Balade au Louvre");
        $sortie->setDescription("Journée découverte de l'histoire de France et de ses magnifiques bijoux. Possibilité de repartir avec des bibelots!");
        $sortie->setLieu($faker->randomElement($lieux));

        $startingDate = $faker->dateTimeBetween('+2 days', '+10 days');
        $sortie->setStartingDate(\DateTimeImmutable::createFromMutable($startingDate));

        $endingDate = $faker->dateTimeBetween($startingDate, $startingDate->modify('+10 hours'));
        $sortie->setEndingDate(\DateTimeImmutable::createFromMutable($endingDate));

        $limitRegistrationDate = $startingDate->modify("-1 days");
        $sortie->setRegisterLimitDate(\DateTimeImmutable::createFromMutable($limitRegistrationDate));

        $maxRegistered = random_int(2,10);
        $sortie->setMaxRegistrationNumber($maxRegistered);
        $sortie->setState($etats[1]);

        $owner = $participants[0];
        $sortie->setOwner($owner);

        $campus = $owner->getCampus();
        $sortie->setCampus($campus);

        while( $sortie->getParticipants()->count() < $maxRegistered){
            $sortie->addParticipant($faker->randomElement($participants));
        }
        $manager->persist($sortie);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ParticipantFixtures::class, LieuFixtures::class, EtatFixtures::class];
    }
}
