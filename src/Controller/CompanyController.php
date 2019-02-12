<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Position;
use App\Utils\Constant;
use App\Utils\ErrorResponse;
use App\Utils\Utils;
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
        if (!$request->isMethod("GET")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_COMP_ID)) {
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

    /**
     * @Route("/company/addposition")
     */
    public function addPosition(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_COMP_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        $missedFields = Utils::getMissingFields($request, ['name', 'description', 'number']);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        /** @var Company $me */
        $me = $this->getDoctrine()
            ->getRepository(Company::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_COMP_ID)]);
        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        $pos = new Position();
        $pos->setName($request->request->get('name'))
            ->setDesctiption($request->request->get('description'))
            ->setNumber($request->request->get('number'))
            ->setCompany($me);

        $me->addPosition($pos);
        try {
            $dm = $this->getDoctrine()->getManager();
            $dm->persist($pos);
            $dm->persist($me);
            $dm->flush();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }

        return new Response(json_encode(['success' => true]));
    }

    /**
     * @Route("/company/getpositions")
     */
    public function getPositions(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("GET")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_COMP_ID) && !$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }
        if (!$request->query->has('companyId')) {
            return ErrorResponse::FieldMissingErrorResponse(['companyId']);
        }

        /** @var Company $comp */
        $comp = $this->getDoctrine()
            ->getRepository(Company::class)
            ->findOneBy(['id' => $request->query->get('companyId')]);
        if ($comp == null) {
            return ErrorResponse::DataNotFoundResponse();
        }

        $pos = $comp->getPositionsArray();

        return new Response(json_encode(['success' => true, 'positions' => $pos]));
    }

    /**
     * @Route("/company/deleteposition")
     */
    public function deletePosition(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_COMP_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }
        $missedFields = Utils::getMissingFields($request, ['positionId']);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        /** @var Position $pos */
        $pos = $this->getDoctrine()
            ->getRepository(Position::class)
            ->findOneBy(['id' => $request->request->get('positionId')]);
        if ($pos == null || $pos->getCompany()->getId() != $session->get(Constant::$SES_KEY_COMP_ID)) {
            return ErrorResponse::DataNotFoundResponse();
        }

        $me = $pos->getCompany();
        $me->removePosition($pos);
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
