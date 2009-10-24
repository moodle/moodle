<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Tool_Project_Context_Filesystem_File
 */
require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

/**
 * @see Zend_CodeGenerator_Php_File
 */
require_once 'Zend/CodeGenerator/Php/File.php';

/**
 * @see Zend_Filter_Word_DashToCamelCase
 */
require_once 'Zend/Filter/Word/DashToCamelCase.php';

/**
 * This class is the front most class for utilizing Zend_Tool_Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 * 
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Context_Zf_ControllerFile extends Zend_Tool_Project_Context_Filesystem_File 
{
    
    /**
     * @var string
     */
    protected $_controllerName = 'index';
    
    /**
     * @var string
     */
    protected $_filesystemName = 'controllerName';
    
    /**
     * init()
     *
     * @return Zend_Tool_Project_Context_Zf_ControllerFile
     */
    public function init()
    {
        $this->_controllerName = $this->_resource->getAttribute('controllerName');
        $this->_filesystemName = ucfirst($this->_controllerName) . 'Controller.php';
        parent::init();
        return $this;
    }
    
    /**
     * getPersistentAttributes
     *
     * @return array
     */
    public function getPersistentAttributes()
    {
        return array(
            'controllerName' => $this->getControllerName()
            );
    }
    
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'ControllerFile';
    }
    
    /**
     * getControllerName()
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controllerName;
    }
  
    /**
     * getContents()
     *
     * @return string
     */
    public function getContents()
    {

        $filter = new Zend_Filter_Word_DashToCamelCase();
        
        $className = $filter->filter($this->_controllerName) . 'Controller';
        
        $codeGenFile = new Zend_CodeGenerator_Php_File(array(
            'fileName' => $this->getPath(),
            'classes' => array(
                new Zend_CodeGenerator_Php_Class(array(
                    'name' => $className,
                    'extendedClass' => 'Zend_Controller_Action',
                    'methods' => array(
                        new Zend_CodeGenerator_Php_Method(array(
                            'name' => 'init',
                            'body' => '/* Initialize action controller here */',
                        ))
                    )
                ))
            )
        ));
        

        if ($className == 'ErrorController') {
            
            $codeGenFile = new Zend_CodeGenerator_Php_File(array(
                'fileName' => $this->getPath(),
                'classes' => array(
                    new Zend_CodeGenerator_Php_Class(array(
                        'name' => $className,
                        'extendedClass' => 'Zend_Controller_Action',
                        'methods' => array(
                            new Zend_CodeGenerator_Php_Method(array(
                                'name' => 'errorAction',
                                'body' => <<<EOS
\$errors = \$this->_getParam('error_handler');

switch (\$errors->type) { 
    case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
    case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

        // 404 error -- controller or action not found
        \$this->getResponse()->setHttpResponseCode(404);
        \$this->view->message = 'Page not found';
        break;
    default:
        // application error 
        \$this->getResponse()->setHttpResponseCode(500);
        \$this->view->message = 'Application error';
        break;
}

\$this->view->exception = \$errors->exception;
\$this->view->request   = \$errors->request;
EOS
                                ))
                            )
                        ))
                    )
                ));

        }
        
        // store the generator into the registry so that the addAction command can use the same object later
        Zend_CodeGenerator_Php_File::registerFileCodeGenerator($codeGenFile); // REQUIRES filename to be set
        return $codeGenFile->generate();
    }
    
    /**
     * addAction()
     *
     * @param string $actionName
     */
    public function addAction($actionName)
    {
        $class = $this->getCodeGenerator();
        $class->setMethod(array('name' => $actionName . 'Action', 'body' => '        // action body here'));
        file_put_contents($this->getPath(), $codeGenFile->generate());
    }
    
    /**
     * getCodeGenerator()
     *
     * @return Zend_CodeGenerator_Php_Class
     */
    public function getCodeGenerator()
    {
        $codeGenFile = Zend_CodeGenerator_Php_File::fromReflectedFileName($this->getPath());
        $codeGenFileClasses = $codeGenFile->getClasses();
        $class = array_shift($codeGenFileClasses);
        return $class;
    }
    
}
