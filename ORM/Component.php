<?php

namespace KodeFoundry\Doctrine\ORM;

// some path constants instead of using really long strings
define('VENDOR_DIR', realpath(__DIR__ . '/../vendor'), true);
define('DOCTRINE_COMMON_DIR', VENDOR_DIR . '/doctrine2/lib/vendor/doctrine-common/lib', true);
define('DOCTRINE_DBAL_DIR', VENDOR_DIR . '/doctrine2/lib/vendor/doctrine-dbal/lib', true);
define('DOCTRINE_ORM_DIR', VENDOR_DIR . '/doctrine2/lib', true);
define('SYMFONY_DIR', VENDOR_DIR . '/doctrine2/lib/vendor', true);
define('CACHE_DIR', realpath(__DIR__ . '/../../cache'), true);

// load the doctrine class loader
require_once DOCTRINE_COMMON_DIR . '/Doctrine/Common/Classloader.php';

// use some namespaces from doctrine
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

/**
 * ORM Component
 *
 * @author  Kevin Bradwick <kbradwick@gmail.com>
 * @package KodeFoundry\Doctrine
 * @license New BSD http://www.opensource.org/licenses/bsd-license.php
 */
class Component extends \CApplicationComponent
{
    /**
     * A common key that is used to define a default index in a configuration array
     */
    const DEFAULT_KEY = 'default';

    /**
     * The cache configuration
     * @var array
     */
    protected $caches = array();

    /**
     * The entity manager configuration
     * @var array
     */
    protected $entityManagers = array();

    /**
     * The connection configurations
     * @var array
     */
    protected $connections = array();

    /**
     * A place to store objects
     * @var array
     */
    private $_cachedObjects = array();

    /**
     * Component initialisation
     *
     * @return null
     */
    public function init()
    {
        $classLoader = new ClassLoader('Doctrine\Common', DOCTRINE_COMMON_DIR);
        \Yii::registerAutoloader(array($classLoader, 'loadClass'));

        $classLoader = new ClassLoader('Doctrine\DBAL', DOCTRINE_DBAL_DIR);
        \Yii::registerAutoloader(array($classLoader, 'loadClass'));

        $classLoader = new ClassLoader('Doctrine\ORM', DOCTRINE_ORM_DIR);
        \Yii::registerAutoloader(array($classLoader, 'loadClass'));

        $classLoader = new ClassLoader('Symfony', SYMFONY_DIR);
        \Yii::registerAutoloader(array($classLoader, 'loadClass'));
    }

    /**
     * Set the entity manager configuration array
     *
     * @param array $entityManagers
     */
    public function setEntityManagers(array $entityManagers)
    {
        $this->entityManagers = $entityManagers;
    }

    /**
     * Set the cache configurations
     *
     * @param array $caches
     * @return null
     */
    public function setCaches(array $caches)
    {
        $this->caches = $caches;
    }

    /**
     * Set connection configuration
     *
     * @param array $connections
     */
    public function setConnections(array $connections)
    {
        $this->connections = $connections;
    }

