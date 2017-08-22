<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/14/17
 * Time: 9:33 AM
 */

namespace CreativeDelta\User\Facebook;


class FacebookException extends \Exception
{
    // Custom Error Codes
    const ERROR_CODE_ACCESS_TOKEN_IS_NULL = -1;

    // Facebook Error Codes
    const ERROR_TYPE                          = "OAuthException";
    const ERROR_CODE_ACCESS_TOKEN_EXPIRED     = 190;
    const ERROR_CODE_API_TOO_MANY_CALLS       = 1;
    const ERROR_CODE_API_USER_TOO_MANY_CALLS  = 2;
    const ERROR_CODE_APPLICATION_LIMIT_REACH  = 341;
    const ERROR_SUB_CODE_PASSWORD_CHANGED     = 460;
    const ERROR_SUB_CODE_EXPIRED              = 463;
    const ERROR_SUB_CODE_INVALID_ACCESS_TOKEN = 467;

    // Class Fields
    protected $type;
    protected $errorSubCode;
    protected $errorUserMessage;
    protected $errorUserTitle;
    protected $fbTraceId;

    /**
     * FacebookException constructor.
     * @param $message
     * @param $code
     * @param $type
     * @param $errorSubCode
     * @param $errorUserMessage
     * @param $errorUserTitle
     * @param $fbTraceId
     * @param $prevException
     */
    public function __construct($message, $code, $type, $errorSubCode, $errorUserMessage, $errorUserTitle, $fbTraceId, \Exception $prevException = null)
    {
        $this->type             = $type;
        $this->errorSubCode     = $errorSubCode;
        $this->errorUserMessage = $errorUserMessage;
        $this->errorUserTitle   = $errorUserTitle;
        $this->fbTraceId        = $fbTraceId;

        parent::__construct($message, $code, $prevException);
    }

    /**
     * @param array           $error
     * @param null|\Exception $prevException
     * @return FacebookException
     * @internal param array $errorData
     */
    static function newFromArray(array $error, \Exception $prevException = null)
    {
        $errorData = $error['error'];

        $message = $errorData['message'];
        $code    = $errorData['code'];
        $type    = $errorData['type'];

        $errorSubCode     = null;
        $errorUserMessage = null;
        $errorUserTitle   = null;
        $errorFbTraceId   = null;

        /** @noinspection SpellCheckingInspection */
        if (isset($errorData['error_subcode']))
            /** @noinspection SpellCheckingInspection */
            $errorSubCode = $errorData['error_subcode'];

        if (isset($errorData['error_user_msg']))
            $errorUserMessage = $errorData['error_user_msg'];

        if (isset($errorData['error_user_title']))
            $errorUserTitle = $errorData['error_user_title'];

        /** @noinspection SpellCheckingInspection */
        if (isset($errorData['fbtrace_id']))
            /** @noinspection SpellCheckingInspection */
            $errorFbTraceId = $errorData['fbtrace_id'];

        $exception = new FacebookException($message, $code, $type, $errorSubCode, $errorUserMessage, $errorUserTitle, $errorFbTraceId, $prevException);

        return $exception;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getErrorSubCode()
    {
        return $this->errorSubCode;
    }

    /**
     * @return mixed
     */
    public function getErrorUserMessage()
    {
        return $this->errorUserMessage;
    }

    /**
     * @return mixed
     */
    public function getErrorUserTitle()
    {
        return $this->errorUserTitle;
    }

    /**
     * @return mixed
     */
    public function getFbTraceId()
    {
        return $this->fbTraceId;
    }
}