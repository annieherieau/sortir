<?php

namespace App\Controller;

use App\Form\SortieFilterType;
use App\Repository\SortieRepository;
use App\Utils\SortiesFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'sortie_', methods: ['GET', 'POST'])]
final class SortieController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request,SortieRepository $sortieRepository): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }

        $campus = $user->getCampus();
        $sortiesList = $sortieRepository->findByCampus($campus);
        $filters = new SortiesFilter();
        $sortieFiltersForm = $this->createForm(SortieFilterType::class, $filters);
        $sortieFiltersForm->handleRequest($request);

        if ($sortieFiltersForm->isSubmitted()) {
            $filteredList = [];
            foreach ($sortiesList as $sortie) {
                if ($filters->filterSortie($sortie, $user)) {
                    $filteredList[] = $sortie;
                }
            }
            $sortiesList = $filteredList;
        }
        return $this->render('sortie/index.html.twig', [
            'campus' => $campus,
            'sorties' => $sortiesList,
            'sortieFiltersForm' => $sortieFiltersForm->createView(),
        ]);
    }


}
