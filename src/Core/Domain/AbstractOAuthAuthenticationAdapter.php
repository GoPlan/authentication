<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/9/17
 * Time: 8:36 AM
 */

namespace CreativeDelta\User\Core\Domain;


abstract class AbstractOAuthAuthenticationAdapter extends AbstractAuthenticationAdapter
{
    /**
     * @param $token
     * @return void
     */
    abstract function setAccessToken($token);

    /**
     * @return string
     */
    abstract function getAccessToken();
}