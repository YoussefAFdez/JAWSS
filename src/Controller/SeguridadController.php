<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SeguridadController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils) : Response {
        return $this->render('seguridad/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_user' => $authenticationUtils->getLastUsername()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() : Response {
        throw new AccessDeniedHttpException();
    }
}
