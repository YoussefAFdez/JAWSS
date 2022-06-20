<?php

namespace App\Controller;

use App\Entity\Tier;
use App\Form\TierType;
use App\Repository\TierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tier')]
class TierController extends AbstractController
{
    #[Route('/', name: 'app_tier_index', methods: ['GET'])]
    public function index(TierRepository $tierRepository): Response
    {
        return $this->render('tier/index.html.twig', [
            'tiers' => $tierRepository->findOrdenadosByAlmacenamiento(),
        ]);
    }

    #[Route('/new', name: 'app_tier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TierRepository $tierRepository): Response
    {
        $tier = new Tier();
        $form = $this->createForm(TierType::class, $tier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $almacenamiento = $form->get('almacenamiento')->getData();
                $tier->setAlmacenamiento(intval($almacenamiento) * 1048576);

                $tierRepository->add($tier, true);

                $this->addFlash('exito', '¡Se ha creado el tier "' . $tier->getNombre() . '" con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de crear el tier...');
            }

            return $this->redirectToRoute('app_tier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tier/new.html.twig', [
            'tier' => $tier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tier_show', methods: ['GET'])]
    public function show(Tier $tier): Response
    {
        return $this->render('tier/show.html.twig', [
            'tier' => $tier,
            'usuarios' => $tier->getUsuarios(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tier $tier, TierRepository $tierRepository): Response
    {
        $form = $this->createForm(TierType::class, $tier, [
            'tamanio' => $tier->getAlmacenamiento() / 1048576,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try{
                $almacenamiento = $form->get('almacenamiento')->getData();
                $tier->setAlmacenamiento(intval($almacenamiento) * 1048576);

                $tierRepository->add($tier, true);
                $this->addFlash('exito', '¡Se ha modificado el tier "' . $tier->getNombre() . '" con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de modificar el tier...');
            }


            return $this->redirectToRoute('app_tier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tier/edit.html.twig', [
            'tier' => $tier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tier_delete', methods: ['POST'])]
    public function delete(Request $request, Tier $tier, TierRepository $tierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tier->getId(), $request->request->get('_token'))) {
            $tierRepository->remove($tier, true);
        }

        return $this->redirectToRoute('app_tier_index', [], Response::HTTP_SEE_OTHER);
    }
}
