<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/24/17
 * Time: 11:57 AM
 */

namespace Application\Controller\Factory;


use Application\Controller\FacebookController;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class FacebookControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter   = $container->get(Adapter::class);
        $authService = $container->get(AuthenticationService::class);
        $controller  = new FacebookController($dbAdapter, $authService);
        return $controller;
    }
}