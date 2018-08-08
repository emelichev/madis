<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class TreatmentConcernedPeopleDictionary extends SimpleDictionary
{
    const TYPE_PARTICULAR = 'particular';
    const TYPE_USER       = 'user';
    const TYPE_AGENT      = 'agent';
    const TYPE_ELECTED    = 'elected';
    const TYPE_COMPANY    = 'company';
    const TYPE_PARTNER    = 'partner';

    public function __construct()
    {
        parent::__construct('registry_treatment_concerned_people', self::getTypes());
    }

    /**
     * Get an array of Types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_PARTICULAR => 'Particuliers',
            self::TYPE_USER       => 'Internautes',
            self::TYPE_AGENT      => 'Agents',
            self::TYPE_ELECTED    => 'Élus',
            self::TYPE_COMPANY    => 'Entreprises',
            self::TYPE_PARTNER    => 'Partenaires',
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
