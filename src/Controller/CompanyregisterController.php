<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CompanyregisterController extends AbstractController
{
    /**
     * @Route("/companyregister", name="companyregister")
     */
    public function index()
    {
        return $this->render('companyregister/index.html.twig', [
            'controller_name' => 'CompanyregisterController',
        ]);
    }
}
