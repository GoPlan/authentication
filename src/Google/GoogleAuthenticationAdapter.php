<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 3:52 PM
 */

namespace CreativeDelta\User\Google;


use CreativeDelta\User\Core\Domain\AbstractOAuthAuthenticationAdapter;
use CreativeDelta\User\Core\Domain\Entity\Identity;
use Google_Client;
use Google_Service_Oauth2;
use Zend\Db\Adapter\AdapterInterface;

class GoogleAuthenticationAdapter extends AbstractOAuthAuthenticationAdapter
{
    const METHOD_NAME = "google";

    /** @var Google_Client $oauthClient */
    protected $oauthClient;

    /** @var  GoogleTable $localTable */
    protected $localTable;


    public function __construct(array $config, AdapterInterface $dbAdapter, Identity $identity = null)
    {
        parent::__construct($config, $dbAdapter, $identity);

        $this->localTable = new GoogleTable($this->dbAdapter);

        $this->oauthClient = new Google_Client();
        $this->oauthClient->setClientId($this->config[GoogleMethod::METHOD_CONFIG_APP_ID]);
        $this->oauthClient->setClientSecret($this->config[GoogleMethod::METHOD_CONFIG_APP_SECRET]);
        $this->oauthClient->setDeveloperKey($this->config[GoogleMethod::METHOD_CONFIG_API_KEY]);
        $this->oauthClient->setScopes(explode(' ', $this->config[GoogleMethod::METHOD_CONFIG_APP_SCOPE]));

        if ($identity) {

            $this->_loadLocalProfile();
            $profile = $identity->getProfile();

            if ($profile && isset($profile[GoogleTable::COLUMN_ACCESS_TOKEN])) {
                $this->oauthClient->setAccessToken($profile[GoogleTable::COLUMN_ACCESS_TOKEN]);
            }
        }
    }

    function pokeIdentity()
    {
        $token = $this->oauthClient->getAccessToken();

        $profileData     = $this->identity->getProfile();
        $profileInstance = GoogleProfile::newFromArray($this->dbAdapter, $profileData, true);
        $profileInstance->setAccessToken($token[GoogleMethod::TOKEN_ACCESS_TOKEN]);
        $profileInstance->save();
    }

    public function verifyIdentity()
    {
        $service = new Google_Service_Oauth2($this->oauthClient);

        if (!$service)
            throw new GoogleException(GoogleException::ERROR_CODE_VERIFY_IDENTITY_FAILED_TO_INITIATE_GOOGLE_OAUTH2);

        $oauthId = $service->userinfo->get()->getId();

        if (!$oauthId)
            return false;

        $profile = $this->identity->getProfile();

        if (!$profile)
            return false;

        $localUserId = $profile[GoogleTable::COLUMN_GOOGLE_ID];

        return $oauthId == $localUserId;
    }

    function setAccessToken($token)
    {
        $this->oauthClient->setAccessToken($token);
    }

    function getAccessToken()
    {
        return $this->oauthClient->getAccessToken();
    }

    private function _loadLocalProfile()
    {
        $result = $this->localTable->getByIdentityId($this->identity->getId());
        $this->identity->setProfile($result->getArrayCopy());
    }
}