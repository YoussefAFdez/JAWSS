<?php

namespace App\Controller;

use App\Entity\Audio;
use App\Form\AudioType;
use App\Repository\AudioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/audio')]
class AudioController extends AbstractController
{
    #[Route('/', name: 'app_audio_index', methods: ['GET'])]
    public function index(AudioRepository $audioRepository): Response
    {
        return $this->render('audio/index.html.twig', [
            'audios' => $audioRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_audio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AudioRepository $audioRepository): Response
    {
        $audio = new Audio();
        $form = $this->createForm(AudioType::class, $audio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $audioRepository->add($audio, true);

            return $this->redirectToRoute('app_audio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('audio/new.html.twig', [
            'audio' => $audio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_audio_show', methods: ['GET'])]
    public function show(Audio $audio): Response
    {
        return $this->render('audio/show.html.twig', [
            'audio' => $audio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_audio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Audio $audio, AudioRepository $audioRepository): Response
    {
        $form = $this->createForm(AudioType::class, $audio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $audioRepository->add($audio, true);

            return $this->redirectToRoute('app_audio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('audio/edit.html.twig', [
            'audio' => $audio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_audio_delete', methods: ['POST'])]
    public function delete(Request $request, Audio $audio, AudioRepository $audioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$audio->getId(), $request->request->get('_token'))) {
            $audioRepository->remove($audio, true);
        }

        return $this->redirectToRoute('app_audio_index', [], Response::HTTP_SEE_OTHER);
    }
}
