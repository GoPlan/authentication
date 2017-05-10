<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 6:11 PM
 */

namespace CreativeDelta\User\Google;


use CreativeDelta\User\Core\Domain\Entity\AbstractOAuthProfile;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\RowGateway\RowGatewayInterface;

class GoogleProfile extends AbstractOAuthProfile implements RowGatewayInterface
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
        $this->rowGateway = new RowGateway(GoogleTable::ID_NAME, GoogleTable::TABLE_NAME, $dbAdapter);
    }

    static function newFromArray(AdapterInterface $dbAdapter, $data, $exist = false)
    {
        $row = new GoogleProfile($dbAdapter);
        $row->populate($data, $exist);
        return $row;
    }

    function getId()
    {
        return $this->rowGateway[GoogleTable::ID_NAME];
    }

    function getUserId()
    {
        return $this->rowGateway[GoogleTable::COLUMN_GOOGLE_ID];
    }

    function getIdentityId()
    {
        return $this->rowGateway[GoogleTable::COLUMN_IDENTITY_ID];
    }

    function getAccessToken()
    {
        return $this->rowGateway[GoogleTable::COLUMN_ACCESS_TOKEN];
    }

    function setAccessToken($token)
    {
        $this->rowGateway[GoogleTable::COLUMN_ACCESS_TOKEN] = $token;
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

}