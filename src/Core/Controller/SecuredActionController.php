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
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class SecuredActionController extends AbstractActionController
{
    /** @var  AdapterInterface $dbAdapter */
    protected $dbAdapter;

    /** @var  AuthenticationServiceInterface $authenticationService */
    protected $authenticationService;

    /**
     * ItemController constructor.
     * @param AdapterInterface $dbAdapter
     */
    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

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
}