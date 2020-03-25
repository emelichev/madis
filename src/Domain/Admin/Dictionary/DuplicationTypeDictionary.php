<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Domain\Admin\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class DuplicationTypeDictionary extends SimpleDictionary
{
    const NAME = 'admin_duplication_type';

    const KEY_TREATMENT  = 'treatment';
    const KEY_CONTRACTOR = 'contractor';
    const KEY_MESUREMENT = 'mesurement';

    public function __construct()
    {
        parent::__construct(self::NAME, self::getData());
    }

    /**
     * Get an array of data.
     *
     * @return array
     */
    public static function getData()
    {
        return [
            self::KEY_TREATMENT  => 'Traitement',
            self::KEY_CONTRACTOR => 'Sous-traitant',
            self::KEY_MESUREMENT => 'Action de protection',
        ];
    }

    /**
     * Get keys of the data array.
     *
     * @return array
     */
    public static function getDataKeys()
    {
        return \array_keys(self::getData());
    }
}
