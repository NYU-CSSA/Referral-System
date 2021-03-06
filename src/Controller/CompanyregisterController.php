<?php

namespace App\Controller;

use App\Utils\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Entity\Company;
use App\Utils\Utils;

class CompanyregisterController extends AbstractController
{
    /**
     * @Route("/register/company", name="register_comp")
     */
    public function registerComp(Request $request): Response
    {
        if (!$request->isMethod("POST")) {
            return ErrorResponse::RequestTypeErrorResponse();
        }

        // validation
        $missedFields = Utils::getMissingFields($request, ['name', 'email', 'password']);
        if (sizeof($missedFields) != 0) {
            return ErrorResponse::FieldMissingErrorResponse($missedFields);
        }

        // create entity
        $company = new Company();
        $company->setName($request->request->get("name"));
        $company->setEmail($request->request->get("email"));
        $company->setPassword($request->request->get("password"));
        $company->setDescription($request->request->get("description"));
        $company->setCreatetime(new \Datetime());

        // store this entity to db
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return ErrorResponse::DuplicatedRegistrationResponse();
        } catch (\Exception $e) {
            $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getTraceAsString());
            return ErrorResponse::InternalErrorResponse($message);
        }

        return new Response(json_encode(["success" => true]));
    }
}
