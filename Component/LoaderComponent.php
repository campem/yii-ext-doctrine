<?php

namespace KodeFoundry\Doctrine\Component;

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
 *
 * @author  Kevin Bradwick <kbradwick@gmail.com>
 * @package
 * @license
 */
class LoaderComponent extends \CApplicationComponent
{
    /**
     * Initialise
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
}
