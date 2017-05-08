<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/3/17
 * Time: 3:31 PM
 */

namespace CreativeDelta\User\Core\Domain\Entity;


use Zend\Authentication\Result;

class Identity
{
    const STATE_NEW      = 0;
    const STATE_ACTIVE   = 1;
    const STATE_DISABLED = 2;

    const CREDENTIAL_RESULT_MESSAGES = [
        Result::SUCCESS                    => "SUCCESS",
        Result::FAILURE                    => "FAILURE",
        Result::FAILURE_IDENTITY_NOT_FOUND => "FAILURE_IDENTITY_NOT_FOUND",
        Result::FAILURE_IDENTITY_AMBIGUOUS => "FAILURE_IDENTITY_AMBIGUOUS",
        Result::FAILURE_CREDENTIAL_INVALID => "FAILURE_CREDENTIAL_INVALID",
        Result::FAILURE_UNCATEGORIZED      => "FAILURE_UNCATEGORIZED"
    ];

    const STATE_MESSAGES = [
        self::STATE_NEW      => "NEW_USER",
        self::STATE_ACTIVE   => "ACTIVE_USER",
        self::STATE_DISABLED => "DISABLED_USER",
    ];

    protected $id;
    protected $identity;
    protected $state;
    protected $profile;
    protected $adapterClassName;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param mixed $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getAdapterClassName()
    {
        return $this->adapterClassName;
    }

    /**
     * @param mixed $adapterClassName
     */
    public function setAdapterClassName($adapterClassName)
    {
        $this->adapterClassName = $adapterClassName;
    }
}