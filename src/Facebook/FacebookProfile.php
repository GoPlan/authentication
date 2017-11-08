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
use Zend\Db\Adapter\Driver\Pdo\Pdo;
use Zend\Db\RowGateway\RowGatewayInterface;

class FacebookProfile extends AbstractOAuthProfile implements RowGatewayInterface
{
    /**
     * @var FacebookTable
     */
    protected $facebookTable;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var bool
     */
    protected $existInDatabase;

    /**
     * @var string
     */
    protected $autoSequence;

    /**
     * FacebookProfile constructor.
     * @param FacebookTable $facebookTable
     */
    public function __construct(FacebookTable $facebookTable)
    {
        $this->facebookTable   = $facebookTable;
        $this->existInDatabase = false;
    }

    static function newFromArray($facebookTable, $data, $exist = false)
    {
        $row = new FacebookProfile($facebookTable);
        $row->populate($data, $exist);
        return $row;
    }

    /**
     * @return string
     */
    public function getAutoSequence()
    {
        return $this->autoSequence;
    }

    /**
     * @param string $autoSequence
     */
    public function setAutoSequence($autoSequence)
    {
        $this->autoSequence = $autoSequence;
    }

    public function getId()
    {
        return $this->data[FacebookTable::ID_NAME];
    }

    public function setId($id)
    {
        $this->data[FacebookTable::ID_NAME] = $id;
    }

    public function getUserId()
    {
        return $this->data[FacebookTable::COLUMN_FACEBOOK_ID];
    }

    public function setUserId($userId)
    {
        $this->data[FacebookTable::COLUMN_FACEBOOK_ID] = $userId;
    }

    public function getIdentityId()
    {
        return $this->data[FacebookTable::COLUMN_IDENTITY_ID];
    }

    public function setIdentityId($identityId)
    {
        $this->data[FacebookTable::COLUMN_IDENTITY_ID] = $identityId;
    }

    public function getAccessToken()
    {
        return $this->data[FacebookTable::COLUMN_ACCESS_TOKEN];
    }

    public function setAccessToken($token)
    {
        $this->data[FacebookTable::COLUMN_ACCESS_TOKEN] = $token;
    }

    public function exchangeArray($data)
    {
        $this->setId($data[FacebookTable::ID_NAME]);
        $this->setIdentityId($data[FacebookTable::COLUMN_IDENTITY_ID]);
        $this->setUserId($data[FacebookTable::COLUMN_FACEBOOK_ID]);
        $this->setAccessToken($data[FacebookTable::COLUMN_ACCESS_TOKEN]);
    }

    public function populate($data, $exist = false)
    {
        $this->exchangeArray($data);
        $this->existInDatabase = $exist;
    }

    public function save()
    {
        if ($this->existInDatabase) {

            $this->facebookTable->getTableGateway()->update($this->data, [FacebookTable::ID_NAME => $this->getId()]);

        } else {

            $count = $this->facebookTable->getTableGateway()->insert($this->data);

            if ($count > 0) {

                $driver = $this->facebookTable->getTableGateway()->getAdapter()->getDriver();
                $newId  = $driver instanceof Pdo ? $driver->getLastGeneratedValue($this->getAutoSequence()) : $driver->getLastGeneratedValue();

                $this->existInDatabase = true;
                $this->setId($newId);
            }
        }
    }

    public function delete()
    {
        $this->facebookTable->getTableGateway()->delete([
            FacebookTable::ID_NAME => $this->getId()
        ]);
    }

}