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

namespace App\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class ViolationImpactDictionary extends SimpleDictionary
{
    public const IMPACT_LOSS_CONTROL_PERSONAL_DATA        = 'loss_control_personal_data';
    public const IMPACT_LIMITATION_RIGHT                  = 'limitation_right';
    public const IMPACT_DISCRIMINATION                    = 'discrimination';
    public const IMPACT_IDENTITY_THEFT                    = 'identity_theft';
    public const IMPACT_FRAUD                             = 'fraud';
    public const IMPACT_UNAUTHORIZED_PSEUDO_LIFTING       = 'unauthorized_pseudo_lifting';
    public const IMPACT_FINANCIAL_LOSSES                  = 'financial_losses';
    public const IMPACT_REPUTATION_DAMAGE                 = 'reputation_damage';
    public const IMPACT_LOSS_PROFESSIONAL_CONFIDENTIALITY = 'loss_professional_confidentiality';
    public const IMPACT_OTHER                             = 'other';

    public function __construct()
    {
        parent::__construct('registry_violation_impact', self::getImpacts());
    }

    /**
     * Get an array of Impacts.
     *
     * @return array
     */
    public static function getImpacts()
    {
        return [
            self::IMPACT_LOSS_CONTROL_PERSONAL_DATA        => 'Perte de contrôle sur leurs données personnelles',
            self::IMPACT_LIMITATION_RIGHT                  => 'Limitation de leurs droits',
            self::IMPACT_DISCRIMINATION                    => 'Discrimination',
            self::IMPACT_IDENTITY_THEFT                    => 'Vol d\'identité',
            self::IMPACT_FRAUD                             => 'Fraude',
            self::IMPACT_UNAUTHORIZED_PSEUDO_LIFTING       => 'Levée non autorisée de pseudonimisation',
            self::IMPACT_FINANCIAL_LOSSES                  => 'Pertes financières',
            self::IMPACT_REPUTATION_DAMAGE                 => 'Atteinte à la réputation',
            self::IMPACT_LOSS_PROFESSIONAL_CONFIDENTIALITY => 'Perte de la confidentialité de données protégées par un secret professionnel',
            self::IMPACT_OTHER                             => 'Autre',
        ];
    }

    /**
     * Get keys of the Impacts array.
     *
     * @return array
     */
    public static function getImpactsKeys()
    {
        return \array_keys(self::getImpacts());
    }
}
