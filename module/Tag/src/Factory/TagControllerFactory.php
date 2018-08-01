<?php

namespace Tag\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Tag\Controller\TagController;
use Tag\Entity\Tag;
use Tag\Entity\TagRepository;
use Tag\Form\TagForm;
use User\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class TagControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TagController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formManager = $container->get('FormElementManager');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        $serviceManager = $container->get(ServiceManager::class);

        /** @var TagRepository $tagRepository */
        $tagRepository = $entityManager->getRepository(Tag::class);

        return new TagController(
            $entityManager,
            $tagRepository,
            $formManager->get(TagForm::class),
            $serviceManager->get(UserService::class)
        );
    }
}
