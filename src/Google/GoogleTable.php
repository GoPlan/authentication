<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 3:53 PM
 */

namespace CreativeDelta\User\Google;


use Zend\Db\TableGateway\TableGateway;

class GoogleTable
{
    const TABLE_NAME          = "user_google";
    const ID_NAME             = "id";
    const COLUMN_IDENTITY_ID  = "identity_id";
    const COLUMN_GOOGLE_ID    = "user_id";
    const COLUMN_ACCESS_TOKEN = "access_token";


    protected $tableGateway;
    protected $dbAdapter;

    /**
     * @return mixed
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * @return mixed
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * GoogleTable constructor.
     * @param $dbAdapter
     */
    public function __construct($dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $this->dbAdapter);
    }

    public function getByIdentityId($identityId)
    {
        return $this->tableGateway->select([self::COLUMN_IDENTITY_ID => $identityId])->current();
    }

    public function getByUserId($userId)
    {
        return $this->tableGateway->select([self::COLUMN_GOOGLE_ID => $userId])->current();
    }

    public function hasUserId($userId)
    {
        return $this->tableGateway->select([self::COLUMN_GOOGLE_ID => $userId])->count() > 0;
    }

}