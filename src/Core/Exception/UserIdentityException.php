<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/4/17
 * Time: 9:49 AM
 */

namespace CreativeDelta\User\Core\Exception;


use Exception;

class UserIdentityException extends \Exception
{
    const CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST                 = 1;
    const CODE_ERROR_INSERT_DATABASE_OPERATION_FAILED             = 2;
    const CODE_ERROR_AUTHENTICATION_UNKNOWN_RESULT_CODE           = 3;
    const CODE_ERROR_AUTHENTICATION_USER_NOT_ACTIVE               = 4;
    const CODE_ERROR_SERVICE_AUTHENTICATION_INITIALIZATION_FAILED = 5;

    const codes = [
        self::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST                 => "INSERT_ACCOUNT_ALREADY_EXIST",
        self::CODE_ERROR_INSERT_DATABASE_OPERATION_FAILED             => "INSERT_DATABASE_OPERATION_FAILED",
        self::CODE_ERROR_AUTHENTICATION_UNKNOWN_RESULT_CODE           => "AUTHENTICATION_UNKNOWN_RESULT_CODE",
        self::CODE_ERROR_AUTHENTICATION_USER_NOT_ACTIVE               => "AUTHENTICATION_USER_NOT_ACTIVE",
        self::CODE_ERROR_SERVICE_AUTHENTICATION_INITIALIZATION_FAILED => "SERVICE_AUTHENTICATION_INITIALIZATION_FAILED"
    ];

    public function __construct($code = 0, Exception $previous = null)
    {
        $message = self::codes[$code];
        parent::__construct($message, $code, $previous);
    }
}