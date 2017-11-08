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

namespace CreativeDelta\User\Core\Impl\Service;


use CreativeDelta\User\Core\Domain\Entity\SessionLog;
use CreativeDelta\User\Core\Impl\Table\UserSessionLogTable;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Hydrator\ClassMethods;

class UserSessionService
{

    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    const SESSION_DATA_NAME = "dataJson";
    const RANDOM_STRING_LEN = 22;
    const QUERY_SESSION_NAME = "session";
    const QUERY_RETURN_URL_NAME = "return";

    protected $dbAdapter;
    protected $userSessionTable;
    protected $bcrypt;

    const PREVIOUS_HASH = 'previousHash';
    const RETURN_URL = 'returnUrl';
    const DATA_JSON = 'dataJson';

    /**
     * UserSessionService constructor.
     * @param AdapterInterface $dbAdapter
     */
    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->bcrypt = new Bcrypt();
        $this->dbAdapter = $dbAdapter;
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
        $row = new RowGateway(UserSessionLogTable::ID_NAME, UserSessionLogTable::TABLE_NAME, $this->dbAdapter);

        $datetime = new \DateTime();
        $random = bin2hex(openssl_random_pseudo_bytes(self::RANDOM_STRING_LEN));
        $sequence = $datetime->format(\DateTime::RFC3339) . '+' . $random;
        $hash = $this->bcrypt->create($sequence);

        $rowData = [
            'id' => 0,
            'random' => $random,
            'hash' => $hash
        ];

        if ($previousHash)
            $rowData[self::PREVIOUS_HASH] = $previousHash;

        if ($returnUrl)
            $rowData[self::RETURN_URL] = $returnUrl;

        if ($data)
            $rowData[self::DATA_JSON] = json_encode($data);

        $row->populate($rowData, false);
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