<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/13/17
 * Time: 8:53 AM
 */

namespace CreativeDelta\User\Google;


use CreativeDelta\User\Core\Service\UserAuthenticationMethodServiceInterface;
use CreativeDelta\User\Core\Service\UserRegisterMethodAdapter;

class UserGoogleMethod implements UserAuthenticationMethodServiceInterface, UserRegisterMethodAdapter
{
    const METHOD_NAME       = "Google";
    const METHOD_TABLE_NAME = "UserGoogle";
    const GOOGLE_RESPONSE   = "code";
    const GOOGLE_OAUTH_URL  = "https://www.facebook.com/v2.8/dialog/oauth";
    const GOOGLE_TOKEN_URL  = "https://graph.facebook.com/v2.8/oauth/access_token";
    const GOOGLE_GRAPH_URL  = "https://graph.facebook.com/me";
    const GOOGLE_SCOPE      = "public_profile, email";

    protected $appId;
    protected $appSecret;

    public function getName()
    {
        return self::METHOD_NAME;
    }

    public function getTableName()
    {
        return self::METHOD_TABLE_NAME;
    }

    public function makeAuthenticationUrl($redirectUri, $state = null)
    {
        // TODO: Implement generateAuthenticationUrl() method.
    }

    public function initAccessToken($redirectUri, $code)
    {
        // TODO: Implement getAccessToken() method.
    }

    public function getProfileData($fields = null)
    {
        // TODO: Implement getProfile() method.
    }

    public function getStoredProfile()
    {
        // TODO: Implement getStoredProfile() method.
    }

    public function has($userId)
    {
        // TODO: Implement has() method.
    }

    public function register($identityId, $userId, $dataJson)
    {
        // TODO: Implement register() method.
    }


}