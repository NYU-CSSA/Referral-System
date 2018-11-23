<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Student;

class StudentregisterController extends AbstractController
{
    /**
     * @Route("/studentregister", name="studentregister")
     */
    public function index()
    {
        return $this->render('studentregister/index.html.twig', [
            'controller_name' => 'StudentregisterController',
        ]);
    }

    /**
     * @Route("/studentregister/submitform", name="sturegform")
     */
    public function rigister(Request $request) {
    	$entityManager = $this->getDoctrine()->getManager();

    	$student = new Student();
    	$student->setName("aaa");
    	$student->setEmail($request->request->get("email"));
    	$student->setPassword("ccc");
    	$student->setCreatetime(new \Datetime());

    	$entityManager->persist($student);
    	$entityManager->flush();

    	$id = $student->getId();

    	return new Response("Successfully inserted!, id=$id");
    }

    /**
    * @Route("/studentregister/list")
    */
    public function listStudents() {
        $students = $this->getDoctrine()->getRepository(Student::class)
            ->findAll();
        $list = "";
        foreach($students as $s){
            $list = $list."</br> ".$s->getEmail();
        }
        return new Response('Check out these emails: '.$list);
    }
}
