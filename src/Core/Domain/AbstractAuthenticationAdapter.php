<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/8/17
 * Time: 10:20 AM
 */

namespace CreativeDelta\User\Core\Domain;


use CreativeDelta\User\Core\Domain\Entity\Identity;
use Zend\Authentication\Result;
use Zend\Db\Adapter\AdapterInterface;

abstract class AbstractAuthenticationAdapter implements \Zend\Authentication\Adapter\AdapterInterface
{
    /**
     * $METHOD_NAME is used for identifying which deriving method (adapter) is being used.
     * It is useful for situation such as loading config where specific method needs to be provide.
     * This field is supposed to be overridden by deriving classes.
     */
    const METHOD_NAME = null;

    /**
     * @var  array
     */
    protected $config;

    /**
     * @var  Identity
     */
    protected $identity;

    /**
     * @var  AdapterInterface
     */
    protected $dbAdapter;

    /**
     * FacebookAuthenticationAdapter constructor.
     * @param array            $config
     * @param AdapterInterface $dbAdapter
     * @param Identity|null    $identity
     */
    public function __construct(array $config, AdapterInterface $dbAdapter, Identity $identity = null)
    {
        $this->config    = $config;
        $this->dbAdapter = $dbAdapter;
        $this->identity  = $identity;

        if ($this->identity)
            $this->identity->setAdapterClassName(static::class);
    }

    static function newFromConfig(array $config, AdapterInterface $dbAdapter, Identity $identity = null)
    {
        $methodConfig   = $config[self::METHOD_NAME];
        $methodInstance = new static($methodConfig, $dbAdapter, $identity);
        return $methodInstance;
    }

    /**
     * This abstract function is called each time a user is authenticated successfully.
     * It is used to renew (User) Identity information such as new access token, new logged-in time, etc.
     * Therefore, deriving classes will define behaviours of this function.
     *
     * @return void
     */
    abstract function pokeIdentity();

    /**
     * This method provides another mechanism for re-verifying an Identity.
     * It is additionally called by AuthenticationService::hasIdentity().
     * Since authentication OAuth token can expire or mis-provided,
     * this method is defined and called to make sure current Identity is valid.
     *
     * @return bool
     */
    abstract function verifyIdentity();

    /**
     * @return Result
     */
    public function authenticate()
    {

        if (!$this->identity) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null);
        }

        if (!($this->identity->getState() == Identity::STATE_ACTIVE)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, [Identity::CREDENTIAL_RESULT_MESSAGES[Result::FAILURE_CREDENTIAL_INVALID]]);
        }

        $this->pokeIdentity();

        return new Result(Result::SUCCESS, $this->identity);
    }
}