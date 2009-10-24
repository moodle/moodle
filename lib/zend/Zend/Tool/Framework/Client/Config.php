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
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Framework_Client_Config
{
    
    protected $_configFilepath = null;
    
    /**
     * @var Zend_Config
     */
    protected $_config = null;
    
    public function __config($options = array())
    {
        if ($options) {
            $this->setOptions($options);
        }
    }
    
    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $setMethodName = 'set' . $optionName;
            if (method_exists($this, $setMethodName)) {
                $this->{$setMethodName}($optionValue);
            }
        }
    }
    
    public function setConfigFilepath($configFilepath)
    {
        if (!file_exists($configFilepath)) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Provided path to config ' . $configFilepath . ' does not exist');
        }

        $this->_configFilepath = $configFilepath;
        
        $suffix = substr($configFilepath, -4);
        
        switch ($suffix) {
            case '.ini':
                require_once 'Zend/Config/Ini.php';
                $this->_config = new Zend_Config_Ini($configFilepath);
                break;
            case '.xml':
                require_once 'Zend/Config/Xml.php';
                $this->_config = new Zend_Config_Xml($configFilepath);
                break;
            case '.php':
                require_once 'Zend/Config.php';
                $this->_config = new Zend_Config(include $configFilepath);
                break;
            default:
                require_once 'Zend/Tool/Framework/Client/Exception.php';
                throw new Zend_Tool_Framework_Client_Exception('Unknown config file type '
                    . $suffix . ' at location ' . $configFilepath
                    );
        }
        
        return $this;
    }
    
    public function getConfigFilepath()
    {
        return $this->_configFilepath;
    }
    
    public function get($name, $defaultValue)
    {
        return $this->_config->get($name, $defaultValue);
    }
    
    public function __get($name)
    {
        return $this->_config->{$name};
    }
    
}