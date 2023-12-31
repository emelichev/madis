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

use App\Domain\Registry\Form\Type\ViolationType;
use App\Domain\Registry\Model\Violation;
use App\Domain\User\Model\Collectivity;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class ViolationTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ViolationType($this->prophesize(Security::class)->reveal(), $this->prophesize(AuthorizationCheckerInterface::class)->reveal()));
    }

    public function testBuildForm()
    {
        $violation    = new Violation();
        $collectivity = new Collectivity();
        $collectivity->setIsServicesEnabled(true);
        $violation->setCollectivity($collectivity);

        $builder = [
            'date'                          => DateType::class,
            'service'                       => EntityType::class,
            'inProgress'                    => CheckboxType::class,
            'violationNatures'              => DictionaryType::class,
            'origins'                       => DictionaryType::class,
            'cause'                         => DictionaryType::class,
            'concernedDataNature'           => DictionaryType::class,
            'concernedPeopleCategories'     => DictionaryType::class,
            'nbAffectedRows'                => IntegerType::class,
            'nbAffectedPersons'             => IntegerType::class,
            'potentialImpactsNature'        => DictionaryType::class,
            'gravity'                       => DictionaryType::class,
            'communication'                 => DictionaryType::class,
            'communicationPrecision'        => TextareaType::class,
            'appliedMeasuresAfterViolation' => TextareaType::class,
            'notification'                  => DictionaryType::class,
            'notificationDetails'           => TextType::class,
            'comment'                       => TextareaType::class,
            'treatments'                    => EntityType::class,
            'contractors'                   => EntityType::class,
            'updatedBy'                     => HiddenType::class,
        ];

        (new ViolationType($this->prophesize(Security::class)->reveal(), $this->prophesize(AuthorizationCheckerInterface::class)->reveal()))->buildForm($this->prophesizeBuilder($builder), ['data' => $violation]);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Violation::class,
            'validation_groups' => [
                'default',
                'violation',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ViolationType($this->prophesize(Security::class)->reveal(), $this->prophesize(AuthorizationCheckerInterface::class)->reveal()))->configureOptions($resolverProphecy->reveal());
    }
}
