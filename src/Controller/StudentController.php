<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Student;
use App\Utils\Constant;
use App\Utils\ErrorResponse;
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
    public function getCompany(Request $request, SessionInterface $session)
    {
        if (!$request->isMethod("GET")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }

        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        /** @var Company[] $companies */
        $companies = $this->getDoctrine()
            ->getRepository(Company::class)
            ->findAll();

        $return_data = array();
        foreach ($companies as $company) {
            $return_data[] = [
                'name' => $company->getName(),
                'description' => $company->getDescription(),
            ];
        }

        return new Response(json_encode([
            "success" => true,
            "company" => $return_data,
        ]));
    }

    /**
     * @Route("/student/getprofile", name="student_get_profile")
     */
    public function getProfile(Request $request, SessionInterface $session)
    {
        if (!$request->isMethod("GET")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }

        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        /** @var Student $me */
        $me = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_STU_ID)]);

        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        return new Response(json_encode([
            'success' => true,
            'profile' => [
                'name' => $me->getName(),
                'email' => $me->getEmail(),
                'photo' => $me->getPhoto(),
                'intro' => $me->getIntro(),
                'gender' => $me->getGender(),
            ],
        ]));
    }

    /**
     * @Route("/student/editprofile", name="student_edit_profile")
     */
    public function editProfile(Request $request, SessionInterface $session)
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        /** @var Student $me */
        $me = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_STU_ID)]);
        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        $missedFields = Utils::getMissingFields($request, ['name', 'gender', 'intro']);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        $me->setName($request->request->get("name"));
        $me->setGender($request->request->get("gender"));
        $me->setIntro($request->request->get("intro"));

        try {
            $dm = $this->getDoctrine()->getManager();
            $dm->persist($me);
            $dm->flush();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }

        return new Response(json_encode(['success' => true]));

    }

    /**
     * @Route("/student/resetpwd", name="student_reset_password")
     */
    public function resetPassword(Request $request, SessionInterface $session)
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        /** @var Student $me */
        $me = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_STU_ID)]);
        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        $missedFields = Utils::getMissingFields($request, ['oldPassword', 'newPassword']);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        if($me->getPassword() != $request->request->get("oldPassword")){
            return ErrorResponse::LoginErrorResponse();
        }

        $me->setPassword($request->request->get("newPassword"));

        try {
            $dm = $this->getDoctrine()->getManager();
            $dm->persist($me);
            $dm->flush();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }

        return new Response(json_encode(['success' => true]));
    }
}
