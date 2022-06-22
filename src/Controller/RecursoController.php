<?php

namespace App\Controller;

use App\Entity\Recurso;
use App\Form\RecursoType;
use App\Repository\RecursoRepository;
use App\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/recurso')]
#[Security("is_granted('ROLE_USER')")]
class RecursoController extends AbstractController
{
    #[Route('/', name: 'app_recurso_index', methods: ['GET'])]
    public function index(RecursoRepository $recursoRepository): Response
    {
        //Recogemos los recursos que pertenecen al usuario y son ficheros
        $recursos = $recursoRepository->findByFicherosAndUsuario($this->getUser());

        //Recogemos los recursos compartidos con el usuario y los agregamos en un array
        $recursosAccesibles = $this->getUser()->getRecursosAccesibles();
        $recursosCompartidos = [];
        foreach ($recursosAccesibles as $recurso) {
            if ($recurso->isFichero()) $recursosCompartidos[] = $recurso;
        }

        return $this->render('recurso/index.html.twig', [
            'recursos' => $recursos,
            'recursosCompartidos' => $recursosCompartidos,
        ]);
    }

    #[Route('/new', name: 'app_recurso_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RecursoRepository $recursoRepository, UsuarioRepository $usuarioRepository): Response
    {
        $recurso = new Recurso();
        $form = $this->createForm(RecursoType::class, $recurso, [
            'nuevo' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $recurso->setFichero(true);
                $recurso->setPropietario($this->getUser());
                if ($form->get('favorito')->getData()) {
                    $recurso->addFavorito($this->getUser());
                } else {
                    $recurso->removeFavorito($this->getUser());
                }
                //Recuperamos el nombre del fichero subido y su extension
                $nombreCompleto = $form->get('ficheroFile')->getData()->getClientOriginalName();
                $extension = pathinfo($nombreCompleto, PATHINFO_EXTENSION);

                //Comprobamos si se ha dejado el campo nombre en blanco:
                if ($form->get('nombre')->getData() == "") {
                    $recurso->setNombre(pathinfo($nombreCompleto, PATHINFO_FILENAME));
                } else {
                    $recurso->setNombre($form->get('nombre')->getData());
                }

                //Comprobamos que el fichero a subir no nos haga exceder por encima del limite de nuestro tier
                $tamanioFichero = $form->get('ficheroFile')->getData()->getSize();
                $tamanioResultante = $this->getUser()->getEspacioUtilizado() + $tamanioFichero;

                try {
                    if ($tamanioResultante > $this->getUser()->getTier()->getAlmacenamiento()) {
                        throw new \Exception('No se ha podido subir el archivo ya que excedería tu cuota o plan actual.');
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->renderForm('recurso/new.html.twig', [
                        'recurso' => $recurso,
                        'form' => $form,
                    ]);
                }

                $recurso->setExtension($extension);

                $recursoRepository->add($recurso, true);

                //Agregamos el tamaño de la nueva imagen al total de bytes usados por el usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $recurso->getTamanio());
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', '¡Se ha subido el recurso ' . $recurso->getNombre() . ' con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de subir el recurso...');
                $this->addFlash('error', $e->getMessage());
            }

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
        //Si el usuario no es admin y no es propietario le denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if ($this->getUser() != $recurso->getPropietario()) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
            }
        }

        return $this->render('recurso/show.html.twig', [
            'recurso' => $recurso,
        ]);
    }

    #[Route('/guest/{id}', name: 'app_recurso_show_guest', methods: ['GET'])]
    public function showInvitado(Recurso $recurso): Response
    {
        //Si el usuario no es admin y no tiene acceso denegamos el paso
        if (!$this->getUser()->isAdministrador()) {
            if (!$recurso->getUsuarios()->contains($this->getUser())) {
                throw $this->createAccessDeniedException('No tienes acceso a este recurso.');
            }
        }

        return $this->render('recurso/showInvitado.html.twig', [
            'recurso' => $recurso,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recurso_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recurso $recurso, RecursoRepository $recursoRepository): Response
    {
        //Denegamos el acceso si el usuario que entra no es el propietario o admin
        if ($this->getUser() != $recurso->getPropietario() && !$this->getUser()->isAdministrador()) {
            throw $this->createAccessDeniedException('No tienes acceso a este recurso porque no eres su propietario.');
        }

        $form = $this->createForm(RecursoType::class, $recurso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($form->get('recurso')->get('favorito')->getData()) {
                    $recurso->addFavorito($this->getUser());
                } else {
                    $recurso->removeFavorito($this->getUser());
                }

                $recursoRepository->add($recurso, true);
                $this->addFlash('exito', '¡Se ha modificado el recurso "' . $recurso->getNombre() . '" con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de modificar el recurso...');
            }

            return $this->redirectToRoute('app_recurso_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recurso/edit.html.twig', [
            'recurso' => $recurso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recurso_delete', methods: ['POST'])]
    public function delete(Request $request, Recurso $recurso, RecursoRepository $recursoRepository, UsuarioRepository $usuarioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recurso->getId(), $request->request->get('_token'))) {
            try {
                //Recuperamos el tamaño del fichero a eliminar
                $tamanio = $recurso->getTamanio();

                //Quitamos el espacio utilizado de la cuenta del usuario
                $usuario = $this->getUser();
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $tamanio);

                //Guardamos los cambios del usaurio
                $usuarioRepository->add($usuario, true);

                //Eliminamos el fichero
                $recursoRepository->remove($recurso, true);

            } catch (\Exception $e) {
                $this->addFlash('exito', 'Se ha eliminado el recurso correctamente');
            }
        }

        return $this->redirectToRoute('app_recurso_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/query/recursos', name: 'api_recurso_query', methods: ['GET'])]
    public function apiPersonQuery(
        Request $request,
        RecursoRepository $recursoRepository,
    ): Response
    {
        $busquedaQuery = $request->get('q');

        $recursos = $recursoRepository->findByBusqueda($busquedaQuery);

        $data = [];
        foreach ($recursos as $recurso) {
            $data[] = ['id' => $recurso->getId(), 'text' => $recurso->getNombre()];
        }

        return new JsonResponse($data);
    }
}
