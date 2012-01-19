<?php

define('KODEFOUNDRY_VENDOR_DIR', realpath(__DIR__ . '/../vendor'), true);
define('DOCTRINE_COMMON_DIR', KODEFOUNDRY_VENDOR_DIR . '/doctrine2/lib/vendor/doctrine-common/lib', true);
define('DOCTRINE_DBAL_DIR', KODEFOUNDRY_VENDOR_DIR . '/doctrine2/lib/vendor/doctrine-dbal/lib', true);
define('DOCTRINE_ORM_DIR', KODEFOUNDRY_VENDOR_DIR . '/doctrine2/lib', true);
define('SYMFONY_DIR', KODEFOUNDRY_VENDOR_DIR . '/doctrine2/lib/vendor', true);
define('KODEFOUNDRY_CACHE_DIR', realpath(__DIR__ . '/../../cache'), true);

// load the doctrine class loader
include_once DOCTRINE_COMMON_DIR . '/Doctrine/Common/Classloader.php';