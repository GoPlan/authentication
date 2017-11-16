<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/4/17
 * Time: 8:47 AM
 */

namespace CreativeDelta\User\Core\Impl\Service;


use CreativeDelta\User\Core\Domain\Entity\Identity;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Domain\UserRegisterMethodAdapter;
use CreativeDelta\User\Core\Impl\Exception\UserIdentityException;
use CreativeDelta\User\Core\Impl\Row\IdentityRow;
use CreativeDelta\User\Core\Impl\Table\UserIdentityTable;
use Zend\Crypt\Password\Bcrypt;
use Zend\Hydrator\ClassMethods;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;

class UserIdentityService implements UserIdentityServiceInterface
{

    const ACCOUNT_RESET_SUCCESS = 1;
    const ACCOUNT_RESET_CURRENT_PASSWORD_IS_INCORRECT = -1;
    const ACCOUNT_RESET_PASSWORD_DOES_NOT_MATCH = -2;
    const ACCOUNT_RESET_CURRENT_PASSWORD_INVALID = -3;
    const ACCOUNT_RESET_NEW_PASSWORD_INVALID = -4;
    const ACCOUNT_RESET_FAILED = -5;

    /**
     * @var UserIdentityTable
     */
    protected $userIdentityTable;


    function __construct(UserIdentityTable $identityTable)
    {
        $this->userIdentityTable = $identityTable;
    }

    /**
     * @param string $account
     * @return bool
     */
    public function hasAccount($account)
    {
        return $this->userIdentityTable->hasAccount($account);
    }

    /**
     * @param string $account
     * @return Identity|null
     */
    public function getIdentityByAccount($account)
    {
        $result = $this->userIdentityTable->getByAccount($account);

        /** @var Identity $account */
        $account = $result ? (new ClassMethods())->hydrate($result->getArrayCopy(), new Identity()) : null;

        return $account;
    }

    /**
     * @param $id
     * @return Identity|null
     */
    public function getIdentityById($id)
    {
        $result = $this->userIdentityTable->get($id);

        /** @var Identity $identity */
        $identity = $result ? (new ClassMethods())->hydrate($result->getArrayCopy(), new Identity()) : null;

        return $identity;
    }

    /**
     * @param UserRegisterMethodAdapter $adapter
     * @param string                    $account
     * @param string|null               $password
     * @param string|null               $userId
     * @param string|null               $data
     * @return int Newly created IdentityId
     * @throws UserIdentityException
     */
    public function register(UserRegisterMethodAdapter $adapter, $account, $password = null, $userId = null, $data = null)
    {
        if ($this->hasAccount($account))
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST);

        $dbAdapter    = $this->userIdentityTable->getTableGateway()->getAdapter();
        $dbConnection = $dbAdapter->getDriver()->getConnection();
        $dbConnection->beginTransaction();

        try {

            $identity = new IdentityRow($this->userIdentityTable);
            $identity->setAutoSequence(UserIdentityTable::AUTO_SEQUENCE);
            $identity->setAccount($account);
            if(!empty($password))
            {
                $bcrypt = new Bcrypt();
                $encryptPassword = $bcrypt->create($password);
                $identity->setPassword($encryptPassword);
            }
            $identity->setState(Identity::STATE_ACTIVE);
            $identity->save();

            $adapter->register($identity->getId(), $userId, $data);
            $dbConnection->commit();

            return $identity->getId();

        } catch (\Exception $exception) {

            $dbConnection->rollback();
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_DATABASE_OPERATION_FAILED, $exception);
        }
    }

    public function attach(UserRegisterMethodAdapter $adapter, $identityId, $userId, $data)
    {
        $dbConnection = $this->userIdentityTable->getTableGateway()->getAdapter()->getDriver()->getConnection();
        $dbConnection->beginTransaction();

        try {

            $adapter->register($identityId, $userId, $data);
            $dbConnection->commit();
            return $identityId;

        } catch (\Exception $exception) {
            $dbConnection->rollback();
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_DATABASE_OPERATION_FAILED, $exception);
        }
    }

    public function setCurrentIdentityPassword(Identity $identity, $currentPass, $newPass, $confirmNewPass)
    {
        $dbConnection = $this->userIdentityTable->getTableGateway()->getAdapter()->getDriver()->getConnection();
        $dbConnection->beginTransaction();

        try
        {
            $bcrypt = new Bcrypt();

            $mValidator = new ValidatorChain();
            $mValidator->attach(new StringLength(['min' => 8, 'max' => 32]));

            if (!$mValidator->isValid($currentPass)) {
                return self::ACCOUNT_RESET_CURRENT_PASSWORD_INVALID;
            }

            if (!$mValidator->isValid($newPass)) {
                return self::ACCOUNT_RESET_NEW_PASSWORD_INVALID;
            }

            if (!$bcrypt->verify($currentPass, $identity->getPassword())) {
                return self::ACCOUNT_RESET_CURRENT_PASSWORD_IS_INCORRECT;
            }

            if ($newPass != $confirmNewPass) {
                return self::ACCOUNT_RESET_PASSWORD_DOES_NOT_MATCH;
            }

            //encrypt new pass
            $identity->setPassword($bcrypt->create($newPass));

            //save new pass
            if ($this->userIdentityTable->saveAccount($identity)) {
                $dbConnection->commit();
                return self::ACCOUNT_RESET_SUCCESS;
            } else {
                return self::ACCOUNT_RESET_FAILED;
            }
        }
        catch (\Exception $exception)
        {
            $dbConnection->rollback();
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_DATABASE_OPERATION_FAILED, $exception);
        }
    }

    public function setAccountPassword($account, $newPass, $confirmNewPass)
    {
        $dbConnection = $this->userIdentityTable->getTableGateway()->getAdapter()->getDriver()->getConnection();
        $dbConnection->beginTransaction();

        try {
            $bcrypt = new Bcrypt();

            $mValidator = new ValidatorChain();
            $mValidator->attach(new StringLength(['min' => 8, 'max' => 32]));

            if (!$mValidator->isValid($newPass)) {
                return self::ACCOUNT_RESET_NEW_PASSWORD_INVALID;
            }
            if ($newPass != $confirmNewPass) {
                return self::ACCOUNT_RESET_PASSWORD_DOES_NOT_MATCH;
            }

            $result = $this->userIdentityTable->getAccountByIdentity($account);

            $rootIdentity = $result ? (new ClassMethods())->hydrate($result->getArrayCopy(), new Identity()) : null;

            if ($rootIdentity == null) {
                $rootIdentity = new Identity();
                $rootIdentity->setAccount($account);
                $rootIdentity->setState(Identity::STATE_ACTIVE);
            }

            $tmp = $bcrypt->create($newPass);
            $rootIdentity->setPassword($tmp);

            if ($this->userIdentityTable->saveAccount($rootIdentity)) {
                $dbConnection->commit();
                return self::ACCOUNT_RESET_SUCCESS;
            } else {
                return self::ACCOUNT_RESET_FAILED;
            }
        }
        catch (\Exception $exception)
        {
            $dbConnection->rollback();
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_DATABASE_OPERATION_FAILED, $exception);
        }
    }
}