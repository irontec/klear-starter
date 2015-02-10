<?php

$namespace = readline("Introduce namespace [app]: \n");
$dbname    = readline("Introduce dbname [db]: \n");
$username  = readline("Introduce username [root]: \n");
$password  = readline("Introduce password [12345]: \n");
$host      = readline("Introduce host [localhost]: \n");

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

$application = file_get_contents('./application/configs/application.ini');

$application = str_replace('{appnamespace}', $namespace, $application);
$application = str_replace('{dbname}', $dbname, $application);
$application = str_replace('{username}', $username, $application);
$application = str_replace('{password}', $password, $application);
$application = str_replace('{host}', $host, $application);

file_put_contents('./application/configs/application.ini', $application);

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    return false;
}

$klearUsers = "
CREATE TABLE `KlearUsers` (
  `userId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(40) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(80) NOT NULL COMMENT '[password]',
  `active` tinyint(1) DEFAULT '1',
  `createdOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='[entity]';";


$userAdmin = "insert into KlearUsers (login, pass, active) values ('admin','$2a$08$0hHHBX8So9JhU0a0SNnRCeAZcMEdAfn7T/pl/u/pESzBwztldhRnO', 1);";

if ($mysqli->query($klearUsers)) {
    $mysqli->query($userAdmin);
} else {
    echo "Fallo la creación de KlearUsers \n";
}




