<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\UpdatePasswordType;
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

        // Formulaire des infos utilisateur
        $user = $this->getUser() ?? new Participant(); // participant
        $form = $this->createForm(ParticipantType::class, $user);
        $form->handleRequest($request);

        // Formulaire de modification de mot de passe
        $pwdForm = $this->createForm(UpdatePasswordType::class);
        $pwdForm->handleRequest($request);

        // Traitement du Formulaire des infos utilisateur
        if ($form->isSubmitted() && $form->isValid()) {

            // vrifier le mot de passe actuel
            $current_password = $form->get('current_password')->getData();
            if ($userPasswordHasher->isPasswordValid($user, $current_password)) {
                try {
                    // mise à jour des infos
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Modifications enregistrées');
                } catch (\Exception $exception) {
                    $this->addFlash('danger', "Une erreur s'est produite :" . $exception->getMessage());
                }

            } else {
                $this->addFlash('danger', "Mot de passe incorrect: les modifications n'ont pas été enregistrées.");
            }
            return $this->redirectToRoute('participant_myprofile');
        }

        // Traitement du Formulaire de modification de mot de passe
        if ($pwdForm->isSubmitted() && $pwdForm->isValid()) {

            // vrifier le mot de passe actuel
            $currentPassword = $pwdForm->get('current_password')->getData();
            if ($userPasswordHasher->isPasswordValid($user, $currentPassword)) {

                // hasher le nouveau mot de passe
                $newPassword = $pwdForm->get('password')->getData();
                $hashed_password = $userPasswordHasher->hashPassword($user, $newPassword);

                try {
                    // mise à jour du mot de passe
                    $user->setPassword($hashed_password);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Mot de passe modifié');
                } catch (\Exception $exception) {
                    $this->addFlash('danger', "Une erreur s'est produite :" . $exception->getMessage());
                }


            } else {
                $this->addFlash('danger', "Mot de passe actuel incorrect: le mot de passe n'a pa été modifié");
            }
            return $this->redirectToRoute('participant_myprofile');
        }

        return $this->render('participant/myprofile.html.twig', [
            'form' => $form,
            'pwdForm' => $pwdForm,
        ]);
    }
}