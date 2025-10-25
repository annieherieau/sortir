<?php

namespace App\Controller;

use App\Form\SortieFilterType;
use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'sortie_', methods: ['GET'])]
final class SortieController extends AbstractController
{
    #[Route('', name: 'index', methods: ['POST'])]
    public function index(Request $request,SortieRepository $sortieRepository): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }

        $campus = $user->getCampus();
        $sortiesList = $sortieRepository->findByCampus($campus);
        $sortieFiltersForm = $this->createForm(SortieFilterType::class);
        $sortieFiltersForm->handleRequest($request);

        if ($sortieFiltersForm->isSubmitted()) {
            $filteredList = [];
            $filters = $sortieFiltersForm->getData();
            foreach ($sortiesList as $sortie) {
                if ($filters->filterSortie($sortie, $user)) {
                    $filteredList[] = $sortie;
                }
            }
            $sortiesList = $filteredList;
            dump($sortiesList);
            return $this->render('sortie/index.html.twig', [
                'campus' => $campus,
                'sorties' => $sortiesList,
                'sortieFiltersForm' => $sortieFiltersForm->createView(),
            ]);
        }
        dump($sortiesList);
        return $this->render('sortie/index.html.twig', [
            'campus' => $campus,
            'sorties' => $sortiesList,
            'sortieFiltersForm' => $sortieFiltersForm->createView(),
        ]);
    }


}
