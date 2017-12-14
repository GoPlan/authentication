<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 13:01
 */

namespace CreativeDelta\User\Application\Controller;

use CreativeDelta\User\Account\AbstractAccountController;

class AccountController extends AbstractAccountController
{
    const ROUTE_NAME = "account";

    function returnResponseLoginSuccess()
    {
        return $this->redirect()->toRoute('application', ['action' => 'index']);
    }

    function returnResponseRegisterSuccess()
    {
        return $this->redirect()->toRoute('application', ['action' => 'index']);
    }

    function returnResponseAccessDeniedProfileAction()
    {
        return $this->redirect()->toRoute('account', ['action' => 'signin']);
    }


}