<?php

namespace App\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Student;

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

    /**
     * @Route("/login/student", name="login_student")
     */
    public function login(Request $request, SessionInterface $session) {
        if($session->has("student_id")){
            return new Response(json_encode(['success'=>false, 'errMsg'=>"You have already logged in"]));
        }

        $email = $request -> request ->get('email');
        $password = $request -> request ->get('password');

        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['email'=>$email]);

        if($student == null) {
            return new Response(json_encode(['success'=>'false', 'errMsg'=>'Wrong email']));
        }
        else if($password != $student->getPassword()) {
            return new Response(json_encode(['success' => 'false', 'errMsg' => "Wrong password"]));
        }

        $session->set("student_id", $student->getId());
        $session->set("student_name", $student->getName());
        $session->set("student_gender", $student->getGender());
        $session->set("student_photo", $student->getPhoto());

        return new Response(json_encode(['success'=>true, 'name'=>$student->getName()]));
    }

    /**
     * @Route("/student/getcompany", name="get_company")
     */
    public function getCompany(Request $request, SessionInterface $session) {
        // TODO
        $response = new Response();
        $companies = array();
        for($i = 0; $i < 10; $i++) {
            $curComp = array();
            $curComp["a"] = 'b';
            array_push($companies, $curComp);
        }
        $response->setContent(json_encode([]));
    }

    /**
     * @Route("/student/logout", name="student_logout")
     */
    public function studentLogOut(Request $request, SessionInterface $session) {
        if(!$session->has("student_id")){
            $session->clear();
            return new Response(json_encode(['success'=>false, 'errMsg'=>"You have not logged in"]));
        }
        $session->clear();
        return new Response(json_encode(['success'=>true]));
    }

    /**
     * @Route("/login/company", name="login_company")
     */
    public function companyLogin(Request $request, SessionInterface $session){
        if($session->has("company_id")){
            return new Response(json_encode(['success'=>false, 'errMsg'=>"You have already logged in"]));
        }

        $email = $request -> request ->get('email');
        $password = $request -> request ->get('password');

        $company = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['email'=>$email]);

        if($company == null) {
            return new Response(json_encode(['success'=>'false', 'errMsg'=>'Wrong email']));
        }
        else if($password != $company->getPassword()) {
            return new Response(json_encode(['success' => 'false', 'errMsg' => "Wrong password"]));
        }

        $session->set("company_id", $company->getId());
        $session->set("company_name", $company->getName());

        return new Response(json_encode(['success'=>true, 'name'=>$company->getName()]));
    }

    /**
     * @Route("/company/logout", name="company_logout")
     */
    public function companyLogOut(Request $request, SessionInterface $session) {
        if(!$session->has("company_id")){
            $session->clear();
            return new Response(json_encode(['success'=>false, 'errMsg'=>"You have not logged in"]));
        }
        $session->clear();
        return new Response(json_encode(['success'=>true]));
    }





}
