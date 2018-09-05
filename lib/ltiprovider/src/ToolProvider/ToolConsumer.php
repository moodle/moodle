<?php

namespace IMSGlobal\LTI\ToolProvider;

use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use IMSGlobal\LTI\ToolProvider\Service;
use IMSGlobal\LTI\HTTPMessage;
use IMSGlobal\LTI\OAuth;
use stdClass;

/**
 * Class to represent a tool consumer
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.2
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class ToolConsumer
{

/**
 * Local name of tool consumer.
 *
 * @var string $name
 */
    public $name = null;
/**
 * Shared secret.
 *
 * @var string $secret
 */
    public $secret = null;
/**
 * LTI version (as reported by last tool consumer connection).
 *
 * @var string $ltiVersion
 */
    public $ltiVersion = null;
/**
 * Name of tool consumer (as reported by last tool consumer connection).
 *
 * @var string $consumerName
 */
    public $consumerName = null;
/**
 * Tool consumer version (as reported by last tool consumer connection).
 *
 * @var string $consumerVersion
 */
    public $consumerVersion = null;
/**
 * Tool consumer GUID (as reported by first tool consumer connection).
 *
 * @var string $consumerGuid
 */
    public $consumerGuid = null;
/**
 * Optional CSS path (as reported by last tool consumer connection).
 *
 * @var string $cssPath
 */
    public $cssPath = null;
/**
 * Whether the tool consumer instance is protected by matching the consumer_guid value in incoming requests.
 *
 * @var boolean $protected
 */
    public $protected = false;
/**
 * Whether the tool consumer instance is enabled to accept incoming connection requests.
 *
 * @var boolean $enabled
 */
    public $enabled = false;
/**
 * Date/time from which the the tool consumer instance is enabled to accept incoming connection requests.
 *
 * @var int $enableFrom
 */
    public $enableFrom = null;
/**
 * Date/time until which the tool consumer instance is enabled to accept incoming connection requests.
 *
 * @var int $enableUntil
 */
    public $enableUntil = null;
/**
 * Date of last connection from this tool consumer.
 *
 * @var int $lastAccess
 */
    public $lastAccess = null;
/**
 * Default scope to use when generating an Id value for a user.
 *
 * @var int $idScope
 */
    public $idScope = ToolProvider::ID_SCOPE_ID_ONLY;
/**
 * Default email address (or email domain) to use when no email address is provided for a user.
 *
 * @var string $defaultEmail
 */
    public $defaultEmail = '';
/**
 * Setting values (LTI parameters, custom parameters and local parameters).
 *
 * @var array $settings
 */
    public $settings = null;
/**
 * Date/time when the object was created.
 *
 * @var int $created
 */
    public $created = null;
/**
 * Date/time when the object was last updated.
 *
 * @var int $updated
 */
    public $updated = null;
/**
 * The consumer profile data.
 *
 * @var stdClass
 */
    public $profile = null;

/**
 * Consumer ID value.
 *
 * @var int $id
 */
    private $id = null;
/**
 * Consumer key value.
 *
 * @var string $key
 */
    private $key = null;
/**
 * Whether the settings value have changed since last saved.
 *
 * @var boolean $settingsChanged
 */
    private $settingsChanged = false;
/**
 * Data connector object or string.
 *
 * @var mixed $dataConnector
 */
    private $dataConnector = null;

/**
 * Class constructor.
 *
 * @param string  $key             Consumer key
 * @param DataConnector   $dataConnector   A data connector object
 * @param boolean $autoEnable      true if the tool consumers is to be enabled automatically (optional, default is false)
 */
    public function __construct($key = null, $dataConnector = null, $autoEnable = false)
    {

        $this->initialize();
        if (empty($dataConnector)) {
            $dataConnector = DataConnector::getDataConnector();
        }
        $this->dataConnector = $dataConnector;
        if (!empty($key)) {
            $this->load($key, $autoEnable);
        } else {
            $this->secret = DataConnector::getRandomString(32);
        }

    }

/**
 * Initialise the tool consumer.
 */
    public function initialize()
    {

        $this->id = null;
        $this->key = null;
        $this->name = null;
        $this->secret = null;
        $this->ltiVersion = null;
        $this->consumerName = null;
        $this->consumerVersion = null;
        $this->consumerGuid = null;
        $this->profile = null;
        $this->toolProxy = null;
        $this->settings = array();
        $this->protected = false;
        $this->enabled = false;
        $this->enableFrom = null;
        $this->enableUntil = null;
        $this->lastAccess = null;
        $this->idScope = ToolProvider::ID_SCOPE_ID_ONLY;
        $this->defaultEmail = '';
        $this->created = null;
        $this->updated = null;

    }

/**
 * Initialise the tool consumer.
 *
 * Pseudonym for initialize().
 */
    public function initialise()
    {

        $this->initialize();

    }

