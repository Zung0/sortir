<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Helpers\CallAPIService;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Request;
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
        $nbParticipants = $sortieRepository->countParticipants($id);

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
            'nbParticipants' => $nbParticipants
        ]);
    }

    #[Route('/inscription/{id}', name: 'app_inscription', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function inscription(SortieRepository $sortieRepository, int $id, EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        $sortie->addParticipant($this->getUser());
        $em->persist($sortie);
        $em->flush();
        //dd($sortie);

        return $this->redirectToRoute('app_detail', ['id' => $id]);
    }

    #[Route('/create', name: 'app_create')]
    public function create(\Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $em, CallAPIService $callService): Response
    {
        $sortie = new Sortie();

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /**@var Sortie $newSortie * */
            $newSortie = $form->getData();
            $newLocation = $newSortie->getLieu();
            $responseApi = $callService->getFranceDataLoc($newLocation);
            if (array_key_exists('features', $responseApi) && count($responseApi['features']) > 0) {
                $newLocation->setLongitude($responseApi['features'][0]['geometry']['coordinates'][0])
                    ->setLatitude($responseApi['features'][0]['geometry']['coordinates'][1]);
                //$user = $this->getUser();
                //$newSortie->setOrganisateur($user);

                $em->persist($sortie);
                $em->flush();
                $this->addFlash('success', 'La sortie a bien été crée');
                return $this->redirectToRoute('app_sortie');
            }

        }

        return $this->render('sortie/form.html.twig', [
            'createForm' => $form
        ]);
    }
}