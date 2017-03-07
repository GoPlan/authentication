<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/3/17
 * Time: 3:32 PM
 */

namespace CreativeDelta\User\Table;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\TableGateway\TableGateway;

class UserIdentityTable
{
    const TABLE_NAME = "UserIdentity";
    const ID_NAME    = "id";

    protected $tableGateway;
    protected $dbAdapter;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $dbAdapter);
    }

    public function get($identity)
    {
        return $this->tableGateway->select(['identity' => $identity])->current();
    }

    public function has($identity)
    {
        return $this->tableGateway->select(['identity' => $identity])->count() > 0;
    }

    public function create($identity)
    {
        $user             = new RowGateway(self::ID_NAME, self::TABLE_NAME, $this->dbAdapter);
        $user['identity'] = $identity;
        $user->save();
        return $user;
    }
}