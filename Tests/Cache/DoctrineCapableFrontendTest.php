<?php
namespace Cyberhouse\DoctrineORM\Tests\Cache;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Cache\DoctrineCapableFrontend;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;

/**
 * Test the Coctrine - TYPO3 cache wrapper
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineCapableFrontendTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/cache/frontend/class.t3lib_cache_frontend_variablefrontend.php'] = [
            'set'    => [],
            'get'    => [],
            'remove' => [],
            'has'    => [],
        ];
    }

    public function testCoreMethodsAreUntouched()
    {
        $backend = $this->getMockBuilder(TransientMemoryBackend::class)->disableOriginalConstructor()->getMock();
        $cache = new DoctrineCapableFrontend('doctrine', $backend);

        $id = 'identifier';
        $content = 'chunk';

        $backend->expects($this->once())
            ->method('set')
            ->with($this->equalTo($id), $this->equalTo($content));

        $backend->expects($this->once())->method('set')->with($this->equalTo($id), $this->equalTo($content));
        $backend->expects($this->once())->method('get')->with($this->equalTo($id))->will($this->returnValue($content));
        $backend->expects($this->once())->method('has')->with($this->equalTo($id))->will($this->returnValue(true));
        $backend->expects($this->once())->method('remove')->with($this->equalTo($id));

        $cache->set($id, $content);
        $this->assertSame($content, $cache->get($id));
        $this->assertTrue($cache->has($id));
        $cache->remove($id);
    }

    public function testDoctrineMethodsDelegateToCore()
    {
        $backend = $this->getMockBuilder(TransientMemoryBackend::class)->disableOriginalConstructor()->getMock();
        $cache = new DoctrineCapableFrontend('doctrine', $backend);

        $id = 'identifier';
        $content = 'chunk';

        $backend->expects($this->once())->method('set')->with($this->equalTo($id), $this->equalTo($content));
        $backend->expects($this->once())->method('get')->with($this->equalTo($id))->will($this->returnValue($content));
        $backend->expects($this->once())->method('has')->with($this->equalTo($id))->will($this->returnValue(true));
        $backend->expects($this->once())->method('remove')->with($this->equalTo($id));

        $cache->save($id, $content);
        $this->assertSame($content, $cache->fetch($id));
        $this->assertTrue($cache->contains($id));
        $cache->delete($id);
    }

    public function testDoctrineKeysAreFixedForCore()
    {
        $manyChars = str_repeat('abcde', 300);
        $invalidKey = 'abcde\\,??' . $manyChars;
        $validKey = substr('abcde-' . $manyChars, 0, 250);

        $backend = $this->getMockBuilder(TransientMemoryBackend::class)->disableOriginalConstructor()->getMock();
        $cache = new DoctrineCapableFrontend('doctrine', $backend);
        $content = 'chunk';

        $backend->expects($this->once())->method('set')->with($this->equalTo($validKey), $this->equalTo($content));

        try {
            $cache->save($invalidKey, $content);
        } catch (\InvalidArgumentException $ex) {
            $this->fail($ex->getMessage());
        }
    }

    public function testStatsDoesNotDoAnything()
    {
        $backend = $this->getMockBuilder(TransientMemoryBackend::class)->disableOriginalConstructor()->getMock();
        $cache = new DoctrineCapableFrontend('doctrine', $backend);

        $backend->expects($this->never())->method('set');
        $backend->expects($this->never())->method('get');
        $backend->expects($this->never())->method('has');
        $backend->expects($this->never())->method('remove');

        $this->assertNull($cache->getStats());
    }
}
