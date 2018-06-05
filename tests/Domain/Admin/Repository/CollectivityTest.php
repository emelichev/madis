<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Domain\Admin\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\Admin\Repository;
use PHPUnit\Framework\TestCase;

class CollectivityTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(
            CRUDRepositoryInterface::class,
            $this->prophesize(Repository\Collectivity::class)->reveal()
        );
    }
}
