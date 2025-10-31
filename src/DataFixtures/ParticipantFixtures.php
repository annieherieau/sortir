<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $campusList = $manager->getRepository(Campus::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setName($faker->lastName());
            $participant->setFirstName($faker->firstName());
            $pseudo = $faker->unique()->userName();
            $participant->setPseudo($pseudo);
            $participant->setEmail($pseudo.'@'.$faker->safeEmailDomain());
            if($i === 0) {
                $participant->addRole('ROLE_ADMIN');
            }
            if($i === 1) {
                $participant->setActive(false);
            }
            $password = $this->userPasswordHasher->hashPassword($participant, '123456');
            $participant->setPassword($password);
            $participant->setCampus($faker->randomElement($campusList));
            $manager->persist($participant);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CampusFixtures::class];
    }
}
