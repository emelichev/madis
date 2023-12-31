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

namespace App\Tests\Infrastructure\ORM\Maturity\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository as DomainRepo;
use App\Domain\User\Model\Collectivity;
use App\Infrastructure\ORM\Maturity\Repository as InfraRepo;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SurveyTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var ManagerRegistry
     */
    private $registryProphecy;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerProphecy;

    /**
     * @var InfraRepo\Survey
     */
    private $infraRepo;

    public function setUp(): void
    {
        $this->registryProphecy      = $this->prophesize(ManagerRegistry::class);
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->infraRepo = new InfraRepo\Survey($this->registryProphecy->reveal(), '60');
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DomainRepo\Survey::class, $this->infraRepo);
        $this->assertInstanceOf(CRUDRepository::class, $this->infraRepo);
    }

    /**
     * Test getModelClass.
     *
     * @throws \ReflectionException
     */
    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Survey::class,
            $this->invokeMethod($this->infraRepo, 'getModelClass')
        );
    }

    /**
     * Test findAllByCollectivity.
     */
    public function testFindAllByCollectivity()
    {
        $collectivity = new Collectivity();
        $orderKey     = 'key';
        $orderDir     = 'asc';
        $results      = ['dummyResult'];

        // Query
        $queryProphecy = $this->prophesize(AbstractQuery::class);
        $queryProphecy->getResult()->shouldBeCalled()->willReturn($results);

        // QueryBuilder
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy
            ->select('o')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->from(Model\Survey::class, 'o')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->andWhere('o.collectivity = :collectivity')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->setParameter('collectivity', $collectivity)
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->addOrderBy("o.{$orderKey}", $orderDir)
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->getQuery()
            ->shouldBeCalled()
            ->willReturn($queryProphecy->reveal())
        ;

        // EntityManager
        $this->entityManagerProphecy
            ->createQueryBuilder()
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy->reveal());

        // Registry
        $this->registryProphecy
            ->getManager()
            ->shouldBeCalled()
            ->willReturn($this->entityManagerProphecy->reveal())
        ;

        $this->assertEquals(
            $results,
            $this->infraRepo->findAllByCollectivity($collectivity, [$orderKey => $orderDir])
        );
    }

    /**
     * Test findPreviousById.
     */
    public function testFindPreviousById()
    {
        $id      = 'uuid';
        $limit   = 2;
        $results = ['dummyResult'];

        $referentiel = new Model\Referentiel();
        $referentiel->setName('ref');

        // Query
        $queryProphecy = $this->prophesize(AbstractQuery::class);
        $queryProphecy->getResult()->shouldBeCalled()->willReturn($results);

        // QueryBuilder
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);

        $queryBuilderProphecy->select('o')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->select('s')->shouldBeCalled()->willReturn($queryBuilderProphecy);

        $queryBuilderProphecy->from(Model\Survey::class, 'o')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->from(Model\Survey::class, 's')->shouldBeCalled()->willReturn($queryBuilderProphecy);

        $queryBuilderProphecy->andWhere('o.id = :id')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->andWhere('o.collectivity = s.collectivity')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy
            ->andWhere('o.referentiel = s.referentiel')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy->andWhere('o.createdAt > s.createdAt')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->orderBy('s.createdAt', 'DESC')->shouldBeCalled()->willReturn($queryBuilderProphecy);

        $queryBuilderProphecy->setMaxResults($limit)->shouldBeCalled()->willReturn($queryBuilderProphecy);

        $queryBuilderProphecy->setParameter('id', $id)->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->getQuery()->shouldBeCalled()->willReturn($queryProphecy->reveal());

        // EntityManager
        $this->entityManagerProphecy
            ->createQueryBuilder()
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy->reveal());

        // Registry
        $this->registryProphecy
            ->getManager()
            ->shouldBeCalled()
            ->willReturn($this->entityManagerProphecy->reveal())
        ;

        $this->assertEquals(
            $results,
            $this->infraRepo->findPreviousById($id, $limit)
        );
    }
}
