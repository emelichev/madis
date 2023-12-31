<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Application\Doctrine\Repository\DataTablesRepository;

interface AnalyseImpact extends CRUDRepositoryInterface, DataTablesRepository
{
    public function findOneByIdWithoutInvisibleScenarios(string $id);
}
