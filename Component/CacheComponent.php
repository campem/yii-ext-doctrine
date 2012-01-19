<?php

namespace KodeFoundry\Doctrine\Component;

/**
 *
 * @author  Kevin Bradwick <kbradwick@gmail.com>
 * @package KodeFoundry\Doctrine
 * @license New BSD http://www.opensource.org/licenses/bsd-license.php
 * @link    https://github.com/kodefoundry/yii-ext-doctrine/
 */
class CacheComponent extends \CApplicationComponent
{
    /**
     * @var string
     */
    protected $default = 'default';

    /**
     * @var array
     */
    protected $caches = array();

    /**
     * @var array
     */
    private $_cachedObjects = array();

    /**
     * @param array $caches
     */
    public function setCaches(array $caches)
    {
        $this->caches = $caches;
    }

    /**
     * @return array
     */
    public function getCaches()
    {
        return $this->caches;
    }

    /**
     * @param string $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

   /**
    * Get a cache driver
    *
    * @param string $cache
    * @return \Doctrine\Common\Cache\Cache
    */
    public function getCache($cache='default')
    {
        if (isset($this->_cachedObjects[$cache]) === true) {
            return $this->_cachedObjects[$cache];
        }

        // if there is not a configuration for the cache, the default cache driver will be returned
        if (isset($this->caches[$cache]) === false) {
            $driver = new \Doctrine\Common\Cache\ArrayCache();
            $this->_cachedObjects[$cache] = $driver;
            return $driver;
        }

        $driver = $this->_createCache($cache);
        $this->_cachedObjects[$cache] = $driver;
        return $driver;
    }

    /**
     * Create a new cache driver
     *
     * @param $cache
     * @return \Doctrine\Common\Cache\CacheProvider
     * @throws \Doctrine\Common\CommonException
     */
    private function _createCache($cache)
    {
        $defaults = array(
            'class' => 'Doctrine\Common\Cache\ArrayCache',
            'namespace' => '__kodefoundry_yiiext_doctrine',
        );

        $config = array_merge($defaults, $this->caches[$cache]);

        if (class_exists($config['class']) === false) {
            throw new \Doctrine\Common\CommonException(\Yii::t('kf.ext', 'Unknown class "{class}" specified in the cache configuration', array(
                '{class}' => $config['class']
            )));
        }

        $driver = new $config['class'];

        // Memcache specific
        if ($driver instanceof \Doctrine\Common\Cache\MemcacheCache) {
            $driver->setMemcache($this->_createMemcacheDriver($config));
        }

        // Memcached specific
        if ($driver instanceof \Doctrine\Common\Cache\MemcachedCache) {
            $driver->setMemcached($this->_createMemcachedDriver($config));
        }

        if ($driver instanceof \Doctrine\Common\Cache\CacheProvider) {
            $driver->setNamespace($config['namespace']);
            $this->_cachedObjects[$cache] = $driver;
            return $driver;
        }

        throw new \Doctrine\Common\CommonException(\Yii::t(
            'kf.ext',
            'The cache driver {driver} must implement \Doctrine\Common\Cache\CacheProvider',
            array('{driver}' => $config['class'])
        ));
    }

    /**
     * Create a Memcached object
     *
     * @param array $config
     * @return \Memcached
     */
    private function _createMemcachedDriver(array $config)
    {
        $defaultServer = array(
            'host' => 'localhost',
            'port' => 11211,
        );

        $memcached = new \Memcached();

        if (isset($config['servers']) === true) {
            foreach ($config['servers'] as $server) {
                $server = array_replace_recursive($defaultServer, $server);
                $memcached->addServer(
                    $server['host'],
                    $server['port']
                );
            }
        } else {
            $memcached->addserver(
                $defaultServer['host'],
                $defaultServer['port']
            );
        }

        return $memcached;
    }

    /**
     * Create a memcache driver and return it
     *
     * @param array $config
     * @return \Memcache
     */
    private function _createMemcacheDriver(array $config)
    {
        $defaultServer = array(
            'host'          => 'localhost',
            'port'          => 11211,
            'persistent'    => true,
            'weight'        => 1,
            'timeout'       => 1,
            'retryInterval' => 15,
            'status'        => true
        );

        $memcache = new \Memcache();

        if (isset($config['servers']) === true) {
            foreach ($config['servers'] as $server) {
                $server = array_replace_recursive($defaultServer, $server);
                $memcache->addserver(
                    $server['host'],
                    $server['port'],
                    $server['persistent'],
                    $server['weight'],
                    $server['timeout'],
                    $server['retryInterval'],
                    $server['status']
                );
            }
        } else {
            $memcache->addserver(
                $defaultServer['host'],
                $defaultServer['port'],
                $defaultServer['persistent'],
                $defaultServer['weight'],
                $defaultServer['timeout'],
                $defaultServer['retryInterval'],
                $defaultServer['status']
            );
        }

        return $memcache;
    }

}
