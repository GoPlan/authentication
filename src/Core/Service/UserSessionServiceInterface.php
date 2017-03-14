<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/14/17
 * Time: 12:02 PM
 */

namespace CreativeDelta\User\Core\Service;


use CreativeDelta\User\Core\Model\SessionLog;

interface UserSessionServiceInterface
{
    /**
     * @param $previousHash
     * @param $returnUrl
     * @param $data
     * @return string
     */
    public function createSessionLog($previousHash = null, $returnUrl = null, $data = null);

    /**
     * @param $hash
     * @return SessionLog|null
     */
    public function getSessionLog($hash);
}