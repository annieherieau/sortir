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


    private \Faker\Generator $faker;

    public function __construct(){
        $this->faker = \Faker\Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $faker = $this->faker;
        $now = date_create();
        $participants = $manager->getRepository(Participant::class)->findAll();
        $lieux = $manager->getRepository(Lieu::class)->findAll();
        $etats = $manager->getRepository(Etat::class)->findAll();

        // En création / Ouverte
        for ($i = 0; $i < 5; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(2, true).' _draft');
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $startingDate = $this->getStartingdate(5, 20);
            $sortie->setStartingDate($startingDate);

            $endingDate = $this->getEndingDate($startingDate);
            $sortie->setEndingDate($endingDate);

            $sortie->setRegisterLimitDate($startingDate->modify('-1 days'));

            $maxRegistered = random_int(2,10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $participants[0];
            $sortie->setOwner($owner);

            $campus = $owner->getCampus();
            $sortie->setCampus($campus);
            $sortie->addParticipant($owner);

            $stateRandom= random_int(0,1);
            $maxRandom = random_int(1, $maxRegistered-1);
            $sortie->setState($etats[$stateRandom]);
            if ($stateRandom == 1)
            {
                while( $sortie->getParticipants()->count() < $maxRandom){
                    $sortie->addParticipant($faker->randomElement($participants));
                }
            }
            $manager->persist($sortie);
        }

        // Clôturée par max participants
        for ($i = 0; $i < 3; $i++) {
            $sortie = new Sortie();
            $sortie->setName("Balade au Louvre");
            $sortie->setDescription("Journée découverte de l'histoire de France et de ses magnifiques bijoux. Possibilité de repartir avec des bibelots!");
            $sortie->setLieu($faker->randomElement($lieux));

            $startingDate = $this->getStartingdate(5, 20);
            $sortie->setStartingDate($startingDate);

            $endingDate = $this->getEndingDate($startingDate);
            $sortie->setEndingDate($endingDate);

            $sortie->setRegisterLimitDate($startingDate->modify('-1 days'));

            $maxRegistered = random_int(2, 10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $participants[0];
            $sortie->setOwner($owner);

            $campus = $owner->getCampus();
            $sortie->setCampus($campus);

            while ($sortie->getParticipants()->count() < $maxRegistered) {
                $sortie->addParticipant($faker->randomElement($participants));
            }
            $sortie->setState($etats[2]);
            $manager->persist($sortie);
        }

        // Clôturée par date limite
        for ($i = 0; $i < 4; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(4, true));
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $startingDate = $this->getStartingdate(0, 1);
            $sortie->setStartingDate($startingDate);

            $endingDate = $this->getEndingDate($startingDate);
            $sortie->setEndingDate($endingDate);

            $sortie->setRegisterLimitDate($startingDate->modify('-1 days'));

            $maxRegistered = random_int(2, 10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $participants[0];
            $sortie->setOwner($owner);

            $campus = $owner->getCampus();
            $sortie->setCampus($campus);

            while ($sortie->getParticipants()->count() < random_int(1, $maxRegistered - 1)) {
                $sortie->addParticipant($faker->randomElement($participants));
            }
            $sortie->setState($etats[2]);
            $manager->persist($sortie);
        }

        //En cours / terminé
        for ($i = 0; $i < 10; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(3, true));
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $startingDate = $this->getStartingdate(-1, 0);
            $sortie->setStartingDate($startingDate);

            $endingDate = $this->getEndingDate($startingDate);
            $sortie->setEndingDate($endingDate);

            $sortie->setRegisterLimitDate($startingDate->modify('-1 days'));

            $maxRegistered = random_int(2,10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $participants[0];
            $sortie->setOwner($owner);

            $campus = $owner->getCampus();
            $sortie->setCampus($campus);
            $sortie->addParticipant($owner);

            $maxRandom = random_int(1, $maxRegistered-1);
            while( $sortie->getParticipants()->count() < $maxRandom){
                $sortie->addParticipant($faker->randomElement($participants));
            }

            $sortie->setState($etats[$now < $endingDate ? 3 : 4]);
            $manager->persist($sortie);
        }

        // Annulée
        for ($i = 0; $i < 3; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(4, true));
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $startingDate = $this->getStartingdate(5, 20);
            $sortie->setStartingDate($startingDate);

            $endingDate = $this->getEndingDate($startingDate);
            $sortie->setEndingDate($endingDate);

            $sortie->setRegisterLimitDate($startingDate->modify('-1 days'));

            $maxRegistered = random_int(2,10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $participants[0];
            $sortie->setOwner($owner);

            $campus = $owner->getCampus();
            $sortie->setCampus($campus);
            $sortie->addParticipant($owner);

            $maxRandom = random_int(1, $maxRegistered-1);
            $sortie->setState($etats[5]);
            if ($stateRandom == 1)
            {
                while( $sortie->getParticipants()->count() < $maxRandom){
                    $sortie->addParticipant($faker->randomElement($participants));
                }
            }
            $manager->persist($sortie);
        }

        // Annulée/historisée
        for ($i = 0; $i < 5; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(3, true));
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $startingDate = $this->getStartingdate(-40, -30);
            $sortie->setStartingDate($startingDate);

            $endingDate = $this->getEndingDate($startingDate);
            $sortie->setEndingDate($endingDate);

            $sortie->setRegisterLimitDate($startingDate->modify('-1 days'));

            $maxRegistered = random_int(2,10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $participants[0];
            $sortie->setOwner($owner);

            $campus = $owner->getCampus();
            $sortie->setCampus($campus);
            $sortie->addParticipant($owner);

            $maxRandom = random_int(1, $maxRegistered-1);
            $sortie->setState($etats[random_int(5,6)]);
            if ($stateRandom == 1)
            {
                while( $sortie->getParticipants()->count() < $maxRandom){
                    $sortie->addParticipant($faker->randomElement($participants));
                }
            }
            $manager->persist($sortie);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ParticipantFixtures::class, LieuFixtures::class, EtatFixtures::class];
    }

    private function getStartingdate(int $min, int $max): \DateTimeImmutable
    {
       return \DateTimeImmutable::createFromMutable( $this->faker->dateTimeBetween('+'.$min.' days', '+'.$max.' days'));
    }
    private function getEndingDate(\DateTimeImmutable $startingDate): \DateTimeImmutable
    {
        $minutes = \DateInterval::createFromDateString(random_int(0, 55).' minutes');
        $hours = \DateInterval::createFromDateString(random_int(0, 23).' hours');
        // $days = \DateInterval::createFromDateString(random_int(0, 4).' days');
        return $startingDate
            //->add( $days )
            ->add( $hours )->add( $minutes );
    }

}
