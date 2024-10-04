<?php

namespace IMSGlobal\LTI\Profile;

/**

 * Class to represent a resource handler message object
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

#[\AllowDynamicProperties]
class Message
{

/**
 * LTI message type.
 *
 * @var string $type
 */
    public $type = null;
/**
 * Path to send message request to (used in conjunction with a base URL for the Tool Provider).
 *
 * @var string $path
 */
    public $path = null;
/**
 * Capabilities required by message.
 *
 * @var array $capabilities
 */
    public $capabilities = null;
/**
 * Variable parameters to accompany message request.
 *
 * @var array $variables
 */
    public $variables = null;
/**
 * Fixed parameters to accompany message request.
 *
 * @var array $constants
 */
    public $constants = null;


/**
 * Class constructor.
 *
 * @param string $type          LTI message type
 * @param string $path          Path to send message request to
 * @param array  $capabilities  Array of capabilities required by message
 * @param array  $variables     Array of variable parameters to accompany message request
 * @param array  $constants     Array of fixed parameters to accompany message request
 */
    function __construct($type, $path, $capabilities = array(), $variables = array(), $constants = array())
    {

        $this->type = $type;
        $this->path = $path;
        $this->capabilities = $capabilities;
        $this->variables = $variables;
        $this->constants = $constants;

    }

}
