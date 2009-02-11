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
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Server/Interface.php';
require_once 'Zend/Soap/Wsdl.php';
require_once 'Zend/Server/Reflection.php';
require_once 'Zend/Server/Exception.php';
require_once 'Zend/Server/Abstract.php';
require_once 'Zend/Uri.php';

/**
 * Zend_Soap_AutoDiscover
 *
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_AutoDiscover implements Zend_Server_Interface {
    /**
     * @var Zend_Soap_Wsdl
     */
    protected $_wsdl = null;

    /**
     * @var Zend_Server_Reflection
     */
    protected $_reflection = null;

    /**
     * @var array
     */
    protected $_functions = array();

    /**
     * @var boolean
     */
    protected $_strategy;

    /**
     * Url where the WSDL file will be available at.
     *
     * @var WSDL Uri
     */
    protected $_uri;

    /**
     * Constructor
     *
     * @param boolean|string|Zend_Soap_Wsdl_Strategy_Interface $strategy
     * @param string|Zend_Uri $uri
     */
    public function __construct($strategy = true, $uri=null)
    {
        $this->_reflection = new Zend_Server_Reflection();
        $this->setComplexTypeStrategy($strategy);

        if($uri !== null) {
            $this->setUri($uri);
        }
    }

    /**
     * Set the location at which the WSDL file will be availabe.
     *
     * @see Zend_Soap_Exception
     * @throws Zend_Soap_AutoDiscover_Exception
     * @param  Zend_Uri|string $uri
     * @return Zend_Soap_AutoDiscover
     */
    public function setUri($uri)
    {
        if(is_string($uri)) {
            $uri = Zend_Uri::factory($uri);
        } else if(!($uri instanceof Zend_Uri)) {
            require_once "Zend/Soap/AutoDiscover/Exception.php";
            throw new Zend_Soap_AutoDiscover_Exception("No uri given to Zend_Soap_AutoDiscover::setUri as string or Zend_Uri instance.");
        }
        $this->_uri = $uri;

        // change uri in WSDL file also if existant
        if($this->_wsdl instanceof Zend_Soap_Wsdl) {
            $this->_wsdl->setUri($uri);
        }

        return $this;
    }

    /**
     * Return the current Uri that the SOAP WSDL Service will be located at.
     *
     * @return Zend_Uri
     */
    public function getUri()
    {
        if($this->_uri instanceof Zend_Uri) {
            $uri = $this->_uri;
        } else {
            $schema     = $this->getSchema();
            $host       = $this->getHostName();
            $scriptName = $this->getRequestUriWithoutParameters();
            $uri = Zend_Uri::factory($schema . '://' . $host . $scriptName);
            $this->setUri($uri);
        }
        return $uri;
    }

    /**
     * Detect and returns the current HTTP/HTTPS Schema
     *
     * @return string
     */
    protected function getSchema()
    {
        $schema = "http";
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $schema = 'https';
        }
        return $schema;
    }

    /**
     * Detect and return the current hostname
     *
     * @return string
     */
    protected function getHostName()
    {
        if(isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = $_SERVER['SERVER_NAME'];
        }
        return $host;
    }

    /**
     * Detect and return the current script name without parameters
     *
     * @return string
     */
    protected function getRequestUriWithoutParameters()
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
        } else {
            $requestUri = $_SERVER['SCRIPT_NAME'];
        }
        if( ($pos = strpos($requestUri, "?")) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        return $requestUri;
    }

    /**
     * Set the strategy that handles functions and classes that are added AFTER this call.
     *
     * @param  boolean|string|Zend_Soap_Wsdl_Strategy_Interface $strategy
     * @return Zend_Soap_AutoDiscover
     */
    public function setComplexTypeStrategy($strategy)
    {
        $this->_strategy = $strategy;
        if($this->_wsdl instanceof  Zend_Soap_Wsdl) {
            $this->_wsdl->setComplexTypeStrategy($strategy);
        }

        return $this;
    }

    /**
     * Set the Class the SOAP server will use
     *
     * @param string $class Class Name
     * @param string $namespace Class Namspace - Not Used
     * @param array $argv Arguments to instantiate the class - Not Used
     */
    public function setClass($class, $namespace = '', $argv = null)
    {
        $uri = $this->getUri();
        $wsdl = new Zend_Soap_Wsdl($class, $uri, $this->_strategy);
        $port = $wsdl->addPortType($class . 'Port');
        $binding = $wsdl->addBinding($class . 'Binding', 'tns:' .$class. 'Port');

        $wsdl->addSoapBinding($binding, 'rpc');
        $wsdl->addService($class . 'Service', $class . 'Port', 'tns:' . $class . 'Binding', $uri);
        
        foreach ($this->_reflection->reflectClass($class)->getMethods() as $method) {
            /* <wsdl:portType>'s */
            $portOperation = $wsdl->addPortOperation($port, $method->getName(), 'tns:' .$method->getName(). 'Request', 'tns:' .$method->getName(). 'Response');
            $desc = $method->getDescription();
            if (strlen($desc) > 0) {
                /** @todo check, what should be done for portoperation documentation */
                //$wsdl->addDocumentation($portOperation, $desc);
            }
            /* </wsdl:portType>'s */

            $this->_functions[] = $method->getName();

            $selectedPrototype = null;
            $maxNumArgumentsOfPrototype = -1;
            foreach ($method->getPrototypes() as $prototype) {
                $numParams = count($prototype->getParameters());
                if($numParams > $maxNumArgumentsOfPrototype) {
                    $maxNumArgumentsOfPrototype = $numParams;
                    $selectedPrototype = $prototype;
                }
            }
            
            if($selectedPrototype != null) {
                $prototype = $selectedPrototype;
                $args = array();
                foreach($prototype->getParameters() as $param) {
                    $args[$param->getName()] = $wsdl->getType($param->getType());
                }
                $message = $wsdl->addMessage($method->getName() . 'Request', $args);
                if (strlen($desc) > 0) {
                    //$wsdl->addDocumentation($message, $desc);
                }
                if ($prototype->getReturnType() != "void") {
                    $message = $wsdl->addMessage($method->getName() . 'Response', array($method->getName() . 'Return' => $wsdl->getType($prototype->getReturnType())));
                }

                /* <wsdl:binding>'s */
                $operation = $wsdl->addBindingOperation($binding, $method->getName(),  array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"), array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"));
                $wsdl->addSoapOperation($operation, $uri->getUri() . '#' .$method->getName());
                /* </wsdl:binding>'s */
            }
        }
        $this->_wsdl = $wsdl;
    }

    /**
     * Add a Single or Multiple Functions to the WSDL
     *
     * @param string $function Function Name
     * @param string $namespace Function namespace - Not Used
     */
    public function addFunction($function, $namespace = '')
    {
        static $port;
        static $operation;
        static $binding;

        if (!is_array($function)) {
            $function = (array) $function;
        }

        $uri = $this->getUri();

        if (!($this->_wsdl instanceof Zend_Soap_Wsdl)) {
            $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
            $name = $parts[0];
            $wsdl = new Zend_Soap_Wsdl($name, $uri, $this->_strategy);

            $port = $wsdl->addPortType($name . 'Port');
            $binding = $wsdl->addBinding($name . 'Binding', 'tns:' .$name. 'Port');

            $wsdl->addSoapBinding($binding, 'rpc');
            $wsdl->addService($name . 'Service', $name . 'Port', 'tns:' . $name . 'Binding', $uri);
        } else {
            $wsdl = $this->_wsdl;
        }

        foreach ($function as $func) {
            $method = $this->_reflection->reflectFunction($func);
            foreach ($method->getPrototypes() as $prototype) {
                $args = array();
                foreach ($prototype->getParameters() as $param) {
                    $args[$param->getName()] = $wsdl->getType($param->getType());
                }
                $message = $wsdl->addMessage($method->getName() . 'Request', $args);
                $desc = $method->getDescription();
                if (strlen($desc) > 0) {
                    //$wsdl->addDocumentation($message, $desc);
                }
                if ($prototype->getReturnType() != "void") {
                    $message = $wsdl->addMessage($method->getName() . 'Response', array($method->getName() . 'Return' => $wsdl->getType($prototype->getReturnType())));
                }
                 /* <wsdl:portType>'s */
                   $portOperation = $wsdl->addPortOperation($port, $method->getName(), 'tns:' .$method->getName(). 'Request', 'tns:' .$method->getName(). 'Response');
                if (strlen($desc) > 0) {
                    //$wsdl->addDocumentation($portOperation, $desc);
                }
                   /* </wsdl:portType>'s */

                /* <wsdl:binding>'s */
                $operation = $wsdl->addBindingOperation($binding, $method->getName(),  array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"), array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"));
                $wsdl->addSoapOperation($operation, $uri->getUri() . '#' .$method->getName());
                /* </wsdl:binding>'s */

                $this->_functions[] = $method->getName();

                // We will only add one prototype
                break;
            }
        }
        $this->_wsdl = $wsdl;
    }

    /**
     * Action to take when an error occurs
     *
     * @param string $fault
     * @param string|int $code
     */
    public function fault($fault = null, $code = null)
    {
        require_once "Zend/Soap/AutoDiscover/Exception.php";
        throw new Zend_Soap_AutoDiscover_Exception("Function has no use in AutoDiscover.");
    }

    /**
     * Handle the Request
     *
     * @param string $request A non-standard request - Not Used
     */
    public function handle($request = false)
    {
        if (!headers_sent()) {
            header('Content-Type: text/xml');
        }
        $this->_wsdl->dump();
    }

    /**
     * Return an array of functions in the WSDL
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->_functions;
    }

    /**
     * Load Functions
     *
     * @param unknown_type $definition
     */
    public function loadFunctions($definition)
    {
        require_once "Zend/Soap/AutoDiscover/Exception.php";
        throw new Zend_Soap_AutoDiscover_Exception("Function has no use in AutoDiscover.");
    }

    /**
     * Set Persistance
     *
     * @param int $mode
     */
    public function setPersistence($mode)
    {
        require_once "Zend/Soap/AutoDiscover/Exception.php";
        throw new Zend_Soap_AutoDiscover_Exception("Function has no use in AutoDiscover.");
    }

    /**
     * Returns an XSD Type for the given PHP type
     *
     * @param string $type PHP Type to get the XSD type for
     * @return string
     */
    public function getType($type)
    {
        if (!($this->_wsdl instanceof Zend_Soap_Wsdl)) {
            /** @todo Exception throwing may be more correct */

            // WSDL is not defined yet, so we can't recognize type in context of current service
            return '';
        } else {
            return $this->_wsdl->getType($type);
        }
    }
}

