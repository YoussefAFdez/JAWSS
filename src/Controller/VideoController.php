<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\UsuarioRepository;
use App\Repository\VideoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/video')]
#[Security("is_granted('ROLE_USER')")]
class VideoController extends AbstractController
{
    #[Route('/', name: 'app_video_index', methods: ['GET'])]
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_video_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VideoRepository $videoRepository, UsuarioRepository $usuarioRepository): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                //Declaramos que no es un fichero corriente y el usuario que lo sube
                $video->getRecurso()->setFichero(false);
                $video->getRecurso()->setPropietario($this->getUser());
                $videoRepository->add($video, true);

                //Agregamos el tamaño del video al total de bytes usados por el usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $imagen->getTamanio());
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', '¡Se ha subido el vídeo "' . $video->getRecurso()->getNombre() . '" con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de subir el vídeo...');
            }

            return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('video/new.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_video_show', methods: ['GET'])]
    public function show(Video $video): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $videoRepository->add($video, true);
                $this->addFlash('exito', '¡Se ha modificado el vídeo "' . $video->getRecurso()->getNombre() . '" con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de modificar el vídeo...');
            }

            return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_video_delete', methods: ['POST'])]
    public function delete(Request $request, Video $video, VideoRepository $videoRepository, UsuarioRepository $usuarioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            try {
                //Recuperamos el tamaño del fichero a eliminar
                $tamanio = $video->getTamanio();

                //Eliminamos el fichero
                $videoRepository->remove($video, true);

                //Quitamos el espacio utilizado de la cuenta del usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $tamanio);

                //Guardamos los cambios del usaurio
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', 'Se ha eliminado la imagen correctamente');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de eliminar la imagen');
            }
        }

        return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
    }
}
