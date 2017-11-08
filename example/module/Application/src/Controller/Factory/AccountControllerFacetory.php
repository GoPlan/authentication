<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 13:24
 */

namespace CreativeDelta\User\Application\Controller\Factory;


use CreativeDelta\User\Application\Controller\AccountController;
use CreativeDelta\User\Core\Domain\AccountServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\Adapter;

class AccountControllerFacetory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // TODO: Implement __invoke() method.
        $authService = $container->get(AuthenticationService::class);
        $AccountServiceInterface = $container->get(AccountServiceInterface::class);
        $controller  = new AccountController($authService,$AccountServiceInterface);
        return $controller;

    }
}