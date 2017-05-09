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
use Google_Service_Oauth2;
use Zend\Db\Adapter\AdapterInterface;

class GoogleMethod implements OAuthAuthenticationInterface
{
    const METHOD_NAME              = "google";
    const METHOD_TABLE_NAME        = "UserGoogle";
    const METHOD_CONFIG_APP_ID     = "clientId";
    const METHOD_CONFIG_APP_SECRET = "clientSecret";
    const METHOD_CONFIG_APP_SCOPE  = "clientScope";
    const METHOD_CONFIG_API_KEY    = "apiKey";

    const RESULT_QUERY_CODE  = "code";
    const RESULT_QUERY_STATE = "state";

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
     * @var GoogleTable $localTable ;
     */
    protected $localTable;

    /**
     * GoogleMethod constructor.
     * @param AdapterInterface $dbAdapter
     * @param array $config
     * @param null $authConfigFile
     */
    public function __construct(AdapterInterface $dbAdapter, array $config, $authConfigFile = null)
    {
        $this->dbAdapter = $dbAdapter;
        $this->config    = $config;

        $this->client = new Google_Client();
        if ($authConfigFile) {
            $this->client->setAuthConfigFile($authConfigFile);
        } else {
            $this->client->useApplicationDefaultCredentials();
        }
        $this->localTable = new GoogleTable($this->dbAdapter);
    }

    public function makeAuthenticationUrl($redirectUri, $state = null)
    {
        $scope = $this->config['client_scope'];
        $this->client->setState($state);
        $this->client->setRedirectUri($redirectUri);
        return $this->client->createAuthUrl($scope);
    }

    public function initAccessToken($redirectUri, $code)
    {
        $this->accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        return $this;
    }

    public function getOAuthProfile($fields = null)
    {
        $this->client->setDeveloperKey($this->config['api_key']);
        $service   = new Google_Service_Oauth2($this->client);
        $userinfo  = $service->userinfo->get();
        $oauthData = [
            'name'  => $userinfo->getName(),
            'email' => $userinfo->getEmail()
        ];
        return $oauthData;
    }

    public function getLocalProfile()
    {
        $userId  = $this->client->getClientId();
        $result  = $this->localTable->getByUserId($userId);
        $profile = $result ? GoogleProfile::newFromArray($this->dbAdapter, $result->getArrayCopy(), true) : null;
        return $profile;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

}