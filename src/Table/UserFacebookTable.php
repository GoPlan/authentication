<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/4/17
 * Time: 8:36 AM
 */

namespace CreativeDelta\User\Table;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
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

    public function has($userId)
    {
        return $this->tableGateway->select(['userId' => $userId])->count() > 0;
    }

    public function get($userId)
    {
        return $this->tableGateway->select(['userId' => $userId])->current();
    }

    public function create($identity, $userId, $profile)
    {
        $account                = new RowGateway(UserFacebookTable::ID_NAME, UserFacebookTable::TABLE_NAME, $this->dbAdapter);
        $account['identity']    = $identity;
        $account['userId']      = $userId;
        $account['profileJson'] = $profile;
        $account->save();

        return $account;
    }
}