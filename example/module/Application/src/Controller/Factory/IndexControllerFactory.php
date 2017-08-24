<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/24/17
 * Time: 12:34 PM
 */

namespace CreativeDelta\User\Application\Controller\Factory;


use CreativeDelta\User\Application\Controller\IndexController;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        $controller  = new IndexController($authService);
        return $controller;
    }
}