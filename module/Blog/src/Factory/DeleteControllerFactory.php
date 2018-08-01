<?php

namespace Blog\Factory;

use Blog\Controller\DeleteController;
use Blog\Entity\Post;
use Blog\Entity\PostRepository;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use User\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class DeleteControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return DeleteController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        /** @var PostRepository $postRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var ServiceManager $serviceManager */
        $serviceManager = $container->get(ServiceManager::class);

        return new DeleteController(
            $entityManager,
            $postRepository,
            $serviceManager->get(UserService::class)
        );
    }
}
