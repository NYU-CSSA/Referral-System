<?php

namespace App\Controller;

use App\Entity\Experience;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\ErrorResponse;
use App\Entity\Student;
use App\Entity\Resume;
use App\Utils\Constant;
use App\Utils\Utils;

class StudentResumeController extends AbstractController
{
    /**
     * @Route("/student/getresumelist")
     */
    public function getResumeList(Request $request, SessionInterface $session): Response
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

        $resumes = $me->getResumes();
        $res = [];
        foreach ($resumes as $resume) {
            $res[] = [
                'resumeId' => $resume->getId(),
                'resumeName' => $resume->getResumeName(),
                'intro' => $resume->getIntro(),
                'lastEditTime' => $resume->getUpdatetime(),
            ];
        }

        return new Response(json_encode(['success' => true, 'resumeList' => $res]));
    }

    /**
     * @Route("/student/getresume")
     */
    public function getResume(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("GET")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }


        if (!$request->query->has('resumeId')) {
            return ErrorResponse::FieldMissingErrorResponse(['resumeId']);
        }

        /** @var Resume $resume */
        $resume = $this->getDoctrine()
            ->getRepository(Resume::class)
            ->findOneBy(['id' => $request->query->get('resumeId')]);

        if ($resume == null || $resume->getStudent()->getId() != $session->get(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::DataNotFoundResponse();
        }

        $res = [
            'resumeId' => $resume->getId(),
            'resumeName' => $resume->getResumeName(),
            'name' => $resume->getName(),
            'grade' => $resume->getGrade(),
            'gpa' => $resume->getGpa(),
            'major' => $resume->getMajor(),
            'intro' => $resume->getIntro(),
            'skills' => $resume->getSkills(),
            'experiences' => $resume->getExperiencesArray(),
            'pdf' => $resume->getPdf(),
            'lastEditTime' => $resume->getUpdatetime(),
        ];

        return new Response(json_encode(['success' => true, 'resume' => $res]));
    }

    /**
     * @Route("/student/newresume")
     */
    public function newResume(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        $missedFields = Utils::getMissingFields($request, [
            'resumeName', 'name', 'grade', 'major', 'gpa', 'experiences', 'skills',
        ]);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        $dm = $this->getDoctrine()->getManager();
        /** @var Student $me */
        $me = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_STU_ID)]);
        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        $resume = new Resume();
        $me->addResume($resume);
        $resume->setName($request->request->get('name'))
            ->setResumeName($request->request->get('resumeName'))
            ->setIntro($request->request->get('intro'))
            ->setGpa($request->request->get('gpa'))
            ->setMajor($request->request->get('major'))
            ->setGrade($request->request->get('grade'))
            ->setSkills($request->request->get('skills'))
            ->setCreatetime(new \DateTime())
            ->setUpdatetime(new \DateTime());

        $exps = json_decode($request->request->get('experiences'), true);
        foreach ($exps as $exp) {
            $e = new Experience();
            $e->setType($exp['type'])
                ->setTimePeriod($exp['timePeriod'])
                ->setDescription($exp['description'])
                ->setCompanyName($exp['companyName'])
                ->setResume($resume);
            $dm->persist($e);
            $resume->addExperience($e);
        }

        $pdf = $request->files->get('pdf');
        if ($pdf !== null) {
            $newFileName = Utils::generateUniqueFileName() . '.' . $pdf->guessExtension();
            $pdf->move("./" . Constant::$RESUME_PATH, $newFileName);
            $resume->setPdf("/" . Constant::$RESUME_PATH . '/' . $newFileName);
        }

        try {
            $dm->persist($resume);
            $dm->persist($me);
            $dm->flush();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }

        return new Response(json_encode([
            'success' => true,
        ]));
    }

    /**
     * @Route("/student/deleteresume")
     */
    public function deleteResume(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        $missedFields = Utils::getMissingFields($request, [
            'resumeId',
        ]);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        /** @var Resume $resume */
        $resume = $this->getDoctrine()
            ->getRepository(Resume::class)
            ->findOneBy(['id' => $request->request->get('resumeId')]);

        if ($resume == null || $resume->getStudent()->getId() != $session->get(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::DataNotFoundResponse();
        }

        /** @var Student $me */
        $me = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_STU_ID)]);
        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        $me->removeResume($resume);
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
     * @Route("/student/editresume")
     */
    public function editResume(Request $request, SessionInterface $session): Response
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }
        if (!$session->has(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::UnLoggedErrorResponse();
        }

        $missedFields = Utils::getMissingFields($request, [
            'resumeId', 'resumeName', 'name', 'grade', 'major', 'gpa', 'experiences', 'skills',
        ]);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        $dm = $this->getDoctrine()->getManager();
        /** @var Student $me */
        $me = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findOneBy(['id' => $session->get(Constant::$SES_KEY_STU_ID)]);
        if ($me == null) {
            $session->clear();
            return ErrorResponse::InternalErrorResponse("IMPOSSIBLE: userID in session does not exist!");
        }

        /** @var Resume $resume */
        $resume = $this->getDoctrine()
            ->getRepository(Resume::class)
            ->findOneBy(['id' => $request->request->get('resumeId')]);

        if ($resume == null || $resume->getStudent()->getId() != $session->get(Constant::$SES_KEY_STU_ID)) {
            return ErrorResponse::DataNotFoundResponse();
        }

        // first clear the experiences of this resume
        foreach ($resume->getExperiences() as $i) {
            $resume->removeExperience($i);
        }

        $resume->setName($request->request->get('name'))
            ->setResumeName($request->request->get('resumeName'))
            ->setIntro($request->request->get('intro'))
            ->setGpa($request->request->get('gpa'))
            ->setMajor($request->request->get('major'))
            ->setGrade($request->request->get('grade'))
            ->setSkills($request->request->get('skills'))
            ->setCreatetime(new \DateTime())
            ->setUpdatetime(new \DateTime());

        $exps = json_decode($request->request->get('experiences'), true);
        foreach ($exps as $exp) {
            $e = new Experience();
            $e->setType($exp['type'])
                ->setTimePeriod($exp['timePeriod'])
                ->setDescription($exp['description'])
                ->setCompanyName($exp['companyName'])
                ->setResume($resume);
            $dm->persist($e);
            $resume->addExperience($e);
        }

        $pdf = $request->files->get('pdf');
        if ($pdf !== null) {
            $newFileName = Utils::generateUniqueFileName() . '.' . $pdf->guessExtension();
            $pdf->move("./" . Constant::$RESUME_PATH, $newFileName);
            $resume->setPdf("/" . Constant::$RESUME_PATH . '/' . $newFileName);
        }

        try {
            $dm->persist($resume);
            $dm->persist($me);
            $dm->flush();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }

        return new Response(json_encode([
            'success' => true,
        ]));
    }

}
