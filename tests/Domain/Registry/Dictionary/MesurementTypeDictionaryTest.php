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

use App\Domain\Registry\Dictionary\MesurementTypeDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class MesurementTypeDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new MesurementTypeDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new MesurementTypeDictionary();

        $this->assertEquals('registry_mesurement_type', $dictionary->getName());
        $this->assertEquals(MesurementTypeDictionary::getTypes(), $dictionary->getValues());
    }

    public function testGetTypes()
    {
        $data = [
            MesurementTypeDictionary::TYPE_LEGAL          => 'Juridique',
            MesurementTypeDictionary::TYPE_ORGANISATIONAL => 'Organisationnel',
            MesurementTypeDictionary::TYPE_TECHNICAL      => 'Technique',
        ];

        $this->assertEquals($data, MesurementTypeDictionary::getTypes());
    }

    public function testGetTypesKeys()
    {
        $data = [
            MesurementTypeDictionary::TYPE_LEGAL,
            MesurementTypeDictionary::TYPE_ORGANISATIONAL,
            MesurementTypeDictionary::TYPE_TECHNICAL,
        ];

        $this->assertEquals($data, MesurementTypeDictionary::getTypesKeys());
    }
}
