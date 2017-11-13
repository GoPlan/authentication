<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 16:00
 */

namespace CreativeDelta\User\Account;


use CreativeDelta\User\Core\Domain\Entity\Identity;

interface AccountTableGatewayInterface
{
    public function getAccountTableGateway();

    public function getDbAdapter();

    public function saveAccount(Identity $account);

    public function getAccount($id);

    public function getAccountByIdentity($identity);

    public function hasAccount($identity);

    public function hasAccountId($id);
}