<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/6/17
 * Time: 5:17 PM
 */

namespace CreativeDelta\User\Facebook;


use CreativeDelta\User\Core\Service\UserAuthenticationMethodServiceInterface;
use CreativeDelta\User\Core\Service\UserRegisterMethodAdapter;
use Zend\Db\RowGateway\RowGateway;
use Zend\Http\Client;
use Zend\Http\Response;

class FacebookMethod implements UserRegisterMethodAdapter, UserAuthenticationMethodServiceInterface
{
    const METHOD_NAME              = "Facebook";
    const METHOD_TABLE_NAME        = "UserFacebook";
    const FACEBOOK_RESPONSE        = "code";
    const FACEBOOK_OAUTH_URL       = "https://www.facebook.com/v2.8/dialog/oauth";
    const FACEBOOK_TOKEN_URL       = "https://graph.facebook.com/v2.8/oauth/access_token";
    const FACEBOOK_GRAPH_URL       = "https://graph.facebook.com/me";
    const FACEBOOK_SCOPE           = "public_profile, email";
    const FACEBOOK_PROFILE_FIELDS  = "id, first_name, last_name, email";
    const FACEBOOK_PROFILE_ID_NAME = "id";

    protected $appId;
    protected $appSecret;
    protected $token;
    protected $scope;
    protected $state;
    protected $dbAdapter;
    protected $userFacebookTable;

    /**
     * UserFacebookService constructor.
     * @param $dbAdapter
     * @param $appId
     * @param $appSecret
     * @param $scope
     * @internal param $redirectUri
     */
    public function __construct($dbAdapter, $appId, $appSecret, $scope)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->scope     = $scope;

        $this->dbAdapter         = $dbAdapter;
        $this->userFacebookTable = new FacebookTable($this->dbAdapter);
    }

    /**
     * Use this method to receive URL for Facebook authentication. To initialize the sign-in sequence, redirect yourself to the URL.
     * Once sign-in step is completed, the user will be redirected back to the URL provided previously in $redirectUri.
     * You should have a controller::action catch this redirection. Then in this action, further activities can be arranged using returned the "code" and "state".
     *
     * @param null $redirectUri
     * @param $state
     * @return string
     */
    public function makeAuthenticationUrl($redirectUri, $state = null)
    {
        $config = [
            'response_type' => self::FACEBOOK_RESPONSE,
            'scope'         => $this->scope,
            'client_id'     => $this->appId,
            'redirect_uri'  => $redirectUri
        ];

        if ($state)
            $config['state'] = $state;

        $query = http_build_query($config);
        $url   = self::FACEBOOK_OAUTH_URL . '?' . $query;

        return $url;
    }

    /**
     * Since service is only set to received [code] in the authentication response,
     * an extra step must be made in order to receive an access_token for your application.
     *
     * Use this method and provide it with the code (received from authentication) to get a token.
     *
     * @param $redirectUri
     * @param $code
     * @return $this
     * @throws FacebookException|\Exception
     */
    public function initAccessToken($redirectUri, $code)
    {
        $config = [
            'client_id'     => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri'  => $redirectUri,
            'code'          => $code
        ];

        $client = new Client(self::FACEBOOK_TOKEN_URL);
        $client->setParameterGet($config);

        $response  = $client->send();
        $dataArray = json_decode($response->getBody(), true);

        switch ($response->getStatusCode()) {
            case Response::STATUS_CODE_200:
                $this->token = $dataArray['access_token'];
                break;
            case Response::STATUS_CODE_400:
                throw FacebookException::newFromArray($dataArray);
            default:
                break;
        }

        return $this;
    }

    /**
     * @param string $fields // a comma separated string of profile fields to be retrieved
     * @return array
     * @throws FacebookException|\Exception
     */
    public function getProfileData($fields = null)
    {
        $fields = $fields ? $fields : self::FACEBOOK_PROFILE_FIELDS;
        $client = new Client(self::FACEBOOK_GRAPH_URL);
        $client->setParameterGet([
            'access_token' => $this->token,
            'fields'       => $fields
        ]);

        $response  = $client->send();
        $dataArray = json_decode($$response->getBody(), true);

        switch ($response->getStatusCode()) {
            case Response::STATUS_CODE_200:
                return $dataArray;
            case Response::STATUS_CODE_400:
                throw FacebookException::newFromArray($dataArray, null);
            default:
                break;
        }

        return $dataArray;
    }

    /**
     * @return null|FacebookProfile
     */
    public function getStoredProfile()
    {
        $profileData         = $this->getProfileData();
        $storedProfileResult = $this->userFacebookTable->get($profileData[self::FACEBOOK_PROFILE_ID_NAME]);

        if ($storedProfileResult) {
            $facebookProfile = FacebookProfile::newFromArray($this->dbAdapter, $storedProfileResult->getArrayCopy(), true);
            return $facebookProfile;
        } else {
            return null;
        }
    }

    /**
     * @param $userId
     * @return bool
     */
    public function has($userId)
    {
        return $this->userFacebookTable->has($userId);
    }

    public function register($identityId, $userId, $dataJson)
    {
        $row               = new RowGateway(FacebookTable::ID_NAME, FacebookTable::TABLE_NAME, $this->dbAdapter);
        $row['identityId'] = $identityId;
        $row['userId']     = $userId;
        $row['dataJson']   = json_encode($dataJson);
        $row->save();

        return $row[FacebookTable::ID_NAME];
    }

    public function getName()
    {
        return self::METHOD_NAME;
    }

    public function getTableName()
    {
        return self::METHOD_TABLE_NAME;
    }

}