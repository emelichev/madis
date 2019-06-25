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

namespace App\Application\Symfony\EventSubscriber\Doctrine;

use App\Application\Traits\Model\HistoryTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class HistorySubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * PrePersist
     * - Add createdAt date
     * - Add updatedAt date.
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        $uses   = \class_uses($object);

        if (\in_array(HistoryTrait::class, $uses)) {
            $object->setCreatedAt(new \DateTimeImmutable());
            $object->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    /**
     * PreUpdate
     * - Update updatedAt date.
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (\method_exists($object, 'setUpdatedAt')) {
            $object->setUpdatedAt(new \DateTimeImmutable());
        }
    }
}
