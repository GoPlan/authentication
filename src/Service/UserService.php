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

namespace CreativeDelta\User\Service;


use CreativeDelta\User\Exception\UserException;
use CreativeDelta\User\Model\Identity;
use CreativeDelta\User\Table\UserSignInLogTable;
use CreativeDelta\User\Table\UserIdentityTable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserService implements UserServiceInterface
{
    protected $userIdentityTable;
    protected $userSignInLogTable;

    protected $strategy;
    protected $dbAdapter;

    // TODO To refactor UserService to use Strategy pattern for Email, Facebook, or G+ service.

    function __construct(AdapterInterface $dbAdapter, UserServiceStrategyInterface $strategy)
    {
        $this->dbAdapter = $dbAdapter;
        $this->strategy  = $strategy;

        $this->userIdentityTable  = new UserIdentityTable($dbAdapter);
        $this->userSignInLogTable = new UserSignInLogTable($dbAdapter);
    }

    /**
     * @param $data
     * @return string
     */
    public function createSignInLogHash($data)
    {
        $signInLog = $this->userSignInLogTable->createSignInLog($data);
        $hash      = $signInLog['hash'];
        return $hash;
    }

    /**
     * @param $hash
     * @return array|\ArrayObject|null
     */
    public function getSignInLog($hash)
    {
        return $this->userSignInLogTable->getByHash($hash);
    }

    /**
     * @param $identity
     * @return bool
     */
    public function hasIdentity($identity)
    {
        return $this->userIdentityTable->has($identity);
    }

    /**
     * @param $userId
     * @return Identity|null
     */
    public function getIdentityByUserId($userId)
    {
        $recordRow   = $this->getRecord($userId);
        $identityRow = $this->userIdentityTable->get($recordRow['identity']);

        /** @var Identity $identity */
        $identity = $identityRow ? (new ClassMethods())->hydrate($identityRow->getArrayCopy(), new Identity()) : null;

        return $identity;
    }

    /**
     * @param $userId
     * @return bool
     */
    public function hasRecord($userId)
    {
        return $this->strategy->has($userId);
    }

    /**
     * @param $userId
     * @return array|\ArrayObject|null
     */
    public function getRecord($userId)
    {
        return $this->strategy->get($userId);
    }

    /**
     * @param string $identity
     * @param int $userId
     * @param array $profile
     * @throws UserException
     */
    public function register($identity, $userId, $profile)
    {
        if ($this->hasIdentity($identity) || $this->hasRecord($userId)) {
            throw new UserException(UserException::CODE_ACCOUNT_EXIST_ERROR);
        }

        try {

            $identity = $this->userIdentityTable->create($identity);
            $this->strategy->register($identity, $userId, $profile);

        } catch (\Exception $exception) {
            throw new UserException(UserException::CODE_DATABASE_INSERT_ERROR, $exception);
        }
    }
}