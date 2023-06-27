<?php

namespace IMSGlobal\LTI\ToolProvider;

/**
 * Class to represent a tool consumer nonce
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.2
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class ConsumerNonce
{

/**
 * Maximum age nonce values will be retained for (in minutes).
 */
    const MAX_NONCE_AGE = 30;  // in minutes

/**
 * Date/time when the nonce value expires.
 *
 * @var int $expires
 */
    public $expires = null;

/**
 * Tool Consumer to which this nonce applies.
 *
 * @var ToolConsumer $consumer
 */
    private $consumer = null;
/**
 * Nonce value.
 *
 * @var string $value
 */
    private $value = null;

/**
 * Class constructor.
 *
 * @param ToolConsumer      $consumer Consumer object
 * @param string            $value    Nonce value (optional, default is null)
 */
    public function __construct($consumer, $value = null)
    {

        $this->consumer = $consumer;
        $this->value = $value;
        $this->expires = time() + (self::MAX_NONCE_AGE * 60);

    }

/**
 * Load a nonce value from the database.
 *
 * @return boolean True if the nonce value was successfully loaded
 */
    public function load()
    {

        return $this->consumer->getDataConnector()->loadConsumerNonce($this);

    }

/**
 * Save a nonce value in the database.
 *
 * @return boolean True if the nonce value was successfully saved
 */
    public function save()
    {

        return $this->consumer->getDataConnector()->saveConsumerNonce($this);

    }

/**
 * Get tool consumer.
 *
 * @return ToolConsumer Consumer for this nonce
 */
    public function getConsumer()
    {

        return $this->consumer;

    }

/**
 * Get outcome value.
 *
 * @return string Outcome value
 */
    public function getValue()
    {

        return $this->value;

    }

}
