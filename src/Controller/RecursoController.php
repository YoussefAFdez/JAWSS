<?php

namespace App\Controller;

use App\Entity\Recurso;
use App\Form\RecursoType;
use App\Repository\RecursoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recurso')]
class RecursoController extends AbstractController
{
    #[Route('/', name: 'app_recurso_index', methods: ['GET'])]
    public function index(RecursoRepository $recursoRepository): Response
    {
        return $this->render('recurso/index.html.twig', [
            'recursos' => $recursoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recurso_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RecursoRepository $recursoRepository): Response
    {
        $recurso = new Recurso();
        $form = $this->createForm(RecursoType::class, $recurso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recursoRepository->add($recurso, true);

            return $this->redirectToRoute('app_recurso_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recurso/new.html.twig', [
            'recurso' => $recurso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recurso_show', methods: ['GET'])]
    public function show(Recurso $recurso): Response
    {
        return $this->render('recurso/show.html.twig', [
            'recurso' => $recurso,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recurso_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recurso $recurso, RecursoRepository $recursoRepository): Response
    {
        $form = $this->createForm(RecursoType::class, $recurso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recursoRepository->add($recurso, true);

            return $this->redirectToRoute('app_recurso_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recurso/edit.html.twig', [
            'recurso' => $recurso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recurso_delete', methods: ['POST'])]
    public function delete(Request $request, Recurso $recurso, RecursoRepository $recursoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recurso->getId(), $request->request->get('_token'))) {
            $recursoRepository->remove($recurso, true);
        }

        return $this->redirectToRoute('app_recurso_index', [], Response::HTTP_SEE_OTHER);
    }
}
