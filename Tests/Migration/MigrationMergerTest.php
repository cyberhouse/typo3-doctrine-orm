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
use Doctrine\DBAL\Schema\Table;
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
            'CREATE TABLE aaaa ();',
        ];

        $table1 = new Table('aaaa');

        $reader = $this->getMockBuilder(SqlReader::class)->disableOriginalConstructor()->getMock();
        $reader->expects($this->once())
            ->method('getCreateTableStatementArray')
            ->with($this->equalTo($start[0]))
            ->will($this->returnValue($start));

        $parser = $this->getMockBuilder(Parser::class)->disableOriginalConstructor()->getMock();
        $parser->expects($this->once())
            ->method('parse')
            ->will($this->returnValue([$table1]));

        $om = $this->getMockBuilder(ObjectManager::class)->disableOriginalConstructor()->getMock();
        $om->expects($this->any())
            ->method('get')
            ->willReturnMap([
                [SqlReader::class, $reader],
                [Parser::class, $start[0], $parser],
            ]);

        $cls = $this->buildAccessibleProxy(MigrationMerger::class);
        $obj = new $cls();
        $obj->_set('objectManager', $om);
        $obj->initialize($start);
    }
}
