<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Form\EleveType;
use App\Form\NoteType;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EleveController extends AbstractController
{
    /**
     * @Route("/eleve/{id}/fiche", name="fiche_stagiaire")
     */
    public function ficheStagiaire(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'élève/stagiaire par son ID
        $stagiaire = $entityManager->getRepository(Eleve::class)->find($id);

        // S'assurer que l'élève existe
        if (!$stagiaire) {
            throw $this->createNotFoundException('Le stagiaire demandé n\'existe pas.');
        }

        // Rendre une vue et passer les données du stagiaire
        return $this->render('eleve/fiche.html.twig', [
            'nomStagiaire' => $stagiaire->getNom(),
        ]);
    }

    // Méthode pour afficher un élève spécifique
    #[Route('/eleve/{id}', name: 'eleve_show', requirements: ['id' => '\d+'])]
    public function show(Eleve $eleve): Response
    {
        return $this->render('eleve/show.html.twig', [
            'eleve' => $eleve,
        ]);

    }

    // Méthode pour la liste des élèves
    #[Route('/eleve', name: 'eleve_list')]
    public function list(EleveRepository $eleveRepository): Response
    {
        $eleve = $eleveRepository->findAll();
        return $this->render('eleve/list.html.twig', [
            'eleve' => $eleve
        ]);
    }

    #[Route('/eleve/new', name: 'eleve_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $eleve = new Eleve();
        $form = $this->createForm(EleveType::class, $eleve);

        $form->handleRequest($request); // Gère la soumission du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de l'avatar
            $avatarFile = $form->get('avatar')->getData(); // Récupérer le fichier uploadé

            if ($avatarFile) {
                // Générer un nom unique pour le fichier
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename); // Assure un nom "safe"
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    // Déplacer le fichier au répertoire upload configuré
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'), // Défini dans `services.yaml`
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les erreurs lors de l'upload
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo.');
                    return $this->redirectToRoute('eleve_new');
                }

                $eleve->setAvatar($newFilename); // Met à jour la propriété `avatar` de l'entité Eleve
            }

            // Persiste et sauvegarde en base de données
            $entityManager->persist($eleve);


            // Message flash de succès
            $this->addFlash('success', 'Élève ajouté avec succès.');


            // Redirige vers la liste
            return $this->redirectToRoute('eleve_list');

            return $this->redirectToRoute('eleve_show', ['id' => $eleve->getId()]);
        }

        return $this->render('eleve/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/eleve/{id}/delete', name: 'eleve_delete', methods: ['POST'])]
    public function delete(Request $request, Eleve $eleve, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete' . $eleve->getId(), $request->request->get('_token'))) {
            $entityManager->remove($eleve);
            $entityManager->flush();

            $this->addFlash('success', 'Élève supprimé avec succès.');
        }

        return $this->redirectToRoute('eleve_list');
    }

}