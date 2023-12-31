<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Tests\Domain\User\Form\Type;

use App\Domain\User\Form\DataTransformer\MoreInfoTransformer;
use App\Domain\User\Form\DataTransformer\RoleTransformer;
use App\Domain\User\Form\Type\EmailNotificationPreferenceType;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserTypeTest extends FormTypeHelper
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationCheckerProphecy;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactoryProphecy;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var PasswordEncoderInterface
     */
    private $encoderProphecy;

    /**
     * @var UserType
     */
    private $formType;

    private User $user;

    protected function setUp(): void
    {
        $this->authorizationCheckerProphecy = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->encoderFactoryProphecy       = $this->prophesize(EncoderFactoryInterface::class);
        $this->security                     = $this->prophesize(Security::class);
        $this->encoderProphecy              = $this->prophesize(PasswordEncoderInterface::class);

        $this->encoderFactoryProphecy->getEncoder(Argument::any())->willReturn($this->encoderProphecy->reveal());

        $this->formType = new UserType(
            $this->authorizationCheckerProphecy->reveal(),
            $this->encoderFactoryProphecy->reveal(),
            $this->security->reveal(),
            true
        );

        $this->user   = new User();
        $collectivity = new Collectivity();
        $collectivity->setIsServicesEnabled(true);
        $this->user->setCollectivity($collectivity);
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildFormAdmin()
    {
        $this->formType = new UserType(
            $this->authorizationCheckerProphecy->reveal(),
            $this->encoderFactoryProphecy->reveal(),
            $this->security->reveal(),
            true
        );
        $builder = [
            'firstName'                   => TextType::class,
            'lastName'                    => TextType::class,
            'email'                       => EmailType::class,
            'collectivity'                => EntityType::class,
            'roles'                       => DictionaryType::class,
            'enabled'                     => CheckboxType::class,
            'moreInfos'                   => DictionaryType::class,
            'notGeneratesNotifications'   => CheckboxType::class,
            'plainPassword'               => RepeatedType::class,
            'collectivitesReferees'       => EntityType::class,
            'emailNotificationPreference' => EmailNotificationPreferenceType::class,
            'apiAuthorized'               => CheckboxType::class,
            'ssoKey'                      => TextType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(true);
        $this->authorizationCheckerProphecy->isGranted('ROLE_PREVIEW')->shouldBeCalled()->willReturn(true);

        $builderProphecy = $this->prophesizeBuilder($builder, false);
        $builderProphecy->get('roles')->shouldBeCalled()->willReturn($builderProphecy);
        $builderProphecy
            ->addModelTransformer(Argument::type(RoleTransformer::class))
            ->shouldBeCalled()
        ;

        $builderProphecy->get('moreInfos')->shouldBeCalled()->willReturn($builderProphecy);
        $builderProphecy
            ->addModelTransformer(Argument::type(MoreInfoTransformer::class))
            ->shouldBeCalled()
        ;

        $builderProphecy
            ->addEventListener(FormEvents::POST_SUBMIT, Argument::any())
            ->shouldBeCalled()
        ;
        $builderProphecy
            ->addEventListener(FormEvents::PRE_SET_DATA, Argument::any())
            ->shouldBeCalled()
        ;
        $builderProphecy
            ->addEventListener(FormEvents::SUBMIT, Argument::any())
            ->shouldBeCalled()
        ;

        $this->formType->buildForm($builderProphecy->reveal(), ['data' => $this->user]);
    }

    public function testBuildFormAdminNotificationsDisabled()
    {
        $this->formType = new UserType(
            $this->authorizationCheckerProphecy->reveal(),
            $this->encoderFactoryProphecy->reveal(),
            $this->security->reveal(),
            false
        );

        $builder = [
            'firstName'             => TextType::class,
            'lastName'              => TextType::class,
            'email'                 => EmailType::class,
            'collectivity'          => EntityType::class,
            'roles'                 => DictionaryType::class,
            'enabled'               => CheckboxType::class,
            'moreInfos'             => DictionaryType::class,
            'plainPassword'         => RepeatedType::class,
            'collectivitesReferees' => EntityType::class,
            'apiAuthorized'         => CheckboxType::class,
            'ssoKey'                => TextType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(true);
        $this->authorizationCheckerProphecy->isGranted('ROLE_PREVIEW')->shouldBeCalled()->willReturn(true);

        $builderProphecy = $this->prophesizeBuilder($builder, false);
        $builderProphecy->get('roles')->shouldBeCalled()->willReturn($builderProphecy);
        $builderProphecy
            ->addModelTransformer(Argument::type(RoleTransformer::class))
            ->shouldBeCalled()
        ;

        $builderProphecy->get('moreInfos')->shouldBeCalled()->willReturn($builderProphecy);
        $builderProphecy->get('notGeneratesNotifications')->shouldNotBeCalled();

        $builderProphecy
            ->addModelTransformer(Argument::type(MoreInfoTransformer::class))
            ->shouldBeCalled()
        ;

        $builderProphecy
            ->addEventListener(FormEvents::POST_SUBMIT, Argument::any())
            ->shouldBeCalled()
        ;
        $builderProphecy
            ->addEventListener(FormEvents::PRE_SET_DATA, Argument::any())
            ->shouldBeCalled()
        ;
        $builderProphecy
            ->addEventListener(FormEvents::SUBMIT, Argument::any())
            ->shouldBeCalled()
        ;

        $this->formType->buildForm($builderProphecy->reveal(), ['data' => $this->user]);
    }

    public function testBuildFormUser()
    {
        $this->formType = new UserType(
            $this->authorizationCheckerProphecy->reveal(),
            $this->encoderFactoryProphecy->reveal(),
            $this->security->reveal(),
            true
        );

        $builder = [
            'firstName'                   => TextType::class,
            'lastName'                    => TextType::class,
            'email'                       => EmailType::class,
            'moreInfos'                   => DictionaryType::class,
            'emailNotificationPreference' => EmailNotificationPreferenceType::class,
            'plainPassword'               => RepeatedType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(false);
        $this->authorizationCheckerProphecy->isGranted('ROLE_PREVIEW')->shouldBeCalled()->willReturn(true);

        // No transformer since not granted admin
        $builderProphecy = $this->prophesizeBuilder($builder, false);

        $builderProphecy->get('moreInfos')->shouldBeCalled()->willReturn($builderProphecy);
        $builderProphecy
            ->addModelTransformer(Argument::type(MoreInfoTransformer::class))
            ->shouldBeCalled()
        ;

        $builderProphecy->get('roles')->shouldNotBeCalled();
        $builderProphecy->addModelTransformer(Argument::type(RoleTransformer::class))->shouldNotBeCalled();

        $builderProphecy
            ->addEventListener(FormEvents::POST_SUBMIT, Argument::any())
            ->shouldBeCalled()
        ;
        $builderProphecy
            ->addEventListener(FormEvents::PRE_SET_DATA, Argument::any())
            ->shouldBeCalled()
        ;
        $builderProphecy
            ->addEventListener(FormEvents::SUBMIT, Argument::any())
            ->shouldBeCalled()
        ;

        $this->formType->buildForm($builderProphecy->reveal(), ['data' => $this->user]);
    }

    public function testBuildFormUserNotificationsDisabled()
    {
        $this->formType = new UserType(
            $this->authorizationCheckerProphecy->reveal(),
            $this->encoderFactoryProphecy->reveal(),
            $this->security->reveal(),
            false
        );

        $builder = [
            'firstName'     => TextType::class,
            'lastName'      => TextType::class,
            'email'         => EmailType::class,
            'moreInfos'     => DictionaryType::class,
            'plainPassword' => RepeatedType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(false);
        $this->authorizationCheckerProphecy->isGranted('ROLE_PREVIEW')->shouldBeCalled()->willReturn(true);

        // No transformer since not granted admin
        $builderProphecy = $this->prophesizeBuilder($builder, false);

        $builderProphecy->get('moreInfos')->shouldBeCalled()->willReturn($builderProphecy);
        $builderProphecy
            ->addModelTransformer(Argument::type(MoreInfoTransformer::class))
            ->shouldBeCalled()
        ;

        $builderProphecy->get('roles')->shouldNotBeCalled();
        $builderProphecy->addModelTransformer(Argument::type(RoleTransformer::class))->shouldNotBeCalled();

        $builderProphecy
            ->addEventListener(FormEvents::POST_SUBMIT, Argument::any())
            ->shouldBeCalled()
        ;
        $builderProphecy
            ->addEventListener(FormEvents::PRE_SET_DATA, Argument::any())
            ->shouldBeCalled()
        ;
        $builderProphecy
            ->addEventListener(FormEvents::SUBMIT, Argument::any())
            ->shouldBeCalled()
        ;

        $this->formType->buildForm($builderProphecy->reveal(), ['data' => $this->user]);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => User::class,
            'validation_groups' => [
                'default',
                'user',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
