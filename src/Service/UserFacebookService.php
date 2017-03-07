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

namespace CreativeDelta\User\Service;


use CreativeDelta\User\Model\Identity;
use CreativeDelta\User\Table\UserFacebookTable;
use Zend\Db\RowGateway\RowGateway;
use Zend\Http\Client;

class UserFacebookService implements UserServiceStrategyInterface
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

    protected $dbAdapter;
    protected $userFacebookTable;

    /**
     * UserFacebookService constructor.
     * @param $appId
     * @param $appSecret
     * @param $scope
     * @param $dbAdapter
     * @param $redirectUri
     */
    public function __construct($appId, $appSecret, $scope, $redirectUri, $dbAdapter)
    {
        $this->appId       = $appId;
        $this->appSecret   = $appSecret;
        $this->scope       = $scope;
        $this->redirectUri = $redirectUri;

        $this->dbAdapter         = $dbAdapter;
        $this->userFacebookTable = new UserFacebookTable($this->dbAdapter);
    }

    /**
     * Use this method to receive URL for Facebook authentication, then redirect your page to the URL.
     * Once user has logged-in, the user will then be redirected back to the URL set by $redirectUri.
     * You should have an controller::action for this redirection url. Then in this action, you can
     * retrieve authentication data {code, state} for next uses.
     *
     * @param $state
     * @return string
     */
    public function generateOAuthUrl($state = null)
    {
        $config = [
            'response_type' => self::FACEBOOK_RESPONSE,
            'scope'         => $this->scope,
            'client_id'     => $this->appId,
            'redirect_uri'  => $this->redirectUri,
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
     * @param $code
     * @return $this
     */
    public function initializeToken($code)
    {
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

        return $this;
    }

    /**
     * @param string $fields // a comma separated string of profile fields to be retrieved
     * @return mixed
     */
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

    public function has($userId)
    {
        return $this->userFacebookTable->has($userId);
    }

    public function get($userId)
    {
        return $this->userFacebookTable->get($userId);
    }

    /**
     * @param RowGateway $identity
     * @param int $userId
     * @param string $dataJson
     */
    public function register($identity, $userId, $dataJson)
    {
        $record = $this->userFacebookTable->create($identity['id'], $userId, $dataJson);

        $identity['primaryTable'] = UserFacebookTable::TABLE_NAME;
        $identity['primaryId']    = $record[UserFacebookTable::ID_NAME];
        $identity['state']        = Identity::STATE_ACTIVE;
        $identity->save();
    }


}