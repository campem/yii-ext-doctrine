<?php

namespace KodeFoundry\Test\YiiFramework\Extension\Doctrine\Component;

/**
 *
 * @author  Kevin Bradwick <kevin@kodefoundry.com>
 * @package KodeFoundry\YiiExt\Tests
 * @license
 */
class CacheComponentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if there is no configuration then the ArrayCache is implemented
     */
    public function testDefaultCacheIsArrayCache()
    {
        $component = new \KodeFoundry\Doctrine\Component\CacheComponent();
        $cache = $component->getCache();

        $this->assertInstanceOf('Doctrine\Common\Cache\ArrayCache', $cache);
        $this->assertSame($cache, $component->getCache());
    }

    /**
     * Test memcache
     */
    public function testMemcacheDriverConfig()
    {
        $component = new \KodeFoundry\Doctrine\Component\CacheComponent();
        $component->setCaches(array(
            'memcache' => array(
                'class' => 'Doctrine\Common\Cache\MemcacheCache',
                'servers' => array(
                    array(
                        'host' => 'localhost',
                        'port' => 11211,
                    )
                ),
            ),
        ));

        $cache = $component->getCache('memcache');
        $this->assertInstanceOf('Doctrine\Common\Cache\MemcacheCache', $cache);
        $this->assertSame($cache, $component->getCache('memcache'));
    }


    /**
     * Default memcache server
     */
    public function testDefaultMemcacheServer()
    {
        $component = new \KodeFoundry\Doctrine\Component\CacheComponent();
        $component->setCaches(array(
            'default' => array(
                'class' => 'Doctrine\Common\Cache\MemcacheCache',
            ),
        ));

        $cache = $component->getCache('default');
        $this->assertInstanceOf('Doctrine\Common\Cache\MemcacheCache', $cache);
        $this->assertSame($cache, $component->getCache('default'));
    }

    public function testApcCache()
    {
        $component = new \KodeFoundry\Doctrine\Component\CacheComponent();
        $component->setCaches(array(
            'apc' => array(
                'class' => 'Doctrine\Common\Cache\ApcCache',
            ),
        ));

        $cache = $component->getCache('apc');
        $this->assertInstanceOf('Doctrine\Common\Cache\ApcCache', $cache);
        $this->assertSame($cache, $component->getCache('apc'));
    }
}
