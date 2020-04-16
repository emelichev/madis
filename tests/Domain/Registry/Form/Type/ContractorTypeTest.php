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

namespace App\Tests\Domain\Registry\Form\Type;

use App\Domain\Registry\Form\Type\ContractorType;
use App\Domain\Registry\Form\Type\Embeddable\AddressType;
use App\Domain\Registry\Model\Contractor;
use App\Domain\User\Form\Type\ContactType;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractorTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ContractorType());
    }

    public function testBuildForm()
    {
        $builder = [
            'name'                       => TextType::class,
            'referent'                   => TextType::class,
            'contractualClausesVerified' => CheckboxType::class,
            'adoptedSecurityFeatures'    => CheckboxType::class,
            'maintainsTreatmentRegister' => CheckboxType::class,
            'sendingDataOutsideEu'       => CheckboxType::class,
            'otherInformations'          => TextareaType::class,
            'address'                    => AddressType::class,
            'legalManager'               => ContactType::class,
            'dpo'                        => ContactType::class,
        ];

        (new ContractorType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Contractor::class,
            'validation_groups' => [
                'default',
                'contractor',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ContractorType())->configureOptions($resolverProphecy->reveal());
    }
}
