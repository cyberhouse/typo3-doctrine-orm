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

use TYPO3\Components\TestingFramework\Core\BaseTestCase;

/**
 * Test the migration merger
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class MigrationMergerTest extends BaseTestCase
{
    public function testMerges()
    {
        $start = [
            'CREATE TABLE aaaa ();',
        ];
    }
}
