<?php

namespace App\Controller;

use App\Entity\Imagen;
use App\Form\ImagenType;
use App\Repository\ImagenRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/imagen')]
#[Security("is_granted('ROLE_USER')")]
class ImagenController extends AbstractController
{
    #[Route('/', name: 'app_imagen_index', methods: ['GET'])]
    public function index(ImagenRepository $imagenRepository): Response
    {
        return $this->render('imagen/index.html.twig', [
            'imagenes' => $imagenRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_imagen_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ImagenRepository $imagenRepository): Response
    {
        $imagen = new Imagen();
        $form = $this->createForm(ImagenType::class, $imagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $imagen->getRecurso()->setFichero(false);
                $imagenRepository->add($imagen, true);

                //Agregamos el tamaño de la nueva imagen al total de bytes usados por el usuario
                $propietario = $imagen->getRecurso()->getPropietario();
                $propietario->setEspacioUtilizado($propietario->getEspacioUtilizado() + $imagen->getTamanio());

                $this->addFlash('exito', '¡Se ha subido la imagen ' . $imagen->getRecurso()->getNombre() . ' con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de subir la imagen...');
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
        return $this->render('imagen/show.html.twig', [
            'imagen' => $imagen,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_imagen_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Imagen $imagen, ImagenRepository $imagenRepository): Response
    {
        //Recogemos el tamaño previo a la modificación
        $tamanioPrevio = $imagen->getTamanio();

        $form = $this->createForm(ImagenType::class, $imagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $imagenRepository->add($imagen, true);

                //Actualizamos el espacio utilizado por el propietario
                $propietario = $imagen->getRecurso()->getPropietario();
                $propietario->setEspacioUtilizado($propietario->getEspacioUtilizado() - $tamanioPrevio + $imagen->getTamanio());

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
    public function delete(Request $request, Imagen $imagen, ImagenRepository $imagenRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imagen->getId(), $request->request->get('_token'))) {
            try {
                $imagenRepository->remove($imagen, true);

            } catch (\Exception $e) {

            }
        }

        return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
    }
}
