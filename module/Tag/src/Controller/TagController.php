<?php

namespace Tag\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Tag\Entity\Tag;
use Tag\Entity\TagRepository;
use Tag\Form\TagForm;
use User\Service\UserService;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class TagController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @var TagForm
     */
    private $form;

    /**
     * @var UserService
     */
    private $userAuthentication;

    public function __construct(
        EntityManager $entityManager,
        TagRepository $tagRepository,
        TagForm $form,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->tagRepository = $tagRepository;
        $this->form = $form;
        $this->userAuthentication = $userService;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $localization = $this->params()->fromRoute('localization');
        return new ViewModel(
            [
                'form' => $this->form,
                'tags' => $this->tagRepository->findAll(),
                'localization' => $localization,
                'isUserLogged' => $this->userAuthentication->isLogged(),
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function viewAction()
    {
        $localization = $this->params()->fromRoute('localization');

        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute(
                'tag',
                [
                    'localization' => $localization,
                ]
            );
        }

        /** @var Tag $tag */
        $tag = $this->tagRepository->find($id);
        if (!$tag) {
            return $this->redirect()->toRoute(
                'tag',
                [
                    'localization' => $localization,
                ]
            );
        }

        return new ViewModel(
            [
                'tag' => $tag,
                'localization' => $localization,
                'isUserLogged' => $this->userAuthentication->isLogged(),
            ]
        );
    }

    /**
     * @return JsonModel
     * @throws \Exception
     */
    public function addTagWithAjaxAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $jsonResponse = [
            'status' => 'WARNING',
            'message' => 'Възникна грешка! Моля опитайте отново',
        ];

        if ($request->isXmlHttpRequest()) {
            /** @var Tag $tag */
            $tag = new Tag();
            $postRequest = $request->getPost();

            $this->form->bind($tag);
            $this->form->setData($postRequest);

            if ($this->form->isValid()) {
                try {
                    $this->entityManager->persist($tag);
                    $this->entityManager->flush();
                } catch (\Exception $ex) {
                    throw $ex;
                }

                $jsonResponse = [
                    'status' => 'SUCCESS',
                    'message' => 'Успешно добавихте тага',
                ];
            } else {
                $jsonResponse = [
                    'status' => 'ERROR',
                    'messages' => $this->form->getMessages(),
                ];
            }
        }

        return new JsonModel($jsonResponse);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        $localization = $this->params()->fromRoute('localization');

        if (!$this->userAuthentication->isLogged()) {
            return $this->redirect()->toRoute(
                'tag',
                [
                    'localization' => $localization,
                ]
            );
        }

        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute(
                'tag',
                [
                    'localization' => $localization,
                ]
            );
        }

        /** @var Tag $tag */
        $tag = $this->tagRepository->find($id);
        if (!$tag) {
            return $this->redirect()->toRoute(
                'tag',
                [
                    'localization' => $localization,
                ]
            );
        }

        $this->form->bind($tag);

        return new ViewModel(
            [
                'form' => $this->form,
                'tags' => $this->tagRepository->findAll(),
                'localization' => $localization,
                'isUserLogged' => $this->userAuthentication->isLogged(),
            ]
        );
    }

    /**
     * @return JsonModel
     * @throws \Exception
     */
    public function editTagWithAjaxAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $jsonResponse = [
            'status' => 'WARNING',
            'message' => 'Възникна грешка! Моля опитайте отново',
        ];

        if ($request->isXmlHttpRequest()) {
            $postRequest = $request->getPost();
            $id = $postRequest[TagForm::ID];

            if ($id) {
                /** @var Tag $tag */
                $tag = $this->tagRepository->find($id);

                if ($tag) {
                    $this->form->bind($tag);
                    $this->form->setData($postRequest);

                    if ($this->form->isValid()) {
                        try {
                            $this->entityManager->flush();
                        } catch (\Exception $ex) {
                            throw $ex;
                        }

                        $jsonResponse = [
                            'status' => 'SUCCESS',
                            'message' => 'Успешно редактирахте тага',
                        ];
                    } else {
                        $jsonResponse = [
                            'status' => 'ERROR',
                            'messages' => $this->form->getMessages(),
                        ];
                    }
                }
            }
        }

        return new JsonModel($jsonResponse);
    }

    public function deleteTagWithAjaxAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $jsonResponse = [
            'status' => 'WARNING',
            'message' => 'Възникна грешка! Моля опитайте отново',
        ];

        if ($request->isXmlHttpRequest()) {
            $postRequest = $request->getPost();
            $id = $postRequest[TagForm::ID];

            if ($id) {
                /** @var Tag $tag */
                $tag = $this->tagRepository->find($id);

                if ($tag) {
                    try {
                        $this->entityManager->remove($tag);
                        $this->entityManager->flush();
                    } catch (ORMException $e) {
                    }

                    $jsonResponse = [
                        'status' => 'SUCCESS',
                        'message' => 'Успешно изтрихте тага',
                    ];
                }
            }
        }

        return new JsonModel($jsonResponse);
    }
}
