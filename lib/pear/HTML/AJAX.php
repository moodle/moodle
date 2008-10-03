<?php
/**
 * OO AJAX Implementation for PHP
 *
 * SVN Rev: $Id$
 *
 * @category  HTML
 * @package   AJAX
 * @author    Joshua Eichorn <josh@bluga.net>
 * @author    Arpad Ray <arpad@php.net>
 * @author    David Coallier <davidc@php.net>
 * @author    Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright 2005-2008 Joshua Eichorn, Arpad Ray, David Coallier, Elizabeth Smith
 * @license   http://www.opensource.org/licenses/lgpl-license.php   LGPL
 * @version   Release: 0.5.6
 * @link      http://pear.php.net/package/HTML_AJAX
 */

/**
 * This is a quick hack, loading serializers as needed doesn't work in php5
 */
require_once "HTML/AJAX/Serializer/JSON.php";
require_once "HTML/AJAX/Serializer/Null.php";
require_once "HTML/AJAX/Serializer/Error.php";
require_once "HTML/AJAX/Serializer/XML.php";
require_once "HTML/AJAX/Serializer/PHP.php";
require_once 'HTML/AJAX/Debug.php';
    
/**
 * OO AJAX Implementation for PHP
 *
 * @category  HTML
 * @package   AJAX
 * @author    Joshua Eichorn <josh@bluga.net>
 * @author    Arpad Ray <arpad@php.net>
 * @author    David Coallier <davidc@php.net>
 * @author    Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright 2005-2008 Joshua Eichorn, Arpad Ray, David Coallier, Elizabeth Smith
 * @license   http://www.opensource.org/licenses/lgpl-license.php   LGPL
 * @version   Release: 0.5.6
 * @link      http://pear.php.net/package/HTML_AJAX
 */
class HTML_AJAX
{
    /**
     * An array holding the instances were exporting
     *
     * key is the exported name
     *
     * row format is 
     * <code>
     * array('className'=>'','exportedName'=>'','instance'=>'','exportedMethods=>'')
     * </code>
     *
     * @var object
     * @access private
     */    
    var $_exportedInstances = array();

    /**
     * Set the server url in the generated stubs to this value
     * If set to false, serverUrl will not be set
     * @var false|string
     */
    var $serverUrl = false;

    /**
     * What encoding your going to use for serializing data 
     * from php being sent to javascript.
     *
     * @var string  JSON|PHP|Null
     */
    var $serializer = 'JSON';

    /**
     * What encoding your going to use for unserializing data sent from javascript
     * @var string  JSON|PHP|Null
     */
    var $unserializer = 'JSON';

    /**
     * Option to use loose typing for JSON encoding
     * @var bool
     * @access public
     */
    var $jsonLooseType = true;

    /**
     * Content-type map
     *
     * Used in to automatically choose serializers as needed
     */
    var $contentTypeMap = array(
            'JSON'  => 'application/json',
            'XML'   => 'application/xml',
            'Null'  => 'text/plain',
            'Error' => 'application/error',
            'PHP'   => 'application/php-serialized',
            'Urlencoded' => 'application/x-www-form-urlencoded'
        );
    
    /**
     * This is the debug variable that we will be passing the
     * HTML_AJAX_Debug instance to.
     *
     * @param object HTML_AJAX_Debug
     */
    var $debug;

    /**
     * This is to tell if debug is enabled or not. If so, then
     * debug is called, instantiated then saves the file and such.
     */
    var $debugEnabled = false;
    
    /**
     * This puts the error into a session variable is set to true.
     * set to false by default.
     *
     * @access public
     */
    var $debugSession = false;

    /**
     * Boolean telling if the Content-Length header should be sent. 
     *
     * If your using a gzip handler on an output buffer, or run into 
     * any compatability problems, try disabling this.
     *
     * @access public
     * @var boolean
     */
    var $sendContentLength = true;

