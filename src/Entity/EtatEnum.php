<?php

namespace App\Entity;

enum EtatEnum: int
{
    case ENCREATION = 0;
    case OUVERTE = 1;
    case CLOTUREE = 2;
    case ENCOURS = 3;
    case TERMINEE = 4;
    case ANNULEE = 5;
    case HISTORISEE = 6;

}
