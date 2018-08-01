<?php

namespace Tag\Factory;


use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Interop\Container\ContainerInterface;
use Tag\Form\TagForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class TagFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TagForm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        return new TagForm(
            new DoctrineObject($entityManager),
            $entityManager
        );
    }
}
