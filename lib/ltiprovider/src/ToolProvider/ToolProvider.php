<?php

namespace IMSGlobal\LTI\ToolProvider;

use IMSGlobal\LTI\Profile\Item;
use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use IMSGlobal\LTI\ToolProvider\MediaType;
use IMSGlobal\LTI\Profile;
use IMSGlobal\LTI\HTTPMessage;
use IMSGlobal\LTI\OAuth;

/**
 * Class to represent an LTI Tool Provider
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version  3.0.2
 * @license  GNU Lesser General Public License, version 3 (<http://www.gnu.org/licenses/lgpl.html>)
 */
class ToolProvider
{

/**
 * Default connection error message.
 */
    const CONNECTION_ERROR_MESSAGE = 'Sorry, there was an error connecting you to the application.';

/**
 * LTI version 1 for messages.
 */
    const LTI_VERSION1 = 'LTI-1p0';
/**
 * LTI version 2 for messages.
 */
    const LTI_VERSION2 = 'LTI-2p0';
/**
 * Use ID value only.
 */
    const ID_SCOPE_ID_ONLY = 0;
/**
 * Prefix an ID with the consumer key.
 */
    const ID_SCOPE_GLOBAL = 1;
/**
 * Prefix the ID with the consumer key and context ID.
 */
    const ID_SCOPE_CONTEXT = 2;
/**
 * Prefix the ID with the consumer key and resource ID.
 */
    const ID_SCOPE_RESOURCE = 3;
/**
 * Character used to separate each element of an ID.
 */
    const ID_SCOPE_SEPARATOR = ':';

/**
 * Permitted LTI versions for messages.
 */
    private static $LTI_VERSIONS = array(self::LTI_VERSION1, self::LTI_VERSION2);
/**
 * List of supported message types and associated class methods.
 */
    private static $MESSAGE_TYPES = array('basic-lti-launch-request' => 'onLaunch',
                                          'ContentItemSelectionRequest' => 'onContentItem',
                                          'ToolProxyRegistrationRequest' => 'register');
/**
 * List of supported message types and associated class methods
 *
 * @var array $METHOD_NAMES
 */
    private static $METHOD_NAMES = array('basic-lti-launch-request' => 'onLaunch',
                                         'ContentItemSelectionRequest' => 'onContentItem',
                                         'ToolProxyRegistrationRequest' => 'onRegister');
/**
 * Names of LTI parameters to be retained in the consumer settings property.
 *
 * @var array $LTI_CONSUMER_SETTING_NAMES
 */
    private static $LTI_CONSUMER_SETTING_NAMES = array('custom_tc_profile_url', 'custom_system_setting_url');
/**
 * Names of LTI parameters to be retained in the context settings property.
 *
 * @var array $LTI_CONTEXT_SETTING_NAMES
 */
    private static $LTI_CONTEXT_SETTING_NAMES = array('custom_context_setting_url',
                                                      'custom_lineitems_url', 'custom_results_url',
                                                      'custom_context_memberships_url');
/**
 * Names of LTI parameters to be retained in the resource link settings property.
 *
 * @var array $LTI_RESOURCE_LINK_SETTING_NAMES
 */
    private static $LTI_RESOURCE_LINK_SETTING_NAMES = array('lis_course_section_sourcedid', 'lis_result_sourcedid', 'lis_outcome_service_url',
                                                            'ext_ims_lis_basic_outcome_url', 'ext_ims_lis_resultvalue_sourcedids',
                                                            'ext_ims_lis_memberships_id', 'ext_ims_lis_memberships_url',
                                                            'ext_ims_lti_tool_setting', 'ext_ims_lti_tool_setting_id', 'ext_ims_lti_tool_setting_url',
                                                            'custom_link_setting_url',
                                                            'custom_lineitem_url', 'custom_result_url');
/**
 * Names of LTI custom parameter substitution variables (or capabilities) and their associated default message parameter names.
 *
 * @var array $CUSTOM_SUBSTITUTION_VARIABLES
 */
    private static $CUSTOM_SUBSTITUTION_VARIABLES = array('User.id' => 'user_id',
                                                          'User.image' => 'user_image',
                                                          'User.username' => 'username',
                                                          'User.scope.mentor' => 'role_scope_mentor',
                                                          'Membership.role' => 'roles',
                                                          'Person.sourcedId' => 'lis_person_sourcedid',
                                                          'Person.name.full' => 'lis_person_name_full',
                                                          'Person.name.family' => 'lis_person_name_family',
                                                          'Person.name.given' => 'lis_person_name_given',
                                                          'Person.email.primary' => 'lis_person_contact_email_primary',
                                                          'Context.id' => 'context_id',
                                                          'Context.type' => 'context_type',
                                                          'Context.title' => 'context_title',
                                                          'Context.label' => 'context_label',
                                                          'CourseOffering.sourcedId' => 'lis_course_offering_sourcedid',
                                                          'CourseSection.sourcedId' => 'lis_course_section_sourcedid',
                                                          'CourseSection.label' => 'context_label',
                                                          'CourseSection.title' => 'context_title',
                                                          'ResourceLink.id' => 'resource_link_id',
                                                          'ResourceLink.title' => 'resource_link_title',
                                                          'ResourceLink.description' => 'resource_link_description',
                                                          'Result.sourcedId' => 'lis_result_sourcedid',
                                                          'BasicOutcome.url' => 'lis_outcome_service_url',
                                                          'ToolConsumerProfile.url' => 'custom_tc_profile_url',
                                                          'ToolProxy.url' => 'tool_proxy_url',
                                                          'ToolProxy.custom.url' => 'custom_system_setting_url',
                                                          'ToolProxyBinding.custom.url' => 'custom_context_setting_url',
                                                          'LtiLink.custom.url' => 'custom_link_setting_url',
                                                          'LineItems.url' => 'custom_lineitems_url',
                                                          'LineItem.url' => 'custom_lineitem_url',
                                                          'Results.url' => 'custom_results_url',
                                                          'Result.url' => 'custom_result_url',
                                                          'ToolProxyBinding.memberships.url' => 'custom_context_memberships_url');


/**
 * True if the last request was successful.
 *
 * @var boolean $ok
 */
    public $ok = true;
/**
 * Tool Consumer object.
 *
 * @var ToolConsumer $consumer
 */
    public $consumer = null;
/**
 * Return URL provided by tool consumer.
 *
 * @var string $returnUrl
 */
    public $returnUrl = null;
/**
 * User object.
 *
 * @var User $user
 */
    public $user = null;
/**
 * Resource link object.
 *
 * @var ResourceLink $resourceLink
 */
    public $resourceLink = null;
/**
 * Context object.
 *
 * @var Context $context
 */
    public $context = null;
/**
 * Data connector object.
 *
 * @var DataConnector $dataConnector
 */
    public $dataConnector = null;
/**
 * Default email domain.
 *
 * @var string $defaultEmail
 */
    public $defaultEmail = '';
/**
 * Scope to use for user IDs.
 *
 * @var int $idScope
 */
    public $idScope = self::ID_SCOPE_ID_ONLY;
/**
 * Whether shared resource link arrangements are permitted.
 *
 * @var boolean $allowSharing
 */
    public $allowSharing = false;
/**
 * Message for last request processed
 *
 * @var string $message
 */
    public $message = self::CONNECTION_ERROR_MESSAGE;
/**
 * Error message for last request processed.
 *
 * @var string $reason
 */
    public $reason = null;
/**
 * Details for error message relating to last request processed.
 *
 * @var array $details
 */
    public $details = array();
/**
 * Base URL for tool provider service
 *
 * @var string $baseUrl
 */
  public $baseUrl = null;
/**
 * Vendor details
 *
 * @var Item $vendor
 */
  public $vendor = null;
/**
 * Product details
 *
 * @var Item $product
 */
  public $product = null;
/**
 * Services required by Tool Provider
 *
 * @var array $requiredServices
 */
  public $requiredServices = null;
/**
 * Optional services used by Tool Provider
 *
 * @var array $optionalServices
 */
  public $optionalServices = null;
/**
 * Resource handlers for Tool Provider
 *
 * @var array $resourceHandlers
 */
  public $resourceHandlers = null;

/**
 * URL to redirect user to on successful completion of the request.
 *
 * @var string $redirectUrl
 */
    protected $redirectUrl = null;
/**
 * URL to redirect user to on successful completion of the request.
 *
 * @var string $mediaTypes
 */
    protected $mediaTypes = null;
/**
 * URL to redirect user to on successful completion of the request.
 *
 * @var string $documentTargets
 */
    protected $documentTargets = null;
/**
 * HTML to be displayed on a successful completion of the request.
 *
 * @var string $output
 */
    protected $output = null;
/**
 * HTML to be displayed on an unsuccessful completion of the request and no return URL is available.
 *
 * @var string $errorOutput
 */
    protected $errorOutput = null;
/**
 * Whether debug messages explaining the cause of errors are to be returned to the tool consumer.
 *
 * @var boolean $debugMode
 */
    protected $debugMode = false;

/**
 * Callback functions for handling requests.
 *
 * @var array $callbackHandler
 */
    private $callbackHandler = null;
/**
 * LTI parameter constraints for auto validation checks.
 *
 * @var array $constraints
 */
    private $constraints = null;

/**
 * Class constructor
 *
 * @param DataConnector     $dataConnector    Object containing a database connection object
 */
    function __construct($dataConnector)
    {

        $this->constraints = array();
        $this->dataConnector = $dataConnector;
        $this->ok = !is_null($this->dataConnector);

// Set debug mode
        $this->debugMode = isset($_POST['custom_debug']) && (strtolower($_POST['custom_debug']) === 'true');

// Set return URL if available
        if (isset($_POST['launch_presentation_return_url'])) {
            $this->returnUrl = $_POST['launch_presentation_return_url'];
        } else if (isset($_POST['content_item_return_url'])) {
            $this->returnUrl = $_POST['content_item_return_url'];
        }
        $this->vendor = new Profile\Item();
        $this->product = new Profile\Item();
        $this->requiredServices = array();
        $this->optionalServices = array();
        $this->resourceHandlers = array();

    }

/**
 * Process an incoming request
 */
    public function handleRequest()
    {

        if ($this->ok) {
            if ($this->authenticate()) {
                $this->doCallback();
            }
        }
        $this->result();

    }

/**
 * Add a parameter constraint to be checked on launch
 *
 * @param string $name           Name of parameter to be checked
 * @param boolean $required      True if parameter is required (optional, default is true)
 * @param int $maxLength         Maximum permitted length of parameter value (optional, default is null)
 * @param array $messageTypes    Array of message types to which the constraint applies (optional, default is all)
 */
    public function setParameterConstraint($name, $required = true, $maxLength = null, $messageTypes = null)
    {

        $name = trim($name);
        if (strlen($name) > 0) {
            $this->constraints[$name] = array('required' => $required, 'max_length' => $maxLength, 'messages' => $messageTypes);
        }

    }

/**
 * Get an array of defined tool consumers
 *
 * @return array Array of ToolConsumer objects
 */
    public function getConsumers()
    {

        return $this->dataConnector->getToolConsumers();

    }

/**
 * Find an offered service based on a media type and HTTP action(s)
 *
 * @param string $format  Media type required
 * @param array  $methods Array of HTTP actions required
 *
 * @return object The service object
 */
    public function findService($format, $methods)
    {

        $found = false;
        $services = $this->consumer->profile->service_offered;
        if (is_array($services)) {
            $n = -1;
            foreach ($services as $service) {
                $n++;
                if (!is_array($service->format) || !in_array($format, $service->format)) {
                    continue;
                }
                $missing = array();
                foreach ($methods as $method) {
                    if (!is_array($service->action) || !in_array($method, $service->action)) {
                        $missing[] = $method;
                    }
                }
                $methods = $missing;
                if (count($methods) <= 0) {
                    $found = $service;
                    break;
                }
            }
        }

        return $found;

    }

/**
 * Send the tool proxy to the Tool Consumer
 *
 * @return boolean True if the tool proxy was accepted
 */
    public function doToolProxyService()
    {

// Create tool proxy
        $toolProxyService = $this->findService('application/vnd.ims.lti.v2.toolproxy+json', array('POST'));
        $secret = DataConnector::getRandomString(12);
        $toolProxy = new MediaType\ToolProxy($this, $toolProxyService, $secret);
        $http = $this->consumer->doServiceRequest($toolProxyService, 'POST', 'application/vnd.ims.lti.v2.toolproxy+json', json_encode($toolProxy));
        $ok = $http->ok && ($http->status == 201) && isset($http->responseJson->tool_proxy_guid) && (strlen($http->responseJson->tool_proxy_guid) > 0);
        if ($ok) {
            $this->consumer->setKey($http->responseJson->tool_proxy_guid);
            $this->consumer->secret = $toolProxy->security_contract->shared_secret;
            $this->consumer->toolProxy = json_encode($toolProxy);
            $this->consumer->save();
        }

        return $ok;

    }

/**
 * Get an array of fully qualified user roles
 *
 * @param mixed $roles  Comma-separated list of roles or array of roles
 *
 * @return array Array of roles
 */
    public static function parseRoles($roles)
    {

        if (!is_array($roles)) {
            $roles = explode(',', $roles);
        }
        $parsedRoles = array();
        foreach ($roles as $role) {
            $role = trim($role);
            if (!empty($role)) {
                if (substr($role, 0, 4) !== 'urn:') {
                    $role = 'urn:lti:role:ims/lis/' . $role;
                }
                $parsedRoles[] = $role;
            }
        }

        return $parsedRoles;

    }

/**
 * Generate a web page containing an auto-submitted form of parameters.
 *
 * @param string $url URL to which the form should be submitted
 * @param array $params Array of form parameters
 * @param string $target Name of target (optional)
 * @return string
 */
    public static function sendForm($url, $params, $target = '')
    {

        $page = <<< EOD
<html>
<head>
<title>IMS LTI message</title>
<script type="text/javascript">
//<![CDATA[
function doOnLoad() {
    document.forms[0].submit();
}

window.onload=doOnLoad;
//]]>
</script>
</head>
<body>
<form action="{$url}" method="post" target="" encType="application/x-www-form-urlencoded">

EOD;

        foreach($params as $key => $value ) {
            $key = htmlentities($key, ENT_COMPAT | ENT_HTML401, 'UTF-8');
            $value = htmlentities($value, ENT_COMPAT | ENT_HTML401, 'UTF-8');
            $page .= <<< EOD
    <input type="hidden" name="{$key}" value="{$value}" />

EOD;

        }

        $page .= <<< EOD
</form>
</body>
</html>
EOD;

        return $page;

    }

###
###    PROTECTED METHODS
###

/**
 * Process a valid launch request
 *
 * @return boolean True if no error
 */
    protected function onLaunch()
    {

        $this->onError();

    }

/**
 * Process a valid content-item request
 *
 * @return boolean True if no error
 */
    protected function onContentItem()
    {

        $this->onError();

    }

/**
 * Process a valid tool proxy registration request
 *
 * @return boolean True if no error
 */
    protected function onRegister() {

        $this->onError();

    }

/**
 * Process a response to an invalid request
 *
 * @return boolean True if no further error processing required
 */
    protected function onError()
    {

        $this->doCallback('onError');

    }

###
###    PRIVATE METHODS
###

/**
 * Call any callback function for the requested action.
 *
 * This function may set the redirect_url and output properties.
 *
 * @return boolean True if no error reported
 */
    private function doCallback($method = null)
    {

        $callback = $method;
        if (is_null($callback)) {
            $callback = self::$METHOD_NAMES[$_POST['lti_message_type']];
        }
        if (method_exists($this, $callback)) {
            $result = $this->$callback();
        } else if (is_null($method) && $this->ok) {
            $this->ok = false;
            $this->reason = "Message type not supported: {$_POST['lti_message_type']}";
        }
        if ($this->ok && ($_POST['lti_message_type'] == 'ToolProxyRegistrationRequest')) {
            $this->consumer->save();
        }

    }

/**
 * Perform the result of an action.
 *
 * This function may redirect the user to another URL rather than returning a value.
 *
 * @return string Output to be displayed (redirection, or display HTML or message)
 */
    private function result()
    {

        $ok = false;
        if (!$this->ok) {
            $ok = $this->onError();
        }
        if (!$ok) {
            if (!$this->ok) {

// If not valid, return an error message to the tool consumer if a return URL is provided
                if (!empty($this->returnUrl)) {
                    $errorUrl = $this->returnUrl;
                    if (strpos($errorUrl, '?') === false) {
                        $errorUrl .= '?';
                    } else {
                        $errorUrl .= '&';
                    }
                    if ($this->debugMode && !is_null($this->reason)) {
                        $errorUrl .= 'lti_errormsg=' . urlencode("Debug error: $this->reason");
                    } else {
                        $errorUrl .= 'lti_errormsg=' . urlencode($this->message);
                        if (!is_null($this->reason)) {
                            $errorUrl .= '&lti_errorlog=' . urlencode("Debug error: $this->reason");
                        }
                    }
                    if (!is_null($this->consumer) && isset($_POST['lti_message_type']) && ($_POST['lti_message_type'] === 'ContentItemSelectionRequest')) {
                        $formParams = array();
                        if (isset($_POST['data'])) {
                            $formParams['data'] = $_POST['data'];
                        }
                        $version = (isset($_POST['lti_version'])) ? $_POST['lti_version'] : self::LTI_VERSION1;
                        $formParams = $this->consumer->signParameters($errorUrl, 'ContentItemSelection', $version, $formParams);
                        $page = self::sendForm($errorUrl, $formParams);
                        echo $page;
                    } else {
                        header("Location: {$errorUrl}");
                    }
                    exit;
                } else {
                    if (!is_null($this->errorOutput)) {
                        echo $this->errorOutput;
                    } else if ($this->debugMode && !empty($this->reason)) {
                        echo "Debug error: {$this->reason}";
                    } else {
                        echo "Error: {$this->message}";
                    }
                }
            } else if (!is_null($this->redirectUrl)) {
                header("Location: {$this->redirectUrl}");
                exit;
            } else if (!is_null($this->output)) {
                echo $this->output;
            }
        }

    }

/**
 * Check the authenticity of the LTI launch request.
 *
 * The consumer, resource link and user objects will be initialised if the request is valid.
 *
 * @return boolean True if the request has been successfully validated.
 */
    private function authenticate()
    {

// Get the consumer
        $doSaveConsumer = false;
// Check all required launch parameters
        $this->ok = isset($_POST['lti_message_type']) && array_key_exists($_POST['lti_message_type'], self::$MESSAGE_TYPES);
        if (!$this->ok) {
            $this->reason = 'Invalid or missing lti_message_type parameter.';
        }
        if ($this->ok) {
            $this->ok = isset($_POST['lti_version']) && in_array($_POST['lti_version'], self::$LTI_VERSIONS);
            if (!$this->ok) {
                $this->reason = 'Invalid or missing lti_version parameter.';
            }
        }
        if ($this->ok) {
            if ($_POST['lti_message_type'] === 'basic-lti-launch-request') {
                $this->ok = isset($_POST['resource_link_id']) && (strlen(trim($_POST['resource_link_id'])) > 0);
                if (!$this->ok) {
                    $this->reason = 'Missing resource link ID.';
                }
            } else if ($_POST['lti_message_type'] === 'ContentItemSelectionRequest') {
                if (isset($_POST['accept_media_types']) && (strlen(trim($_POST['accept_media_types'])) > 0)) {
                    $mediaTypes = array_filter(explode(',', str_replace(' ', '', $_POST['accept_media_types'])), 'strlen');
                    $mediaTypes = array_unique($mediaTypes);
                    $this->ok = count($mediaTypes) > 0;
                    if (!$this->ok) {
                        $this->reason = 'No accept_media_types found.';
                    } else {
                        $this->mediaTypes = $mediaTypes;
                    }
                } else {
                    $this->ok = false;
                }
                if ($this->ok && isset($_POST['accept_presentation_document_targets']) && (strlen(trim($_POST['accept_presentation_document_targets'])) > 0)) {
                    $documentTargets = array_filter(explode(',', str_replace(' ', '', $_POST['accept_presentation_document_targets'])), 'strlen');
                    $documentTargets = array_unique($documentTargets);
                    $this->ok = count($documentTargets) > 0;
                    if (!$this->ok) {
                        $this->reason = 'Missing or empty accept_presentation_document_targets parameter.';
                    } else {
                        foreach ($documentTargets as $documentTarget) {
                            $this->ok = $this->checkValue($documentTarget, array('embed', 'frame', 'iframe', 'window', 'popup', 'overlay', 'none'),
                                 'Invalid value in accept_presentation_document_targets parameter: %s.');
                            if (!$this->ok) {
                                break;
                            }
                        }
                        if ($this->ok) {
                            $this->documentTargets = $documentTargets;
                        }
                    }
                } else {
                    $this->ok = false;
                }
                if ($this->ok) {
                    $this->ok = isset($_POST['content_item_return_url']) && (strlen(trim($_POST['content_item_return_url'])) > 0);
                    if (!$this->ok) {
                        $this->reason = 'Missing content_item_return_url parameter.';
                    }
                }
            } else if ($_POST['lti_message_type'] == 'ToolProxyRegistrationRequest') {
                $this->ok = ((isset($_POST['reg_key']) && (strlen(trim($_POST['reg_key'])) > 0)) &&
                             (isset($_POST['reg_password']) && (strlen(trim($_POST['reg_password'])) > 0)) &&
                             (isset($_POST['tc_profile_url']) && (strlen(trim($_POST['tc_profile_url'])) > 0)) &&
                             (isset($_POST['launch_presentation_return_url']) && (strlen(trim($_POST['launch_presentation_return_url'])) > 0)));
                if ($this->debugMode && !$this->ok) {
                    $this->reason = 'Missing message parameters.';
                }
            }
        }
        $now = time();
// Check consumer key
        if ($this->ok && ($_POST['lti_message_type'] != 'ToolProxyRegistrationRequest')) {
            $this->ok = isset($_POST['oauth_consumer_key']);
            if (!$this->ok) {
                $this->reason = 'Missing consumer key.';
            }
            if ($this->ok) {
                $this->consumer = new ToolConsumer($_POST['oauth_consumer_key'], $this->dataConnector);
                $this->ok = !is_null($this->consumer->created);
                if (!$this->ok) {
                    $this->reason = 'Invalid consumer key.';
                }
            }
            if ($this->ok) {
                $today = date('Y-m-d', $now);
                if (is_null($this->consumer->lastAccess)) {
                    $doSaveConsumer = true;
                } else {
                    $last = date('Y-m-d', $this->consumer->lastAccess);
                    $doSaveConsumer = $doSaveConsumer || ($last !== $today);
                }
                $this->consumer->last_access = $now;
                try {
                    $store = new OAuthDataStore($this);
                    $server = new OAuth\OAuthServer($store);
                    $method = new OAuth\OAuthSignatureMethod_HMAC_SHA1();
                    $server->add_signature_method($method);
                    $request = OAuth\OAuthRequest::from_request();
                    $res = $server->verify_request($request);
                } catch (\Exception $e) {
                    $this->ok = false;
                    if (empty($this->reason)) {
                        if ($this->debugMode) {
                            $consumer = new OAuth\OAuthConsumer($this->consumer->getKey(), $this->consumer->secret);
                            $signature = $request->build_signature($method, $consumer, false);
                            $this->reason = $e->getMessage();
                            if (empty($this->reason)) {
                                $this->reason = 'OAuth exception';
                            }
                            $this->details[] = 'Timestamp: ' . time();
                            $this->details[] = "Signature: {$signature}";
                            $this->details[] = "Base string: {$request->base_string}]";
                        } else {
                            $this->reason = 'OAuth signature check failed - perhaps an incorrect secret or timestamp.';
                        }
                    }
                }
            }
            if ($this->ok) {
                $today = date('Y-m-d', $now);
                if (is_null($this->consumer->lastAccess)) {
                    $doSaveConsumer = true;
                } else {
                    $last = date('Y-m-d', $this->consumer->lastAccess);
                    $doSaveConsumer = $doSaveConsumer || ($last !== $today);
                }
                $this->consumer->last_access = $now;
                if ($this->consumer->protected) {
                    if (!is_null($this->consumer->consumerGuid)) {
                        $this->ok = empty($_POST['tool_consumer_instance_guid']) ||
                             ($this->consumer->consumerGuid === $_POST['tool_consumer_instance_guid']);
                        if (!$this->ok) {
                            $this->reason = 'Request is from an invalid tool consumer.';
                        }
                    }
                }
                if ($this->ok) {
                    $this->ok = $this->consumer->enabled;
                    if (!$this->ok) {
                        $this->reason = 'Tool consumer has not been enabled by the tool provider.';
                    }
                }
                if ($this->ok) {
                    $this->ok = is_null($this->consumer->enableFrom) || ($this->consumer->enableFrom <= $now);
                    if ($this->ok) {
                        $this->ok = is_null($this->consumer->enableUntil) || ($this->consumer->enableUntil > $now);
                        if (!$this->ok) {
                            $this->reason = 'Tool consumer access has expired.';
                        }
                    } else {
                        $this->reason = 'Tool consumer access is not yet available.';
                    }
                }
            }

// Validate other message parameter values
            if ($this->ok) {
                if ($_POST['lti_message_type'] === 'ContentItemSelectionRequest') {
                    if (isset($_POST['accept_unsigned'])) {
                        $this->ok = $this->checkValue($_POST['accept_unsigned'], array('true', 'false'), 'Invalid value for accept_unsigned parameter: %s.');
                    }
                    if ($this->ok && isset($_POST['accept_multiple'])) {
                        $this->ok = $this->checkValue($_POST['accept_multiple'], array('true', 'false'), 'Invalid value for accept_multiple parameter: %s.');
                    }
                    if ($this->ok && isset($_POST['accept_copy_advice'])) {
                        $this->ok = $this->checkValue($_POST['accept_copy_advice'], array('true', 'false'), 'Invalid value for accept_copy_advice parameter: %s.');
                    }
                    if ($this->ok && isset($_POST['auto_create'])) {
                        $this->ok = $this->checkValue($_POST['auto_create'], array('true', 'false'), 'Invalid value for auto_create parameter: %s.');
                    }
                    if ($this->ok && isset($_POST['can_confirm'])) {
                        $this->ok = $this->checkValue($_POST['can_confirm'], array('true', 'false'), 'Invalid value for can_confirm parameter: %s.');
                    }
                } else if (isset($_POST['launch_presentation_document_target'])) {
                    $this->ok = $this->checkValue($_POST['launch_presentation_document_target'], array('embed', 'frame', 'iframe', 'window', 'popup', 'overlay'),
                         'Invalid value for launch_presentation_document_target parameter: %s.');
                }
            }
        }

        if ($this->ok && ($_POST['lti_message_type'] === 'ToolProxyRegistrationRequest')) {
            $this->ok = $_POST['lti_version'] == self::LTI_VERSION2;
            if (!$this->ok) {
                $this->reason = 'Invalid lti_version parameter';
            }
            if ($this->ok) {
                $http = new HTTPMessage($_POST['tc_profile_url'], 'GET', null, 'Accept: application/vnd.ims.lti.v2.toolconsumerprofile+json');
                $this->ok = $http->send();
                if (!$this->ok) {
                    $this->reason = 'Tool consumer profile not accessible.';
                } else {
                    $tcProfile = json_decode($http->response);
                    $this->ok = !is_null($tcProfile);
                    if (!$this->ok) {
                        $this->reason = 'Invalid JSON in tool consumer profile.';
                    }
                }
            }
// Check for required capabilities
            if ($this->ok) {
                $this->consumer = new ToolConsumer($_POST['reg_key'], $this->dataConnector);
                $this->consumer->profile = $tcProfile;
                $capabilities = $this->consumer->profile->capability_offered;
                $missing = array();
                foreach ($this->resourceHandlers as $resourceHandler) {
                    foreach ($resourceHandler->requiredMessages as $message) {
                        if (!in_array($message->type, $capabilities)) {
                            $missing[$message->type] = true;
                        }
                    }
                }
                foreach ($this->constraints as $name => $constraint) {
                    if ($constraint['required']) {
                        if (!in_array($name, $capabilities) && !in_array($name, array_flip($capabilities))) {
                            $missing[$name] = true;
                        }
                    }
                }
                if (!empty($missing)) {
                    ksort($missing);
                    $this->reason = 'Required capability not offered - \'' . implode('\', \'', array_keys($missing)) . '\'';
                    $this->ok = false;
                }
            }
// Check for required services
            if ($this->ok) {
                foreach ($this->requiredServices as $service) {
                    foreach ($service->formats as $format) {
                        if (!$this->findService($format, $service->actions)) {
                            if ($this->ok) {
                                $this->reason = 'Required service(s) not offered - ';
                                $this->ok = false;
                            } else {
                                $this->reason .= ', ';
                            }
                            $this->reason .= "'{$format}' [" . implode(', ', $service->actions) . ']';
                        }
                    }
                }
            }
            if ($this->ok) {
                if ($_POST['lti_message_type'] === 'ToolProxyRegistrationRequest') {
                    $this->consumer->profile = $tcProfile;
                    $this->consumer->secret = $_POST['reg_password'];
                    $this->consumer->ltiVersion = $_POST['lti_version'];
                    $this->consumer->name = $tcProfile->product_instance->service_owner->service_owner_name->default_value;
                    $this->consumer->consumerName = $this->consumer->name;
                    $this->consumer->consumerVersion = "{$tcProfile->product_instance->product_info->product_family->code}-{$tcProfile->product_instance->product_info->product_version}";
                    $this->consumer->consumerGuid = $tcProfile->product_instance->guid;
                    $this->consumer->enabled = true;
                    $this->consumer->protected = true;
                    $doSaveConsumer = true;
                }
            }
        } else if ($this->ok && !empty($_POST['custom_tc_profile_url']) && empty($this->consumer->profile)) {
            $http = new HTTPMessage($_POST['custom_tc_profile_url'], 'GET', null, 'Accept: application/vnd.ims.lti.v2.toolconsumerprofile+json');
            if ($http->send()) {
                $tcProfile = json_decode($http->response);
                if (!is_null($tcProfile)) {
                    $this->consumer->profile = $tcProfile;
                    $doSaveConsumer = true;
                }
            }
        }

// Validate message parameter constraints
        if ($this->ok) {
            $invalidParameters = array();
            foreach ($this->constraints as $name => $constraint) {
                if (empty($constraint['messages']) || in_array($_POST['lti_message_type'], $constraint['messages'])) {
                    $ok = true;
                    if ($constraint['required']) {
                        if (!isset($_POST[$name]) || (strlen(trim($_POST[$name])) <= 0)) {
                            $invalidParameters[] = "{$name} (missing)";
                            $ok = false;
                        }
                    }
                    if ($ok && !is_null($constraint['max_length']) && isset($_POST[$name])) {
                        if (strlen(trim($_POST[$name])) > $constraint['max_length']) {
                            $invalidParameters[] = "{$name} (too long)";
                        }
                    }
                }
            }
            if (count($invalidParameters) > 0) {
                $this->ok = false;
                if (empty($this->reason)) {
                    $this->reason = 'Invalid parameter(s): ' . implode(', ', $invalidParameters) . '.';
                }
            }
        }

        if ($this->ok) {

// Set the request context
            if (isset($_POST['context_id'])) {
                $this->context = Context::fromConsumer($this->consumer, trim($_POST['context_id']));
                $title = '';
                if (isset($_POST['context_title'])) {
                    $title = trim($_POST['context_title']);
                }
                if (empty($title)) {
                    $title = "Course {$this->context->getId()}";
                }
                if (isset($_POST['context_type'])) {
                    $this->context->type = trim($_POST['context_type']);
                }
                $this->context->title = $title;
            }

// Set the request resource link
            if (isset($_POST['resource_link_id'])) {
                $contentItemId = '';
                if (isset($_POST['custom_content_item_id'])) {
                    $contentItemId = $_POST['custom_content_item_id'];
                }
                $this->resourceLink = ResourceLink::fromConsumer($this->consumer, trim($_POST['resource_link_id']), $contentItemId);
                if (!empty($this->context)) {
                    $this->resourceLink->setContextId($this->context->getRecordId());
                }
                $title = '';
                if (isset($_POST['resource_link_title'])) {
                    $title = trim($_POST['resource_link_title']);
                }
                if (empty($title)) {
                    $title = "Resource {$this->resourceLink->getId()}";
                }
                $this->resourceLink->title = $title;
// Delete any existing custom parameters
                foreach ($this->consumer->getSettings() as $name => $value) {
                    if (strpos($name, 'custom_') === 0) {
                        $this->consumer->setSetting($name);
                        $doSaveConsumer = true;
                    }
                }
                if (!empty($this->context)) {
                    foreach ($this->context->getSettings() as $name => $value) {
                        if (strpos($name, 'custom_') === 0) {
                            $this->context->setSetting($name);
                        }
                    }
                }
                foreach ($this->resourceLink->getSettings() as $name => $value) {
                    if (strpos($name, 'custom_') === 0) {
                        $this->resourceLink->setSetting($name);
                    }
                }
// Save LTI parameters
                foreach (self::$LTI_CONSUMER_SETTING_NAMES as $name) {
                    if (isset($_POST[$name])) {
                        $this->consumer->setSetting($name, $_POST[$name]);
                    } else {
                        $this->consumer->setSetting($name);
                    }
                }
                if (!empty($this->context)) {
                    foreach (self::$LTI_CONTEXT_SETTING_NAMES as $name) {
                        if (isset($_POST[$name])) {
                            $this->context->setSetting($name, $_POST[$name]);
                        } else {
                            $this->context->setSetting($name);
                        }
                    }
                }
                foreach (self::$LTI_RESOURCE_LINK_SETTING_NAMES as $name) {
                    if (isset($_POST[$name])) {
                        $this->resourceLink->setSetting($name, $_POST[$name]);
                    } else {
                        $this->resourceLink->setSetting($name);
                    }
                }
// Save other custom parameters
                foreach ($_POST as $name => $value) {
                    if ((strpos($name, 'custom_') === 0) &&
                        !in_array($name, array_merge(self::$LTI_CONSUMER_SETTING_NAMES, self::$LTI_CONTEXT_SETTING_NAMES, self::$LTI_RESOURCE_LINK_SETTING_NAMES))) {
                        $this->resourceLink->setSetting($name, $value);
                    }
                }
            }

// Set the user instance
            $userId = '';
            if (isset($_POST['user_id'])) {
                $userId = trim($_POST['user_id']);
            }

            $this->user = User::fromResourceLink($this->resourceLink, $userId);

// Set the user name
            $firstname = (isset($_POST['lis_person_name_given'])) ? $_POST['lis_person_name_given'] : '';
            $lastname = (isset($_POST['lis_person_name_family'])) ? $_POST['lis_person_name_family'] : '';
            $fullname = (isset($_POST['lis_person_name_full'])) ? $_POST['lis_person_name_full'] : '';
            $this->user->setNames($firstname, $lastname, $fullname);

// Set the user email
            $email = (isset($_POST['lis_person_contact_email_primary'])) ? $_POST['lis_person_contact_email_primary'] : '';
            $this->user->setEmail($email, $this->defaultEmail);

// Set the user image URI
            if (isset($_POST['user_image'])) {
                $this->user->image = $_POST['user_image'];
            }

// Set the user roles
            if (isset($_POST['roles'])) {
                $this->user->roles = self::parseRoles($_POST['roles']);
            }

// Initialise the consumer and check for changes
            $this->consumer->defaultEmail = $this->defaultEmail;
            if ($this->consumer->ltiVersion !== $_POST['lti_version']) {
                $this->consumer->ltiVersion = $_POST['lti_version'];
                $doSaveConsumer = true;
            }
            if (isset($_POST['tool_consumer_instance_name'])) {
                if ($this->consumer->consumerName !== $_POST['tool_consumer_instance_name']) {
                    $this->consumer->consumerName = $_POST['tool_consumer_instance_name'];
                    $doSaveConsumer = true;
                }
            }
            if (isset($_POST['tool_consumer_info_product_family_code'])) {
                $version = $_POST['tool_consumer_info_product_family_code'];
                if (isset($_POST['tool_consumer_info_version'])) {
                    $version .= "-{$_POST['tool_consumer_info_version']}";
                }
// do not delete any existing consumer version if none is passed
                if ($this->consumer->consumerVersion !== $version) {
                    $this->consumer->consumerVersion = $version;
                    $doSaveConsumer = true;
                }
            } else if (isset($_POST['ext_lms']) && ($this->consumer->consumerName !== $_POST['ext_lms'])) {
                $this->consumer->consumerVersion = $_POST['ext_lms'];
                $doSaveConsumer = true;
            }
            if (isset($_POST['tool_consumer_instance_guid'])) {
                if (is_null($this->consumer->consumerGuid)) {
                    $this->consumer->consumerGuid = $_POST['tool_consumer_instance_guid'];
                    $doSaveConsumer = true;
                } else if (!$this->consumer->protected) {
                    $doSaveConsumer = ($this->consumer->consumerGuid !== $_POST['tool_consumer_instance_guid']);
                    if ($doSaveConsumer) {
                        $this->consumer->consumerGuid = $_POST['tool_consumer_instance_guid'];
                    }
                }
            }
            if (isset($_POST['launch_presentation_css_url'])) {
                if ($this->consumer->cssPath !== $_POST['launch_presentation_css_url']) {
                    $this->consumer->cssPath = $_POST['launch_presentation_css_url'];
                    $doSaveConsumer = true;
                }
            } else if (isset($_POST['ext_launch_presentation_css_url']) &&
                 ($this->consumer->cssPath !== $_POST['ext_launch_presentation_css_url'])) {
                $this->consumer->cssPath = $_POST['ext_launch_presentation_css_url'];
                $doSaveConsumer = true;
            } else if (!empty($this->consumer->cssPath)) {
                $this->consumer->cssPath = null;
                $doSaveConsumer = true;
            }
        }

// Persist changes to consumer
        if ($doSaveConsumer) {
            $this->consumer->save();
        }
        if ($this->ok && isset($this->context)) {
            $this->context->save();
        }
        if ($this->ok && isset($this->resourceLink)) {

// Check if a share arrangement is in place for this resource link
            $this->ok = $this->checkForShare();

// Persist changes to resource link
            $this->resourceLink->save();

// Save the user instance
            if (isset($_POST['lis_result_sourcedid'])) {
                if ($this->user->ltiResultSourcedId !== $_POST['lis_result_sourcedid']) {
                    $this->user->ltiResultSourcedId = $_POST['lis_result_sourcedid'];
                    $this->user->save();
                }
            } else if (!empty($this->user->ltiResultSourcedId)) {
                $this->user->ltiResultSourcedId = '';
                $this->user->save();
            }
        }

        return $this->ok;

    }

/**
 * Check if a share arrangement is in place.
 *
 * @return boolean True if no error is reported
 */
    private function checkForShare()
    {

        $ok = true;
        $doSaveResourceLink = true;

        $id = $this->resourceLink->primaryResourceLinkId;

        $shareRequest = isset($_POST['custom_share_key']) && !empty($_POST['custom_share_key']);
        if ($shareRequest) {
            if (!$this->allowSharing) {
                $ok = false;
                $this->reason = 'Your sharing request has been refused because sharing is not being permitted.';
            } else {
// Check if this is a new share key
                $shareKey = new ResourceLinkShareKey($this->resourceLink, $_POST['custom_share_key']);
                if (!is_null($shareKey->primaryConsumerKey) && !is_null($shareKey->primaryResourceLinkId)) {
// Update resource link with sharing primary resource link details
                    $key = $shareKey->primaryConsumerKey;
                    $id = $shareKey->primaryResourceLinkId;
                    $ok = ($key !== $this->consumer->getKey()) || ($id != $this->resourceLink->getId());
                    if ($ok) {
                        $this->resourceLink->primaryConsumerKey = $key;
                        $this->resourceLink->primaryResourceLinkId = $id;
                        $this->resourceLink->shareApproved = $shareKey->autoApprove;
                        $ok = $this->resourceLink->save();
                        if ($ok) {
                            $doSaveResourceLink = false;
                            $this->user->getResourceLink()->primaryConsumerKey = $key;
                            $this->user->getResourceLink()->primaryResourceLinkId = $id;
                            $this->user->getResourceLink()->shareApproved = $shareKey->autoApprove;
                            $this->user->getResourceLink()->updated = time();
// Remove share key
                            $shareKey->delete();
                        } else {
                            $this->reason = 'An error occurred initialising your share arrangement.';
                        }
                    } else {
                        $this->reason = 'It is not possible to share your resource link with yourself.';
                    }
                }
                if ($ok) {
                    $ok = !is_null($key);
                    if (!$ok) {
                        $this->reason = 'You have requested to share a resource link but none is available.';
                    } else {
                        $ok = (!is_null($this->user->getResourceLink()->shareApproved) && $this->user->getResourceLink()->shareApproved);
                        if (!$ok) {
                            $this->reason = 'Your share request is waiting to be approved.';
                        }
                    }
                }
            }
        } else {
// Check no share is in place
            $ok = is_null($id);
            if (!$ok) {
                $this->reason = 'You have not requested to share a resource link but an arrangement is currently in place.';
            }
        }

// Look up primary resource link
        if ($ok && !is_null($id)) {
            $consumer = new ToolConsumer($key, $this->dataConnector);
            $ok = !is_null($consumer->created);
            if ($ok) {
                $resourceLink = ResourceLink::fromConsumer($consumer, $id);
                $ok = !is_null($resourceLink->created);
            }
            if ($ok) {
                if ($doSaveResourceLink) {
                    $this->resourceLink->save();
                }
                $this->resourceLink = $resourceLink;
            } else {
                $this->reason = 'Unable to load resource link being shared.';
            }
        }

        return $ok;

    }

/**
 * Validate a parameter value from an array of permitted values.
 *
 * @return boolean True if value is valid
 */
    private function checkValue($value, $values, $reason)
    {

        $ok = in_array($value, $values);
        if (!$ok && !empty($reason)) {
            $this->reason = sprintf($reason, $value);
        }

        return $ok;

    }

}
