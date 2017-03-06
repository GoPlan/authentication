<?php
/**
 * Created by PhpStorm.
 *
 * combo-outfit (by Duc-Anh LE)
 *
 * User: ducanh-ki
 * Date: 3/4/17
 * Time: 8:36 AM
 */

namespace CreativeDelta\User\Table;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\TableGateway\TableGateway;

class UserFacebookTable
{
    const TABLE_NAME = "UserFacebook";
    const ID_NAME    = "id";

    protected $tableGateway;
    protected $dbAdapter;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $dbAdapter);
    }

    public function has($facebookId)
    {
        return $this->tableGateway->select(['facebookId' => $facebookId])->count() > 0;
    }

    public function create($username, $facebookId, $profile)
    {
        $account                = new RowGateway(UserFacebookTable::ID_NAME, UserFacebookTable::TABLE_NAME, $this->dbAdapter);
        $account['username']    = $username;
        $account['facebookId']  = $facebookId;
        $account['profileJson'] = $profile;
        $account->save();

        return $account;
    }
}