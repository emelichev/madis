<?php

namespace App\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Form\Type\ConformiteOrganisation\EvaluationPiloteType;
use App\Domain\Registry\Form\Type\ConformiteOrganisation\EvaluationType;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use App\Domain\Registry\Repository\ConformiteOrganisation as Repository;
use App\Domain\Registry\Symfony\EventSubscriber\Event\ConformiteOrganisationEvent;
use App\Domain\Reporting\Handler\WordHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Evaluation $repository
 */
class ConformiteOrganisationController extends CRUDController
{
    /**
     * @var Repository\Question
     */
    private $questionRepository;

    /**
     * @var Repository\Processus
     */
    private $processusRepository;

    /**
     * @var Repository\Conformite
     */
    private $conformiteRepository;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var WordHandler
     */
    private $wordHandler;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Evaluation $repository,
        Repository\Question $questionRepository,
        Repository\Processus $processusRepository,
        Repository\Conformite $conformiteRepository,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $dispatcher,
        WordHandler $wordHandler
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->questionRepository     = $questionRepository;
        $this->processusRepository    = $processusRepository;
        $this->conformiteRepository   = $conformiteRepository;
        $this->userProvider           = $userProvider;
        $this->authorizationChecker   = $authorizationChecker;
        $this->dispatcher             = $dispatcher;
        $this->wordHandler            = $wordHandler;
    }

    public function createAction(Request $request): Response
    {
        $organisation = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $evaluation = $this->repository->findLastByOrganisation($organisation);
        if (null !== $evaluation) {
            $evaluation = clone $evaluation;
            $this->addMissingNewQuestionsAndProcessus($evaluation);
        } else {
            $evaluation = new Evaluation();

            foreach ($this->processusRepository->findAll(['position' => 'asc']) as $processus) {
                $conformite = new Conformite();
                $conformite->setProcessus($processus);
                foreach ($this->questionRepository->findAllByProcessus($processus) as $question) {
                    $reponse = new Reponse();
                    $reponse->setConformite($conformite);
                    $reponse->setQuestion($question);
                    $conformite->addReponse($reponse);
                }
                $evaluation->addConformite($conformite);
            }
            $evaluation->setCollectivity($organisation);
        }

        $form = $this->createForm($this->getFormType(), $evaluation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($evaluation);
            $em->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $evaluation));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'form'      => $form->createView(),
        ]);
    }

    public function listConformitesAction(Request $request): Response
    {
        $form        = null;
        $isAdminView = $this->authorizationChecker->isGranted('ROLE_ADMIN');
        if (!$isAdminView) {
            $evaluations  = $this->repository->findAllByOrganisationOrderedByDate($this->userProvider->getAuthenticatedUser()->getCollectivity()->getId());
            if (!empty($evaluations)) {
                $form = $this->createForm(EvaluationPiloteType::class, $evaluations[0]);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    $this->addFlash('success', $this->getFlashbagMessage('success', 'pilote', $evaluations[0]));

                    return $this->redirectToRoute($this->getRouteName('list'));
                }
                $form = $form->createView();
            }
        } else {
            $evaluations  = $this->repository->findAllByOrganisationOrderedByDate();
        }

        return $this->render($this->getTemplatingBasePath('list'), [
            'evaluations' => $evaluations,
            'form'        => $form,
        ]);
    }

    public function editAction(Request $request, string $id): Response
    {
        /** @var Evaluation $evaluation */
        $evaluation = $this->repository->findOneById($id);
        if (!$evaluation) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->addMissingNewQuestionsAndProcessus($evaluation);

        $form = $this->createForm($this->getFormType(), $evaluation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formPrePersistData($evaluation);
            $this->entityManager->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'edit', $evaluation));

            $this->dispatcher->dispatch(new ConformiteOrganisationEvent($evaluation));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('edit'), [
            'form' => $form->createView(),
        ]);
    }

    public function reportAction(string $id)
    {
        $evaluation = $this->repository->findOneById($id);
        if (!$evaluation) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->wordHandler->generateRegistryConformiteOrganisationReport($evaluation);
    }

    private function addMissingNewQuestionsAndProcessus(Evaluation $evaluation)
    {
        foreach ($this->processusRepository->findNewNotUsedInGivenConformite($evaluation) as $processus) {
            $conformite = new Conformite();
            $conformite->setProcessus($processus);
            $evaluation->addConformite($conformite);
        }

        foreach ($evaluation->getConformites() as $conformite) {
            foreach ($this->questionRepository->findNewNotUsedByGivenConformite($conformite) as $question) {
                $reponse = new Reponse();
                $reponse->setQuestion($question);
                $conformite->addReponse($reponse);
            }
        }
    }

    protected function getDomain(): string
    {
        return 'registry';
    }

    protected function getModel(): string
    {
        return 'conformite_organisation';
    }

    protected function getModelClass(): string
    {
        return Evaluation::class;
    }

    protected function getFormType(): string
    {
        return EvaluationType::class;
    }
}