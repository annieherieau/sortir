<?php

namespace App\Utils;

use App\Entity\Participant;
use App\Entity\Sortie;
use DateTime;

class SortiesFilter
{
    private string $sortieName;
    private DateTime $minStartDate;
    private DateTime $maxStartDate;
    private bool $isOwner;
    private bool $isRegisteredUser;
    private bool $isNotRegisteredUser;
    private bool $isFinishedSortie;

    public function __construct()
    {
    }

    public function filterSortie(Sortie $sortie, Participant $user): bool{
        $startingDate = $sortie->getStartingDate();
        if(!str_contains($sortie->getName(), $this->sortieName)){
            return false;
        }
        if($startingDate < $this->minStartDate){
            return false;
        }
        if($startingDate > $this->maxStartDate){
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