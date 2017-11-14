<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/25/17
 * Time: 9:18 AM
 */

namespace CreativeDelta\User\Google;


use CreativeDelta\User\Core\Domain\Entity\Identity;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Impl\Exception\AuthenticationException;
use CreativeDelta\User\Core\Impl\Exception\UserIdentityException;
use CreativeDelta\User\Core\Impl\Service\AuthenticationService;
use CreativeDelta\User\Core\Impl\Service\UserIdentityService;
use CreativeDelta\User\Core\Impl\Service\UserSessionService;
use Exception;
use Zend\Authentication\Result;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

abstract class GoogleAbstractController extends AbstractActionController
{
    const RETURN_URL = 'returnUrl';

    /**
     * @var array
     */
    protected $googleConfig;

    /**
     * @var GoogleMethod
     */
    protected $googleMethod;

    /**
     * @var UserIdentityServiceInterface
     */
    protected $userIdentityService;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * @var Container
     */
    protected $container;

    /**
     * GoogleAbstractController constructor.
     * @param Adapter                      $dbAdapter
     * @param AuthenticationService        $authenticationService
     * @param GoogleMethod                 $googleMethod
     * @param UserIdentityServiceInterface $userIdentityService
     * @internal param array $googleConfig
     */
    public function __construct(
        Adapter $dbAdapter,
        AuthenticationService $authenticationService,
        GoogleMethod $googleMethod = null,
        UserIdentityServiceInterface $userIdentityService = null)
    {
        $this->dbAdapter             = $dbAdapter;
        $this->authenticationService = $authenticationService;
        $this->googleConfig          = $authenticationService->getConfig()[GoogleMethod::METHOD_NAME];
        $this->googleMethod          = $googleMethod;
        $this->userIdentityService   = $userIdentityService;
    }

    /**
     * @return UserIdentityServiceInterface
     */
    public function getUserIdentityService()
    {
        if (!$this->userIdentityService) {
            $this->userIdentityService = new UserIdentityService($this->dbAdapter);
        }

        return $this->userIdentityService;
    }

    /**
     * @return array
     */
    public function getGoogleConfig()
    {
        return $this->googleConfig;
    }

    /**
     * @return GoogleMethod
     */
    public function getGoogleMethod()
    {
        if (!$this->googleMethod) {
            $this->googleMethod = new GoogleMethod($this->dbAdapter, $this->googleConfig);
        }

        return $this->googleMethod;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * @return AdapterInterface
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->container = new Container(self::class);
        }

