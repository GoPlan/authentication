<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/25/17
 * Time: 9:46 AM
 */

namespace CreativeDelta\User\Application\Controller\Factory;


use CreativeDelta\User\Application\Controller\GoogleController;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class GoogleControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter   = $container->get(Adapter::class);
        $authService = $container->get(AuthenticationService::class);
        $controller  = new GoogleController($dbAdapter, $authService);
        return $controller;
    }
}