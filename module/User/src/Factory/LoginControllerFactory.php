<?php

namespace User\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\LoginController;
use User\Form\LoginForm;
use User\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formManager = $container->get('FormElementManager');

        /** @var ServiceManager $serviceManager */
        $serviceManager = $container->get(ServiceManager::class);

        return new LoginController(
            $formManager->get(LoginForm::class),
            $serviceManager->get(UserService::class)
        );
    }
}
