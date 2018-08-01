<?php

namespace User\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use User\Entity\User;
use User\Entity\UserRepository;
use User\Service\UserService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class UserServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        /** @var ServiceManager $serviceManager */
        $serviceManager = $container->get(ServiceManager::class);

        /** @var Application $application */
        $application = $serviceManager->get('Application');

        $response = $application->getResponse();

        $headers = null;
        if ($response instanceof Response) {
            $headers = $response->getHeaders();
        }

        $request = null;
        if ($application->getRequest() instanceof Request){
            /** @var Request $request */
            $request = $application->getRequest();
        }

        return new UserService(
            $entityManager,
            $userRepository,
            $container->get('UserSession'),
            $headers,
            $request
        );
    }
}
