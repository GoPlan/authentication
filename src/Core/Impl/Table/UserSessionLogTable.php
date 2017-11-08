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

namespace CreativeDelta\User\Core\Impl\Table;


use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class UserSessionLogTable
{
    const TABLE_NAME = "user_session_log";
    const ID_NAME    = "id";

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


}