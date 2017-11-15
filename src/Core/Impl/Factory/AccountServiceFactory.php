<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 16:41
 */

namespace CreativeDelta\User\Core\Impl\Factory;


use CreativeDelta\User\Core\Impl\Service\AccountService;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter = $container->get(Adapter::class);
        $service   = new AccountService($dbAdapter);
        return $service;
    }
}