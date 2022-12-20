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

namespace App\Domain\User\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class CollectivityTypeDictionary extends SimpleDictionary
{
    public const TYPE_COMMUNE            = 'commune';
    public const TYPE_SOCIAL_INSTITUTION = 'social_institution';
    public const TYPE_EPCI               = 'epci';
    public const TYPE_DEPARTMENTAL_UNION = 'departmental_union';
    public const TYPE_MEDICAL_INSTITUTION = 'medical_institution';
    public const TYPE_SANITARY_INSTITUTION = 'sanitary_institution';
    public const TYPE_OTHER              = 'other';

    public function __construct()
    {
        parent::__construct('user_collectivity_type', self::getTypes());
    }

    /**
     * Get an array of Types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_COMMUNE            => 'Commune',
            self::TYPE_SOCIAL_INSTITUTION => 'Établissement social',
            self::TYPE_EPCI               => 'Établissement Public de Coopération Intercommunale (EPCI)',
            self::TYPE_DEPARTMENTAL_UNION => 'Syndicat départemental',
            self::TYPE_MEDICAL_INSTITUTION     => 'Établissement médico-social',
            self::TYPE_SANITARY_INSTITUTION    => 'Établissement sanitaire',
            self::TYPE_OTHER              => 'Autre',
        ];
    }

    /**
     * Get keys of the Types array.
     *
     * @return array
     */
    public static function getTypesKeys()
    {
        return \array_keys(self::getTypes());
    }
}
