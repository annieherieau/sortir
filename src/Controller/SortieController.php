<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'sortie_', methods: ['GET'])]
final class SortieController extends AbstractController
{
    public function __construct(CampusRepository $campusRepository)
    {
        $this->campusList = $campusRepository->findAll();
    }

    #[Route('', name: 'index')]
    public function index(SortieRepository $sortieRepository): Response
    {

        /* TODO ajouter losrque dev login OK
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $campus = $user->getCampus();
        */

        /* TODO enlever losrque dev login OK */
        $campusList = $this->campusList;
        $campusDefault = $campusList[0];
        foreach ($campusList as $c) {
            if ($c->getSorties()->count() >0)
            {
                $campusDefault = $c;
            }
        }
        $user = $this->getUser() ?? new Participant();
        $campus = $user->getCampus() ?? $campusDefault;
        /**/

        $sorties = $sortieRepository->findByCampus($campus);
        dump($sorties);
        return $this->render('sortie/index.html.twig', [
            'campusList' => $this->campusList,
            'campus' => $campus,
            'sorties' => $sorties,
        ]);
    }
}
