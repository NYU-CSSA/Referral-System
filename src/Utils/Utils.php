<?php
// src/Utils/Utils.php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class Utils {
    public static function fieldsExist(Request $req, array $fields){
        foreach ($fields as $field){
            if(!$req->request->has($field)){
                return false;
            }
        }
        return true;
    }
}