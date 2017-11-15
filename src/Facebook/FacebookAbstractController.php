<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/22/17
 * Time: 3:18 PM
 */

namespace CreativeDelta\User\Facebook;


use CreativeDelta\User\Core\Domain\Entity\Identity;
use CreativeDelta\User\Core\Domain\OAuthAuthenticationAdapter;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Impl\Exception\AuthenticationException;
use CreativeDelta\User\Core\Impl\Exception\UserIdentityException;
use CreativeDelta\User\Core\Impl\Service\UserSessionService;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

abstract class FacebookAbstractController extends AbstractActionController
{
    const RETURN_URL = 'returnUrl';

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var UserIdentityServiceInterface
     */
    protected $userIdentityService;

    /**
     * @var FacebookMethod
     */
    protected $facebookMethod;

    /**
     * @var Container
     */
    protected $container;


    /**
     * FacebookAbstractController constructor.
     * @param AuthenticationService        $authenticationService
     * @param UserIdentityServiceInterface $userIdentityService
     * @param FacebookMethod               $facebookMethod
     */
    public function __construct(
        AuthenticationService $authenticationService,
        UserIdentityServiceInterface $userIdentityService,
        FacebookMethod $facebookMethod)
    {
        $this->authenticationService = $authenticationService;
        $this->facebookMethod        = $facebookMethod;
        $this->userIdentityService   = $userIdentityService;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * @return FacebookMethod
     */
    public function getFacebookMethod()
    {
        return $this->facebookMethod;
    }

    /**
     * @return UserIdentityServiceInterface
     */
    public function getUserIdentityService()
    {
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

    abstract function getAttachReturnPath();

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
    abstract function newAccountName($facebookId, $facebookData);


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

    public function getAttachReturnUrl()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $scheme  = $request->getUri()->getScheme();
        $host    = $request->getUri()->getHost() . ':' . $request->getUri()->getPort();
        $path    = $this->getAttachReturnPath();

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

    public function attachAccountAction()
    {
        /** @var Request $request */
        $request   = $this->getRequest();
        $returnUrl = $request->getQuery(UserSessionService::QUERY_RETURN_URL_NAME);

        if (!$returnUrl)
            throw AuthenticationException::ReturnUrlIsNotProvided();

        $this->getContainer()[self::RETURN_URL] = $returnUrl;

        $oauthUrl = $this->getFacebookMethod()->makeAuthenticationUrl($this->getAttachReturnUrl(), null);

        return $this->redirect()->toUrl($oauthUrl);
    }

    public function attachAccountReturnAction()
    {
        /** @var Request $req */
        $req  = $this->getRequest();
        $data = $req->getQuery()->toArray();
        $code = $data[FacebookMethod::RESULT_QUERY_CODE];

        try {

            $this->getFacebookMethod()->initAccessToken($this->getAttachReturnUrl(), $code);
            $facebookData = $this->getFacebookMethod()->getOAuthProfile();
            $facebookId   = $facebookData[FacebookMethod::PROFILE_FIELD_ID];

            $registerAdapter = $this->getFacebookMethod();

            if ($this->authenticationService->hasIdentity()) {
                /** @var Identity $getIdentity */
                $getIdentity = $this->authenticationService->getIdentity();

                $this->getUserIdentityService()->attach($registerAdapter, $getIdentity->getId(), $facebookId, $facebookData);

                $this->createUserInLocalDatabase($getIdentity->getId(), $facebookData);
            }

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
            $facebookData = $this->getFacebookMethod()->getOAuthProfile();
            $facebookId   = $facebookData[FacebookMethod::PROFILE_FIELD_ID];

            $registerAdapter = $this->getFacebookMethod();
            $newAccountName  = $this->newAccountName($facebookId, $facebookData);
            $newIdentityId   = $this->getUserIdentityService()->register($registerAdapter, $newAccountName, null, $facebookId, $facebookData);

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

            $this->getFacebookMethod()->initAccessToken($this->getAuthenticationReturnUrl(), $code);

            $returnUrl   = $this->getContainer()[self::RETURN_URL];
            $authService = $this->getAuthenticationService();

            if ($this->authenticationService instanceof AuthenticationService) {

                $adapter = new OAuthAuthenticationAdapter($this->getUserIdentityService(), $this->getFacebookMethod());
                $result  = $authService->authenticate($adapter);

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