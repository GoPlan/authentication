<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/4/17
 * Time: 9:25 AM
 */

namespace CreativeDelta\User\Table;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class UserEmailTable
{
    const TABLE_NAME = "UserEmailTable";
    const ID_NAME    = "id";

    protected $tableGateway;
    protected $dbAdapter;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $dbAdapter);
    }

    public function has($email)
    {
        return !($this->getEmail($email) == null);
    }

    public function getEmail($email)
    {
        return $this->tableGateway->select(['email' => $email])->current();
    }
}