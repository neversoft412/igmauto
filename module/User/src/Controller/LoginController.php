<?php

namespace User\Controller;

use User\Form\LoginForm;
use User\Service\UserService;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController
{
    /**
     * @var LoginForm
     */
    private $form;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        LoginForm $form,
        UserService $userService
    ) {
        $this->form = $form;
        $this->userService = $userService;
    }

    public function indexAction()
    {
        $localization = $this->params()->fromRoute('localization');

        if ($this->userService->isLogged()) {
            return $this->redirect()->toRoute(
                'blog',
                [
                    'localization' => $localization,
                ]
            );
        }

        /** @var Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);

            if ($this->form->isValid()) {
                $logInUser = $this->userService->logIn(
                    $postData[LoginForm::USERNAME],
                    $postData[LoginForm::PASSWORD],
                    $postData[LoginForm::REMEMBER_ME]
                );

                if ($logInUser) {
                    return $this->redirect()->toRoute(
                        'blog',
                        [
                            'localization' => $localization,
                        ]
                    );
                }

                $username = $this->form->get(LoginForm::USERNAME);
                $password = $this->form->get(LoginForm::PASSWORD);

                $username->setMessages(
                    [
                        'Invalid username',
                    ]
                );

                $password->setMessages(
                    [
                        'Invalid password',
                    ]
                );
            }
        }

        return new ViewModel(
            [
                'form' => $this->form,
            ]
        );
    }
}
