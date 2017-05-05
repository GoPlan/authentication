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


use CreativeDelta\User\Core\Domain\AuthenticationAdapterInterface;
use CreativeDelta\User\Core\Domain\OAuthAuthenticationInterface;

class GoogleAuthenticationAdapter implements AuthenticationAdapterInterface
{
    /**
     * @var array $data
     */
    protected $data;

    /**
     * @var GoogleTable $googleUserTable
     */
    protected $googleUserTable;

    /**
     * @var \Google_Client $googleClient
     */
    protected $googleClient;

    /**
     * @var OAuthAuthenticationInterface $oauthAuthenticationService
     */
    protected $oauthAuthenticationService;


    public function authenticate()
    {
        

        // TODO: Implement authenticate() method.
    }

    public function hasExpired()
    {
        // TODO: Implement hasExpired() method.
    }

}