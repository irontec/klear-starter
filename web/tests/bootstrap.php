<?php

$baseDir = realpath(__DIR__ . '/../');

/**
 * Define path to application directory
 */
$applicationPath = $baseDir . '/application';
defined('APPLICATION_PATH') || define('APPLICATION_PATH', $applicationPath);

/**
 * Define application environment
 */
$env = (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', $env);

/**
 * Composer autoloader
 */
$vendorDir = $baseDir . '/vendor/';
if (file_exists($vendorDir)) {
    require_once $vendorDir . 'autoload.php';
}

/**
 * Ensure library/ is on include_path
 */
$library = $baseDir . '/library';
$zendAutoload = $vendorDir . 'zendframework/zendframework1/library';
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            $library,
            $zendAutoload
        )
    )
);

/** Zend Autoloader */
require_once 'Zend/Loader/Autoloader.php';
\Zend_Loader_Autoloader::getInstance();