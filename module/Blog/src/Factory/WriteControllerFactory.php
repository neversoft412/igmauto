<?php

namespace Blog\Factory;

use Blog\Controller\WriteController;
use Blog\Entity\Language;
use Blog\Entity\LanguageRepository;
use Blog\Entity\Post;
use Blog\Form\PostForm;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Interop\Container\ContainerInterface;
use User\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class WriteControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return WriteController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formManager = $container->get('FormElementManager');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        /** @var EntityRepository $postRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var LanguageRepository $languageRepository */
        $languageRepository = $entityManager->getRepository(Language::class);

        /** @var ServiceManager $serviceManager */
        $serviceManager = $container->get(ServiceManager::class);

        return new WriteController(
            $formManager->get(PostForm::class),
            $entityManager,
            $postRepository,
            $languageRepository,
            $serviceManager->get(UserService::class)
        );
    }
}
