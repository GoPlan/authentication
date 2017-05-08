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

abstract class AbstractAuthenticationAdapter implements AuthenticationAdapterInterface
{
    const METHOD_NAME = null;

    /** @var  array $config */
    protected $config;

    /** @var  Identity $identity */
    protected $identity;

    /** @var  AdapterInterface $dbAdapter */
    protected $dbAdapter;

    /**
     * FacebookAuthenticationAdapter constructor.
     * @param array $config
     * @param AdapterInterface $dbAdapter
     * @param Identity|null $identity
     */
    public function __construct(array $config, AdapterInterface $dbAdapter, Identity $identity = null)
    {
        $this->config    = $config;
        $this->dbAdapter = $dbAdapter;
        $this->identity  = $identity;

        if ($this->identity)
            $this->identity->setAdapterClassName(static::class);
    }

    /**
     * @return Result
     */
    public function authenticate()
    {

        if (!$this->identity) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null);
        }

        if (!($this->identity->getState() == Identity::STATE_ACTIVE)) {
            return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                [Identity::CREDENTIAL_RESULT_MESSAGES[Result::FAILURE_CREDENTIAL_INVALID]]);
        }

        $this->pokeIdentity($this->identity);

        return new Result(Result::SUCCESS, $this->identity);
    }

    abstract function pokeIdentity(Identity $identity);

    static function newFromConfig(array $config, AdapterInterface $dbAdapter, Identity $identity = null)
    {
        $methodConfig = $config[self::METHOD_NAME];
        $instance     = new static($methodConfig, $dbAdapter, $identity);
        return $instance;
    }
}