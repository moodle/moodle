<?php

namespace IMSGlobal\LTI\ToolProvider\DataConnector;

use IMSGlobal\LTI\ToolProvider\ConsumerNonce;
use IMSGlobal\LTI\ToolProvider\Context;
use IMSGlobal\LTI\ToolProvider\ResourceLink;
use IMSGlobal\LTI\ToolProvider\ResourceLinkShareKey;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use IMSGlobal\LTI\ToolProvider\ToolProxy;
use IMSGlobal\LTI\ToolProvider\User;
use PDO;

/**
 * Class to provide a connection to a persistent store for LTI objects
 *
 * This class assumes no data persistence - it should be extended for specific database connections.
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
#[\AllowDynamicProperties]
class DataConnector
{

/**
 * Default name for database table used to store tool consumers.
 */
    const CONSUMER_TABLE_NAME = 'lti2_consumer';
/**
 * Default name for database table used to store pending tool proxies.
 */
    const TOOL_PROXY_TABLE_NAME = 'lti2_tool_proxy';
/**
 * Default name for database table used to store contexts.
 */
    const CONTEXT_TABLE_NAME = 'lti2_context';
/**
 * Default name for database table used to store resource links.
 */
    const RESOURCE_LINK_TABLE_NAME = 'lti2_resource_link';
/**
 * Default name for database table used to store users.
 */
    const USER_RESULT_TABLE_NAME = 'lti2_user_result';
/**
 * Default name for database table used to store resource link share keys.
 */
    const RESOURCE_LINK_SHARE_KEY_TABLE_NAME = 'lti2_share_key';
/**
 * Default name for database table used to store nonce values.
 */
    const NONCE_TABLE_NAME = 'lti2_nonce';

/**
 * Database object.
 *
 * @var object $db
 */
    protected $db = null;
/**
 * Prefix for database table names.
 *
 * @var string $dbTableNamePrefix
 */
    protected $dbTableNamePrefix = '';
/**
 * SQL date format (default = 'Y-m-d')
 *
 * @var string $dateFormat
 */
    protected $dateFormat = 'Y-m-d';
/**
 * SQL time format (default = 'H:i:s')
 *
 * @var string $timeFormat
 */
    protected $timeFormat = 'H:i:s';

/**
 * Class constructor
 *
 * @param object $db                 Database connection object
 * @param string $dbTableNamePrefix  Prefix for database table names (optional, default is none)
 */
    public function __construct($db, $dbTableNamePrefix = '')
    {

        $this->db = $db;
        $this->dbTableNamePrefix = $dbTableNamePrefix;

    }

###
###  ToolConsumer methods
###

/**
 * Load tool consumer object.
 *
 * @param ToolConsumer $consumer ToolConsumer object
 *
 * @return boolean True if the tool consumer object was successfully loaded
 */
    public function loadToolConsumer($consumer)
    {

        $consumer->secret = 'secret';
        $consumer->enabled = true;
        $now = time();
        $consumer->created = $now;
        $consumer->updated = $now;

        return true;

    }

/**
 * Save tool consumer object.
 *
 * @param ToolConsumer $consumer Consumer object
 *
 * @return boolean True if the tool consumer object was successfully saved
 */
    public function saveToolConsumer($consumer)
    {

        $consumer->updated = time();

        return true;

    }

/**
 * Delete tool consumer object.
 *
 * @param ToolConsumer $consumer Consumer object
 *
 * @return boolean True if the tool consumer object was successfully deleted
 */
    public function deleteToolConsumer($consumer)
    {

        $consumer->initialize();

        return true;

    }

/**
 * Load tool consumer objects.
 *
 * @return array Array of all defined ToolConsumer objects
 */
    public function getToolConsumers()
    {

        return array();

    }


###
###  ToolProxy methods
###

/**
 * Load tool proxy object.
 *
 * @param ToolProxy $toolProxy ToolProxy object
 *
 * @return boolean True if the tool proxy object was successfully loaded
 */
    public function loadToolProxy($toolProxy)
    {

        $now = time();
        $toolProxy->created = $now;
        $toolProxy->updated = $now;

        return true;

    }