        return $this->container;
    }

    /**
     * Because you will have a different route configuration for the authentication pages.
     * Implement this method to return the correct url of returning url.
     * This path should be a resolved route string of signInReturnAction().
     * @return string
     */
    abstract function getAuthenticationReturnPath();

    abstract function getAttachAccountReturnPath();

    /**
     * Because you will have a different route configuration for the authentication pages.
     * Implement this method to return the correct url of returning url.
     * This path should be a resolved route string of registerReturnAction().
     * @return string
     */
    abstract function getRegisterReturnPath();

    /**
     * @return Response
     */
    abstract function getReturnResponseForIdentityNotFound();

    /**
     * @return Response
     */
    abstract function getReturnResponseForInvalidCredential();

    /**
     * @return Response
     */
    abstract function getReturnResponseForOtherIssues();

    /**
     * @return Response
     */
    abstract function getReturnResponseForNewUserCreated();

    /**
     * @return Response
     */
    abstract function getReturnResponseForUserAlreadyExisted();

    /**
     * @param int   $identity
     * @param array $googleData
     * @return void
     */
    abstract function createUserInLocalDatabase($identity, array $googleData);

    /**
     * @param int   $googleId
     * @param array $googleData
     * @return string
     */
    abstract function newAccountName($googleId, $googleData);

    public function attachAccountAction()
    {
        /** @var Request $request */
        $request   = $this->getRequest();
        $returnUrl = $request->getQuery(UserSessionService::QUERY_RETURN_URL_NAME);

        if (!$returnUrl)
            throw AuthenticationException::ReturnUrlIsNotProvided();

        $this->getContainer()[self::RETURN_URL] = $returnUrl;

        $oauthUrl = $this->getGoogleMethod()->makeAuthenticationUrl($this->getAttachAccountReturnPath(), null);

        return $this->redirect()->toUrl($oauthUrl);

    }

    public function attachAccountReturnAction()
    {
        $data = $this->params()->fromQuery();
        $code = $data[GoogleMethod::RESULT_QUERY_CODE];

        try {

            $this->getGoogleMethod()->initAccessToken($this->getAttachAccountReturnPath(), $code);
            $oauthData = $this->getGoogleMethod()->getOAuthProfile();
            $oauthId   = $oauthData[GoogleMethod::PROFILE_FIELD_ID];

            $registerAdapter = $this->getGoogleMethod();

            if($this->authenticationService->hasIdentity())
            {
                /** @var Identity $getIdentity */
                $getIdentity = $this->authenticationService->getIdentity();

                $this->getUserIdentityService()->attach($registerAdapter, $getIdentity->getId(), $oauthId, $oauthData);

                $this->createUserInLocalDatabase($getIdentity->getId(), $oauthData);
            }




            return $this->getReturnResponseForNewUserCreated();

        } catch (UserIdentityException $exception) {
            switch ($exception->getCode()) {
                case UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST:
                    return $this->getReturnResponseForUserAlreadyExisted();
                default:
                    throw $exception;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function registerAction()
    {
        /** @var Request $request */
        $request   = $this->getRequest();
        $returnUrl = $request->getQuery(UserSessionService::QUERY_RETURN_URL_NAME);

        if (!$returnUrl)
            throw AuthenticationException::ReturnUrlIsNotProvided();

        $this->getContainer()[self::RETURN_URL] = $returnUrl;

        $oauthUrl = $this->getGoogleMethod()->makeAuthenticationUrl($this->getRegisterReturnPath(), null);

        return $this->redirect()->toUrl($oauthUrl);
    }

    public function registerReturnAction()
    {
        $data = $this->params()->fromQuery();
        $code = $data[GoogleMethod::RESULT_QUERY_CODE];

        try {

            $this->getGoogleMethod()->initAccessToken($this->getRegisterReturnPath(), $code);
            $oauthData = $this->getGoogleMethod()->getOAuthProfile();
            $oauthId   = $oauthData[GoogleMethod::PROFILE_FIELD_ID];

            $registerAdapter = $this->getGoogleMethod();
            $newAccountName  = $this->newAccountName($oauthId, $oauthData);
            $newIdentityId   = $this->getUserIdentityService()->register($registerAdapter, $newAccountName, null, $oauthId, $oauthData);

            $this->createUserInLocalDatabase($newIdentityId, $oauthData);

            return $this->getReturnResponseForNewUserCreated();

        } catch (UserIdentityException $exception) {
            switch ($exception->getCode()) {
                case UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST:
                    return $this->getReturnResponseForUserAlreadyExisted();
                default:
                    throw $exception;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function signInAction()
    {
        /** @var Request $request */
        $request   = $this->getRequest();
        $returnUrl = $request->getQuery(UserSessionService::QUERY_RETURN_URL_NAME);

        if (!$returnUrl)
            throw AuthenticationException::ReturnUrlIsNotProvided();

        $this->getContainer()[self::RETURN_URL] = $returnUrl;

        $oauthUrl = $this->getGoogleMethod()->makeAuthenticationUrl($this->getAuthenticationReturnPath(), null);

        return $this->redirect()->toUrl($oauthUrl);
    }

    public function signInReturnAction()
    {
        /** @var Request $req */
        $req  = $this->getRequest();
        $data = $req->getQuery();
        $code = $data[GoogleMethod::RESULT_QUERY_CODE];

        try {

            $returnUrl   = $this->getContainer()[self::RETURN_URL];
            $profile     = $this->getGoogleMethod()->initAccessToken($this->getAuthenticationReturnPath(), $code)->getLocalProfile();
            $identity    = $profile ? $this->getUserIdentityService()->getIdentityById($profile->getIdentityId()) : null;
            $authService = $this->getAuthenticationService();

            if ($authService instanceof AuthenticationService) {

                $adapter = new GoogleAuthenticationAdapter($this->googleConfig, $this->dbAdapter, $identity);
                $adapter->setAccessToken($this->getGoogleMethod()->getAccessToken());
                $result = $authService->authenticate($adapter);

            } else {

                throw new AuthenticationException(AuthenticationException::ERROR_CODE_AUTHENTICATION_SERVICE_NOT_SUPPORTED);
            }

            if ($result->isValid()) {

                $returnUrl = urldecode($returnUrl);
                return $this->redirect()->toUrl($returnUrl);

            } else {

                switch ($result->getCode()) {
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                        return $this->getReturnResponseForIdentityNotFound();
                    case Result::FAILURE_CREDENTIAL_INVALID:
                        return $this->getReturnResponseForInvalidCredential();
                    default:
                        return $this->getReturnResponseForOtherIssues();
                }
            }

        } catch (UserIdentityException $e) {
            return array('error' => $e);
        } catch (AuthenticationException $e) {
            return array('error' => $e);
        } catch (GoogleException $e) {
            return array('error' => $e);
        } catch (Exception $e) {
            throw $e;
        }
    }

}