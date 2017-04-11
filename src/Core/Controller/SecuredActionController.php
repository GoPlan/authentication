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
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class SecuredActionController extends AbstractActionController
{
    /** @var  AdapterInterface $dbAdapter */
    protected $dbAdapter;

    /** @var  AuthenticationService $authService */
    protected $authService;

    /**
     * ItemController constructor.
     * @param AdapterInterface $dbAdapter
     */
    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function getAuthenticationService()
    {
        if (!$this->authService) {
            $this->authService = new AuthenticationService();
        }

        return $this->authService;
    }
}