/**
 * Save tool proxy object.
 *
 * @param ToolProxy $toolProxy ToolProxy object
 *
 * @return boolean True if the tool proxy object was successfully saved
 */
    public function saveToolProxy($toolProxy)
    {

        $toolProxy->updated = time();

        return true;

    }

/**
 * Delete tool proxy object.
 *
 * @param ToolProxy $toolProxy ToolProxy object
 *
 * @return boolean True if the tool proxy object was successfully deleted
 */
    public function deleteToolProxy($toolProxy)
    {

        $toolProxy->initialize();

        return true;

    }

###
###  Context methods
###

/**
 * Load context object.
 *
 * @param Context $context Context object
 *
 * @return boolean True if the context object was successfully loaded
 */
    public function loadContext($context)
    {

        $now = time();
        $context->created = $now;
        $context->updated = $now;

        return true;

    }

/**
 * Save context object.
 *
 * @param Context $context Context object
 *
 * @return boolean True if the context object was successfully saved
 */
    public function saveContext($context)
    {

        $context->updated = time();

        return true;

    }

/**
 * Delete context object.
 *
 * @param Context $context Context object
 *
 * @return boolean True if the Context object was successfully deleted
 */
    public function deleteContext($context)
    {

        $context->initialize();

        return true;

    }

###
###  ResourceLink methods
###

/**
 * Load resource link object.
 *
 * @param ResourceLink $resourceLink Resource_Link object
 *
 * @return boolean True if the resource link object was successfully loaded
 */
    public function loadResourceLink($resourceLink)
    {

        $now = time();
        $resourceLink->created = $now;
        $resourceLink->updated = $now;

        return true;

    }

/**
 * Save resource link object.
 *
 * @param ResourceLink $resourceLink Resource_Link object
 *
 * @return boolean True if the resource link object was successfully saved
 */
    public function saveResourceLink($resourceLink)
    {

        $resourceLink->updated = time();

        return true;

    }

/**
 * Delete resource link object.
 *
 * @param ResourceLink $resourceLink Resource_Link object
 *
 * @return boolean True if the resource link object was successfully deleted
 */
    public function deleteResourceLink($resourceLink)
    {

        $resourceLink->initialize();

        return true;

    }

/**
 * Get array of user objects.
 *
 * Obtain an array of User objects for users with a result sourcedId.  The array may include users from other
 * resource links which are sharing this resource link.  It may also be optionally indexed by the user ID of a specified scope.
 *
 * @param ResourceLink $resourceLink      Resource link object
 * @param boolean     $localOnly True if only users within the resource link are to be returned (excluding users sharing this resource link)
 * @param int         $idScope     Scope value to use for user IDs
 *
 * @return array Array of User objects
 */
    public function getUserResultSourcedIDsResourceLink($resourceLink, $localOnly, $idScope)
    {

        return array();

    }

/**
 * Get array of shares defined for this resource link.
 *
 * @param ResourceLink $resourceLink Resource_Link object
 *
 * @return array Array of ResourceLinkShare objects
 */
    public function getSharesResourceLink($resourceLink)
    {

        return array();

    }

###
###  ConsumerNonce methods
###

/**
 * Load nonce object.
 *
 * @param ConsumerNonce $nonce Nonce object
 *
 * @return boolean True if the nonce object was successfully loaded
 */
    public function loadConsumerNonce($nonce)
    {
        return false;  // assume the nonce does not already exist

    }

/**
 * Save nonce object.
 *
 * @param ConsumerNonce $nonce Nonce object
 *
 * @return boolean True if the nonce object was successfully saved
 */
    public function saveConsumerNonce($nonce)
    {

        return true;

    }

###
###  ResourceLinkShareKey methods
###

/**
 * Load resource link share key object.
 *
 * @param ResourceLinkShareKey $shareKey Resource_Link share key object
 *
 * @return boolean True if the resource link share key object was successfully loaded
 */
    public function loadResourceLinkShareKey($shareKey)
    {

        return true;

    }

/**
 * Save resource link share key object.
 *
 * @param ResourceLinkShareKey $shareKey Resource link share key object
 *
 * @return boolean True if the resource link share key object was successfully saved
 */
    public function saveResourceLinkShareKey($shareKey)
    {

        return true;

    }

