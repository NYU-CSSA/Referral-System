<?php

namespace App\Controller;

use App\Entity\Company;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Student;
use App\Utils\Constant;

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
    public function login(Request $request, SessionInterface $session)
    {
        if ($session->has(Constant::$SES_KEY_STU_ID) or $session->has(Constant::$SES_KEY_COMP_ID)) {
            return Utils::makeErrMsgResponse("You have already logged in");
        }
        $session->clear();

        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['email' => $email]);

        if ($student == null) {
            return Utils::makeErrMsgResponse("Wrong email");
        } else if ($password != $student->getPassword()) {
            return Utils::makeErrMsgResponse("Wrong password");
        }
        $session->set(Constant::$SES_KEY_STU_ID, $student->getId());
        $session->set(Constant::$SES_KEY_STU_NAME, $student->getName());
        $session->set(Constant::$SES_KEY_COMP_EMAIL, $student->getEmail());

        return new Response(json_encode(['success' => true, 'name' => $student->getName()]));
    }

    /**
     * @Route("/student/logout", name="student_logout")
     */
    public function studentLogOut(Request $request, SessionInterface $session)
    {
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            $session->clear();
            return Utils::makeErrMsgResponse("You have not logged in");
        }
        $session->clear();
        return new Response(json_encode(['success' => true]));
    }

    /**
     * @Route("/login/company", name="login_company")
     */
    public function companyLogin(Request $request, SessionInterface $session)
    {
        if ($session->has(Constant::$SES_KEY_STU_ID) or $session->has(Constant::$SES_KEY_COMP_ID)) {
            return Utils::makeErrMsgResponse("You have already logged in");
        }
        $session->clear();

        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $company = $this->getDoctrine()
            ->getRepository(Company::class)
            ->findOneBy(['email' => $email]);

        if ($company == null) {
            return Utils::makeErrMsgResponse("Wrong email");
        } else if ($password != $company->getPassword()) {
            return Utils::makeErrMsgResponse("Wrong password");
        }

        $session->set(Constant::$SES_KEY_COMP_ID, $company->getId());
        $session->set(Constant::$SES_KEY_COMP_EMAIL, $company->getEmail());

        return new Response(json_encode(['success' => true, 'name' => $company->getName()]));
    }

    /**
     * @Route("/company/logout", name="company_logout")
     */
    public function companyLogOut(Request $request, SessionInterface $session)
    {
        if (!$session->has(Constant::$SES_KEY_COMP_ID)) {
            $session->clear();
            return Utils::makeErrMsgResponse("You have not logged in");
        }
        $session->clear();
        return new Response(json_encode(['success' => true]));
    }


}
