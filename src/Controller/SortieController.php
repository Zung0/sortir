<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('sortie/liste.html.twig', [
            'controller_name' => 'SortieController',
            'sorties' => $sorties
        ]);
    }
    #[Route('/detail/{id}', name: 'app_detail', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function detail(SortieRepository $sortieRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);
        return $this->render('sortie/detail.html.twig',[
            'sortie' => $sortie
            ]);
    }
    #[Route('/create', name: 'app_create')]
    public function create(): Response
    {
        $sortie = new Sortie();

        //TODO form de créa de sortie.



        return $this->render('sortie/create.html.twig',[
            'sortie' => $sortie
        ]);
    }
}
