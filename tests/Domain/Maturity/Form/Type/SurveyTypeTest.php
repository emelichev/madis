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

namespace App\Tests\Domain\Maturity\Form\Type;

use App\Domain\Maturity\Form\Type\SurveyType;
use App\Domain\Maturity\Model\Referentiel;
use App\Domain\Maturity\Model\Survey;
use App\Tests\Utils\FormTypeHelper;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyTypeTest extends FormTypeHelper
{
    use ProphecyTrait;

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new SurveyType());
    }

    public function testBuildForm()
    {
        $builder = [
            'referentiel' => EntityType::class,
            'questions'   => CollectionType::class,
        ];

        $survey = new Survey();

        $survey->setReferentiel(new Referentiel());

        (new SurveyType())->buildForm($this->prophesizeBuilder($builder), ['data' => $survey]);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Survey::class,
            'validation_groups' => [
                'default',
                'survey',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new SurveyType())->configureOptions($resolverProphecy->reveal());
    }
}
