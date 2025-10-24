<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    #[Route('/myprofile', name: 'myprofile', methods: ['GET'])]
    public function myaccount(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/myprofile.html.twig', [
        ]);
    }
}
