<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param AuthenticationUtils $utils
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {

            if ($this->getUser())
            {
                return $this->redirectToRoute('ticket_index');
            }
            $error = $utils->getLastAuthenticationError();
            $lastUsername = $utils->getLastUsername();
            return $this->render('security/login.html.twig', [
                'error' => $error,
                'last_username' => $lastUsername
            ]);


    }
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function postLogin()
    {
        //$tokenInterface = $this->get('security.token_storage')->getToken();
        //$isAuthenticated = $tokenInterface->isAuthenticated();


            dump($this->getUser());
            return $this->render('dashboard/index.html.twig', [
                'controller_name' => 'DashboardController',
            ]);

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }
}