    /**
     * Make Generated code compatible with php4 by lowercasing all 
     * class/method names before exporting to JavaScript.
     *
     * If you have code that works on php4 but not on php5 then setting 
     * this flag can fix the problem. The recommended solution is too 
     * specify the class and method names when registering the class 
     * letting you have function case in php4 as well
     *
     * @access public
     * @var boolean
     */
    var $php4CompatCase = false;

    /**
     * Automatically pack all generated JavaScript making it smaller
     *
     * If your using output compression this might not make sense
     */
    var $packJavaScript = false;

    /**
     * Holds current payload info
     *
     * @access private
     * @var string
     */
    var $_payload;

    /**
     * Holds iframe id IF this is an iframe xmlhttprequest
     *
     * @access private
     * @var string
     */
    var $_iframe;

    /**
     * Holds the list of classes permitted to be unserialized
     *
     * @access private
     * @var array
     */
    var $_allowedClasses = array();
    
    /**
     * Holds serializer instances
     */
    var $_serializers = array();
    
    /**
     * PHP callbacks we're exporting
     */
    var $_validCallbacks = array();

    /**
     * Interceptor instance
     */
    var $_interceptor = false;

    /**
     * Set a class to handle requests
     *
     * @param object &$instance       An instance to export
     * @param mixed  $exportedName    Name used for the javascript class, 
     *                                if false the name of the php class is used
     * @param mixed  $exportedMethods If false all functions without a _ prefix 
     *                                are exported, if an array only the methods 
     *                                listed in the array are exported
     *
     * @return void
     */
    function registerClass(&$instance, $exportedName = false, 
        $exportedMethods = false)
    {
        $className = strtolower(get_class($instance));

        if ($exportedName === false) {
            $exportedName = get_class($instance);
            if ($this->php4CompatCase) {
                $exportedName = strtolower($exportedName);
            }
        }

        if ($exportedMethods === false) {
            $exportedMethods = $this->_getMethodsToExport($className);
        }


        $index                                               = strtolower($exportedName);
        $this->_exportedInstances[$index]                    = array();
        $this->_exportedInstances[$index]['className']       = $className;
        $this->_exportedInstances[$index]['exportedName']    = $exportedName;
        $this->_exportedInstances[$index]['instance']        =& $instance;
        $this->_exportedInstances[$index]['exportedMethods'] = $exportedMethods;
    }

    /**
     * Get a list of methods in a class to export
     *
     * This function uses get_class_methods to get a list of callable methods, 
     * so if you're on PHP5 extending this class with a class you want to export 
     * should export its protected methods, while normally only its public methods 
     * would be exported. All methods starting with _ are removed from the export list.
     * This covers PHP4 style private by naming as well as magic methods in either PHP4 or PHP5
     *
     * @param string $className Name of the class
     *
     * @return array all methods of the class that are public
     * @access private
     */    
    function _getMethodsToExport($className)
    {
        $funcs = get_class_methods($className);

        foreach ($funcs as $key => $func) {
            if (strtolower($func) === $className || substr($func, 0, 1) === '_') {
                unset($funcs[$key]);
            } else if ($this->php4CompatCase) {
                $funcs[$key] = strtolower($func);
            }
        }
        return $funcs;
    }

    /**
     * Generate the client Javascript code
     *
     * @return   string   generated javascript client code
     */
    function generateJavaScriptClient()
    {
        $client = '';

        $names = array_keys($this->_exportedInstances);
        foreach ($names as $name) {
            $client .= $this->generateClassStub($name);
        }
        return $client;
    }

