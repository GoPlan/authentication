<?php
/**
 * Created by PhpStorm.
 *
 * combo-outfit (by Duc-Anh LE)
 *
 * User: ducanh-ki
 * Date: 3/3/17
 * Time: 3:32 PM
 */

namespace CreativeDelta\User\Table;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\TableGateway\TableGateway;

class UserTable
{
    const TABLE_NAME = "User";
    const ID_NAME    = "id";

    protected $tableGateway;
    protected $dbAdapter;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $dbAdapter);
    }

    public function get($username)
    {
        return $this->tableGateway->select(['username' => $username])->current();
    }

    public function has($username)
    {
        return !($this->get($username) == null);
    }

    public function create($username)
    {
        $user             = new RowGateway(self::ID_NAME, self::TABLE_NAME, $this->dbAdapter);
        $user['username'] = $username;
        return $user->save();
    }
}