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
use CreativeDelta\User\Core\Impl\Service\UserIdentityService;
use CreativeDelta\User\Core\Impl\Service\UserSessionService;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

abstract class FacebookController extends AbstractActionController
{
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
    protected $zendAuthenticationService;

    /**
     * @var AdapterInterface;
     */
    protected $zendDbAdapter;


    public function __construct(array $facebookConfig,
                                AdapterInterface $dbAdapter,
                                AuthenticationService $authenticationService,
                                FacebookMethod $facebookMethod = null,
                                UserIdentityServiceInterface $userIdentityService = null)
    {
        $this->facebookConfig            = $facebookConfig;
        $this->zendDbAdapter             = $dbAdapter;
        $this->zendAuthenticationService = $authenticationService;
        $this->facebookMethod            = $facebookMethod;
        $this->userIdentityService       = $userIdentityService;
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

            $this->facebookMethod = new FacebookMethod($this->zendDbAdapter, $appId, $appSecret, $appScope);
        }

        return $this->facebookMethod;
    }

    /**
     * @return UserIdentityServiceInterface
     */
    public function getUserIdentityService()
    {
        if (!$this->userIdentityService) {
            $this->userIdentityService = new UserIdentityService($this->zendDbAdapter);
        }

        return $this->userIdentityService;
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
     * @param string $returnUrl
     * @param string $sessionHash
     * @return Response
     */
    abstract function getReturnResponseForIdentityNotFound($returnUrl, $sessionHash);

    /**
     * @param string $returnUrl
     * @param string $sessionHash
     * @return Response
     */
    abstract function getReturnResponseForInvalidCredential($returnUrl, $sessionHash);

    /**
     * @param string $returnUrl
     * @param string $sessionHash
     * @return Response
     */
    abstract function getReturnResponseForNewUserCreated($returnUrl, $sessionHash);

    /**
     * @param string $returnUrl
     * @param string $sessionHash
     * @return Response
     */
    abstract function getReturnResponseForUserAlreadyExisted($returnUrl, $sessionHash);

    /**
     * @param int   $identity
     * @param array $facebookData
     * @return void
     */
    abstract function createUserInLocalDatabase($identity, array $facebookData);


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
        $request     = $this->getRequest();
        $returnUrl   = $request->getQuery(UserSessionService::QUERY_RETURN_URL_NAME);
        $sessionHash = $request->getQuery(UserSessionService::QUERY_SESSION_NAME);

        if (!$returnUrl)
            throw AuthenticationException::ReturnUrlIsNotProvided();

        $newSession = $this->getUserIdentityService()->createSessionLog($sessionHash, $returnUrl);
        $oauthUrl   = $this->getFacebookMethod()->makeAuthenticationUrl($this->getRegisterReturnUrl(), $newSession);

        return $this->redirect()->toUrl($oauthUrl);
    }

    public function registerReturnAction()
    {
        /** @var Request $req */
        $req     = $this->getRequest();
        $data    = $req->getQuery()->toArray();
        $code    = $data[FacebookMethod::RESULT_QUERY_CODE];
        $state   = $data[FacebookMethod::RESULT_QUERY_STATE];
        $session = $this->getUserIdentityService()->getSessionLog($state);

        try {

            $this->getFacebookMethod()->initAccessToken($this->getRegisterReturnUrl(), $code);

            // Register new identity that has Identity field with value as "facebook" + facebookId
            $facebookData  = $this->getFacebookMethod()->getOAuthProfile();
            $facebookId    = $facebookData[FacebookMethod::PROFILE_FIELD_ID];
            $newIdentity   = "facebook" . '+' . $facebookId;
            $newIdentityId = $this->getUserIdentityService()->register($this->getFacebookMethod(), $newIdentity, $facebookId, $facebookData);

            // Create a user record from facebook data
            $this->createUserInLocalDatabase($newIdentityId, $facebookData);

            return $this->getReturnResponseForNewUserCreated($session->getReturnUrl(), $session->getPreviousHash());

        } catch (UserIdentityException $exception) {
            switch ($exception->getCode()) {
                case UserIdentityException::CODE_ERROR_INSERT_ACCOUNT_ALREADY_EXIST:
                    return $this->getReturnResponseForUserAlreadyExisted($session->getReturnUrl(), $session->getPreviousHash());
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
        $request     = $this->getRequest();
        $returnUrl   = $request->getQuery(UserSessionService::QUERY_RETURN_URL_NAME);
        $sessionHash = $request->getQuery(UserSessionService::QUERY_SESSION_NAME);

        if (!$returnUrl)
            throw AuthenticationException::ReturnUrlIsNotProvided();

        $newSession      = $this->getUserIdentityService()->createSessionLog($sessionHash, $returnUrl);
        $facebookAuthUrl = $this->getFacebookMethod()->makeAuthenticationUrl($this->getAuthenticationReturnUrl(), $newSession);

        return $this->redirect()->toUrl($facebookAuthUrl);
    }

    public function signInReturnAction()
    {
        /** @var Request $req */
        $req  = $this->getRequest();
        $data = $req->getQuery();
        $code = $data[FacebookMethod::RESULT_QUERY_CODE];
        $hash = $data[FacebookMethod::RESULT_QUERY_STATE];

        try {

            $prevSession     = $hash ? $this->getUserIdentityService()->getSessionLog($hash) : null;
            $prevReturnUrl   = $prevSession->getReturnUrl();
            $prevSessionHash = $prevSession->getPreviousHash();

            $localProfile = $this->getFacebookMethod()->initAccessToken($this->getAuthenticationReturnUrl(), $code)->getLocalProfile();
            $identity     = $localProfile ? $this->getUserIdentityService()->getIdentityById($localProfile->getIdentityId()) : null;

            if ($this->zendAuthenticationService instanceof AuthenticationService) {
                $adapter = new FacebookAuthenticationAdapter($this->facebookConfig, $this->zendDbAdapter, $identity);
                $adapter->setAccessToken($this->getFacebookMethod()->getAccessToken());
                $result = $this->zendAuthenticationService->authenticate($adapter);
            } else {
                throw new AuthenticationException(AuthenticationException::ERROR_CODE_AUTHENTICATION_SERVICE_NOT_SUPPORTED);
            }

            if ($result->isValid()) {
                $returnQuery = [UserSessionService::QUERY_SESSION_NAME => $prevSessionHash];
                $returnUrl   = urldecode($prevReturnUrl) . '?' . http_build_query($returnQuery);
                return $this->redirect()->toUrl($returnUrl);
            } else {
                switch ($result->getCode()) {
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                        return $this->getReturnResponseForIdentityNotFound($prevSession->getReturnUrl(), $prevSession->getPreviousHash());
                    case Result::FAILURE_CREDENTIAL_INVALID:
                        throw new UserIdentityException(UserIdentityException::CODE_ERROR_AUTHENTICATION_USER_NOT_ACTIVE);
                    default:
                        return $this->getReturnResponseForInvalidCredential($prevSession->getReturnUrl(), $prevSession->getPreviousHash());
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