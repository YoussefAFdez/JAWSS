<?php

namespace App\Controller;

use App\Entity\Audio;
use App\Form\AudioType;
use App\Repository\AudioRepository;
use App\Repository\RecursoRepository;
use App\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/audio')]
#[Security("is_granted('ROLE_USER')")]
class AudioController extends AbstractController
{
    #[Route('/', name: 'app_audio_index', methods: ['GET'])]
    public function index(AudioRepository $audioRepository, RecursoRepository $recursoRepository): Response
    {
        //Recogemos los recursos que pertenecen al usuario y los agregamos en un array
        $recursosUsuario = $recursoRepository->findByUsuario($this->getUser());
        $audios = [];
        foreach ($recursosUsuario as $recurso) {
            $audio = $audioRepository->findByRecurso($recurso);
            if (!empty($audio)) $audios[] = $audio;
        }

        //Recogemos los recursos compartidos con el usuario y los agregamos en un array
        $recursosAccesibles = $this->getUser()->getRecursosAccesibles();
        $audiosCompartidos = [];
        foreach ($recursosAccesibles as $recurso) {
            $audioCompartido = $audioRepository->findByRecurso($recurso);
            if (!empty($audioCompartido)) $audiosCompartidos[] = $audioCompartido;
        }

        return $this->render('audio/index.html.twig', [
            'audios' => $audios,
            'audiosCompartidos' => $audiosCompartidos,
        ]);
    }

    #[Route('/new', name: 'app_audio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AudioRepository $audioRepository, UsuarioRepository $usuarioRepository): Response
    {
        $audio = new Audio();
        $form = $this->createForm(AudioType::class, $audio, [
            'nuevo' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $audio->getRecurso()->setFichero(false);
                $audio->getRecurso()->setPropietario($this->getUser());
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $audio->getRecurso()->addFavorito($this->getUser());
                } else {
                    $audio->getRecurso()->removeFavorito($this->getUser());
                }

                $nombreFichero = $form->get('audioFile')->getData()->getClientOriginalName();
                $extension = pathinfo($nombreFichero, PATHINFO_EXTENSION);

                $extensionesValidas = ['wav', 'mp3', 'flac'];

                try {
                    if (!in_array($extension, $extensionesValidas)) {
                        throw new FileException("El fichero que intentas subir al servidor no tiene un formato aceptado. Los formatos aceptados son: wav, mp3, flac.");
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->renderForm('audio/new.html.twig', [
                        'audio' => $audio,
                        'form' => $form,
                    ]);
                }

                //Comprobamos que el fichero a subir no nos haga exceder por encima del limite de nuestro tier
                $tamanioFichero = $form->get('audioFile')->getData()->getSize();
                $tamanioResultante = $this->getUser()->getEspacioUtilizado() + $tamanioFichero;

                try {
                    if ($tamanioResultante > $this->getUser()->getTier()->getAlmacenamiento()) {
                        throw new \Exception('No se ha podido subir el archivo ya que exceder??a tu cuota o plan actual.');
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->renderForm('audio/new.html.twig', [
                        'audio' => $audio,
                        'form' => $form,
                    ]);
                }

                $audio->getRecurso()->setExtension($extension);

                //Comprobamos si se ha dejado el campo nombre en blanco:
                if ($form->get('recurso')->get('nombre')->getData() == "") {
                    $audio->getRecurso()->setNombre(pathinfo($nombreFichero, PATHINFO_FILENAME));
                } else {
                    $audio->getRecurso()->setNombre(($form->get('nombre')->getData()));
                }

                $audioRepository->add($audio, true);

                //Agregamos el tama??o de la nueva imagen al total de bytes usados por el usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $audio->getTamanio());
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', '??Se ha subido el audio "' . $audio->getRecurso()->getNombre() . '" con ??xito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de subir el audio...');

            }

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
        //Si el usuario no es admin y no es propietario le denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if ($this->getUser() != $audio->getRecurso()->getPropietario()) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
            }
        }

        return $this->render('audio/show.html.twig', [
            'audio' => $audio,
        ]);
    }

    #[Route('/guest/{id}', name: 'app_audio_show_guest', methods: ['GET'])]
    public function showInvitado(Audio $audio): Response
    {
        //Si el usuario no es admin y no tiene acceso denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if (!$audio->getRecurso()->getUsuarios()->contains($this->getUser())) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso.');
            }
        }

        return $this->render('audio/showInvitado.html.twig', [
            'audio' => $audio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_audio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Audio $audio, AudioRepository $audioRepository): Response
    {
        //Denegamos el acceso si el usuario que entra no es el propietario o admin
        if ($this->getUser() != $audio->getRecurso()->getPropietario() && !$this->getUser()->isAdministrador()) {
            throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
        }

        $form = $this->createForm(AudioType::class, $audio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $audio->getRecurso()->addFavorito($this->getUser());
                } else {
                    $audio->getRecurso()->removeFavorito($this->getUser());
                }

                $audioRepository->add($audio, true);
                $this->addFlash('exito', '??Se ha modificado el audio "' . $audio->getRecurso()->getNombre() . '" con ??xito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de modificar el audio...');

            }

            return $this->redirectToRoute('app_audio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('audio/edit.html.twig', [
            'audio' => $audio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_audio_delete', methods: ['POST'])]
    public function delete(Request $request, Audio $audio, AudioRepository $audioRepository, UsuarioRepository $usuarioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$audio->getId(), $request->request->get('_token'))) {
            try {
                //Recuperamos el tama??o del fichero a eliminar
                $tamanio = $audio->getTamanio();

                //Quitamos el espacio utilizado de la cuenta del usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $tamanio);

                //Guardamos los cambios del usaurio
                $usuarioRepository->add($usuario, true);

                //Eliminamos el fichero
                $audioRepository->remove($audio, true);

            } catch (\Exception $e) {
                $this->addFlash('exito', 'Se ha eliminado la cancion correctamente');
            }
        }

        return $this->redirectToRoute('app_audio_index', [], Response::HTTP_SEE_OTHER);
    }
}
