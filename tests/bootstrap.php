<?php

include_once realpath(__DIR__ . '/../vendor/doctrine2/lib/vendor/doctrine-common/lib/Doctrine/Common/') . '/ClassLoader.php';

use Doctrine\Common\ClassLoader;

$classLoader = new ClassLoader('Doctrine\Common',realpath(__DIR__ . '/../vendor/doctrine2/lib/vendor/doctrine-common/lib'));
$classLoader->register();

$classLoader = new ClassLoader('Doctrine\DBAL', realpath(__DIR__ . '/../vendor/doctrine2/lib/vendor/doctrine-dbal/lib'));
$classLoader->register();

$classLoader = new ClassLoader('Doctrine\ORM', realpath(__DIR__ . '/../vendor/doctrine2/lib/'));
$classLoader->register();

$classLoader = new ClassLoader('Symfony', realpath(__DIR__ . '/../vendor/doctrine2/lib/vendor/'));
$classLoader->register();

$classLoader = new ClassLoader('KodeFoundry\Doctrine', realpath(__DIR__ . '/../../../'));
$classLoader->register();

// YiiBase.php
$yiiBase = realpath(__DIR__ . '/../../../../../../yii/framework') . '/YiiBase.php';
require_once $yiiBase;