<?php

namespace App\Utils;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use DateTime;

class SortiesFilter
{
    private Campus $campus;
    private ?string $sortieName ='';
    private ?DateTime $minStartDate = null;
    private ?DateTime $maxStartDate = null;
    private ?bool $isOwner = null;
    private ?bool $isRegisteredUser = null;
    private ?bool $isNotRegisteredUser= null;
    private ?bool $isFinishedSortie = null;

    public function __construct()
    {
        $this->minStartDate = new DateTime('1970-01-01');
        $this->maxStartDate = new DateTime('3000-01-01');
    }

    public function filterSortie(Sortie $sortie, Participant $user, Campus $campus): bool{
        $startingDate = $sortie->getStartingDate();

        if($sortie->getCampus()->getId() !== $campus->getId()){
            return false;
        }
        if(!str_contains($sortie->getName(), $this->sortieName)){
            return false;
        }
        if($this->minStartDate && $startingDate < $this->minStartDate){
            return false;
        }
        if($this->maxStartDate && $startingDate > $this->maxStartDate){
            return false;
        }
        if($this->isOwner){
            if($sortie->getOwner()->getName() != $user->getName()){
                return false;
            }
        }
        if($this->isRegisteredUser){
            if(!$sortie->getParticipants()->contains($user)){
                return false;
            }
        }
        if ($this->isNotRegisteredUser){
            if($sortie->getParticipants()->contains($user)){
                return false;
            }
        }
        if($this->isFinishedSortie){
            return $sortie->getState()->getNb() === 4;
        }
        return true;
    }


    public function getCampus(): Campus
    {
        return $this->campus;
    }

    public function setCampus(Campus $campus): void
    {
        $this->campus = $campus;
    }
    public function getSortieName(): string
    {
        return $this->sortieName;
    }

    public function setSortieName(string $sortieName): void
    {
        $this->sortieName = $sortieName;
    }

    public function getMinStartDate(): DateTime
    {
        return $this->minStartDate;
    }

    public function setMinStartDate(DateTime $minStartDate): void
    {
            $this->minStartDate = $minStartDate;
    }

    public function getMaxStartDate(): DateTime
    {
        return $this->maxStartDate;
    }

    public function setMaxStartDate(DateTime $maxStartDate): void
    {
        $this->maxStartDate = $maxStartDate;
    }

    public function isOwner(): bool
    {
        return $this->isOwner;
    }

    public function setIsOwner(bool $isOwner): void
    {
        $this->isOwner = $isOwner;
    }

    public function isRegisteredUser(): bool
    {
        return $this->isRegisteredUser;
    }

    public function setIsRegisteredUser(bool $isRegisteredUser): void
    {
        $this->isRegisteredUser = $isRegisteredUser;
    }

    public function isNotRegisteredUser(): bool
    {
        return $this->isNotRegisteredUser;
    }

    public function setIsNotRegisteredUser(bool $isNotRegisteredUser): void
    {
        $this->isNotRegisteredUser = $isNotRegisteredUser;
    }

    public function isFinishedSortie(): bool
    {
        return $this->isFinishedSortie;
    }

    public function setIsFinishedSortie(bool $isFinishedSortie): void
    {
        $this->isFinishedSortie = $isFinishedSortie;
    }



}