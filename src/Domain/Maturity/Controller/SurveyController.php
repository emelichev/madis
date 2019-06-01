<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Maturity\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Maturity\Calculator\MaturityHandler;
use App\Domain\Maturity\Form\Type\SurveyType;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SurveyController extends CRUDController
{
    /**
     * @var Repository\Question
     */
    private $questionRepository;

    /**
     * @var WordHandler
     */
    private $wordHandler;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var MaturityHandler
     */
    protected $maturityHandler;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Survey $repository,
        Repository\Question $questionRepository,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        MaturityHandler $maturityHandler
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->questionRepository   = $questionRepository;
        $this->wordHandler          = $wordHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
        $this->maturityHandler      = $maturityHandler;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'maturity';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'survey';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Survey::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return SurveyType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $order = [
            'createdAt' => 'DESC',
        ];

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->repository->findAll($order);
        }

        $data = $this->repository->findAllByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            $order
        );

        return $data;
    }

    /**
     * Some actions made before object persistence.
     * Here, we wanna calculate maturity score.
     *
     * @param Model\Survey $object
     */
    public function formPrePersistData($object)
    {
        $this->maturityHandler->handle($object);
    }

    /**
     * {@inheritdoc}
     * Override method in order to hydrate survey answers.
     */
    public function createAction(Request $request): Response
    {
        /**
         * @var Model\Survey
         */
        $modelClass = $this->getModelClass();
        $object     = new $modelClass();

        // Before create form, hydrate answers array with potential question responses
        foreach ($this->questionRepository->findAll() as $question) {
            $answer = new Model\Answer();
            $answer->setQuestion($question);
            $object->addAnswer($answer);
        }

        $form = $this->createForm($this->getFormType(), $object);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Generate a word report of survey.
     * Get current survey and previous one.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return Response
     */
    public function reportAction(string $id): Response
    {
        $data        = [];
        $data['new'] = $this->repository->findOneById($id);

        $oldObjects = $this->repository->findPreviousById($id, 1);
        if (!empty($oldObjects)) {
            $data['old'] = $oldObjects[0];
        }

        return $this->wordHandler->generateMaturitySurveyReport($data);
    }
}
