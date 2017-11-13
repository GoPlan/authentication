<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 16:12
 */

namespace CreativeDelta\User\Core\Impl\Service;


use CreativeDelta\User\Account\AccountTable;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Domain\UserRegisterMethodAdapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Json\Json;
use CreativeDelta\User\Core\Domain\Entity\Identity;

class AccountService implements UserIdentityServiceInterface
{
    protected $AccountTable;

    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->AccountTable = new AccountTable($dbAdapter);

    }

#region "AccountServiceInterface"

    public function register(UserRegisterMethodAdapter $adapter, $account, $password = null, $userId = null, $data = null)
    {
        // TODO: Implement register() method.

        if ($this->hasAccount($account)) {
//            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST);
            return false;
        }

        if($data == null)
        {
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
        // TODO: Implement createSessionLog() method.
    }

    public function getSessionLog($hash)
    {
        // TODO: Implement getSessionLog() method.
    }

    public function getIdentityById($id)
    {
        $id = (int)$id;
        $Result = $this->AccountTable->getAccount($id);
        return $Result;
    }

#endregion


}
