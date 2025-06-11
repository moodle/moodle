<?php

namespace IMSGlobal\LTI\ToolProvider;

/**
 * Class to represent a tool consumer resource link share
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
#[\AllowDynamicProperties]
class ResourceLinkShare
{

/**
 * Consumer key value.
 *
 * @var string $consumerKey
 */
    public $consumerKey = null;
/**
 * Resource link ID value.
 *
 * @var string $resourceLinkId
 */
    public $resourceLinkId = null;
/**
 * Title of sharing context.
 *
 * @var string $title
 */
    public $title = null;
/**
 * Whether sharing request is to be automatically approved on first use.
 *
 * @var boolean $approved
 */
    public $approved = null;

/**
 * Class constructor.
 */
    public function __construct()
    {
    }

}
