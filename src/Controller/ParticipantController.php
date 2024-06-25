<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participants', name: 'participants_')]
class ParticipantController extends AbstractController
{
    #[Route('/modifProfil', name: 'modif_profil', methods: ['PUT'])]
    public function modifProfil(): Response
    {


        return $this->render('participant/modif-profil.html.twig', [

        ]);
    }
}
