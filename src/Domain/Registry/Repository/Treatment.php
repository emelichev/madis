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

namespace App\Domain\Registry\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\User\Model\Collectivity;

interface Treatment extends CRUDRepositoryInterface
{
    /**
     * Find all treatments.
     *
     * @param bool  $active Get active / inactive treatments
     * @param array $order  Order results
     *
     * @return array The array of treatments
     */
    public function findAllActive(bool $active = true, array $order = []);

    /**
     * Find all treatments by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param array        $order        Order results
     *
     * @return array The array of treatments given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = []);

    /**
     * Find all active treatments by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param bool         $active       Get active / inactive treatment
     * @param array        $order        Order results
     *
     * @return array The array of treatments given by the collectivity
     */
    public function findAllActiveByCollectivity(Collectivity $collectivity, bool $active = true, array $order = []);
}
