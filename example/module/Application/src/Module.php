<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CreativeDelta\User\Application;


use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface ;

class Module implements ConsoleBannerProviderInterface,
    ConsoleUsageProviderInterface
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getConsoleBanner(Console $console)
    {
        return 'Reset password Module 0.0.1';
    }

    public function getConsoleUsage(Console $console)
    {
        return [
            'user resetrootpassword account newPass confirmNewPass' => 'Set new password for root user',

            ['account', 'User account'],
            ['newPass', 'New pass for root user'],
            ['confirmNewPass', 'Confirm new password for root user'],
        ];

    }

}
