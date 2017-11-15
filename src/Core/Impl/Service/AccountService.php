<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 16:12
 */

namespace CreativeDelta\User\Core\Impl\Service;


use CreativeDelta\User\Account\AccountTable;
use CreativeDelta\User\Core\Domain\Entity\Identity;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Domain\UserRegisterMethodAdapter;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Json\Json;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;

class AccountService implements UserIdentityServiceInterface
{

    const ACCOUNT_RESET_SUCCESS                       = 1;
    const ACCOUNT_RESET_CURRENT_PASSWORD_IS_INCORRECT = -1;
    const ACCOUNT_RESET_PASSWORD_DOES_NOT_MATCH       = -2;
    const ACCOUNT_RESET_CURRENT_PASSWORD_INVALID      = -3;
    const ACCOUNT_RESET_NEW_PASSWORD_INVALID          = -4;
    const ACCOUNT_RESET_FAILED                        = -5;


    protected $AccountTable;


    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->AccountTable = new AccountTable($dbAdapter);
    }

#region "AccountServiceInterface"

    public function register(UserRegisterMethodAdapter $adapter, $account, $password = null, $userId = null, $data = null)
    {
        if ($this->hasAccount($account)) {
//            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST);
            return false;
        }

        if ($data == null) {
            $nAccount = new Identity();
            $nAccount->setAccount($account);
            $nAccount->setPassword($password);
            $nAccount->setState(1);

            $data = Json::encode($nAccount->getArrayCopy());
        }

        return $adapter->register($account, $userId, $data);
    }

    public function getIdentityByAccount($account)
    {
        $Result = $this->AccountTable->getAccountByIdentity($account);
        return $Result;
    }

    public function hasAccount($account)
    {
        return $this->AccountTable->hasAccount($account);
    }

    public function createSessionLog($previousHash = null, $returnUrl = null, $data = null)
    {
        //
    }

    public function getSessionLog($hash)
    {
        //
    }

    public function getIdentityById($id)
    {
        $id     = (int)$id;
        $Result = $this->AccountTable->getAccount($id);
        return $Result;
    }

    public function setCurrentPasswordByAccount(Identity $identity, $currentPass, $newPass, $confirmNewPass)
    {
        $bcrypt = new Bcrypt();

        $mValidator = new ValidatorChain();
        $mValidator->attach(new StringLength(['min' => 8, 'max' => 32]));

        if (!$mValidator->isValid($currentPass)) {
            return self::ACCOUNT_RESET_CURRENT_PASSWORD_INVALID;
        }

        if (!$mValidator->isValid($newPass)) {
            return self::ACCOUNT_RESET_NEW_PASSWORD_INVALID;
        }

        if (!$bcrypt->verify($currentPass, $identity->getPassword())) {
            return self::ACCOUNT_RESET_CURRENT_PASSWORD_IS_INCORRECT;
        }

        if ($newPass != $confirmNewPass) {
            return self::ACCOUNT_RESET_PASSWORD_DOES_NOT_MATCH;
        }

        //encrypt new pass
        $identity->setPassword($bcrypt->create($newPass));

        //save new pass
        if ($this->AccountTable->saveAccount($identity)) {
            return self::ACCOUNT_RESET_SUCCESS;
        } else {
            return self::ACCOUNT_RESET_FAILED;
        }
    }

    public function setRootPassword($account, $newPass, $confirmNewPass)
    {
        $bcrypt = new Bcrypt();

        $mValidator = new ValidatorChain();
        $mValidator->attach(new StringLength(['min' => 8, 'max' => 32]));

        if (!$mValidator->isValid($newPass)) {
            return self::ACCOUNT_RESET_NEW_PASSWORD_INVALID;
        }
        if ($newPass != $confirmNewPass) {
            return self::ACCOUNT_RESET_PASSWORD_DOES_NOT_MATCH;
        }

        $rootIdentity = $this->AccountTable->getAccountByIdentity($account);
        if ($rootIdentity == null) {
            $rootIdentity = new Identity();
            $rootIdentity->setAccount($account);
            $rootIdentity->setState(Identity::STATE_ACTIVE);
        }

        $rootIdentity->setPassword($bcrypt->create($newPass));

        if ($this->AccountTable->saveAccount($rootIdentity)) {
            return self::ACCOUNT_RESET_SUCCESS;
        } else {
            return self::ACCOUNT_RESET_FAILED;
        }

    }

    public function attach(UserRegisterMethodAdapter $adapter, $identityId, $userId, $data)
    {

    }

#endregion

}
