<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/7/17
 * Time: 2:52 PM
 */

namespace CreativeDelta\User\Core\Controller;


use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;

class SecuredActionController extends AbstractActionController
{
    const AUTHENTICATION_SERVICE = 'Zend\Authentication\AuthenticationService';

    public function getAuthenticationService()
    {
        /** @var AuthenticationService $authService */
        $authService = $this->getServiceLocator()->get(self::AUTHENTICATION_SERVICE);
        return $authService;
    }
}