<?php

namespace Acl\Service\Factory;


use Acl\Entity\Resource;
use Acl\Entity\Role;
use Acl\Service\AclService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AclServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        /** @var EntityRepository $roleRepository */
        $roleRepository = $entityManager->getRepository(Role::class);

        /** @var EntityRepository $resourceRepository */
        $resourceRepository = $entityManager->getRepository(Resource::class);

        return new AclService(
            $roleRepository,
            $resourceRepository
        );
    }
}
