<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/site', name: 'app_admin_site')]
    public function addSite(Request $request, EntityManagerInterface $em, User $user): Response
    {
        if ($this->getUser()->getRoles() === array('ROLE_ADMIN')) {
            $site = new Site();
            $formSite = $this->createForm(SiteType::class, $site);
            $formSite->handleRequest($request);
            if ($formSite->isSubmitted() && $formSite->isValid()) {
                $em->persist($site);
                $em->flush();
                $this->addFlash('success', 'Site ajouté avec succès');
                return $this->redirectToRoute('app_admin_site');

            }
            return $this->render('admin/newSite.html.twig', [
                'formSite' => $formSite
            ]);
        }
        return $this->redirectToRoute('app_login');
    }
}
