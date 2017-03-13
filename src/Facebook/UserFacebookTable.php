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

class UserFacebookTable
{
    const TABLE_NAME           = "UserFacebook";
    const ID_NAME              = "id";
    const REF_USER_ID_NAME     = "userId";
    const REF_IDENTITY_ID_NAME = "identityId";

    protected $tableGateway;
    protected $dbAdapter;

    function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $this->tableGateway = new TableGateway(self::TABLE_NAME, $dbAdapter);
    }

    /**
     * @param $userId
     * @return bool
     */
    public function has($userId)
    {
        return $this->tableGateway->select(['userId' => $userId])->count() > 0;
    }

    /**
     * @param $userId
     * @return array|\ArrayObject|null
     */
    public function get($userId)
    {
        return $this->tableGateway->select(['userId' => $userId])->current();
    }
}