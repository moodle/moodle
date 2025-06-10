<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * File for SessionManagementWsdlClass to communicate with SOAP service
 * @package SessionManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */
/**
 * SessionManagementWsdlClass to communicate with SOAP service
 *
 * @package SessionManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-01-19
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../panopto_timeout_soap_client.php');

class SessionManagementWsdlClass extends stdClass implements ArrayAccess,Iterator,Countable
{
    /**
     * Option key to define WSDL url
     * @var string
     */
    const WSDL_URL = 'wsdl_url';
    /**
     * Constant to define the default WSDL URI
     * @var string
     */
    const VALUE_WSDL_URL = 'https://demo.hosted.panopto.com/Panopto/PublicAPI/4.6/SessionManagement.svc?singlewsdl';
    /**
     * Option key to define WSDL login
     * @var string
     */
    const WSDL_LOGIN = 'wsdl_login';
    /**
     * Option key to define WSDL password
     * @deprecated use WSDL_PASSWORD instead
     * @var string
     */
    const WSDL_PASSWD = 'wsdl_password';
    /**
     * Option key to define WSDL password
     * @var string
     */
    const WSDL_PASSWORD = 'wsdl_password';
    /**
     * Option key to define WSDL trace option
     * @var string
     */
    const WSDL_TRACE = 'wsdl_trace';
    /**
     * Option key to define WSDL exceptions
     * @deprecated use WSDL_EXCEPTIONS instead
     * @var string
     */
    const WSDL_EXCPTS = 'wsdl_exceptions';
    /**
     * Option key to define WSDL exceptions
     * @var string
     */
    const WSDL_EXCEPTIONS = 'wsdl_exceptions';
    /**
     * Option key to define WSDL cache_wsdl
     * @var string
     */
    const WSDL_CACHE_WSDL = 'wsdl_cache_wsdl';
    /**
     * Option key to define WSDL stream_context
     * @var string
     */
    const WSDL_STREAM_CONTEXT = 'wsdl_stream_context';
    /**
     * Option key to define WSDL soap_version
     * @var string
     */
    const WSDL_SOAP_VERSION = 'wsdl_soap_version';
    /**
     * Option key to define WSDL compression
     * @var string
     */
    const WSDL_COMPRESSION = 'wsdl_compression';
    /**
     * Option key to define WSDL encoding
     * @var string
     */
    const WSDL_ENCODING = 'wsdl_encoding';
    /**
     * Option key to define WSDL connection_timeout
     * @var string
     */
    const WSDL_CONNECTION_TIMEOUT = 'wsdl_connection_timeout';
    /**
     * Option key to define WSDL typemap
     * @var string
     */
    const WSDL_TYPEMAP = 'wsdl_typemap';
    /**
     * Option key to define WSDL user_agent
     * @var string
     */
    const WSDL_USER_AGENT = 'wsdl_user_agent';
    /**
     * Option key to define WSDL features
     * @var string
     */
    const WSDL_FEATURES = 'wsdl_features';
    /**
     * Option key to define WSDL keep_alive
     * @var string
     */
    const WSDL_KEEP_ALIVE = 'wsdl_keep_alive';
    /**
     * Option key to define WSDL proxy_host
     * @var string
     */
    const WSDL_PROXY_HOST = 'wsdl_proxy_host';
    /**
     * Option key to define WSDL proxy_port
     * @var string
     */
    const WSDL_PROXY_PORT = 'wsdl_proxy_port';
    /**
     * Option key to define WSDL proxy_login
     * @var string
     */
    const WSDL_PROXY_LOGIN = 'wsdl_proxy_login';
    /**
     * Option key to define WSDL proxy_password
     * @var string
     */
    const WSDL_PROXY_PASSWORD = 'wsdl_proxy_password';
    /**
     * Option key to define WSDL local_cert
     * @var string
     */
    const WSDL_LOCAL_CERT = 'wsdl_local_cert';
    /**
     * Option key to define WSDL passphrase
     * @var string
     */
    const WSDL_PASSPHRASE = 'wsdl_passphrase';
    /**
     * Option key to define WSDL authentication
     * @var string
     */
    const WSDL_AUTHENTICATION = 'wsdl_authentication';
    /**
     * Option key to define WSDL ssl_method
     * @var string
     */
    const WSDL_SSL_METHOD = 'wsdl_ssl_method';
    /**
     * Soapclient called to communicate with the actual SOAP Service
     * @var PanoptoTimeoutSoapClient
     */
    private static $soapClient;
    /**
     * Contains Soap call result
     * @var mixed
     */
    private $result;
    /**
     * Contains last errors
     * @var array
     */
    private $lastError;
    /**
     * Array that contains values when only one parameter is set when calling __construct method
     * @var array
     */
    private $internArrayToIterate;
    /**
     * Bool that tells if array is set or not
     * @var bool
     */
    private $internArrayToIterateIsArray;
    /**
     * Items index browser
     * @var int
     */
    private $internArrayToIterateOffset;
    /**
     * Constructor
     * @uses SessionManagementWsdlClass::setLastError()
     * @uses SessionManagementWsdlClass::initSoapClient()
     * @uses SessionManagementWsdlClass::initInternArrayToIterate()
     * @uses SessionManagementWsdlClass::_set()
     * @param array $_arrayOfValues SoapClient options or object attribute values
     * @param bool $_resetSoapClient allows to disable the SoapClient redefinition
     * @return SessionManagementWsdlClass
     */
    public function __construct($_arrayOfValues = array(),$_resetSoapClient = true)
    {
        $this->setLastError(array());
        /**
         * Init soap Client
         * Set default values
         */
        if($_resetSoapClient)
            $this->initSoapClient($_arrayOfValues);
        /**
         * Init array of values if set
         */
        $this->initInternArrayToIterate($_arrayOfValues);
        /**
         * Generic set methods
         */
        if(is_array($_arrayOfValues) && count($_arrayOfValues))
        {
            foreach($_arrayOfValues as $name=>$value)
                $this->_set($name,$value);
        }

        if(array_key_exists('panopto_socket_timeout', $_arrayOfValues)) {
            self::$soapClient->__setSocketTimeout($_arrayOfValues['panopto_socket_timeout']);
        }

        if(array_key_exists('panopto_connection_timeout', $_arrayOfValues)) {
            self::$soapClient->__setConnectionTimeout($_arrayOfValues['panopto_connection_timeout']);
        }

        if(array_key_exists('wsdl_proxy_host', $_arrayOfValues)) {
            self::$soapClient->__setProxyHost($_arrayOfValues['wsdl_proxy_host']);
        }

        if(array_key_exists('wsdl_proxy_port', $_arrayOfValues)) {
            self::$soapClient->__setProxyPort($_arrayOfValues['wsdl_proxy_port']);
        }
    }
    /**
     * Generic method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @uses SessionManagementWsdlClass::_set()
     * @param array $_array the exported values
     * @param string $_className optional (used by inherited classes in order to always call this method)
     * @return SessionManagementWsdlClass|null
     */
    public static function __set_state(array $_array)
    {
        $_className = __CLASS__;
        if(class_exists($_className))
        {
            $object = @new $_className();
            if(is_object($object) && is_subclass_of($object,'SessionManagementWsdlClass'))
            {
                foreach($_array as $name=>$value)
                    $object->_set($name,$value);
            }
            return $object;
        }
        else
            return null;
    }
    /**
     * Static method getting current SoapClient
     * @return SoapClient
     */
    public static function getSoapClient()
    {
        return self::$soapClient;
    }
    /**
     * Static method setting current SoapClient
     * @param SoapClient $_soapClient
     * @return SoapClient
     */
    protected static function setSoapClient(SoapClient $_soapClient)
    {
        return (self::$soapClient = $_soapClient);
    }
    /**
     * Method initiating SoapClient
     * @uses SessionManagementClassMap::classMap()
     * @uses SessionManagementWsdlClass::getDefaultWsdlOptions()
     * @uses SessionManagementWsdlClass::getSoapClientClassName()
     * @uses SessionManagementWsdlClass::setSoapClient()
     * @param array $_wsdlOptions WSDL options
     * @return void
     */
    public function initSoapClient($_wsdlOptions)
    {
        if(class_exists('SessionManagementClassMap',true))
        {
            $wsdlOptions = array();
            $wsdlOptions['classmap'] = SessionManagementClassMap::classMap();
            $defaultWsdlOptions = self::getDefaultWsdlOptions();
            foreach($defaultWsdlOptions as $optioName=>$optionValue)
            {
                if(array_key_exists($optioName,$_wsdlOptions) && !empty($_wsdlOptions[$optioName]))
                    $wsdlOptions[str_replace('wsdl_','',$optioName)] = $_wsdlOptions[$optioName];
                elseif(!empty($optionValue))
                    $wsdlOptions[str_replace('wsdl_','',$optioName)] = $optionValue;
            }
            if(array_key_exists(str_replace('wsdl_','',self::WSDL_URL),$wsdlOptions))
            {
                $wsdlUrl = $wsdlOptions[str_replace('wsdl_','',self::WSDL_URL)];
                unset($wsdlOptions[str_replace('wsdl_','',self::WSDL_URL)]);
                $soapClientClassName = self::getSoapClientClassName();
                self::setSoapClient(new $soapClientClassName($wsdlUrl,$wsdlOptions));
            }
        }
    }
    /**
     * Returns the SoapClient class name to use to create the instance of the SoapClient.
     * The SoapClient class is determined based on the package name.
     * If a class is named as {SessionManagement}SoapClient, then this is the class that will be used.
     * Be sure that this class inherits from the native PHP SoapClient class and this class has been loaded or can be loaded.
     * The goal is to allow the override of the SoapClient without having to modify this generated class.
     * Then the overridding SoapClient class can override for example the SoapClient::__doRequest() method if it is needed.
     * @return string
     */
    public static function getSoapClientClassName()
    {
        if(class_exists('SessionManagementSoapClient') && is_subclass_of('SessionManagementSoapClient','PanoptoTimeoutSoapClient'))
            return 'SessionManagementSoapClient';
        else
            return 'PanoptoTimeoutSoapClient';
    }
    /**
     * Method returning all default options values
     * @uses SessionManagementWsdlClass::WSDL_CACHE_WSDL
     * @uses SessionManagementWsdlClass::WSDL_COMPRESSION
     * @uses SessionManagementWsdlClass::WSDL_CONNECTION_TIMEOUT
     * @uses SessionManagementWsdlClass::WSDL_ENCODING
     * @uses SessionManagementWsdlClass::WSDL_EXCEPTIONS
     * @uses SessionManagementWsdlClass::WSDL_FEATURES
     * @uses SessionManagementWsdlClass::WSDL_LOGIN
     * @uses SessionManagementWsdlClass::WSDL_PASSWORD
     * @uses SessionManagementWsdlClass::WSDL_SOAP_VERSION
     * @uses SessionManagementWsdlClass::WSDL_STREAM_CONTEXT
     * @uses SessionManagementWsdlClass::WSDL_TRACE
     * @uses SessionManagementWsdlClass::WSDL_TYPEMAP
     * @uses SessionManagementWsdlClass::WSDL_URL
     * @uses SessionManagementWsdlClass::VALUE_WSDL_URL
     * @uses SessionManagementWsdlClass::WSDL_USER_AGENT
     * @uses SessionManagementWsdlClass::WSDL_PROXY_HOST
     * @uses SessionManagementWsdlClass::WSDL_PROXY_PORT
     * @uses SessionManagementWsdlClass::WSDL_PROXY_LOGIN
     * @uses SessionManagementWsdlClass::WSDL_PROXY_PASSWORD
     * @uses SessionManagementWsdlClass::WSDL_LOCAL_CERT
     * @uses SessionManagementWsdlClass::WSDL_PASSPHRASE
     * @uses SessionManagementWsdlClass::WSDL_AUTHENTICATION
     * @uses SessionManagementWsdlClass::WSDL_SSL_METHOD
     * @uses SOAP_SINGLE_ELEMENT_ARRAYS
     * @uses SOAP_USE_XSI_ARRAY_TYPE
     * @return array
     */
    public static function getDefaultWsdlOptions()
    {
        return array(
                    self::WSDL_CACHE_WSDL=>WSDL_CACHE_NONE,
                    self::WSDL_COMPRESSION=>null,
                    self::WSDL_CONNECTION_TIMEOUT=>null,
                    self::WSDL_ENCODING=>null,
                    self::WSDL_EXCEPTIONS=>true,
                    self::WSDL_FEATURES=>SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE,
                    self::WSDL_LOGIN=>null,
                    self::WSDL_PASSWORD=>null,
                    self::WSDL_SOAP_VERSION=>null,
                    self::WSDL_STREAM_CONTEXT=>null,
                    self::WSDL_TRACE=>true,
                    self::WSDL_TYPEMAP=>null,
                    self::WSDL_URL=>self::VALUE_WSDL_URL,
                    self::WSDL_USER_AGENT=>null,
                    self::WSDL_PROXY_HOST=>null,
                    self::WSDL_PROXY_PORT=>null,
                    self::WSDL_PROXY_LOGIN=>null,
                    self::WSDL_PROXY_PASSWORD=>null,
                    self::WSDL_LOCAL_CERT=>null,
                    self::WSDL_PASSPHRASE=>null,
                    self::WSDL_AUTHENTICATION=>null,
                    self::WSDL_SSL_METHOD=>null);
    }
    /**
     * Allows to set the SoapClient location to call
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SoapClient::__setLocation()
     * @param string $_location
     */
    public function setLocation($_location)
    {
        return self::getSoapClient()?self::getSoapClient()->__setLocation($_location):false;
    }
    /**
     * Returns the last request content as a DOMDocument or as a formated XML String
     * @see SoapClient::__getLastRequest()
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::getFormatedXml()
     * @uses SoapClient::__getLastRequest()
     * @param bool $_asDomDocument
     * @return DOMDocument|string
     */
    public function getLastRequest($_asDomDocument = false)
    {
        if(self::getSoapClient())
            return self::getFormatedXml(self::getSoapClient()->__getLastRequest(),$_asDomDocument);
        return null;
    }
    /**
     * Returns the last response content as a DOMDocument or as a formated XML String
     * @see SoapClient::__getLastResponse()
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::getFormatedXml()
     * @uses SoapClient::__getLastResponse()
     * @param bool $_asDomDocument
     * @return DOMDocument|string
     */
    public function getLastResponse($_asDomDocument = false)
    {
        if(self::getSoapClient())
            return self::getFormatedXml(self::getSoapClient()->__getLastResponse(),$_asDomDocument);
        return null;
    }
    /**
     * Returns the last request headers used by the SoapClient object as the original value or an array
     * @see SoapClient::__getLastRequestHeaders()
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::convertStringHeadersToArray()
     * @uses SoapClient::__getLastRequestHeaders()
     * @param bool $_asArray allows to get the headers in an associative array
     * @return null|string|array
     */
    public function getLastRequestHeaders($_asArray = false)
    {
        $headers = self::getSoapClient()?self::getSoapClient()->__getLastRequestHeaders():null;
        if(is_string($headers) && $_asArray)
            return self::convertStringHeadersToArray($headers);
        return $headers;
    }
    /**
     * Returns the last response headers used by the SoapClient object as the original value or an array
     * @see SoapClient::__getLastResponseHeaders()
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SessionManagementWsdlClass::convertStringHeadersToArray()
     * @uses SoapClient::__getLastRequestHeaders()
     * @param bool $_asArray allows to get the headers in an associative array
     * @return null|string|array
     */
    public function getLastResponseHeaders($_asArray = false)
    {
        $headers = self::getSoapClient()?self::getSoapClient()->__getLastResponseHeaders():null;
        if(is_string($headers) && $_asArray)
            return self::convertStringHeadersToArray($headers);
        return $headers;
    }
    /**
     * Returns a XML string content as a DOMDocument or as a formated XML string
     * @uses DOMDocument::loadXML()
     * @uses DOMDocument::saveXML()
     * @param string $_string
     * @param bool $_asDomDocument
     * @return DOMDocument|string|null
     */
    public static function getFormatedXml($_string,$_asDomDocument = false)
    {
        if(!empty($_string) && class_exists('DOMDocument'))
        {
            $dom = new DOMDocument('1.0','UTF-8');
            $dom->formatOutput = true;
            $dom->preserveWhiteSpace = false;
            $dom->resolveExternals = false;
            $dom->substituteEntities = false;
            $dom->validateOnParse = false;
            if($dom->loadXML($_string))
                return $_asDomDocument?$dom:$dom->saveXML();
        }
        return $_asDomDocument?null:$_string;
    }
    /**
     * Returns an associative array between the headers name and their respective values
     * @param string $_headers
     * @return array
     */
    public static function convertStringHeadersToArray($_headers)
    {
        $lines = explode("\r\n",$_headers);
        $headers = array();
        foreach($lines as $line)
        {
            if(strpos($line,':'))
            {
                $headerParts = explode(':',$line);
                $headers[$headerParts[0]] = trim(implode(':',array_slice($headerParts,1)));
            }
        }
        return $headers;
    }
    /**
     * Sets a SoapHeader to send
     * For more information, please read the online documentation on {@link http://www.php.net/manual/en/class.soapheader.php}
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @uses SoapClient::__setSoapheaders()
     * @param string $_nameSpace SoapHeader namespace
     * @param string $_name SoapHeader name
     * @param mixed $_data SoapHeader data
     * @param bool $_mustUnderstand
     * @param string $_actor
     * @return bool true|false
     */
    public function setSoapHeader($_nameSpace,$_name,$_data,$_mustUnderstand = false,$_actor = null)
    {
        if(self::getSoapClient())
        {
            $defaultHeaders = (isset(self::getSoapClient()->__default_headers) && is_array(self::getSoapClient()->__default_headers))?self::getSoapClient()->__default_headers:array();
            foreach($defaultHeaders as $index=>$soapheader)
            {
                if($soapheader->name == $_name)
                {
                    unset($defaultHeaders[$index]);
                    break;
                }
            }
            self::getSoapClient()->__setSoapheaders(null);
            if(!empty($_actor))
                array_push($defaultHeaders,new SoapHeader($_nameSpace,$_name,$_data,$_mustUnderstand,$_actor));
            else
                array_push($defaultHeaders,new SoapHeader($_nameSpace,$_name,$_data,$_mustUnderstand));
            return self::getSoapClient()->__setSoapheaders($defaultHeaders);
        }
        else
            return false;
    }
    /**
     * Sets the SoapClient Stream context HTTP Header name according to its value
     * If a context already exists, it tries to modify it
     * It the context does not exist, it then creates it with the header name and its value
     * @uses SessionManagementWsdlClass::getSoapClient()
     * @param string $_headerName
     * @param mixed $_headerValue
     * @return bool true|false
     */
    public function setHttpHeader($_headerName,$_headerValue)
    {
        if(self::getSoapClient() && !empty($_headerName))
        {
            $streamContext = (isset(self::getSoapClient()->_stream_context) && is_resource(self::getSoapClient()->_stream_context))?self::getSoapClient()->_stream_context:null;
            if(!is_resource($streamContext))
            {
                $options = array();
                $options['http'] = array();
                $options['http']['header'] = '';
            }
            else
            {
                $options = stream_context_get_options($streamContext);
                if(is_array($options))
                {
                    if(!array_key_exists('http',$options) || !is_array($options['http']))
                    {
                        $options['http'] = array();
                        $options['http']['header'] = '';
                    }
                    elseif(!array_key_exists('header',$options['http']))
                        $options['http']['header'] = '';
                }
                else
                {
                    $options = array();
                    $options['http'] = array();
                    $options['http']['header'] = '';
                }
            }
            if(count($options) && array_key_exists('http',$options) && is_array($options['http']) && array_key_exists('header',$options['http']) && is_string($options['http']['header']))
            {
                $lines = explode("\r\n",$options['http']['header']);
                /**
                 * Ensure there is only one header entry for this header name
                 */
                $newLines = array();
                foreach($lines as $line)
                {
                    if(!empty($line) && strpos($line,$_headerName) === false)
                        array_push($newLines,$line);
                }
                /**
                 * Add new header entry
                 */
                array_push($newLines,"$_headerName: $_headerValue");
                /**
                 * Set the context http header option
                 */
                $options['http']['header'] = implode("\r\n",$newLines);
                /**
                 * Create context if it does not exist
                 */
                if(!is_resource($streamContext))
                    return (self::getSoapClient()->_stream_context = stream_context_create($options))?true:false;
                /**
                 * Set the new context http header option
                 */
                else
                    return stream_context_set_option(self::getSoapClient()->_stream_context,'http','header',$options['http']['header']);
            }
            else
                return false;
        }
        else
            return false;
    }
    /**
     * Method alias to count
     * @uses SessionManagementWsdlClass::count()
     * @return int
     */
    public function length()
    {
        return $this->count();
    }
    /**
     * Method returning item length, alias to length
     * @uses SessionManagementWsdlClass::getInternArrayToIterate()
     * @uses SessionManagementWsdlClass::getInternArrayToIterateIsArray()
     * @return int
     */
    public function count(): int
    {
        return $this->getInternArrayToIterateIsArray()?count($this->getInternArrayToIterate()):-1;
    }
    /**
     * Method returning the current element
     * @uses SessionManagementWsdlClass::offsetGet()
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->offsetGet($this->internArrayToIterateOffset);
    }
    /**
     * Method moving the current position to the next element
     * @uses SessionManagementWsdlClass::getInternArrayToIterateOffset()
     * @uses SessionManagementWsdlClass::setInternArrayToIterateOffset()
     * @return void
     */
    public function next(): void
    {
        $this->setInternArrayToIterateOffset($this->getInternArrayToIterateOffset() + 1);
    }
    /**
     * Method resetting itemOffset
     * @uses SessionManagementWsdlClass::setInternArrayToIterateOffset()
     * @return void
     */
    public function rewind(): void
    {
        $this->setInternArrayToIterateOffset(0);
    }
    /**
     * Method checking if current itemOffset points to an existing item
     * @uses SessionManagementWsdlClass::getInternArrayToIterateOffset()
     * @uses SessionManagementWsdlClass::offsetExists()
     * @return bool true|false
     */
    public function valid(): bool
    {
        return $this->offsetExists($this->getInternArrayToIterateOffset());
    }
    /**
     * Method returning current itemOffset value, alias to getInternArrayToIterateOffset
     * @uses SessionManagementWsdlClass::getInternArrayToIterateOffset()
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->getInternArrayToIterateOffset();
    }
    /**
     * Method alias to offsetGet
     * @see SessionManagementWsdlClass::offsetGet()
     * @uses SessionManagementWsdlClass::offsetGet()
     * @param int $_index
     * @return mixed
     */
    public function item($_index)
    {
        return $this->offsetGet($_index);
    }
    /**
     * Default method adding item to array
     * @uses SessionManagementWsdlClass::getAttributeName()
     * @uses SessionManagementWsdlClass::__toString()
     * @uses SessionManagementWsdlClass::_set()
     * @uses SessionManagementWsdlClass::_get()
     * @uses SessionManagementWsdlClass::setInternArrayToIterate()
     * @uses SessionManagementWsdlClass::setInternArrayToIterateIsArray()
     * @uses SessionManagementWsdlClass::setInternArrayToIterateOffset()
     * @param mixed $_item value
     * @return bool true|false
     */
    public function add($_item)
    {
        if($this->getAttributeName() != '' && stripos($this->__toString(),'array') !== false)
        {
            /**
             * init array
             */
            if(!is_array($this->_get($this->getAttributeName())))
                $this->_set($this->getAttributeName(),array());
            /**
             * current array
             */
            $currentArray = $this->_get($this->getAttributeName());
            array_push($currentArray,$_item);
            $this->_set($this->getAttributeName(),$currentArray);
            $this->setInternArrayToIterate($currentArray);
            $this->setInternArrayToIterateIsArray(true);
            $this->setInternArrayToIterateOffset(0);
            return true;
        }
        return false;
    }
    /**
     * Method to call when sending data to request for *array* type class
     * @uses SessionManagementWsdlClass::getAttributeName()
     * @uses SessionManagementWsdlClass::__toString()
     * @uses SessionManagementWsdlClass::_get()
     * @return mixed
     */
    public function toSend()
    {
        if($this->getAttributeName() != '' && stripos($this->__toString(),'array') !== false)
            return $this->_get($this->getAttributeName());
        else
            return null;
    }
    /**
     * Method returning the first item
     * @uses SessionManagementWsdlClass::item()
     * @return mixed
     */
    public function first()
    {
        return $this->item(0);
    }
    /**
     * Method returning the last item
     * @uses SessionManagementWsdlClass::item()
     * @uses SessionManagementWsdlClass::length()
     * @return mixed
     */
    public function last()
    {
        return $this->item($this->length() - 1);
    }
    /**
     * Method testing index in item
     * @uses SessionManagementWsdlClass::getInternArrayToIterateIsArray()
     * @uses SessionManagementWsdlClass::getInternArrayToIterate()
     * @param int $_offset
     * @return bool true|false
     */
    public function offsetExists($_offset): bool
    {
        return ($this->getInternArrayToIterateIsArray() && array_key_exists($_offset,$this->getInternArrayToIterate()));
    }
    /**
     * Method returning the item at "index" value
     * @uses SessionManagementWsdlClass::offsetExists()
     * @param int $_offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($_offset)
    {
        return $this->offsetExists($_offset)?$this->internArrayToIterate[$_offset]:null;
    }
    /**
     * Method useless but necessarly overridden, can't set
     * @param mixed $_offset
     * @param mixed $_value
     * @return void
     */
    public function offsetSet($_offset,$_value): void
    {}
    /**
     * Method useless but necessarly overridden, can't unset
     * @param mixed $_offset
     * @return void
     */
    public function offsetUnset($_offset): void
    {}
    /**
     * Method returning current result from Soap call
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
    /**
     * Method setting current result from Soap call
     * @param mixed $_result
     * @return mixed
     */
    protected function setResult($_result)
    {
        return ($this->result = $_result);
    }
    /**
     * Method returning last errors occured during the calls
     * @return array
     */
    public function getLastError()
    {
        return $this->lastError;
    }
    /**
     * Method setting last errors occured during the calls
     * @param array $_lastError
     * @return array
     */
    private function setLastError($_lastError)
    {
        return ($this->lastError = $_lastError);
    }
    /**
     * Method saving the last error returned by the SoapClient
     * @param string $_methoName the method called when the error occurred
     * @param SoapFault $_soapFault l'objet de l'erreur
     * @return bool true|false
     */
    protected function saveLastError($_methoName,SoapFault $_soapFault)
    {
        return ($this->lastError[$_methoName] = $_soapFault);
    }
    /**
     * Method getting the last error for a certain method
     * @param string $_methoName method name to get error from
     * @return SoapFault|null
     */
    public function getLastErrorForMethod($_methoName)
    {
        return (is_array($this->lastError) && array_key_exists($_methoName,$this->lastError))?$this->lastError[$_methoName]:null;
    }
    /**
     * Method returning intern array to iterate trough
     * @return array
     */
    public function getInternArrayToIterate()
    {
        return $this->internArrayToIterate;
    }
    /**
     * Method setting intern array to iterate trough
     * @param array $_internArrayToIterate
     * @return array
     */
    public function setInternArrayToIterate($_internArrayToIterate)
    {
        return ($this->internArrayToIterate = $_internArrayToIterate);
    }
    /**
     * Method returnint intern array index when iterating trough
     * @return int
     */
    public function getInternArrayToIterateOffset()
    {
        return $this->internArrayToIterateOffset;
    }
    /**
     * Method initiating internArrayToIterate
     * @uses SessionManagementWsdlClass::setInternArrayToIterate()
     * @uses SessionManagementWsdlClass::setInternArrayToIterateOffset()
     * @uses SessionManagementWsdlClass::setInternArrayToIterateIsArray()
     * @uses SessionManagementWsdlClass::getAttributeName()
     * @uses SessionManagementWsdlClass::initInternArrayToIterate()
     * @uses SessionManagementWsdlClass::__toString()
     * @param array $_array the array to iterate trough
     * @param bool $_internCall indicates that methods is calling itself
     * @return void
     */
    public function initInternArrayToIterate($_array = array(),$_internCall = false)
    {
        if(stripos($this->__toString(),'array') !== false)
        {
            if(is_array($_array) && count($_array))
            {
                $this->setInternArrayToIterate($_array);
                $this->setInternArrayToIterateOffset(0);
                $this->setInternArrayToIterateIsArray(true);
            }
            elseif(!$_internCall && $this->getAttributeName() != '' && property_exists($this->__toString(),$this->getAttributeName()))
                $this->initInternArrayToIterate($this->_get($this->getAttributeName()),true);
        }
    }
    /**
     * Method setting intern array offset when iterating trough
     * @param int $_internArrayToIterateOffset
     * @return int
     */
    public function setInternArrayToIterateOffset($_internArrayToIterateOffset)
    {
        return ($this->internArrayToIterateOffset = $_internArrayToIterateOffset);
    }
    /**
     * Method returning true if intern array is an actual array
     * @return bool true|false
     */
    public function getInternArrayToIterateIsArray()
    {
        return $this->internArrayToIterateIsArray;
    }
    /**
     * Method setting if intern array is an actual array
     * @param bool $_internArrayToIterateIsArray
     * @return bool true|false
     */
    public function setInternArrayToIterateIsArray($_internArrayToIterateIsArray = false)
    {
        return ($this->internArrayToIterateIsArray = $_internArrayToIterateIsArray);
    }
    /**
     * Generic method setting value
     * @param string $_name property name to set
     * @param mixed $_value property value to use
     * @return bool
     */
    public function _set($_name,$_value)
    {
        $setMethod = 'set' . ucfirst($_name);
        if(method_exists($this,$setMethod))
        {
            $this->$setMethod($_value);
            return true;
        }
        else
            return false;
    }
    /**
     * Generic method getting value
     * @param string $_name property name to get
     * @return mixed
     */
    public function _get($_name)
    {
        $getMethod = 'get' . ucfirst($_name);
        if(method_exists($this,$getMethod))
            return $this->$getMethod();
        else
            return false;
    }
    /**
     * Method returning alone attribute name when class is *array* type
     * @return string
     */
    public function getAttributeName()
    {
        return '';
    }
    /**
     * Generic method telling if current value is valid according to the attribute setted with the current value
     * @param mixed $_value the value to test
     * @return bool true|false
     */
    public static function valueIsValid($_value)
    {
        return true;
    }
    /**
     * Method returning actual class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}

/**
* Class SessionManagementSoapClient
*/
class SessionManagementSoapClient extends PanoptoTimeoutSoapClient {

    /**
     * Constructor wrapper
     */
    public function __construct ($wsdl, array $options = null) {
        parent::__construct($wsdl, $options);
    }

    /**
     * Wrapper around dorequest so we can enforce https on all calls
     *
     * @param object $request - the request being made
     * @param string $location - the location the request will be made to
     * @param string $action
     * @param string $version
     * @param int $one_way
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0): ?string {
        if (get_config('block_panopto', 'enforce_https_on_wsdl')) {
            $location = str_replace('http://', 'https://', $location);
        }

        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
