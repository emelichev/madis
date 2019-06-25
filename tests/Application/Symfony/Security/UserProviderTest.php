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

namespace App\Tests\Application\Symfony\Security;

use App\Application\Symfony\Security\UserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProviderTest extends TestCase
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    private $token;

    /**
     * @var UserProvider
     */
    private $userProvider;

    public function setUp()
    {
        $this->token        = $this->prophesize(TokenInterface::class);
        $this->tokenStorage = $this->prophesize(TokenStorageInterface::class);

        $this->userProvider = new UserProvider(
            $this->tokenStorage->reveal()
        );
    }

    /**
     * Test to get an authenticated user.
     */
    public function testGetAuthenticatedUser()
    {
        $user = $this->prophesize(UserInterface::class)->reveal();
        $this->token->getUser()->shouldBeCalled()->willReturn($user);
        $this->tokenStorage->getToken()->shouldBeCalled()->willReturn($this->token->reveal());

        $this->assertInstanceOf(UserInterface::class, $this->userProvider->getAuthenticatedUser());
    }

    /**
     * Test to get an authenticated user but the route isn't under security.
     */
    public function testGetAuthenticatedUserButRouteIsntUnderSecurity()
    {
        $this->token->getUser()->shouldNotBeCalled();
        $this->tokenStorage->getToken()->shouldBeCalled()->willReturn(null);

        $this->assertNull($this->userProvider->getAuthenticatedUser());
    }

    /**
     * Test to get an authenticated user but user isn't an object (case of anonymous user).
     */
    public function testGetAuthenticatedUserWithoutObjectUser()
    {
        $user = 'anon.';
        $this->token->getUser()->shouldBeCalled()->willReturn($user);
        $this->tokenStorage->getToken()->shouldBeCalled()->willReturn($this->token->reveal());

        $this->assertNull($this->userProvider->getAuthenticatedUser());
    }
}
