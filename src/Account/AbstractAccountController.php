<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 13:49
 */

namespace CreativeDelta\User\Account;


use CreativeDelta\User\Application\Form\ProfileForm;
use CreativeDelta\User\Application\Form\RegisterForm;
use CreativeDelta\User\Application\Form\SignInForm;
use CreativeDelta\User\Core\Domain\Entity\Identity;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Impl\Service\UserIdentityService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Db\Adapter\Adapter;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractAccountController extends AbstractActionController
{
    /**
     * @var AuthenticationService;
     */
    protected $authService;

    /**
     * @var UserIdentityServiceInterface
     */
    protected $userIdentityService;

    /**
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * @var AccountMethod
     */
    protected $AccountMethod;

    /**
     * IndexController constructor.
     * @param Adapter                           $dbAdapter
     * @param AuthenticationService             $authService
     * @param UserIdentityServiceInterface|null $userIdentityService
     */
    public function __construct(Adapter $dbAdapter, AuthenticationService $authService = null, UserIdentityServiceInterface $userIdentityService = null)
    {
        $this->dbAdapter      = $dbAdapter;
        $this->authService    = $authService;
        $this->userIdentityService = $userIdentityService;
    }

    public function indexAction()
    {
        return [];
    }

    public function signinAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $mForm   = new SignInForm();
        $mForm->get('submit')->setValue('Sign In');

        $mAuthService = new AuthenticationService();

        if ($request->isPost()) {

            if ($mAuthService->hasIdentity())
                $mAuthService->clearIdentity();

            $mForm->setData($request->getPost());

            if ($mForm->isValid()) {
                $mUsername = $mForm->get('txtUsername')->getValue();
                $mPassword = $mForm->get('txtPassword')->getValue();

                $mAuthAdapter = new AccountAuthenticationAdapter($this->userIdentityService, $mUsername, $mPassword);
                $mResult      = $mAuthService->authenticate($mAuthAdapter);
                if ($mResult->getCode() == Result::SUCCESS) {
                    return $this->redirect()->toRoute('application', ['action' => 'index']);
                } else {
                    $mForm->get('ResultMessages')->setValue($mResult->getMessages()[0]);
                }
            }
        }

        return ['form' => $mForm];
    }

    public function registerAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $mForm   = new RegisterForm();
        $mForm->get('submit')->setValue('Sign In');

        $mAuthService = new AuthenticationService();

        if ($request->isPost()) {
            if ($mAuthService->hasIdentity())
                $mAuthService->clearIdentity();

            $mForm->setData($request->getPost());

            if ($mForm->isValid()) {

                $mUsername        = $mForm->get('txtUsername')->getValue();
                $mPassword        = $mForm->get('txtPassword')->getValue();
                $mConfirmPassword = $mForm->get('txtConfirmPassword')->getValue();

                $loadidentity = $this->userIdentityService->getIdentityByAccount($mUsername);

                if ($loadidentity == null) {
                    if (($mPassword == $mConfirmPassword) && !empty($mUsername) && !empty($mPassword)) {

                        if ($this->userIdentityService->register($this->getAccountMethod(), $mUsername, $mPassword)) {
                            $mAuthAdapter = new AccountAuthenticationAdapter($this->userIdentityService, $mUsername, $mPassword);
                            $mResult      = $mAuthService->authenticate($mAuthAdapter);
                            if ($mResult->getCode() == Result::SUCCESS) {
                                return $this->redirect()->toRoute('application', ['action' => 'index']);
                            } else {
                                $mForm->get('ResultMessages')->setValue($mResult->getMessages()[0]);
                            }
                        } else {
                            $mForm->get('ResultMessages')->setValue('Register failed.');
                        }
                    }
                } else {
                    $mForm->get('ResultMessages')->setValue('Account already exists.');
                }
            }
        }

        return ['form' => $mForm];
    }

    public function getAccountMethod()
    {
        if ($this->AccountMethod == null) {
            $this->AccountMethod = new AccountMethod($this->dbAdapter);
        }

        return $this->AccountMethod;
    }

    public function profileAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $mForm   = new ProfileForm();
        $mForm->get('submit')->setValue('Update');

        $mAuthService = new AuthenticationService();

        if ($mAuthService->hasIdentity()) {

            /** @var Identity $account */
            $account = $mAuthService->getIdentity();
            $mForm->get('Identity')->setValue($account->getAccount());

            if ($request->isPost()) {
                $mForm->setData($request->getPost());

                if ($mForm->isValid()) {
                    $mCurrentPassword = $mForm->get('txtCurrentPassword')->getValue();
                    $mPassword        = $mForm->get('txtPassword')->getValue();
                    $mConfirmPassword = $mForm->get('txtConfirmPassword')->getValue();

                    switch ($this->userIdentityService->setCurrentIdentityPassword($account, $mCurrentPassword, $mPassword, $mConfirmPassword)) {

                        case UserIdentityService::ACCOUNT_RESET_SUCCESS:
                            $mForm->get('ResultMessages')->setValue('Change password success.');
                            break;
                        case UserIdentityService::ACCOUNT_RESET_NEW_PASSWORD_INVALID:
                            $mForm->get('ResultMessages')->setValue('New pass is invalid.');
                            break;
                        case UserIdentityService::ACCOUNT_RESET_CURRENT_PASSWORD_INVALID:
                            $mForm->get('ResultMessages')->setValue('Current pass is invalid.');
                            break;
                        case UserIdentityService::ACCOUNT_RESET_FAILED:
                            $mForm->get('ResultMessages')->setValue('Can not change password.');
                            break;
                        case UserIdentityService::ACCOUNT_RESET_CURRENT_PASSWORD_IS_INCORRECT:
                            $mForm->get('ResultMessages')->setValue('Current password wrong.');
                            break;
                        case UserIdentityService::ACCOUNT_RESET_PASSWORD_DOES_NOT_MATCH:
                            $mForm->get('ResultMessages')->setValue('Confirm password not match.');
                            break;
                    }
                }

            }

        } else {
            $this->redirect()->toRoute('account', ['action' => 'signin']);
        }

        return ['form' => $mForm];
    }
}