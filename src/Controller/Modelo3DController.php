<?php

namespace App\Controller;

use App\Entity\Modelo3D;
use App\Form\Modelo3DType;
use App\Repository\Modelo3DRepository;
use App\Repository\RecursoRepository;
use App\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/modelo3d')]
#[Security("is_granted('ROLE_USER')")]
class Modelo3DController extends AbstractController
{
    #[Route('/', name: 'app_modelo3d_index', methods: ['GET'])]
    public function index(Modelo3DRepository $modelo3DRepository, RecursoRepository $recursoRepository): Response
    {
        //Recogemos los recursos que pertenecen al usuario y los agregamos en un array
        $recursosUsuario = $recursoRepository->findByUsuario($this->getUser());
        $modelos3d = [];
        foreach ($recursosUsuario as $recurso) {
            $modelo3d = $modelo3DRepository->findByRecurso($recurso);
            if (!empty($modelo3d)) $modelos3d[] = $modelo3d;
        }

        //Recogemos los recursos compartidos con el usuario y los agregamos en un array
        $recursosAccesibles = $this->getUser()->getRecursosAccesibles();
        $modelos3dCompartidos = [];
        foreach ($recursosAccesibles as $recurso) {
            $modelo3dCompartido = $modelo3DRepository->findByRecurso($recurso);
            if (!empty($modelo3dCompartido)) $modelos3dCompartidos[] = $modelo3dCompartido;
        }

        return $this->render('modelo3d/index.html.twig', [
            'modelos3d' => $modelos3d,
            'modelos3dCompartidos' => $modelos3dCompartidos,
        ]);
    }

    #[Route('/new', name: 'app_modelo3d_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Modelo3DRepository $modelo3DRepository, UsuarioRepository $usuarioRepository): Response
    {
        $modelo3D = new Modelo3D();
        $form = $this->createForm(Modelo3DType::class, $modelo3D, [
            'nuevo' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $modelo3D->getRecurso()->setFichero(false);
                $modelo3D->getRecurso()->setPropietario($this->getUser());
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $modelo3D->getRecurso()->addFavorito($this->getUser());
                } else {
                    $modelo3D->getRecurso()->removeFavorito($this->getUser());
                }

                $nombreFichero = $form->get('modeloFile')->getData()->getClientOriginalName();
                $extension = pathinfo($nombreFichero, PATHINFO_EXTENSION);

                $extensionesValidas = ['glb', 'fbx', 'stl'];

                try {
                    if (!in_array($extension, $extensionesValidas)) {
                        throw new FileException("El fichero que intentas subir al servidor no tiene un formato aceptado. Los formatos aceptados son: glb, fbx, stl.");
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->renderForm('modelo3d/new.html.twig', [
                        'modelo3d' => $modelo3D,
                        'form' => $form,
                    ]);
                }

                //Comprobamos que el fichero a subir no nos haga exceder por encima del limite de nuestro tier
                $tamanioFichero = $form->get('modeloFile')->getData()->getSize();
                $tamanioResultante = $this->getUser()->getEspacioUtilizado() + $tamanioFichero;

                try {
                    if ($tamanioResultante > $this->getUser()->getTier()->getAlmacenamiento()) {
                        throw new \Exception('No se ha podido subir el archivo ya que excedería tu cuota o plan actual.');
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->renderForm('modelo3d/new.html.twig', [
                        'modelo3d' => $modelo3D,
                        'form' => $form,
                    ]);
                }

                $modelo3D->getRecurso()->setExtension($extension);

                //Comprobamos si se ha dejado el campo nombre en blanco:
                if ($form->get('recurso')->get('nombre')->getData() == "") {
                    $modelo3D->getRecurso()->setNombre(pathinfo($nombreFichero, PATHINFO_FILENAME));
                } else {
                    $modelo3D->getRecurso()->setNombre(($form->get('nombre')->getData()));
                }
                
                $modelo3DRepository->add($modelo3D, true);

                //Agregamos el tamaño de la nueva imagen al total de bytes usados por el usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $modelo3D->getTamanio());
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', '¡Se ha subido el objeto "' . $modelo3D->getRecurso()->getNombre() . '" con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de subir el objeto...');
            }

            return $this->redirectToRoute('app_modelo3d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('modelo3d/new.html.twig', [
            'modelo3d' => $modelo3D,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_modelo3d_show', methods: ['GET'])]
    public function show(Modelo3D $modelo3D): Response
    {
        //Si el usuario no es admin y no es propietario le denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if ($this->getUser() != $modelo3D->getRecurso()->getPropietario()) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
            }
        }

        return $this->render('modelo3d/show.html.twig', [
            'modelo3d' => $modelo3D,
        ]);
    }

    #[Route('/guest/{id}', name: 'app_modelo3d_show_guest', methods: ['GET'])]
    public function showInvitado(Modelo3D $modelo3D): Response
    {
        //Si el usuario no es admin y no tiene acceso denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if (!$modelo3D->getRecurso()->getUsuarios()->contains($this->getUser())) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso.');
            }
        }

        return $this->render('modelo3d/showInvitado.html.twig', [
            'modelo3d' => $modelo3D,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_modelo3d_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Modelo3D $modelo3D, Modelo3DRepository $modelo3DRepository): Response
    {
        //Denegamos el acceso si el usuario que entra no es el propietario o admin
        if ($this->getUser() != $modelo3D->getRecurso()->getPropietario() && !$this->getUser()->isAdministrador()) {
            throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
        }

        $form = $this->createForm(Modelo3DType::class, $modelo3D);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $modelo3D->getRecurso()->addFavorito($this->getUser());
                } else {
                    $modelo3D->getRecurso()->removeFavorito($this->getUser());
                }

                $modelo3DRepository->add($modelo3D, true);
                $this->addFlash('exito', '¡Se ha modificado el objeto "' . $modelo3D->getRecurso()->getNombre() . '" con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de modificar el objeto...');
            }

            return $this->redirectToRoute('app_modelo3d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('modelo3d/edit.html.twig', [
            'modelo3d' => $modelo3D,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_modelo3d_delete', methods: ['POST'])]
    public function delete(Request $request, Modelo3D $modelo3D, Modelo3DRepository $modelo3DRepository, UsuarioRepository $usuarioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$modelo3D->getId(), $request->request->get('_token'))) {
            try {
                //Recuperamos el tamaño del fichero a eliminar
                $tamanio = $modelo3D->getTamanio();

                //Quitamos el espacio utilizado de la cuenta del usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $tamanio);

                //Guardamos los cambios del usaurio
                $usuarioRepository->add($usuario, true);

                //Eliminamos la imagen
                $modelo3DRepository->remove($modelo3D, true);

            } catch (\Exception $e) {
                $this->addFlash('exito', 'Se ha eliminado el objeto correctamente');
            }
        }

        return $this->redirectToRoute('app_modelo3d_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/modelo/prueba', name: 'app_prueba3d')]
    Public function prueba3D(): Response
    {
        return $this->render('modelo3d/prueba.html.twig');
    }
}
