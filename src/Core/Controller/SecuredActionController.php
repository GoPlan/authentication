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


use CreativeDelta\User\Core\Service\UserIdentityService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;

class SecuredActionController extends AbstractActionController
{

    public function getAuthenticationService()
    {
        /** @var AuthenticationService $authService */
        $authService = $this->getServiceLocator()->get(UserIdentityService::AUTHENTICATION_SERVICE_NAME);
        return $authService;
    }
}