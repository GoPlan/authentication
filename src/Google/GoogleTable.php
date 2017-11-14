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


use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

class GoogleTable
{
    const AUTO_SEQUENCE       = "public.user_google_id_seq";
    const TABLE_NAME          = "UserGoogle";
    const ID_NAME             = "id";
    const COLUMN_IDENTITY_ID  = "identityId";
    const COLUMN_GOOGLE_ID    = "userId";
    const COLUMN_ACCESS_TOKEN = "accessToken";

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * @return TableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * @return Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * GoogleTable constructor.
     * @param Adapter $dbAdapter
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