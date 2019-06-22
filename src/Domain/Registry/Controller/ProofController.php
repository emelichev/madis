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

namespace App\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Form\Type\ProofType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use Doctrine\ORM\EntityManagerInterface;
use Gaufrette\FilesystemInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ProofController extends CRUDController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var WordHandler
     */
    protected $wordHandler;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var FilesystemInterface
     */
    protected $documentFilesystem;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Proof $repository,
        RequestStack $requestStack,
        WordHandler $wordHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        FilesystemInterface $documentFilesystem
    ) {
        parent::__construct($entityManager, $translator, $repository);
        $this->requestStack         = $requestStack;
        $this->wordHandler          = $wordHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
        $this->documentFilesystem   = $documentFilesystem;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'registry';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'proof';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Proof::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return ProofType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListData()
    {
        $request   = $this->requestStack->getMasterRequest();
        $archived  = 'false' === $request->query->get('archive') || \is_null($request->query->get('archive'))
            ? false
            : true
        ;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->repository->findAllArchived($archived);
        }

        return $this->repository->findAllArchivedByCollectivity(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            $archived
        );
    }

    /**
     * {@inheritdoc}
     * - Upload documentFile before object persistence in database.
     */
    public function formPrePersistData($object)
    {
        if ($file = $object->getDocumentFile()) {
            $filename = (string) Uuid::uuid4() . '.' . $file->getClientOriginalExtension();
            $this->documentFilesystem->write($filename, \fopen($file->getRealPath(), 'r'));
            $object->setDocument($filename);
            $object->setDocumentFile(null);
        }
    }

    /**
     * The archive action view
     * Display a confirmation message to confirm data archivage.
     *
     * @param string $id
     *
     * @return Response
     */
    public function archiveAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('archive'), [
            'object' => $object,
        ]);
    }

    /**
     * The archive action
     * Archive the data.
     *
     * @param string $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function archiveConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $object->setDeletedAt(new \DateTimeImmutable());
        $this->repository->update($object);

        $this->addFlash('success', $this->getFlashbagMessage('success', 'archive', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    /**
     * {@inheritdoc}
     * OVERRIDE METHOD.
     * Override deletion in order to delete Proof into server.
     */
    public function deleteConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if ($this->isSoftDelete()) {
            if (!\method_exists($object, 'setDeletedAt')) {
                throw new MethodNotImplementedException('setDeletedAt');
            }
            $object->setDeletedAt(new \DateTimeImmutable());
            $this->repository->update($object);
        } else {
            $filename = $object->getDocument();

            $this->entityManager->remove($object);
            $this->entityManager->flush();

            // TODO: Log error if deletion fail
            $this->documentFilesystem->delete($filename);
        }

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    /**
     * Download uploaded document which belongs to provided object id.
     *
     * @param string $id
     *
     * @return Response
     */
    public function downloadAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);

        if (!$object) {
            throw new NotFoundHttpException("No object exists with id '{$id}'");
        }

        if ($this->userProvider->getAuthenticatedUser()->getCollectivity() !== $object->getCollectivity()) {
            throw new AccessDeniedHttpException("You can't access to an object that does not belong to your collectivity");
        }

        $extension = \pathinfo($object->getDocument(), PATHINFO_EXTENSION);
        $response  = new BinaryFileResponse('gaufrette://registry_proof_document/' . $object->getDocument());

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "{$object->getName()}.{$extension}"
        );

        return $response;
    }
}
