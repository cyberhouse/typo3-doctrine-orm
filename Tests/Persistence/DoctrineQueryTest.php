<?php
namespace Cyberhouse\DoctrineORM\Tests\Persistence;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Persistence\DoctrineQuery;
use Cyberhouse\DoctrineORM\Persistence\DoctrineQueryResult;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Nimut\TestingFramework\TestCase\AbstractTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\Comparison;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\PropertyValue;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\Selector;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Test the doctrine query object wrapper for interface compliance
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineQueryTest extends AbstractTestCase
{
    public function testInterfaceMethods()
    {
        $ext = 'my_ext';

        $ex = $this->getMockBuilder(Expr::class)->getMock();
        $qb = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $qb->expects($this->any())->method('expr')->will($this->returnValue($ex));
        $qb->expects($this->any())->method('from')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('select')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('setMaxResults')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('setFirstResult')->will($this->returnValue($qb));

        $qb->expects($this->once())->method('setFirstResult')->with($this->equalTo(10));
        $qb->expects($this->once())->method('getFirstResult')->will($this->returnValue(10));

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));
        $q = Query::class;

        $qb->expects($this->once())->method('setMaxResults')->with($this->equalTo(100));
        $qb->expects($this->once())->method('getMaxResults')->will($this->returnValue(100));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($q));

        $res = $this->getMockBuilder(DoctrineQueryResult::class)->disableOriginalConstructor()->getMock();

        $om = $this->getMockBuilder(ObjectManager::class)->getMock();
        $om->expects($this->once())->method('get')
            ->with($this->equalTo(DoctrineQueryResult::class), $this->equalTo($q), $this->equalTo(false))
            ->will($this->returnValue($res));

        try {
            $cls = $this->buildAccessibleProxy(DoctrineQuery::class);
            $query = new $cls($em, $ext);
            $query->_set('objectManager', $om);
            $query->matching($query->logicalAnd(
                $query->not($query->equals('a', 'b')),
                $query->greaterThan('c', 1),
                $query->greaterThanOrEqual('d', 2),
                $query->lessThan('e', 4),
                $query->lessThanOrEqual('f', 5),
                $query->like('g', '%6%'),
                $query->in('h', ['i', 'j']),
                $query->logicalOr($query->isEmpty('a'), $query->contains('a', 'empty'))
            ));
            $query->setOrderings(['a' => 'ASC', 'b' => 'DESC']);

            $query->setOffset(10);
            $query->setLimit(100);

            $this->assertSame(10, $query->getOffset());
            $this->assertSame(100, $query->getLimit());

            $this->assertSame($qb, $query->getSource());
            $this->assertSame($ext, $query->getType());
            $this->assertSame($ex, $query->getConstraint());

            $this->assertSame($res, $query->execute());
        } catch (\Throwable $ex) {
            $this->fail($ex->getMessage());
        }
    }

    public function testCountDoesNotCloseCurrentQuery()
    {
        $q = $this->getMockBuilder(AbstractQuery::class)->disableOriginalConstructor()->getMock();
        $q->expects($this->once())->method('getSingleScalarResult');

        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableArgumentCloning()
            ->disableOriginalClone()
            ->disableOriginalConstructor()
            ->getMock();
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));

        $qb->expects($this->any())->method('from')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('select')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('getQuery')->will($this->returnValue($q));

        $cls = $this->buildAccessibleProxy(DoctrineQuery::class);
        /** @var DoctrineQuery $query */
        $query = new $cls($em, 'test');
        $query->count();
    }

    public function testGetStatementDoesNotCloseQuery()
    {
        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableArgumentCloning()
            ->disableOriginalClone()
            ->disableOriginalConstructor()
            ->getMock();
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));

        $qb->expects($this->any())->method('from')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('select')->will($this->returnValue($qb));
        $qb->expects($this->atLeastOnce())->method('getDQL');

        $cls = $this->buildAccessibleProxy(DoctrineQuery::class);
        /** @var DoctrineQuery $query */
        $query = new $cls($em, 'test');
        $query->getStatement();
    }

    public function testNotSupportedMethodsThrowNotImplementedException()
    {
        $ext = 'my_ext';
        $qb = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $qb->expects($this->never())->method('expr');
        $qb->expects($this->never())->method('from');
        $qb->expects($this->never())->method('select');
        $qb->expects($this->never())->method('setMaxResults');
        $qb->expects($this->never())->method('setFirstResult');

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));

        $query = new DoctrineQuery($em, $ext);

        try {
            $prop = new PropertyValue('a');
            $const = new Comparison($prop, QueryInterface::OPERATOR_EQUAL_TO, 2);
            $query->logicalNot($const);
            $this->fail('No exception on logicalNot');
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(NotImplementedException::class, $ex);
        }

        try {
            $settings = new Typo3QuerySettings();
            $query->setQuerySettings($settings);
            $this->fail('No exception on setQuerySettings');
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(NotImplementedException::class, $ex);
        }

        try {
            $query->getQuerySettings();
            $this->fail('No exception on getQuerySettings');
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(NotImplementedException::class, $ex);
        }

        try {
            $query->getOrderings();
            $this->fail('No exception on getOrderings');
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(NotImplementedException::class, $ex);
        }

        try {
            $source = new Selector('a', 'b');
            $query->setSource($source);
            $this->fail('No exception on setSource');
        } catch (\Throwable $ex) {
            $this->assertInstanceOf(NotImplementedException::class, $ex);
        }
    }
}
