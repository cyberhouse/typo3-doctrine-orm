<?php
namespace Cyberhouse\DoctrineORM\Tests\Utility;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Utility\EntityManagerFactory;
use Cyberhouse\DoctrineORM\Utility\ExtensionRegistry;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\Components\TestingFramework\Core\BaseTestCase;

/**
 * Test the entity manager factory
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class EntityManagerFactoryTest extends BaseTestCase
{
    public function testRegisteredExtensionCreatesAnEntityManager()
    {
        if (!defined('PATH_site')) {
            define('PATH_site', '');
        }

        $paths = [__DIR__];
        $extKey = 'my_ext';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['doctrine_orm'] = [
            'devMode'  => true,
            'proxyDir' => sys_get_temp_dir()
        ];

        $ev = $this->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pkg = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pkg->expects($this->once())
            ->method('getPackagePath')
            ->will($this->returnValue(__DIR__));

        $pm = $this->getMockBuilder(PackageManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pm->expects($this->once())
            ->method('isPackageActive')
            ->with($this->equalTo('doctrine_orm'))
            ->will($this->returnValue(true));

        ExtensionManagementUtility::setPackageManager($pm);

        $pm->expects($this->once())
            ->method('getPackage')
            ->with($this->equalTo('doctrine_orm'))
            ->will($this->returnValue($pkg));

        $cnx = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cnx->expects($this->once())
            ->method('getEventManager')
            ->will($this->returnValue($ev));

        $pool = $this->getMockBuilder(ConnectionPool::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pool->expects($this->once())
            ->method('getConnectionForTable')
            ->with($this->equalTo($extKey))
            ->will($this->returnValue($cnx));

        $om = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $om->expects($this->once())
            ->method('get')
            ->with($this->equalTo(ConnectionPool::class))
            ->will($this->returnValue($pool));

        $registry = $this->getMockBuilder(ExtensionRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registry->expects($this->once())
            ->method('getExtensionPaths')
            ->with($this->equalTo($extKey))
            ->will($this->returnValue($paths));

        $cache = new ArrayCache();

        $cm = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'] = [];

        $cm->expects($this->once())
            ->method('getCache')
            ->with($this->equalTo('doctrine_orm'))
            ->will($this->returnValue($cache));

        $class = $this->buildAccessibleProxy(EntityManagerFactory::class);
        $obj = new $class();

        $obj->_set('registry', $registry);
        $obj->_set('objectManager', $om);
        $obj->_set('cacheManager', $cm);

        $em = $obj->get($extKey);

        $this->assertInstanceOf(EntityManager::class, $em);

        $em2 = $obj->get($extKey);

        $this->assertSame($em, $em2);
    }

    public function testUnconfiguredExtensionsThrowExceptionFromRegistry()
    {
        $this->expectException(\UnexpectedValueException::class);

        $cls = $this->buildAccessibleProxy(EntityManagerFactory::class);
        $obj = new $cls();
        $obj->_set('registry', new ExtensionRegistry());
        $obj->get('my_ext');
    }
}
