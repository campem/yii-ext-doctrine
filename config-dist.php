<?php

define('KODEFOUNDRY_VENDOR_DIR', realpath(__DIR__ . '/../../vendor'), true);
define('DOCTRINE_COMMON_DIR', KODEFOUNDRY_VENDOR_DIR, true);
define('DOCTRINE_DBAL_DIR', KODEFOUNDRY_VENDOR_DIR, true);
define('DOCTRINE_ORM_DIR', KODEFOUNDRY_VENDOR_DIR, true);
define('SYMFONY_DIR', KODEFOUNDRY_VENDOR_DIR, true);
define('KODEFOUNDRY_CACHE_DIR', realpath(__DIR__ . '/../../cache'), true);

// load the doctrine class loader
require_once DOCTRINE_COMMON_DIR . '/Doctrine/Common/Classloader.php';