<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participant', name: 'participant_', methods: ['GET'])]
final class ParticipantController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }
    #[Route('/myprofile', name: 'myprofile', methods: ['GET', 'POST'])]
    public function myaccount(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser() ?? new Participant(); // participant
        $form = $this->createForm(ParticipantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // TODO hasher le mot de passe
            /*
            $newPassword = $form->get('password')->getData();
            dump($newPassword);
            if ($newPassword) {
                // check si le mot de passe actuel est correct
                $current_password = $request->getPayload()->get('current_password');
                dump($current_password);
                if (true){
                    // si oui coder le nouveau
                    $hashed_password = $userPasswordHasher->hashPassword($user, $newPassword);
                    dump($hashed_password);
                    $user->setPassword($hashed_password);
                }

            }
*/
            // mise à jour
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Modifications enregistrées');
            return $this->redirectToRoute('participant_myprofile');
        }
        return $this->render('participant/myprofile.html.twig', [
            'form' => $form,
        ]);
    }

}
