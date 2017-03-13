<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/13/17
 * Time: 2:25 PM
 */

namespace CreativeDelta\User\Core\Model;


abstract class AbstractProfile
{
    abstract function getId();

    abstract function getUserId();

    abstract function getIdentityId();
}