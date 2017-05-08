<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace CreativeDelta\User\Core\Domain;


use Zend\Authentication\Adapter\AdapterInterface;

interface AuthenticationAdapterInterface extends AdapterInterface
{

    /**
     * Check if user is current to OAuth user
     *
     * @return bool
     */
    public function isCurrent();

    /**
     * Check if token is still active (not expired)
     *
     * @return bool
     */
    public function isActive();
}