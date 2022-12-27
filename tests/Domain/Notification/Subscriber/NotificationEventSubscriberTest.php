<?php

namespace App\Tests\Domain\Notification\Subscriber;

use App\Domain\Maturity\Model\Survey;
use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\LateRequestEvent;
use App\Domain\Notification\Event\LateSurveyEvent;
use App\Domain\Notification\Event\NoLoginEvent;
use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\NotificationUser;
use App\Domain\Notification\Serializer\NotificationNormalizer;
use App\Domain\Notification\Subscriber\NotificationEventSubscriber;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use App\Infrastructure\ORM\Notification\Repository\NotificationUser as NotificationUserRepository;
use App\Tests\Domain\Notification\Symfony\EventSubscriber\Doctrine\NotificationToken;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationEventSubscriberTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $notificationNormalizer;
    private ObjectProphecy $userRepository;
    private ObjectProphecy $notificationUserRepository;
    private ObjectProphecy $notificationRepository;

    private NotificationEventSubscriber $subscriber;

    public function setUp(): void
    {
        $this->notificationRepository     = $this->prophesize(NotificationRepository::class);
        $this->notificationNormalizer     = $this->prophesize(NotificationNormalizer::class);
        $this->userRepository             = $this->prophesize(UserRepository::class);
        $this->notificationUserRepository = $this->prophesize(NotificationUserRepository::class);

        $this->subscriber = new NotificationEventSubscriber($this->notificationRepository->reveal(), $this->notificationUserRepository->reveal(), $this->notificationNormalizer->reveal(), $this->userRepository->reveal());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertEquals([
            LateActionEvent::class  => 'onLateAction',
            LateRequestEvent::class => 'onLateRequest',
            NoLoginEvent::class     => 'onNoLogin',
            LateSurveyEvent::class  => 'onLateSurvey',
        ], NotificationEventSubscriber::getSubscribedEvents());
    }

    public function testLateSurveyEvent()
    {
        $collectivity = new Collectivity();
        $collectivity->setName('coll');
        $survey = new Survey();
        $survey->setCollectivity($collectivity);
        $survey->setCreatedAt(new \DateTimeImmutable());

        $this->notificationRepository->findBy([
            'module'       => 'notification.modules.maturity',
            'collectivity' => $survey->getCollectivity(),
            'action'       => 'notifications.actions.late_survey',
            'name'         => $survey->__toString(),
        ])->shouldBeCalled()->willReturn([]);

        $user = new User();

        $notification = new Notification();

        $notification->setModule('notification.modules.maturity');
        $notification->setCollectivity($survey->getCollectivity());
        $notification->setAction('notifications.actions.late_survey');
        $notification->setName($survey->__toString());
        $notification->setObject((object) [
            'collectivity' => [
                'name' => 'coll',
            ],
        ]);

        $notification2 = new Notification();
        $notification2->setModule('notification.modules.maturity');
        $notification2->setCollectivity($survey->getCollectivity());
        $notification2->setAction('notifications.actions.late_survey');
        $notification2->setName($survey->__toString());
        $notification2->setObject((object) [
            'collectivity' => [
                'name' => 'coll',
            ],
        ]);
        $this->notificationRepository->insert(new NotificationToken($notification))->shouldBeCalled();

        $this->notificationRepository->insert(new NotificationToken($notification2))->shouldBeCalled();

        $nu = new NotificationUser();
        $nu->setUser($user);
        $nu->setNotification($notification2);

        $notification2->setNotificationUsers([$user]);

        $this->notificationRepository->update(new NotificationToken($notification2))->shouldBeCalled();

        $this->userRepository->findNonDpoUsersForCollectivity($collectivity)->shouldBeCalled()->willReturn([$user]);

        $this->notificationUserRepository->saveUsers(new NotificationToken($notification2), [$user])->shouldBeCalled()->willReturn([$nu]);

        $this->notificationNormalizer->normalize(Argument::exact($survey), null, Argument::any())
            ->shouldBeCalled()
            ->willReturn((object) [
                'collectivity' => [
                    'name' => 'coll',
                ],
            ])
        ;

        $event = new LateSurveyEvent($survey);

        $this->subscriber->onLateSurvey($event);
    }
}