/**
 * Save the tool consumer to the database.
 *
 * @return boolean True if the object was successfully saved
 */
    public function save()
    {

        $ok = $this->dataConnector->saveToolConsumer($this);
        if ($ok) {
            $this->settingsChanged = false;
        }

        return $ok;

    }

/**
 * Delete the tool consumer from the database.
 *
 * @return boolean True if the object was successfully deleted
 */
    public function delete()
    {

        return $this->dataConnector->deleteToolConsumer($this);

    }

/**
 * Get the tool consumer record ID.
 *
 * @return int Consumer record ID value
 */
    public function getRecordId()
    {

        return $this->id;

    }

/**
 * Sets the tool consumer record ID.
 *
 * @param int $id  Consumer record ID value
 */
    public function setRecordId($id)
    {

        $this->id = $id;

    }

/**
 * Get the tool consumer key.
 *
 * @return string Consumer key value
 */
    public function getKey()
    {

        return $this->key;

    }

/**
 * Set the tool consumer key.
 *
 * @param string $key  Consumer key value
 */
    public function setKey($key)
    {

        $this->key = $key;

    }

/**
 * Get the data connector.
 *
 * @return mixed Data connector object or string
 */
    public function getDataConnector()
    {

        return $this->dataConnector;

    }

/**
 * Is the consumer key available to accept launch requests?
 *
 * @return boolean True if the consumer key is enabled and within any date constraints
 */
    public function getIsAvailable()
    {

        $ok = $this->enabled;

        $now = time();
        if ($ok && !is_null($this->enableFrom)) {
            $ok = $this->enableFrom <= $now;
        }
        if ($ok && !is_null($this->enableUntil)) {
            $ok = $this->enableUntil > $now;
        }

        return $ok;

    }

/**
 * Get a setting value.
 *
 * @param string $name    Name of setting
 * @param string $default Value to return if the setting does not exist (optional, default is an empty string)
 *
 * @return string Setting value
 */
    public function getSetting($name, $default = '')
    {

        if (array_key_exists($name, $this->settings)) {
            $value = $this->settings[$name];
        } else {
            $value = $default;
        }

        return $value;

    }

/**
 * Set a setting value.
 *
 * @param string $name  Name of setting
 * @param string $value Value to set, use an empty value to delete a setting (optional, default is null)
 */
    public function setSetting($name, $value = null)
    {

        $old_value = $this->getSetting($name);
        if ($value !== $old_value) {
            if (!empty($value)) {
                $this->settings[$name] = $value;
            } else {
                unset($this->settings[$name]);
            }
            $this->settingsChanged = true;
        }

    }

/**
 * Get an array of all setting values.
 *
 * @return array Associative array of setting values
 */
    public function getSettings()
    {

        return $this->settings;

    }

/**
 * Set an array of all setting values.
 *
 * @param array $settings  Associative array of setting values
 */
    public function setSettings($settings)
    {

        $this->settings = $settings;

    }

/**
 * Save setting values.
 *
 * @return boolean True if the settings were successfully saved
 */
    public function saveSettings()
    {

        if ($this->settingsChanged) {
            $ok = $this->save();
        } else {
            $ok = true;
        }

        return $ok;

    }

/**
 * Check if the Tool Settings service is supported.
 *
 * @return boolean True if this tool consumer supports the Tool Settings service
 */
    public function hasToolSettingsService()
    {

        $url = $this->getSetting('custom_system_setting_url');

        return !empty($url);

    }

/**
 * Get Tool Settings.
 *
 * @param boolean  $simple     True if all the simple media type is to be used (optional, default is true)
 *
 * @return mixed The array of settings if successful, otherwise false
 */
    public function getToolSettings($simple = true)
    {

        $url = $this->getSetting('custom_system_setting_url');
        $service = new Service\ToolSettings($this, $url, $simple);
        $response = $service->get();

        return $response;

    }

/**
 * Perform a Tool Settings service request.
 *
 * @param array    $settings   An associative array of settings (optional, default is none)
 *
 * @return boolean True if action was successful, otherwise false
 */
    public function setToolSettings($settings = array())
    {

        $url = $this->getSetting('custom_system_setting_url');
        $service = new Service\ToolSettings($this, $url);
        $response = $service->set($settings);

        return $response;

    }

