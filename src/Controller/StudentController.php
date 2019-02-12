<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Student;
use App\Utils\Constant;
use App\Utils\ErrorResponse;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\Collection;

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
        $companies = null;
        if ($request->query->has('companyId')) {
            $companies = $this->getDoctrine()
                ->getRepository(Company::class)
                ->findBy(['id' => $request->query->get('companyId')]);
            if (sizeof($companies) == 0) {
                return ErrorResponse::DataNotFoundResponse();
            }
        } else {
            $companies = $this->getDoctrine()
                ->getRepository(Company::class)
                ->findAll();
        }

        $return_data = array();
        foreach ($companies as $company) {
            $return_data[] = [
                'companyId' => $company->getId(),
                'name' => $company->getName(),
                'description' => $company->getDescription(),
                'positions' => $company->getPositionsArray(),
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

        if ($me->getPassword() != $request->request->get("oldPassword")) {
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

    /**
     * @Route("/student/updatephoto", name="student_update_photo")
     */
    public function updatePhoto(Request $request, SessionInterface $session)
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

        /** @var UploadedFile $file */
        $file = $request->files->get("photo");
        if ($file == null) {
            return ErrorResponse::FieldMissingErrorResponse(['photo']);
        }

        $newFileName = Utils::generateUniqueFileName() . '.' . $file->guessExtension();
        $file->move("./" . Constant::$USER_PHOTO_PATH, $newFileName);
        $me->setPhoto("/" . Constant::$USER_PHOTO_PATH . '/' . $newFileName);

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
     * @Route("/student/searchcompany", name="student_search_company")
     */
    public function searchCompany(Request $request, SessionInterface $session)
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }

        $missedFields = Utils::getMissingFields($request, ['companyName']);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        /** @var Collection|Company[] $companies */
        $companies = $this->getDoctrine()
            ->getRepository(Company::class)
            ->findBy(['name' => $request->request->get('companyName')]);

        $return_data = array();
        foreach ($companies as $company) {
            $return_data[] = [
                'companyId' => $company->getId(),
                'name' => $company->getName(),
                'description' => $company->getDescription(),
                'positions' => $company->getPositionsArray(),
            ];
        }

        return new Response(json_encode([
            "success" => true,
            "company" => $return_data,
        ]));
    }
}
