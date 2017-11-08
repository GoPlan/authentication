<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 13:49
 */

namespace CreativeDelta\User\Account;


use CreativeDelta\User\Core\Domain\AccountServiceInterface;
use CreativeDelta\User\Core\Impl\Service\AccountService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use CreativeDelta\User\Core\Impl\Service\AuthenticationService;

abstract class AbstractAccountController extends AbstractActionController
{
    /**
     * @var AuthenticationService;
     */
    protected $authService;
    /** @var AccountServiceInterface $AccountService */
    protected $AccountService;

    /**
     * IndexController constructor.
     * @param AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService = null,AccountServiceInterface $accountService = null)
    {
        $this->authService = $authService;
        $this->AccountService = $accountService;
    }
}