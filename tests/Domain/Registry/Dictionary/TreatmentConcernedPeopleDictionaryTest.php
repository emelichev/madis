<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Domain\Registry\Dictionary;

use App\Domain\Registry\Dictionary\TreatmentConcernedPeopleDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class TreatmentConcernedPeopleDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new TreatmentConcernedPeopleDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new TreatmentConcernedPeopleDictionary();

        $this->assertEquals('registry_treatment_concerned_people', $roleDictionary->getName());
        $this->assertEquals(TreatmentConcernedPeopleDictionary::getTypes(), $roleDictionary->getValues());
    }

    public function testGetTypes()
    {
        $data = [
            TreatmentConcernedPeopleDictionary::TYPE_PARTICULAR => 'Particuliers',
            TreatmentConcernedPeopleDictionary::TYPE_USER       => 'Internautes',
            TreatmentConcernedPeopleDictionary::TYPE_AGENT      => 'Agents',
            TreatmentConcernedPeopleDictionary::TYPE_ELECTED    => 'Élus',
            TreatmentConcernedPeopleDictionary::TYPE_COMPANY    => 'Entreprises',
            TreatmentConcernedPeopleDictionary::TYPE_PARTNER    => 'Partenaires',
        ];

        $this->assertEquals($data, TreatmentConcernedPeopleDictionary::getTypes());
    }

    public function testGetTypesKeys()
    {
        $data = [
            TreatmentConcernedPeopleDictionary::TYPE_PARTICULAR,
            TreatmentConcernedPeopleDictionary::TYPE_USER,
            TreatmentConcernedPeopleDictionary::TYPE_AGENT,
            TreatmentConcernedPeopleDictionary::TYPE_ELECTED,
            TreatmentConcernedPeopleDictionary::TYPE_COMPANY,
            TreatmentConcernedPeopleDictionary::TYPE_PARTNER,
        ];

        $this->assertEquals($data, TreatmentConcernedPeopleDictionary::getTypesKeys());
    }
}
