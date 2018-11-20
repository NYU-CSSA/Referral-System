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
     * @Route('/referal-system/public/Login')
     */
    public function login(Request $request){

        if($request -> request -> has("submitEnterprise")){
            echo("TURE");
            $this->studentLogin($request);
        }
        else{
            $this->enterpriseLogin($request);
        }
        return $this-> render('index.html.twig');


    }

    //need to look up in the student table
    public function studentLogin(Request $request){
        $password = $request -> request ->get('password');
        $email = $request -> request ->get('email');

        $correctpw = "adsdaasd";
        $correct = FALSE;
        $error = "Unsuccessful login.";
        if(!$correct){
            return new Response('<html><body>hello world</body></html>')   ;
        }
        else {
            return $this->render("placeholder");
        }
    }

    //need to look up in the enterprise table
    public function enterpriseLogin(Request $request){
        $password = $request -> request ->get('password');
        $email = $request -> request ->get('email');

        $correct = FALSE;
        $correctpw = "adsdaasd";
        $error = "Unsuccessful login.";

        if(!$correct){
            return $this -> render("helloworld");
        }
        else {
            return $this->render("placeholder");
        }
    }


}
