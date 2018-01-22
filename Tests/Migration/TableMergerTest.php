<?php
namespace Cyberhouse\DoctrineORM\Tests\Migration;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Migration\TableMerger;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * Test a table schema merge
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class TableMergerTest extends \PHPUnit_Framework_TestCase
{
    public function testMergeTwoTables()
    {
        $c1 = new Column('a', Type::getType(Type::INTEGER));
        $c2 = new Column('b', Type::getType(Type::DATETIME));
        $c3 = new Column('c', Type::getType(Type::STRING));

        $i1 = new Index('i1', ['a']);
        $i2 = new Index('i2', ['b']);

        $fk = new ForeignKeyConstraint(['a'], new Table('bbb'), ['e'], 'f1');

        $t1 = new Table('aaa', [$c1, $c2], [$i1, $i2]);
        $t2 = new Table('aaa', [$c2, $c3, $c1], [$i1], [$fk]);

        $expected = new Table('aaa', [$c1, $c2, $c3], [$i1, $i2], [$fk]);

        $obj = new TableMerger($t1);
        $actual = $obj->mergeWith($t2);

        $this->assertEquals($expected, $actual);
    }
}
