<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Company;
use App\Entity\Position;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\ErrorResponse;
use App\Utils\Utils;
use App\Utils\Constant;
use App\Entity\Resume;
use App\Entity\Student;
use Symfony\Component\Validator\Constraints\Collection;

class ApplicationController extends AbstractController
{
    /**
     * @Route("/student/sendresume", name="sendresume")
     */
    public function sendResume(Request $request, SessionInterface $session): Response
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

        $missedFields = Utils::getMissingFields($request, ['resumeId', 'companyId', 'positionId', 'notes']);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        /** @var Resume $resume */
        $resume = $this->getDoctrine()
            ->getRepository(Resume::class)
            ->findOneBy(['id' => $request->request->get('resumeId')]);
        if (null === $resume || $resume->getStudent() !== $me) {
            return ErrorResponse::DataNotFoundResponse();
        }

        /** @var Position $pos */
        $pos = $this->getDoctrine()
            ->getRepository(Position::class)
            ->findOneBy(['id' => $request->request->get('positionId')]);
        if (null === $pos || $pos->getCompany()->getId() != $request->request->get('companyId')) {
            return ErrorResponse::DataNotFoundResponse();
        }

        $app = new Application();
        $app->setNotes($request->request->get('notes'))
            ->setCreateDate(new \DateTime());
        $pos->addApplication($app);
        $pos->getCompany()->addApplication($app);
        $resume->addApplication($app);

        try {
            $dm = $this->getDoctrine()->getManager();
            $dm->persist($app);
            $dm->persist($resume);
            $dm->persist($pos);
            $dm->persist($pos->getCompany());
            $dm->flush();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }

        return new Response(json_encode(['success' => true]));
    }

    /**
     * @Route("/student/getapplicationlist", name="application_list")
     */
    public function getAppList(Request $request, SessionInterface $session): Response
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

        /** @var Collection|Application[] $apps */
        $apps = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        $res = [];
        foreach ($apps as $app) {
            $res[] = [
                'applicationId' => $app->getId(),
                'resumeId' => $app->getResume()->getId(),
                'resumeName' => $app->getResume()->getName(),
                'companyId' => $app->getCompany()->getId(),
                'companyName' => $app->getCompany()->getName(),
                'positionId' => $app->getPosition()->getId(),
                'positionName' => $app->getPosition()->getName(),
                'applyDate' => $app->getCreateDate(),
            ];
        }
        return new Response(json_encode(['success' => true, 'applications' => $res]));
    }

    /**
     * @Route("/student/getapplication", name="get_application")
     */
    public function getApp(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("GET")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }
        if (!$request->query->has('applicationId')) {
            return ErrorResponse::FieldMissingErrorResponse(['applicationId']);
        }

        /** @var Student $me */
        $me = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_STU_ID)]);
        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        /** @var Application $app */
        $app = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findOneBy(['id' => $request->query->get('applicationId')]);
        if (null === $app) {
            return ErrorResponse::DataNotFoundResponse();
        }

        $res = [
            'applicationId' => $app->getId(),
            'resumeId' => $app->getResume()->getId(),
            'resumeName' => $app->getResume()->getName(),
            'companyId' => $app->getCompany()->getId(),
            'companyName' => $app->getCompany()->getName(),
            'positionId' => $app->getPosition()->getId(),
            'positionName' => $app->getPosition()->getName(),
            'applyDate' => $app->getCreateDate(),
            'notes' => $app->getNotes(),
        ];
        return new Response(json_encode(['success' => true, 'application' => $res]));
    }

    /**
     * @Route("/student/withdrawapp", name="withdraw_application")
     */
    public function withdrawApp(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }
        if (!$request->request->has('applicationId')) {
            return ErrorResponse::FieldMissingErrorResponse(['applicationId']);
        }

        /** @var Student $me */
        $me = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_STU_ID)]);
        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        /** @var Application $app */
        $app = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findOneBy(['id' => $request->request->get('applicationId')]);
        if (null === $app) {
            return ErrorResponse::DataNotFoundResponse();
        }

        try {
            $dm = $this->getDoctrine()->getManager();
            $dm->remove($app);
            $dm->flush();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }
        return new Response(json_encode(['success' => true]));
    }

    /**
     * @Route("/company/getapplist")
     */
    public function compGetAppList(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("GET")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_COMP_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        /** @var Company $comp */
        $comp = $this->getDoctrine()
            ->getRepository(Company::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_COMP_ID)]);
        if ($comp == null) {
            return ErrorResponse::DataNotFoundResponse();
        }

        $apps = $comp->getApplicationsArray();

        return new Response(json_encode(['success' => true, 'applications' => $apps]));
    }

}