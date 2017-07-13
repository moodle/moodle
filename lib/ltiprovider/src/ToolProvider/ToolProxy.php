<?php

namespace IMSGlobal\LTI\ToolProvider;

use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use IMSGlobal\LTI\ToolProvider\MediaType;

/**
 * Class to represent an LTI Tool Proxy
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version  3.0.2
 * @license  GNU Lesser General Public License, version 3 (<http://www.gnu.org/licenses/lgpl.html>)
 */
class ToolProxy
{

/**
 * Local id of tool consumer.
 *
 * @var string $id
 */
    public $id = null;

/**
 * Tool Consumer for this tool proxy.
 *
 * @var ToolConsumer $consumer
 */
    private $consumer = null;
/**
 * Tool Consumer ID for this tool proxy.
 *
 * @var int $consumerId
 */
    private $consumerId = null;
/**
 * Consumer ID value.
 *
 * @var int $id
 */
    private $recordId = null;
/**
 * Data connector object.
 *
 * @var DataConnector $dataConnector
 */
    private $dataConnector = null;
/**
 * Tool Proxy document.
 *
 * @var MediaType\ToolProxy $toolProxy
 */
    private $toolProxy = null;

/**
 * Class constructor.
 *
 * @param DataConnector   $dataConnector   Data connector
 * @param string                        $id              Tool Proxy ID (optional, default is null)
 */
    public function __construct($dataConnector, $id = null)
    {

        $this->initialize();
        $this->dataConnector = $dataConnector;
        if (!empty($id)) {
            $this->load($id);
        } else {
            $this->recordId = DataConnector::getRandomString(32);
        }

    }

/**
 * Initialise the tool proxy.
 */
    public function initialize()
    {

        $this->id = null;
        $this->recordId = null;
        $this->toolProxy = null;
        $this->created = null;
        $this->updated = null;

    }

/**
 * Initialise the tool proxy.
 *
 * Pseudonym for initialize().
 */
    public function initialise()
    {

        $this->initialize();

    }

/**
 * Get the tool proxy record ID.
 *
 * @return int Tool Proxy record ID value
 */
    public function getRecordId()
    {

        return $this->recordId;

    }

/**
 * Sets the tool proxy record ID.
 *
 * @param int $recordId  Tool Proxy record ID value
 */
    public function setRecordId($recordId)
    {

        $this->recordId = $recordId;

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
 * Get the data connector.
 *
 * @return DataConnector  Data connector object
 */
    public function getDataConnector()
    {

        return $this->dataConnector;

    }


###
###  PRIVATE METHOD
###

/**
 * Load the tool proxy from the database.
 *
 * @param string  $id        The tool proxy id value
 *
 * @return boolean True if the tool proxy was successfully loaded
 */
    private function load($id)
    {

        $this->initialize();
        $this->id = $id;
        $ok = $this->dataConnector->loadToolProxy($this);
        if (!$ok) {
            $this->enabled = $autoEnable;
        }

        return $ok;

    }

}
