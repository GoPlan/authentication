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

namespace CreativeDelta\User\Core\Impl\Exception;


use Throwable;

class AuthenticationException extends \Exception
{
    const ERROR_CODE_AUTHENTICATION_SERVICE_NOT_SUPPORTED = -1;
    const ERROR_CODE_UNKNOWN_IMPLEMENTATION_OF_ADAPTER    = -2;

    const MESSAGES = [
        self::ERROR_CODE_AUTHENTICATION_SERVICE_NOT_SUPPORTED => "ERROR_CODE_AUTHENTICATION_SERVICE_NOT_SUPPORTED",
        self::ERROR_CODE_UNKNOWN_IMPLEMENTATION_OF_ADAPTER    => "ERROR_CODE_UNKNOWN_IMPLEMENTATION_OF_ADAPTER"
    ];

    public function __construct($code = 0, Throwable $previous = null)
    {
        $message = self::MESSAGES[$code];
        parent::__construct($message, $code, $previous);
    }
}