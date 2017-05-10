<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/8/17
 * Time: 10:57 AM
 */

namespace CreativeDelta\User\Facebook;


use Zend\Http\Client;
use Zend\Http\Response;

class FacebookClient
{
    // Query field names
    const QUERY_ACCESS_TOKEN  = "access_token";
    const QUERY_CLIENT_ID     = "client_id";
    const QUERY_CLIENT_SECRET = "client_secret";
    const QUERY_APP_SCOPE     = "scope";
    const QUERY_STATE         = "state";
    const QUERY_CODE          = "code";
    const QUERY_FIELDS        = "fields";
    const QUERY_RESPONSE_TYPE = "response_type";
    const QUERY_REDIRECT_URI  = "redirect_uri";

    // Links
    const FACEBOOK_OAUTH_URL = "https://www.facebook.com/v2.8/dialog/oauth";
    const FACEBOOK_TOKEN_URL = "https://graph.facebook.com/v2.8/oauth/access_token";
    const FACEBOOK_GRAPH_URL = "https://graph.facebook.com/me";

    // Defaults
    const FACEBOOK_RESPONSE        = "code";
    const FACEBOOK_SCOPE           = "public_profile, email";
    const FACEBOOK_PROFILE_FIELDS  = "id, first_name, last_name, email";
    const FACEBOOK_PROFILE_ID_NAME = "id";

    protected $appId;
    protected $appSecret;
    protected $appScope;
    protected $accessToken;
    protected $code;

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * FacebookClient constructor.
     * @param $appId
     * @param $appSecret
     * @param $appScope
     */
    public function __construct($appId, $appSecret, $appScope)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->appScope  = $appScope;
    }

    /**
     * @param $redirectUri
     * @param null $state
     * @return string
     */
    public function makeAuthenticationUrl($redirectUri, $state = null)
    {
        $config = [
            self::QUERY_RESPONSE_TYPE => self::FACEBOOK_RESPONSE,
            self::QUERY_CLIENT_ID     => $this->appId,
            self::QUERY_APP_SCOPE     => $this->appScope,
            self::QUERY_REDIRECT_URI  => $redirectUri
        ];

        if ($state)
            $config[self::QUERY_STATE] = $state;

        $query = http_build_query($config);
        $url   = self::FACEBOOK_OAUTH_URL . '?' . $query;

        return $url;
    }

    /**
     * @param $redirectUri
     * @param $code
     * @return $this
     * @throws FacebookException
     */
    public function initAccessToken($redirectUri, $code)
    {
        $this->code = $code;

        $config = [
            'sslverifypeer' => false
        ];

        $query = [
            self::QUERY_CLIENT_ID     => $this->appId,
            self::QUERY_CLIENT_SECRET => $this->appSecret,
            self::QUERY_REDIRECT_URI  => $redirectUri,
            self::QUERY_CODE          => $code
        ];

        $client = new Client(self::FACEBOOK_TOKEN_URL, $config);
        $client->setParameterGet($query);

        $res  = $client->send();
        $data = json_decode($res->getBody(), true);

        switch ($res->getStatusCode()) {
            case Response::STATUS_CODE_200:
                $this->accessToken = $data[self::QUERY_ACCESS_TOKEN];
                break;
            case Response::STATUS_CODE_400:
                throw FacebookException::newFromArray($data);
            default:
                break;
        }

        return $this;
    }

    /**
     * @param null $fields
     * @return array
     * @throws FacebookException
     */
    public function getFacebookProfile($fields = null)
    {
        if (!$this->accessToken)
            return null;

        $config = [
            'sslverifypeer' => false
        ];

        $fields = $fields ? $fields : self::FACEBOOK_PROFILE_FIELDS;

        $query = [
            self::QUERY_ACCESS_TOKEN => $this->accessToken,
            self::QUERY_FIELDS       => $fields
        ];

        $client = new Client(self::FACEBOOK_GRAPH_URL, $config);
        $client->setParameterGet($query);

        $res  = $client->send();
        $data = json_decode($res->getBody(), true);

        switch ($res->getStatusCode()) {
            case Response::STATUS_CODE_200:
                return $data;
            case Response::STATUS_CODE_400:
                throw FacebookException::newFromArray($data, null);
            default:
                break;
        }

        return $data;
    }
}