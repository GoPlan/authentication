<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/7/17
 * Time: 10:56 AM
 */

/**
 * This UserSignInLog table is to be used with OAuth service which agree to hold a tracing variable for you (such as "state" variable of Facebook).
 * Each time user is about to sign-in, create a record in UserSignInTable to hold your configuration variables, this record is uniquely identifiable by the returned hash.
 * During the sign-in process, user will be redirected to OAuth service page of the service provider, all of your variables will be lost except one or more dedicated variable
 * that OAuth service agreed to pass on such as the "state" in Facebook OAuth. Use this "state" variable to store the hash, once sign-process is finished and user is redirected
 * back to your page, use the hash (stored in "state") to retrieve back your configuration variables (which were setup earlier).
 */

namespace CreativeDelta\User\Table;


use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\TableGateway\TableGateway;

class UserSignInLogTable
{
    const TABLE_NAME = "UserSignInLog";
    const ID_NAME    = "id";

    const DATETIME_FORMAT   = 'Y-m-d H:i:s';
    const RANDOM_STRING_LEN = 8;

    /** @var  TableGateway $tableGateway */
    protected $tableGateway;

    /** @var  AdapterInterface $dbAdapter */
    protected $dbAdapter;

    /** @var  Bcrypt $bcrypt */
    protected $bcrypt;

    /**
     * UserSignInLogTable constructor.
     * @param AdapterInterface $dbAdapter
     */
    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $dbAdapter);
        $this->bcrypt       = new Bcrypt();
    }

    /**
     * @param $hash
     * @return array|\ArrayObject|null
     */
    public function getByHash($hash)
    {
        return $this->tableGateway->select(['hash' => $hash])->current();
    }

    /**
     * @param array $data
     * @return RowGateway
     */
    public function createSignInLog($data)
    {
        $datetime = new \DateTime();
        $random   = $this->random();
        $salt     = $this->random();

        $this->bcrypt->setSalt($salt);

        $sequence = $datetime->format(\DateTime::RFC3339) . '+' . $random;
        $hash     = $this->bcrypt->create($sequence);

        $row             = new RowGateway(self::ID_NAME, self::TABLE_NAME, $this->dbAdapter);
        $row['datetime'] = $datetime->format(self::DATETIME_FORMAT);
        $row['random']   = $random;
        $row['salt']     = $salt;
        $row['hash']     = $hash;
        $row['dataJson'] = json_encode($data);

        $row->save();

        return $row;
    }

    /**
     * @return string
     */
    private function random()
    {
        return bin2hex(openssl_random_pseudo_bytes(self::RANDOM_STRING_LEN));
    }
}