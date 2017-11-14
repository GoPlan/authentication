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

namespace CreativeDelta\User\Facebook;


use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class FacebookTable
{
    const AUTO_SEQUENCE       = "public.user_facebook_id_seq";
    const TABLE_NAME          = "UserFacebook";
    const ID_NAME             = "id";
    const COLUMN_FACEBOOK_ID  = "userId";
    const COLUMN_IDENTITY_ID  = "identityId";
    const COLUMN_ACCESS_TOKEN = "accessToken";


    protected $tableGateway;
    protected $dbAdapter;

    /**
     * @return AdapterInterface
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

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
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $dbAdapter);
    }

    /**
     * @param $userId
     * @return bool
     */
    public function hasUserId($userId)
    {
        return $this->tableGateway->select([self::COLUMN_FACEBOOK_ID => $userId])->count() > 0;
    }

    /**
     * @param $userId
     * @return array|\ArrayObject|null
     */
    public function getByUserId($userId)
    {
        return $this->tableGateway->select([self::COLUMN_FACEBOOK_ID => $userId])->current();
    }

    /**
     * @param $identityId
     * @return array|\ArrayObject|null
     */
    public function getByIdentityId($identityId)
    {
        return $this->tableGateway->select([self::COLUMN_IDENTITY_ID => $identityId])->current();
    }

    /**
     * @param $identityId
     * @return bool
     */
    public function hasIdentityId($identityId)
    {
        return $this->tableGateway->select([self::COLUMN_IDENTITY_ID => $identityId])->count() > 0;
    }
}