    /**
     * Return the stub for a class
     *
     * @param string $name name of the class to generated the stub for, 
     * note that this is the exported name not the php class name
     *
     * @return string javascript proxy stub code for a single class
     */
    function generateClassStub($name)
    {
        if (!isset($this->_exportedInstances[$name])) {
            return '';
        }

        $client  = "// Client stub for the {$this->_exportedInstances[$name]['exportedName']} PHP Class\n";
        $client .= "function {$this->_exportedInstances[$name]['exportedName']}(callback) {\n";
        $client .= "\tmode = 'sync';\n";
        $client .= "\tif (callback) { mode = 'async'; }\n";
        $client .= "\tthis.className = '{$this->_exportedInstances[$name]['exportedName']}';\n";
        if ($this->serverUrl) {
            $client .= "\tthis.dispatcher = new HTML_AJAX_Dispatcher(this.className,mode,callback,'{$this->serverUrl}','{$this->unserializer}');\n}\n";
        } else {
            $client .= "\tthis.dispatcher = new HTML_AJAX_Dispatcher(this.className,mode,callback,false,'{$this->unserializer}');\n}\n";
        }
        $client .= "{$this->_exportedInstances[$name]['exportedName']}.prototype  = {\n";
        $client .= "\tSync: function() { this.dispatcher.Sync(); }, \n";
        $client .= "\tAsync: function(callback) { this.dispatcher.Async(callback); },\n";
        foreach ($this->_exportedInstances[$name]['exportedMethods'] as $method) {
            $client .= $this->_generateMethodStub($method);
        }
        $client  = substr($client, 0, (strlen($client)-2))."\n";
        $client .= "}\n\n";

        if ($this->packJavaScript) {
                $client = $this->packJavaScript($client);
        }
        return $client;
    }

    /**
     * Returns a methods stub
     *
     * @param string $method the method name
     *
     * @return string the js code
     * @access private
     */    
    function _generateMethodStub($method)
    {
        $stub = "\t{$method}: function() { return ".
            "this.dispatcher.doCall('{$method}',arguments); },\n";
        return $stub;
    }

    /**
     * Populates the current payload
     *
     * @return string the js code
     * @access private
     */    
    function populatePayload()
    {
        if (isset($_REQUEST['Iframe_XHR'])) {
            $this->_iframe = $_REQUEST['Iframe_XHR_id'];
            if (isset($_REQUEST['Iframe_XHR_headers']) && 
                is_array($_REQUEST['Iframe_XHR_headers'])) {
                foreach ($_REQUEST['Iframe_XHR_headers'] as $header) {

                    $array    = explode(':', $header);
                    $array[0] = strip_tags(strtoupper(str_replace('-', '_', $array[0])));
                    //only content-length and content-type can go in without an 
                    //http_ prefix - security
                    if (strpos($array[0], 'HTTP_') !== 0
                          && strcmp('CONTENT_TYPE', $array[0])
                          && strcmp('CONTENT_LENGTH', $array[0])) {
                        $array[0] = 'HTTP_' . $array[0];
                    }
                    $_SERVER[$array[0]] = strip_tags($array[1]);
                }
            }
            $this->_payload = (isset($_REQUEST['Iframe_XHR_data']) 
                ? $_REQUEST['Iframe_XHR_data'] : '');

            if (isset($_REQUEST['Iframe_XHR_method'])) {
                $_GET['m'] = $_REQUEST['Iframe_XHR_method'];
            }
            if (isset($_REQUEST['Iframe_XHR_class'])) {
                $_GET['c'] = $_REQUEST['Iframe_XHR_class'];
            }
        }
    }

