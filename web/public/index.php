<?php

/**
 * Define path to application directory
 */
$applicationPath = realpath(dirname(__FILE__) . '/../application');
defined('APPLICATION_PATH') || define('APPLICATION_PATH', $applicationPath);

/**
 * Define application environment
 */
$env = (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', $env);

/**
 * Composer autoloader
 */
$autoLoad = APPLICATION_PATH . '/../library/vendor/autoload.php';
if (file_exists(realpath($autoLoad))) {
    require_once realpath($autoLoad);
}

/**
 * Ensure library/ is on include_path
 */
$library = realpath(APPLICATION_PATH . '/../library');
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            $library,
            get_include_path()
        )
    )
);

/** Zend_Application */
require_once 'Zend/Application.php';

/**
 * Create application, bootstrap, and run
 */
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()->run();