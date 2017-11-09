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
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

abstract class AbstractSecuredActionController extends AbstractActionController
{
    /**
     * @var  AuthenticationServiceInterface
     */
    protected $authenticationService;

    /**
     * @return AuthenticationServiceInterface
     */
    public function getAuthenticationService()
    {
        if (!$this->authenticationService) {
            $this->authenticationService = new AuthenticationService();
        }

        return $this->authenticationService;
    }

    /**
     * @param AuthenticationServiceInterface $authenticationService
     */
    public function setAuthenticationService($authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function onDispatch(MvcEvent $e)
    {
        return $this->getAuthenticationService()->hasIdentity() ? parent::onDispatch($e) : $this->noIdentityDispatch($e);
    }

    abstract function noIdentityDispatch(MvcEvent $e);
}