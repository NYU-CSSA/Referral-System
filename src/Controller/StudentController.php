<?php

namespace App\Controller;

use App\Utils\Constant;
use App\Utils\Utils;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StudentController extends AbstractController
{
    /**
     * @Route("/student/getcompany", name="get_company")
     */
    public function getCompany(Request $request, SessionInterface $session) {
        if(!$request->isMethod("GET")){
            return Utils::makeErrMsgResponse("not a GET request");
        }

        if(!$session->has(Constant::$SES_KEY_STU_ID)){
            return Utils::makeErrMsgResponse("You have not logged in");
        }

        $response = new Response();
        $companies = array();
        for($i = 0; $i < 10; $i++) {
            $curComp = array();
            $curComp["a"] = 'b';
            array_push($companies, $curComp);
        }
        $response->setContent(json_encode([]));
    }
}
