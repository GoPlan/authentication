<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/7/17
 * Time: 2:05 PM
 */

namespace CreativeDelta\User\Service;


interface UserServiceStrategyInterface
{
    public function has($userId);

    public function get($userId);

    public function register($identity, $userId, $dataJson);
}