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

use Cyberhouse\DoctrineORM\Migration\MigrationMerger;
use Cyberhouse\DoctrineORM\Migration\SchemaPrinter;
use Cyberhouse\DoctrineORM\Migration\TableMerger;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;
use Doctrine\DBAL\Schema\SqliteSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;
use TYPO3\CMS\Core\Database\Schema\Parser\Parser;
use TYPO3\CMS\Core\Database\Schema\SqlReader;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\Components\TestingFramework\Core\BaseTestCase;

/**
 * Test the migration merger
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class MigrationMergerTest extends BaseTestCase
{
    public function testInitializeCreateSchemaFromSql()
    {
        $start = [
            'CREATE TABLE aaaa (f1);',
            'CREATE TABLE bbbb (f1);',
            'CREATE TABLE aaaa (f2);',
        ];

        $t1 = new Table('aaaa');
        $t2 = new Table('bbbb');
        $t1Ext = new Table('aaaa');

        $reader = $this->getMockBuilder(SqlReader::class)->disableOriginalConstructor()->getMock();
        $reader->expects($this->atLeastOnce())
            ->method('getCreateTableStatementArray')
            ->willReturnMap([
                [$start[0], [$start[0]]],
                [$start[1], [$start[1]]],
                [$start[2], [$start[2]]],
            ]);

        $p1 = $this->getMockBuilder(Parser::class)->disableOriginalConstructor()->getMock();
        $p1->expects($this->once())
            ->method('parse')
            ->will($this->returnValue([$t1]));

        $p2 = $this->getMockBuilder(Parser::class)->disableOriginalConstructor()->getMock();
        $p2->expects($this->once())
            ->method('parse')
            ->will($this->returnValue([$t2]));

        $p3 = $this->getMockBuilder(Parser::class)->disableOriginalConstructor()->getMock();
        $p3->expects($this->once())
            ->method('parse')
            ->will($this->returnValue([$t1Ext]));

        $mg = $this->getMockBuilder(TableMerger::class)->setConstructorArgs([$t1])->getMock();
        $mg->expects($this->once())->method('mergeWith')->with($this->equalTo($t1Ext))->will($this->returnValue($t1));

        $om = $this->getMockBuilder(ObjectManager::class)->disableOriginalConstructor()->getMock();
        $om->expects($this->any())
            ->method('get')
            ->willReturnMap([
                [SqlReader::class, $reader],
                [Parser::class, $start[0], $p1],
                [Parser::class, $start[1], $p2],
                [Parser::class, $start[2], $p3],
                [TableMerger::class, $t1, $mg],
            ]);

        $cls = $this->buildAccessibleProxy(MigrationMerger::class);
        $obj = new $cls();
        $obj->_set('objectManager', $om);
        $obj->initialize($start);
    }

    public function testMergeTwoSchemas()
    {
        $t1 = new Table('aaa');
        $t2 = new Table('bbb');

        $s1 = new Schema([$t1, $t2]);
        $s2 = new Schema([$t2, $t1]);

        $ext = 'my_ext';
        $metaData = ['some', 'meta', 'data'];

        $metaFactory = $this->getMockBuilder(ClassMetadataFactory::class)->disableOriginalConstructor()->getMock();
        $metaFactory->expects($this->once())->method('getAllMetaData')->will($this->returnValue($metaData));

        $tool = $this->getMockBuilder(SchemaTool::class)->disableOriginalConstructor()->getMock();
        $tool->expects($this->once())->method('getSchemaFromMetadata')->with($this->equalTo($metaData))->will($this->returnValue($s2));

        $merge1 = $this->getMockBuilder(TableMerger::class)->disableOriginalConstructor()->getMock();
        $merge1->expects($this->once())->method('mergeWith')->with($this->equalTo($t1))->will($this->returnValue($t1));

        $merge2 = $this->getMockBuilder(TableMerger::class)->disableOriginalConstructor()->getMock();
        $merge2->expects($this->once())->method('mergeWith')->with($this->equalTo($t2))->will($this->returnValue($t2));

        $config = $this->getMockBuilder(SchemaConfig::class)->disableOriginalConstructor()->getMock();

        $mgm = $this->getMockBuilder(SqliteSchemaManager::class)->disableOriginalConstructor()->getMock();
        $mgm->expects($this->once())->method('createSchemaConfig')->will($this->returnValue($config));

        $cnx = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $cnx->expects($this->once())->method('getSchemaManager')->will($this->returnValue($mgm));

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('getMetaDataFactory')->will($this->returnValue($metaFactory));
        $em->expects($this->once())->method('getConnection')->will($this->returnValue($cnx));

        $expected = new Schema([$t1, $t2], [], $config, []);

        $om = $this->getMockBuilder(ObjectManager::class)->getMock();
        $om->expects($this->atLeastOnce())->method('get')->will($this->returnValueMap([
            [SchemaTool::class, $em, $tool],
            [TableMerger::class, $t1, $merge1],
            [TableMerger::class, $t2, $merge2],
            [Schema::class, [$t1, $t2], [], $config, [], $expected],
        ]));

        $cls = $this->buildAccessibleProxy(MigrationMerger::class);
        $obj = new $cls();

        $obj->_set('schema', $s1);
        $obj->_set('objectManager', $om);

        $obj->mergeWith($em, $ext);

        $this->assertSame($ext, $obj->_get('extension'));
        $this->assertEquals($expected, $obj->_get('schema'));
    }

    public function testGetResultImplodesWithSemicolon()
    {
        $sqls = [
            'CREATE TABLE aaa()',
            'CREATE TABLE bbb()',
        ];
        $ext = 'my_ext';
        $schema = $this->getMockBuilder(Schema::class)->disableOriginalConstructor()->getMock();
        $printer = $this->getMockBuilder(SchemaPrinter::class)->disableOriginalConstructor()->getMock();
        $printer->expects($this->once())
            ->method('toSQL')
            ->with($this->equalTo($schema), $this->equalTo($ext))
            ->will($this->returnValue($sqls));

        $om = $this->getMockBuilder(ObjectManager::class)->getMock();
        $om->expects($this->once())->method('get')->with(SchemaPrinter::class)->will($this->returnValue($printer));

        $cls = $this->buildAccessibleProxy(MigrationMerger::class);
        $obj = new $cls();
        $obj->_set('extension', $ext);
        $obj->_set('schema', $schema);
        $obj->_set('objectManager', $om);

        $expected = [
            $sqls[0] . ';' . LF,
            $sqls[1] . ';' . LF,
        ];
        $actual = $obj->getResult();

        $this->assertSame($expected, $actual);
    }
}
