<?php

namespace Blog\Controller;

use Blog\Entity\PostLanguage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use User\Service\UserService;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class DeleteController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $postLanguageRepository;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param EntityManager    $entityManager
     * @param EntityRepository $postLanguageRepository
     * @param UserService      $userService
     */
    public function __construct(
        EntityManager $entityManager,
        EntityRepository $postLanguageRepository,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->postLanguageRepository = $postLanguageRepository;
        $this->userService = $userService;
    }

    /**
     * @return JsonModel
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deletePostWithAjaxAction()
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
            $post = $this->postLanguageRepository->find($postRequest['id']);
            if (!$post) {
                return $jsonModelWarning;
            }

            $this->entityManager->remove($post);
            $this->entityManager->flush();

            return new JsonModel(
                [
                    'status' => 'SUCCESS',
                    'message' => 'Успешно изтрихте поста',
                ]
            );
        }

        return $jsonModelWarning;
    }
}
