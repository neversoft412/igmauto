<?php

namespace Application\Factory;

use Acl\Service\AclService;
use Interop\Container\ContainerInterface;
use User\Service\UserService;
use Zend\Permissions\Acl\Acl;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\Navigation;
use Zend\View\HelperPluginManager;

class RiznNavigationFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Navigation
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $container->get(ServiceManager::class);

        /** @var UserService $userService */
        $userService = $serviceManager->get(UserService::class);

        /** @var AclService $aclService */
        $aclService = $serviceManager->get(AclService::class);

        /** @var HelperPluginManager $helperPluginManager */
        $helperPluginManager = $container->get('ViewHelperManager');

        /** @var Navigation $navigation */
        $navigation = $helperPluginManager->get(Navigation::class);

        /** @var Acl $acl */
        $acl = $aclService->getAcl();

        $navigation->setAcl($acl);

        $currentRoles = $userService->getCurrentRoles();

        $navigation->setRole($currentRoles[0]);

        return $navigation;
    }
}
