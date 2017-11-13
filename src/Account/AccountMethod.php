<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 08/11/2017
 * Time: 13:20
 */

namespace CreativeDelta\User\Account;


use CreativeDelta\User\Core\Domain\UserRegisterMethodAdapter;
use Zend\Db\Adapter\Adapter;
use Zend\Json\Json;
use CreativeDelta\User\Core\Domain\Entity\Identity;

class AccountMethod implements UserRegisterMethodAdapter
{
    const METHOD_NAME       = "account";
    protected $dbAdapter;
    protected $AccountTable;

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        $this->AccountTable = new AccountTable($dbAdapter);
    }

    public function getName()
    {
        return self::METHOD_NAME;
    }

    public function getTableName()
    {
        return $this->AccountTable->getTableName();
    }

    public function has($userId)
    {
        return $this->AccountTable->hasAccountId($userId);
    }

    public function register($identityId, $userId, $dataJson)
    {
        $data = Json::decode($dataJson, Json::TYPE_ARRAY);
        /** @var Identity $nAccount */
        $nAccount = new Identity();
        $nAccount->exchangeArray($data);
        return $this->AccountTable->saveAccount($nAccount);
    }
}