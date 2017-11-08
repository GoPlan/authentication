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
use Zend\Db\Adapter\Adapter;
use Zend\Hydrator\ClassMethods;

class UserIdentityService implements UserIdentityServiceInterface
{
    const AUTHENTICATION_SERVICE_NAME = \Zend\Authentication\AuthenticationService::class;

    protected $bcrypt;
    protected $dbAdapter;
    protected $userIdentityTable;

    function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter         = $dbAdapter;
        $this->bcrypt            = new Bcrypt();
        $this->userIdentityTable = new UserIdentityTable($dbAdapter);
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
     * @param null                      $password
     * @param int                       $userId
     * @param null                      $data
     * @return int Newly created IdentityId
     * @throws UserIdentityException
     */
    public function register(UserRegisterMethodAdapter $adapter, $account, $password = null, $userId = null, $data = null)
    {
        if ($this->hasAccount($account) || $adapter->has($userId))
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST);

        $dbConnection = $this->dbAdapter->getDriver()->getConnection();
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
}