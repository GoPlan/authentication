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

namespace CreativeDelta\User\Core\Service;


use CreativeDelta\User\Core\Exception\UserIdentityException;
use CreativeDelta\User\Core\Model\Identity;
use CreativeDelta\User\Core\Model\SessionLog;
use CreativeDelta\User\Core\Table\UserIdentityTable;
use CreativeDelta\User\Core\Table\UserSessionLogTable;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserIdentityService implements UserIdentityServiceInterface, UserSessionServiceInterface
{
    const AUTHENTICATION_SERVICE_NAME = 'Zend\Authentication\AuthenticationService';

    protected $bcrypt;
    protected $dbAdapter;
    protected $userIdentityTable;
    protected $userSignInLogTable;
    protected $userSessionService;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->bcrypt             = new Bcrypt();
        $this->dbAdapter          = $dbAdapter;
        $this->userIdentityTable  = new UserIdentityTable($dbAdapter);
        $this->userSignInLogTable = new UserSessionLogTable($dbAdapter);
        $this->userSessionService = new UserSessionService($this->dbAdapter);
    }

    /**
     * @param $previousHash
     * @param $returnUrl
     * @param $data
     * @return string
     */
    public function createSessionLog($previousHash = null, $returnUrl = null, $data = null)
    {
        return $this->userSessionService->createSessionLog($previousHash, $returnUrl, $data);
    }

    /**
     * @param $hash
     * @return SessionLog|null
     */
    public function getSessionLog($hash)
    {
        return $this->userSessionService->getSessionLog($hash);
    }

    /**
     * @param Identity|string $identity
     * @return bool
     */
    public function hasIdentity($identity)
    {
        return $this->userIdentityTable->hasIdentity($identity);
    }

    /**
     * @param string $identity
     * @return null|Identity
     */
    public function getIdentityByIdentity($identity)
    {
        $result = $this->userIdentityTable->getByIdentity($identity);

        /** @var Identity $identity */
        $identity = $result ? (new ClassMethods())->hydrate($result->getArrayCopy(), new Identity()) : null;

        return $identity;
    }

    /**
     * @param $identityId
     * @return Identity|null
     */
    public function getIdentityById($identityId)
    {
        $result = $this->userIdentityTable->get($identityId);

        /** @var Identity $identity */
        $identity = $result ? (new ClassMethods())->hydrate($result->getArrayCopy(), new Identity()) : null;

        return $identity;
    }

    /**
     * @param UserRegisterMethodAdapter $adapter
     * @param string $identity
     * @param int $userId
     * @param array $data
     * @return int
     * @throws UserIdentityException
     */
    public function register(UserRegisterMethodAdapter $adapter, $identity, $userId, $data)
    {
        if ($this->hasIdentity($identity) || $adapter->has($userId)) {
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST);
        }

        try {

            $identityObj             = new RowGateway(UserIdentityTable::ID_NAME, UserIdentityTable::TABLE_NAME, $this->dbAdapter);
            $identityObj['identity'] = $identity;
            $identityObj->save();

            $identityId  = $identityObj[UserIdentityTable::ID_NAME];
            $methodRowId = $adapter->register($identityId, $userId, $data);

            $identityObj['state']        = Identity::STATE_ACTIVE;
            $identityObj['primaryTable'] = $adapter->getTableName();
            $identityObj['primaryId']    = $methodRowId;
            $identityObj->save();

            return $identityObj[UserIdentityTable::ID_NAME];

        } catch (\Exception $exception) {
            throw new UserIdentityException(UserIdentityException::CODE_ERROR_INSERT_DATABASE_OPERATION_FAILED, $exception);
        }
    }
}