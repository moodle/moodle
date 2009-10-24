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
 * @package    Zend_Application
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Application_Bootstrap_Bootstrap
 */
require_once 'Zend/Application/Bootstrap/Bootstrap.php';

/**
 * Base bootstrap class for modules
 * 
 * @uses       Zend_Loader_Autoloader_Resource
 * @uses       Zend_Application_Bootstrap_Bootstrap
 * @package    Zend_Application
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Application_Module_Bootstrap 
    extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @var Zend_Loader_Autoloader_Resource
     */
    protected $_resourceLoader;

    /**
     * Constructor
     * 
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application 
     * @return void
     */
    public function __construct($application)
    {
        $this->setApplication($application);

        // Use same plugin loader as parent bootstrap
        if ($application instanceof Zend_Application_Bootstrap_ResourceBootstrapper) {
            $this->setPluginLoader($application->getPluginLoader());
        }

        $key = strtolower($this->getModuleName());
        if ($application->hasOption($key)) {
            // Don't run via setOptions() to prevent duplicate initialization
            $this->setOptions($application->getOption($key));
        }

        if ($application->hasOption('resourceloader')) {
            $this->setOptions(array(
                'resourceloader' => $application->getOption('resourceloader')
            ));
        }
        $this->initResourceLoader();

        // ZF-6545: ensure front controller resource is loaded
        if (!$this->hasPluginResource('FrontController')) {
            $this->registerPluginResource('FrontController');
        }

        // ZF-6545: prevent recursive registration of modules
        if ($this->hasPluginResource('modules')) {
            $this->unregisterPluginResource('modules');
        }
    }

    /**
     * Set module resource loader
     * 
     * @param  Zend_Loader_Autoloader_Resource $loader 
     * @return Zend_Application_Module_Bootstrap
     */
    public function setResourceLoader(Zend_Loader_Autoloader_Resource $loader)
    {
        $this->_resourceLoader = $loader;
        return $this;
    }

    /**
     * Retrieve module resource loader
     * 
     * @return Zend_Loader_Autoloader_Resource
     */
    public function getResourceLoader()
    {
        if (null === $this->_resourceLoader) {
            $r    = new ReflectionClass($this);
            $path = $r->getFileName();
            $this->setResourceLoader(new Zend_Application_Module_Autoloader(array(
                'namespace' => $this->getModuleName(),
                'basePath'  => dirname($path),
            )));
        }
        return $this->_resourceLoader;
    }

    /**
     * Ensure resource loader is loaded
     * 
     * @return void
     */
    public function initResourceLoader()
    {
        $this->getResourceLoader();
    }

    /**
     * Retrieve module name
     * 
     * @return string
     */
    public function getModuleName()
    {
        if (empty($this->_moduleName)) {
            $class = get_class($this);
            if (preg_match('/^([a-z][a-z0-9]*)_/i', $class, $matches)) {
                $prefix = $matches[1];
            } else {
                $prefix = $class;
            }
            $this->_moduleName = $prefix;
        }
        return $this->_moduleName;
    }
}
