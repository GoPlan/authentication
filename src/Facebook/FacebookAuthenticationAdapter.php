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

namespace CreativeDelta\User\Facebook;


use CreativeDelta\User\Core\Domain\AuthenticationAdapterInterface;
use CreativeDelta\User\Core\Domain\Entity\Identity;
use Zend\Authentication\Result;

class FacebookAuthenticationAdapter implements AuthenticationAdapterInterface
{

    /** @var  Identity $identity */
    protected $identity;

    /**
     * FacebookAuthenticationAdapter constructor.
     * @param Identity|null $identity
     */
    public function __construct(Identity $identity = null)
    {
        $this->identity = $identity;
    }

    /**
     * @return Result
     */
    public function authenticate()
    {

        if (!$this->identity) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null);
        }

        if (!($this->identity->getState() == Identity::STATE_ACTIVE)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['USER_NOT_ACTIVE']);
        }

        return new Result(Result::SUCCESS, $this->identity);
    }

    public function hasExpired()
    {
        // TODO: Implement hasExpired() method.
    }

}