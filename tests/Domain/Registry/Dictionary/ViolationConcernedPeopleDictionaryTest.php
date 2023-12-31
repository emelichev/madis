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
use App\Domain\Registry\Dictionary\ViolationConcernedPeopleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationConcernedPeopleDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationConcernedPeopleDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationConcernedPeopleDictionary();

        $this->assertEquals('registry_violation_concerned_people', $dictionary->getName());
        $this->assertEquals(ViolationConcernedPeopleDictionary::getConcernedPeople(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationConcernedPeopleDictionary::PEOPLE_EMPLOYEE       => 'Employés',
            ViolationConcernedPeopleDictionary::PEOPLE_USER           => 'Utilisateurs',
            ViolationConcernedPeopleDictionary::PEOPLE_MEMBER         => 'Adhérents',
            ViolationConcernedPeopleDictionary::PEOPLE_STUDENT        => 'Étudiants / élèves',
            ViolationConcernedPeopleDictionary::PEOPLE_MILITARY       => 'Personnel militaire',
            ViolationConcernedPeopleDictionary::PEOPLE_CUSTOMER       => 'Clients (actuels ou potentiels)',
            ViolationConcernedPeopleDictionary::PEOPLE_PATIENT        => 'Patients',
            ViolationConcernedPeopleDictionary::PEOPLE_MINOR          => 'Mineurs',
            ViolationConcernedPeopleDictionary::PEOPLE_VULNERABLE     => 'Personnes vulnérables',
            ViolationConcernedPeopleDictionary::PEOPLE_NOT_DETERMINED => 'Pas déterminé pour le moment',
            ViolationConcernedPeopleDictionary::PEOPLE_OTHER          => 'Autres',
        ];

        $this->assertEquals($data, ViolationConcernedPeopleDictionary::getConcernedPeople());
        $this->assertEquals(
            \array_keys(ViolationConcernedPeopleDictionary::getConcernedPeople()),
            ViolationConcernedPeopleDictionary::getConcernedPeopleKeys()
        );
    }
}
