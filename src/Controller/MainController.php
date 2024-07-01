<?php

namespace App\Controller;

use App\Form\Modeles\FormFilterMainModele;
use App\Form\SearchSortieType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\services\MiseAJourEtatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function home(Request $request, SortieRepository $sortieRepository, CampusRepository $campusRepository, MiseAJourEtatService $miseAJourEtatService): Response
    {
        $formFilter = new FormFilterMainModele();
        $form = $this->createForm(SearchSortieType::class, $formFilter);
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid())
                $sorties = $sortieRepository->searchSorties( $formFilter, $this->getUser(), "-p");
            else
                $sorties = null;
        else {
            $sorties = $sortieRepository->searchSorties(null, null, "-a");
        }

        $miseAJourEtatService->miseAJourEtatSortie($sorties);
        $campuses = $campusRepository->findAll();

        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'campuses' => $campuses,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sortie/{id}', name: 'sortie_view')]
    public function viewSortie(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('The sortie does not exist');
        }

        return $this->render('main/viewsortie.html.twig', [
            'sortie' => $sortie,
        ]);
    }
}
