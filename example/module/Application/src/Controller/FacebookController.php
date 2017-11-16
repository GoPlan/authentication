<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/24/17
 * Time: 11:45 AM
 */

namespace CreativeDelta\User\Application\Controller;


use CreativeDelta\User\Facebook\FacebookAbstractController;

class FacebookController extends FacebookAbstractController
{
    const ROUTE_SIGN_IN_NAME         = "application/sign-in/facebook/sign-in";
    const ROUTE_SIGN_IN_RETURN_NAME  = "application/sign-in/facebook/sign-in-return";
    const ROUTE_REGISTER_NAME        = "application/register/facebook/register";
    const ROUTE_REGISTER_RETURN_NAME = "application/register/facebook/register-return";
    const ROUTE_ATTACH_NAME          = "application/register/facebook/attach";
    const ROUTE_ATTACH_RETURN_NAME   = "application/register/facebook/attach-account-return";

    function getAuthenticationReturnPath()
    {
        return $this->url()->fromRoute(self::ROUTE_SIGN_IN_RETURN_NAME);
    }

    function getAttachReturnPath()
    {
        return $this->url()->fromRoute(self::ROUTE_ATTACH_RETURN_NAME);
    }

    function getRegisterReturnPath()
    {
        return $this->url()->fromRoute(self::ROUTE_REGISTER_RETURN_NAME);
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

    function createUserInLocalDatabase($identity, array $facebookData)
    {
        // If you need to store newly created profile, use this method to create the profile on your local database.
    }

    function newAccountName($facebookId, $facebookData)
    {
        return "Facebook+{$facebookId}";
    }
}