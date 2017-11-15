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
use Zend\Hydrator\ClassMethods;

class UserIdentityService implements UserIdentityServiceInterface
{
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
        if ($this->hasAccount($account) || $adapter->has($userId))
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST);

        $dbAdapter    = $this->userIdentityTable->getTableGateway()->getAdapter();
        $dbConnection = $dbAdapter->getDriver()->getConnection();
        $dbConnection->beginTransaction();

        try {

            $identity = new IdentityRow($this->userIdentityTable);
            $identity->setAutoSequence(UserIdentityTable::AUTO_SEQUENCE);
            $identity->setAccount($account);
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
        // TODO: Implement setCurrentPasswordByAccount() method.
    }

    public function setAccountPassword($account, $newPass, $confirmNewPass)
    {
        // TODO: Implement setRootPassword() method.
    }
}