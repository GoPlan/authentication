<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/9/17
 * Time: 8:36 AM
 */

namespace CreativeDelta\User\Core\Domain;


use CreativeDelta\User\Core\Domain\Entity\Identity;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class OAuthAuthenticationAdapter implements AdapterInterface
{

    /**
     * @var UserIdentityServiceInterface
     */
    protected $identityService;

    /**
     * @var OAuthAuthenticationInterface
     */
    protected $oauthAuthenticationService;

    /**
     * GoogleAuthenticationAdapter constructor.
     * @param UserIdentityServiceInterface $identityService
     * @param OAuthAuthenticationInterface $oauthAuthenticationService
     */
    public function __construct(UserIdentityServiceInterface $identityService, OAuthAuthenticationInterface $oauthAuthenticationService)
    {
        $this->identityService            = $identityService;
        $this->oauthAuthenticationService = $oauthAuthenticationService;
    }

    public function authenticate()
    {
        $profile  = $this->oauthAuthenticationService->getLocalProfile();
        $identity = $profile ? $this->identityService->getIdentityById($profile->getIdentityId()) : null;

        if (!$identity)

            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, [Identity::CREDENTIAL_RESULT_MESSAGES[Result::FAILURE_IDENTITY_NOT_FOUND]]);

        else {

            if ($identity->getState() != Identity::STATE_ACTIVE)

                return new Result(Result::FAILURE_CREDENTIAL_INVALID, $identity, [Identity::CREDENTIAL_RESULT_MESSAGES[Result::FAILURE_CREDENTIAL_INVALID]]);

            else

                return new Result(Result::SUCCESS, $identity);
        }
    }
}