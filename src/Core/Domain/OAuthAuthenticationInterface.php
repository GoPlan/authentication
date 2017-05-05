<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 3/13/17
 * Time: 8:56 AM
 */

namespace CreativeDelta\User\Core\Domain;


use CreativeDelta\User\Core\Domain\Entity\AbstractProfile;

interface OAuthAuthenticationInterface
{

    /**
     * Use this method to receive URL for Facebook authentication. To initialize the sign-in sequence, redirect yourself to the URL.
     * Once sign-in step is completed, the user will be redirected back to the URL provided previously in $redirectUri.
     * You should have a controller::action catch this redirection. Then in this action, further activities can be arranged using returned the "code" and "state".
     *
     * @param $redirectUri
     * @param $state
     * @return string
     */
    public function makeAuthenticationUrl($redirectUri, $state = null);

    /**
     * Since service is only set to received [code] in the authentication response,
     * an extra step must be made in order to receive an access_token for your application.
     *
     * Use this method and provide it with the code (received from authentication) to get a token.
     *
     * @param $redirectUri
     * @param $code
     * @return $this
     */
    public function initAccessToken($redirectUri, $code);

    /**
     * @param string $fields // a comma separated string of profile fields to be retrieved
     * @return null|array
     */
    public function getProfileData($fields = null);

    /**
     * @return null|AbstractProfile
     */
    public function getStoredProfile();
}