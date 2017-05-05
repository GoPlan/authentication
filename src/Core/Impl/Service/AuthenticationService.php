<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 2:23 PM
 */

namespace CreativeDelta\User\Core\Impl\Service;


use CreativeDelta\User\Core\Domain\AuthenticationAdapterInterface;
use CreativeDelta\User\Core\Impl\Exception\AuthenticationException;

class AuthenticationService extends \Zend\Authentication\AuthenticationService
{

    public function hasIdentity()
    {
        return parent::hasIdentity() && $this->isActive();
    }

    protected function isActive()
    {
        if (!$this->adapter instanceof AuthenticationAdapterInterface) {
            throw new AuthenticationException(AuthenticationException::ERROR_CODE_UNKNOWN_IMPLEMENTATION_OF_ADAPTER);
        }

        return !$this->adapter->hasExpired();
    }

}