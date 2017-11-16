<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 08/11/2017
 * Time: 15:50
 */

namespace CreativeDelta\User\Account;


use CreativeDelta\User\Core\Domain\Entity\Identity;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;

class AccountAuthenticationAdapter implements AdapterInterface
{
    protected $userIdentityService;
    protected $account;
    protected $password;

    public function __construct(UserIdentityServiceInterface $userIdentityService, $account, $password)
    {
        $this->userIdentityService = $userIdentityService;
        $this->account        = $account;
        $this->password       = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        $bcrypt = new Bcrypt();
        /** @var Identity $account */
        $account = $this->userIdentityService->getIdentityByAccount($this->account);


        if (!$account) {
            return new Result(Result::FAILURE, null, ['Login failed.']);
        }

        if ($account->getState() != 1) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Account disabled.']);
        }

        if (!$this->password || strlen($this->password) == 0) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Wrong password.']);
        }

        if (!$bcrypt->verify($this->password, $account->getPassword())) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Wrong password.']);
        }

        return new Result(Result::SUCCESS, $account, ['Login Successful.']);
    }
}