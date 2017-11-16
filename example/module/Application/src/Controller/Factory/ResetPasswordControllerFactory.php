<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 13/11/2017
 * Time: 16:51
 */

namespace CreativeDelta\User\Application\Controller\Factory;


use CreativeDelta\User\Application\Controller\ResetPasswordController;
use CreativeDelta\User\Core\Impl\Service\AccountService;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class ResetPasswordControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter      = $container->get(Adapter::class);
        $AccountService = $container->get(AccountService::class);
        $controller     = new ResetPasswordController($dbAdapter, $AccountService);
        return $controller;
    }
}