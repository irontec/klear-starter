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

/** Zend_Application */
require_once 'Zend/Application.php';

/**
 * Create application, bootstrap, and run
 */
$application = new \Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()->run();
