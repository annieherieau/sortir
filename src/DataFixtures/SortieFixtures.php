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
        for ($i = 0; $i < 10; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(2, true));
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $this->setDates($sortie, 5, 20, -1);

            $maxRegistered = random_int(2,10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $faker->randomElement($participants);
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
        for ($i = 0; $i < 4; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(3, true));
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $this->setDates($sortie, 5, 20, -1);

            $maxRegistered = random_int(2, 10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $faker->randomElement($participants);
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
        for ($i = 0; $i < 5; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(4, true));
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $this->setDates($sortie, 0, 1, -1);

            $maxRegistered = random_int(2, 10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $faker->randomElement($participants);
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

            $this->setDates($sortie, -1,0, -1);

            $maxRegistered = random_int(2,10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $faker->randomElement($participants);
            $sortie->setOwner($owner);

            $campus = $owner->getCampus();
            $sortie->setCampus($campus);
            $sortie->addParticipant($owner);

            $maxRandom = random_int(1, $maxRegistered-1);
            while( $sortie->getParticipants()->count() < $maxRandom){
                $sortie->addParticipant($faker->randomElement($participants));
            }

            $sortie->setState($etats[$now < $sortie->getEndingDate() ? 3 : 4]);
            $manager->persist($sortie);
        }

        // Annulée
        for ($i = 0; $i < 3; $i++) {
            $sortie = new Sortie();
            $sortie->setName($faker->words(4, true));
            $sortie->setDescription($faker->sentence());
            $sortie->setLieu($faker->randomElement($lieux));

            $this->setDates($sortie, 5, 20, -1);

            $maxRegistered = random_int(2,10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $faker->randomElement($participants);
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

            $this->setDates($sortie, -40, -30, -1);

            $maxRegistered = random_int(2,10);
            $sortie->setMaxRegistrationNumber($maxRegistered);

            $owner = $faker->randomElement($participants);
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

    /**
     * Permet de calculer les dates par rapport à aujourd'hui
     * Met à jour le attributs de la sortie
     * @param Sortie $sortie
     * @param $min
     * @param $max
     * @param $limit
     * @return void
     * @throws \Random\RandomException
     */
    public function setDates(Sortie &$sortie, $min, $max, $limit): void
    {
        // date début
        $startingDate = $this->faker->dateTimeBetween('+'.$min.' days', '+'.$max.' days');
        $minutes = [0, 15, 30, 45];
        $startingDate->setTime(random_int(7, 23), $this->faker->randomElement($minutes));
        $startingDate = \DateTimeImmutable::createFromMutable($startingDate);
        $sortie->setStartingDate($startingDate);

        // date de fin
        $minutes = \DateInterval::createFromDateString(random_int(0, 55).' minutes');
        $hours = \DateInterval::createFromDateString(random_int(0, 23).' hours');
        // $days = \DateInterval::createFromDateString(random_int(0, 4).' days');
        $endingDate = $startingDate
            //->add( $days )
            ->add( $hours )->add( $minutes );
        $sortie->setEndingDate($endingDate);

        // date limite d'inscription
        $sortie->setRegisterLimitDate($startingDate->modify('-'.$limit.' days'));
    }

}
