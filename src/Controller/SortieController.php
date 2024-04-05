<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Helpers\CallAPIService;
use App\Repository\EtatRepository;
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
    public function index(SortieRepository $sortieRepository, EtatRepository $statutRepository): Response
    {
        $statut = $statutRepository->find(3);
        $sortiesBeforeFilter = $sortieRepository->findAll();
        $oneMonthFromNow = new \DateTime();
        $oneMonthFromNow->modify('+1 month');

        $sorties = []; // Initialisez le tableau de sorties filtrées

        foreach ($sortiesBeforeFilter as $sortie) {
            $dateDebut = $sortie->getDateHeureDebut();
            $interval = $dateDebut->diff($oneMonthFromNow);
            if ($interval->invert === 0 && $interval->m <= 1) {
                // La sortie commence dans le mois actuel ou dans le mois suivant
                $sorties[] = $sortie;
            } else {
                // La sortie commence après le mois suivant
                $sortie->setStatut($statut);
            }
        }

        return $this->render('sortie/liste.html.twig', [
            'controller_name' => 'SortieController',
            'sorties' => $sorties,
            'oneMonthFromNow' => $oneMonthFromNow
        ]);
    }

    #[Route('/detail/{id}', name: 'app_detail', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function detail(SortieRepository $sortieRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);
        $nbParticipants = $sortieRepository->countParticipants($id);
        $participants = $sortie->getParticipants();

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
            'nbParticipants' => $nbParticipants,
            'user' => $this->getUser(),
            'participants' => $participants
        ]);
    }

    #[Route('/inscription/{id}', name: 'app_inscription', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function inscription(SortieRepository $sortieRepository, int $id, EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        if($sortie->getDateLimiteInscription() > new \DateTime())
        {
            if (!$sortie->getParticipants()->contains($this->getUser())) {
                $nbplaces = $sortie->getNbinscriptionMax();
                $nbplaces--;
                $sortie->setNbinscriptionMax($nbplaces);
            }

            $sortie->addParticipant($this->getUser());
            $em->persist($sortie);
            $em->flush();
            //dd($sortie);

            return $this->redirectToRoute('app_detail', ['id' => $id]);
        }

        $this->addFlash('error', 'La date limite d\'inscription est dépassée');
        return $this->redirectToRoute('app_detail', ['id' => $id]);
    }

    #[Route('/annulerInscription/{id}', name: 'app_annulerInscription', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function annulerInscription(SortieRepository $sortieRepository, int $id, EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        if ($sortie->getParticipants()->contains($this->getUser())) {
            $nbplaces = $sortie->getNbinscriptionMax();
            $nbplaces++;
            $sortie->setNbinscriptionMax($nbplaces);
        }
        $sortie->removeParticipant($this->getUser());
        $em->persist($sortie);
        $em->flush();
        return $this->redirectToRoute('app_detail', ['id' => $id]);
    }

    #[Route('/delete/{id}', name: 'app_delete', requirements: ['id' => '\d+'])]
    public function deleteSortie(SortieRepository $sortieRepository, int $id, EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        if ($sortie->getOrganisateur() === $this->getUser()) {
            $em->remove($sortie);
            $em->flush();
            return $this->redirectToRoute('app_sortie');
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer une sortie dont vous n\'êtes pas l\'organisateur');
        }

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
                $sortie->setOrganisateur($this->getUser());

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

    #[Route('/modifier/{id}', name: 'app_modifier', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function modifier(\Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $em, CallAPIService $callService, Sortie $sortie): Response
    {


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
                $sortie->setOrganisateur($this->getUser());

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