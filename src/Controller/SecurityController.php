<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\RegisterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $errorMessage = null;
        if ($error) {
            if ($error instanceof LockedException) {
                $errorMessage = 'Your account has been locked. Please contact support.';
            } elseif ($error instanceof DisabledException) {
                $errorMessage = 'Your account has been disabled. Please contact support.';
            } else {
                $errorMessage = 'Invalid email or password.';
            }
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $errorMessage]);
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, RegisterService $registerService): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $registerService->register($user);
//            return $this->render('security/register.html.twig', [
//                'isEmailConfirmationPending' => true
//            ]);
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route(path: '/login-check', name: 'app_loginCheck')]
    public function checkLogin(Request $request, RegisterService $registerService, EntityManagerInterface $em): Response | null
    {
        $user = $registerService->checkUserToken($request->get('token'));

        if(!$user) {
            throw new \Exception('Invalid token');
        }
        $user->setRoles(['IS_FULLY_AUTHENTICATED', ...$user->getRoles()]);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('app_login');
    }


}
