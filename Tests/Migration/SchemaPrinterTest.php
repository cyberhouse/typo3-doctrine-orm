<?php
namespace Cyberhouse\DoctrineORM\Tests\Migration;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Database\CreateTablePrinter;
use Cyberhouse\DoctrineORM\Migration\SchemaPrinter;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Schema;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\Components\TestingFramework\Core\BaseTestCase;

/**
 * Test the schema printer
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class SchemaPrinterTest extends BaseTestCase
{
    public function testToSql()
    {
        $ext = 'my_ext';
        $platform = $this->getMockBuilder(SqlitePlatform::class)->disableOriginalConstructor()->getMock();

        $cnx = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $cnx->expects($this->once())->method('getDatabasePlatform')->will($this->returnValue($platform));

        $pool = $this->getMockBuilder(ConnectionPool::class)->getMock();
        $pool->expects($this->once())
            ->method('getConnectionForTable')
            ->with($this->equalTo($ext))
            ->will($this->returnValue($cnx));

        $sqls = [
            'CREATE TABLE aaa ()',
            'ALTER TABLE aaa ()',
        ];

        $schema = $this->getMockBuilder(Schema::class)->disableOriginalConstructor()->getMock();
        $schema->expects($this->once())
            ->method('toSQL')
            ->with($this->equalTo($platform))
            ->will($this->returnValue($sqls));

        $printer = $this->getMockBuilder(CreateTablePrinter::class)->getMock();
        $printer->expects($this->once())
            ->method('getStatement')
            ->with($this->equalTo($sqls[0]), $this->equalTo(false))
            ->will($this->returnValue($sqls[0]));

        $om = $this->getMockBuilder(ObjectManager::class)->getMock();
        $om->expects($this->any())->method('get')->will($this->returnValueMap([
            [ConnectionPool::class, $pool],
            [CreateTablePrinter::class, $printer],
        ]));

        $cls = $this->buildAccessibleProxy(SchemaPrinter::class);
        $obj = new $cls();
        $obj->_set('objectManager', $om);

        $expected = [$sqls[0]];
        $actual = $obj->toSQL($schema, $ext);

        $this->assertSame($expected, $actual);
    }

    public function testMultipleTablesOfSameNameThrowException()
    {
        $this->expectException(\UnexpectedValueException::class);
        $ext = 'my_ext';
        $platform = $this->getMockBuilder(SqlitePlatform::class)->disableOriginalConstructor()->getMock();

        $cnx = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $cnx->expects($this->once())->method('getDatabasePlatform')->will($this->returnValue($platform));

        $pool = $this->getMockBuilder(ConnectionPool::class)->getMock();
        $pool->expects($this->once())
            ->method('getConnectionForTable')
            ->with($this->equalTo($ext))
            ->will($this->returnValue($cnx));

        $sqls = [
            'CREATE TABLE aaa ()',
            'CREATE TABLE aaa ()',
        ];

        $schema = $this->getMockBuilder(Schema::class)->disableOriginalConstructor()->getMock();
        $schema->expects($this->once())
            ->method('toSQL')
            ->with($this->equalTo($platform))
            ->will($this->returnValue($sqls));

        $printer = $this->getMockBuilder(CreateTablePrinter::class)->getMock();
        $printer->expects($this->once())
            ->method('getStatement')
            ->with($this->equalTo($sqls[0]), $this->equalTo(false))
            ->will($this->returnValue($sqls[0]));

        $om = $this->getMockBuilder(ObjectManager::class)->getMock();
        $om->expects($this->any())->method('get')->will($this->returnValueMap([
            [ConnectionPool::class, $pool],
            [CreateTablePrinter::class, $printer],
        ]));

        $cls = $this->buildAccessibleProxy(SchemaPrinter::class);
        $obj = new $cls();
        $obj->_set('objectManager', $om);

        $obj->toSQL($schema, $ext);
    }
}
