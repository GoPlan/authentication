<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 11/9/17
 * Time: 3:01 PM
 */

namespace CreativeDelta\User\Core\Impl\Factory;


use CreativeDelta\User\Core\Impl\Service\UserIdentityService;
use CreativeDelta\User\Core\Impl\Table\UserIdentityTable;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserIdentityServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter           = $container->get(Adapter::class);
        $userIdentityTable   = new UserIdentityTable($dbAdapter);
        $userIdentityService = new UserIdentityService($userIdentityTable);
        return $userIdentityService;
    }
}