<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 11/7/17
 * Time: 4:18 PM
 */

namespace CreativeDelta\User\Core\Impl\Session;


class DbTableGatewayOptions extends \Zend\Session\SaveHandler\DbTableGatewayOptions
{
    protected $dataColumn = '"data"';
}