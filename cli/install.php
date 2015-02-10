<?php

$namespace = readline("Introduce namespace [app] \n");
$dbname    = readline("Introduce dbname [db] \n");
$username  = readline("Introduce username [root] \n");
$password  = readline("Introduce password [12345] \n");
$host      = readline("Introduce host [localhost] \n");

if (empty($namespace)) {
    $namespace = 'app';
}

if (empty($dbname)) {
    $dbname = 'db';
}

if (empty($username)) {
    $username = 'root';
}

if (empty($password)) {
    $password = '12345';
}

if (empty($host)) {
    $host = 'localhost';
}

$application = file_get_contents('../application/config/application.ini');

$application = str_replace('{appnamespace}', $namespace, $application);
$application = str_replace('{dbname}', $dbname, $application);
$application = str_replace('{username}', $username, $application);
$application = str_replace('{password}', $password, $application);
$application = str_replace('{host}', $host, $application);

file_put_contents('../application/config/application.ini', $application);
