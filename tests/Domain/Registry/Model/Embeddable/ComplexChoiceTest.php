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

namespace App\Tests\Domain\Registry\Model\Embeddable;

use App\Domain\Registry\Model\Embeddable\ComplexChoice;
use PHPUnit\Framework\TestCase;

class ComplexChoiceTest extends TestCase
{
    public function testConstruct()
    {
        $model = new ComplexChoice();

        $this->assertFalse($model->isCheck());
    }
}
