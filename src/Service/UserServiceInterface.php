<?php
/**
 * Created by PhpStorm.
 *
 * combo-outfit (by Duc-Anh LE)
 *
 * User: ducanh-ki
 * Date: 3/3/17
 * Time: 3:31 PM
 */

namespace CreativeDelta\User\Service;


interface UserServiceInterface
{
    public function hasUsername($username);

    public function hasFacebook($facebookId);

    public function registerFacebook($username, $facebookId, $profile);

}