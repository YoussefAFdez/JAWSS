<?php

namespace App\Controller;

use App\Entity\Imagen;
use App\Form\ImagenType;
use App\Repository\ImagenRepository;
use App\Repository\RecursoRepository;
use App\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/imagen')]
#[Security("is_granted('ROLE_USER')")]
class ImagenController extends AbstractController
{
    #[Route('/', name: 'app_imagen_index', methods: ['GET'])]
    public function index(ImagenRepository $imagenRepository, RecursoRepository $recursoRepository): Response
    {
        //Recogemos los recursos que pertenecen al usuario y los agregamos en un array
        $recursosUsuario = $recursoRepository->findByUsuario($this->getUser());
        $imagenes = [];
        foreach ($recursosUsuario as $recurso) {
            $imagen = $imagenRepository->findByRecurso($recurso);
            if (!empty($imagen)) $imagenes[] = $imagen;
        }

        //Recogemos los recursos compartidos con el usuario y los agregamos en un array
        $recursosAccesibles = $this->getUser()->getRecursosAccesibles();
        $imagenesCompartidas = [];
        foreach ($recursosAccesibles as $recurso) {
            $imagenenCompartida = $imagenRepository->findByRecurso($recurso);
            if (!empty($imagenCompartida)) $imagenesCompartidas[] = $imagenenCompartida;
        }

        return $this->render('imagen/index.html.twig', [
            'imagenes' => $imagenes,
            'imagenesCompartidas' => $imagenesCompartidas,
        ]);
    }

    #[Route('/new', name: 'app_imagen_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ImagenRepository $imagenRepository, UsuarioRepository $usuarioRepository): Response
    {
        $imagen = new Imagen();
        $form = $this->createForm(ImagenType::class, $imagen, [
            'nuevo' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $imagen->getRecurso()->setFichero(false);
                $imagen->getRecurso()->setPropietario($this->getUser());
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $imagen->getRecurso()->addFavorito($this->getUser());
                } else {
                    $imagen->getRecurso()->removeFavorito($this->getUser());
                }

                $imagenRepository->add($imagen, true);

                //Agregamos el tamaño de la nueva imagen al total de bytes usados por el usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $imagen->getTamanio());
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', '¡Se ha subido la imagen ' . $imagen->getRecurso()->getNombre() . ' con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de subir la imagen...');
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('imagen/new.html.twig', [
            'imagen' => $imagen,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_imagen_show', methods: ['GET'])]
    public function show(Imagen $imagen): Response
    {
        //Si el usuario no es admin y no es propietario le denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if ($this->getUser() != $imagen->getRecurso()->getPropietario()) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
            }
        }

        return $this->render('imagen/show.html.twig', [
            'imagen' => $imagen,
        ]);
    }

    #[Route('/guest/{id}', name: 'app_imagen_show_guest', methods: ['GET'])]
    public function showInvitado(Imagen $imagen): Response
    {
        //Si el usuario no es admin y no tiene acceso denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if (!$imagen->getRecurso()->getUsuarios()->contains($this->getUser())) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso.');
            }
        }

        return $this->render('imagen/showInvitado.html.twig', [
            'imagen' => $imagen,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_imagen_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Imagen $imagen, ImagenRepository $imagenRepository): Response
    {
        //Denegamos el acceso si el usuario que entra no es el propietario o admin
        if ($this->getUser() != $imagen->getRecurso()->getPropietario() && !$this->getUser()->isAdministrador()) {
            throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
        }

        $form = $this->createForm(ImagenType::class, $imagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $imagen->getRecurso()->addFavorito($this->getUser());
                } else {
                    $imagen->getRecurso()->removeFavorito($this->getUser());
                }
                $imagenRepository->add($imagen, true);
                $this->addFlash('exito', '¡Se ha modificado la imagen "' . $imagen->getRecurso()->getNombre() . '" con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de modificar la imagen...');
            }

            return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('imagen/edit.html.twig', [
            'imagen' => $imagen,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_imagen_delete', methods: ['POST'])]
    public function delete(Request $request, Imagen $imagen, ImagenRepository $imagenRepository, UsuarioRepository $usuarioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imagen->getId(), $request->request->get('_token'))) {
            try {
                //Recuperamos el tamaño del fichero a eliminar
                $tamanio = $imagen->getTamanio();

                //Quitamos el espacio utilizado de la cuenta del usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $tamanio);

                //Guardamos los cambios del usaurio
                $usuarioRepository->add($usuario, true);

                //Eliminamos el fichero
                $imagenRepository->remove($imagen, true);

            } catch (\Exception $e) {
                $this->addFlash('exito', 'Se ha eliminado la imagen correctamente');
            }
        }

        return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
    }
}
