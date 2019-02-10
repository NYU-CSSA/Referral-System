<?php
// src/Utils/Utils.php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Utils {
    public static function fieldsExist(Request $req, array $fields): bool {
        foreach ($fields as $field){
            if(!$req->request->has($field)){
                return false;
            }
        }
        return true;
    }

    public static function makeErrMsgResponse(string $errMsg): Response {
        return new Response(json_encode(['success'=>false, 'errMsg'=>$errMsg]));
    }
}