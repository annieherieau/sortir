<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\EtatEnum;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'sortie_', methods: ['GET'])]
final class SortieController extends AbstractController
{
    /**
     * @var Campus[]|array
     */
    private array $campusList;
    /**
     * @var Etat[]|array
     */
    private array $etats;

    public function __construct(CampusRepository $campusRepository, EtatRepository $etatRepository)
    {
        $this->campusList = $campusRepository->findAll();
        $this->etats = $etatRepository->findAll();
    }

    #[Route('', name: 'index')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $campus = $user->getCampus();

        $sorties = $sortieRepository->findByCampus($campus);
        dump($sorties);
        return $this->render('sortie/index.html.twig', [
            'campusList' => $this->campusList,
            'campus' => $campus,
            'sorties' => $sorties,
        ]);
    }

    /**
     * Publier une sortie:
     * Qui ? l'organisateur
     * Etat ? ENCREATION -> OUVERTE
     * @param Sortie $sortie
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/sortie/{id}/publish', name: 'publish', methods: ['GET'])]
    public function publish(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if($sortie->isTheOwner($user) and $sortie->getStateNb() === EtatEnum::ENCREATION->value){
            $state = $this->etats[EtatEnum::OUVERTE->value];
            $sortie->setState($state);
            try{
                $entityManager->persist($sortie);
                $entityManager->flush();
                //$this->addFlash('succes', "La sortie ".$sortie->getName()." a été publiée.");
            }catch (\Exception $e){
                $this->addFlash('warning', "La sortie n'a pas pu être publiée, veuillez contacter l'administrateur");
            }
        }

        return $this->redirectToRoute('sortie_index');
    }
}
