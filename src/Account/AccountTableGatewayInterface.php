<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 16:00
 */

namespace CreativeDelta\User\Account;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

interface AccountTableGatewayInterface
{
    public function getAccountTableGateway();

    public function getDbAdapter();

    public function saveAccount(Account $account);

    public function getAccount($id);

    public function getAccountByIdentity($identity);

    public function hasIdentity($identity);

    public function hasAccountId($id);
}