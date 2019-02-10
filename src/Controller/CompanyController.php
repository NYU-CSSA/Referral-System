<?php

namespace App\Controller;

use App\Utils\Constant;
use App\Utils\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Student;

class CompanyController extends AbstractController
{
    /**
     * @Route("/company/getstudents")
     */
    public function getStudents(Request $request, SessionInterface $session): Response
    {
        if(!$session->has(Constant::$SES_KEY_COMP_ID)){
            return ErrorResponse::UnLoggedErrorResponse();
        }

        /** @var Student[] $students */
        $students = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findAll();
        $list = [];
        foreach ($students as $s) {
            $list[] = [
                'name' => $s->getName(),
                'gender' => $s->getGender(),
                'intro' => $s->getIntro(),
                'photo' => $s->getPhoto(),
                'email' => $s->getEmail(),
            ];
        }
        return new Response(json_encode($list));
    }
}
