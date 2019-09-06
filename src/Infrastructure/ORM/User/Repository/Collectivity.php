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

namespace App\Infrastructure\ORM\User\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\User\Model;
use App\Domain\User\Repository;

class Collectivity extends CRUDRepository implements Repository\Collectivity
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Collectivity::class;
    }

    /**
     * {@inheritdoc}
     */
    public function findByIds(array $ids): array
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->andWhere($qb->expr()->in('o.id', ':in_ids'))
            ->setParameter('in_ids', $ids)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByTypes(array $types, ?Model\Collectivity $excludedCollectivity = null): array
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->andWhere($qb->expr()->in('o.type', ':in_types'))
            ->setParameter('in_types', $types)
        ;

        if (null !== $excludedCollectivity) {
            $qb
                ->andWhere($qb->expr()->neq('o.id', ':excluded_collectivity_id'))
                ->setParameter('excluded_collectivity_id', $excludedCollectivity->getId()->toString())
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
