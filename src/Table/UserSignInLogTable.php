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

namespace CreativeDelta\User\Table;


use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\TableGateway\TableGateway;

class UserSignInLogTable
{
    const TABLE_NAME = "UserSignInLog";
    const ID_NAME    = "id";

    const SECURITY_STRING_LENGTH = 18;

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
        $row['datetime'] = $datetime->format(\DateTime::RFC3339);
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
        return bin2hex(openssl_random_pseudo_bytes(self::SECURITY_STRING_LENGTH));
    }
}