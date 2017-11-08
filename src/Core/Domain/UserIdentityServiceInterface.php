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

namespace CreativeDelta\User\Core\Domain;


use CreativeDelta\User\Core\Domain\Entity\Identity;

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
     * @param UserRegisterMethodAdapter $adapter
     * @param string                    $account
     * @param string|null               $password
     * @param mixed                     $userId
     * @param mixed                     $data // This field is for additional data such as profile data, password, or configuration. Array type is recommended
     * @return int Primary key of the newly created UserIdentity record
     */
    public function register(UserRegisterMethodAdapter $adapter, $account, $password = null, $userId = null, $data = null);
}