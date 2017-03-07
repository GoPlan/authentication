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
use CreativeDelta\User\Model\User;
use CreativeDelta\User\Table\UserEmailTable;
use CreativeDelta\User\Table\UserFacebookTable;
use CreativeDelta\User\Table\UserTable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserService implements UserServiceInterface
{

    protected $userTable;
    protected $userFacebookTable;
    protected $userEmailTable;

    protected $dbAdapter;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter         = $dbAdapter;
        $this->userTable         = new UserTable($dbAdapter);
        $this->userEmailTable    = new UserEmailTable($dbAdapter);
        $this->userFacebookTable = new UserFacebookTable($dbAdapter);
    }

    /**
     * @param $username
     * @return bool
     */
    public function hasUsername($username)
    {
        return $this->userTable->has($username);
    }

    /**
     * @param $facebookId
     * @return User|RowGateway|null
     */
    public function getUserByFacebookId($facebookId)
    {
        $facebookRecord = $this->userFacebookTable->get($facebookId);
        $userRecord     = $this->userTable->get($facebookRecord['username']);

        /** @var User $user */
        $user = $facebookRecord && $userRecord ?
            (new ClassMethods())->hydrate($userRecord->getArrayCopy(), new User()) : null;

        return $user;
    }

    /**
     * @param $facebookId
     * @return bool
     */
    public function hasFacebookRecord($facebookId)
    {
        return $this->userFacebookTable->has($facebookId);
    }

    public function getFacebookRecord($facebookId)
    {
        return $this->userFacebookTable->get($facebookId);
    }

    /**
     * @param $username
     * @param int $facebookId
     * @param $profile
     * @throws UserException
     */
    public function registerFacebook($username, $facebookId, $profile)
    {
        if ($this->hasUsername($username) || $this->hasFacebookRecord($facebookId)) {
            throw new UserException(UserException::CODE_ACCOUNT_EXIST_ERROR);
        }

        try {

            $user     = $this->userTable->create($username);
            $facebook = $this->userFacebookTable->create($username, $facebookId, $profile);

            $user['primaryTable'] = UserFacebookTable::TABLE_NAME;
            $user['primaryId']    = $facebook[UserFacebookTable::ID_NAME];
            $user['state']        = User::STATE_ACTIVE;
            $user->save();

        } catch (\Exception $exception) {
            throw new UserException(UserException::CODE_DATABASE_INSERT_ERROR, $exception);
        }
    }
}