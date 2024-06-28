<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
#[Route('/sorties', name: 'sorties_')]
class SortieController extends AbstractController
{

    #[Route('/create', name: 'create')]
    #[Route('/update/{id}', name: 'update')]
    public function create(
        Request                 $request,
        EntityManagerInterface  $entityManager,
        SortieRepository        $sortieRepository,
        EtatRepository          $etatRepository,
        int                     $id = null
    ): Response
    {
        if ($id) {
            $sortie = $sortieRepository->find($id);
            if ($this->getUser() !== $sortie->getOrganisateur()) {
                throw $this->createAccessDeniedException('You are not allowed to edit this entity if you are not the owner');
            }
        } else {
            $sortie = new Sortie();
            $user = $this->getUser();
            $sortie->setOrganisateur($user);
            $sortie->addListInscrit($user);
            $sortie->setEtat($etatRepository->find(1));
            $sortie->setDateHeureDebut(new \DateTime());
            $sortie->setDateLimiteInscription(new \DateTime());
        }


        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success','Sortie has been created !');
            return $this->redirectToRoute('main_home');

        }
            return $this->render('sortie/create.html.twig', [
                "sortieForm" => $sortieForm->createView(),
                "Title" => $sortie->getId() !=null ?"Mettre à jour la sortie" : "Créer la sortie"
            ]);
    }


    #[Route('/annuler/{id}', name: 'annuler_form', methods: ['GET'])]
    public function annulerSortieForm(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            return $this->json(['error' => 'Sortie not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->render('main/annulersortie.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/annuler/{id}', name: 'annuler', methods: ['POST'])]
    public function annulerSortie(int $id, Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            $this->addFlash('error', 'Sortie pas trouvée');
            return $this->redirectToRoute('main_home');
        }

        $motif = $request->request->get('motif');

        if (!$motif) {
            $this->addFlash('error', 'Motif est requis');
            return $this->redirectToRoute('main_home');
        }

        $sortie->setInfoSortie($motif);

        $etatAnnule = $etatRepository->findOneBy(['libelle' => 'Annulée']);
        if (!$etatAnnule) {
            $this->addFlash('error', 'Etat "Annulée" pas trouvé');
            return $this->redirectToRoute('main_home');
        }

        $sortie->setEtat($etatAnnule);

        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash('success', 'Sortie annulée !');
        return $this->redirectToRoute('main_home');
    }

}
