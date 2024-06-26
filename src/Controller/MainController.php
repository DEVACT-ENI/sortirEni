<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function home(Request $request, SortieRepository $sortieRepository, CampusRepository $campusRepository): Response
    {
        $campus = $request->query->get('campus');
        $keyword = $request->query->get('keyword');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        $organisateur = $request->query->get('organisateur');
        $inscrit = $request->query->get('inscrit');
        $nonInscrit = $request->query->get('nonInscrit');
        $sortiesPassees = $request->query->get('sortiesPassees');

        $sorties = $sortieRepository->searchSorties($campus, $keyword, $dateDebut, $dateFin, $organisateur, $inscrit, $nonInscrit, $sortiesPassees);
        $campuses = $campusRepository->findAll();

        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'campuses' => $campuses,
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
