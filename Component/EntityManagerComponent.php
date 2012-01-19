<?php

namespace KodeFoundry\Doctrine\Component;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

/**
 *
 * @author  Kevin Bradwick <kbradwick@gmail.com>
 * @package KodeFoundry\Doctrine
 * @license New BSD http://www.opensource.org/licenses/bsd-license.php
 */
class EntityManagerComponent extends \CApplicationComponent
{
    /**
     * @var string
     */
    protected $default = 'default';

    /**
     * @var array
     */
    protected $entityManagers = array();

    /**
     * @var array
     */
    private $_cachedObjects = array();

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
     * @param array $entityManagers
     */
    public function setEntityManagers(array $entityManagers)
    {
        $this->entityManagers = $entityManagers;
    }

    /**
     * @return array
     */
    public function getEntityManagers()
    {
        return $this->entityManagers;
    }

    /**
     * Get an entity manager
     *
     * @param string $entityManager
     * @return \Doctrine\ORM\EntityManager
     * @throws \CException
     */
    public function getEntityManager($entityManager='default')
    {
        if (isset($this->_cachedObjects[$entityManager]) === true) {
            return $this->_cachedObjects[$entityManager];
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
            'proxyDirectory' => \Yii::getPathOfAlias('KodeFoundry.cache.proxies'),
            'proxyNamespace' => 'KodeFoundryProxy',
            'connection' => 'default',
        );

        $options = array_merge($defaults, $this->entityManagers[$entityManager]);

        $config = new Configuration();
        $config->setMetadataCacheImpl(\Yii::app()->doctrineCache->getCache($options['metadataCache']));
        $config->setQueryCacheImpl(\Yii::app()->doctrineCache->getCache($options['queryCache']));
        $config->setAutoGenerateProxyClasses((bool) $options['autoGenerateProxyClasses']);
        $config->setProxyDir($options['proxyDirectory']);
        $config->setProxyNamespace($options['proxyNamespace']);

        if ($options['metadataDriver'] === null) {
            $driverImpl = $config->newDefaultAnnotationDriver($options['entityPaths']);
        } else {
            $driverImpl = $this->_createMetadataDriver($options['metadataDriver']);
        }

        $config->setMetadataDriverImpl($driverImpl);

        $conn = \Yii::app()->doctrineConnection->getConnection($options['connection']);
        $em = \Doctrine\ORM\EntityManager::create($conn, $config);

        $this->_cachedObjects['entityManager'][$entityManager] = $em;

        return $em;
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
}
