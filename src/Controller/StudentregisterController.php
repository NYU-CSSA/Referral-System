<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StudentregisterController extends AbstractController
{
    /**
     * @Route("/studentregister", name="studentregister")
     */
    public function index()
    {
        return $this->render('studentregister/index.html.twig', [
            'controller_name' => 'StudentregisterController',
        ]);
    }
}
