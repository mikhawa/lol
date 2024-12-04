<?php

namespace App\Controller;

use App\Repository\EleveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EleveController extends AbstractController
{
    #[Route('/eleve/{id}', name: 'eleve_show')]
    public function show(int $id, EleveRepository $eleveRepository): Response
    {
        $eleve = $eleveRepository->find($id);

        if (!$eleve) {
            throw $this->createNotFoundException('Élève non trouvé');
        }

        return $this->render('eleve/index.html.twig', [
            'eleve' => $eleve, // Transmission de la variable au template
        ]);
    }
    #[Route('/eleves', name: 'eleve_list')]
    public function list(EleveRepository $eleveRepository): Response
    {
        $eleves = $eleveRepository->findAll();
        return $this->render('eleve/list.html.twig', [
            'eleves' => $eleves,
        ]);
    }
}
