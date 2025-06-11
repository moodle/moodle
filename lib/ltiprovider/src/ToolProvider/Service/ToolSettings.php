<?php

namespace IMSGlobal\LTI\ToolProvider\Service;

/**
 * Class to implement the Tool Settings service
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
#[\AllowDynamicProperties]
class ToolSettings extends Service
{

/**
 * Settings at current level mode.
 */
    const MODE_CURRENT_LEVEL = 1;
/**
 * Settings at all levels mode.
 */
    const MODE_ALL_LEVELS = 2;
/**
 * Settings with distinct names at all levels mode.
 */
    const MODE_DISTINCT_NAMES = 3;

/**
 * Names of LTI parameters to be retained in the consumer settings property.
 *
 * @var array $LEVEL_NAMES
 */
    private static $LEVEL_NAMES = array('ToolProxy' => 'system',
                                        'ToolProxyBinding' => 'context',
                                        'LtiLink' => 'link');

/**
 * The object to which the settings apply (ResourceLink, Context or ToolConsumer).
 *
 * @var object  $source
 */
    private $source;
/**
 * Whether to use the simple JSON format.
 *
 * @var boolean  $simple
 */
    private $simple;

/**
 * Class constructor.
 *
 * @param object       $source     The object to which the settings apply (ResourceLink, Context or ToolConsumer)
 * @param string       $endpoint   Service endpoint
 * @param boolean      $simple     True if the simple media type is to be used (optional, default is true)
 */
    public function __construct($source, $endpoint, $simple = true)
    {

        if (is_a($source, 'IMSGlobal\LTI\ToolProvider\ToolConsumer')) {
            $consumer = $source;
        } else {
            $consumer = $source->getConsumer();
        }
        if ($simple) {
            $mediaType = 'application/vnd.ims.lti.v2.toolsettings.simple+json';
        } else {
            $mediaType = 'application/vnd.ims.lti.v2.toolsettings+json';
        }
        parent::__construct($consumer, $endpoint, $mediaType);
        $this->source = $source;
        $this->simple = $simple;

    }

/**
 * Get the tool settings.
 *
 * @param int          $mode       Mode for request (optional, default is current level only)
 *
 * @return mixed The array of settings if successful, otherwise false
 */
    public function get($mode = self::MODE_CURRENT_LEVEL) {

        $parameter = array();
        if ($mode === self::MODE_ALL_LEVELS) {
            $parameter['bubble'] = 'all';
        } else if ($mode === self::MODE_DISTINCT_NAMES) {
            $parameter['bubble'] = 'distinct';
        }
        $http = $this->send('GET', $parameter);
        if (!$http->ok) {
            $response = false;
        } else if ($this->simple) {
            $response = json_decode($http->response, true);
        } else if (isset($http->responseJson->{'@graph'})) {
            $response = array();
            foreach ($http->responseJson->{'@graph'} as $level) {
                $settings = json_decode(json_encode($level->custom), true);
                unset($settings['@id']);
                $response[self::$LEVEL_NAMES[$level->{'@type'}]] = $settings;
            }
        }

        return $response;

    }

/**
 * Set the tool settings.
 *
 * @param array  $settings  An associative array of settings (optional, default is null)
 *
 * @return HTTPMessage HTTP object containing request and response details
 */
    public function set($settings) {

        if (!$this->simple) {
            if (is_a($this->source, 'ToolConsumer')) {
                $type = 'ToolProxy';
            } else if (is_a($this->source, 'ToolConsumer')) {
                $type = 'ToolProxyBinding';
            } else {
                $type = 'LtiLink';
            }
            $obj = new \stdClass();
            $obj->{'@context'} = 'http://purl.imsglobal.org/ctx/lti/v2/ToolSettings';
            $obj->{'@graph'} = array();
            $level = new \stdClass();
            $level->{'@type'} = $type;
            $level->{'@id'} = $this->endpoint;
            $level->{'custom'} = $settings;
            $obj->{'@graph'}[] = $level;
            $body = json_encode($obj);
        } else {
            $body = json_encode($settings);
        }

        $response = parent::send('PUT', null, $body);

        return $response->ok;

    }

}
