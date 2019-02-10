<?php
// src/Utils/Utils.php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Utils
{
    public static function getMissingFields(Request $req, array $fields): array
    {
        $res = [];
        foreach ($fields as $field) {
            if (!$req->request->has($field)) {
                $res[] = $field;
            }
        }
        return $res;
    }
    
}