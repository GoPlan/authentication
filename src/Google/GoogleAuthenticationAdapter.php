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
use CreativeDelta\User\Facebook\FacebookTable;
use Google_Client;
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

        $this->localTable  = new GoogleTable($this->dbAdapter);
        $this->oauthClient = new Google_Client();

        if ($identity) {
            $this->_loadLocalProfile();
            $this->oauthClient->setAccessToken($identity->getProfile()[FacebookTable::COLUMN_ACCESS_TOKEN]);
        }
    }

    function pokeIdentity()
    {
        $profileData     = $this->identity->getProfile();
        $profileInstance = GoogleProfile::newFromArray($this->dbAdapter, $profileData, true);
        $profileInstance->setAccessToken($this->oauthClient->getAccessToken());
        $profileInstance->save();
    }

    public function verifyIdentity()
    {
        $oauthId = $this->oauthClient->getClientId();

        if (!$oauthId)
            return false;

        $identityProfile = $this->identity->getProfile();

        if (!$identityProfile)
            return false;

        $localUserId = $identityProfile[GoogleTable::COLUMN_GOOGLE_ID];

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