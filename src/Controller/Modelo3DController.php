<?php

namespace App\Controller;

use App\Entity\Modelo3D;
use App\Form\Modelo3DType;
use App\Repository\Modelo3DRepository;
use App\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/modelo3d')]
#[Security("is_granted('ROLE_USER')")]
class Modelo3DController extends AbstractController
{
    #[Route('/', name: 'app_modelo3d_index', methods: ['GET'])]
    public function index(Modelo3DRepository $modelo3DRepository): Response
    {
        return $this->render('modelo3d/index.html.twig', [
            'modelos3d' => $modelo3DRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_modelo3d_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Modelo3DRepository $modelo3DRepository, UsuarioRepository $usuarioRepository): Response
    {
        $modelo3D = new Modelo3D();
        $form = $this->createForm(Modelo3DType::class, $modelo3D);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $modelo3D->getRecurso()->setFichero(false);
                $modelo3D->getRecurso()->setPropietario($this->getUser());
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
        return $this->render('modelo3d/show.html.twig', [
            'modelo3d' => $modelo3D,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_modelo3d_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Modelo3D $modelo3D, Modelo3DRepository $modelo3DRepository): Response
    {
        $form = $this->createForm(Modelo3DType::class, $modelo3D);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
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

                //Eliminamos la imagen
                $modelo3DRepository->remove($modelo3D, true);

                //Quitamos el espacio utilizado de la cuenta del usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $tamanio);

                //Guardamos los cambios del usaurio
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', 'Se ha eliminado el objeto correctamente');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de eliminar el objeto');
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
