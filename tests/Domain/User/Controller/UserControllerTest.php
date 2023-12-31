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

namespace App\Tests\Domain\User\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\User\Controller\UserController;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserControllerTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var EntityManagerInterface
     */
    private $managerProphecy;

    /**
     * @var TranslatorInterface
     */
    private $translatorProphecy;

    /**
     * @var Repository\User
     */
    private $repositoryProphecy;

    /**
     * @var RequestStack
     */
    private $requestStackProphecy;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactoryProphecy;

    /**
     * @var Pdf|ObjectProphecy
     */
    private $pdf;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var UserController
     */
    private $controller;

    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    protected function setUp(): void
    {
        $this->managerProphecy        = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy     = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy     = $this->prophesize(Repository\User::class);
        $this->requestStackProphecy   = $this->prophesize(RequestStack::class);
        $this->encoderFactoryProphecy = $this->prophesize(EncoderFactoryInterface::class);
        $this->pdf                    = $this->prophesize(Pdf::class);
        $this->router                 = $this->prophesize(RouterInterface::class);
        $this->security               = $this->prophesize(Security::class);
        $this->userProviderProphecy   = $this->prophesize(UserProvider::class);
        $this->authorizationChecker   = $this->prophesize(AuthorizationCheckerInterface::class);

        $this->controller = new UserController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->requestStackProphecy->reveal(),
            $this->encoderFactoryProphecy->reveal(),
            $this->pdf->reveal(),
            $this->router->reveal(),
            $this->security->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->authorizationChecker->reveal(),
        );

        parent::setUp();
    }

    /**
     * Test inheritance.
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    /**
     * Test getDomain.
     *
     * @throws \ReflectionException
     */
    public function testGetDomain(): void
    {
        $this->assertEquals(
            'user',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    /**
     * Test getModel.
     *
     * @throws \ReflectionException
     */
    public function testGetModel(): void
    {
        $this->assertEquals(
            'user',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    /**
     * Test getModelClass.
     *
     * @throws \ReflectionException
     */
    public function testGetModelClass(): void
    {
        $this->assertEquals(
            Model\User::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    /**
     * Test getFormType.
     *
     * @throws \ReflectionException
     */
    public function testGetFormType(): void
    {
        $this->assertEquals(
            UserType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }

    /**
     * Test isSoftDelete.
     *
     * @throws \ReflectionException
     */
    public function testIsSoftDelete(): void
    {
        $this->assertTrue($this->invokeMethod($this->controller, 'isSoftDelete'));
    }
}
