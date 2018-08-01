<?php

namespace Blog\Factory;

use Application\Hydrator\EntityFormHydrator;
use Blog\Entity\Language;
use Blog\Entity\LanguageRepository;
use Blog\Form\PostForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Tag\Entity\Tag;
use Tag\Entity\TagRepository;
use Zend\ServiceManager\Factory\FactoryInterface;

class PostFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return PostForm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** * @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        /** @var LanguageRepository $languageRepository */
        $languageRepository = $entityManager->getRepository(Language::class);

        /** @var TagRepository $tagRepository */
        $tagRepository = $entityManager->getRepository(Tag::class);

        return new PostForm(
            new EntityFormHydrator(
                $container->get(EntityManager::class)
            ),
            $entityManager,
            $languageRepository,
            $tagRepository
        );
    }
}
