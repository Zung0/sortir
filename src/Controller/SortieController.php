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
        $sortie = $sortieRepository->findAll();
        return $this->render('sortie/liste.html.twig', [
            'controller_name' => 'SortieController',
            'sortie' => $sortie
        ]);
    }
}
