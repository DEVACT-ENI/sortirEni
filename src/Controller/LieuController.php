<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lieux', name: 'lieux_')]
class LieuController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(
        Request                $request,
        EntityManagerInterface $entityManager,
        LieuRepository         $lieuRepository,
        int                    $id = null

    ): Response
    {
        $lieu = new Lieu();

        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'New Lieu has been created !');
            return $this->redirectToRoute('sorties_create');

        }

        return $this->render('lieu/lieuCreate.html.twig', [
            "lieuForm" => $lieuForm->createView(),
        ]);

    }
}
