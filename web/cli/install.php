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

        $this->_logSuccess('Generando application.ini');
        $this->_ApplicationIni($namespace, $connectDB);

        $this->_logSuccess('Poblando MySQL');
        $this->_insertMysql();

        $this->_logSuccess('Ejecutando Generadores');
        $this->_RunGeneratos();

        $this->_logSuccess(
            'Creando archivos restantes para el Auth y los Roles'
        );
        $this->_generateAuthRoles($namespace);

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

    protected function _RunGeneratos()
    {

        $pathApplication = __DIR__ . '/../application';
        $pathGenerators = __DIR__ . '/../vendor/bin';

        exec('php ' . $pathGenerators . '/klear-models-mappers-generator.php -a ' . $pathApplication);
        exec('php ' . $pathGenerators . '/klear-db-generator.php -a ' . $pathApplication);
        exec('php ' . $pathGenerators . '/klear-yaml-generator.php -a ' . $pathApplication);

    }

    protected function _generateAuthRoles($namespace)
    {

        $libradyApp = __DIR__ . '/../library/' . $namespace;

        $this->_replaceData(
            __DIR__ . '/KlearSections.php',
            $libradyApp . '/Mapper/Sql/KlearSections.php',
            '{appnamespace}',
            $namespace
        );

        $authApp = __DIR__ . '/Auth';
        $authNewApp = __DIR__ . '/../library/' . $namespace . '/Auth';

        $this->_xcopy($authApp, $authNewApp);

        $klearConf = __DIR__ . '/../application/configs/klear/';

        $filesChangeAppnamespace = array(
            $authNewApp . '/Users.php',
            $authNewApp . '/Adapter.php',
            $authNewApp . '/Model/User.php',
            $klearConf . 'conf.d/mapperList.yaml',
            $klearConf . 'model/KlearRoles.yaml',
            $klearConf . 'model/KlearRolesSections.yaml',
            $klearConf . 'model/KlearSections.yaml',
            $klearConf . 'model/KlearUsers.yaml',
            $klearConf . 'model/KlearUsersRoles.yaml',
            $klearConf . 'klear.yaml'
        );

        foreach ($filesChangeAppnamespace as $file) {
            $this->_replaceData($file, $file, '{appnamespace}', $namespace);
        }

    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       string   $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
    protected function _xcopy($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->_xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        // Clean up
        $dir->close();
        return true;
    }

    protected function _replaceData($source, $dest, $shortCode, $data)
    {

        $infoReplace = file_get_contents($source);

        $infoReplace = str_replace($shortCode, $data, $infoReplace);

        file_put_contents($dest, $infoReplace);

    }

    protected function _messageInit()
    {

        $texts = array(
            '',
            ' ****************************************',
            '  Autogenerador para datos de inicio.',
            '  Antes de empezar es necesario tener los siguientes datos de MySQL:',
            '  * Usuario',
            '  * Contraseña',
            '  * Base de datos',
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
