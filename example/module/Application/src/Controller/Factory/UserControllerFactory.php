<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/24/17
 * Time: 3:41 PM
 */

namespace Application\Controller\Factory;


use Application\Controller\UserController;
use CreativeDelta\User\Core\Impl\Service\AuthenticationService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        $controller  = new UserController($authService);
        return $controller;
    }
}