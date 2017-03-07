<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/7/17
 * Time: 8:54 AM
 */

namespace CreativeDelta\User\Adapter;


use CreativeDelta\User\Model\User;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class FacebookAuthenticationAdapter implements AdapterInterface
{

    /** @var  User $user */
    protected $user;

    /**
     * FacebookAuthenticationAdapter constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return Result
     */
    public function authenticate()
    {

        if (!$this->user) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null);
        }

        if (!($this->user->getState() == User::STATE_ACTIVE)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['User is not active']);
        }

        return new Result(Result::SUCCESS, $this->user->getUsername());
    }
}