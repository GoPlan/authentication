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

class UserIdentityService implements UserIdentityServiceInterface
{
    const QUERY_RETURN_URL_NAME = "return";
    const QUERY_SESSION_NAME    = "session";
    const DATETIME_FORMAT       = 'Y-m-d H:i:s';
    const SESSION_DATA_NAME     = "dataJson";
    const RANDOM_STRING_LEN     = 8;

    protected $bcrypt;
    protected $dbAdapter;
    protected $userIdentityTable;
    protected $userSignInLogTable;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->bcrypt             = new Bcrypt();
        $this->dbAdapter          = $dbAdapter;
        $this->userIdentityTable  = new UserIdentityTable($dbAdapter);
        $this->userSignInLogTable = new UserSessionLogTable($dbAdapter);
    }

    /**
     * @param $previousHash
     * @param $returnUrl
     * @param $data
     * @return string
     */
    public function createSessionLog($previousHash = null, $returnUrl = null, $data = null)
    {
        $datetime = new \DateTime();
        $random   = bin2hex(openssl_random_pseudo_bytes(self::RANDOM_STRING_LEN));
        $salt     = bin2hex(openssl_random_pseudo_bytes(self::RANDOM_STRING_LEN));

        $this->bcrypt->setSalt($salt);

        $sequence = $datetime->format(\DateTime::RFC3339) . '+' . $random;
        $hash     = $this->bcrypt->create($sequence);

        $row             = new RowGateway(UserSessionLogTable::ID_NAME, UserSessionLogTable::TABLE_NAME, $this->dbAdapter);
        $row['datetime'] = $datetime->format(self::DATETIME_FORMAT);
        $row['random']   = $random;
        $row['salt']     = $salt;
        $row['hash']     = $hash;

        if ($previousHash)
            $row['previousHash'] = $previousHash;

        if ($returnUrl)
            $row['returnUrl'] = $returnUrl;

        if ($data)
            $row['dataJson'] = json_encode($data);

        $row->save();

        return $row['hash'];
    }

    /**
     * @param $hash
     * @return SessionLog|null
     */
    public function getSessionLog($hash)
    {
        $result = $this->userSignInLogTable->getByHash($hash);

        if ($result) {
            $sessionLog = (new ClassMethods())->hydrate($result->getArrayCopy(), new SessionLog());
            return $sessionLog;
        } else {
            return null;
        }
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
     * @param array $dataJson
     * @return int
     * @throws UserIdentityException
     */
    public function register(UserRegisterMethodAdapter $adapter, $identity, $userId, $dataJson)
    {
        if ($this->hasIdentity($identity) || $adapter->has($userId)) {
            throw new UserIdentityException(UserIdentityException::CODE_ACCOUNT_EXIST_ERROR);
        }

        try {

            $identityObj             = new RowGateway(UserIdentityTable::ID_NAME, UserIdentityTable::TABLE_NAME, $this->dbAdapter);
            $identityObj['identity'] = $identity;
            $identityObj->save();

            $identityId  = $identityObj[UserIdentityTable::ID_NAME];
            $methodRowId = $adapter->register($identityId, $userId, $dataJson);

            $identityObj['state']        = Identity::STATE_ACTIVE;
            $identityObj['primaryTable'] = $adapter->getTableName();
            $identityObj['primaryId']    = $methodRowId;
            $identityObj->save();

            return $identityObj[UserIdentityTable::ID_NAME];

        } catch (\Exception $exception) {
            throw new UserIdentityException(UserIdentityException::CODE_DATABASE_INSERT_ERROR, $exception);
        }
    }
}