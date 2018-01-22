<?php
namespace Cyberhouse\DoctrineORM\Tests\Database;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Database\CreateTablePrinter;

/**
 * Test the pretty printer
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class CreateTablePrinterTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        if (!defined('LF')) {
            define('LF', "\n");
        }
    }

    public function snippetsProvider()
    {
        return [
            [
                'CREATE TABLE `be_groups` (uid int(11) unsigned NOT NULL auto_increment, pid int(11) unsigned DEFAULT \'0\' NOT NULL, tstamp int(11) unsigned DEFAULT \'0\' NOT NULL, title varchar(50) DEFAULT \'\' NOT NULL, non_exclude_fields text, explicit_allowdeny text, PRIMARY KEY (uid), KEY idx_title (pid,title(255)), KEY parent (pid))',
                '#
# Table structure for table \'be_groups\'
#
CREATE TABLE be_groups (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT \'0\' NOT NULL,
  tstamp int(11) unsigned DEFAULT \'0\' NOT NULL,
  title varchar(50) DEFAULT \'\' NOT NULL,
  non_exclude_fields text,
  explicit_allowdeny text,
  PRIMARY KEY (uid),
  KEY idx_title (pid,title(255)),
  KEY parent (pid)
);
'
            ],
            [
                'CREATE TABLE `be_groups` (uid int(11) unsigned NOT NULL auto_increment, pid int(11) unsigned DEFAULT \'0\' NOT NULL, PRIMARY KEY (uid)) COLLATE=utf8_general_ci',
                '#
# Table structure for table \'be_groups\'
#
CREATE TABLE be_groups (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT \'0\' NOT NULL,
  PRIMARY KEY (uid)
) COLLATE=utf8_general_ci;
'
            ],
        ];
    }

    /**
     *
     * @dataProvider snippetsProvider
     * @param string $src
     * @param string $expected
     */
    public function testCommonCreateTableSnippet($src, $expected)
    {
        $obj = new CreateTablePrinter();
        $actual = $obj->getStatement($src);
        $this->assertSame($expected, $actual);
    }
}
