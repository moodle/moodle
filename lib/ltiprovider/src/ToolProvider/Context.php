<?php

namespace IMSGlobal\LTI\ToolProvider;

use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use IMSGlobal\LTI\ToolProvider\Service;

/**
 * Class to represent a tool consumer context
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.2
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class Context
{

/**
 * Context ID as supplied in the last connection request.
 *
 * @var string $ltiContextId
 */
    public $ltiContextId = null;
/**
 * Context title.
 *
 * @var string $title
 */
    public $title = null;
/**
 * Setting values (LTI parameters, custom parameters and local parameters).
 *
 * @var array $settings
 */
    public $settings = null;
/**
 * Context type.
 *
 * @var string $type
 */
    public $type = null;
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
 * Tool Consumer for this context.
 *
 * @var ToolConsumer $consumer
 */
    private $consumer = null;
/**
 * Tool Consumer ID for this context.
 *
 * @var int $consumerId
 */
    private $consumerId = null;
/**
 * ID for this context.
 *
 * @var int $id
 */
    private $id = null;
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
 */
    public function __construct()
    {

        $this->initialize();

    }

/**
 * Initialise the context.
 */
    public function initialize()
    {

        $this->title = '';
        $this->settings = array();
        $this->created = null;
        $this->updated = null;

    }

/**
 * Initialise the context.
 *
 * Pseudonym for initialize().
 */
    public function initialise()
    {

        $this->initialize();

    }

/**
 * Save the context to the database.
 *
 * @return boolean True if the context was successfully saved.
 */
    public function save()
    {

        $ok = $this->getDataConnector()->saveContext($this);
        if ($ok) {
            $this->settingsChanged = false;
        }

        return $ok;

    }

/**
 * Delete the context from the database.
 *
 * @return boolean True if the context was successfully deleted.
 */
    public function delete()
    {

        return $this->getDataConnector()->deleteContext($this);

    }

/**
 * Get tool consumer.
 *
 * @return ToolConsumer Tool consumer object for this context.
 */
    public function getConsumer()
    {

        if (is_null($this->consumer)) {
            $this->consumer = ToolConsumer::fromRecordId($this->consumerId, $this->getDataConnector());
        }

        return $this->consumer;

    }
/**
 * Set tool consumer ID.
 *
 * @param int $consumerId  Tool Consumer ID for this resource link.
 */
    public function setConsumerId($consumerId)
    {

        $this->consumer = null;
        $this->consumerId = $consumerId;

    }

/**
 * Get tool consumer key.
 *
 * @return string Consumer key value for this context.
 */
    public function getKey()
    {

        return $this->getConsumer()->getKey();

    }

/**
 * Get context ID.
 *
 * @return string ID for this context.
 */
    public function getId()
    {

        return $this->ltiContextId;

    }

/**
 * Get the context record ID.
 *
 * @return int Context record ID value
 */
    public function getRecordId()
    {

        return $this->id;

    }

/**
 * Sets the context record ID.
 *
 * @return int $id  Context record ID value
 */
    public function setRecordId($id)
    {

        $this->id = $id;

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
 * @param array $settings Associative array of setting values
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
 * @return boolean True if this context supports the Tool Settings service
 */
    public function hasToolSettingsService()
    {

        $url = $this->getSetting('custom_context_setting_url');

        return !empty($url);

    }

/**
 * Get Tool Settings.
 *
 * @param int      $mode       Mode for request (optional, default is current level only)
 * @param boolean  $simple     True if all the simple media type is to be used (optional, default is true)
 *
 * @return mixed The array of settings if successful, otherwise false
 */
    public function getToolSettings($mode = Service\ToolSettings::MODE_CURRENT_LEVEL, $simple = true)
    {

        $url = $this->getSetting('custom_context_setting_url');
        $service = new Service\ToolSettings($this, $url, $simple);
        $response = $service->get($mode);

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

        $url = $this->getSetting('custom_context_setting_url');
        $service = new Service\ToolSettings($this, $url);
        $response = $service->set($settings);

        return $response;

    }

/**
 * Check if the Membership service is supported.
 *
 * @return boolean True if this context supports the Membership service
 */
    public function hasMembershipService()
    {

        $url = $this->getSetting('custom_context_memberships_url');

        return !empty($url);

    }

/**
 * Get Memberships.
 *
 * @return mixed The array of User objects if successful, otherwise false
 */
    public function getMembership()
    {

        $url = $this->getSetting('custom_context_memberships_url');
        $service = new Service\Membership($this, $url);
        $response = $service->get();

        return $response;

    }

/**
 * Load the context from the database.
 *
 * @param int             $id               Record ID of context
 * @param DataConnector   $dataConnector    Database connection object
 *
 * @return Context    Context object
 */
    public static function fromRecordId($id, $dataConnector)
    {

        $context = new Context();
        $context->dataConnector = $dataConnector;
        $context->load($id);

        return $context;

    }

/**
 * Class constructor from consumer.
 *
 * @param ToolConsumer $consumer Consumer instance
 * @param string $ltiContextId LTI Context ID value
 * @return Context
 */
    public static function fromConsumer($consumer, $ltiContextId)
    {

        $context = new Context();
        $context->consumer = $consumer;
        $context->dataConnector = $consumer->getDataConnector();
        $context->ltiContextId = $ltiContextId;
        if (!empty($ltiContextId)) {
            $context->load();
        }

        return $context;

    }

###
###  PRIVATE METHODS
###

/**
 * Load the context from the database.
 *
 * @param int $id     Record ID of context (optional, default is null)
 *
 * @return boolean True if context was successfully loaded
 */
    private function load($id = null)
    {

        $this->initialize();
        $this->id = $id;
        return $this->getDataConnector()->loadContext($this);

    }

}
