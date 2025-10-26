<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
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

    public function __construct(CampusRepository $campusRepository)
    {
        $this->campusList = $campusRepository->findAll();
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

        return $this->render('sortie/index.html.twig', [
            'campusList' => $this->campusList,
            'campus' => $campus,
            'sorties' => $sorties,
        ]);
    }

    #[Route('/sortie/{id}/detail', name: 'detail',requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function detail(SortieRepository $sortieRepository, int $id): Response{
        $sortie = $sortieRepository->findOneBy(['id'=>$id]);

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
        ]);
    }
}
