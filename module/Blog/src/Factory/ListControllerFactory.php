<?php

namespace Blog\Factory;

use Blog\Controller\ListController;
use Blog\Entity\Post;
use Blog\Entity\PostRepository;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use User\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class ListControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return ListController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        /** @var PostRepository $postRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var ServiceManager $serviceManager */
        $serviceManager = $container->get(ServiceManager::class);

        return new ListController(
            $postRepository,
            $serviceManager->get(UserService::class)
        );
    }
}
