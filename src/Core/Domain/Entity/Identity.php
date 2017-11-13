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
use Zend\Hydrator\ClassMethods;


class Identity extends \ArrayObject
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
    protected $account;
    protected $state;
    protected $password;
    protected $adapterClassName;

    const TABLE_NAME = 'UserIdentity';
    const ID_NAME = 'id';
    const COLUMN_USER_NAME = 'account';
    const COLUMN_USER_PASSWORD = 'password';
    const COLUMN_STATE = 'state';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param string $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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

    public function exchangeArray($data)
    {
        $hydrator = new ClassMethods(false);
        $hydrator->hydrate($data,$this);
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            self::ID_NAME => $this->getId(),
            self::COLUMN_STATE => $this->getState(),
            self::COLUMN_USER_NAME => $this->getAccount(),
            self::COLUMN_USER_PASSWORD => $this->getPassword()
        ];
    }
}