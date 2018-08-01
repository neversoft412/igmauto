<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Factory\RiznNavigationFactory;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param MvcEvent $mvcEvent
     */
    public function onBootstrap(MvcEvent $mvcEvent)
    {
        $container = $mvcEvent->getApplication()->getServiceManager();

        // Allow translation of validation messages
        $translator = $container->get('MvcTranslator');
        AbstractValidator::setDefaultTranslator($translator);

        $eventManager = $mvcEvent->getApplication()->getEventManager();
        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH,
            [
                $this,
                'initializeLocalization',
            ]
        );
    }

    /**
     * @param MvcEvent $mvcEvent
     *
     * @return mixed
     */
    public function initializeLocalization(MvcEvent $mvcEvent)
    {
        $routeMatch = $mvcEvent->getRouteMatch();
        $localization = $routeMatch->getParam('localization');

        $container = $mvcEvent->getApplication()->getServiceManager();

        $translator = $container->get('MvcTranslator');
        $translator->setLocale($localization);
    }

    /**
     * @return array
     */
    public function getViewHelperConfig()
    {
        return [
            'aliases' => [
                'navigation' => 'rizn.navigation',
            ],
            'factories' => [
                'rizn.navigation' => RiznNavigationFactory::class,
            ],
        ];
    }
}
