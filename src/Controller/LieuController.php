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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED")]
#[Route('/lieux', name: 'lieux_')]
class LieuController extends AbstractController
{
    #[Route('/create', name: 'create')]
    #[Route('/create/{id}', name: 'createModif')]
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
//            return $this->redirectToRoute('sorties_update', ['id' => $id]);
            if(!$id) {
                return $this->redirectToRoute('sorties_create');}
            else{
                return $this->redirectToRoute('sorties_update', ['id' => $id]);
            }


        }

        return $this->render('lieu/lieuCreate.html.twig', [
            "lieuForm" => $lieuForm->createView(),
        ]);

    }
}
