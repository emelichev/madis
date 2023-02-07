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

namespace App\Domain\Maturity\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ReferentielAnswer
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $recommendation;

    /**
     * @var ReferentielQuestion|null
     */
    private $referentielQuestion;

    /**
     * @var int|null
     */
    public $orderNumber;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    public function setRecommendation(?string $recommendation): void
    {
        $this->recommendation = $recommendation;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?int $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function getReferentielQuestion(): ?ReferentielQuestion
    {
        return $this->referentielQuestion;
    }

    public function setReferentielQuestion(?ReferentielQuestion $referentielQuestion): void
    {
        $this->referentielQuestion = $referentielQuestion;
    }
}
