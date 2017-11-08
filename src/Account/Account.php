<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 15:00
 */

namespace CreativeDelta\User\Account;


class Account
{
    public $id;
    public $identity;
    public $state;
    public $primaryTable;
    public $primaryId;
    public $password;

    const TABLE_NAME = 'UserIdentity';
    const ID_NAME = 'id';
    const COLUMN_USER_NAME = 'identity';
    const COLUMN_USER_PASSWORD = 'password';
    const COLUMN_STATE = 'state';
    const COLUMN_PRIMARY_ID = 'primaryId';
    const COLUMN_PRIMARY_TABLE = 'primaryTable';

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->identity = (!empty($data['identity'])) ? $data['identity'] : null;
        $this->state = (!empty($data[state])) ? $data['state'] : null;
        $this->primaryTable = (!empty($data['primaryTable'])) ? $data['primaryTable'] : null;
        $this->primaryId = (!empty($data['primaryId'])) ? $data['primaryId'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
    }

    public function getArrayCopy()
    {
        return [
            self::ID_NAME => $this->id,
            self::COLUMN_USER_NAME => $this->identity,
            self::COLUMN_STATE => $this->state,
            self::COLUMN_PRIMARY_TABLE => $this->primaryTable,
            self::COLUMN_PRIMARY_ID => $this->primaryId,
            self::COLUMN_USER_PASSWORD => $this->password
        ];
    }
}