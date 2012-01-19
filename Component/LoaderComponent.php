<?php

namespace KodeFoundry\Doctrine\Component;

include_once __DIR__ . '/config.php';

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

/**
 * LoaderComponent
 *
 * This component ensures all the doctrine library components are loaded correctly
 *
 * @author  Kevin Bradwick <kevin@kodefoundry.com>
 * @package KodeFoundry\YiiExtDoctrine
 * @license New BSD http://www.opensource.org/licenses/bsd-license.php
 * @link    https://github.com/kodefoundry/yii-ext-doctrine
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

        $classLoader = new ClassLoader('Symfony', SYMFONY_DIR);
        \Yii::registerAutoloader(array($classLoader, 'loadClass'));

        $classLoader = new ClassLoader('Doctrine\DBAL', DOCTRINE_DBAL_DIR);
        \Yii::registerAutoloader(array($classLoader, 'loadClass'));

        $classLoader = new ClassLoader('Doctrine\ORM', DOCTRINE_ORM_DIR);
        \Yii::registerAutoloader(array($classLoader, 'loadClass'));
    }
}
