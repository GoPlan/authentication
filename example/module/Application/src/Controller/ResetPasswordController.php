<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 13/11/2017
 * Time: 15:22
 */

namespace CreativeDelta\User\Application\Controller;

use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Impl\Service\UserIdentityService;
use Zend\Console\Request as ConsoleRequest;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;


class ResetPasswordController extends AbstractActionController
{
    protected $dbAdapter;
    protected $userIdentityService;

    public function __construct(Adapter $dbAdapter, UserIdentityServiceInterface $userIdentityService)
    {
        $this->dbAdapter      = $dbAdapter;
        $this->userIdentityService = $userIdentityService;
    }

    public function indexAction()
    {
        return [];
    }

    public function resetpasswordAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest) {
            return 'this function only call at command line.';
        }

        $account        = $request->getParam('account');
        $newPass        = $request->getParam('newPass');
        $confirmNewPass = $request->getParam('confirmNewPass');

        switch ($this->userIdentityService->setAccountPassword($account, $newPass, $confirmNewPass)) {
            case UserIdentityService::ACCOUNT_RESET_SUCCESS:
                return "Success!!!\r\nNew password: $newPass \r\nConfirm new password: $confirmNewPass \r\n";
            default:
                return "Can not set this value, try again.\r\n";
        }
    }
}