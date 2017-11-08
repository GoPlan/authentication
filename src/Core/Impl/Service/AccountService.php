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
use CreativeDelta\User\Core\Impl\Exception\UserIdentityException;

class AccountService implements UserIdentityServiceInterface
{
    protected $AccountTable;

    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->AccountTable = new AccountTable($dbAdapter);
//        $testAccount = new Account();
//        $testAccount->identity = 'newUserName2';
//        $testAccount->password = 'newPassword';
//        $testAccount->primaryId = 'Tester';
//        $testAccount->primaryTable = 'Tester';
//        $testAccount->state = 1;
//        $jsonData = Json::encode($testAccount);
//        $this->registerAccount($jsonData);

    }

#region "AccountServiceInterface"

    public function register(UserRegisterMethodAdapter $adapter, $identity, $userId, $data = null)
    {
        // TODO: Implement register() method.

        if ($this->hasIdentity($identity) || $adapter->has($userId)) {
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST);
        }

        
    }

    public function createSessionLog($previousHash = null, $returnUrl = null, $data = null)
    {
        // TODO: Implement createSessionLog() method.
    }

    public function getSessionLog($hash)
    {
        // TODO: Implement getSessionLog() method.
    }

    public function hasIdentity($identity)
    {
        return $this->AccountTable->hasIdentity($identity);
    }

    public function getIdentityByIdentity($identity)
    {
        $Result = $this->AccountTable->getAccountByIdentity($identity);
        return $Result;
    }

    public function getIdentityById($id)
    {
        $id = (int)$id;
        $Result = $this->AccountTable->getAccount($id);
        return $Result;
    }

#endregion


}
