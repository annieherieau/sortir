<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startingDate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endingDate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $registerLimitDate = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\GreaterThan(0)]
    private ?int $maxRegistrationNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(inversedBy: 'ownedEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $owner = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'sorties')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $state = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $cancelMemo = null;


    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartingDate(): ?\DateTimeImmutable
    {
        return $this->startingDate;
    }

    public function setStartingDate(\DateTimeImmutable $startingDate): static
    {
        $this->startingDate = $startingDate;

        return $this;
    }

    public function getEndingDate(): ?\DateTimeImmutable
    {
        return $this->endingDate;
    }

    public function setEndingDate(\DateTimeImmutable $endingDate): static
    {
        $this->endingDate = $endingDate;

        return $this;
    }

    public function getRegisterLimitDate(): ?\DateTimeImmutable
    {
        return $this->registerLimitDate;
    }

    public function setRegisterLimitDate(\DateTimeImmutable $registerLimitDate): static
    {
        $this->registerLimitDate = $registerLimitDate;

        return $this;
    }

    public function getMaxRegistrationNumber(): ?int
    {
        return $this->maxRegistrationNumber;
    }

    public function setMaxRegistrationNumber(int $maxRegistrationNumber): static
    {
        $this->maxRegistrationNumber = $maxRegistrationNumber;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getOwner(): ?Participant
    {
        return $this->owner;
    }

    public function setOwner(?Participant $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    // TODO basculer les vérifications dans un SortieManager, car ce n'est pas compatible avec les fixtures
    public function addParticipant(Participant $participant): static
    {
        $this->participants->add($participant);
        $participant->addSortie($this);
        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        $this->participants->removeElement($participant);
        return $this;
    }

    public function getState(): ?Etat
    {
        return $this->state;
    }

    public function setState(?Etat $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Renvoie la durée de la sortie par défault en DateInterval, ou en jours, heures, minutes si précisé
     * @param string $str
     * @return mixed
     */
    public function getDuration(string $str = 'interval'): mixed
    {
        $duration = $this->startingDate->diff($this->endingDate);
        $array = ['interval'=>$duration, 'd'=>$duration->days, 'h' => $duration->h, 'i' => $duration->i];
        return $array[$str];
    }

    /**
     * Permet de vérifier si un participant est l'organisateur de la sortie
     * @param Participant|null $participant
     * @return bool
     */
    public function isTheOwner(?Participant $participant): bool
    {
        return $this->owner === $participant;
    }

    /**
     * Permet de vérifier si un participant est inscrit à la sortie
     * @param Participant|null $participant
     * @return bool
     */
    public function isRegistred(?Participant $participant): bool
    {
        return $this->participants->contains($participant);
    }

    public function getStateNb(): ?int
    {
        return $this->getState()->getNb();
    }

    public function getStateLibelle(): ?string
    {
        return $this->getState()->getLibelle();
    }


    /**
     * Permet de charger l'état désiré via le numero.
     * @param int $etatNb // EtatEnum::ETATNAME->value
     * @param EtatRepository $etatRepository
     * @return Etat|null
     */
    public function findEtatbyEnum(int $etatNb, EtatRepository $etatRepository): ?Etat
    {
        return $etatRepository->findOneBy(['nb' => $etatNb]);
    }

    /**
     * Permet de calculer la dateHeure de fin en ajoutant la durée en minute
     * @param int $minutes
     * @return $this
     */
    public function setEndingDateWithDurationInMunutes(int $minutes): static
    {
        $this->endingDate = $this->startingDate->modify('+'.$minutes.' minutes');
        return $this;
    }

    /**
     * Renvoie si la sortie peut être annulée
     * @return bool
     */
    public function isCancellable(): bool
    {
        return ($this->getStateNb() === EtatEnum::OUVERTE->value || $this->getStateNb() === EtatEnum::CLOTUREE->value)
            && $this->getStartingDate() > new \DateTimeImmutable();
    }

    /**
     * Renvoie si la sortie est en création (brouillon)
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->getStateNb() === EtatEnum::ENCREATION->value;
    }

    public function getCancelMemo(): ?string
    {
        return $this->cancelMemo;
    }

    public function setCancelMemo(?string $cancelMemo): static
    {
        $this->cancelMemo = $cancelMemo;

        return $this;
    }

}
