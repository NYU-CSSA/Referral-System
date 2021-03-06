<?php
// src/Utils/Constant.php

namespace App\Utils;

class Constant
{
    /** ------ Keys for session ------ */
    /** @var string */
    public static $SES_KEY_STU_ID = "student_id";
    /** @var string */
    public static $SES_KEY_STU_NAME = "student_name";
    /** @var string */
    public static $SES_KEY_STU_EMAIL = "student_email";
    /** @var string */
    public static $SES_KEY_COMP_ID = "company_id";
    /** @var string */
    public static $SES_KEY_COMP_EMAIL = "company_email";

    /** ------ File Paths ------ */
    public static $UPLOADS_PATH = "uploads";
    public static $USER_PHOTO_PATH = "uploads/userPhotos";
    public static $RESUME_PATH = "uploads/resumes";
}