<?php

namespace Blog\Controller;

use Blog\Entity\Post;
use Blog\Entity\PostRepository;
use User\Service\UserService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ListController extends AbstractActionController
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        PostRepository $postRepository,
        UserService $userService
    ) {
        $this->postRepository = $postRepository;
        $this->userService = $userService;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $localization = $this->params()->fromRoute('localization');

        return new ViewModel(
            [
                'posts' => $this->postRepository->findAll(),
                'localization' => $localization,
                'isUserLogged' => $this->userService->isLogged(),
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function detailAction()
    {
        $id = $this->params()->fromRoute('id');
        $localization = $this->params()->fromRoute('localization');

        try {
            /** @var Post $post */
            $post = $this->postRepository->find($id);
        } catch (\InvalidArgumentException $ex) {
            return $this->redirect()->toRoute('blog');
        }

        return new ViewModel(
            [
                'post' => $post,
                'localization' => $localization,
            ]
        );
    }
}
