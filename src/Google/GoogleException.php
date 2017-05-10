<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 3:54 PM
 */

namespace CreativeDelta\User\Google;


use Exception;
use Throwable;

class GoogleException extends Exception
{
    const ERROR_CODE_VERIFY_IDENTITY_FAILED_TO_INITIATE_GOOGLE_OAUTH2 = 0;

    const MESSAGES = [
        self::ERROR_CODE_VERIFY_IDENTITY_FAILED_TO_INITIATE_GOOGLE_OAUTH2 => "ERROR_CODE_VERIFY_IDENTITY_FAILED_TO_INITIATE_GOOGLE_OAUTH2"
    ];

    public function __construct($code = 0, Throwable $previous = null)
    {
        $message = self::MESSAGES[$code];
        parent::__construct($message, $code, $previous);
    }
}