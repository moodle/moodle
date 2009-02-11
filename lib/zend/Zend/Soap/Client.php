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
 */

/** Zend_Soap_Client_Exception */
require_once 'Zend/Soap/Client/Exception.php';

/** Zend_Soap_Server */
require_once 'Zend/Soap/Server.php';

/** Zend_Soap_Client_Local */
require_once 'Zend/Soap/Client/Local.php';

/** Zend_Soap_Client_Common */
require_once 'Zend/Soap/Client/Common.php';


/**
 * Zend_Soap_Client
 *
 * @category   Zend
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Soap_Client
{
    /**
     * Encoding
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Array of SOAP type => PHP class pairings for handling return/incoming values
     * @var array
     */
    protected $_classmap = null;

    /**
     * Registered fault exceptions
     * @var array
     */
    protected $_faultExceptions = array();

    /**
     * SOAP version to use; SOAP_1_2 by default, to allow processing of headers
     * @var int
     */
    protected $_soapVersion = SOAP_1_2;

    /** Set of other SoapClient options */
    protected $_uri                 = null;
    protected $_location            = null;
    protected $_style               = null;
    protected $_use                 = null;
    protected $_login               = null;
    protected $_password            = null;
    protected $_proxy_host          = null;
    protected $_proxy_port          = null;
    protected $_proxy_login         = null;
    protected $_proxy_password      = null;
    protected $_local_cert          = null;
    protected $_passphrase          = null;
    protected $_compression         = null;
    protected $_connection_timeout  = null;

    /**
     * WSDL used to access server
     * It also defines Zend_Soap_Client working mode (WSDL vs non-WSDL)
     *
     * @var string
     */
    protected $_wsdl = null;

    /**
     * SoapClient object
     *
     * @var SoapClient
     */
    protected $_soapClient;

    /**
     * Last invoked method
     *
     * @var string
     */
    protected $_lastMethod = '';

    /**
     * Constructor
     *
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($wsdl = null, $options = null)
    {
        if (!extension_loaded('soap')) {
            throw new Zend_Soap_Client_Exception('SOAP extension is not loaded.');
        }

        if ($wsdl !== null) {
            $this->setWsdl($wsdl);
        }
        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * Set wsdl
     *
     * @param string $wsdl
     * @return Zend_Soap_Client
     */
    public function setWsdl($wsdl)
    {
        $this->_wsdl = $wsdl;
        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get wsdl
     *
     * @return string
     */
    public function getWsdl()
    {
        return $this->_wsdl;
    }

    /**
     * Set Options
     *
     * Allows setting options as an associative array of option => value pairs.
     *
     * @param  array $options
     * @return Zend_Soap_Client
     * @throws Zend_SoapClient_Exception
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'classmap':
                case 'classMap':
                    $this->setClassmap($value);
                    break;
                case 'encoding':
                    $this->setEncoding($value);
                    break;
                case 'soapVersion':
                case 'soap_version':
                    $this->setSoapVersion($value);
                    break;
                case 'wsdl':
                    $this->setWsdl($value);
                    break;
                case 'uri':
                    $this->setUri($value);
                    break;
                case 'location':
                    $this->setLocation($value);
                    break;
                case 'style':
                    $this->setStyle($value);
                    break;
                case 'use':
                    $this->setEncodingMethod($value);
                    break;
                case 'login':
                    $this->setHttpLogin($value);
                    break;
                case 'password':
                    $this->setHttpPassword($value);
                    break;
                case 'proxy_host':
                    $this->setProxyHost($value);
                    break;
                case 'proxy_port':
                    $this->setProxyPort($value);
                    break;
                case 'proxy_login':
                    $this->setProxyLogin($value);
                    break;
                case 'proxy_password':
                    $this->setProxyPassword($value);
                    break;
                case 'local_cert':
                    $this->setHttpsCertificate($value);
                    break;
                case 'passphrase':
                    $this->setHttpsCertPassphrase($value);
                    break;
                case 'compression':
                    $this->setCompressionOptions($value);
                    break;

                // Not used now
                // case 'connection_timeout':
                //     $this->_connection_timeout = $value;
                //    break;

                default:
                    throw new Zend_Soap_Client_Exception('Unknown SOAP client option');
                    break;
            }
        }

        return $this;
    }

    /**
     * Return array of options suitable for using with SoapClient constructor
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array();

        $options['classmap']       = $this->getClassmap();
        $options['encoding']       = $this->getEncoding();
        $options['soap_version']   = $this->getSoapVersion();
        $options['wsdl']           = $this->getWsdl();
        $options['uri']            = $this->getUri();
        $options['location']       = $this->getLocation();
        $options['style']          = $this->getStyle();
        $options['use']            = $this->getEncodingMethod();
        $options['login']          = $this->getHttpLogin();
        $options['password']       = $this->getHttpPassword();
        $options['proxy_host']     = $this->getProxyHost();
        $options['proxy_port']     = $this->getProxyPort();
        $options['proxy_login']    = $this->getProxyLogin();
        $options['proxy_password'] = $this->getProxyPassword();
        $options['local_cert']     = $this->getHttpsCertificate();
        $options['passphrase']     = $this->getHttpsCertPassphrase();
        $options['compression']    = $this->getCompressionOptions();
//        $options['connection_timeout'] = $this->_connection_timeout;

        foreach ($options as $key => $value) {
            if ($value == null) {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * Set SOAP version
     *
     * @param  int $version One of the SOAP_1_1 or SOAP_1_2 constants
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid soap version argument
     */
    public function setSoapVersion($version)
    {
        if (!in_array($version, array(SOAP_1_1, SOAP_1_2))) {
            throw new Zend_Soap_Client_Exception('Invalid soap version specified. Use SOAP_1_1 or SOAP_1_2 constants.');
        }
        $this->_soapVersion = $version;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get SOAP version
     *
     * @return int
     */
    public function getSoapVersion()
    {
        return $this->_soapVersion;
    }

    /**
     * Set classmap
     *
     * @param  array $classmap
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception for any invalid class in the class map
     */
    public function setClassmap(array $classmap)
    {
        foreach ($classmap as $type => $class) {
            if (!class_exists($class)) {
                throw new Zend_Soap_Client_Exception('Invalid class in class map');
            }
        }

        $this->_classmap = $classmap;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Retrieve classmap
     *
     * @return mixed
     */
    public function getClassmap()
    {
        return $this->_classmap;
    }

    /**
     * Set encoding
     *
     * @param  string $encoding
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid encoding argument
     */
    public function setEncoding($encoding)
    {
        if (!is_string($encoding)) {
            throw new Zend_Soap_Client_Exception('Invalid encoding specified');
        }

        $this->_encoding = $encoding;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Check for valid URN
     *
     * @param  string $urn
     * @return true
     * @throws Zend_Soap_Client_Exception on invalid URN
     */
    public function validateUrn($urn)
    {
        $segs = parse_url($urn);
        if (isset($segs['scheme'])) {
            return true;
        }

        throw new Zend_Soap_Client_Exception('Invalid URN');
    }

    /**
     * Set URI
     *
     * URI in Web Service the target namespace
     *
     * @param  string $uri
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid uri argument
     */
    public function setUri($uri)
    {
        $this->validateUrn($uri);
        $this->_uri = $uri;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Retrieve URI
     *
     * @return string
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * Set Location
     *
     * URI in Web Service the target namespace
     *
     * @param  string $location
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid uri argument
     */
    public function setLocation($location)
    {
        $this->validateUrn($location);
        $this->_location = $location;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Retrieve URI
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->_location;
    }

    /**
     * Set request style
     *
     * @param  int $style One of the SOAP_RPC or SOAP_DOCUMENT constants
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid style argument
     */
    public function setStyle($style)
    {
        if (!in_array($style, array(SOAP_RPC, SOAP_DOCUMENT))) {
            throw new Zend_Soap_Client_Exception('Invalid request style specified. Use SOAP_RPC or SOAP_DOCUMENT constants.');
        }

        $this->_style = $style;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get request style
     *
     * @return int
     */
    public function getStyle()
    {
        return $this->_style;
    }

    /**
     * Set message encoding method
     *
     * @param  int $use One of the SOAP_ENCODED or SOAP_LITERAL constants
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid message encoding method argument
     */
    public function setEncodingMethod($use)
    {
        if (!in_array($use, array(SOAP_ENCODED, SOAP_LITERAL))) {
            throw new Zend_Soap_Client_Exception('Invalid message encoding method. Use SOAP_ENCODED or SOAP_LITERAL constants.');
        }

        $this->_use = $use;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get message encoding method
     *
     * @return int
     */
    public function getEncodingMethod()
    {
        return $this->_use;
    }

    /**
     * Set HTTP login
     *
     * @param  string $login
     * @return Zend_Soap_Client
     */
    public function setHttpLogin($login)
    {
        $this->_login = $login;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Retrieve HTTP Login
     *
     * @return string
     */
    public function getHttpLogin()
    {
        return $this->_login;
    }

    /**
     * Set HTTP password
     *
     * @param  string $password
     * @return Zend_Soap_Client
     */
    public function setHttpPassword($password)
    {
        $this->_password = $password;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Retrieve HTTP Password
     *
     * @return string
     */
    public function getHttpPassword()
    {
        return $this->_password;
    }

    /**
     * Set proxy host
     *
     * @param  string $proxyHost
     * @return Zend_Soap_Client
     */
    public function setProxyHost($proxyHost)
    {
        $this->_proxy_host = $proxyHost;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Retrieve proxy host
     *
     * @return string
     */
    public function getProxyHost()
    {
        return $this->_proxy_host;
    }

    /**
     * Set proxy port
     *
     * @param  int $proxyPort
     * @return Zend_Soap_Client
     */
    public function setProxyPort($proxyPort)
    {
        $this->_proxy_port = (int)$proxyPort;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Retrieve proxy port
     *
     * @return int
     */
    public function getProxyPort()
    {
        return $this->_proxy_port;
    }

    /**
     * Set proxy login
     *
     * @param  string $proxyLogin
     * @return Zend_Soap_Client
     */
    public function setProxyLogin($proxyLogin)
    {
        $this->_proxy_login = $proxyLogin;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Retrieve proxy login
     *
     * @return string
     */
    public function getProxyLogin()
    {
        return $this->_proxy_login;
    }

    /**
     * Set proxy password
     *
     * @param  string $proxyLogin
     * @return Zend_Soap_Client
     */
    public function setProxyPassword($proxyPassword)
    {
        $this->_proxy_password = $proxyPassword;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Set HTTPS client certificate path
     *
     * @param  string $localCert local certificate path
     * @return Zend_Soap_Client
     * @throws Zend_Soap_Client_Exception with invalid local certificate path argument
     */
    public function setHttpsCertificate($localCert)
    {
        if (!is_readable($localCert)) {
            throw new Zend_Soap_Client_Exception('Invalid HTTPS client certificate path.');
        }

        $this->_local_cert = $localCert;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get HTTPS client certificate path
     *
     * @return string
     */
    public function getHttpsCertificate()
    {
        return $this->_local_cert;
    }

    /**
     * Set HTTPS client certificate passphrase
     *
     * @param  string $passphrase
     * @return Zend_Soap_Client
     */
    public function setHttpsCertPassphrase($passphrase)
    {
        $this->_passphrase = $passphrase;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get HTTPS client certificate passphrase
     *
     * @return string
     */
    public function getHttpsCertPassphrase()
    {
        return $this->_passphrase;
    }

    /**
     * Set compression options
     *
     * @param  int $compressionOptions
     * @return Zend_Soap_Client
     */
    public function setCompressionOptions($compressionOptions)
    {
        $this->_compression = $compressionOptions;

        $this->_soapClient = null;

        return $this;
    }

    /**
     * Get Compression options
     *
     * @return int
     */
    public function getCompressionOptions()
    {
        return $this->_compression;
    }

    /**
     * Retrieve proxy password
     *
     * @return string
     */
    public function getProxyPassword()
    {
        return $this->_proxy_password;
    }

    /**
     * Retrieve request XML
     *
     * @return string
     */
    public function getLastRequest()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastRequest();
        }

        return '';
    }

    /**
     * Get response XML
     *
     * @return string
     */
    public function getLastResponse()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastResponse();
        }

        return '';
    }

    /**
     * Retrieve request headers
     *
     * @return string
     */
    public function getLastRequestHeaders()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastRequestHeaders();
        }

        return '';
    }

    /**
     * Retrieve response headers
     *
     * @return string
     */
    public function getLastResponseHeaders()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastResponseHeaders();
        }

        return '';
    }

    /**
     * Retrieve last invoked method
     *
     * @return string
     */
    public function getLastMethod()
    {
        return $this->_lastMethod;
    }

    /**
     * Do request proxy method.
     *
     * May be overridden in subclasses
     *
     * @internal
     * @param Zend_Soap_Client_Common $client
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $one_way
     * @return mixed
     */
    public function _doRequest(Zend_Soap_Client_Common $client, $request, $location, $action, $version, $one_way = null)
    {
        // Perform request as is
        if ($one_way == null) {
            return call_user_func(array($client,'SoapClient::__doRequest'), $request, $location, $action, $version);
        } else {
            return call_user_func(array($client,'SoapClient::__doRequest'), $request, $location, $action, $version, $one_way);
        }
    }

    /**
     * Initialize SOAP Client object
     *
     * @throws Zend_Soap_Client_Exception
     */
    protected function _initSoapClientObject()
    {
        $wsdl = $this->getWsdl();
        $options = array_merge($this->getOptions(), array('trace' => true));


        if ($wsdl == null) {
            if (!isset($options['location'])) {
                throw new Zend_Soap_Client_Exception('\'location\' parameter is required in non-WSDL mode.');
            }
            if (!isset($options['uri'])) {
                throw new Zend_Soap_Client_Exception('\'uri\' parameter is required in non-WSDL mode.');
            }
        } else {
            if (isset($options['use'])) {
                throw new Zend_Soap_Client_Exception('\'use\' parameter only works in non-WSDL mode.');
            }
            if (isset($options['style'])) {
                throw new Zend_Soap_Client_Exception('\'style\' parameter only works in non-WSDL mode.');
            }
        }
        unset($options['wsdl']);

        $this->_soapClient = new Zend_Soap_Client_Common(array($this, '_doRequest'), $wsdl, $options);
    }


    /**
     * Perform arguments pre-processing
     *
     * My be overridden in descendant classes
     *
     * @param array $arguments
     */
    protected function _preProcessArguments($arguments)
    {
        // Do nothing
        return $arguments;
    }

    /**
     * Perform result pre-processing
     *
     * My be overridden in descendant classes
     *
     * @param array $arguments
     */
    protected function _preProcessResult($result)
    {
        // Do nothing
        return $result;
    }

    /**
     * Perform a SOAP call
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($this->_soapClient == null) {
            $this->_initSoapClientObject();
        }

        $this->_lastMethod = $name; 
        
        $result = call_user_func_array(array($this->_soapClient, $name), $this->_preProcessArguments($arguments));

        return $this->_preProcessResult($result);
    }


    /**
     * Return a list of available functions
     *
     * @return array
     * @throws Zend_Soap_Client_Exception
     */
    public function getFunctions()
    {
        if ($this->getWsdl() == null) {
            throw new Zend_Soap_Client_Exception('\'getFunctions\' method is available only in WSDL mode.');
        }

        if ($this->_soapClient == null) {
            $this->_initSoapClientObject();
        }

        return $this->_soapClient->__getFunctions();
    }


    /**
     * Get used types.
     *
     * @return array
     */

    /**
     * Return a list of SOAP types
     *
     * @return array
     * @throws Zend_Soap_Client_Exception
     */
    public function getTypes()
    {
        if ($this->getWsdl() == null) {
            throw new Zend_Soap_Client_Exception('\'getTypes\' method is available only in WSDL mode.');
        }

        if ($this->_soapClient == null) {
            $this->_initSoapClientObject();
        }

        return $this->_soapClient->__getTypes();
    }
}
