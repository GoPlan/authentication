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

    public function saveIdentity(Identity $identity);

    public function getIdentityById($id);

    public function getIdentityByAccount($account);

    public function hasAccount($account);

    public function hasId($id);
}