<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 11/7/17
 * Time: 4:16 PM
 */

namespace CreativeDelta\User\Core\Impl\Factory;


use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Zend\Session\SessionManager;

class SessionManagerFactory implements FactoryInterface
{
    const SESSION_SAVE_HANDLER_TABLE = "user_session_savehandler";

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter      = $container->get(Adapter::class);
        $tableGateway = new TableGateway(self::SESSION_SAVE_HANDLER_TABLE, $adapter);
        $saveHandler  = new DbTableGateway($tableGateway, new DbTableGatewayOptions());
        $manager      = new SessionManager(null, null, $saveHandler);

        Container::setDefaultManager($manager);

        return $manager;
    }
}