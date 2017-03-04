<?php
/**
 * Created by PhpStorm.
 *
 * combo-outfit (by Duc-Anh LE)
 *
 * User: ducanh-ki
 * Date: 3/4/17
 * Time: 8:47 AM
 */

namespace CreativeDelta\User\Service;


use CreativeDelta\User\Exception\UserException;
use CreativeDelta\User\Table\UserEmailTable;
use CreativeDelta\User\Table\UserFacebookTable;
use CreativeDelta\User\Table\UserTable;
use Zend\Db\Adapter\AdapterInterface;

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

    public function hasUsername($username)
    {
        return $this->userTable->has($username);
    }

    public function hasFacebook($facebookId)
    {
        return $this->userFacebookTable->has($facebookId);
    }

    /**
     * @param $username
     * @param int $facebookId
     * @param $profile
     * @throws UserException
     */
    public function registerFacebook($username, $facebookId, $profile)
    {
        if ($this->hasUsername($username) || $this->hasFacebook($facebookId)) {
            throw new UserException(UserException::CODE_ACCOUNT_EXIST_ERROR);
        }

        try {

            $this->userTable->create($username) &&
            $this->userFacebookTable->create($username, $facebookId, $profile);

        } catch (\Exception $exception) {
            throw new UserException(UserException::CODE_DATABASE_INSERT_ERROR, $exception);
        }
    }
}