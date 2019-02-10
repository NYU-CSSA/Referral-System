<?php

namespace App\Controller;

use App\Entity\Company;
use App\Utils\Constant;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        /** @var Company[] $companies */
        $companies = $this->getDoctrine()
            ->getRepository(Company::class)
            ->findAll();

        $return_data = array();
        foreach ($companies as $company){
            $return_data[] = [
                'name'=>$company->getName(),
                'description'=>$company->getDescription(),
                ];
        }
        return new Response(json_encode([
            "success"=>true,
            "company"=>$return_data,
        ]));
    }
}
