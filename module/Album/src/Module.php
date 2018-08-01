<?php

namespace Album;

use Album\Model\AlbumTableGateway;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getControllerConfig(): array
    {
        return [
            'factories' => [
                Controller\AlbumController::class => function ($container) {
                    return new Controller\AlbumController(
                        $container->get(Model\AlbumTable::class)
                    );
                },
            ],
        ];
    }

    /**
     * @return array
     */
    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                Model\AlbumTable::class => function (ContainerInterface $container) {
                    $tableGateway = $container->get(Model\AlbumTableGateway::class);
                    return new Model\AlbumTable($tableGateway);
                },
                Model\AlbumTableGateway::class => function (ContainerInterface $container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Album());
                    return new AlbumTableGateway('albums', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }
}
