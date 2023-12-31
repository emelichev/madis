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

namespace App\Domain\Reporting\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class LogJournalActionDictionary extends SimpleDictionary
{
    public const CREATE             = 'create';
    public const DELETE             = 'delete';
    public const LOGIN              = 'login';
    public const SOFT_DELETE        = 'soft_delete';
    public const SOFT_DELETE_REVOKE = 'soft_delete_revoke';
    public const UPDATE             = 'update';
    public const SWITCH_USER_ON     = 'switch_user_on';
    public const SWITCH_USER_OFF    = 'switch_user_off';

    public function __construct()
    {
        parent::__construct('reporting_log_journal_action', self::getActions());
    }

    /**
     * @return array
     */
    public static function getActions()
    {
        return [
            self::CREATE             => 'Création',
            self::UPDATE             => 'Mise à jour',
            self::DELETE             => 'Suppression',
            self::LOGIN              => 'Connexion',
            self::SOFT_DELETE        => 'Archivage',
            self::SOFT_DELETE_REVOKE => 'Désarchivage',
            self::SWITCH_USER_ON     => 'Se connecter en tant que - Début',
            self::SWITCH_USER_OFF    => 'Se connecter en tant que - Fin',
        ];
    }

    /**
     * @return array
     */
    public static function getActionsKeys()
    {
        return \array_keys(self::getActions());
    }
}
