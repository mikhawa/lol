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

    // Méthode pour ajouter un nouvel élève
    #[Route('/eleve/new', name: 'eleve_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eleve = new Eleve();
        $form = $this->createForm(EleveType::class, $eleve);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eleve);
            $entityManager->flush();

            $this->addFlash('success', 'Élève ajouté avec succès.');

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