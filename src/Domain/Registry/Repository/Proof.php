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

namespace App\Domain\Registry\Repository;

use App\Application\DDD\Repository\RepositoryInterface;
use App\Application\Doctrine\Repository\DataTablesRepository;
use App\Domain\User\Model\Collectivity;

interface Proof extends RepositoryInterface, DataTablesRepository
{
    /**
     * Insert an object.
     */
    public function insert($object): void;

    /**
     * Update an object.
     */
    public function update($object): void;

    /**
     * Create an object.
     */
    public function create();

    /**
     * Remove an object.
     */
    public function remove($object): void;

    /**
     * Get all objects.
     *
     * @return mixed[]
     */
    public function findAll(bool $deleted = false): array;

    /**
     * Get an object by ID.
     *
     * @param string $id The ID to find
     *
     * @return mixed[]
     */
    public function findOneById(string $id);

    /**
     * Find all proofs by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param bool         $deleted      Get deleted rows or not
     * @param array        $order        Order results
     *
     * @return array The array of proofs given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, bool $deleted = false, array $order = []);

    /**
     * Find all proofs by criteria.
     *
     * @param array $criteria List of criteria
     *
     * @return array The array of proofs given by criteria
     */
    public function findBy(array $criteria = []);

    /**
     * Find all active proofs by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param bool         $archived     Get all archived or not
     * @param array        $order        Order results
     *
     * @return array The array of proofs given by the collectivity
     */
    public function findAllArchivedByCollectivity(Collectivity $collectivity, bool $archived = false, array $order = []);

    /**
     * Get the last proof by type and collectivity.
     */
    public function findOneOrNullByTypeAndCollectivity(string $type, Collectivity $collectivity): ?\App\Domain\Registry\Model\Proof;

    /**
     * Count all by collectivity.
     */
    public function countAllByCollectivity(Collectivity $collectivity);

    /**
     * Average of proof filed.
     *
     * @return string The average
     */
    public function averageProofFiled(array $collectivities = []);

    /**
     * Average balance sheet proof created during the last year.
     *
     * @return string The count of mesurements
     */
    public function averageBalanceSheetProof(array $collectivities = []);
}
