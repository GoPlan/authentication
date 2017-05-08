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


use CreativeDelta\User\Core\Domain\AbstractAuthenticationAdapter;
use CreativeDelta\User\Core\Domain\Entity\Identity;
use Zend\Db\Adapter\AdapterInterface;

class FacebookAuthenticationAdapter extends AbstractAuthenticationAdapter
{
    const QUERY_ID    = "id";
    const METHOD_NAME = "facebook";

    /** @var  FacebookClient $facebookClient */
    protected $facebookClient;

    /** @var  FacebookTable $facebookTable */
    protected $facebookTable;


    public function __construct(array $config, AdapterInterface $dbAdapter, $identity = null)
    {
        parent::__construct($config, $dbAdapter, $identity);

        $this->facebookTable = new FacebookTable($this->dbAdapter);

        $appId     = $this->config['appId'];
        $appSecret = $this->config['appSecret'];
        $appScope  = $this->config['appScope'];

        $this->facebookClient = new FacebookClient($appId, $appSecret, $appScope);

        $this->loadIdentityProfile();
    }

    public function isCurrent()
    {

        $oauthProfile = $this->facebookClient->getProfileData(self::QUERY_ID);

        if (!$oauthProfile)
            return false;

        $identityProfile = $this->identity->getProfile();

        if (!$identityProfile)
            return false;

        $facebookId = $oauthProfile[self::QUERY_ID];

        return $facebookId == $identityProfile['userId'];
    }

    public function setAccessToken($token)
    {
        $this->facebookClient->setAccessToken($token);
    }

    public function isActive()
    {
        return true;
    }

    function pokeIdentity(Identity $identity)
    {
        /** @var FacebookProfile $profile */
        $profileData = $identity->getProfile();

        $profileInstance = FacebookProfile::newFromArray($this->dbAdapter, $profileData, true);
        $profileInstance->setAccessToken($this->facebookClient->getAccessToken());
        $profileInstance->save();
    }

    private function loadIdentityProfile()
    {
        if (!$this->identity)
            return;

        $result = $this->facebookTable->getByIdentityId($this->identity->getId());
        $this->identity->setProfile($result->getArrayCopy());
    }
}