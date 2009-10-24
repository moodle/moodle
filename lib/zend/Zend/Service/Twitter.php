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
 * @package    Zend_Service
 * @subpackage Twitter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
/**
 * @see Zend_Rest_Client
 */
require_once 'Zend/Rest/Client.php';
/**
 * @see Zend_Rest_Client_Result
 */
require_once 'Zend/Rest/Client/Result.php';
/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Twitter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Twitter extends Zend_Rest_Client
{
    
    /**
     * 246 is the current limit for a status message, 140 characters are displayed
     * initially, with the remainder linked from the web UI or client. The limit is
     * applied to a html encoded UTF-8 string (i.e. entities are counted in the limit
     * which may appear unusual but is a security measure).
     *
     * This should be reviewed in the future...
     */
    const STATUS_MAX_CHARACTERS = 246;
    
    /**
     * Whether or not authorization has been initialized for the current user.
     * @var bool
     */
    protected $_authInitialized = false;
    /**
     * @var Zend_Http_CookieJar
     */
    protected $_cookieJar;
    /**
     * Date format for 'since' strings
     * @var string
     */
    protected $_dateFormat = 'D, d M Y H:i:s T';
    /**
     * Username
     * @var string
     */
    protected $_username;
    /**
     * Password
     * @var string
     */
    protected $_password;
    /**
     * Current method type (for method proxying)
     * @var string
     */
    protected $_methodType;
    /**
     * Types of API methods
     * @var array
     */
    protected $_methodTypes = array('status', 'user', 'directMessage', 'friendship', 'account', 'favorite', 'block');
    
    /**
     * Local HTTP Client cloned from statically set client
     * @var Zend_Http_Client
     */
    protected $_localHttpClient = null;

    /**
     * Constructor
     *
     * @param  string $username
     * @param  string $password
     * @return void
     */
    public function __construct($username, $password = null)
    {
        $this->setLocalHttpClient(clone self::getHttpClient());
        if (is_array($username) && is_null($password)) {
            if (isset($username['username']) && isset($username['password'])) {
                $this->setUsername($username['username']);
                $this->setPassword($username['password']);
            } elseif (isset($username[0]) && isset($username[1])) {
                $this->setUsername($username[0]);
                $this->setPassword($username[1]);
            }
        } else {
            $this->setUsername($username);
            $this->setPassword($password);
        }
        $this->setUri('http://twitter.com');
        $this->_localHttpClient->setHeaders('Accept-Charset', 'ISO-8859-1,utf-8');
    }

    /**
     * Set local HTTP client as distinct from the static HTTP client
     * as inherited from Zend_Rest_Client.
     *
     * @param Zend_Http_Client $client
     * @return self
     */
    public function setLocalHttpClient(Zend_Http_Client $client)
    {
        $this->_localHttpClient = $client;
        return $this;
    }

    public function getLocalHttpClient()
    {
        return $this->_localHttpClient;
    }

    /**
     * Retrieve username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Set username
     *
     * @param  string $value
     * @return Zend_Service_Twitter
     */
    public function setUsername($value)
    {
        $this->_username = $value;
        $this->_authInitialized = false;
        return $this;
    }

    /**
     * Retrieve password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Set password
     *
     * @param  string $value
     * @return Zend_Service_Twitter
     */
    public function setPassword($value)
    {
        $this->_password = $value;
        $this->_authInitialized = false;
        return $this;
    }

    /**
     * Proxy service methods
     *
     * @param  string $type
     * @return Zend_Service_Twitter
     * @throws Zend_Service_Twitter_Exception if method is not in method types list
     */
    public function __get($type)
    {
        if (!in_array($type, $this->_methodTypes)) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Invalid method type "' . $type . '"');
        }
        $this->_methodType = $type;
        return $this;
    }

    /**
     * Method overloading
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     * @throws Zend_Service_Twitter_Exception if unable to find method
     */
    public function __call($method, $params)
    {
        if (empty($this->_methodType)) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Invalid method "' . $method . '"');
        }
        $test = $this->_methodType . ucfirst($method);
        if (!method_exists($this, $test)) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Invalid method "' . $test . '"');
        }
        
        return call_user_func_array(array($this, $test), $params);
    }

    /**
     * Initialize HTTP authentication
     *
     * @return void
     */
    protected function _init()
    {
        $client = $this->_localHttpClient;
        $client->resetParameters();
        if (null == $this->_cookieJar) {
            $client->setCookieJar();
            $this->_cookieJar = $client->getCookieJar();
        } else {
            $client->setCookieJar($this->_cookieJar);
        }
        if (!$this->_authInitialized) {
            $client->setAuth($this->getUsername(), $this->getPassword());
            $this->_authInitialized = true;
        }
    }

    /**
     * Set date header
     *
     * @param  int|string $value
     * @deprecated Not supported by Twitter since April 08, 2009
     * @return void
     */
    protected function _setDate($value)
    {
        if (is_int($value)) {
            $date = date($this->_dateFormat, $value);
        } else {
            $date = date($this->_dateFormat, strtotime($value));
        }
        $this->_localHttpClient->setHeaders('If-Modified-Since', $date);
    }

    /**
     * Public Timeline status
     *
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function statusPublicTimeline()
    {
        $this->_init();
        $path = '/statuses/public_timeline.xml';
        $response = $this->_get($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Friend Timeline Status
     *
     * $params may include one or more of the following keys
     * - id: ID of a friend whose timeline you wish to receive
     * - count: how many statuses to return
     * - since_id: return results only after the specific tweet
     * - page: return page X of results
     *
     * @param  array $params
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return void
     */
    public function statusFriendsTimeline(array $params = array())
    {
        $this->_init();
        $path = '/statuses/friends_timeline';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'count':
                    $count = (int) $value;
                    if (0 >= $count) {
                        $count = 1;
                    } elseif (200 < $count) {
                        $count = 200;
                    }
                    $_params['count'] = (int) $count;
                    break;
                case 'since_id':
                    $_params['since_id'] = $this->_validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';
        $response = $this->_get($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * User Timeline status
     *
     * $params may include one or more of the following keys
     * - id: ID of a friend whose timeline you wish to receive
     * - since_id: return results only after the tweet id specified
     * - page: return page X of results
     * - count: how many statuses to return
     * - max_id: returns only statuses with an ID less than or equal to the specified ID
     * - user_id: specfies the ID of the user for whom to return the user_timeline
     * - screen_name: specfies the screen name of the user for whom to return the user_timeline
     *
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function statusUserTimeline(array $params = array())
    {
        $this->_init();
        $path = '/statuses/user_timeline';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $value;
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                case 'count':
                    $count = (int) $value;
                    if (0 >= $count) {
                        $count = 1;
                    } elseif (200 < $count) {
                        $count = 200;
                    }
                    $_params['count'] = $count;
                    break;
                case 'user_id':
                    $_params['user_id'] = $this->_validInteger($value);
                    break;
                case 'screen_name':
                    $_params['screen_name'] = $this->_validateScreenName($value);
                    break;
                case 'since_id':
                    $_params['since_id'] = $this->_validInteger($value);
                    break;
                case 'max_id':
                    $_params['max_id'] = $this->_validInteger($value);
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';
        $response = $this->_get($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Show a single status
     *
     * @param  int $id Id of status to show
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function statusShow($id)
    {
        $this->_init();
        $path = '/statuses/show/' . $this->_validInteger($id) . '.xml';
        $response = $this->_get($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Update user's current status
     *
     * @param  string $status
     * @param  int $in_reply_to_status_id
     * @return Zend_Rest_Client_Result
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @throws Zend_Service_Twitter_Exception if message is too short or too long
     */
    public function statusUpdate($status, $in_reply_to_status_id = null)
    {
        $this->_init();
        $path = '/statuses/update.xml';
        $len = iconv_strlen(htmlspecialchars($status, ENT_QUOTES, 'UTF-8'), 'UTF-8');
        if ($len > self::STATUS_MAX_CHARACTERS) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must be no more than ' . self::STATUS_MAX_CHARACTERS . ' characters in length');
        } elseif (0 == $len) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must contain at least one character');
        }
        $data = array('status' => $status);
        if (is_numeric($in_reply_to_status_id) && !empty($in_reply_to_status_id)) {
            $data['in_reply_to_status_id'] = $in_reply_to_status_id;
        }
        //$this->status = $status;
        $response = $this->_post($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Get status replies
     *
     * $params may include one or more of the following keys
     * - since_id: return results only after the specified tweet id
     * - page: return page X of results
     *
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function statusReplies(array $params = array())
    {
        $this->_init();
        $path = '/statuses/replies.xml';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since_id':
                    $_params['since_id'] = $this->_validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $response = $this->_get($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Destroy a status message
     *
     * @param  int $id ID of status to destroy
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function statusDestroy($id)
    {
        $this->_init();
        $path = '/statuses/destroy/' . $this->_validInteger($id) . '.xml';
        $response = $this->_post($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * User friends
     *
     * @param  int|string $id Id or username of user for whom to fetch friends
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function userFriends(array $params = array())
    {
        $this->_init();
        $path = '/statuses/friends';
        $_params = array();
        
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $value;
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';
        
        $response = $this->_get($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * User Followers
     *
     * @param  bool $lite If true, prevents inline inclusion of current status for followers; defaults to false
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function userFollowers($lite = false)
    {
        $this->_init();
        $path = '/statuses/followers.xml';
        if ($lite) {
            $this->lite = 'true';
        }
        $response = $this->_get($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Get featured users
     *
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function userFeatured()
    {
        $this->_init();
        $path = '/statuses/featured.xml';
        $response = $this->_get($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Show extended information on a user
     *
     * @param  int|string $id User ID or name
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function userShow($id)
    {
        $this->_init();
        $path = '/users/show/' . $id . '.xml';
        $response = $this->_get($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Retrieve direct messages for the current user
     *
     * $params may include one or more of the following keys
     * - since_id: return statuses only greater than the one specified
     * - page: return page X of results
     *
     * @param  array $params
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function directMessageMessages(array $params = array())
    {
        $this->_init();
        $path = '/direct_messages.xml';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since_id':
                    $_params['since_id'] = $this->_validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $response = $this->_get($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Retrieve list of direct messages sent by current user
     *
     * $params may include one or more of the following keys
     * - since_id: return statuses only greater than the one specified
     * - page: return page X of results
     *
     * @param  array $params
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function directMessageSent(array $params = array())
    {
        $this->_init();
        $path = '/direct_messages/sent.xml';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since_id':
                    $_params['since_id'] = $this->_validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $response = $this->_get($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Send a direct message to a user
     *
     * @param  int|string $user User to whom to send message
     * @param  string $text Message to send to user
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception if message is too short or too long
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     */
    public function directMessageNew($user, $text)
    {
        $this->_init();
        $path = '/direct_messages/new.xml';
        $len = iconv_strlen($text, 'UTF-8');
        if (0 == $len) {
            throw new Zend_Service_Twitter_Exception('Direct message must contain at least one character');
        } elseif (140 < $len) {
            throw new Zend_Service_Twitter_Exception('Direct message must contain no more than 140 characters');
        }
        $data = array('user' => $user, 'text' => $text);
        $response = $this->_post($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Destroy a direct message
     *
     * @param  int $id ID of message to destroy
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function directMessageDestroy($id)
    {
        $this->_init();
        $path = '/direct_messages/destroy/' . $this->_validInteger($id) . '.xml';
        $response = $this->_post($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Create friendship
     *
     * @param  int|string $id User ID or name of new friend
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function friendshipCreate($id)
    {
        $this->_init();
        $path = '/friendships/create/' . $id . '.xml';
        $response = $this->_post($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Destroy friendship
     *
     * @param  int|string $id User ID or name of friend to remove
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function friendshipDestroy($id)
    {
        $this->_init();
        $path = '/friendships/destroy/' . $id . '.xml';
        $response = $this->_post($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Friendship exists
     *
     * @param int|string $id User ID or name of friend to see if they are your friend
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_result
     */
    public function friendshipExists($id)
    {
        $this->_init();
        $path = '/friendships/exists.xml';
        $data = array('user_a' => $this->getUsername(), 'user_b' => $id);
        $response = $this->_get($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Verify Account Credentials
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     *
     * @return Zend_Rest_Client_Result
     */
    public function accountVerifyCredentials()
    {
        $this->_init();
        $response = $this->_get('/account/verify_credentials.xml');
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * End current session
     *
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return true
     */
    public function accountEndSession()
    {
        $this->_init();
        $this->_get('/account/end_session');
        return true;
    }

    /**
     * Returns the number of api requests you have left per hour.
     *
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function accountRateLimitStatus()
    {
        $this->_init();
        $response = $this->_get('/account/rate_limit_status.xml');
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Fetch favorites
     *
     * $params may contain one or more of the following:
     * - 'id': Id of a user for whom to fetch favorites
     * - 'page': Retrieve a different page of resuls
     *
     * @param  array $params
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function favoriteFavorites(array $params = array())
    {
        $this->_init();
        $path = '/favorites';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $this->_validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';
        $response = $this->_get($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Mark a status as a favorite
     *
     * @param  int $id Status ID you want to mark as a favorite
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function favoriteCreate($id)
    {
        $this->_init();
        $path = '/favorites/create/' . $this->_validInteger($id) . '.xml';
        $response = $this->_post($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Remove a favorite
     *
     * @param  int $id Status ID you want to de-list as a favorite
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function favoriteDestroy($id)
    {
        $this->_init();
        $path = '/favorites/destroy/' . $this->_validInteger($id) . '.xml';
        $response = $this->_post($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Blocks the user specified in the ID parameter as the authenticating user.
     * Destroys a friendship to the blocked user if it exists.
     * 
     * @param integer|string $id       The ID or screen name of a user to block. 
     * @return Zend_Rest_Client_Result
     */
    public function blockCreate($id)
    {
        $this->_init();
        $path = '/blocks/create/' . $id . '.xml';
        $response = $this->_post($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Un-blocks the user specified in the ID parameter for the authenticating user
     * 
     * @param integer|string $id       The ID or screen_name of the user to un-block. 
     * @return Zend_Rest_Client_Result
     */
    public function blockDestroy($id)
    {
        $this->_init();
        $path = '/blocks/destroy/' . $id . '.xml';
        $response = $this->_post($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Returns if the authenticating user is blocking a target user. 
     * 
     * @param string|integer $id    The ID or screen_name of the potentially blocked user.    
     * @param boolean $returnResult Instead of returning a boolean return the rest response from twitter
     * @return Boolean|Zend_Rest_Client_Result
     */
    public function blockExists($id, $returnResult = false)
    {
        $this->_init();
        $path = '/blocks/exists/' . $id . '.xml';
        $response = $this->_get($path);
        
        $cr = new Zend_Rest_Client_Result($response->getBody());
        
        if ($returnResult === true)
            return $cr;
        
        if (!empty($cr->request)) {
            return false;
        }
        
        return true;
    }

    /**
     * Returns an array of user objects that the authenticating user is blocking
     * 
     * @param integer $page         Optional. Specifies the page number of the results beginning at 1. A single page contains 20 ids. 
     * @param boolean $returnUserIds  Optional. Returns only the userid's instead of the whole user object
     * @return Zend_Rest_Client_Result
     */
    public function blockBlocking($page = 1, $returnUserIds = false)
    {
        $this->_init();
        $path = '/blocks/blocking';
        if ($returnUserIds === true) {
            $path .= '/ids';
        }
        $path .= '.xml';
        $response = $this->_get($path, array('page' => $page));
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Protected function to validate that the integer is valid or return a 0
     * @param $int
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return integer
     */
    protected function _validInteger($int)
    {
        if (preg_match("/(\d+)/", $int)) {
            return $int;
        }
        return 0;
    }

    /**
     * Validate a screen name using Twitter rules
     *
     * @param string $name
     * @throws Zend_Service_Twitter_Exception
     * @return string
     */
    protected function _validateScreenName($name)
    {
        if (!preg_match('/^[a-zA-Z0-9_]{0,20}$/', $name)) {
            require_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Screen name, "' . $name . '" should only contain alphanumeric characters and' . ' underscores, and not exceed 15 characters.');
        }
        return $name;
    }

    /**
     * Call a remote REST web service URI and return the Zend_Http_Response object
     *
     * @param  string $path            The path to append to the URI
     * @throws Zend_Rest_Client_Exception
     * @return void
     */
    protected function _prepare($path)
    {
        // Get the URI object and configure it
        if (!$this->_uri instanceof Zend_Uri_Http) {
            require_once 'Zend/Rest/Client/Exception.php';
            throw new Zend_Rest_Client_Exception('URI object must be set before performing call');
        }
        
        $uri = $this->_uri->getUri();
        
        if ($path[0] != '/' && $uri[strlen($uri) - 1] != '/') {
            $path = '/' . $path;
        }
        
        $this->_uri->setPath($path);
        
        /**
         * Get the HTTP client and configure it for the endpoint URI.  Do this each time
         * because the Zend_Http_Client instance is shared among all Zend_Service_Abstract subclasses.
         */
        $this->_localHttpClient->resetParameters()->setUri($this->_uri);
    }

    /**
     * Performs an HTTP GET request to the $path.
     *
     * @param string $path
     * @param array  $query Array of GET parameters
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     */
    protected function _get($path, array $query = null)
    {
        $this->_prepare($path);
        $this->_localHttpClient->setParameterGet($query);
        return $this->_localHttpClient->request('GET');
    }

    /**
     * Performs an HTTP POST request to $path.
     *
     * @param string $path
     * @param mixed $data Raw data to send
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     */
    protected function _post($path, $data = null)
    {
        $this->_prepare($path);
        return $this->_performPost('POST', $data);
    }

    /**
     * Perform a POST or PUT
     *
     * Performs a POST or PUT request. Any data provided is set in the HTTP
     * client. String data is pushed in as raw POST data; array or object data
     * is pushed in as POST parameters.
     *
     * @param mixed $method
     * @param mixed $data
     * @return Zend_Http_Response
     */
    protected function _performPost($method, $data = null)
    {
        $client = $this->_localHttpClient;
        if (is_string($data)) {
            $client->setRawData($data);
        } elseif (is_array($data) || is_object($data)) {
            $client->setParameterPost((array) $data);
        }
        return $client->request($method);
    }

}
