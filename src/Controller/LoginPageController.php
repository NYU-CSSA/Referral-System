<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginPageController extends AbstractController
{
    /**
     * @Route("/", name="login_page")
     */
    public function index()
    {
        return $this->render('login_page/index.html.twig', [
            'controller_name' => 'LoginPageController',
        ]);
    }
}
