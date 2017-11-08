<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 08/11/2017
 * Time: 15:50
 */

namespace CreativeDelta\User\Account;


use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use Zend\Authentication\Result;

class AccountAuthenticationAdapter implements \Zend\Authentication\Adapter\AdapterInterface
{
    protected $AccountService;
    protected $account;
    protected $password;

    public function __construct(UserIdentityServiceInterface $AccountService, $account, $password)
    {
        $this->AccountService = $AccountService;
        $this->account = $account;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        /** @var Account $identity */
        $identity = $this->AccountService->getIdentityByIdentity($this->account);



        if(!$identity)
        {
            return new Result( Result::FAILURE, null, "Login failed.");
        }

        if($identity->getState() != 1)
        {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, "Account disabled.");
        }

        if($identity->getPassword() != $this->password)
        {
            return new Result( Result::FAILURE_CREDENTIAL_INVALID, null, "Wrong password.");
        }

        return new Result(Result::SUCCESS, $identity, "Login Successful.");
    }
}