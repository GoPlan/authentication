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

namespace CreativeDelta\User\Core\Service;


use CreativeDelta\User\Core\Model\Identity;
use CreativeDelta\User\Core\Model\SessionLog;

interface UserIdentityServiceInterface
{

    /**
     * @param string $identity
     * @return bool
     */
    public function hasIdentity($identity);

    /**
     * @param string $identity
     * @return Identity
     */
    public function getIdentityByIdentity($identity);

    /**
     * @param $identityId
     * @return Identity
     */
    public function getIdentityById($identityId);

    /**
     * @param UserRegisterMethodAdapter|UserAuthenticationMethodServiceInterface $adapter
     * @param string $identity
     * @param $userId // This field is a primary key of user record stored in (authentication) method tables - email, facebook, g+. It is usually either email, user_id depending on your authentication method record.
     * @param mixed $data // This field is a data field in json format storing additional data such as profile data, or configuration.
     * @return int
     */
    public function register(UserRegisterMethodAdapter $adapter, $identity, $userId, $data);

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