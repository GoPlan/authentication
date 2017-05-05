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

namespace CreativeDelta\User\Core\Service;


interface UserRegisterMethodAdapter
{
    /**
     * @param $userId
     * @return mixed
     */
    public function has($userId);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getTableName();

    /**
     * @param $identityId
     * @param $userId
     * @param $dataJson
     * @return int // Newly inserted record key
     */
    public function register($identityId, $userId, $dataJson);
}