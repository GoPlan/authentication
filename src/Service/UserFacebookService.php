<?php
/**
 * Created by PhpStorm.
 *
 * combo-outfit (by Duc-Anh LE)
 *
 * User: ducanh-ki
 * Date: 3/6/17
 * Time: 5:17 PM
 */

namespace CreativeDelta\User\Service;


use Zend\Http\Client;

class UserFacebookService
{
    const FACEBOOK_RESPONSE  = "code";
    const FACEBOOK_OAUTH_URL = "https://www.facebook.com/v2.8/dialog/oauth";
    const FACEBOOK_TOKEN_URL = "https://graph.facebook.com/v2.8/oauth/access_token";
    const FACEBOOK_GRAPH_URL = "https://graph.facebook.com/me";
    const FACEBOOK_SCOPE     = "public_profile, email";

    protected $appId;
    protected $appSecret;

    protected $token;
    protected $scope;
    protected $state;
    protected $redirectUri;

    /**
     * UserFacebookService constructor.
     * @param $appId
     * @param $appSecret
     * @param $scope
     * @param $redirectUri
     */
    public function __construct($appId, $appSecret, $scope, $redirectUri)
    {
        $this->appId       = $appId;
        $this->appSecret   = $appSecret;
        $this->scope       = $scope;
        $this->redirectUri = $redirectUri;
    }

    public function generateOAuthUrl($state)
    {
        $config = [
            'response_type' => self::FACEBOOK_RESPONSE,
            'scope'         => $this->scope,
            'client_id'     => $this->appId,
            'redirect_uri'  => $this->redirectUri,
            'state'         => $state
        ];

        $query = http_build_query($config);
        $url   = self::FACEBOOK_OAUTH_URL . '?' . $query;

        return $url;
    }

    public function initializeToken($data)
    {
        $code = $data['code'];

        $config = [
            'client_id'     => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri'  => $this->redirectUri,
            'code'          => $code
        ];

        $httpToken = new Client(self::FACEBOOK_TOKEN_URL);
        $httpToken->setParameterGet($config);

        $response  = $httpToken->send();
        $dataArray = json_decode($response->getBody(), true);

        $this->token = $dataArray['access_token'];
    }

    public function profile($fields)
    {
        $httpGraph = new Client(self::FACEBOOK_GRAPH_URL);
        $httpGraph->setParameterGet([
            'access_token' => $this->token,
            'fields'       => $fields
        ]);

        $response     = $httpGraph->send();
        $profileJson  = $response->getBody();
        $profileArray = json_decode($profileJson, true);

        return $profileArray;
    }
}