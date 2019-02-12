<?php
// src/Utils/ErrprResponse.php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse
{
    /** -------- Application-level errors -------- */
    public static function UnLoggedErrorResponse(): Response
    {
        return self::makeErrMsgResponse(101, "You have not logged in");
    }

    public static function DuplicateLoginErrorResponse(): Response
    {
        return self::makeErrMsgResponse(102, "You have already logged in");
    }

    public static function RequestTypeErrorResponse(): Response
    {
        return self::makeErrMsgResponse(103, "Wrong Request Type (GET/POST)");
    }

    public static function FieldMissingErrorResponse(array $missedFields): Response
    {
        return self::makeErrMsgResponse(104, "Request Field Missing: " . json_encode($missedFields));
    }

    /** -------- User-level errors -------- */
    public static function DuplicatedRegistrationResponse(): Response
    {
        return self::makeErrMsgResponse(201, "The email is already registered");
    }

    public static function LoginErrorResponse(): Response
    {
        return self::makeErrMsgResponse(202, "Wrong password / User does not exist");
    }

    public static function DataNotFoundResponse(): Response
    {
        return self::makeErrMsgResponse(203, "The requested data is not found or you don't have the permission");
    }

    /** -------- Backend-level errors --------- */
    public static function InternalErrorResponse(string $errMsg): Response
    {
        return self::makeErrMsgResponse(999, $errMsg);
    }

    private static function makeErrMsgResponse(int $errCode, string $errMsg): Response
    {
        return new Response(json_encode([
            'success' => false,
            'errCode' => $errCode,
            'errMsg' => $errMsg
        ]));
    }
}