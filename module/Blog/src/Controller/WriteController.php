<?php

namespace Blog\Controller;

use Blog\Entity\LanguageRepository;
use Blog\Entity\Post;
use Blog\Entity\PostLanguage;
use Blog\Form\PostForm;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use User\Service\UserService;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class WriteController extends AbstractActionController
{
    /**
     * @var PostForm
     */
    private $form;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PostLanguage
     */
    private $postRepository;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param PostForm           $form
     * @param EntityManager      $entityManager
     * @param EntityRepository   $postRepository
     * @param LanguageRepository $languageRepository
     * @param UserService        $userService
     */
    public function __construct(
        PostForm $form,
        EntityManager $entityManager,
        EntityRepository $postRepository,
        LanguageRepository $languageRepository,
        UserService $userService
    ) {
        $this->form = $form;
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
        $this->languageRepository = $languageRepository;
        $this->userService = $userService;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function addAction()
    {
        /** @var string $localization */
        $localization = $this->params()->fromRoute('localization');

        if (!$this->userService->isLogged()) {
            return $this->redirect()->toRoute(
                'blog',
                [
                    'localization' => $localization,
                ]
            );
        }

        return new ViewModel(
            [
                'form' => $this->form,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|JsonModel
     * @throws Exception
     */
    public function addPostWithAjaxAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $jsonModel = [
            'status' => 'WARNING',
            'message' => 'Възникна грешка! Моля опитайте отново',
        ];

        if ($request->isXmlHttpRequest()) {
            /** @var Post $post */
            $post = new Post($this->languageRepository->findAll());
            $post->setDateAdded(new \DateTime('now'));

            $this->form->bind($post);
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                try {
                    $this->entityManager->persist($post);
                    $this->entityManager->flush();
                } catch (\Exception $ex) {
                    throw $ex;
                }

                $jsonModel = [
                    'status' => 'SUCCESS',
                    'message' => 'Успешно добавихте пост',
                    'data' => [
                        'id' => $post->getId(),
                    ],
                ];
            } else {
                $jsonModel = [
                    'status' => 'ERROR',
                    'messages' => $this->form->getMessages(),
                ];
            }
        }

        return new JsonModel($jsonModel);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws Exception
     */
    public function editAction()
    {
        /** @var string $localization */
        $localization = $this->params()->fromRoute('localization');

        $redirectToBlog = $this->redirect()->toRoute(
            'blog',
            [
                'localization' => $localization,
            ]
        );

        if (!$this->userService->isLogged()) {
            return $redirectToBlog;
        }

        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $redirectToBlog;
        }

        /** @var Post $post */
        $post = $this->postRepository->find($id);
        if (!$post) {
            return $redirectToBlog;
        }

        $this->form->remove(PostForm::CAPTCHA);
        $this->form->bind($post);

        return new ViewModel(
            [
                'form' => $this->form,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|JsonModel
     * @throws Exception
     */
    public function editPostWithAjaxAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $jsonModelWarning = new JsonModel(
            [
                'status' => 'WARNING',
                'message' => 'Възникна грешка! Моля опитайте отново',
            ]
        );

        if ($request->isXmlHttpRequest()) {
            $postRequest = $request->getPost();
            if (!$postRequest['id']) {
                return $jsonModelWarning;
            }

            /** @var PostLanguage $post */
            $post = $this->postRepository->find($postRequest['id']);
            if (!$post) {
                return $jsonModelWarning;
            }

            $this->form->bind($post);
            $this->form->remove(PostForm::CAPTCHA);
            $this->form->setData($postRequest);

            if (!$this->form->isValid()) {
                return new JsonModel(
                    [
                        'status' => 'ERROR',
                        'messages' => $this->form->getMessages(),
                    ]
                );
            }

            try {
                $this->entityManager->flush();
            } catch (\Exception $ex) {
                throw $ex;
            }

            return new JsonModel(
                [
                    'status' => 'SUCCESS',
                    'message' => 'Успешно редактирахте поста',
                    'data' => [
                        'id' => $post->getId(),
                    ],
                ]
            );
        }

        return $jsonModelWarning;
    }
}
