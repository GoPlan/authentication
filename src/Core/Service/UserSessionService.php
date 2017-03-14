<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/14/17
 * Time: 12:02 PM
 */

namespace CreativeDelta\User\Core\Service;


use CreativeDelta\User\Core\Model\SessionLog;
use CreativeDelta\User\Core\Table\UserSessionLogTable;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserSessionService
{
    const DATETIME_FORMAT   = 'Y-m-d H:i:s';
    const SESSION_DATA_NAME = "dataJson";
    const RANDOM_STRING_LEN = 8;

    protected $dbAdapter;
    protected $userSessionTable;
    protected $bcrypt;

    /**
     * UserSessionService constructor.
     * @param AdapterInterface $dbAdapter
     */
    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->bcrypt           = new Bcrypt();
        $this->dbAdapter        = $dbAdapter;
        $this->userSessionTable = new UserSessionLogTable($dbAdapter);
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
        $result = $this->userSessionTable->getByHash($hash);

        if ($result) {
            /** @var SessionLog $sessionLog */
            $sessionLog = (new ClassMethods())->hydrate($result->getArrayCopy(), new SessionLog());
            return $sessionLog;
        } else {
            return null;
        }
    }

}