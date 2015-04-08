<?php

/**
 * Application Model Mapper
 *
 * @package Mapper
 * @subpackage Sql
 * @author
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Data Mapper implementation for {appnamespace}\Model\KlearSections
 *
 * @package Mapper
 * @subpackage Sql
 * @author
 */
namespace {appnamespace}\Mapper\Sql;
class KlearSections extends Raw\KlearSections
{

    protected function _checkMenu()
    {

        $configPath = APPLICATION_PATH . '/configs/klear/klear.yaml';
        $config = new \Zend_Config_Yaml(
            $configPath,
            APPLICATION_ENV,
            array(
                "yamldecoder" => "yaml_parse"
            )
        );

        $klearConfig = new \Klear_Model_MainConfig();
        $klearConfig->setConfig($config);

        foreach ($klearConfig->getMenu() as $menu) {

            foreach ($menu as $submenu) {

                $file = $submenu->getMainFile();

                if (!$this->findByField('identifier', $file)) {

                    $entry = new \{appnamespace}\Model\KlearSections();
                    $entry->setIdentifier($file);
                    $entry->setName($file);
                    $entry->setDescription($file);

                    try {

                        $this->save($entry);

                    } catch (\Exception $e) {

                    }
                }
            }
        }
    }

    public function fetchList(
        $where = null,
        $order = null,
        $count = null,
        $offset = null
    )
    {

        $this->_checkMenu();

        return parent::fetchList(
            $where,
            $order,
            $count,
            $offset
        );

    }

}