    /**
     * Handle a ajax request if needed
     *
     * The current check is if GET variables c (class) and m (method) are set, 
     * more options may be available in the future
     *
     * @return boolean true if an ajax call was handled, false otherwise
     */
    function handleRequest()
    {
        set_error_handler(array(&$this,'_errorHandler'));
        if (function_exists('set_exception_handler')) {
            set_exception_handler(array(&$this,'_exceptionHandler'));
        }
        if (isset($_GET['px'])) {
            if ($this->_iframeGrabProxy()) {
                restore_error_handler();
                if (function_exists('restore_exception_handler')) {
                    restore_exception_handler();
                }
                return true;
            }
        }
        
        $class       = strtolower($this->_getVar('c'));
        $method      = $this->_getVar('m');
        $phpCallback = $this->_getVar('cb');

        
        if (!empty($class) && !empty($method)) {
            if (!isset($this->_exportedInstances[$class])) {
                // handle error
                trigger_error('Unknown class: '. $class); 
            }
            if (!in_array(($this->php4CompatCase ? strtolower($method) : $method),
                $this->_exportedInstances[$class]['exportedMethods'])) {
                // handle error
                trigger_error('Unknown method: ' . $method);
            }
        } else if (!empty($phpCallback)) {
            if (strpos($phpCallback, '.') !== false) {
                $phpCallback = explode('.', $phpCallback);
            }
            if (!$this->_validatePhpCallback($phpCallback)) {
                restore_error_handler();
                if (function_exists('restore_exception_handler')) {
                    restore_exception_handler();
                }
                return false;
            }
        } else {
            restore_error_handler();
            if (function_exists('restore_exception_handler')) {
                restore_exception_handler();
            }
            return false;
        }

        // auto-detect serializer to use from content-type
        $type = $this->unserializer;
        $key  = array_search($this->_getClientPayloadContentType(),
            $this->contentTypeMap);
        if ($key) {
            $type = $key;
        }
        $unserializer = $this->_getSerializer($type);

        $args = $unserializer->unserialize($this->_getClientPayload(), $this->_allowedClasses);
        if (!is_array($args)) {
            $args = array($args);
        }

        if ($this->_interceptor !== false) {
            $args = $this->_processInterceptor($class, $method, $phpCallback, $args);
        }
        
        if (empty($phpCallback)) {
            $ret = call_user_func_array(array(&$this->_exportedInstances[$class]['instance'], $method), $args);
        } else {
            $ret = call_user_func_array($phpCallback, $args);
        }
        
        restore_error_handler();
        $this->_sendResponse($ret);
        return true;
    }

    /**
     * Determines the content type of the client payload
     *
     * @return string
     *   a MIME content type
     */
    function _getClientPayloadContentType()
    {
        //OPERA IS STUPID FIX
        if (isset($_SERVER['HTTP_X_CONTENT_TYPE'])) {
            $type = $this->_getServer('HTTP_X_CONTENT_TYPE');
            $pos  = strpos($type, ';');

            return strtolower($pos ? substr($type, 0, $pos) : $type);
        } else if (isset($_SERVER['CONTENT_TYPE'])) {
            $type = $this->_getServer('CONTENT_TYPE');
            $pos  = strpos($type, ';');

            return strtolower($pos ? substr($type, 0, $pos) : $type);
        }
        return 'text/plain';
    }

    /**
     * Send a reponse adding needed headers and serializing content
     *
     * Note: this method echo's output as well as setting headers to prevent caching
     * Iframe Detection: if this has been detected as an iframe response, it has to
     * be wrapped in different code and headers changed (quite a mess)
     *
     * @param mixed $response content to serialize and send
     *
     * @access private
     * @return void
     */
    function _sendResponse($response)
    {
        if (is_object($response) && is_a($response, 'HTML_AJAX_Response')) {
            $output  = $response->getPayload();
            $content = $response->getContentType();

        } elseif (is_a($response, 'PEAR_Error')) {
            $serializer = $this->_getSerializer('Error');
            $output     = $serializer->serialize(array(
                'message'  => $response->getMessage(),
                'userinfo' => $response->getUserInfo(),
                'code'     => $response->getCode(),
                'mode'     => $response->getMode()
                ));
            $content    = $this->contentTypeMap['Error'];

        } else {
            $serializer = $this->_getSerializer($this->serializer);
            $output     = $serializer->serialize($response);

            $serializerType = $this->serializer;
            // let a serializer change its output type
            if (isset($serializer->serializerNewType)) {
                $serializerType = $serializer->serializerNewType;
            }

            if (isset($this->contentTypeMap[$serializerType])) {
                $content = $this->contentTypeMap[$serializerType];
            }
        }
        // headers to force things not to be cached:
        $headers = array();
        //OPERA IS STUPID FIX
        if (isset($_SERVER['HTTP_X_CONTENT_TYPE'])) {
            $headers['X-Content-Type'] = $content;
            $content                   = 'text/plain';
        }

        if ($this->_sendContentLength()) {
            $headers['Content-Length'] = strlen($output);
        }

        $headers['Expires']       = 'Mon, 26 Jul 1997 05:00:00 GMT';
        $headers['Last-Modified'] = gmdate("D, d M Y H:i:s").'GMT';
        $headers['Cache-Control'] = 'no-cache, must-revalidate';
        $headers['Pragma']        = 'no-cache';
        $headers['Content-Type']  = $content.'; charset=utf-8';

        //intercept to wrap iframe return data
        if ($this->_iframe) {
            $output                  = $this->_iframeWrapper($this->_iframe, 
                                         $output, $headers);
            $headers['Content-Type'] = 'text/html; charset=utf-8';
        }

        $this->_sendHeaders($headers);
        echo $output;
    }

