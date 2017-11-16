<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 14:25
 */

namespace CreativeDelta\User\Account;

use CreativeDelta\User\Core\Domain\Entity\Identity;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class AccountTable implements AccountTableGatewayInterface
{
    protected $dbAdapter;
    protected $accountTableGateway;

    const TABLE_NAME           = 'UserIdentity';
    const ID_NAME              = 'id';
    const COLUMN_USER_ACCOUNT  = 'account';
    const COLUMN_USER_PASSWORD = 'password';
    const COLUMN_STATE         = 'state';

    public function __construct($dbAdapter)
    {
        $this->dbAdapter    = $dbAdapter;
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Identity());
        $this->accountTableGateway = new TableGateway(self::TABLE_NAME, $this->dbAdapter, null, $resultSetPrototype);
    }

    /**
     * @return TableGateway
     */
    public function getAccountTableGateway()
    {
        return $this->accountTableGateway;
    }

    /**
     * @return mixed
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    public function saveIdentity(Identity $identity)
    {
        $data = $identity->getArrayCopy();

        $id = $identity->getId();
        if ($id == 0) {
            unset($data[self::ID_NAME]);
            $this->accountTableGateway->insert($data);
            return true;
        } else {
            if ($this->getIdentityById($id)) {
                $this->accountTableGateway->update($data, [self::ID_NAME => $id]);
                return true;
            } else {
                return false;
            }
        }
    }

    public function getIdentityById($id)
    {
        $id     = (int)$id;
        $rowset = $this->accountTableGateway->select([
                self::ID_NAME => $id
            ]
        );
        return $rowset->current();
    }

    public function getIdentityByAccount($account)
    {
        $rowset = $this->accountTableGateway->select(
            [self::COLUMN_USER_ACCOUNT => $account]
        );
        return $rowset->current();
    }

    public function hasAccount($account)
    {
        $rowset = $this->accountTableGateway->select(
            [
                self::COLUMN_USER_ACCOUNT => $account
            ]
        );
        return $rowset->count() != 0 ? true : false;
    }

    public function hasId($id)
    {
        $id     = (int)$id;
        $rowset = $this->accountTableGateway->select(
            [
                self::ID_NAME => $id,
            ]
        );

        return $rowset->count() != 0 ? true : false;
    }

    public function getTableName()
    {
        return self::TABLE_NAME;
    }
}