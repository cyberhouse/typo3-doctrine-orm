<?php
namespace Cyberhouse\DoctrineORM\Tests\Persistence;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Persistence\DoctrineQueryResult;
use Doctrine\ORM\AbstractQuery;

/**
 * Test the query result wrapper
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineQueryResultTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementationConformsToInterface()
    {
        $data = [
            'a',
            'b',
        ];

        $query = $this->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query->expects($this->once())
            ->method('getResult')
            ->with($this->equalTo(AbstractQuery::HYDRATE_OBJECT))
            ->will($this->returnValue($data));

        $result = new DoctrineQueryResult($query, false);
        $this->assertSame($data[0], $result->getFirst());

        // The following checks basically test PHP itself, but we need to make sure
        $this->assertSame(2, $result->count());

        $called = 0;
        foreach ($result as $i => $val) {
            $this->assertSame($data[$i], $val);
            $called++;
        }

        $this->assertSame(2, $called);

        $this->assertSame($query, $result->getQuery());
        $this->assertSame($data, $result->toArray());
        $this->assertSame($data[0], $result[0]);
        $this->assertTrue(isset($result[1]));

        unset($result[1]);
        $this->assertFalse(isset($result[1]));

        $result[1] = $data[1];
        $this->assertTrue(isset($result[1]));
    }
}
