<?php
/**
 * Created by PhpStorm.
 * User: pear
 * Date: 5/18/18
 * Time: 10:33 AM
 */

namespace Blog\Factory;

use Application\Hydrator\EntityFormHydrator;
use Blog\Form\PostLanguageFieldset;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PostLanguageFieldsetFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return PostLanguageFieldset
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PostLanguageFieldset(
            new EntityFormHydrator(
                $container->get(EntityManager::class)
            )
        );
    }
}
