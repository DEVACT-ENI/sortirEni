<?php

namespace App\Controller;

use App\Form\PhotoProfilType;
use App\Form\ModifProfilType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
#[Route('/participants', name: 'participants_')]
class ParticipantController extends AbstractController
{
    #[Route('/modif-profil', name: 'modif_profil', methods: ['GET', 'POST'])]
    public function modifProfil(ParticipantRepository $participantRepository, Request $request, UserPasswordHasherInterface $hasher, FileUploader $fileUploader): Response
    {
        $participant =  $participantRepository->find($this->getUser()->getId());
        $participant2 = clone $participant;
        $form = $this->createForm(ModifProfilType::class, $participant2);
        $formPhoto = $this->createForm(PhotoProfilType::class);
        $formPhoto->handleRequest($request);
        $form->handleRequest($request);

        if ($formPhoto->isSubmitted() && $formPhoto->isValid()) {
            /** @var UploadedFile $file */
            $file = $formPhoto['photo']->getData();

            if ($file)
                $fileName = $fileUploader->upload($file, (string)$participant->getId());
            return $this->redirectToRoute('participants_modif_profil');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            dump("j'ai passe la validation");
            if ($form->get('password')->getData() != null)
                $participant2->setPassword($hasher->hashPassword(
                    $participant,
                    $form->get('password')->getData()));

            $participant->copyFrom($participant2);
            $participantRepository->save($participant);
            $this->addFlash('success', 'Profil modifié avec succès');
            return $this->redirectToRoute('participants_modif_profil');
        }

        return $this->render('participant/modif-profil.html.twig', [
            'form' => $form->createView(),
            'formPhoto' => $formPhoto->createView(),
            'participant' => $participant,
        ]);
    }

    #[Route('/inscription-sortie/{id}', name: 'inscription_sortie', methods: ['GET'])]
    public function inscription(SortieRepository $sortieRepository, int $id): Response
    {
        $sortieRepository->inscriptionOrInvert($id, $this->getUser(), "-i");

        return $this->redirectToRoute('main_home');
    }

    #[Route('/desinscription-sortie/{id}', name: 'desinscription_sortie', methods: ['GET'])]
    public function desinscription(SortieRepository $sortieRepository, int $id): Response
    {
        $sortieRepository->inscriptionOrInvert($id, $this->getUser(), "-d");

        return $this->redirectToRoute('main_home');
    }

    #[Route('/detail/{id}', name: 'detail', methods: ['GET'])]
    public function detail(ParticipantRepository $participantRepository, int $id): Response
    {
        $participant = $participantRepository->find($id);

        return $this->render('participant/detail.html.twig', [
            'participant' => $participant,
        ]);
    }

}