    /**
     * Decide if we should send a Content-length header
     *
     * @return   bool true if it's ok to send the header, false otherwise
     * @access   private
     */
    function _sendContentLength() 
    {
        if (!$this->sendContentLength) {
            return false;
        }
        $ini_tests = array( "output_handler",
                            "zlib.output_compression",
                            "zlib.output_handler");
        foreach ($ini_tests as $test) {
            if (ini_get($test)) {
                return false;
            }
        }
        return (ob_get_level() <= 0);
    }

    /**
     * Actually send a list of headers
     *
     * @param array $array list of headers to send
     *
     * @access private
     * @return void
     */
    function _sendHeaders($array)
    {
        foreach ($array as $header => $value) {
            header($header . ': ' . $value);
        }
    }

    /**
     * Get an instance of a serializer class
     *
     * @param string $type Last part of the class name
     *
     * @access private
     * @return HTML_AJAX_Serializer
     */
    function _getSerializer($type)
    {
        if (isset($this->_serializers[$type])) {
            return $this->_serializers[$type];
        }
    
        $class = 'HTML_AJAX_Serializer_'.$type;

        if ( (version_compare(phpversion(), 5, '>') && !class_exists($class, false)) 
            || (version_compare(phpversion(), 5, '<') && !class_exists($class)) ) {
            // include the class only if it isn't defined
            include_once "HTML/AJAX/Serializer/{$type}.php";
        }

        //handle JSON loose typing option for associative arrays
        if ($type == 'JSON') {
            $this->_serializers[$type] = new $class($this->jsonLooseType);
        } else {
            $this->_serializers[$type] = new $class();
        }
        return $this->_serializers[$type];
    }

