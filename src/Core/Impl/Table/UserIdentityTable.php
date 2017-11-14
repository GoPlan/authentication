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

namespace CreativeDelta\User\Core\Impl\Table;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class UserIdentityTable
{
    const AUTO_SEQUENCE   = "public.useridentity_id_seq";
    const TABLE_NAME      = "UserIdentity";
    const ID_NAME         = "id";
    const COLUMN_ACCOUNT  = "account";
    const COLUMN_PASSWORD = "password";
    const COLUMN_STATE    = "state";

    protected $tableGateway;
    protected $dbAdapter;
    protected $schema;

    /**
     * @return TableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $this->schema       = $dbAdapter->getDriver()->getConnection()->getCurrentSchema();
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $dbAdapter);
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function get($id)
    {
        return $this->tableGateway->select([self::ID_NAME => $id])->current();
    }

    /**
     * @param $account
     * @return array|\ArrayObject|null
     */
    public function getByAccount($account)
    {
        $result = $this->tableGateway->select([self::COLUMN_ACCOUNT => $account])->current();
        return $result;
    }

    /**
     * @param $account
     * @return bool
     * @internal param $identity
     */
    public function hasAccount($account)
    {
        return $this->tableGateway->select([self::COLUMN_ACCOUNT => $account])->count() > 0;
    }
}

