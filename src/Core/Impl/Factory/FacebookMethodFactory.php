<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 11/9/17
 * Time: 3:21 PM
 */

namespace CreativeDelta\User\Core\Impl\Factory;


use CreativeDelta\User\Facebook\FacebookMethod;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class FacebookMethodFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter = $container->get(Adapter::class);
        $config    = $container->get("config")["authConfig"]["facebook"];
        $method    = new FacebookMethod($dbAdapter, $config);
        return $method;
    }
}