<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\RecursoRepository;
use App\Repository\UsuarioRepository;
use App\Repository\VideoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/video')]
#[Security("is_granted('ROLE_USER')")]
class VideoController extends AbstractController
{
    #[Route('/', name: 'app_video_index', methods: ['GET'])]
    public function index(VideoRepository $videoRepository, RecursoRepository $recursoRepository): Response
    {
        //Recogemos los recursos que pertenecen al usuario y los agregamos en un array
        $recursosUsuario = $recursoRepository->findByUsuario($this->getUser());
        $videos = [];
        foreach ($recursosUsuario as $recurso) {
            $video = $videoRepository->findByRecurso($recurso);
            if (!empty($video)) $videos[] = $video;
        }

        //Recogemos los recursos compartidos con el usuario y los agregamos en un array
        $recursosAccesibles = $this->getUser()->getRecursosAccesibles();
        $videosCompartidos = [];
        foreach ($recursosAccesibles as $recurso) {
            $videoCompartido = $videoRepository->findByRecurso($recurso);
            if (!empty($videoCompartido)) $videosCompartidos[] = $videoCompartido;
        }

        return $this->render('video/index.html.twig', [
            'videos' => $videos,
            'videosCompartidos' => $videosCompartidos,
        ]);
    }

    #[Route('/new', name: 'app_video_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VideoRepository $videoRepository, UsuarioRepository $usuarioRepository): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video, [
            'nuevo' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                //Declaramos que no es un fichero corriente y el usuario que lo sube
                $video->getRecurso()->setFichero(false);
                $video->getRecurso()->setPropietario($this->getUser());
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $video->getRecurso()->addFavorito($this->getUser());
                } else {
                    $video->getRecurso()->removeFavorito($this->getUser());
                }

                $nombreFichero = $form->get('videoFile')->getData()->getClientOriginalName();
                $extension = pathinfo($nombreFichero, PATHINFO_EXTENSION);

                $extensionesValidas = ['mp4', 'ogv', 'webm'];

                try {
                    if (!in_array($extension, $extensionesValidas)) {
                        throw new FileException("El fichero que intentas subir al servidor no tiene un formato aceptado. Los formatos aceptados son: mp4, ogv, webm.");
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->renderForm('video/new.html.twig', [
                        'video' => $video,
                        'form' => $form,
                    ]);
                }

                //Comprobamos que el fichero a subir no nos haga exceder por encima del limite de nuestro tier
                $tamanioFichero = $form->get('videoFile')->getData()->getSize();
                $tamanioResultante = $this->getUser()->getEspacioUtilizado() + $tamanioFichero;

                try {
                    if ($tamanioResultante > $this->getUser()->getTier()->getAlmacenamiento()) {
                        throw new \Exception('No se ha podido subir el archivo ya que exceder??a tu cuota o plan actual.');
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->renderForm('video/new.html.twig', [
                        'video' => $video,
                        'form' => $form,
                    ]);
                }

                $video->getRecurso()->setExtension($extension);

                //Comprobamos si se ha dejado el campo nombre en blanco:
                if ($form->get('recurso')->get('nombre')->getData() == "") {
                    $video->getRecurso()->setNombre(pathinfo($nombreFichero, PATHINFO_FILENAME));
                } else {
                    $video->getRecurso()->setNombre(($form->get('nombre')->getData()));
                }

                $videoRepository->add($video, true);

                //Agregamos el tama??o del video al total de bytes usados por el usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $video->getTamanio());
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', '??Se ha subido el v??deo "' . $video->getRecurso()->getNombre() . '" con ??xito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de subir el v??deo...');
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
        //Si el usuario no es admin y no es propietario le denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if ($this->getUser() != $video->getRecurso()->getPropietario()) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
            }
        }

        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/guest/{id}', name: 'app_video_show_guest', methods: ['GET'])]
    public function showInvitado(Video $video): Response
    {
        //Si el usuario no es admin y no tiene acceso denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if (!$video->getRecurso()->getUsuarios()->contains($this->getUser())) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso.');
            }
        }

        return $this->render('video/showInvitado.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, VideoRepository $videoRepository): Response
    {

        //Denegamos el acceso si el usuario que entra no es el propietario o admin
        if ($this->getUser() != $video->getRecurso()->getPropietario() && !$this->getUser()->isAdministrador()) {
            throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
        }

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $video->getRecurso()->addFavorito($this->getUser());
                } else {
                    $video->getRecurso()->removeFavorito($this->getUser());
                }
                $videoRepository->add($video, true);
                $this->addFlash('exito', '??Se ha modificado el v??deo "' . $video->getRecurso()->getNombre() . '" con ??xito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de modificar el v??deo...');
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
                //Recuperamos el tama??o del fichero a eliminar
                $tamanio = $video->getTamanio();

                //Quitamos el espacio utilizado de la cuenta del usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $tamanio);

                //Guardamos los cambios del usaurio
                $usuarioRepository->add($usuario, true);

                //Eliminamos el fichero
                $videoRepository->remove($video, true);

            } catch (\Exception $e) {
                $this->addFlash('exito', 'Se ha eliminado el video correctamente');
            }
        }

        return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
    }
}
