<?php

namespace User\Controller;

use User\Service\UserService;
use Zend\Mvc\Controller\AbstractActionController;

class LogoutController extends AbstractActionController
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    public function indexAction()
    {
        $this->userService->logout();

        /** @var string $localization */
        $localization = $this->params()->fromRoute('localization');

        return $this->redirect()->toRoute(
            'blog',
            [
                'localization' => $localization,
            ]
        );
    }
}
