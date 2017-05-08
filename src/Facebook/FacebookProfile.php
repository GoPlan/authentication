<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/13/17
 * Time: 2:21 PM
 */

namespace CreativeDelta\User\Facebook;


use CreativeDelta\User\Core\Domain\Entity\AbstractOAuthProfile;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\RowGateway\RowGatewayInterface;

class FacebookProfile extends AbstractOAuthProfile implements RowGatewayInterface
{
    /** @var  AdapterInterface $dbAdapter */
    protected $dbAdapter;

    /** @var  RowGateway $rowGateway */
    protected $rowGateway;

    /**
     * ProfileRow constructor.
     * @param AdapterInterface $dbAdapter
     */
    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter  = $dbAdapter;
        $this->rowGateway = new RowGateway(FacebookTable::ID_NAME, FacebookTable::TABLE_NAME, $dbAdapter);
    }

    static function newFromArray(AdapterInterface $dbAdapter, $data, $exist = false)
    {
        $row = new FacebookProfile($dbAdapter);
        $row->populate($data, $exist);
        return $row;
    }

    public function exchangeArray($data)
    {
        $this->rowGateway->exchangeArray($data);
    }

    public function populate($data, $exist = false)
    {
        $this->rowGateway->populate($data, $exist);
    }

    public function save()
    {
        $this->rowGateway->save();
    }

    public function delete()
    {
        $this->rowGateway->delete();
    }

    function getId()
    {
        return $this->rowGateway[FacebookTable::ID_NAME];
    }

    function getUserId()
    {
        return $this->rowGateway[FacebookTable::COLUMN_FACEBOOK_ID];
    }

    function getIdentityId()
    {
        return $this->rowGateway[FacebookTable::COLUMN_IDENTITY_ID];
    }

    function getCode()
    {
        return $this->rowGateway[FacebookTable::COLUMN_CODE];
    }

    function getRefreshToken()
    {
        return $this->rowGateway[FacebookTable::COLUMN_REFRESH_TOKEN];
    }

    function getAccessToken()
    {
        return $this->rowGateway[FacebookTable::COLUMN_ACCESS_TOKEN];
    }

    function setAccessToken($token)
    {
        $this->rowGateway[FacebookTable::COLUMN_ACCESS_TOKEN] = $token;
    }

}