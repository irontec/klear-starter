<?php

class ConsoleController extends Zend_Controller_Action
{

    /**
     * Hace una comprobación de que se esta ejecutando desde consola
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (PHP_SAPI !== 'cli') {
            die();
        }

    }

    public function indexAction()
    {
        die('Run task index');
    }

}