/**
 * Delete resource link share key object.
 *
 * @param ResourceLinkShareKey $shareKey Resource link share key object
 *
 * @return boolean True if the resource link share key object was successfully deleted
 */
    public function deleteResourceLinkShareKey($shareKey)
    {

        return true;

    }

###
###  User methods
###

/**
 * Load user object.
 *
 * @param User $user User object
 *
 * @return boolean True if the user object was successfully loaded
 */
    public function loadUser($user)
    {

        $now = time();
        $user->created = $now;
        $user->updated = $now;

        return true;

    }

/**
 * Save user object.
 *
 * @param User $user User object
 *
 * @return boolean True if the user object was successfully saved
 */
    public function saveUser($user)
    {

        $user->updated = time();

        return true;

    }

/**
 * Delete user object.
 *
 * @param User $user User object
 *
 * @return boolean True if the user object was successfully deleted
 */
    public function deleteUser($user)
    {

        $user->initialize();

        return true;

    }

###
###  Other methods
###

/**
 * Return a hash of a consumer key for values longer than 255 characters.
 *
 * @param string $key
 * @return string
 */
    protected static function getConsumerKey($key)
    {

        $len = strlen($key);
        if ($len > 255) {
            $key = 'sha512:' . hash('sha512', $key);
        }

        return $key;

    }

/**
 * Create data connector object.
 *
 * A data connector provides access to persistent storage for the different objects.
 *
 * Names of tables may be given a prefix to allow multiple versions to share the same schema.  A separate sub-class is defined for
 * each different database connection - the class to use is determined by inspecting the database object passed, but this can be overridden
 * (for example, to use a bespoke connector) by specifying a type.  If no database is passed then this class is used which acts as a dummy
 * connector with no persistence.
 *
 * @param string  $dbTableNamePrefix  Prefix for database table names (optional, default is none)
 * @param object  $db                 A database connection object or string (optional, default is no persistence)
 * @param string  $type               The type of data connector (optional, default is based on $db parameter)
 *
 * @return DataConnector Data connector object
 */
    public static function getDataConnector($dbTableNamePrefix = '', $db = null, $type = '')
    {

        if (is_null($dbTableNamePrefix)) {
            $dbTableNamePrefix = '';
        }
        if (!is_null($db) && empty($type)) {
            if (is_object($db)) {
                $type = get_class($db);
            }
        }
        $type = strtolower($type);
        if (($type === 'pdo') && ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite')) {
            $type .= '_sqlite';
        }
        if (!empty($type)) {
            $type ="DataConnector_{$type}";
        } else {
            $type ='DataConnector';
        }
        $type = "\\IMSGlobal\\LTI\\ToolProvider\\DataConnector\\{$type}";
        $dataConnector = new $type($db, $dbTableNamePrefix);

        return $dataConnector;

    }

/**
 * Generate a random string.
 *
 * The generated string will only comprise letters (upper- and lower-case) and digits.
 *
 * @param int $length Length of string to be generated (optional, default is 8 characters)
 *
 * @return string Random string
 */
    static function getRandomString($length = 8)
    {

        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $value = '';
        $charsLength = strlen($chars) - 1;

        for ($i = 1 ; $i <= $length; $i++) {
            $value .= $chars[rand(0, $charsLength)];
        }

        return $value;

    }

/**
 * Quote a string for use in a database query.
 *
 * Any single quotes in the value passed will be replaced with two single quotes.  If a null value is passed, a string
 * of 'null' is returned (which will never be enclosed in quotes irrespective of the value of the $addQuotes parameter.
 *
 * @param string $value Value to be quoted
 * @param bool $addQuotes If true the returned string will be enclosed in single quotes (optional, default is true)
 * @return string The quoted string.
 */
    static function quoted($value, $addQuotes = true)
    {

        if (is_null($value)) {
            $value = 'null';
        } else {
            $value = str_replace('\'', '\'\'', $value);
            if ($addQuotes) {
                $value = "'{$value}'";
            }
        }

        return $value;

    }

}
