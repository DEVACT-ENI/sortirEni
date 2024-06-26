<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sorties', name: 'sorties_')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'create')]
    #[Route('/update/{id}', name: 'update')]
    public function create(
        Request                 $request,
        EntityManagerInterface  $entityManager,
        SortieRepository        $sortieRepository,
        int                     $id = null
    ): Response
    {
        if ($id) {
            $sortie = $sortieRepository->find($id);
            if ($this->getUser() != $sortie->getUser()) {
                throw $this->createAccessDeniedException('You are not allowed to edit this entity if you are not the owner');
            }
        } else {
            $sortie = new Sortie();
            $user = $this->getUser();
            $sortie->setOrganisateur($user);
            $sortie->addListInscrit($user);
        }


        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        //TODO rajouter la condition de la validation
        if ($sortieForm->isSubmitted()) {

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success','Sortie has been created !');
            return $this->redirectToRoute('main_home');

        }
            return $this->render('sortie/create.html.twig', [
                "sortieForm" => $sortieForm->createView(),
            ]);
    }

}
