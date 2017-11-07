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
    const TABLE_NAME = "user_identity";
    const ID_NAME    = "id";

    const COLUMN_STATE         = "state";
    const COLUMN_IDENTITY      = "identity";
    const COLUMN_PRIMARY_TABLE = "primary_table";
    const COLUMN_PRIMARY_ID    = "primary_id";

    protected $tableGateway;
    protected $dbAdapter;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
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
     * @param $identity
     * @return array|\ArrayObject|null
     */
    public function getByIdentity($identity)
    {
        $result = $this->tableGateway->select([self::COLUMN_IDENTITY => $identity])->current();
        return $result;
    }

    /**
     * @param $identity
     * @return bool
     */
    public function hasIdentity($identity)
    {
        return $this->tableGateway->select([self::COLUMN_IDENTITY => $identity])->count() > 0;
    }

}