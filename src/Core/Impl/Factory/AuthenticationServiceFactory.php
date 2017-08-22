<?php
/**
 * Created by PhpStorm.
 *
 * Duc-Anh LE (ducanh.ke@gmail.com)
 *
 * User: ducanh-ki
 * Date: 5/8/17
 * Time: 9:53 AM
 */

namespace CreativeDelta\User\Core\Impl\Factory;


use CreativeDelta\User\Core\Impl\Service\AuthenticationService;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var array $appConfig */
        $appConfig = $container->get('config')[AuthenticationService::CONFIG_AUTH_CONFIG];

        /** @var AdapterInterface $dbAdapter $dbAdapter */
        $dbAdapter = $container->get(Adapter::class);

        /** @var AuthenticationService $service */
        $service = new AuthenticationService();
        $service->setConfig($appConfig);
        $service->setDbAdapter($dbAdapter);

        return $service;
    }

    /**
     * Keeping this method for backward compatibility
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AuthenticationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var array $appConfig */
        $appConfig = $serviceLocator->get('config')[AuthenticationService::CONFIG_AUTH_CONFIG];

        /** @var AdapterInterface $dbAdapter $dbAdapter */
        $dbAdapter = $serviceLocator->get(Adapter::class);

        /** @var AuthenticationService $service */
        $service = new AuthenticationService();
        $service->setConfig($appConfig);
        $service->setDbAdapter($dbAdapter);

        return $service;
    }
}