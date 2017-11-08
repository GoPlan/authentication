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
use Zend\Db\Adapter\Driver\Pgsql\Pgsql;
use Zend\Db\RowGateway\RowGatewayInterface;

class GoogleProfile extends AbstractOAuthProfile implements RowGatewayInterface
{
    /**
     * @var GoogleTable
     */
    protected $googleTable;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var bool
     */
    protected $rowExistInDatabase;

    /**
     * @var string
     */
    protected $autoSequence;

    /**
     * GoogleProfile constructor.
     * @param GoogleTable $googleTable
     */
    public function __construct(GoogleTable $googleTable)
    {
        $this->googleTable        = $googleTable;
        $this->rowExistInDatabase = false;
    }

    static function newFromArray(GoogleTable $googleTable, $data, $exist = false)
    {
        $row = new GoogleProfile($googleTable);
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
        return $this->data[GoogleTable::ID_NAME];
    }

    public function setId($id)
    {
        $this->data[GoogleTable::ID_NAME] = $id;
    }

    public function getIdentityId()
    {
        return $this->data[GoogleTable::COLUMN_IDENTITY_ID];
    }

    public function setIdentityId($identityId)
    {
        $this->data[GoogleTable::COLUMN_IDENTITY_ID] = $identityId;
    }

    public function getUserId()
    {
        return $this->data[GoogleTable::COLUMN_GOOGLE_ID];
    }

    public function setUserId($userId)
    {
        $this->data[GoogleTable::COLUMN_GOOGLE_ID] = $userId;
    }

    function getAccessToken()
    {
        return $this->data[GoogleTable::COLUMN_ACCESS_TOKEN];
    }

    function setAccessToken($token)
    {
        $this->data[GoogleTable::COLUMN_ACCESS_TOKEN] = $token;
    }

    public function exchangeArray($data)
    {
        $this->setId($data[GoogleTable::ID_NAME]);
        $this->setIdentityId($data[GoogleTable::COLUMN_IDENTITY_ID]);
        $this->setUserId($data[GoogleTable::COLUMN_GOOGLE_ID]);
        $this->setAccessToken($data[GoogleTable::COLUMN_ACCESS_TOKEN]);
    }

    public function populate($data, $exist = false)
    {
        $this->exchangeArray($data);
        $this->rowExistInDatabase = $exist;
    }

    public function save()
    {
        if ($this->rowExistInDatabase) {

            $this->googleTable->getTableGateway()->update($this->data, [
                GoogleTable::ID_NAME => $this->data[GoogleTable::ID_NAME]
            ]);

        } else {

            $count = $this->googleTable->getTableGateway()->insert($this->data);

            if ($count > 0) {

                $driver = $this->googleTable->getDbAdapter()->getDriver();
                $newId  = $driver instanceof Pgsql ? $driver->getLastGeneratedValue($this->getAutoSequence()) : $driver->getLastGeneratedValue();
                $this->setId($newId);
            }
        }
    }

    public function delete()
    {
        $this->googleTable->getTableGateway()->delete([
            GoogleTable::ID_NAME => $this->getId()
        ]);
    }
}