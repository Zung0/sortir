<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CsvImportType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImportUtilisateursController extends AbstractController
{
    #[Route('/import/utilisateurs', name: 'app_import_utilisateurs')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $csv = new CsvImportType();
        $file = $this->createForm(CsvImportType::class, $csv);
        $file->handleRequest($request);

        // Create the FormView
        $formView = $file->createView();

        // Valider le fichier téléchargé

        if ($file->isSubmitted() && $file->isValid()) {
            $file = $file->get('file')->getData();

            // Lire le contenu du fichier
            $lines = $this->readCsvFile($file);

            // Initialisation d'un tableau pour stocker les utilisateurs
            $utilisateurs = [];

            // Parcourir le fichier
            foreach ($lines as $line) {
                // Parser la ligne CSV
                $data = str_getcsv($line, ',');

                $utilisateur = new User();
                $utilisateur->setUsername($data[0]); // Colonne 'username'
             //   $utilisateur->setRoles([$data[1]]); // Colonne 'roles'
                $utilisateur->setPassword($data[1]); // Colonne 'password'
                $utilisateur->setImages($data[2]); // Colonne 'images'
                $utilisateur->setNom($data[3]); // Colonne 'nom'
                $utilisateur->setPrenom($data[4]); // Colonne 'prenom'
                $utilisateur->setTelephone($data[5]); // Colonne 'telephone'
                $utilisateur->setEmail($data[6]); // Colonne 'email'

                $utilisateurs[] = $utilisateur;
            }

            // Enregistrer les utilisateurs en base de données
            foreach ($utilisateurs as $utilisateur) {
                $entityManager->persist($utilisateur);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Les utilisateurs ont bien été importés');

            return $this->redirectToRoute('app_import_utilisateurs');
        }

        // Retourne le formualire du dépôt de fichier
        return $this->render('import_utilisateurs/index.html.twig', [
            'form' => $formView
        ]);
    }

    private function readCsvFile($file)
    {
        $lines = [];

        //fopen ouvre un fichier CSV specifié dans le $file et le r veut dire ouvert en lecture
        $handle = fopen($file, "r");

        //fgets lit une ligne du gestionnaire de fichier $handle et attribut à $line
        while (($line = fgets($handle)) !== false) {
            $lines[] = $line;
        }
        //fclose ferme le gestionnaire du fichier
        fclose($handle);
        return $lines;
    }

}