/**
 * Add the OAuth signature to an LTI message.
 *
 * @param string  $url         URL for message request
 * @param string  $type        LTI message type
 * @param string  $version     LTI version
 * @param array   $params      Message parameters
 *
 * @return array Array of signed message parameters
 */
    public function signParameters($url, $type, $version, $params)
    {

        if (!empty($url)) {
// Check for query parameters which need to be included in the signature
            $queryParams = array();
            $queryString = parse_url($url, PHP_URL_QUERY);
            if (!is_null($queryString)) {
                $queryItems = explode('&', $queryString);
                foreach ($queryItems as $item) {
                    if (strpos($item, '=') !== false) {
                        list($name, $value) = explode('=', $item);
                        $queryParams[urldecode($name)] = urldecode($value);
                    } else {
                        $queryParams[urldecode($item)] = '';
                    }
                }
            }
            $params = $params + $queryParams;
// Add standard parameters
            $params['lti_version'] = $version;
            $params['lti_message_type'] = $type;
            $params['oauth_callback'] = 'about:blank';
// Add OAuth signature
            $hmacMethod = new OAuth\OAuthSignatureMethod_HMAC_SHA1();
            $consumer = new OAuth\OAuthConsumer($this->getKey(), $this->secret, null);
            $req = OAuth\OAuthRequest::from_consumer_and_token($consumer, null, 'POST', $url, $params);
            $req->sign_request($hmacMethod, $consumer, null);
            $params = $req->get_parameters();
// Remove parameters being passed on the query string
            foreach (array_keys($queryParams) as $name) {
                unset($params[$name]);
            }
        }

        return $params;

    }

/**
 * Add the OAuth signature to an array of message parameters or to a header string.
 *
 * @return mixed Array of signed message parameters or header string
 */
    public static function addSignature($endpoint, $consumerKey, $consumerSecret, $data, $method = 'POST', $type = null)
    {

        $params = array();
        if (is_array($data)) {
            $params = $data;
        }
// Check for query parameters which need to be included in the signature
        $queryParams = array();
        $queryString = parse_url($endpoint, PHP_URL_QUERY);
        if (!is_null($queryString)) {
            $queryItems = explode('&', $queryString);
            foreach ($queryItems as $item) {
                if (strpos($item, '=') !== false) {
                    list($name, $value) = explode('=', $item);
                    $queryParams[urldecode($name)] = urldecode($value);
                } else {
                    $queryParams[urldecode($item)] = '';
                }
            }
            $params = $params + $queryParams;
        }

        if (!is_array($data)) {
// Calculate body hash
            $hash = base64_encode(sha1($data, true));
            $params['oauth_body_hash'] = $hash;
        }

// Add OAuth signature
        $hmacMethod = new OAuth\OAuthSignatureMethod_HMAC_SHA1();
        $oauthConsumer = new OAuth\OAuthConsumer($consumerKey, $consumerSecret, null);
        $oauthReq = OAuth\OAuthRequest::from_consumer_and_token($oauthConsumer, null, $method, $endpoint, $params);
        $oauthReq->sign_request($hmacMethod, $oauthConsumer, null);
        $params = $oauthReq->get_parameters();
// Remove parameters being passed on the query string
        foreach (array_keys($queryParams) as $name) {
            unset($params[$name]);
        }

        if (!is_array($data)) {
            $header = $oauthReq->to_header();
            if (empty($data)) {
                if (!empty($type)) {
                    $header .= "\nAccept: {$type}";
                }
            } else if (isset($type)) {
                $header .= "\nContent-Type: {$type}";
                $header .= "\nContent-Length: " . strlen($data);
            }
            return $header;
        } else {
            return $params;
        }

    }

/**
 * Perform a service request
 *
 * @param object $service  Service object to be executed
 * @param string $method   HTTP action
 * @param string $format   Media type
 * @param mixed  $data     Array of parameters or body string
 *
 * @return HTTPMessage HTTP object containing request and response details
 */
    public function doServiceRequest($service, $method, $format, $data)
    {

        $header = ToolConsumer::addSignature($service->endpoint, $this->getKey(), $this->secret, $data, $method, $format);

// Connect to tool consumer
        $http = new HTTPMessage($service->endpoint, $method, $data, $header);
// Parse JSON response
        if ($http->send() && !empty($http->response)) {
            $http->responseJson = json_decode($http->response);
            $http->ok = !is_null($http->responseJson);
        }

        return $http;

    }

/**
 * Load the tool consumer from the database by its record ID.
 *
 * @param string          $id                The consumer key record ID
 * @param DataConnector   $dataConnector    Database connection object
 *
 * @return object ToolConsumer       The tool consumer object
 */
    public static function fromRecordId($id, $dataConnector)
    {

        $toolConsumer = new ToolConsumer(null, $dataConnector);

        $toolConsumer->initialize();
        $toolConsumer->setRecordId($id);
        if (!$dataConnector->loadToolConsumer($toolConsumer)) {
            $toolConsumer->initialize();
        }

        return $toolConsumer;

    }


###
###  PRIVATE METHOD
###

/**
 * Load the tool consumer from the database.
 *
 * @param string  $key        The consumer key value
 * @param boolean $autoEnable True if the consumer should be enabled (optional, default if false)
 *
 * @return boolean True if the consumer was successfully loaded
 */
    private function load($key, $autoEnable = false)
    {

        $this->key = $key;
        $ok = $this->dataConnector->loadToolConsumer($this);
        if (!$ok) {
            $this->enabled = $autoEnable;
        }

        return $ok;

    }

}