    /**
     * Get payload in its submitted form, currently only supports raw post
     *
     * @access   private
     * @return   string   raw post data
     */
    function _getClientPayload()
    {
        if (empty($this->_payload)) {
            if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
                $this->_payload = $GLOBALS['HTTP_RAW_POST_DATA'];
            } else if (function_exists('file_get_contents')) {
                // both file_get_contents() and php://input require PHP >= 4.3.0
                $this->_payload = file_get_contents('php://input');
            } else {
                $this->_payload = '';
            }
        }
        return $this->_payload;
    }

    /**
     * stub for getting get vars - applies strip_tags
     *
     * @param string $var variable to get
     *
     * @access   private
     * @return   string   filtered _GET value
     */
    function _getVar($var)
    {
        if (!isset($_GET[$var])) {
            return null;
        } else {
            return strip_tags($_GET[$var]);
        }
    }

    /**
     * stub for getting server vars - applies strip_tags
     *
     * @param string $var variable to get
     *
     * @access   private
     * @return   string   filtered _GET value
     */
    function _getServer($var)
    {
        if (!isset($_SERVER[$var])) {
            return null;
        } else {
            return strip_tags($_SERVER[$var]);
        }
    }

    /**
     * Exception handler, passes them to _errorHandler to do the actual work
     *
     * @param Exception $ex Exception to be handled
     *
     * @access private
     * @return void
     */
    function _exceptionHandler($ex)
    {
        $this->_errorHandler($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
    }
      

    /**
     * Error handler that sends it errors to the client side
     *
     * @param int    $errno   Error number
     * @param string $errstr  Error string
     * @param string $errfile Error file
     * @param string $errline Error line
     *
     * @access private
     * @return void
     */
    function _errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno & error_reporting()) {
            $e          = new stdClass();
            $e->errNo   = $errno;
            $e->errStr  = $errstr;
            $e->errFile = $errfile;
            $e->errLine = $errline;


            $this->serializer = 'Error';
            $this->_sendResponse($e);
            if ($this->debugEnabled) {
                $this->debug = new HTML_AJAX_Debug($errstr, $errline, $errno, $errfile);
                if ($this->debugSession) {
                    $this->debug->sessionError();
                }
                $this->debug->_saveError();
            }
            die();
        }
    }

    /**
     * Creates html to wrap serialized info for iframe xmlhttprequest fakeout
     *
     * @param string $id      iframe instance id
     * @param string $data    data to pass
     * @param string $headers headers to pass
     *
     * @access private
     * @return string html page with iframe passing code
     */
    function _iframeWrapper($id, $data, $headers = array())
    {
        $string = '<html><script type="text/javascript">'."\n".
            'var Iframe_XHR_headers = new Object();';

        foreach ($headers as $label => $value) {
            $string .= 'Iframe_XHR_headers["'.preg_replace("/\r?\n/", "\\n", 
                addslashes($label)).'"] = "'.preg_replace("/\r?\n/", "\\n", 
                addslashes($value))."\";\n";
        }
        $string .= 'var Iframe_XHR_data = "' . preg_replace("/\r?\n/", "\\n", 
            addslashes($data)) . '";</script>'
            . '<body onload="parent.HTML_AJAX_IframeXHR_instances[\''.$id.'\']'
            . '.isLoaded(Iframe_XHR_headers, Iframe_XHR_data);"></body></html>';
        return $string;
    }

    /**
     * Handles a proxied grab request
     *
     * @return bool true to end the response, false to continue trying to handle it
     * @access private
     */
    function _iframeGrabProxy()
    {
        if (!isset($_REQUEST['Iframe_XHR_id'])) {
            trigger_error('Invalid iframe ID');
            return false;
        }
        $this->_iframe  = $_REQUEST['Iframe_XHR_id'];
        $this->_payload = (isset($_REQUEST['Iframe_XHR_data']) ? $_REQUEST['Iframe_XHR_data'] : '');
        $url            = urldecode($_GET['px']);
        $url_parts      = parse_url($url);
        $urlregex       = '#^https?://#i';

        if (!preg_match($urlregex, $url) || $url_parts['host'] != $_SERVER['HTTP_HOST']) {
            trigger_error('Invalid URL for grab proxy');
            return true;
        }
        $method = (isset($_REQUEST['Iframe_XHR_HTTP_method'])
            ? strtoupper($_REQUEST['Iframe_XHR_HTTP_method'])
            : 'GET');
        // validate method
        if ($method != 'GET' && $method != 'POST') {
            trigger_error('Invalid grab URL');
            return true;
        }
        // validate headers
        $headers = '';
        if (isset($_REQUEST['Iframe_XHR_headers'])) {
            foreach ($_REQUEST['Iframe_XHR_headers'] as $header) {
                if (strpos($header, "\r") !== false
                        || strpos($header, "\n") !== false) {
                    trigger_error('Invalid grab header');
                    return true;
                }
                $headers .= $header . "\r\n";
            }
        }
        // tries to make request with file_get_contents()
        if (ini_get('allow_url_fopen') && version_compare(phpversion(), '5.0.0'. '>=')) {
            $opts = array(
                $url_parts['scheme'] => array(
                    'method'  => $method,
                    'headers' => $headers,
                    'content' => $this->_payload
                )
            );
            $ret  = @file_get_contents($url, false, stream_context_create($opts));
            if (!empty($ret)) {
                $this->_sendResponse($ret);
                return true;
            }
        }
        // tries to make request using the curl extension
        if (function_exists('curl_setopt')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $ret = curl_exec($ch);
            if ($ret !== false) {
                curl_close($ch);
                $this->_sendResponse($ret);
                return true;
            }
        }
        if (isset($url_parts['port'])) {
            $port = $url_parts['port'];
        } else { 
            $port = getservbyname(strtolower($url_parts['scheme']), 'tcp');
            if ($port === false) {
                trigger_error('Grab proxy: Unknown port or service, defaulting to 80', E_USER_WARNING);
                $port = 80;
            }
        }
        if (!isset($url_parts['path'])) {
            $url_parts['path'] = '/';
        }
        if (!empty($url_parts['query'])) {
            $url_parts['path'] .= '?' . $url_parts['query'];
        }
        $request = "$method {$url_parts['path']} HTTP/1.0\r\n"
            . "Host: {$url['host']}\r\n"
            . "Connection: close\r\n"
            . "$headers\r\n";
        // tries to make request using the socket functions
        $fp = fsockopen($_SERVER['HTTP_HOST'], $port, $errno, $errstr, 4);
        if ($fp) {
            fputs($fp, $request);

            $ret          = '';
            $done_headers = false;

            while (!feof($fp)) {
                $ret .= fgets($fp, 2048);
                if ($done_headers || ($contentpos = strpos($ret, "\r\n\r\n")) === false) {
                    continue;
                }
                $done_headers = true;
                $ret          = substr($ret, $contentpos + 4);
            }
            fclose($fp);
            $this->_sendResponse($ret);
            return true;
        }
        // tries to make the request using the socket extension
        $host = gethostbyname($url['host']);
        if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0
            || ($connected = socket_connect($socket, $host, $port)) < 0
            || ($written = socket_write($socket, $request)) < strlen($request)) {
             trigger_error('Grab proxy failed: ' . socket_strerror($socket));
             return true;
        }

        $ret          = '';
        $done_headers = false;

        while ($out = socket_read($socket, 2048)) {
            $ret .= $out;
            if ($done_headers || ($contentpos = strpos($ret, "\r\n\r\n")) === false) {
                continue;
            }
            $done_headers = true;
            $ret          = substr($ret, $contentpos + 4);
        }
        socket_close($socket);
        $this->_sendResponse($ret);
        return true;
    }

    /**
     * Add a class or classes to those allowed to be unserialized
     *
     * @param mixed $classes the class or array of classes to add
     *
     * @access public
     * @return void
     */
    function addAllowedClasses($classes)
    {
        if (!is_array($classes)) {
            $this->_allowedClasses[] = $classes;
        } else {
            $this->_allowedClasses = array_merge($this->_allowedClasses, $classes);
        }
        $this->_allowedClasses = array_unique($this->_allowedClasses);
    }
    
    /**
     * Checks that the given callback is callable and allowed to be called
     *
     * @param callback $callback the callback to check
     *
     * @return bool true if the callback is valid, false otherwise
     * @access private
     */
    function _validatePhpCallback($callback)
    {
        if (!is_callable($callback)) {
            return false;
        }
        $sig = md5(serialize($callback));
        return isset($this->_validCallbacks[$sig]);
    }
    
    /**
     * Register a callback so it may be called from JS
     * 
     * @param callback $callback the callback to register
     *
     * @access public
     * @return void
     */
    function registerPhpCallback($callback)
    {
        $this->_validCallbacks[md5(serialize($callback))] = 1;
    }

    /**
     * Make JavaScript code smaller
     *
     * Currently just strips whitespace and comments, needs to remain fast
     * Strips comments only if they are not preceeded by code
     * Strips /*-style comments only if they span over more than one line
     * Since strings cannot span over multiple lines, it cannot be defeated by a 
     * string containing /*
     *
     * @param string $input Javascript to pack
     *
     * @access public
     * @return string packed javascript
     */
    function packJavaScript($input) 
    {
        $stripPregs    = array(
            '/^\s*$/',
            '/^\s*\/\/.*$/'
        );
        $blockStart    = '/^\s*\/\/\*/';
        $blockEnd      = '/\*\/\s*(.*)$/';
        $inlineComment = '/\/\*.*\*\//';
        $out           = '';

        $lines   = explode("\n", $input);
        $inblock = false;
        foreach ($lines as $line) {
            $keep = true;
            if ($inblock) {
                if (preg_match($blockEnd, $line)) {
                    $inblock = false;
                    $line    = preg_match($blockEnd, '$1', $line);
                    $keep    = strlen($line) > 0;
                }
            } elseif (preg_match($inlineComment, $line)) {
                $keep = true;
            } elseif (preg_match($blockStart, $line)) {
                $inblock = true;
                $keep    = false;
            }

            if (!$inblock) {
                foreach ($stripPregs as $preg) {
                    if (preg_match($preg, $line)) {
                        $keep = false;
                        break;
                    }
                }
            }

            if ($keep && !$inblock) {
                $out .= trim($line)."\n";
            }
            /* Enable to see what your striping out
            else {
                echo $line."<br>";
            }//*/
        }
        $out .= "\n";
        return $out;
    }

    /**
     * Set an interceptor class
     *
     * An interceptor class runs during the process of handling a request, 
     * it allows you to run security checks globally. It also allows you to 
     * rewrite parameters
     *
     * You can throw errors and exceptions in your intercptor methods and 
     * they will be passed to javascript
     * 
     * You can add interceptors are 3 levels
     * For a particular class/method, this is done by add a method to you class 
     *   named ClassName_MethodName($params)
     * For a particular class, method ClassName($methodName,$params)
     * Globally, method intercept($className,$methodName,$params)
     * 
     * Only one match is done, using the most specific interceptor
     *
     * All methods have to return $params, if you want to empty all of the 
     * parameters return an empty array
     *
     * @param Object $instance an instance of you interceptor class
     *
     * @todo handle php callbacks
     * @access public
     * @return void
     */
    function setInterceptor($instance) 
    {
        $this->_interceptor = $instance;
    }

    /**
     * Attempt to intercept a call
     *
     * @param string $className  Class Name
     * @param string $methodName Method Name
     * @param string $callback   Not implemented
     * @param array  $params     Array of parameters to pass to the interceptor
     *
     * @todo handle php callbacks
     * @access private
     * @return array Updated params
     */
    function _processInterceptor($className,$methodName,$callback,$params) 
    {

        $m = $className.'_'.$methodName;
        if (method_exists($this->_interceptor, $m)) {
            return $this->_interceptor->$m($params);
        }

        $m = $className;
        if (method_exists($this->_interceptor, $m)) {
            return $this->_interceptor->$m($methodName, $params);
        }

        $m = 'intercept';
        if (method_exists($this->_interceptor, $m)) {
            return $this->_interceptor->$m($className, $methodName, $params);
        }

        return $params;
    }
}

/**
 * PHP 4 compat function for interface/class exists
 *
 * @param string $class    Class name
 * @param bool   $autoload Should the autoloader be called
 *
 * @access public
 * @return bool
 */
function HTML_AJAX_Class_exists($class, $autoload) 
{
    if (function_exists('interface_exists')) {
        return class_exists($class, $autoload);
    } else {
        return class_exists($class);
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
