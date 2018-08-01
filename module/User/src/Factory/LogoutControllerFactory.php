<?php

namespace User\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\LogoutController;
use User\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class LogoutControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $container->get(ServiceManager::class);

        return new LogoutController(
            $serviceManager->get(UserService::class)
        );
    }
}
