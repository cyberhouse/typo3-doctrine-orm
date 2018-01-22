<?php
namespace Cyberhouse\DoctrineORM\Database;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

/**
 * Test the identifer quotes utility
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class IdentifierQuotesTest extends \PHPUnit_Framework_TestCase
{
    public function testHasQuotes()
    {
        $obj = new IdentifierQuotes();
        $this->assertTrue($obj->has(' `table`'));
        $this->assertFalse($obj->has(' table'));
    }

    public function testRemoveQuotes()
    {
        $tests = [
            'table',
            'table`',
            '`table',
            '`table`',
            '`table` ',
        ];
        $expected = 'table';
        $obj = new IdentifierQuotes();

        foreach ($tests as $fixture) {
            $actual = $obj->remove($fixture);
            $this->assertSame($expected, $actual);
        }

        $tests = [
            '   ` table `  ',
            ' ` table `',
            '` table ` ',
        ];
        $expected = ' table ';

        foreach ($tests as $fixture) {
            $actual = $obj->remove($fixture);
            $this->assertSame($expected, $actual);
        }
    }
}
