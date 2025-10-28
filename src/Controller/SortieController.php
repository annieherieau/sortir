<?php

namespace App\Controller;


use App\Form\SortieFilterType;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\EtatEnum;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Utils\SortiesFilter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'sortie_', methods: ['GET'])]
final class SortieController extends AbstractController
{
    /**
     * @var Etat[]|array
     */
    private array $etats;

    private array $campusList;

    private array $sortiesList;

    public function __construct(CampusRepository $campusRepository, EtatRepository $etatRepository,
                                SortieRepository $sortieRepository)
    {
        $this->campusList = $campusRepository->findAll();
        $this->sortiesList = $sortieRepository->findAllActive();
        $this->etats = $etatRepository->findAll();
    }

    #[Route('', name: 'index', methods: ['POST'])]
    public function index(Request $request,SortieRepository $sortieRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $campus = $user->getCampus();
        $sortiesList = $this->sortiesList;
        $filters = new SortiesFilter();
        $sortieFiltersForm = $this->createForm(SortieFilterType::class, $filters);
        $sortieFiltersForm->handleRequest($request);

        if ($sortieFiltersForm->isSubmitted()) {
            $selectedCampus = $sortieFiltersForm->getData()->getCampus();
            $filteredList = [];
            foreach ($sortiesList as $sortie) {
                if ($filters->filterSortie($sortie, $user, $selectedCampus)) {
                    $filteredList[] = $sortie;
                }
            }
            $sortiesList = $filteredList;
        }
        return $this->render('sortie/index.html.twig', [
            'campusList' => $this->campusList,
            'campus' => $campus,
            'sorties' => $sortiesList,
            'sortieFiltersForm' => $sortieFiltersForm->createView(),
        ]);
    }

    #[Route('/sortie/{id}/detail', name: 'detail',requirements: ['id'=>'\d+'])]
    public function detail(SortieRepository $sortieRepository, int $id): Response
    {
        $sortie = $sortieRepository->findOneBy(['id' => $id]);

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
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
    #[Route('/sortie/{id}/publish', name: 'publish')]
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

    #[Route('/sortie/create', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $sortie = new Sortie();
        $sortie->setCampus($user->getCampus());
        $sortie->setOwner($user);
        $sortie->setState($this->etats[EtatEnum::ENCREATION->value]);


        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        dump($sortieForm);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $duration =  strval($sortieForm->get('durationInMunites')->getData());
            $sortie->setEndingDateWithDurationInMunutes($duration);

//            $publier = $sortieForm->get('publier')->getData();
//
//            if($publier){
//                $sortie->setState($this->etats[EtatEnum::OUVERTE->value]);
//            }

            try{

                $entityManager->persist($sortie);
                $entityManager->flush();
                $message  = 'Votre sortie a été enregistrée';

//                if($publier){
//                    $this->addFlash('success', $message.' et publiée');
//                    return $this->redirectToRoute('sortie_publish', ['id' => $sortie->getId()]);
//                }else{
                    $this->addFlash('success', $message);
                    return $this->redirectToRoute('sortie_index');
//                }

            }catch (\Exception $e){
                $this->addFlash('danger', $e->getMessage());
                return $this->redirectToRoute('sortie_create');
            }

        }

        return $this->render('sortie/create.html.twig', [
            'titre' => 'Créer une sortie',
            "sortie" => $sortie,
            'sortieForm' => $sortieForm->createView(),
        ]);

    }
}
