<?php

namespace IMSGlobal\LTI\ToolProvider\MediaType;

use IMSGlobal\LTI\Profile\ServiceDefinition;
use IMSGlobal\LTI\ToolProvider\ToolProvider;

/**
 * Class to represent an LTI Tool Proxy media type
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version  3.0.0
 * @license  GNU Lesser General Public License, version 3 (<http://www.gnu.org/licenses/lgpl.html>)
 */
#[\AllowDynamicProperties]
class ToolProxy
{

/**
 * Class constructor.
 *
 * @param ToolProvider $toolProvider   Tool Provider object
 * @param ServiceDefinition $toolProxyService  Tool Proxy service
 * @param string $secret  Shared secret
 */
    function __construct($toolProvider, $toolProxyService, $secret)
    {

        $contexts = array();

        $this->{'@context'} = array_merge(array('http://purl.imsglobal.org/ctx/lti/v2/ToolProxy'), $contexts);
        $this->{'@type'} = 'ToolProxy';
        $this->{'@id'} = "{$toolProxyService->endpoint}";
        $this->lti_version = 'LTI-2p0';
        $this->tool_consumer_profile = $toolProvider->consumer->profile->{'@id'};
        $this->tool_profile = new ToolProfile($toolProvider);
        $this->security_contract = new SecurityContract($toolProvider, $secret);

    }

}
