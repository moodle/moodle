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
 * Zend_Gdata_App
 */
require_once 'Zend/Gdata/App.php';

/**
 * Provides functionality to interact with Google data APIs
 * Subclasses exist to implement service-specific features
 *
 * As the Google data API protocol is based upon the Atom Publishing Protocol
 * (APP), GData functionality extends the appropriate Zend_Gdata_App classes
 *
 * @link http://code.google.com/apis/gdata/overview.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata extends Zend_Gdata_App
{

    /**
     * Service name for use with Google's authentication mechanisms
     *
     * @var string
     */
    const AUTH_SERVICE_NAME = 'xapi';

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
            'Zend_Gdata_Kind',
            'Zend_Gdata_Extension',
            'Zend_Gdata',
            'Zend_Gdata_App_Extension',
            'Zend_Gdata_App');

    /**
     * Namespaces used for GData data
     *
     * @var array
     */
    public static $namespaces = array(
        'openSearch' => 'http://a9.com/-/spec/opensearchrss/1.0/',
        'rss' => 'http://blogs.law.harvard.edu/tech/rss',
        'gd' => 'http://schemas.google.com/g/2005');

    /**
     * Client object used to communicate
     *
     * @var Zend_Gdata_HttpClient
     */
    protected $_httpClient;

    /**
     * Client object used to communicate in static context
     *
     * @var Zend_Gdata_HttpClient
     */
    protected static $_staticHttpClient = null;

    /**
     * Create Gdata object
     *
     * @param Zend_Http_Client $client
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        parent::__construct($client, $applicationId);
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
    public static function import($uri, $client = null, $className='Zend_Gdata_Feed')
    {
        $app = new Zend_Gdata($client);
        $requestData = $app->decodeRequest('GET', $uri);
        $response = $app->performHttpRequest($requestData['method'], $requestData['url']);

        $feedContent = $response->getBody();
        $feed = self::importString($feedContent, $className);
        if ($client != null) {
            $feed->setHttpClient($client);
        }
        return $feed;
    }

    /**
     * Retreive feed object
     *
     * @param mixed $location The location as string or Zend_Gdata_Query
     * @param string $className The class type to use for returning the feed
     * @throws Zend_Gdata_App_InvalidArgumentException
     * @return Zend_Gdata_Feed
     */
    public function getFeed($location, $className='Zend_Gdata_Feed')
    {
        if (is_string($location)) {
            $uri = $location;
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the location as either a string URI ' .
                    'or a child of Zend_Gdata_Query');
        }
        return parent::getFeed($uri, $className);
    }

    /**
     * Retreive entry object
     *
     * @param mixed $location The location as string or Zend_Gdata_Query
     * @return Zend_Gdata_Feed
     */
    public function getEntry($location, $className='Zend_Gdata_Entry')
    {
        if (is_string($location)) {
            $uri = $location;
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the location as either a string URI ' .
                    'or a child of Zend_Gdata_Query');
        }
        return parent::getEntry($uri, $className);
    }

    /**
     * Performs a HTTP request using the specified method.
     * 
     * Overrides the definition in the parent (Zend_Gdata_App)
     * and uses the Zend_Gdata_HttpClient functionality
     * to filter the HTTP requests and responses.
     *  
     * @param string $method The HTTP method for the request -
     *                       'GET', 'POST', 'PUT', 'DELETE'
     * @param string $url The URL to which this request is being performed,
     *                    or null if found in $data
     * @param array $headers An associative array of HTTP headers
     *                       for this request
     * @param string $body The body of the HTTP request
     * @param string $contentType The value for the content type of the
     *                            request body
     * @param int $remainingRedirects Number of redirects to follow
     *                                if requests results in one
     * @return Zend_Http_Response The response object
     */
    public function performHttpRequest($method, $url, $headers = array(), $body = null, $contentType = null, $remainingRedirects = null)
    {
        if ($this->_httpClient instanceof Zend_Gdata_HttpClient) {
            $filterResult = $this->_httpClient->filterHttpRequest($method, $url, $headers, $body, $contentType);
            $method = $filterResult['method'];
            $url = $filterResult['url'];
            $body = $filterResult['body'];
            $headers = $filterResult['headers'];
            $contentType = $filterResult['contentType'];
            return $this->_httpClient->filterHttpResponse(parent::performHttpRequest($method, $url, $headers, $body, $contentType, $remainingRedirects));
        } else {
            return parent::performHttpRequest($method, $url, $headers, $body, $contentType, $remainingRedirects);
        }
    }

}
