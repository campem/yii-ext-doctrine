<?php

define('VENDOR_DIR', realpath(__DIR__ . '/../vendor'), true);
define('DOCTRINE_COMMON_DIR', VENDOR_DIR . '/doctrine2/lib/vendor/doctrine-common/lib', true);
define('DOCTRINE_DBAL_DIR', VENDOR_DIR . '/doctrine2/lib/vendor/doctrine-dbal/lib', true);
define('DOCTRINE_ORM_DIR', VENDOR_DIR . '/doctrine2/lib', true);
define('SYMFONY_DIR', VENDOR_DIR . '/doctrine2/lib/vendor', true);
define('CACHE_DIR', realpath(__DIR__ . '/../../cache'), true);

require_once DOCTRINE_COMMON_DIR . '/Doctrine/Common/Classloader.php';