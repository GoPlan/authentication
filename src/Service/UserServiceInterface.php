<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/3/17
 * Time: 3:31 PM
 */

namespace CreativeDelta\User\Service;


interface UserServiceInterface
{
    public function hasIdentity($identity);

    public function hasRecord($userId);

    public function register($identity, $userId, $profile);

}