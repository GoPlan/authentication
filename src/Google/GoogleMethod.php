<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 5:46 PM
 */

namespace CreativeDelta\User\Google;


use CreativeDelta\User\Core\Domain\OAuthAuthenticationInterface;
use Google_Client;
use Google_Service_People;
use Zend\Db\Adapter\AdapterInterface;

class GoogleMethod implements OAuthAuthenticationInterface
{
    /**
     * @var AdapterInterface $dbAdapter
     */
    protected $dbAdapter;

    /**
     * @var array $config ;
     */
    protected $config;

    /**
     * @var $accessToken
     */
    protected $accessToken;

    /**
     * @var Google_Client $client
     */
    protected $client;

    /**
     * @var GoogleTable $userTable ;
     */
    protected $userTable;

    /**
     * GoogleMethod constructor.
     * @param AdapterInterface $dbAdapter
     * @param array $config
     */
    public function __construct(AdapterInterface $dbAdapter, array $config)
    {
        $this->dbAdapter = $dbAdapter;
        $this->config    = $config;
        $this->client    = new Google_Client($this->config);
        $this->userTable = new GoogleTable($this->dbAdapter);
    }

    public function makeAuthenticationUrl($redirectUri, $state = null)
    {
        $this->client->setState($state);
        $this->client->setRedirectUri($redirectUri);
        return $this->client->createAuthUrl();
    }

    public function initAccessToken($redirectUri, $code)
    {
        $this->accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        return $this;
    }

    public function getProfileData($fields = null)
    {
        $service = new Google_Service_People($this->client);
        $profile = $service->people->get('people/me');

        return $profile;
    }

    public function getStoredProfile()
    {
        $userId  = $this->client->getClientId();
        $result  = $this->userTable->get($userId);
        $profile = $result ? GoogleProfile::newFromData($result->getArrayCopy()) : null;

        return $profile;
    }

    public function verifyUserId($userId)
    {
        $this->client->
    }

    public function hasExpired()
    {
        return $this->client->isAccessTokenExpired();
    }

    public function refreshToken()
    {
        $this->accessToken = $this->client->getRefreshToken();
    }

}