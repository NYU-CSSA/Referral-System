<?php

namespace App\Controller;

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
            return new Response(json_encode(["success" => false, "errMsg" => "not a Post request"]));
        }

        // validation
        if (!Utils::fieldsExist($request, ['name', 'email', 'gender', 'photo', 'password'])) {
            return new Response(json_encode(["success" => false, "errMsg" => "field doesn't exist", "received" => $request]));
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
            return Utils::makeErrMsgResponse("The email has already been registered");
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return Utils::makeErrMsgResponse($message);
        }

        // we can get the auto-generated ID of this entity
        // $id = $student->getId();

        return new Response(json_encode(["success" => true]));
    }

    /**
     * @Route("/studentregister/list")
     */
    public function listStudents(): Response
    {
        $students = $this->getDoctrine()->getRepository(Student::class)
            ->findAll();
        $list = "";
        foreach ($students as $s) {
            $list = $list . "</br> " . $s->getEmail();
        }
        return new Response('Check out these emails: ' . $list);
    }
}