    /**
     * Get an entity manager
     *
     * @param string $entityManager
     * @return \Doctrine\ORM\EntityManager
     * @throws \CException
     */
    public function getEntityManager($entityManager=self::DEFAULT_KEY)
    {
        if (isset($this->_cachedObjects['entityManager'][$entityManager]) === true) {
            return $this->_cachedObjects['entityManager'][$entityManager];
        }

        if (isset($this->entityManagers[$entityManager]) === false) {
            throw new \CException(\Yii::t('kf.ext', 'There is no Entity Manager specified by the name of "{name}"', array(
                '{name}' => $entityManager,
            )));
        }

        $defaults = array(
            'metadataCache' => 'default',
            'metadataDriver' => null,
            'entityPaths' => array(),
            'queryCache' => 'default',
            'autoGenerateProxyClasses' => true,
            'proxyDirectory' => CACHE_DIR . '/proxies',
            'proxyNamespace' => 'KodeFoundryProxy',
            'connection' => 'default',
        );

        $options = array_merge($defaults, $this->entityManagers[$entityManager]);

        $config = new Configuration();
        $config->setMetadataCacheImpl($this->getCache($options['metadataCache']));
        $config->setQueryCacheImpl($this->getCache($options['queryCache']));
        $config->setAutoGenerateProxyClasses((bool) $options['autoGenerateProxyClasses']);
        $config->setProxyDir($options['proxyDirectory']);
        $config->setProxyNamespace($options['proxyNamespace']);

        if ($options['metadataDriver'] === null) {
            $driverImpl = $config->newDefaultAnnotationDriver($options['entityPaths']);
        } else {
            $driverImpl = $this->_createMetadataDriver($options['metadataDriver']);
        }

        $config->setMetadataDriverImpl($driverImpl);

        $em = \Doctrine\ORM\EntityManager::create($this->getConnection($options['connection']), $config);

        $this->_cachedObjects['entityManager'][$entityManager] = $em;

        return $em;
    }

    /**
     * Get a cache driver
     *
     * @param string $cache
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache($cache=self::DEFAULT_KEY)
    {
        if (isset($this->_cachedObjects['cache'][$cache]) === true) {
            return $this->_cachedObjects['cache'][$cache];
        }

        // if there is not a configuration for the cache, the default cache driver will be returned
        if (isset($this->caches[$cache]) === false) {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
            $this->_cachedObjects['cache'][self::DEFAULT_KEY] = $cache;
            return $cache;
        }

        return $this->_createCache($cache);
    }

    /**
     * Create a metadata driver
     *
     * @param array $options
     * @return \Doctrine\ORM\Mapping\Driver\YamlDriver
     * @throws \CException
     */
    private function _createMetadataDriver(array $options)
    {
        // throw exception if paths not set
        if (isset($options['paths']) === false) {
            throw new \CException(\Yii::t('kf.ext', 'No paths set for yaml mapping'));
        }

        $paths = array();
        foreach ($options['paths'] as $path) {
            $paths[] = \Yii::getPathOfAlias($path);
        }

        if ($options['type'] === 'yaml') {
            $driver = new \Doctrine\ORM\Mapping\Driver\YamlDriver($paths);
        } else if ($options['type'] === 'xml') {
            $driver = new \Doctrine\ORM\Mapping\Driver\XmlDriver($paths);
        } else if ($options['type'] === 'php') {
            $driver = new \Doctrine\ORM\Mapping\Driver\PHPDriver($paths);
        } else {
            throw new \CException(\Yii::t(
                'kf.ext',
                'Annotation, PHP, XML and YAML are the only supported metadata drivers of this extension'
            ));
        }

        if (isset($options['fileExtension']) === true) {
            $driver->setFileExtension($options['fileExtension']);
        }

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
            $this->_cachedObjects['cache'][$cache] = $driver;
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

    public function getConnection($connection=self::DEFAULT_KEY)
    {
        if (isset($this->_cachedObjects['connection'][$connection]) === true) {
            return $this->_cachedObjects['connection'][$connection];
        }

        if (isset($this->connections[$connection]) === false) {
            throw new \CException(\Yii::t('kf.ext', 'There is no configuration set for "{name}"', array(
                '{name}' => $connection,
            )));
        }

        $default = array(
            'dbname' => '',
            'user' => '',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );

        $params = array_replace_recursive($default, $this->connections[$connection]);
        $config = new \Doctrine\DBAL\Configuration();

        if (isset($params['resultCache']) === true) {
            unset($params['resultCache']);
            $cache = $this->_createCache($params['resultCache']);
            $config->setResultCacheImpl($cache);
        }

        $conn = \Doctrine\DBAL\DriverManager::getConnection($params, $config);
        $this->_cachedObjects['connection'][$connection] = $conn;

        return $conn;
    }

}