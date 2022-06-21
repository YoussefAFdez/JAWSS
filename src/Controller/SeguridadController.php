<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use App\Form\UsuarioType;
use App\Repository\TierRepository;
use App\Repository\UsuarioRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SeguridadController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils) : Response {
        return $this->render('seguridad/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_user' => $authenticationUtils->getLastUsername()
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout() : Response {
        throw new AccessDeniedHttpException();
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TierRepository $tierRepository): Response
    {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            try {
                $user->setClave(
                    $userPasswordHasher->hashPassword(
                        $user, $form->get('clave')->get('first')->getData()
                    )
                );

                $user->setTier($tierRepository->findTierBase());
                $user->setAdministrador(false);
                $user->setEspacioUtilizado(0);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('exito', 'Se ha registrado su cuenta correctamente.');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('youssef.a.fdez@gmail.com', 'Yuu'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('exito', 'Se ha verificado tu cuenta de correo electrónico');

        return $this->redirectToRoute('index');
    }

    #[Route("/clave/cambio", name: "app_cambio_clave")]
    #[Security("is_granted('ROLE_USER')")]
    public function cambioClave(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $userPasswordHasher) : Response {
        $usuario = $this->getUser();
        $usuario = $usuarioRepository->findUsuario($usuario->getNombreUsuario());

        $form = $this->createForm(UsuarioType::class, $usuario, [
            'cambioDatos' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $usuario->setClave(
                    $userPasswordHasher->hashPassword(
                        $usuario, $form->get('clave')->get('first')->getData()
                    )
                );
                $usuarioRepository->add($usuario, true);

                $this->addFlash('exito', 'Se han modificado tus datos de usuario.');
                return $this->redirectToRoute('index');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Ha ocurrido un error a la hora de modificar tus datos.');
            }
        }

        return $this->render('usuario/selfEdit.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }
}
