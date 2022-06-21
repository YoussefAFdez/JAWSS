<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    #[Route('/query/usuarios', name: 'api_usuario_query', methods: ['GET'])]
    #[Security('is_granted("ROLE_USER")')]
    public function apiPersonQuery(
        Request $request,
        UsuarioRepository $usuarioRepository,
    ): Response
    {
        $busquedaQuery = $request->get('q');

        $usuarios = $usuarioRepository->findByBusqueda($busquedaQuery);

        $data = [];
        foreach ($usuarios as $usuario) {
            $data[] = ['id' => $usuario->getId(), 'text' => $usuario->getNombreUsuario()];
        }

        return new JsonResponse($data);
    }
}