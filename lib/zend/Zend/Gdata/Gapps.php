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
 * @subpackage Gapps
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Gdata
 */
require_once 'Zend/Gdata.php';

/**
 * @see Zend_Gdata_Gapps_UserFeed
 */
require_once 'Zend/Gdata/Gapps/UserFeed.php';

/**
 * @see Zend_Gdata_Gapps_NicknameFeed
 */
require_once 'Zend/Gdata/Gapps/NicknameFeed.php';

/**
 * @see Zend_Gdata_Gapps_EmailListFeed
 */
require_once 'Zend/Gdata/Gapps/EmailListFeed.php';

/**
 * @see Zend_Gdata_Gapps_EmailListRecipientFeed
 */
require_once 'Zend/Gdata/Gapps/EmailListRecipientFeed.php';


/**
 * Service class for interacting with the Google Apps Provisioning API.
 *
 * Like other service classes in this module, this class provides access via
 * an HTTP client to Google servers for working with entries and feeds.
 *
 * Because of the nature of this API, all access must occur over an
 * authenticated connection.
 *
 * @link http://code.google.com/apis/apps/gdata_provisioning_api_v2.0_reference.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gapps
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Gapps extends Zend_Gdata
{

    const APPS_BASE_FEED_URI = 'https://apps-apis.google.com/a/feeds';
    const AUTH_SERVICE_NAME = 'apps';

    /**
     * Path to user feeds on the Google Apps server.
     */
    const APPS_USER_PATH = '/user/2.0';

    /**
     * Path to nickname feeds on the Google Apps server.
     */
    const APPS_NICKNAME_PATH = '/nickname/2.0';

    /**
     * Path to email list feeds on the Google Apps server.
     */
    const APPS_EMAIL_LIST_PATH = '/emailList/2.0';

    /**
     * Path to email list recipient feeds on the Google Apps server.
     */
    const APPS_EMAIL_LIST_RECIPIENT_POSTFIX = '/recipient';

    /**
     * The domain which is being administered via the Provisioning API.
     *
     * @var string
     */
    protected $_domain = null;

    /**
     * Namespaces used for Zend_Gdata_Gapps
     *
     * @var array
     */
    public static $namespaces = array(
        array('apps', 'http://schemas.google.com/apps/2006', 1, 0)
    );

    /**
     * Create Gdata_Gapps object
     *
     * @param Zend_Http_Client $client (optional) The HTTP client to use when
     *          when communicating with the Google Apps servers.
     * @param string $domain (optional) The Google Apps domain which is to be
     *          accessed.
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $domain = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('Zend_Gdata_Gapps');
        $this->registerPackage('Zend_Gdata_Gapps_Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
        $this->_domain = $domain;
    }

    /**
     * Convert an exception to an ServiceException if an AppsForYourDomain
     * XML document is contained within the original exception's HTTP
     * response. If conversion fails, throw the original error.
     *
     * @param Zend_Gdata_Exception $e The exception to convert.
     * @throws Zend_Gdata_Gapps_ServiceException
     * @throws mixed
     */
    public static function throwServiceExceptionIfDetected($e) {
        // Check to make sure that there actually response!
        // This can happen if the connection dies before the request
        // completes. (See ZF-5949)
        $response = $e->getResponse();
        if (!$response) {
          require_once('Zend/Gdata/App/IOException.php');
          throw new Zend_Gdata_App_IOException('No HTTP response received (possible connection failure)');
        }

        try {
            // Check to see if there is an AppsForYourDomainErrors
            // datastructure in the response. If so, convert it to
            // an exception and throw it.
            require_once 'Zend/Gdata/Gapps/ServiceException.php';
            $error = new Zend_Gdata_Gapps_ServiceException();
            $error->importFromString($response->getBody());
            throw $error;
        } catch (Zend_Gdata_App_Exception $e2) {
            // Unable to convert the response to a ServiceException,
            // most likely because the server didn't return an
            // AppsForYourDomainErrors document. Throw the original
            // exception.
            throw $e;
        }
    }

    /**
     * Imports a feed located at $uri.
     * This method overrides the default behavior of Zend_Gdata_App,
     * providing support for Zend_Gdata_Gapps_ServiceException.
     *
     * @param  string $uri
     * @param  Zend_Http_Client $client (optional) The client used for
     *          communication
     * @param  string $className (optional) The class which is used as the
     *          return type
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     * @return Zend_Gdata_App_Feed
     */
    public static function import($uri, $client = null, $className='Zend_Gdata_App_Feed')
    {
        try {
            return parent::import($uri, $client, $className);
        } catch (Zend_Gdata_App_HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * GET a URI using client object.
     * This method overrides the default behavior of Zend_Gdata_App,
     * providing support for Zend_Gdata_Gapps_ServiceException.
     *
     * @param string $uri GET URI
     * @param array $extraHeaders Extra headers to add to the request, as an
     *        array of string-based key/value pairs.
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     * @return Zend_Http_Response
     */
    public function get($uri, $extraHeaders = array())
    {
        try {
            return parent::get($uri, $extraHeaders);
        } catch (Zend_Gdata_App_HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * POST data with client object.
     * This method overrides the default behavior of Zend_Gdata_App,
     * providing support for Zend_Gdata_Gapps_ServiceException.
     *
     * @param mixed $data The Zend_Gdata_App_Entry or XML to post
     * @param string $uri (optional) POST URI
     * @param integer $remainingRedirects (optional)
     * @param string $contentType Content-type of the data
     * @param array $extraHaders Extra headers to add tot he request
     * @return Zend_Http_Response
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_App_InvalidArgumentException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function post($data, $uri = null, $remainingRedirects = null,
            $contentType = null, $extraHeaders = null)
    {
        try {
            return parent::post($data, $uri, $remainingRedirects, $contentType, $extraHeaders);
        } catch (Zend_Gdata_App_HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * PUT data with client object
     * This method overrides the default behavior of Zend_Gdata_App,
     * providing support for Zend_Gdata_Gapps_ServiceException.
     *
     * @param mixed $data The Zend_Gdata_App_Entry or XML to post
     * @param string $uri (optional) PUT URI
     * @param integer $remainingRedirects (optional)
     * @param string $contentType Content-type of the data
     * @param array $extraHaders Extra headers to add tot he request
     * @return Zend_Http_Response
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_App_InvalidArgumentException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function put($data, $uri = null, $remainingRedirects = null,
            $contentType = null, $extraHeaders = null)
    {
        try {
            return parent::put($data, $uri, $remainingRedirects, $contentType, $extraHeaders);
        } catch (Zend_Gdata_App_HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * DELETE entry with client object
     * This method overrides the default behavior of Zend_Gdata_App,
     * providing support for Zend_Gdata_Gapps_ServiceException.
     *
     * @param mixed $data The Zend_Gdata_App_Entry or URL to delete
     * @param integer $remainingRedirects (optional)
     * @return void
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_App_InvalidArgumentException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function delete($data, $remainingRedirects = null)
    {
        try {
            return parent::delete($data, $remainingRedirects);
        } catch (Zend_Gdata_App_HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * Set domain for this service instance. This should be a fully qualified
     * domain, such as 'foo.example.com'.
     *
     * This value is used when calculating URLs for retrieving and posting
     * entries. If no value is specified, a URL will have to be manually
     * constructed prior to using any methods which interact with the Google
     * Apps provisioning service.
     *
     * @param string $value The domain to be used for this session.
     */
    public function setDomain($value)
    {
        $this->_domain = $value;
    }

    /**
     * Get domain for this service instance. This should be a fully qualified
     * domain, such as 'foo.example.com'. If no domain is set, null will be
     * returned.
     *
     * @return string The domain to be used for this session, or null if not
     *          set.
     */
    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * Returns the base URL used to access the Google Apps service, based
     * on the current domain. The current domain can be temporarily
     * overridden by providing a fully qualified domain as $domain.
     *
     * @param string $domain (optional) A fully-qualified domain to use
     *          instead of the default domain for this service instance.
     * @throws Zend_Gdata_App_InvalidArgumentException
     */
     public function getBaseUrl($domain = null)
     {
         if ($domain !== null) {
             return self::APPS_BASE_FEED_URI . '/' . $domain;
         } else if ($this->_domain !== null) {
             return self::APPS_BASE_FEED_URI . '/' . $this->_domain;
         } else {
             require_once 'Zend/Gdata/App/InvalidArgumentException.php';
             throw new Zend_Gdata_App_InvalidArgumentException(
                     'Domain must be specified.');
         }
     }

    /**
     * Retrieve a UserFeed containing multiple UserEntry objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return Zend_Gdata_Gapps_UserFeed
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function getUserFeed($location = null)
    {
        if ($location === null) {
            $uri = $this->getBaseUrl() . self::APPS_USER_PATH;
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend_Gdata_Gapps_UserFeed');
    }

    /**
     * Retreive NicknameFeed object containing multiple NicknameEntry objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return Zend_Gdata_Gapps_NicknameFeed
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function getNicknameFeed($location = null)
    {
        if ($location === null) {
            $uri = $this->getBaseUrl() . self::APPS_NICKNAME_PATH;
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend_Gdata_Gapps_NicknameFeed');
    }

    /**
     * Retreive EmailListFeed object containing multiple EmailListEntry
     * objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return Zend_Gdata_Gapps_EmailListFeed
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function getEmailListFeed($location = null)
    {
        if ($location === null) {
            $uri = $this->getBaseUrl() . self::APPS_NICKNAME_PATH;
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend_Gdata_Gapps_EmailListFeed');
    }

    /**
     * Retreive EmailListRecipientFeed object containing multiple
     * EmailListRecipientEntry objects.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return Zend_Gdata_Gapps_EmailListRecipientFeed
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function getEmailListRecipientFeed($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend_Gdata_Gapps_EmailListRecipientFeed');
    }

    /**
     * Retreive a single UserEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return Zend_Gdata_Gapps_UserEntry
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function getUserEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend_Gdata_Gapps_UserEntry');
    }

    /**
     * Retreive a single NicknameEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return Zend_Gdata_Gapps_NicknameEntry
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function getNicknameEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend_Gdata_Gapps_NicknameEntry');
    }

    /**
     * Retreive a single EmailListEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return Zend_Gdata_Gapps_EmailListEntry
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function getEmailListEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend_Gdata_Gapps_EmailListEntry');
    }

    /**
     * Retreive a single EmailListRecipientEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return Zend_Gdata_Gapps_EmailListRecipientEntry
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function getEmailListRecipientEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend_Gdata_Gapps_EmailListRecipientEntry');
    }

    /**
     * Create a new user from a UserEntry.
     *
     * @param Zend_Gdata_Gapps_UserEntry $user The user entry to insert.
     * @param string $uri (optional) The URI where the user should be
     *          uploaded to. If null, the default user creation URI for
     *          this domain will be used.
     * @return Zend_Gdata_Gapps_UserEntry The inserted user entry as
     *          returned by the server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function insertUser($user, $uri = null)
    {
        if ($uri === null) {
            $uri = $this->getBaseUrl() . self::APPS_USER_PATH;
        }
        $newEntry = $this->insertEntry($user, $uri, 'Zend_Gdata_Gapps_UserEntry');
        return $newEntry;
    }

    /**
     * Create a new nickname from a NicknameEntry.
     *
     * @param Zend_Gdata_Gapps_NicknameEntry $nickname The nickname entry to
     *          insert.
     * @param string $uri (optional) The URI where the nickname should be
     *          uploaded to. If null, the default nickname creation URI for
     *          this domain will be used.
     * @return Zend_Gdata_Gapps_NicknameEntry The inserted nickname entry as
     *          returned by the server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function insertNickname($nickname, $uri = null)
    {
        if ($uri === null) {
            $uri = $this->getBaseUrl() . self::APPS_NICKNAME_PATH;
        }
        $newEntry = $this->insertEntry($nickname, $uri, 'Zend_Gdata_Gapps_NicknameEntry');
        return $newEntry;
    }

    /**
     * Create a new email list from an EmailListEntry.
     *
     * @param Zend_Gdata_Gapps_EmailListEntry $emailList The email list entry
     *          to insert.
     * @param string $uri (optional) The URI where the email list should be
     *          uploaded to. If null, the default email list creation URI for
     *          this domain will be used.
     * @return Zend_Gdata_Gapps_EmailListEntry The inserted email list entry
     *          as returned by the server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function insertEmailList($emailList, $uri = null)
    {
        if ($uri === null) {
            $uri = $this->getBaseUrl() . self::APPS_EMAIL_LIST_PATH;
        }
        $newEntry = $this->insertEntry($emailList, $uri, 'Zend_Gdata_Gapps_EmailListEntry');
        return $newEntry;
    }

    /**
     * Create a new email list recipient from an EmailListRecipientEntry.
     *
     * @param Zend_Gdata_Gapps_EmailListRecipientEntry $recipient The recipient
     *          entry to insert.
     * @param string $uri (optional) The URI where the recipient should be
     *          uploaded to. If null, the default recipient creation URI for
     *          this domain will be used.
     * @return Zend_Gdata_Gapps_EmailListRecipientEntry The inserted
     *          recipient entry as returned by the server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function insertEmailListRecipient($recipient, $uri = null)
    {
        if ($uri === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'URI must not be null');
        } elseif ($uri instanceof Zend_Gdata_Gapps_EmailListEntry) {
            $uri = $uri->getLink('edit')->href;
        }
        $newEntry = $this->insertEntry($recipient, $uri, 'Zend_Gdata_Gapps_EmailListRecipientEntry');
        return $newEntry;
    }

    /**
     * Provides a magic factory method to instantiate new objects with
     * shorter syntax than would otherwise be required by the Zend Framework
     * naming conventions. For more information, see Zend_Gdata_App::__call().
     *
     * This overrides the default behavior of __call() so that query classes
     * do not need to have their domain manually set when created with
     * a magic factory method.
     *
     * @see Zend_Gdata_App::__call()
     * @param string $method The method name being called
     * @param array $args The arguments passed to the call
     * @throws Zend_Gdata_App_Exception
     */
    public function __call($method, $args) {
        if (preg_match('/^new(\w+Query)/', $method, $matches)) {
            $class = $matches[1];
            $foundClassName = null;
            foreach ($this->_registeredPackages as $name) {
                 try {
                     // Autoloading disabled on next line for compatibility
                     // with magic factories. See ZF-6660.
                     if (!class_exists($name . '_' . $class, false)) {
                        require_once 'Zend/Loader.php';
                        @Zend_Loader::loadClass($name . '_' . $class);
                     }
                     $foundClassName = $name . '_' . $class;
                     break;
                 } catch (Zend_Exception $e) {
                     // package wasn't here- continue searching
                 }
            }
            if ($foundClassName != null) {
                $reflectionObj = new ReflectionClass($foundClassName);
                // Prepend the domain to the query
                $args = array_merge(array($this->getDomain()), $args);
                return $reflectionObj->newInstanceArgs($args);
            } else {
                require_once 'Zend/Gdata/App/Exception.php';
                throw new Zend_Gdata_App_Exception(
                        "Unable to find '${class}' in registered packages");
            }
        } else {
            return parent::__call($method, $args);
        }

    }

    // Convenience methods
    // Specified at http://code.google.com/apis/apps/gdata_provisioning_api_v2.0_reference.html#appendix_e

    /**
     * Create a new user entry and send it to the Google Apps servers.
     *
     * @param string $username The username for the new user.
     * @param string $givenName The given name for the new user.
     * @param string $familyName The family name for the new user.
     * @param string $password The password for the new user as a plaintext string
     *                 (if $passwordHashFunction is null) or a SHA-1 hashed
     *                 value (if $passwordHashFunction = 'SHA-1').
     * @param string $quotaLimitInMB (optional) The quota limit for the new user in MB.
     * @return Zend_Gdata_Gapps_UserEntry (optional) The new user entry as returned by
     *                 server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function createUser ($username, $givenName, $familyName, $password,
            $passwordHashFunction = null, $quotaLimitInMB = null) {
        $user = $this->newUserEntry();
        $user->login = $this->newLogin();
        $user->login->username = $username;
        $user->login->password = $password;
        $user->login->hashFunctionName = $passwordHashFunction;
        $user->name = $this->newName();
        $user->name->givenName = $givenName;
        $user->name->familyName = $familyName;
        if ($quotaLimitInMB !== null) {
            $user->quota = $this->newQuota();
            $user->quota->limit = $quotaLimitInMB;
        }
        return $this->insertUser($user);
    }

    /**
     * Retrieve a user based on their username.
     *
     * @param string $username The username to search for.
     * @return Zend_Gdata_Gapps_UserEntry The username to search for, or null
     *              if no match found.
     * @throws Zend_Gdata_App_InvalidArgumentException
     * @throws Zend_Gdata_App_HttpException
     */
    public function retrieveUser ($username) {
        $query = $this->newUserQuery($username);
        try {
            $user = $this->getUserEntry($query);
        } catch (Zend_Gdata_Gapps_ServiceException $e) {
            // Set the user to null if not found
            if ($e->hasError(Zend_Gdata_Gapps_Error::ENTITY_DOES_NOT_EXIST)) {
                $user = null;
            } else {
                throw $e;
            }
        }
        return $user;
    }

    /**
     * Retrieve a page of users in alphabetical order, starting with the
     * provided username.
     *
     * @param string $startUsername (optional) The first username to retrieve.
     *          If null or not declared, the page will begin with the first
     *          user in the domain.
     * @return Zend_Gdata_Gapps_UserFeed Collection of Zend_Gdata_UserEntry
     *              objects representing all users in the domain.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrievePageOfUsers ($startUsername = null) {
        $query = $this->newUserQuery();
        $query->setStartUsername($startUsername);
        return $this->getUserFeed($query);
    }

    /**
     * Retrieve all users in the current domain. Be aware that
     * calling this function on a domain with many users will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @return Zend_Gdata_Gapps_UserFeed Collection of Zend_Gdata_UserEntry
     *              objects representing all users in the domain.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrieveAllUsers () {
        return $this->retrieveAllEntriesForFeed($this->retrievePageOfUsers());
    }

    /**
     * Overwrite a specified username with the provided UserEntry.  The
     * UserEntry does not need to contain an edit link.
     *
     * This method is provided for compliance with the Google Apps
     * Provisioning API specification. Normally users will instead want to
     * call UserEntry::save() instead.
     *
     * @see Zend_Gdata_App_Entry::save
     * @param string $username The username whose data will be overwritten.
     * @param Zend_Gdata_Gapps_UserEntry $userEntry The user entry which
     *          will be overwritten.
     * @return Zend_Gdata_Gapps_UserEntry The UserEntry returned by the
     *          server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function updateUser($username, $userEntry) {
        return $this->updateEntry($userEntry, $this->getBaseUrl() .
            self::APPS_USER_PATH . '/' . $username);
    }

    /**
     * Mark a given user as suspended.
     *
     * @param string $username The username associated with the user who
     *          should be suspended.
     * @return Zend_Gdata_Gapps_UserEntry The UserEntry for the modified
     *          user.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function suspendUser($username) {
        $user = $this->retrieveUser($username);
        $user->login->suspended = true;
        return $user->save();
    }

    /**
     * Mark a given user as not suspended.
     *
     * @param string $username The username associated with the user who
     *          should be restored.
     * @return Zend_Gdata_Gapps_UserEntry The UserEntry for the modified
     *          user.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function restoreUser($username) {
        $user = $this->retrieveUser($username);
        $user->login->suspended = false;
        return $user->save();
    }

    /**
     * Delete a user by username.
     *
     * @param string $username The username associated with the user who
     *          should be deleted.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function deleteUser($username) {
        $this->delete($this->getBaseUrl() . self::APPS_USER_PATH . '/' .
            $username);
    }

    /**
     * Create a nickname for a given user.
     *
     * @param string $username The username to which the new nickname should
     *          be associated.
     * @param string $nickname The new nickname to be created.
     * @return Zend_Gdata_Gapps_NicknameEntry The nickname entry which was
     *          created by the server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function createNickname($username, $nickname) {
        $entry = $this->newNicknameEntry();
        $nickname = $this->newNickname($nickname);
        $login = $this->newLogin($username);
        $entry->nickname = $nickname;
        $entry->login = $login;
        return $this->insertNickname($entry);
    }

    /**
     * Retrieve the entry for a specified nickname.
     *
     * @param string $nickname The nickname to be retrieved.
     * @return Zend_Gdata_Gapps_NicknameEntry The requested nickname entry.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrieveNickname($nickname) {
        $query = $this->newNicknameQuery();
        $query->setNickname($nickname);
        try {
            $nickname = $this->getNicknameEntry($query);
        } catch (Zend_Gdata_Gapps_ServiceException $e) {
            // Set the nickname to null if not found
            if ($e->hasError(Zend_Gdata_Gapps_Error::ENTITY_DOES_NOT_EXIST)) {
                $nickname = null;
            } else {
                throw $e;
            }
        }
        return $nickname;
    }

    /**
     * Retrieve all nicknames associated with a specific username.
     *
     * @param string $username The username whose nicknames should be
     *          returned.
     * @return Zend_Gdata_Gapps_NicknameFeed A feed containing all nicknames
     *          for the given user, or null if
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrieveNicknames($username) {
        $query = $this->newNicknameQuery();
        $query->setUsername($username);
        $nicknameFeed = $this->retrieveAllEntriesForFeed(
            $this->getNicknameFeed($query));
        return $nicknameFeed;
    }

    /**
     * Retrieve a page of nicknames in alphabetical order, starting with the
     * provided nickname.
     *
     * @param string $startNickname (optional) The first nickname to
     *          retrieve. If null or not declared, the page will begin with
     *          the first nickname in the domain.
     * @return Zend_Gdata_Gapps_NicknameFeed Collection of Zend_Gdata_NicknameEntry
     *              objects representing all nicknames in the domain.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrievePageOfNicknames ($startNickname = null) {
        $query = $this->newNicknameQuery();
        $query->setStartNickname($startNickname);
        return $this->getNicknameFeed($query);
    }

    /**
     * Retrieve all nicknames in the current domain. Be aware that
     * calling this function on a domain with many nicknames will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @return Zend_Gdata_Gapps_NicknameFeed Collection of Zend_Gdata_NicknameEntry
     *              objects representing all nicknames in the domain.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrieveAllNicknames () {
        return $this->retrieveAllEntriesForFeed($this->retrievePageOfNicknames());
    }

    /**
     * Delete a specified nickname.
     *
     * @param string $nickname The name of the nickname to be deleted.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function deleteNickname($nickname) {
        $this->delete($this->getBaseUrl() . self::APPS_NICKNAME_PATH . '/' . $nickname);
    }

    /**
     * Create a new email list.
     *
     * @param string $emailList The name of the email list to be created.
     * @return Zend_Gdata_Gapps_EmailListEntry The email list entry
     *          as created on the server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function createEmailList($emailList) {
        $entry = $this->newEmailListEntry();
        $list = $this->newEmailList();
        $list->name = $emailList;
        $entry->emailList = $list;
        return $this->insertEmailList($entry);
    }

    /**
     * Retrieve all email lists associated with a recipient.
     *
     * @param string $username The recipient whose associated email lists
     *          should be returned.
     * @return Zend_Gdata_Gapps_EmailListFeed The list of email lists found as
     *          Zend_Gdata_EmailListEntry objects.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrieveEmailLists($recipient) {
        $query = $this->newEmailListQuery();
        $query->recipient = $recipient;
        return $this->getEmailListFeed($query);
    }

    /**
     * Retrieve a page of email lists in alphabetical order, starting with the
     * provided email list.
     *
     * @param string $startEmailListName (optional) The first list to
     *              retrieve. If null or not defined, the page will begin
     *              with the first email list in the domain.
     * @return Zend_Gdata_Gapps_EmailListFeed Collection of Zend_Gdata_EmailListEntry
     *              objects representing all nicknames in the domain.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrievePageOfEmailLists ($startNickname = null) {
        $query = $this->newEmailListQuery();
        $query->setStartEmailListName($startNickname);
        return $this->getEmailListFeed($query);
    }

    /**
     * Retrieve all email lists associated with the curent domain. Be aware that
     * calling this function on a domain with many email lists will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @return Zend_Gdata_Gapps_EmailListFeed The list of email lists found
     *              as Zend_Gdata_Gapps_EmailListEntry objects.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrieveAllEmailLists() {
        return $this->retrieveAllEntriesForFeed($this->retrievePageOfEmailLists());
    }

    /**
     * Delete a specified email list.
     *
     * @param string $emailList The name of the emailList to be deleted.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function deleteEmailList($emailList) {
        $this->delete($this->getBaseUrl() . self::APPS_EMAIL_LIST_PATH . '/'
            . $emailList);
    }

    /**
     * Add a specified recipient to an existing emailList.
     *
     * @param string $recipientAddress The address of the recipient to be
     *              added to the email list.
     * @param string $emailList The name of the email address to which the
     *              recipient should be added.
     * @return Zend_Gdata_Gapps_EmailListRecipientEntry The recipient entry
     *              created by the server.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function addRecipientToEmailList($recipientAddress, $emailList) {
        $entry = $this->newEmailListRecipientEntry();
        $who = $this->newWho();
        $who->email = $recipientAddress;
        $entry->who = $who;
        $address = $this->getBaseUrl() .  self::APPS_EMAIL_LIST_PATH . '/' .
            $emailList . self::APPS_EMAIL_LIST_RECIPIENT_POSTFIX . '/';
        return $this->insertEmailListRecipient($entry, $address);
    }

    /**
     * Retrieve a page of email list recipients in alphabetical order,
     * starting with the provided email list recipient.
     *
     * @param string $emaiList The email list which should be searched.
     * @param string $startRecipient (optinal) The address of the first
     *              recipient, or null to start with the first recipient in
     *              the list.
     * @return Zend_Gdata_Gapps_EmailListRecipientFeed Collection of
     *              Zend_Gdata_EmailListRecipientEntry objects representing all
     *              recpients in the specified list.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrievePageOfRecipients ($emailList,
            $startRecipient = null) {
        $query = $this->newEmailListRecipientQuery();
        $query->setEmailListName($emailList);
        $query->setStartRecipient($startRecipient);
        return $this->getEmailListRecipientFeed($query);
    }

    /**
     * Retrieve all recipients associated with an email list. Be aware that
     * calling this function on a domain with many email lists will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @param string $emaiList The email list which should be searched.
     * @return Zend_Gdata_Gapps_EmailListRecipientFeed The list of email lists
     *              found as Zend_Gdata_Gapps_EmailListRecipientEntry objects.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function retrieveAllRecipients($emailList) {
        return $this->retrieveAllEntriesForFeed(
                $this->retrievePageOfRecipients($emailList));
    }

    /**
     * Remove a specified recipient from an email list.
     *
     * @param string $recipientAddress The recipient to be removed.
     * @param string $emailList The list from which the recipient should
     *              be removed.
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     * @throws Zend_Gdata_Gapps_ServiceException
     */
    public function removeRecipientFromEmailList($recipientAddress, $emailList) {
        $this->delete($this->getBaseUrl() . self::APPS_EMAIL_LIST_PATH . '/'
            . $emailList . self::APPS_EMAIL_LIST_RECIPIENT_POSTFIX . '/'
            . $recipientAddress);
    }

}
