<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 11:58 AM
 */

namespace CreativeDelta\User\Core\Exception;


use Throwable;

class AuthenticationException extends \Exception
{
    const ERROR_CODE_AUTHENTICATION_SERVICE_NOT_SUPPORTED = -1;

    const MESSAGES = [
        self::ERROR_CODE_AUTHENTICATION_SERVICE_NOT_SUPPORTED => "ERROR_CODE_AUTHENTICATION_SERVICE_NOT_SUPPORTED"
    ];

    public function __construct($code = 0, Throwable $previous = null)
    {
        $message = self::MESSAGES[$code];
        parent::__construct($message, $code, $previous);
    }
}