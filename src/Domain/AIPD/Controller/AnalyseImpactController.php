<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\AIPD\Form\Flow\AnalyseImpactFlow;
use App\Domain\AIPD\Form\Type\AnalyseImpactType;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\AIPD\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Gaufrette\Filesystem;
use Knp\Snappy\Pdf;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\AnalyseImpact $repository
 */
class AnalyseImpactController extends CRUDController
{
    use ServersideDatatablesTrait;

    private RouterInterface $router;
    private RequestStack $requestStack;
    private $modeleRepository;
    private AnalyseImpactFlow $analyseFlow;
    private Filesystem $fichierFilesystem;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\AnalyseImpact $repository,
        Pdf $pdf,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        RouterInterface $router,
        RequestStack $requestStack,
        Repository\ModeleAnalyse $modeleRepository,
        AnalyseImpactFlow $analyseFlow,
        Filesystem $fichierFilesystem
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->router                  = $router;
        $this->requestStack            = $requestStack;
        $this->modeleRepository        = $modeleRepository;
        $this->analyseFlow             = $analyseFlow;
        $this->fichierFilesystem       = $fichierFilesystem;
    }

    protected function getDomain(): string
    {
        return 'aipd';
    }

    protected function getModel(): string
    {
        return 'analyse_impact';
    }

    protected function getModelClass(): string
    {
        return AnalyseImpact::class;
    }

    protected function getFormType(): string
    {
        return AnalyseImpactType::class;
    }

    public function listAction(): Response
    {
        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count(),
            'route'     => $this->router->generate('aipd_analyse_impact_datatables'),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $request = $this->requestStack->getMasterRequest();

        $analyses = $this->getResults($request);

        $response = $this->getBaseDataTablesResponse($request, $analyses);
        /** @var AnalyseImpact $analyse */
        foreach ($analyses as $analyse) {
            $response['data'][] = [
                'traitement'       => '<a href="' . $this->router->generate('registry_treatment_show', ['id' => $analyse->getConformiteTraitement()->getTraitement()->getId()]) . '">' . $analyse->getConformiteTraitement()->getTraitement()->getName() . '</a>',
                'dateDeCreation'   => $analyse->getCreatedAt()->format('d/m/Y H:i'),
                'dateDeValidation' => null === $analyse->getDateValidation() ? '' : $analyse->getDateValidation()->format('d/m/Y H:i'),
                'modele'           => $analyse->getModeleAnalyse(),
                'actions'          => $this->generateActionCell($analyse),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($response));

        return $jsonResponse;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'traitement',
            1 => 'dateDeCreation',
            2 => 'dateDeValidation',
            3 => 'modele',
            4 => 'actions',
        ];
    }

    private function generateActionCell(AnalyseImpact $analyseImpact): string
    {
        $cell = '<a href="' . $this->router->generate('aipd_analyse_impact_print', ['id' => $analyseImpact->getId()]) . '">
        <i class="fa fa-print"></i>' .
            $this->translator->trans('action.print') . '
        </a>';
        $cell .= '<a href="' . $this->router->generate('aipd_analyse_impact_edit', ['id' => $analyseImpact->getId()]) . '">
        <i class="fa fa-pencil-alt"></i>' .
            $this->translator->trans('action.edit') . '
        </a>';

        return $cell;
    }

    /**
     * {@inheritdoc}
     * - Upload documentFile before object persistence in database.
     *
     * @throws \Exception
     */
    public function formPrePersistData($object)
    {
        if (!$object instanceof AnalyseImpact) {
            throw new \RuntimeException('You must persist a ' . AnalyseImpact::class . ' object class with your form');
        }

        foreach ($object->getCriterePrincipeFondamentaux() as $criterePrincipeFondamental) {
            $file = $criterePrincipeFondamental->getFichierFile();

            if ($file) {
                $filename = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
                $this->fichierFilesystem->write($filename, \fopen($file->getRealPath(), 'r'));
                $criterePrincipeFondamental->setFichier($filename);
                $criterePrincipeFondamental->setFichierFile(null);
            }
        }
    }

    /**
     * The creation action view
     * Create a new data.
     */
    public function createAnalyseAction(Request $request, string $id): Response
    {
        if (null === $object = $this->repository->findOneByIdWithoutInvisibleScenarios($id)) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->analyseFlow->bind($object);
        $form = $this->analyseFlow->createForm();

        if ($this->analyseFlow->isValid($form)) {
            $this->formPrePersistData($object);
            $this->analyseFlow->saveCurrentStepData($form);

            if ($this->analyseFlow->nextStep()) {
                $form = $this->analyseFlow->createForm();
            //TODO Persist and flush here to allow draft ?
            } else {
                $this->entityManager->persist($object);
                $this->entityManager->flush();

                $this->analyseFlow->reset();

                return $this->redirectToRoute($this->getRouteName('evaluation'), [
                    'id' => $id,
                ]);
            }
        }

        return $this->render($this->getTemplatingBasePath('create'), [
            'flow' => $this->analyseFlow,
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, string $id): Response
    {
        if (null === $object = $this->repository->findOneByIdWithoutInvisibleScenarios($id)) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $this->analyseFlow->bind($object);
        $form = $this->analyseFlow->createForm();

        if ($this->analyseFlow->isValid($form)) {
            $this->formPrePersistData($object);
            $this->analyseFlow->saveCurrentStepData($form);

            if ($this->analyseFlow->nextStep()) {
                $form = $this->analyseFlow->createForm();
            } else {
                $this->entityManager->persist($object);
                $this->entityManager->flush();

                $this->analyseFlow->reset();

                return $this->redirectToRoute($this->getRouteName('evaluation'), [
                    'id' => $id,
                ]);
            }
        }

        return $this->render($this->getTemplatingBasePath('edit'), [
            'flow' => $this->analyseFlow,
            'form' => $form->createView(),
        ]);
    }

    public function modelesDatatablesAction()
    {
        $request = $this->requestStack->getMasterRequest();

        $user = $this->userProvider->getAuthenticatedUser();

        $modeles = $this->getModeleResults($request);

        $reponse = $this->getBaseDataTablesResponse($request, $modeles);
        foreach ($modeles as $modele) {
            $reponse['data'][] = [
                'nom'         => '<input type="radio" value="' . $modele->getId() . '" name="modele_choice" required="true"/> ' . $modele->getNom(),
                'description' => $modele->getDescription(),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($reponse));

        return $jsonResponse;
    }

    public function evaluationAction(string $id)
    {
        if (null === $object = $this->repository->findOneById($id)) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('evaluation'), [
            'analyseImpact' => $object,
        ]);
    }

    protected function getModeleResults(Request $request): ?Paginator
    {
        $first      = $request->request->get('start');
        $maxResults = $request->request->get('length');
        $orders     = $request->request->get('order');
        $columns    = $request->request->get('columns');

        $orderColumn = $this->getCorrespondingLabelFromkeyForModeles($orders[0]['column']);
        $orderDir    = $orders[0]['dir'];

        $searches = [];
        foreach ($columns as $column) {
            if ('' !== $column['search']['value']) {
                $searches[$column['data']] = $column['search']['value'];
            }
        }

        return $this->modeleRepository->findPaginated($first, $maxResults, $orderColumn, $orderDir, $searches);
    }

    private function getCorrespondingLabelFromkeyForModeles(string $key)
    {
        return \array_key_exists($key, $this->getLabelAndKeysArrayForModeles()) ? $this->getLabelAndKeysArrayForModeles()[$key] : null;
    }

    private function getLabelAndKeysArrayForModeles()
    {
        return [
            '0' => 'nom',
            '1' => 'description',
        ];
    }

    public function printAction(string $id)
    {
    }
}
