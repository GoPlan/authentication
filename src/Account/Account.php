<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 15:00
 */

namespace CreativeDelta\User\Account;


use Zend\Hydrator\ClassMethods;

class Account
{
    protected $id;
    protected $identity;
    protected $state;
    protected $password;

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

    const TABLE_NAME = 'UserIdentity';
    const ID_NAME = 'id';
    const COLUMN_USER_NAME = 'identity';
    const COLUMN_USER_PASSWORD = 'password';
    const COLUMN_STATE = 'state';

    public function exchangeArray($data)
    {
        $hydrator = new ClassMethods(false);
        $hydrator->hydrate($data,$this);
    }

    public function getArrayCopy()
    {
        $hydrator = new ClassMethods(false);
        return $hydrator->extract($this);
    }
}