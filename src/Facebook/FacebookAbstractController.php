<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/22/17
 * Time: 3:18 PM
 */

namespace CreativeDelta\User\Facebook;


use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Impl\Exception\AuthenticationException;
use CreativeDelta\User\Core\Impl\Exception\UserIdentityException;
use CreativeDelta\User\Core\Impl\Service\AuthenticationService;
use CreativeDelta\User\Core\Impl\Service\UserIdentityService;
use CreativeDelta\User\Core\Impl\Service\UserSessionService;
use Exception;
use Zend\Authentication\Result;
use Zend\Db\Adapter\Adapter;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

abstract class FacebookAbstractController extends AbstractActionController
{
    const RETURN_URL = 'returnUrl';

    /**
     * @var array
     */
    protected $facebookConfig;

    /**
     * @var FacebookMethod
     */
    protected $facebookMethod;

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
     * FacebookAbstractController constructor.
     * @param Adapter                           $dbAdapter
     * @param AuthenticationService             $authenticationService
     * @param FacebookMethod|null               $facebookMethod
     * @param UserIdentityServiceInterface|null $userIdentityService
     */
    public function __construct($dbAdapter,
        $authenticationService,
        $facebookMethod = null,
        $userIdentityService = null)
    {
        $this->dbAdapter             = $dbAdapter;
        $this->authenticationService = $authenticationService;
        $this->facebookConfig        = $authenticationService->getConfig()[FacebookMethod::METHOD_NAME];
        $this->facebookMethod        = $facebookMethod;
        $this->userIdentityService   = $userIdentityService;
    }

    /**
     * @return FacebookMethod
     */
    public function getFacebookMethod()
    {
        if (!$this->facebookMethod) {

            $appId     = $this->facebookConfig[FacebookMethod::METHOD_CONFIG_APP_ID];
            $appSecret = $this->facebookConfig[FacebookMethod::METHOD_CONFIG_APP_SECRET];
            $appScope  = $this->facebookConfig[FacebookMethod::METHOD_CONFIG_APP_SCOPE];

            $this->facebookMethod = new FacebookMethod($this->dbAdapter, $appId, $appSecret, $appScope);
        }

        return $this->facebookMethod;
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
     * @param array $facebookData
     * @return void
     */
    abstract function createUserInLocalDatabase($identity, array $facebookData);

    /**
     * @param int   $facebookId
     * @param array $facebookData
     * @return string
     */
    abstract function newIdentity($facebookId, $facebookData);


    public function getAuthenticationReturnUrl()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $scheme  = $request->getUri()->getScheme();
        $host    = $request->getUri()->getHost() . ':' . $request->getUri()->getPort();
        $path    = $this->getAuthenticationReturnPath();

        $url = "{$scheme}://{$host}{$path}";

        return $url;
    }

    public function getRegisterReturnUrl()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $scheme  = $request->getUri()->getScheme();
        $host    = $request->getUri()->getHost() . ':' . $request->getUri()->getPort();
        $path    = $this->getRegisterReturnPath();

        $url = "{$scheme}://{$host}{$path}";

        return $url;
    }

    public function registerAction()
    {
        /** @var Request $request */
        $request   = $this->getRequest();
        $returnUrl = $request->getQuery(UserSessionService::QUERY_RETURN_URL_NAME);

        if (!$returnUrl)
            throw AuthenticationException::ReturnUrlIsNotProvided();

        $this->getContainer()[self::RETURN_URL] = $returnUrl;

        $oauthUrl = $this->getFacebookMethod()->makeAuthenticationUrl($this->getRegisterReturnUrl(), null);

        return $this->redirect()->toUrl($oauthUrl);
    }

    public function registerReturnAction()
    {
        /** @var Request $req */
        $req  = $this->getRequest();
        $data = $req->getQuery()->toArray();
        $code = $data[FacebookMethod::RESULT_QUERY_CODE];

        try {

            $this->getFacebookMethod()->initAccessToken($this->getRegisterReturnUrl(), $code);

            // Register new identity that has Identity field with value as "facebook" + facebookId
            $facebookData  = $this->getFacebookMethod()->getOAuthProfile();
            $facebookId    = $facebookData[FacebookMethod::PROFILE_FIELD_ID];
            $newIdentity   = $this->newIdentity($facebookId, $facebookData);
            $newIdentityId = $this->getUserIdentityService()->register($this->getFacebookMethod(), $newIdentity, null, $facebookId, $facebookData);

            // Create a user record from facebook data
            $this->createUserInLocalDatabase($newIdentityId, $facebookData);

            return $this->getReturnResponseForNewUserCreated();

        } catch (UserIdentityException $exception) {
            switch ($exception->getCode()) {
                case UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST:
                    return $this->getReturnResponseForUserAlreadyExisted();
                default:
                    throw $exception;
            }
        } catch (\Exception $e) {
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

        $facebookAuthUrl = $this->getFacebookMethod()->makeAuthenticationUrl($this->getAuthenticationReturnUrl(), null);

        return $this->redirect()->toUrl($facebookAuthUrl);
    }

    public function signInReturnAction()
    {
        /** @var Request $req */
        $req  = $this->getRequest();
        $data = $req->getQuery();
        $code = $data[FacebookMethod::RESULT_QUERY_CODE];

        try {

            $returnUrl = $this->getContainer()[self::RETURN_URL];
            $profile   = $this->getFacebookMethod()->initAccessToken($this->getAuthenticationReturnUrl(), $code)->getLocalProfile();
            $identity  = $profile ? $this->getUserIdentityService()->getIdentityById($profile->getIdentityId()) : null;

            if ($this->authenticationService instanceof AuthenticationService) {

                $adapter = new FacebookAuthenticationAdapter($this->facebookConfig, $this->dbAdapter, $identity);
                $adapter->setAccessToken($this->getFacebookMethod()->getAccessToken());
                $result = $this->authenticationService->authenticate($adapter);

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
        } catch (FacebookException $e) {
            return array('error' => $e);
        } catch (Exception $e) {
            throw $e;
        }
    }
}