<?php

declare(strict_types=1);

namespace App\Domain\Notification\Symfony\EventSubscriber\Doctrine;

use App\Application\Interfaces\CollectivityRelated;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\Documentation\Model\Document;
use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\NotificationUser;
use App\Domain\Notification\Serializer\NotificationNormalizer;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;
use App\Domain\User\Dictionary\UserMoreInfoDictionary;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use App\Infrastructure\ORM\Notification\Repository\NotificationUser as NotificationUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * This subscriber handles events that are generated by doctrine, and creates notifications from them if necessary.
 */
class NotificationEventSubscriber implements EventSubscriber
{
    protected array $classes = [
        AnalyseImpact::class,
        Treatment::class,
        Mesurement::class,
        Violation::class,
        Proof::class,
        Contractor::class,
        Request::class,
        Document::class,
    ];

    protected array $recipients = [
        AnalyseImpact::class => Notification::NOTIFICATION_COLLECTIVITY | Notification::NOTIFICATION_DPO,
        Treatment::class     => Notification::NOTIFICATION_DPO,
        Mesurement::class    => Notification::NOTIFICATION_DPO,
        Violation::class     => Notification::NOTIFICATION_DPO,
        Proof::class         => Notification::NOTIFICATION_DPO,
        Contractor::class    => Notification::NOTIFICATION_DPO,
        Request::class       => Notification::NOTIFICATION_DPO,
        Document::class      => Notification::NOTIFICATION_COLLECTIVITY,
    ];

    protected NotificationRepository $notificationRepository;
    protected NotificationUserRepository $notificationUserRepository;
    protected UserRepository $userRepository;
    protected Security $security;
    protected NormalizerInterface $normalizer;

