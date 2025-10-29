<?php

namespace App\Controller;


use App\Entity\Lieu;
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
        $filters->setCampus($campus);

        $sortieFiltersForm = $this->createForm(SortieFilterType::class, $filters);

        $sortieFiltersForm->handleRequest($request);
        $selectedCampus = $campus;

        if ($sortieFiltersForm->isSubmitted()) {
            $selectedCampus = $sortieFiltersForm->getData()->getCampus() ?? $campus;
            $minStartDate = $sortieFiltersForm->get('minStartDate')->getData();
            if($minStartDate){
                $filters->setMinStartDate($minStartDate);
            }
            $maxStartDate = $sortieFiltersForm->get('maxStartDate')->getData();
            if($maxStartDate){
                $filters->setMaxStartDate($maxStartDate);
            }

        }

        $filteredList = [];
        foreach ($sortiesList as $sortie) {
            if ($filters->filterSortie($sortie, $user, $selectedCampus)) {
                $filteredList[] = $sortie;
            }
        }
        $sortiesList = $filteredList;
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
    #[Route('/sortie/{id}/publish', name: 'publish', requirements: ['id'=>'\d+'])]
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

    /**
     * Créer une nouvelle sortie ou modifier une sortie existante
     * Modification par l'organisateur des sortie en statut ENCREATION
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Sortie|null $sortie
     * @param int $id
     * @return Response
     */
    #[Route('/sortie/{id}/create', name: 'create',   requirements: ['id'=>'\d+'],methods: ['POST'])]
    #[Route('/sortie/{id}/edit', name: 'edit',  requirements: ['id'=>'\d+'],methods: ['POST'])]
    public function createOrEdit(Request $request, EntityManagerInterface $entityManager, ?Sortie $sortie, int $id=0): Response
    {
        $lieuxList = $entityManager->getRepository(Lieu::class)->findAll();
        $lieux = [];
        foreach ($lieuxList as $lieu) {
            $lieux[$lieu->getId()] = [
                'street' => $lieu->getStreet(),
                'codeAndVille' => $lieu->getCodeAndVille(),
                'coordToString' => $lieu->getCoordToString()];
        }
        if($sortie === null){
            $titre = 'Créer une sortie';
            $sortie = new Sortie();
            $user = $this->getUser();
            $sortie->setCampus($user->getCampus());
            $sortie->setOwner($user);
            if($sortie->getState() === null){
                $sortie->setState($this->etats[EtatEnum::ENCREATION->value]);
            }
            $durationInMinutes = 0;
        }else{
            $titre = 'Modifier la sortie';

            // Modification des sortie en création uniquement
            if($sortie->getStateNb() !== EtatEnum::ENCREATION->value && !$sortie->isTheOwner($this->getUser())){
                return $this->redirectToRoute('sortie_index');
            }

            // durée en minutes à partir de la dateHeure de fin
            $duration = $sortie->getDuration();
            $durationInMinutes = 24*60*$duration->d + 60*$duration->h + $duration->i;
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        // Durée en minutes par défaut dans le formulaire
        if ($durationInMinutes){
            $sortieForm->get('durationInMunites')->setData($durationInMinutes);
        }
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            // recalcul de la dateHeure de fin
            $duration =  strval($sortieForm->get('durationInMunites')->getData());
            $sortie->setEndingDateWithDurationInMunutes($duration);

            // Soumission par la bouton "Publier"
            $publier = $sortieForm->get('publier')->getData();
            if($publier){
                $sortie->setState($this->etats[EtatEnum::OUVERTE->value]);
            }

            try{

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Votre sortie a été enregistrée'.($publier ? ' et publiée' :''));
                return $this->redirectToRoute('sortie_index');

            }catch (\Exception $e){
                $this->addFlash('danger', $e->getMessage());
                return $this->redirectToRoute('sortie_edit', ['id' => $sortie->getId()]);
            }

        }

        return $this->render('sortie/form.html.twig', [
            'titre' => $titre,
            "sortie" => $sortie,
            'sortieForm' => $sortieForm->createView(),
            'lieux' => $lieux
        ]);

    }

    #[Route('/sortie/{id}/delete', name: 'delete', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function delete(Request $request, ?Sortie $sortie, EntityManagerInterface $entityManager, int $id=0): Response
    {
        if($sortie->isTheOwner($this->getUser()) and $sortie->getStateNb() === EtatEnum::ENCREATION->value){
            // sécurité contre attaques CSRT
            if($this->isCsrfTokenValid('delete-'.$sortie->getId(), $request->get('token'))){
                try{
                    $entityManager->remove($sortie);
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('success', "La sortie ".$sortie->getName()." a été supprimée.");
                }catch (\Exception $e){
                    $this->addFlash('warning', "La sortie n'a pas pu être supprimée, veuillez contacter l'administrateur");
                }
            }else{
                $this->addFlash('danger', 'Attaque CSRT : le sortie n\'a pas pu être supprimée !');
            }

        }
        return $this->redirectToRoute('sortie_index');
    }
}
