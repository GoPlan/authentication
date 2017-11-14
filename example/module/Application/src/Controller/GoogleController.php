<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/25/17
 * Time: 9:31 AM
 */

namespace CreativeDelta\User\Application\Controller;


use CreativeDelta\User\Google\GoogleAbstractController;
use Zend\Http\Request;

class GoogleController extends GoogleAbstractController
{
    const ROUTE_SIGN_IN_NAME         = "application/sign-in/google/sign-in";
    const ROUTE_SIGN_IN_RETURN_NAME  = "application/sign-in/google/sign-in-return";
    const ROUTE_REGISTER_NAME        = "application/register/google/register";
    const ROUTE_REGISTER_RETURN_NAME = "application/register/google/register-return";
    const ROUTE_ATTACH_NAME        = "application/register/google/attach-account";
    const ROUTE_ATTACH_RETURN_NAME = "application/register/google/attach-account-return";

    function getAuthenticationReturnPath()
    {
        /** @var Request $req */
        $req    = $this->getRequest();
        $scheme = $req->getUri()->getScheme();
        $host   = $req->getUri()->getHost();
        $path   = $this->url()->fromRoute(self::ROUTE_SIGN_IN_RETURN_NAME);
        $return = "{$scheme}://{$host}{$path}";

        return $return;
    }

    function getAttachAccountReturnPath()
    {
        /** @var Request $req */
        $req    = $this->getRequest();
        $scheme = $req->getUri()->getScheme();
        $host   = $req->getUri()->getHost();
        $path   = $this->url()->fromRoute(self::ROUTE_ATTACH_RETURN_NAME);
        $return = "{$scheme}://{$host}{$path}";

        return $return;
    }

    function getRegisterReturnPath()
    {
        /** @var Request $req */
        $req    = $this->getRequest();
        $scheme = $req->getUri()->getScheme();
        $host   = $req->getUri()->getHost();
        $path   = $this->url()->fromRoute(self::ROUTE_REGISTER_RETURN_NAME);
        $return = "{$scheme}://{$host}{$path}";

        return $return;
    }

    function getReturnResponseForIdentityNotFound()
    {
        $query = ['return' => $this->getContainer()[self::RETURN_URL]];
        return $this->redirect()->toRoute(IndexController::ROUTE_APPLICATION_NAME, ['action' => 'register'], ['query' => $query]);
    }

    function getReturnResponseForInvalidCredential()
    {
        $query = ['return' => $this->getContainer()[self::RETURN_URL]];
        return $this->redirect()->toRoute(IndexController::ROUTE_APPLICATION_NAME, ['action' => 'sign-in'], ['query' => $query]);
    }

    function getReturnResponseForOtherIssues()
    {
        $query = ['return' => $this->getContainer()[self::RETURN_URL]];
        return $this->redirect()->toRoute(IndexController::ROUTE_APPLICATION_NAME, ['action' => 'sign-in'], ['query' => $query]);
    }

    function getReturnResponseForNewUserCreated()
    {
        $query = ['return' => $this->getContainer()[self::RETURN_URL]];
        return $this->redirect()->toRoute(self::ROUTE_SIGN_IN_NAME, [], ['query' => $query]);
    }

    function getReturnResponseForUserAlreadyExisted()
    {
        $query = ['return' => $this->getContainer()[self::RETURN_URL]];
        return $this->redirect()->toRoute(self::ROUTE_SIGN_IN_NAME, [], ['query' => $query]);
    }

    function createUserInLocalDatabase($identity, array $googleData)
    {
        // If you need to store newly created profile, use this method to create the profile on your local database.
    }

    function newAccountName($googleId, $googleData)
    {
        return "Google+{$googleId}";
    }
}