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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Gdata_Feed
 */
require_once 'Zend/Gdata/Feed.php';

/**
 * Zend_Gdata_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Zend_Version
 */
require_once 'Zend/Version.php';

/**
 * Zend_Gdata_App_MediaSource
 */
require_once 'Zend/Gdata/App/MediaSource.php';

/**
 * Provides Atom Publishing Protocol (APP) functionality.  This class and all
 * other components of Zend_Gdata_App are designed to work independently from
 * other Zend_Gdata components in order to interact with generic APP services.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App
{

    /**
     * Client object used to communicate
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient;

    /**
     * Client object used to communicate in static context
     *
     * @var Zend_Http_Client
     */
    protected static $_staticHttpClient = null;

    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var boolean
     */
    protected static $_httpMethodOverride = false;

    /**
     * Enable gzipped responses?
     *
     * @var boolean
     */
    protected static $_gzipEnabled = false;

    /**
     * Use verbose exception messages.  In the case of HTTP errors,
     * use the body of the HTTP response in the exception message.
     *
     * @var boolean
     */
    protected static $_verboseExceptionMessages = true;

    /**
     * Default URI to which to POST.
     *
     * @var string
     */
    protected $_defaultPostUri = null;

    /**
     * Packages to search for classes when using magic __call method, in order.
     *
     * @var array
     */
    protected $_registeredPackages = array(
            'Zend_Gdata_App_Extension',
            'Zend_Gdata_App');

    /**
     * Maximum number of redirects to follow during HTTP operations
     *
     * @var int
     */
    protected static $_maxRedirects = 5;

    /**
     * Create Gdata object
     *
     * @param Zend_Http_Client $client
     * @param string $applicationId
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->setHttpClient($client, $applicationId);
    }

    /**
     * Adds a Zend Framework package to the $_registeredPackages array.
     * This array is searched when using the magic __call method below
     * to instantiante new objects.
     *
     * @param string $name The name of the package (eg Zend_Gdata_App)
     * @return void
     */
    public function registerPackage($name)
    {
        array_unshift($this->_registeredPackages, $name);
    }

    /**
     * Retreive feed object
     *
     * @param string $uri The uri from which to retrieve the feed
     * @param string $className The class which is used as the return type
     * @return Zend_Gdata_App_Feed
     */
    public function getFeed($uri, $className='Zend_Gdata_App_Feed')
    {
        return $this->importUrl($uri, $className);
    }

    /**
     * Retreive entry object
     *
     * @param string $uri
     * @param string $className The class which is used as the return type
     * @return Zend_Gdata_App_Entry
     */
    public function getEntry($uri, $className='Zend_Gdata_App_Entry')
    {
        return $this->importUrl($uri, $className);
    }

    /**
     * Get the Zend_Http_Client object used for communication
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    /**
     * Set the Zend_Http_Client object used for communication
     *
     * @param Zend_Http_Client $client The client to use for communication
     * @throws Zend_Gdata_App_HttpException
     * @return Zend_Gdata_App Provides a fluent interface
     */
    public function setHttpClient($client, $applicationId = 'MyCompany-MyApp-1.0')
    {
        if ($client === null) {
            $client = new Zend_Http_Client();
        }
        if (!$client instanceof Zend_Http_Client) {
            require_once 'Zend/Gdata/App/HttpException.php';
            throw new Zend_Gdata_App_HttpException('Argument is not an instance of Zend_Http_Client.');
        }
        $userAgent = $applicationId . ' Zend_Framework_Gdata/' . Zend_Version::VERSION;
        $client->setHeaders('User-Agent', $userAgent);
        $client->setConfig(array(
            'strictredirects' => true
            )
        );
        $this->_httpClient = $client;
        Zend_Gdata::setStaticHttpClient($client);
        return $this;
    }


    /**
     * Set the static HTTP client instance
     *
     * Sets the static HTTP client object to use for retrieving the feed.
     *
     * @param  Zend_Http_Client $httpClient
     * @return void
     */
    public static function setStaticHttpClient(Zend_Http_Client $httpClient)
    {
        self::$_staticHttpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object. If none is set, a new Zend_Http_Client will be used.
     *
     * @return Zend_Http_Client
     */
    public static function getStaticHttpClient()
    {
        if (!self::$_staticHttpClient instanceof Zend_Http_Client) {
            $client = new Zend_Http_Client();
            $userAgent = 'Zend_Framework_Gdata/' . Zend_Version::VERSION;
            $client->setHeaders('User-Agent', $userAgent);
            $client->setConfig(array(
                'strictredirects' => true
                )
            );
            self::$_staticHttpClient = $client;
        }
        return self::$_staticHttpClient;
    }

    /**
     * Toggle using POST instead of PUT and DELETE HTTP methods
     *
     * Some feed implementations do not accept PUT and DELETE HTTP
     * methods, or they can't be used because of proxies or other
     * measures. This allows turning on using POST where PUT and
     * DELETE would normally be used; in addition, an
     * X-Method-Override header will be sent with a value of PUT or
     * DELETE as appropriate.
     *
     * @param  boolean $override Whether to override PUT and DELETE with POST.
     * @return void
     */
    public static function setHttpMethodOverride($override = true)
    {
        self::$_httpMethodOverride = $override;
    }

    /**
     * Get the HTTP override state
     *
     * @return boolean
     */
    public static function getHttpMethodOverride()
    {
        return self::$_httpMethodOverride;
    }

    /**
     * Toggle requesting gzip encoded responses
     *
     * @param  boolean $enabled Whether or not to enable gzipped responses
     * @return void
     */
    public static function setGzipEnabled($enabled = false)
    {
        if ($enabled && !function_exists('gzinflate')) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You cannot enable gzipped responses if the zlib module ' .
                    'is not enabled in your PHP installation.');
        
        }
        self::$_gzipEnabled = $enabled;
    }

    /**
     * Get the HTTP override state
     *
     * @return boolean
     */
    public static function getGzipEnabled()
    {
        return self::$_gzipEnabled;
    }

    /**
     * Get whether to use verbose exception messages
     *
     * In the case of HTTP errors,  use the body of the HTTP response 
     * in the exception message.
     *
     * @return boolean
     */
    public static function getVerboseExceptionMessages()
    {
        return self::$_verboseExceptionMessages;
    }

    /**
     * Set whether to use verbose exception messages
     *
     * In the case of HTTP errors, use the body of the HTTP response 
     * in the exception message.
     *
     * @param boolean $verbose Whether to use verbose exception messages
     */
    public static function setVerboseExceptionMessages($verbose)
    {
        self::$_verboseExceptionMessages = $verbose;
    }

    /**
     * Set the maximum number of redirects to follow during HTTP operations
     *
     * @param int $maxRedirects Maximum number of redirects to follow
     * @return void
     */
    public static function setMaxRedirects($maxRedirects)
    {
        self::$_maxRedirects = $maxRedirects;
    }

    /**
     * Get the maximum number of redirects to follow during HTTP operations
     *
     * @return int Maximum number of redirects to follow
     */
    public static function getMaxRedirects()
    {
        return self::$_maxRedirects;
    }

    /**
     * Provides pre-processing for HTTP requests to APP services.  
     *
     * 1. Checks the $data element and, if it's an entry, extracts the XML, 
     *    multipart data, edit link (PUT,DELETE), etc.
     * 2. If $data is a string, sets the default content-type  header as 
     *    'application/atom+xml' if it's not already been set.
     * 3. Adds a x-http-method override header and changes the HTTP method 
     *    to 'POST' if necessary as per getHttpMethodOverride()
     *
     * @param string $method The HTTP method for the request - 'GET', 'POST', 
     *                       'PUT', 'DELETE'
     * @param string $url The URL to which this request is being performed, 
     *                    or null if found in $data
     * @param array $headers An associative array of HTTP headers for this 
     *                       request
     * @param mixed $data The Zend_Gdata_App_Entry or XML for the  
     *                    body of the request
     * @param string $contentTypeOverride The override value for the 
     *                                    content type of the request body
     * @return array An associative array containing the determined 
     *               'method', 'url', 'data', 'headers', 'contentType'
     */
    public function prepareRequest($method, $url = null, $headers = array(), $data = null, $contentTypeOverride = null)
    {
        $rawData = null;
        $finalContentType = null;
        if ($url == null) {
            $url = $this->_defaultPostUri;
        }

        if (is_string($data)) {
            $rawData = $data;
            if ($contentTypeOverride === null) {
                $finalContentType = 'application/atom+xml';
            }
        } elseif ($data instanceof Zend_Gdata_App_MediaEntry) {
            $rawData = $data->encode();
            if ($data->getMediaSource() !== null) {
                $finalContentType = 'multipart/related; boundary="' . $data->getBoundary() . '"';
                $headers['MIME-version'] = '1.0'; 
                $headers['Slug'] = $data->getMediaSource()->getSlug();
            } else {
                $finalContentType = 'application/atom+xml';
            }
            if ($method == 'PUT' || $method == 'DELETE') {
                $editLink = $data->getEditLink();
                if ($editLink != null) {
                    $url = $editLink->getHref();
                }
            }
        } elseif ($data instanceof Zend_Gdata_App_Entry) {
            $rawData = $data->saveXML();
            $finalContentType = 'application/atom+xml';
            if ($method == 'PUT' || $method == 'DELETE') {
                $editLink = $data->getEditLink();
                if ($editLink != null) {
                    $url = $editLink->getHref();
                }
            }
        } elseif ($data instanceof Zend_Gdata_App_MediaSource) {
            $rawData = $data->encode();
            if ($data->getSlug() !== null) {
                $headers['Slug'] = $data->getSlug();
            }
            $finalContentType = $data->getContentType();
        }
        if ($method == 'DELETE') {
            $rawData = null;
        }
        if ($method != 'POST' && $method != 'GET' && Zend_Gdata_App::getHttpMethodOverride()) {
            $headers['x-http-method-override'] = $method;
            $method = 'POST';
        } else {
            $headers['x-http-method-override'] = null;
        }

        if ($contentTypeOverride != null) {
            $finalContentType = $contentTypeOverride;
        }

        return array('method' => $method, 'url' => $url, 'data' => $rawData, 'headers' => $headers, 'contentType' => $finalContentType);
    }

    /**
     * Performs a HTTP request using the specified method
     *
     * @param string $method The HTTP method for the request - 'GET', 'POST', 
     *                       'PUT', 'DELETE'
     * @param string $url The URL to which this request is being performed
     * @param array $headers An associative array of HTTP headers 
     *                       for this request
     * @param string $body The body of the HTTP request
     * @param string $contentType The value for the content type 
     *                                of the request body
     * @param int $remainingRedirects Number of redirects to follow if request
     *                              s results in one
     * @return Zend_Http_Response The response object
     */
    public function performHttpRequest($method, $url, $headers = null, $body = null, $contentType = null, $remainingRedirects = null)
    {
        require_once 'Zend/Http/Client/Exception.php';
        if ($remainingRedirects === null) {
            $remainingRedirects = self::getMaxRedirects();
        }
        if ($headers === null) {
            $headers = array();
        }
        // check the overridden method
        if (($method == 'POST' || $method == 'PUT') && $body === null && $headers['x-http-method-override'] != 'DELETE') {
                require_once 'Zend/Gdata/App/InvalidArgumentException.php';
                throw new Zend_Gdata_App_InvalidArgumentException(
                        'You must specify the data to post as either a ' . 
                        'string or a child of Zend_Gdata_App_Entry');
        }
        if ($url === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('You must specify an URI to which to post.');
        }
        $headers['Content-Type'] = $contentType;
        if (Zend_Gdata_App::getGzipEnabled()) {
            // some services require the word 'gzip' to be in the user-agent header
            // in addition to the accept-encoding header
            if (strpos($this->_httpClient->getHeader('User-Agent'), 'gzip') === false) {
                $headers['User-Agent'] = $this->_httpClient->getHeader('User-Agent') . ' (gzip)';
            }
            $headers['Accept-encoding'] = 'gzip, deflate';
        } else {
            $headers['Accept-encoding'] = 'identity';
        }

        // Make sure the HTTP client object is 'clean' before making a request
        // In addition to standard headers to reset via resetParameters(), 
        // also reset the Slug header
        $this->_httpClient->resetParameters();
        $this->_httpClient->setHeaders('Slug', null);

        // Set the params for the new request to be performed
        $this->_httpClient->setHeaders($headers);
        $this->_httpClient->setUri($url);
        $this->_httpClient->setConfig(array('maxredirects' => 0));
        $this->_httpClient->setRawData($body, $contentType);
        try {
            $response = $this->_httpClient->request($method);
        } catch (Zend_Http_Client_Exception $e) {
            require_once 'Zend/Gdata/App/HttpException.php';
            throw new Zend_Gdata_App_HttpException($e->getMessage(), $e);
        }
        if ($response->isRedirect()) {
            if ($remainingRedirects > 0) {
                $newUrl = $response->getHeader('Location');
                $response = $this->performHttpRequest($method, $newUrl, $headers, $body, $contentType, $remainingRedirects);
            } else {
                require_once 'Zend/Gdata/App/HttpException.php';
                throw new Zend_Gdata_App_HttpException(
                        'Number of redirects exceeds maximum', null, $response);
            }
        }
        if (!$response->isSuccessful()) {
            require_once 'Zend/Gdata/App/HttpException.php';
            $exceptionMessage = 'Expected response code 200, got ' . $response->getStatus();
            if (self::getVerboseExceptionMessages()) {
                $exceptionMessage .= "\n" . $response->getBody();
            }
            $exception = new Zend_Gdata_App_HttpException($exceptionMessage);
            $exception->setResponse($response);
            throw $exception;
        }
        return $response;
    }

    /**
     * Imports a feed located at $uri.
     *
     * @param  string $uri
     * @param  Zend_Http_Client $client The client used for communication
     * @param  string $className The class which is used as the return type
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_App_Feed
     */
    public static function import($uri, $client = null, $className='Zend_Gdata_App_Feed')
    {
        $app = new Zend_Gdata_App($client);
        $requestData = $app->prepareRequest('GET', $uri);
        $response = $app->performHttpRequest($requestData['method'], $requestData['url']);

        $feedContent = $response->getBody();
        $feed = self::importString($feedContent, $className);
        if ($client != null) {
            $feed->setHttpClient($client);
        }
        return $feed;
    }

    /**
     * Imports the specified URL (non-statically).
     *
     * @param  string $url The URL to import
     * @param  string $className The class which is used as the return type
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_App_Feed
     */
    public function importUrl($url, $className='Zend_Gdata_App_Feed')
    {
        $response = $this->get($url);
        
        $feedContent = $response->getBody();
        $feed = self::importString($feedContent, $className);
        if ($this->getHttpClient() != null) {
            $feed->setHttpClient($this->getHttpClient());
        }   
        return $feed;
    }   


    /**
     * Imports a feed represented by $string.
     *
     * @param  string $string
     * @param  string $className The class which is used as the return type
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_App_Feed
     */
    public static function importString($string, $className='Zend_Gdata_App_Feed')
    {
        // Load the feed as an XML DOMDocument object
        @ini_set('track_errors', 1);
        $doc = new DOMDocument();
        $success = @$doc->loadXML($string);
        @ini_restore('track_errors');

        if (!$success) {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception("DOMDocument cannot parse XML: $php_errormsg");
        }
        $feed = new $className($string);
        $feed->setHttpClient(self::getstaticHttpClient());
        return $feed;
    }


    /**
     * Imports a feed from a file located at $filename.
     *
     * @param  string $filename
     * @param  string $className The class which is used as the return type
     * @param  string $useIncludePath Whether the include_path should be searched
     * @throws Zend_Gdata_App_Exception
     * @return Zend_Gdata_Feed
     */
    public static function importFile($filename,
            $className='Zend_Gdata_App_Feed', $useIncludePath = false)
    {
        @ini_set('track_errors', 1);
        $feed = @file_get_contents($filename, $useIncludePath);
        @ini_restore('track_errors');
        if ($feed === false) {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception("File could not be loaded: $php_errormsg");
        }
        return self::importString($feed, $className);
    }

    /**
     * GET a uri using client object
     *
     * @param  string $uri
     * @throws Zend_Gdata_App_HttpException
     * @return Zend_Http_Response
     */
    public function get($uri)
    {
        $requestData = $this->prepareRequest('GET', $uri);
        return $this->performHttpRequest($requestData['method'], $requestData['url']);
    }

    /**
     * POST data with client object
     *
     * @param mixed $data The Zend_Gdata_App_Entry or XML to post
     * @param string $uri POST URI
     * @param array $headers Additional HTTP headers to insert.
     * @param string $contentType Content-type of the data
     * @param array $extraHaders Extra headers to add to the request
     * @return Zend_Http_Response
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_App_InvalidArgumentException
     */
    public function post($data, $uri = null, $remainingRedirects = null,
            $contentType = null, $extraHeaders = null)
    {
        $requestData = $this->prepareRequest('POST', $uri, $extraHeaders, 
                                             $data, $contentType);
        return $this->performHttpRequest(
                $requestData['method'], $requestData['url'], 
                $requestData['headers'], $requestData['data'], 
                $requestData['contentType']);
    }

    /**
     * PUT data with client object
     *
     * @param mixed $data The Zend_Gdata_App_Entry or XML to post
     * @param string $uri PUT URI
     * @param array $headers Additional HTTP headers to insert.
     * @param string $contentType Content-type of the data
     * @param array $extraHaders Extra headers to add to the request
     * @return Zend_Http_Response
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_App_InvalidArgumentException
     */
    public function put($data, $uri = null, $remainingRedirects = null,
            $contentType = null, $extraHeaders = null)
    {
        $requestData = $this->prepareRequest('PUT', $uri, $extraHeaders, $data, $contentType);
        return $this->performHttpRequest(
                $requestData['method'], $requestData['url'], 
                $requestData['headers'], $requestData['data'], 
                $requestData['contentType']);
    }

    /**
     * DELETE entry with client object
     *
     * @param mixed $data The Zend_Gdata_App_Entry or URL to delete
     * @return void
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_App_InvalidArgumentException
     */
    public function delete($data, $remainingRedirects = null)
    {
        if (is_string($data)) {
            $requestData = $this->prepareRequest('DELETE', $data);
        } else {
            $requestData = $this->prepareRequest('DELETE', null, null, $data); 
        }
        return $this->performHttpRequest($requestData['method'], $requestData['url'], 
                                         $requestData['headers'], '', $requestData['contentType'], 
                                         $remainingRedirects);
    }

    /**
     * Inserts an entry to a given URI and returns the response as a fully formed Entry.
     * @param mixed  $data The Zend_Gdata_App_Entry or XML to post
     * @param string $uri POST URI
     * @param string $className The class of entry to be returned.
     * @return Zend_Gdata_App_Entry The entry returned by the service after insertion.
     */
    public function insertEntry($data, $uri, $className='Zend_Gdata_App_Entry')
    {
        $response = $this->post($data, $uri);

        $returnEntry = new $className($response->getBody());
        $returnEntry->setHttpClient(self::getstaticHttpClient());
        return $returnEntry;
    }

    /**
     * Update an entry
     *
     * @param mixed $data Zend_Gdata_App_Entry or XML (w/ID and link rel='edit')
     * @return Zend_Gdata_App_Entry The entry returned from the server
     * @throws Zend_Gdata_App_Exception
     */
    public function updateEntry($data, $uri = null, $className = null)
    {
        if ($className === null && $data instanceof Zend_Gdata_App_Entry) {
            $className = get_class($data);
        } elseif ($className === null) {
            $className = 'Zend_Gdata_App_Entry';
        }
        
        $response = $this->put($data, $uri);
        $returnEntry = new $className($response->getBody());
        $returnEntry->setHttpClient(self::getstaticHttpClient());
        return $returnEntry;
    }

    /**
     * Provides a magic factory method to instantiate new objects with
     * shorter syntax than would otherwise be required by the Zend Framework
     * naming conventions.  For instance, to construct a new
     * Zend_Gdata_Calendar_Extension_Color, a developer simply needs to do
     * $gCal->newColor().  For this magic constructor, packages are searched
     * in the same order as which they appear in the $_registeredPackages
     * array
     *
     * @param string $method The method name being called
     * @param array $args The arguments passed to the call
     * @throws Zend_Gdata_App_Exception
     */
    public function __call($method, $args)
    {
        if (preg_match('/^new(\w+)/', $method, $matches)) {
            $class = $matches[1];
            $foundClassName = null;
            foreach ($this->_registeredPackages as $name) {
                 try {
                     @Zend_Loader::loadClass("${name}_${class}");
                     $foundClassName = "${name}_${class}";
                     break;
                 } catch (Zend_Exception $e) {
                     // package wasn't here- continue searching
                 }
            }
            if ($foundClassName != null) {
                $reflectionObj = new ReflectionClass($foundClassName);
                return $reflectionObj->newInstanceArgs($args);
            } else {
                require_once 'Zend/Gdata/App/Exception.php';
                throw new Zend_Gdata_App_Exception(
                        "Unable to find '${class}' in registered packages");
            }
        } else {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception("No such method ${method}");
        }
    }

    /**
     * Retrieve all entries for a feed, iterating through pages as necessary.
     * Be aware that calling this function on a large dataset will take a 
     * significant amount of time to complete. In some cases this may cause 
     * execution to timeout without proper precautions in place.
     *
     * @param $feed The feed to iterate through.
     * @return mixed A new feed of the same type as the one originally 
     *          passed in, containing all relevent entries.
     */
    public function retrieveAllEntriesForFeed($feed) {
        $feedClass = get_class($feed);
        $reflectionObj = new ReflectionClass($feedClass);
        $result = $reflectionObj->newInstance();
        do {
            foreach ($feed as $entry) {
                $result->addEntry($entry);
            }
            
            $next = $feed->getLink('next');
            if ($next !== null) {
                $feed = $this->getFeed($next->href, $feedClass);
            } else {
                $feed = null;
            }
        }
        while ($feed != null);
        return $result;
    }

    /**
     * This method enables logging of requests by changing the
     * Zend_Http_Client_Adapter used for performing the requests.
     * NOTE: This will not work if you have customized the adapter
     * already to use a proxy server or other interface.
     * 
     * @param $logfile The logfile to use when logging the requests
     */
    public function enableRequestDebugLogging($logfile) 
    {
        $this->_httpClient->setConfig(array(
            'adapter' => 'Zend_Gdata_App_LoggingHttpClientAdapterSocket',
            'logfile' => $logfile
            ));
    }
}
