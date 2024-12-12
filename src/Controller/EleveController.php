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
    // Méthode pour afficher un élève spécifique
    #[Route('/eleve/{id}', name: 'eleve_show', requirements: ['id' => '\d+'])]
    public function show(Eleve $eleve): Response
    {
        return $this->render('eleve/show.html.twig', [
            'eleve' => $eleve
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
    #[Route('/eleve/new', name: 'eleve_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eleve = new Eleve();
        $form = $this->createForm(EleveType::class, $eleve);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eleve);
            $entityManager->flush();

            return $this->redirectToRoute('eleve_list');
        }

        return $this->render('eleve/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}