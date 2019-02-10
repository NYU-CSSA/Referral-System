<?php

namespace App\Controller;

use App\Utils\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Entity\Student;
use App\Utils\Utils;

class StudentregisterController extends AbstractController
{
    /**
     * @Route("/register/student", name="register_stu")
     */
    public function registerStu(Request $request): Response
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }

        // validation
        $missedFileds = Utils::getMissingFields($request, ['name', 'email', 'gender', 'photo', 'password']);
        if (sizeof($missedFileds) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFileds);
        }

        // create entity
        $student = new Student();
        $student->setName($request->request->get("name"));
        $student->setEmail($request->request->get("email"));
        $student->setGender($request->request->get("gender"));
        $student->setPhoto($request->request->get("photo"));
        $student->setPassword($request->request->get("password"));
        $student->setCreatetime(new \Datetime());

        // store this entity to db
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return ErrorResponse::DuplicatedRegistrationResponse();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }

        // we can get the auto-generated ID of this entity
        // $id = $student->getId();

        return new Response(json_encode(["success" => true]));
    }
}
