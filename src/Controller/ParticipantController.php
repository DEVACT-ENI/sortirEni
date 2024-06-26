<?php

namespace App\Controller;

use App\Form\ModifProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participants', name: 'participants_')]
class ParticipantController extends AbstractController
{
    #[Route('/modifProfil/{id}', name: 'modif_profil', methods: ['GET', 'POST'])]
    public function modifProfil(ParticipantRepository $participantRepository, Request $request,UserPasswordHasherInterface $hasher, int $id): Response
    {
        $participant = $participantRepository->find($id);
        $form = $this->createForm(ModifProfilType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData() != null)
            $participant->setPassword($hasher->hashPassword(
                $participant,
                $form->get('password')->getData()));
            $participantRepository->save($participant);
            return $this->redirectToRoute('main_home');
        }

        return $this->render('participant/modif-profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}