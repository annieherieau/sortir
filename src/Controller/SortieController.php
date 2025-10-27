<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
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

    public function __construct(CampusRepository $campusRepository, EtatRepository $etatRepository)
    {
        $this->campusList = $campusRepository->findAll();
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

    #[Route('/sortie/create', name: 'create',  methods: ['POST'])]
    #[Route('/sortie/{id}/edit', name: 'edit', requirements: ['id'=>'\d+'], methods: ['POST'])]
    public function createOrEdit(Request $request, EntityManagerInterface $entityManager, Sortie $sortie=null, int $id=0): Response
    {
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

            // vérifier si la sortie doit être publiée
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
        ]);

    }

    #[Route('/sortie/{id}/delete', name: 'delete', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function delete(Request $request, ?Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if($sortie->isTheOwner($this->getUser()) and $sortie->getStateNb() === EtatEnum::ENCREATION->value){
            // TODO sécurité contre attaques CSRT
           // if($this->isCsrfTokenValid('delete-'.$sortie->getId(), $request->get('token'))){
                try{
                    $entityManager->remove($sortie);
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('success', "La sortie ".$sortie->getName()." a été supprimée.");
                }catch (\Exception $e){
                    $this->addFlash('warning', "La sortie n'a pas pu être supprimée, veuillez contacter l'administrateur");
                }
           // }else{
//                $this->addFlash('danger', 'Attaque CSRT : le sortie n\'a pas pu être supprimée !');
//            }

        }
        return $this->redirectToRoute('sortie_index');
    }
}
