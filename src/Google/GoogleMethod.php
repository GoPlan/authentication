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
use CreativeDelta\User\Core\Domain\UserRegisterMethodAdapter;
use Google_Client;
use Google_Service_Oauth2;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\RowGateway\RowGateway;

class GoogleMethod implements OAuthAuthenticationInterface, UserRegisterMethodAdapter
{
    const METHOD_NAME              = "google";
    const METHOD_TABLE_NAME        = "UserGoogle";
    const METHOD_CONFIG_APP_ID     = "clientId";
    const METHOD_CONFIG_APP_SECRET = "clientSecret";
    const METHOD_CONFIG_APP_SCOPE  = "clientScope";
    const METHOD_CONFIG_API_KEY    = "apiKey";

    const RESULT_QUERY_CODE  = "code";
    const RESULT_QUERY_STATE = "state";

    const PROFILE_FIELD_ID         = "id";
    const PROFILE_FIELD_FIRST_NAME = "first_name";
    const PROFILE_FIELD_LAST_NAME  = "last_name";
    const PROFILE_FIELD_EMAIL      = "email";

    /**
     * @var AdapterInterface $dbAdapter
     */
    protected $dbAdapter;

    /**
     * @var array $config ;
     */
    protected $config;

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

        $client_id     = $this->config[self::METHOD_CONFIG_APP_ID];
        $client_secret = $this->config[self::METHOD_CONFIG_APP_SECRET];
        $api_key       = $this->config[self::METHOD_CONFIG_API_KEY];

        $this->client = new Google_Client();
        $this->client->setClientId($client_id);
        $this->client->setClientSecret($client_secret);
        $this->client->setDeveloperKey($api_key);

        $this->client->setIncludeGrantedScopes(true);
        $this->client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
        $this->client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

        $this->localTable = new GoogleTable($this->dbAdapter);
    }

    public function makeAuthenticationUrl($redirectUri, $state = null)
    {
        $this->client->setState($state);
        $this->client->setRedirectUri($redirectUri);
        return $this->client->createAuthUrl();
    }

    public function initAccessToken($redirectUri, $code)
    {
        $this->client->setRedirectUri($redirectUri);
        $this->client->fetchAccessTokenWithAuthCode($code);
        return $this;
    }

    public function getOAuthProfile($fields = null)
    {
        $service  = new Google_Service_Oauth2($this->client);
        $userInfo = $service->userinfo->get();

        $oauthData = [
            self::PROFILE_FIELD_ID         => $userInfo->getId(),
            self::PROFILE_FIELD_EMAIL      => $userInfo->getEmail(),
            self::PROFILE_FIELD_FIRST_NAME => $userInfo->getGivenName(),
            self::PROFILE_FIELD_LAST_NAME  => $userInfo->getFamilyName()
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
        return $this->client->getAccessToken();
    }

    public function has($userId)
    {
        return $this->localTable->hasUserId($userId);
    }

    public function getName()
    {
        return self::METHOD_NAME;
    }

    public function getTableName()
    {
        return self::METHOD_TABLE_NAME;
    }

    public function register($identityId, $userId, $dataJson)
    {
        $row = new RowGateway(GoogleTable::ID_NAME, GoogleTable::TABLE_NAME, $this->dbAdapter);

        $row[GoogleTable::COLUMN_IDENTITY_ID] = $identityId;
        $row[GoogleTable::COLUMN_GOOGLE_ID]   = $userId;
        $row->save();

        return $row[GoogleTable::ID_NAME];
    }

}