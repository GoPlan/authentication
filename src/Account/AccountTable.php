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

    const TABLE_NAME = 'UserIdentity';
    const ID_NAME = 'id';
    const COLUMN_USER_NAME = 'account';
    const COLUMN_USER_PASSWORD = 'password';
    const COLUMN_STATE = 'state';

    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
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

    public function saveAccount(Identity $account)
    {
        $data = $account->getArrayCopy();

        $id = $account->getId();
        if ($id == 0) {
            unset($data[self::ID_NAME]);
            $this->accountTableGateway->insert($data);
            return true;
        } else {
            if($this->getAccount($id)){
                $this->accountTableGateway->update($data, [self::ID_NAME => $id]);
                return true;
            }
            else {
                return false;
            }
        }
    }

    public function getAccount($id)
    {
        $id = (int)$id;
        $rowset = $this->accountTableGateway->select([
                self::ID_NAME => $id
            ]
        );
        return $rowset->current();
    }

    public function getAccountByIdentity($identity)
    {
        $rowset = $this->accountTableGateway->select(
            [self::COLUMN_USER_NAME => $identity]
        );
        return $rowset->current();
    }

    public function hasAccount($identity)
    {
        $rowset = $this->accountTableGateway->select(
        [
            self::COLUMN_USER_NAME => $identity
        ]
        );
        return $rowset->count() != 0 ? true : false;
    }

    public function hasAccountId($id)
    {
        $id = (int)$id;
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