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

namespace App\Tests\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\Registry\Dictionary\ViolationGravityDictionary;
use PHPUnit\Framework\TestCase;

class ViolationGravityDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationGravityDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationGravityDictionary();

        $this->assertEquals('registry_violation_gravity', $dictionary->getName());
        $this->assertEquals(ViolationGravityDictionary::getGravities(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationGravityDictionary::GRAVITY_NEGLIGIBLE => 'Négligeable',
            ViolationGravityDictionary::GRAVITY_LIMITED    => 'Limité',
            ViolationGravityDictionary::GRAVITY_IMPORTANT  => 'Important',
            ViolationGravityDictionary::GRAVITY_MAXIMUM    => 'Maximal',
        ];

        $this->assertEquals($data, ViolationGravityDictionary::getGravities());
        $this->assertEquals(
            \array_keys(ViolationGravityDictionary::getGravities()),
            ViolationGravityDictionary::getGravitiesKeys()
        );
    }
}
