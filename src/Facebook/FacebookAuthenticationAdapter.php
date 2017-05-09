<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/7/17
 * Time: 8:54 AM
 */

namespace CreativeDelta\User\Facebook;


use CreativeDelta\User\Core\Domain\AbstractOAuthAuthenticationAdapter;
use CreativeDelta\User\Core\Domain\Entity\Identity;
use Exception;
use Zend\Db\Adapter\AdapterInterface;

class FacebookAuthenticationAdapter extends AbstractOAuthAuthenticationAdapter
{
    const METHOD_NAME = "facebook";

    /** @var  FacebookClient $facebookClient */
    protected $facebookClient;

    /** @var  FacebookTable $facebookTable */
    protected $facebookTable;


    public function __construct(array $config, AdapterInterface $dbAdapter, Identity $identity = null)
    {
        parent::__construct($config, $dbAdapter, $identity);

        $this->facebookTable = new FacebookTable($this->dbAdapter);

        $appId     = $this->config[FacebookMethod::METHOD_CONFIG_APP_ID];
        $appSecret = $this->config[FacebookMethod::METHOD_CONFIG_APP_SECRET];
        $appScope  = $this->config[FacebookMethod::METHOD_CONFIG_APP_SCOPE];

        $this->facebookClient = new FacebookClient($appId, $appSecret, $appScope);

        if ($identity) {
            $this->_loadLocalProfile();
            $this->facebookClient->setAccessToken($identity->getProfile()[FacebookTable::COLUMN_ACCESS_TOKEN]);
        }
    }

    function setAccessToken($token)
    {
        $this->facebookClient->setAccessToken($token);
    }

    function getAccessToken()
    {
        return $this->facebookClient->getAccessToken();
    }

    public function verify()
    {
        $facebookData = null;

        try {

            $facebookData = $this->facebookClient->getFacebookProfile();

        } catch (FacebookException $e) {

            if ($e->getCode() == FacebookException::ERROR_CODE_ACCESS_TOKEN_EXPIRED) {
                return false;
            } else {
                throw $e;
            }

        } catch (Exception $e) {
            throw $e;
        }

        if (!$facebookData)
            return false;

        $identityProfile = $this->identity->getProfile();

        if (!$identityProfile)
            return false;

        $facebookId = $facebookData[FacebookMethod::FACEBOOK_PROFILE_ID_NAME];

        return $facebookId == $identityProfile[FacebookTable::COLUMN_FACEBOOK_ID];
    }

    function pokeIdentity(Identity $identity)
    {
        /** @var FacebookProfile $profile */
        $profileData = $identity->getProfile();

        $profileInstance = FacebookProfile::newFromArray($this->dbAdapter, $profileData, true);
        $profileInstance->setAccessToken($this->facebookClient->getAccessToken());
        $profileInstance->save();
    }

    private function _loadLocalProfile()
    {
        $result = $this->facebookTable->getByIdentityId($this->identity->getId());
        $this->identity->setProfile($result->getArrayCopy());
    }
}