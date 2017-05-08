<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/8/17
 * Time: 11:36 AM
 */

namespace CreativeDelta\User\Core\Domain\Entity;


abstract class AbstractOAuthProfile extends AbstractProfile
{
    /**
     * @return mixed
     */
    abstract function getAccessToken();

    /**
     * @param $token
     * @return mixed
     */
    abstract function setAccessToken($token);

    /**
     * @return mixed
     */
    abstract function getCode();

    /**
     * @return mixed
     */
    abstract function getRefreshToken();

}