#!/usr/bin/php
<?php

defined('__DIR__') || define('__DIR__', dirname(__FILE__));

define('APPLICATION_PATH', realpath(__DIR__ . '/../../application'));

require_once 'Zend/Loader/Autoloader.php';

/**
 * Composer autoloader
 */
$autoLoad = APPLICATION_PATH . '/../library/vendor/autoload.php';
if (file_exists(realpath($autoLoad))) {
    require_once realpath($autoLoad);
}

$loader = Zend_Loader_Autoloader::getInstance();

$formatOf = "module/controller/action/param1/param2/param3/..";
$opt = array(
    'action|a=s' => 'action to perform in format of "' . $formatOf . '"',
    'env|e-s'    => 'defines application environment (defaults "production")',
    'help|h'     => 'displays usage information',
);

$getopt = new \Zend_Console_Getopt($opt);

try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    /**
     * Bad options passed: report usage
     */
    echo $e->getUsageMessage();
    return false;
}

/**
 * Show help message in case it was requested or params
 * were incorrect (module, controller and action)
 */
if ($getopt->getOption('h') || !$getopt->getOption('a')) {
    echo $getopt->getUsageMessage();
    return true;
}

/**
 * Initialize values based on presence or absence of CLI options
 */
$env = $getopt->getOption('e');
if (!$env) {
    $env = getenv('APPLICATION_ENV');
    if (!$env) {
        $env = 'production';
    }
}

defined('APPLICATION_ENV') || define('APPLICATION_ENV', $env);

/**
 * initialize Zend_Application
 */
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$loader->registerNamespace('Iron_');

/**
 * Bootstrap and retrive the frontController resource
 */
$front = $application->getBootstrap()
      ->bootstrap('frontController')
      ->getResource('frontController');

$params = array_reverse(explode('/', $getopt->getOption('a')));

$module = array_pop($params);
$controller = array_pop($params);
$action = array_pop($params);

$reqParams = array();

if (sizeof($params) % 2 == 0) {
    do {
        $key = array_pop($params);
        $value = array_pop($params);
        $reqParams[$key] = $value;
    } while (sizeof($params) > 1);
}

$request = new Zend_Controller_Request_Simple(
    $action,
    $controller,
    $module,
    $reqParams
);

/**
 * set front controller options to make everything operational from CLI
 */
$front->setRequest($request)
   ->setResponse(new Zend_Controller_Response_Cli())
   ->setRouter(new Iron_Controller_Router_Cli())
   ->throwExceptions(true);

/**
 * lets bootstrap our application and enjoy!
 */
$application->bootstrap()->run();