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

use Cyberhouse\DoctrineORM\Migration\DoctrineConnectionMigrator;
use Cyberhouse\DoctrineORM\Migration\MigrationMerger;
use Cyberhouse\DoctrineORM\Utility\EntityManagerFactory;
use Cyberhouse\DoctrineORM\Utility\ExtensionRegistry;
use Doctrine\ORM\EntityManager;
use Nimut\TestingFramework\TestCase\AbstractTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Test the connection migrator
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineConnectionMigratorTest extends AbstractTestCase
{
    public function testExistingIsNotChangedIfNoEntityManagers()
    {
        $factory = $this->getMockBuilder(EntityManagerFactory::class)->disableOriginalConstructor()->getMock();
        $factory->expects($this->never())->method('get');

        $start = ['blaa', 'bluub'];

        $registry = $this->getMockBuilder(ExtensionRegistry::class)->disableOriginalConstructor()->getMock();
        $registry->expects($this->once())
            ->method('getRegisteredExtensions')
            ->will($this->returnValue([]));

        $merger = $this->getMockBuilder(MigrationMerger::class)->disableOriginalConstructor()->getMock();

        $om = $this->getMockBuilder(ObjectManager::class)->disableOriginalConstructor()->getMock();
        $om->expects($this->once())->method('get')
            ->with($this->equalTo(MigrationMerger::class))
            ->will($this->returnValue($merger));

        $merger->expects($this->once())->method('initialize')->with($this->equalTo($start));
        $merger->expects($this->once())->method('getResult')->will($this->returnValue($start));
        $merger->expects($this->never())->method('mergeWith');

        $cls = $this->buildAccessibleProxy(DoctrineConnectionMigrator::class);
        $obj = new $cls();

        $obj->_set('factory', $factory);
        $obj->_set('registry', $registry);
        $obj->_set('objectManager', $om);

        $actual = $obj->addEntitySQL($start);
        $this->assertSame([$start], $actual);
    }

    public function testMergerIsCalledPerEntityManager()
    {
        $ems = [
            'ext1' => $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock(),
            'ext2' => $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock(),
        ];

        $factory = $this->getMockBuilder(EntityManagerFactory::class)->disableOriginalConstructor()->getMock();
        $factory->expects($this->exactly(count($ems)))->method('get')->will($this->returnValueMap([
            ['ext1', $ems['ext1']],
            ['ext2', $ems['ext2']],
        ]));

        $start = ['blaa', 'bluub'];

        $registry = $this->getMockBuilder(ExtensionRegistry::class)->disableOriginalConstructor()->getMock();
        $registry->expects($this->once())
            ->method('getRegisteredExtensions')
            ->will($this->returnValue(array_keys($ems)));

        $merger = $this->getMockBuilder(MigrationMerger::class)->disableOriginalConstructor()->getMock();
        $merger->expects($this->once())->method('initialize')->with($this->equalTo($start));
        $merger->expects($this->exactly(count($ems)))->method('mergeWith')->withConsecutive(
            [$ems['ext1'], 'ext1'],
            [$ems['ext2'], 'ext2']
        );

        $om = $this->getMockBuilder(ObjectManager::class)->disableOriginalConstructor()->getMock();
        $om->expects($this->once())->method('get')
            ->with($this->equalTo(MigrationMerger::class))
            ->will($this->returnValue($merger));

        $merger->expects($this->once())->method('getResult')->will($this->returnValue($start));

        $cls = $this->buildAccessibleProxy(DoctrineConnectionMigrator::class);
        $obj = new $cls();

        $obj->_set('factory', $factory);
        $obj->_set('registry', $registry);
        $obj->_set('objectManager', $om);

        $actual = $obj->addEntitySQL($start);
        $this->assertSame([$start], $actual);
    }
}
