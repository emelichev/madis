<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Domain\Reporting\Handler;

use App\Domain\Reporting\Generator\Csv\CollectivityGenerator;
use App\Domain\Reporting\Generator\Csv\TreatmentGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportCsvHandler
{
    const COLLECTIVITY_TYPE = 'collectivity';
    const TREATMENT_TYPE    = 'treatment';

    /**
     * @var CollectivityGenerator
     */
    private $collectivityGenerator;

    /**
     * @var TreatmentGenerator
     */
    private $treatmentGenerator;

    public function __construct(CollectivityGenerator $collectivityGenerator, TreatmentGenerator $treatmentGenerator)
    {
        $this->collectivityGenerator = $collectivityGenerator;
        $this->treatmentGenerator    = $treatmentGenerator;
    }

    public function generateCsv(string $type): BinaryFileResponse
    {
        switch ($type) {
            case self::COLLECTIVITY_TYPE:
                return $this->collectivityGenerator->generateResponse(self::COLLECTIVITY_TYPE);
                break;
            case self::TREATMENT_TYPE:
                return $this->treatmentGenerator->generateResponse(self::TREATMENT_TYPE);
                break;
            default:
                throw new \LogicException('The type ' . $type . ' is not support for csv export');
        }
    }
}