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
use CreativeDelta\User\Core\Impl\Service\AccountService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\Adapter;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;


abstract class AbstractAccountController extends AbstractActionController
{
    /**
     * @var AuthenticationService;
     */
    protected $authService;
    /** @var UserIdentityServiceInterface $AccountService */
    protected $AccountService;
    protected $dbAdapter;
    protected $AccountMethod;

    /**
     * IndexController constructor.
     * @param AuthenticationService $authService
     */
    public function __construct(Adapter $dbAdapter, AuthenticationService $authService = null, UserIdentityServiceInterface $accountService = null)
    {
        $this->dbAdapter = $dbAdapter;
        $this->authService = $authService;
        $this->AccountService = $accountService;
    }

    public function indexAction()
    {
        return [];
    }

    public function signinAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $mForm = new SignInForm();
        $mForm->get('submit')->setValue('Sign In');

        $mAuthService = new AuthenticationService();


        if ($request->isPost()) {

            if ($mAuthService->hasIdentity())
                $mAuthService->clearIdentity();

            $mForm->setData($request->getPost());

            if ($mForm->isValid()) {
                $mUsername = $mForm->get('txtUsername')->getValue();
                $mPassword = $mForm->get('txtPassword')->getValue();

                $mAuthAdapter = new AccountAuthenticationAdapter($this->AccountService, $mUsername, $mPassword);
                $mResult = $mAuthService->authenticate($mAuthAdapter);
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
        $mForm = new RegisterForm();
        $mForm->get('submit')->setValue('Sign In');

        $mAuthService = new AuthenticationService();

        if ($request->isPost()) {
            if ($mAuthService->hasIdentity())
                $mAuthService->clearIdentity();

            $mForm->setData($request->getPost());

            if ($mForm->isValid()) {
                $mUsername = $mForm->get('txtUsername')->getValue();
                $mPassword = $mForm->get('txtPassword')->getValue();
                $mConfirmPassword = $mForm->get('txtConfirmPassword')->getValue();

                $loadidentity = $this->AccountService->getIdentityByAccount($mUsername);
                if ($loadidentity == null) {
                    if (($mPassword == $mConfirmPassword) && !empty($mUsername) && !empty($mPassword)) {

                        $bcrypt = new Bcrypt();
                        $encryptPass = $bcrypt->create($mPassword);

                        if ($this->AccountService->register($this->getAccountMethod(), $mUsername, $encryptPass)) {
                            $mAuthAdapter = new AccountAuthenticationAdapter($this->AccountService, $mUsername, $mPassword);
                            $mResult = $mAuthService->authenticate($mAuthAdapter);
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
        $mForm = new ProfileForm();
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
                    $mPassword = $mForm->get('txtPassword')->getValue();
                    $mConfirmPassword = $mForm->get('txtConfirmPassword')->getValue();

                    switch ($this->AccountService->setCurrentPasswordByAccount($account, $mCurrentPassword, $mPassword, $mConfirmPassword)) {

                        case AccountService::ACCOUNT_RESET_SUCCESS:
                            $mForm->get('ResultMessages')->setValue('Change password success.');
                            break;
                        case AccountService::ACCOUNT_RESET_NEW_PASSWORD_INVALID:
                            $mForm->get('ResultMessages')->setValue('New pass is invalid.');
                            break;
                        case AccountService::ACCOUNT_RESET_CURRENT_PASSWORD_INVALID:
                            $mForm->get('ResultMessages')->setValue('Current pass is invalid.');
                            break;
                        case AccountService::ACCOUNT_RESET_FAILED:
                            $mForm->get('ResultMessages')->setValue('Can not change password.');
                            break;
                        case AccountService::ACCOUNT_RESET_CURRENT_PASSWORD_IS_INCORRECT:
                            $mForm->get('ResultMessages')->setValue('Current password wrong.');
                            break;
                        case AccountService::ACCOUNT_RESET_PASSWORD_DOES_NOT_MATCH:
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