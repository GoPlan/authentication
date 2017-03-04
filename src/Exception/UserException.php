<?php
/**
 * Created by PhpStorm.
 *
 * combo-outfit (by Duc-Anh LE)
 *
 * User: ducanh-ki
 * Date: 3/4/17
 * Time: 9:49 AM
 */

namespace CreativeDelta\User\Exception;


use Exception;

class UserException extends \Exception
{
    const CODE_ACCOUNT_EXIST_ERROR   = 1;
    const CODE_DATABASE_INSERT_ERROR = 2;

    const codes = [
        self::CODE_ACCOUNT_EXIST         => "Account already existed",
        self::CODE_DATABASE_INSERT_ERROR => "Database insertion failed"
    ];

    public function __construct($code = 0, Exception $previous = null)
    {
        $message = self::codes[$code];
        parent::__construct($message, $code, $previous);
    }
}