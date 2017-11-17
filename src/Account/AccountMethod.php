<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 08/11/2017
 * Time: 13:20
 */

namespace CreativeDelta\User\Account;


use CreativeDelta\User\Core\Domain\UserRegisterMethodAdapter;

class AccountMethod implements UserRegisterMethodAdapter
{
    const METHOD_NAME = "account";
    protected $dbAdapter;
    protected $UserIdentityTable;

//    public function __construct(Adapter $dbAdapter)
//    {
//        $this->dbAdapter    = $dbAdapter;
//        $this->UserIdentityTable = new UserIdentityTable($dbAdapter);
//    }

    public function getName()
    {
        return self::METHOD_NAME;
    }

    public function getTableName()
    {
//        return $this->UserIdentityTable->getTableName();
    }

    public function has($userId)
    {
//        return $this->UserIdentityTable->hasId($userId);
    }

    public function register($identityId, $userId, $dataJson)
    {
        // Since UserIdentityService already created the Identity record, this function can run with empty execution
        return;
    }
}