    public function __construct(
        NotificationRepository $notificationRepository,
        NotificationNormalizer $normalizer,
        UserRepository $userRepository,
        NotificationUserRepository $notificationUserRepository,
        Security $security,
    ) {
        $this->notificationRepository     = $notificationRepository;
        $this->normalizer                 = $normalizer;
        $this->userRepository             = $userRepository;
        $this->notificationUserRepository = $notificationUserRepository;
        $this->security                   = $security;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (isset($user) && is_object($user) && User::class === get_class($user) && $user->isNotGeneratesNotifications()) {
            // User does not generate notifications, exit now
            return;
        }
        $em  = $eventArgs->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $class = get_class($entity);
            if (!in_array($class, $this->classes) || Request::class === $class || AnalyseImpact::class === $class) {
                continue;
            }

            $this->createNotifications($entity, 'create', $em);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $class = get_class($entity);
            if (!in_array($class, $this->classes) || Document::class === $class) {
                continue;
            }
            $action = 'update';
            if (Request::class === $class) {
                $ch = $uow->getEntityChangeSet($entity);
                // Exit if the request has no state change
                if (!isset($ch['state'])) {
                    continue;
                }
                $action = 'state_change';
            }

            if (AnalyseImpact::class === $class) {
                $ch = $uow->getEntityChangeSet($entity);
                // Exit if the request has no state change
                if (!isset($ch['statut']) && !isset($ch['isReadyForValidation'])) {
                    continue;
                }
                if (isset($ch['statut'])) {
                    $action = 'state_change';
                } elseif (isset($ch['isReadyForValidation'])) {
                    $action = 'validation';
                }
            }

            $this->createNotifications($entity, $action, $em);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $class = get_class($entity);
            if (!in_array($class, $this->classes) || Request::class == $class || Document::class === $class || AnalyseImpact::class === $class) {
                continue;
            }
            $this->createNotifications($entity, 'delete', $em);
        }
    }

    private function createNotifications($object, $action, $em): array
    {
        $notifications = [];
        $recipients    = $this->recipients[get_class($object)];

        if (AnalyseImpact::class === get_class($object) && 'state_change' === $action) {
            // DO not send status change of AIPD to collectivity
            $recipients = Notification::NOTIFICATION_DPO;
        }

        $uow = $em->getUnitOfWork();

        $normalized = $this->normalizer->normalize($object, null,
            [
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {
                    return $this->getObjectSimpleValue($o);
                },
                'maxDepth'                                         => 1,
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH         => true,
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
                AbstractObjectNormalizer::MAX_DEPTH_HANDLER        => function ($o) {
                    if (is_iterable($o)) {
                        $d = [];
                        foreach ($o as $item) {
                            $d[] = $this->getObjectSimpleValue($item);
                        }

                        return $d;
                    }

                    return $this->getObjectSimpleValue($o);
                },
            ],
        );

        if ($recipients & Notification::NOTIFICATION_DPO) {
            $notification    = $this->createNotificationForUsers($object, $action, $normalized);
            $notifications[] = $notification;
        }

        if ($recipients & Notification::NOTIFICATION_COLLECTIVITY) {
            // get all non-DPO users
            $collectivity = method_exists($object, 'getCollectivity') ? $object->getCollectivity() : null;

            if (AnalyseImpact::class === get_class($object) && $object->getConformiteTraitement() && $object->getConformiteTraitement()->getTraitement()) {
                $collectivity = $object->getConformiteTraitement()->getTraitement()->getCollectivity();
            }

            if ($collectivity) {
                $users = $this->userRepository->findNonDpoUsersForCollectivity($collectivity);
            } else {
                $users = $this->userRepository->findNonDpoUsers();
            }

            $notification    = $this->createNotificationForUsers($object, $action, $normalized, $users);
            $notifications[] = $notification;
        }

        // Insert notifications and notification_users

        $meta  = $em->getClassMetadata(Notification::class);
        $meta2 = $em->getClassMetadata(NotificationUser::class);

        foreach ($notifications as $notif) {
            $em->persist($notif);
            /**
             * @var Notification $notif
             */
            if ($notif->getNotificationUsers()) {
                foreach ($notif->getNotificationUsers() as $u) {
                    $em->persist($u);
                    $uow->computeChangeSet($meta2, $u);
                }
            }

            $uow->computeChangeSet($meta, $notif);
        }

        return $notifications;
    }

    private function createNotificationForUsers($object, $action, $normalized, $users = null): Notification
    {
        $notification = new Notification();
        $mod          = Notification::MODULES[get_class($object)];
        $recipients   = $this->recipients[get_class($object)];
        $notification->setModule('notification.modules.' . $mod);
        $collectivity = method_exists($object, 'getCollectivity') ? $object->getCollectivity() : null;

        if (AnalyseImpact::class === get_class($object) && $object->getConformiteTraitement() && $object->getConformiteTraitement()->getTraitement()) {
            $collectivity = $object->getConformiteTraitement()->getTraitement()->getCollectivity();
        }

        $user = $this->security->getUser();

        if ($recipients & Notification::NOTIFICATION_DPO) {
            $notification->setDpo(true);
        }

        $notification->setCollectivity($collectivity);
        $notification->setName(method_exists($object, 'getName') ? $object->getName() : $object->__toString());
        $notification->setAction('notification.actions.' . $action);
        if ($user && is_object($user) && User::class === get_class($user)) {
            $notification->setCreatedBy($user);
        }

        $notification->setObject((object) $normalized);

        $nus = [];

        if ($users) {
            $nus = $this->notificationUserRepository->saveUsers($notification, $users);

            $notification->setNotificationUsers($nus);
        } else {
            if (AnalyseImpact::class === get_class($object)) {
                $nus = $this->saveEmailNotificationForDPO($notification);
                $notification->setNotificationUsers($nus);
            }
        }

        if (Document::class === get_class($object)) {
            $newnus = array_merge($this->saveEmailNotificationForRefOp($notification), $nus);
            $notification->setNotificationUsers($newnus);
        }
        if (Violation::class === get_class($object)) {
            $newnus = array_merge($this->saveEmailNotificationForRespTrait($notification, $object), $nus);
            $notification->setNotificationUsers($newnus);
        }

        return $notification;
    }

    private function saveEmailNotificationForRefOp(Notification $notification): array
    {
        $nus = [];
        // Get referent operationnels for this collectivity
        $refs = (new ArrayCollection($this->userRepository->findAll()))->filter(function (User $u) {
            $mi = $u->getMoreInfos();

            return $mi && isset($mi[UserMoreInfoDictionary::MOREINFO_OPERATIONNAL]) && $mi[UserMoreInfoDictionary::MOREINFO_OPERATIONNAL];
        });

        // Add notification with email address for the référents
        foreach ($refs as $ref) {
            $nu = new NotificationUser();
            if (User::class === get_class($ref)) {
                $nu->setMail($ref->getEmail());
                $nu->setUser($ref);
            } else {
                $nu->setMail($ref);
            }

            $nu->setNotification($notification);
            $nu->setActive(false);
            $nu->setToken(sha1($notification->getName() . microtime() . $nu->getMail()));
            $nu->setSent(false);
            $nus[] = $nu;
        }

        return $nus;
    }

    private function saveEmailNotificationForDPO(Notification $notification): array
    {
        // Get DPOS
        $refs = (new ArrayCollection($this->userRepository->findAll()))->filter(function (User $u) {
            $mi = $u->getMoreInfos();

            return in_array('ROLE_ADMIN', $u->getRoles())
                || in_array('ROLE_REFERENT', $u->getRoles())
                || ($mi && isset($mi[UserMoreInfoDictionary::MOREINFO_DPD]) && $mi[UserMoreInfoDictionary::MOREINFO_DPD]);
        });

        $nus = [];
        // Add notification with email address for the référents
        foreach ($refs as $ref) {
            $nu = new NotificationUser();
            if (is_object($ref) && $ref instanceof User) {
                $nu->setMail($ref->getEmail());
                $nu->setUser($ref);
            } else {
                $nu->setMail($ref);
            }

            $nu->setNotification($notification);
            $nu->setActive(false);
            $nu->setToken(sha1($notification->getName() . microtime() . $nu->getMail()));
            $nu->setSent(false);
            $nus[] = $nu;
        }

        return $nus;
    }

    private function saveEmailNotificationForRespTrait(Notification $notification, CollectivityRelated $object): array
    {
        $nus = [];
        // Get referent operationnels for this collectivity
        $refs = $object->getCollectivity()->getUsers()->filter(function (User $u) {
            $mi = $u->getMoreInfos();

            return $mi && isset($mi[UserMoreInfoDictionary::MOREINFO_TREATMENT]) && $mi[UserMoreInfoDictionary::MOREINFO_TREATMENT];
        });
        if (0 === $refs->count()) {
            // No ref OP, get from collectivity
            if ($object->getCollectivity() && $object->getCollectivity()->getLegalManager()) {
                $refs = [$object->getCollectivity()->getLegalManager()->getMail()];
            }
        }

        foreach ($refs as $ref) {
            $nu = new NotificationUser();
            if (is_object($ref) && User::class === get_class($ref)) {
                $nu->setMail($ref->getEmail());
                $nu->setUser($ref);
            } else {
                $nu->setMail($ref);
            }

            $nu->setToken(sha1($notification->getName() . microtime() . $nu->getMail()));
            $nu->setNotification($notification);
            $nu->setActive(false);
            $nu->setSent(false);
            $nus[] = $nu;
//            $this->notificationUserRepository->persist($nu);
        }

        return $nus;
    }

    private function getObjectSimpleValue($object)
    {
        if (is_object($object)) {
            if (method_exists($object, 'getId')) {
                return $object->getId();
            } elseif (method_exists($object, '__toString')) {
                return $object->__toString();
            } elseif (method_exists($object, 'format')) {
                return $object->format(DATE_ATOM);
            }

            return '';
        }

        if (is_array($object)) {
            return join(', ', $object);
        }

        return $object;
    }
}
