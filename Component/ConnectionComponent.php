<?php

namespace KodeFoundry\Doctrine\Component;

/**
 *
 * @author  Kevin Bradwick <kbradwick@gmail.com>
 * @package KodeFoundry\Doctrine
 * @license New BSD http://www.opensource.org/licenses/bsd-license.php
 */
class ConnectionComponent extends \CApplicationComponent
{
    /**
     * @var string
     */
    protected $default = 'default';

    /**
     * @var array
     */
    protected $connections = array();

    /**
     * @var array
     */
    private $_cachedObjects = array();

    /**
     * @param array $connections
     */
    public function setConnections(array $connections)
    {
        $this->connections = $connections;
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
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
     * Get a connection
     *
     * @param string $connection
     * @return \Doctrine\DBAL\Doctrine\DBAL\Connection
     * @throws \CException
     */
    public function getConnection($connection='default')
    {
        if (isset($this->_cachedObjects[$connection]) === true) {
            return $this->_cachedObjects[$connection];
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
            $cache = \Yii::app()->doctrineCache->getCache($params['resultCache']);
            unset($params['resultCache']);
            $config->setResultCacheImpl($cache);
        }

        $conn = \Doctrine\DBAL\DriverManager::getConnection($params, $config);
        $this->_cachedObjects[$connection] = $conn;

        return $conn;
    }
}
