<?php

class install
{

    protected $_color;
    protected $_mysqli;

    public function __construct()
    {

        require __DIR__ . '/Colors.php';
        $this->_color = new Colors();
        $this->_messageInit();
    }


    public function start()
    {

        $namespace = $this->_getNamespace();
        $connectDB = $this->_getConnectDB();
        $isRestApp = false;//$this->_getIsRestApp();

        $this->_logSuccess('Generando application.ini');
        $this->_ApplicationIni($namespace, $connectDB);

        $this->_logSuccess('Poblando MySQL');
        $this->_insertMysql();

        $this->_logSuccess('Ejecutando Generadores');
        $this->_RunGeneratos($isRestApp);

        $this->_messageEnd();

    }

    protected function _getNamespace()
    {

        $text = " * Escribe un namespace sin espacios: ";
        $command = readline($text);

        while ($command == '') {
            if ($command == '') {
                $command = readline($text);
                $command = str_replace(' ', '', $command);
            }
        }

        $this->_logSuccess('namepsace: ' . ucfirst($command));

        return ucfirst($command);

    }

    protected function _logSuccess($msg)
    {

        echo $this->_color->getColoredString(
            ' >> ' . $msg,
            'light_green',
            null
        ) . "\n";

    }

    protected function _logFail($msg)
    {

        echo $this->_color->getColoredString(
            ' [error] ' . $msg,
            'red',
            null
        ) . "\n";

    }

    protected function _getConnectDB()
    {

        $data = array();
        $data['user'] = readline("Introduce username [root]: \n");
        $data['pass'] = readline("Introduce password [12345]: \n");
        $data['host'] = readline("Introduce host [localhost]: \n");
        $data['dbname'] = readline("Introduce dbname [db]: \n");

        $this->_mysqli = new mysqli(
            ($data['host'] == '' ? 'localhost' : $data['host']),
            ($data['user'] == '' ? 'root' : $data['user']),
            ($data['pass'] == '' ? '12345' : $data['pass']),
            ($data['dbname'] == '' ? 'db' : $data['dbname'])
        );

        while ($this->_mysqli->connect_errno) {
            $this->_logFail('Connect failed: ' . $this->_mysqli->connect_error);

            if ($this->_mysqli->connect_errno) {

                $data = array();
                $data['user'] = readline("Introduce username [root]: \n");
                $data['pass'] = readline("Introduce password [12345]: \n");
                $data['host'] = readline("Introduce host [localhost]: \n");
                $data['dbname'] = readline("Introduce dbname [db]: \n");

                $this->_mysqli = new mysqli(
                    ($data['host'] == '' ? 'localhost' : $data['host']),
                    ($data['user'] == '' ? 'root' : $data['user']),
                    ($data['pass'] == '' ? '12345' : $data['pass']),
                    ($data['dbname'] == '' ? 'db' : $data['dbname'])
                );

            }
        }

        $this->_logSuccess('Connect MySQL Success');

        return $data;

    }

    protected function _getIsRestApp()
    {

        $isRest = readline('Es una api rest (y/n): ');

        while ($isRest == '' || !in_array($isRest, array('y', 'n'))) {
            if (!in_array($isRest, array('y', 'n'))) {
                $isRest = readline("Es una api rest (y/n): ");
            }
        }

        return ($isRest == 'y' ? true : false);

    }

    protected function _ApplicationIni($namespace, $connectDB)
    {

        $dbname = ($connectDB['dbname'] == '' ? 'db' : $connectDB['dbname']);
        $username = ($connectDB['user'] == '' ? 'root' : $connectDB['user']);
        $password = ($connectDB['pass'] == '' ? '12345' : $connectDB['pass']);
        $host = ($connectDB['host'] == '' ? 'localhost' : $connectDB['host']);


        $pathAppication = __DIR__ . '/../application/configs/application.ini';

        $application = file_get_contents($pathAppication);

        $application = str_replace('{appnamespace}', $namespace, $application);
        $application = str_replace('{dbname}', $dbname, $application);
        $application = str_replace('{username}', $username, $application);
        $application = str_replace('{password}', $password, $application);
        $application = str_replace('{host}', $host, $application);

        file_put_contents($pathAppication, $application);

    }

    protected function _insertMysql()
    {

        $templine = '';
        $lines = file(__DIR__ . '/../../phing/deltas/001-init.sql');
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                $this->_mysqli->query($templine);
                $templine = '';
            }
        }

        $templine = '';
        $lines = file(__DIR__ . '/../../phing/dumps/changelog.sql');
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                $this->_mysqli->query($templine);
                $templine = '';
            }
        }

        $this->_logSuccess('Tablas importadas correctamente.');

    }

    protected function _RunGeneratos($isRestApp)
    {

        $pathApplication = __DIR__ . '/../application';
        $pathGenerators = __DIR__ . '/../vendor/irontec/Generator';

        exec('php ' . $pathGenerators . '/klear-models-mappers-generator.php -a ' . $pathApplication);
        exec('php ' . $pathGenerators . '/klear-db-generator.php -a ' . $pathApplication);
        exec('php ' . $pathGenerators . '/klear-yaml-generator.php -a ' . $pathApplication);

        if ($isRestApp) {
            exec('php ' . $pathGenerators . '/klear-rest-generator.php -a ' . $pathApplication);
        }

    }

    protected function _messageInit()
    {

        $texts = array(
            '',
            ' ****************************************',
            '  Autogenerador para datos de inicio.',
            '  Antes de empezar es necesario tener:',
            '  * Usuario, contraseña de MySQL junto a una Base de datos creada',
            ''
        );

        foreach ($texts as $text) {
            echo $this->_color->getColoredString($text, 'cyan', null) . "\n";
        }

        $continue = readline("Continuar (y/n): ");

        while ($continue == '' || !in_array($continue, array('y', 'n'))) {
            if (!in_array($continue, array('y', 'n'))) {
                $continue = readline("Continuar (y/n): ");
            }
        }

        if ($continue == 'n') {
            die();
        }

        echo "\n";

    }

    protected function _messageEnd()
    {

        $texts = array(
            '',
            '  Todo a terminado.',
            '  Ahora a jugar con Zend y Klear',
            ' ****************************************',
            ''
        );

        foreach ($texts as $text) {
            echo $this->_color->getColoredString($text, 'cyan', null) . "\n";
        }

    }

}

$install = new install();
$install->start();
