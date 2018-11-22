<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/referal-system/forms/login")
     */
    public function login(Request $request) {
        $formName = $request -> request -> get('formName');

        if ($formName == "studentLogin") {
            return $this->studentLogin($request);
        }
        else if ($formName == "enterpriseLogin") {
            return $this->enterpriseLogin($request);
        } else {
            return new Response("Undefined form type: $formName");
        }
    }

    private function studentLogin(Request $request){
        $password = $request -> request ->get('password');
        $email = $request -> request ->get('email');

        $correctpw = "passwd"; // TODO: fetch data from database
        $correct = $password == $correctpw;

        $error = "Unsuccessful login.";
        if($correct){
            return new Response("<html><body>Success! Welcome, $email</body></html>")   ;
        }
        else {
            return new Response($error);
        }
    }

    private function enterpriseLogin(Request $request){
        $password = $request -> request ->get('password');
        $email = $request -> request ->get('email');

        $correctpw = "passwd"; // TODO: fetch data from database
        $correct = $password == $correctpw;

        $error = "Unsuccessful login.";

        if($correct){
            return new Response("Success! Welcome, $email");
        }
        else {
            return new Response($error);
        }
    }


}
