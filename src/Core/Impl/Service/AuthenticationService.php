<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 2:23 PM
 */

namespace CreativeDelta\User\Core\Impl\Service;


use CreativeDelta\User\Core\Domain\AuthenticationAdapterInterface;
use CreativeDelta\User\Core\Domain\Entity\Identity;
use CreativeDelta\User\Core\Impl\Exception\AuthenticationException;
use Zend\Db\Adapter\AdapterInterface;

class AuthenticationService extends \Zend\Authentication\AuthenticationService
{
    const CONFIG_AUTH_CONFIG = "authConfig";

    const CONFIG_FACEBOOK            = "facebook";
    const CONFIG_FACEBOOK_APP_ID     = "appId";
    const CONFIG_FACEBOOK_APP_SECRET = "appSecret";
    const CONFIG_FACEBOOK_APP_SCOPE  = "scope";

    const CONFIG_GOOGLE            = "google";
    const CONFIG_GOOGLE_APP_ID     = "appId";
    const CONFIG_GOOGLE_APP_SECRET = "appSecret";
    const CONFIG_GOOGLE_APP_SCOPE  = "scope";

    /** @var  array $config */
    protected $config;

    /** @var  AdapterInterface $dbAdapter */
    protected $dbAdapter;

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return AdapterInterface
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param AdapterInterface $dbAdapter
     */
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function hasIdentity()
    {
        $valid = parent::hasIdentity() && $this->_verifyIdentity();

        if (parent::hasIdentity() && !$valid) {
            $this->clearIdentity();
        }

        return $valid;
    }

    private function _verifyIdentity()
    {
        if ((!$identity = $this->getIdentity()) || !($identity instanceof Identity)) {
            return false;
        }

        if (!$this->adapter) {
            $adapterClass  = $identity->getAdapterClassName();
            $adapterConfig = $this->config[$adapterClass::METHOD_NAME];
            $this->adapter = new $adapterClass($adapterConfig, $this->getDbAdapter(), $identity);
        }

        if (!$this->adapter instanceof AuthenticationAdapterInterface) {
            throw new AuthenticationException(AuthenticationException::ERROR_CODE_UNKNOWN_IMPLEMENTATION_OF_ADAPTER);
        }

        return $this->adapter->verify();
    }

}