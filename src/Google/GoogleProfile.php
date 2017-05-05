<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 6:11 PM
 */

namespace CreativeDelta\User\Google;


use CreativeDelta\User\Core\Domain\Entity\AbstractProfile;

class GoogleProfile extends AbstractProfile
{

    public static function newFromData($data)
    {
        $profile = new GoogleProfile();



        return $profile;
    }

    function getId()
    {
        // TODO: Implement getId() method.
    }

    function getUserId()
    {
        // TODO: Implement getUserId() method.
    }

    function getIdentityId()
    {
        // TODO: Implement getIdentityId() method.
    }
}