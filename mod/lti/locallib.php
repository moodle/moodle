<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file contains the library of functions and constants for the lti module
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// TODO: Switch to core oauthlib once implemented - MDL-30149.
use moodle\mod\lti as lti;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

global $CFG;
require_once($CFG->dirroot.'/mod/lti/OAuth.php');
require_once($CFG->libdir.'/weblib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/lti/TrivialStore.php');

define('LTI_URL_DOMAIN_REGEX', '/(?:https?:\/\/)?(?:www\.)?([^\/]+)(?:\/|$)/i');

define('LTI_LAUNCH_CONTAINER_DEFAULT', 1);
define('LTI_LAUNCH_CONTAINER_EMBED', 2);
define('LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS', 3);
define('LTI_LAUNCH_CONTAINER_WINDOW', 4);
define('LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW', 5);

define('LTI_TOOL_STATE_ANY', 0);
define('LTI_TOOL_STATE_CONFIGURED', 1);
define('LTI_TOOL_STATE_PENDING', 2);
define('LTI_TOOL_STATE_REJECTED', 3);
define('LTI_TOOL_PROXY_TAB', 4);

define('LTI_TOOL_PROXY_STATE_CONFIGURED', 1);
define('LTI_TOOL_PROXY_STATE_PENDING', 2);
define('LTI_TOOL_PROXY_STATE_ACCEPTED', 3);
define('LTI_TOOL_PROXY_STATE_REJECTED', 4);

define('LTI_SETTING_NEVER', 0);
define('LTI_SETTING_ALWAYS', 1);
define('LTI_SETTING_DELEGATE', 2);

define('LTI_COURSEVISIBLE_NO', 0);
define('LTI_COURSEVISIBLE_PRECONFIGURED', 1);
define('LTI_COURSEVISIBLE_ACTIVITYCHOOSER', 2);

define('LTI_VERSION_1', 'LTI-1p0');
define('LTI_VERSION_2', 'LTI-2p0');
define('LTI_VERSION_1P3', '1.3.0');
define('LTI_RSA_KEY', 'RSA_KEY');
define('LTI_JWK_KEYSET', 'JWK_KEYSET');

define('LTI_DEFAULT_ORGID_SITEID', 'SITEID');
define('LTI_DEFAULT_ORGID_SITEHOST', 'SITEHOST');

define('LTI_ACCESS_TOKEN_LIFE', 3600);

// Standard prefix for JWT claims.
define('LTI_JWT_CLAIM_PREFIX', 'https://purl.imsglobal.org/spec/lti');

/**
 * Return the mapping for standard message types to JWT message_type claim.
 *
 * @return array
 */
function lti_get_jwt_message_type_mapping() {
    return array(
        'basic-lti-launch-request' => 'LtiResourceLinkRequest',
        'ContentItemSelectionRequest' => 'LtiDeepLinkingRequest',
        'LtiDeepLinkingResponse' => 'ContentItemSelection',
    );
}

/**
 * Return the mapping for standard message parameters to JWT claim.
 *
 * @return array
 */
function lti_get_jwt_claim_mapping() {
    return array(
        'accept_copy_advice' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'accept_copy_advice',
            'isarray' => false
        ],
        'accept_media_types' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'accept_media_types',
            'isarray' => true
        ],
        'accept_multiple' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'accept_multiple',
            'isarray' => false
        ],
        'accept_presentation_document_targets' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'accept_presentation_document_targets',
            'isarray' => true
        ],
        'accept_types' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'accept_types',
            'isarray' => true
        ],
        'accept_unsigned' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'accept_unsigned',
            'isarray' => false
        ],
        'auto_create' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'auto_create',
            'isarray' => false
        ],
        'can_confirm' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'can_confirm',
            'isarray' => false
        ],
        'content_item_return_url' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'deep_link_return_url',
            'isarray' => false
        ],
        'content_items' => [
            'suffix' => 'dl',
            'group' => '',
            'claim' => 'content_items',
            'isarray' => true
        ],
        'data' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'data',
            'isarray' => false
        ],
        'text' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'text',
            'isarray' => false
        ],
        'title' => [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'title',
            'isarray' => false
        ],
        'lti_msg' => [
            'suffix' => 'dl',
            'group' => '',
            'claim' => 'msg',
            'isarray' => false
        ],
        'lti_log' => [
            'suffix' => 'dl',
            'group' => '',
            'claim' => 'log',
            'isarray' => false
        ],
        'lti_errormsg' => [
            'suffix' => 'dl',
            'group' => '',
            'claim' => 'errormsg',
            'isarray' => false
        ],
        'lti_errorlog' => [
            'suffix' => 'dl',
            'group' => '',
            'claim' => 'errorlog',
            'isarray' => false
        ],
        'context_id' => [
            'suffix' => '',
            'group' => 'context',
            'claim' => 'id',
            'isarray' => false
        ],
        'context_label' => [
            'suffix' => '',
            'group' => 'context',
            'claim' => 'label',
            'isarray' => false
        ],
        'context_title' => [
            'suffix' => '',
            'group' => 'context',
            'claim' => 'title',
            'isarray' => false
        ],
        'context_type' => [
            'suffix' => '',
            'group' => 'context',
            'claim' => 'type',
            'isarray' => true
        ],
        'lis_course_offering_sourcedid' => [
            'suffix' => '',
            'group' => 'lis',
            'claim' => 'course_offering_sourcedid',
            'isarray' => false
        ],
        'lis_course_section_sourcedid' => [
            'suffix' => '',
            'group' => 'lis',
            'claim' => 'course_section_sourcedid',
            'isarray' => false
        ],
        'launch_presentation_css_url' => [
            'suffix' => '',
            'group' => 'launch_presentation',
            'claim' => 'css_url',
            'isarray' => false
        ],
        'launch_presentation_document_target' => [
            'suffix' => '',
            'group' => 'launch_presentation',
            'claim' => 'document_target',
            'isarray' => false
        ],
        'launch_presentation_height' => [
            'suffix' => '',
            'group' => 'launch_presentation',
            'claim' => 'height',
            'isarray' => false
        ],
        'launch_presentation_locale' => [
            'suffix' => '',
            'group' => 'launch_presentation',
            'claim' => 'locale',
            'isarray' => false
        ],
        'launch_presentation_return_url' => [
            'suffix' => '',
            'group' => 'launch_presentation',
            'claim' => 'return_url',
            'isarray' => false
        ],
        'launch_presentation_width' => [
            'suffix' => '',
            'group' => 'launch_presentation',
            'claim' => 'width',
            'isarray' => false
        ],
        'lis_person_contact_email_primary' => [
            'suffix' => '',
            'group' => null,
            'claim' => 'email',
            'isarray' => false
        ],
        'lis_person_name_family' => [
            'suffix' => '',
            'group' => null,
            'claim' => 'family_name',
            'isarray' => false
        ],
        'lis_person_name_full' => [
            'suffix' => '',
            'group' => null,
            'claim' => 'name',
            'isarray' => false
        ],
        'lis_person_name_given' => [
            'suffix' => '',
            'group' => null,
            'claim' => 'given_name',
            'isarray' => false
        ],
        'lis_person_sourcedid' => [
            'suffix' => '',
            'group' => 'lis',
            'claim' => 'person_sourcedid',
            'isarray' => false
        ],
        'user_id' => [
            'suffix' => '',
            'group' => null,
            'claim' => 'sub',
            'isarray' => false
        ],
        'user_image' => [
            'suffix' => '',
            'group' => null,
            'claim' => 'picture',
            'isarray' => false
        ],
        'roles' => [
            'suffix' => '',
            'group' => '',
            'claim' => 'roles',
            'isarray' => true
        ],
        'role_scope_mentor' => [
            'suffix' => '',
            'group' => '',
            'claim' => 'role_scope_mentor',
            'isarray' => false
        ],
        'deployment_id' => [
            'suffix' => '',
            'group' => '',
            'claim' => 'deployment_id',
            'isarray' => false
        ],
        'lti_message_type' => [
            'suffix' => '',
            'group' => '',
            'claim' => 'message_type',
            'isarray' => false
        ],
        'lti_version' => [
            'suffix' => '',
            'group' => '',
            'claim' => 'version',
            'isarray' => false
        ],
        'resource_link_description' => [
            'suffix' => '',
            'group' => 'resource_link',
            'claim' => 'description',
            'isarray' => false
        ],
        'resource_link_id' => [
            'suffix' => '',
            'group' => 'resource_link',
            'claim' => 'id',
            'isarray' => false
        ],
        'resource_link_title' => [
            'suffix' => '',
            'group' => 'resource_link',
            'claim' => 'title',
            'isarray' => false
        ],
        'tool_consumer_info_product_family_code' => [
            'suffix' => '',
            'group' => 'tool_platform',
            'claim' => 'family_code',
            'isarray' => false
        ],
        'tool_consumer_info_version' => [
            'suffix' => '',
            'group' => 'tool_platform',
            'claim' => 'version',
            'isarray' => false
        ],
        'tool_consumer_instance_contact_email' => [
            'suffix' => '',
            'group' => 'tool_platform',
            'claim' => 'contact_email',
            'isarray' => false
        ],
        'tool_consumer_instance_description' => [
            'suffix' => '',
            'group' => 'tool_platform',
            'claim' => 'description',
            'isarray' => false
        ],
        'tool_consumer_instance_guid' => [
            'suffix' => '',
            'group' => 'tool_platform',
            'claim' => 'guid',
            'isarray' => false
        ],
        'tool_consumer_instance_name' => [
            'suffix' => '',
            'group' => 'tool_platform',
            'claim' => 'name',
            'isarray' => false
        ],
        'tool_consumer_instance_url' => [
            'suffix' => '',
            'group' => 'tool_platform',
            'claim' => 'url',
            'isarray' => false
        ],
        'custom_context_memberships_url' => [
            'suffix' => 'nrps',
            'group' => 'namesroleservice',
            'claim' => 'context_memberships_url',
            'isarray' => false
        ],
        'custom_context_memberships_versions' => [
            'suffix' => 'nrps',
            'group' => 'namesroleservice',
            'claim' => 'service_versions',
            'isarray' => true
        ],
        'custom_gradebookservices_scope' => [
            'suffix' => 'ags',
            'group' => 'endpoint',
            'claim' => 'scope',
            'isarray' => true
        ],
        'custom_lineitems_url' => [
            'suffix' => 'ags',
            'group' => 'endpoint',
            'claim' => 'lineitems',
            'isarray' => false
        ],
        'custom_lineitem_url' => [
            'suffix' => 'ags',
            'group' => 'endpoint',
            'claim' => 'lineitem',
            'isarray' => false
        ],
        'custom_results_url' => [
            'suffix' => 'ags',
            'group' => 'endpoint',
            'claim' => 'results',
            'isarray' => false
        ],
        'custom_result_url' => [
            'suffix' => 'ags',
            'group' => 'endpoint',
            'claim' => 'result',
            'isarray' => false
        ],
        'custom_scores_url' => [
            'suffix' => 'ags',
            'group' => 'endpoint',
            'claim' => 'scores',
            'isarray' => false
        ],
        'custom_score_url' => [
            'suffix' => 'ags',
            'group' => 'endpoint',
            'claim' => 'score',
            'isarray' => false
        ],
        'lis_outcome_service_url' => [
            'suffix' => 'bos',
            'group' => 'basicoutcomesservice',
            'claim' => 'lis_outcome_service_url',
            'isarray' => false
        ],
        'lis_result_sourcedid' => [
            'suffix' => 'bos',
            'group' => 'basicoutcomesservice',
            'claim' => 'lis_result_sourcedid',
            'isarray' => false
        ],
    );
}

/**
 * Return the type of the instance, using domain matching if no explicit type is set.
 *
 * @param  object $instance the external tool activity settings
 * @return object|null
 * @since  Moodle 3.9
 */
function lti_get_instance_type(object $instance) : ?object {
    if (empty($instance->typeid)) {
        if (!$tool = lti_get_tool_by_url_match($instance->toolurl, $instance->course)) {
            $tool = lti_get_tool_by_url_match($instance->securetoolurl,  $instance->course);
        }
        return $tool;
    }
    return lti_get_type($instance->typeid);
}

/**
 * Return the launch data required for opening the external tool.
 *
 * @param  stdClass $instance the external tool activity settings
 * @param  string $nonce  the nonce value to use (applies to LTI 1.3 only)
 * @return array the endpoint URL and parameters (including the signature)
 * @since  Moodle 3.0
 */
function lti_get_launch_data($instance, $nonce = '') {
    global $PAGE, $CFG, $USER;

    $tool = lti_get_instance_type($instance);
    if ($tool) {
        $typeid = $tool->id;
        $ltiversion = $tool->ltiversion;
    } else {
        $typeid = null;
        $ltiversion = LTI_VERSION_1;
    }

    if ($typeid) {
        $typeconfig = lti_get_type_config($typeid);
    } else {
        // There is no admin configuration for this tool. Use configuration in the lti instance record plus some defaults.
        $typeconfig = (array)$instance;

        $typeconfig['sendname'] = $instance->instructorchoicesendname;
        $typeconfig['sendemailaddr'] = $instance->instructorchoicesendemailaddr;
        $typeconfig['customparameters'] = $instance->instructorcustomparameters;
        $typeconfig['acceptgrades'] = $instance->instructorchoiceacceptgrades;
        $typeconfig['allowroster'] = $instance->instructorchoiceallowroster;
        $typeconfig['forcessl'] = '0';
    }

    if (isset($tool->toolproxyid)) {
        $toolproxy = lti_get_tool_proxy($tool->toolproxyid);
        $key = $toolproxy->guid;
        $secret = $toolproxy->secret;
    } else {
        $toolproxy = null;
        if (!empty($instance->resourcekey)) {
            $key = $instance->resourcekey;
        } else if ($ltiversion === LTI_VERSION_1P3) {
            $key = $tool->clientid;
        } else if (!empty($typeconfig['resourcekey'])) {
            $key = $typeconfig['resourcekey'];
        } else {
            $key = '';
        }
        if (!empty($instance->password)) {
            $secret = $instance->password;
        } else if (!empty($typeconfig['password'])) {
            $secret = $typeconfig['password'];
        } else {
            $secret = '';
        }
    }

    $endpoint = !empty($instance->toolurl) ? $instance->toolurl : $typeconfig['toolurl'];
    $endpoint = trim($endpoint);

    // If the current request is using SSL and a secure tool URL is specified, use it.
    if (lti_request_is_using_ssl() && !empty($instance->securetoolurl)) {
        $endpoint = trim($instance->securetoolurl);
    }

    // If SSL is forced, use the secure tool url if specified. Otherwise, make sure https is on the normal launch URL.
    if (isset($typeconfig['forcessl']) && ($typeconfig['forcessl'] == '1')) {
        if (!empty($instance->securetoolurl)) {
            $endpoint = trim($instance->securetoolurl);
        }

        $endpoint = lti_ensure_url_is_https($endpoint);
    } else {
        if (!strstr($endpoint, '://')) {
            $endpoint = 'http://' . $endpoint;
        }
    }

    $orgid = lti_get_organizationid($typeconfig);

    $course = $PAGE->course;
    $islti2 = isset($tool->toolproxyid);
    $allparams = lti_build_request($instance, $typeconfig, $course, $typeid, $islti2);
    if ($islti2) {
        $requestparams = lti_build_request_lti2($tool, $allparams);
    } else {
        $requestparams = $allparams;
    }
    $requestparams = array_merge($requestparams, lti_build_standard_message($instance, $orgid, $ltiversion));
    $customstr = '';
    if (isset($typeconfig['customparameters'])) {
        $customstr = $typeconfig['customparameters'];
    }
    $requestparams = array_merge($requestparams, lti_build_custom_parameters($toolproxy, $tool, $instance, $allparams, $customstr,
        $instance->instructorcustomparameters, $islti2));

    $launchcontainer = lti_get_launch_container($instance, $typeconfig);
    $returnurlparams = array('course' => $course->id,
        'launch_container' => $launchcontainer,
        'instanceid' => $instance->id,
        'sesskey' => sesskey());

    // Add the return URL. We send the launch container along to help us avoid frames-within-frames when the user returns.
    $url = new \moodle_url('/mod/lti/return.php', $returnurlparams);
    $returnurl = $url->out(false);

    if (isset($typeconfig['forcessl']) && ($typeconfig['forcessl'] == '1')) {
        $returnurl = lti_ensure_url_is_https($returnurl);
    }

    $target = '';
    switch($launchcontainer) {
        case LTI_LAUNCH_CONTAINER_EMBED:
        case LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS:
            $target = 'iframe';
            break;
        case LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW:
            $target = 'frame';
            break;
        case LTI_LAUNCH_CONTAINER_WINDOW:
            $target = 'window';
            break;
    }
    if (!empty($target)) {
        $requestparams['launch_presentation_document_target'] = $target;
    }

    $requestparams['launch_presentation_return_url'] = $returnurl;

    // Add the parameters configured by the LTI services.
    if ($typeid && !$islti2) {
        $services = lti_get_services();
        foreach ($services as $service) {
            $serviceparameters = $service->get_launch_parameters('basic-lti-launch-request',
                    $course->id, $USER->id , $typeid, $instance->id);
            foreach ($serviceparameters as $paramkey => $paramvalue) {
                $requestparams['custom_' . $paramkey] = lti_parse_custom_parameter($toolproxy, $tool, $requestparams, $paramvalue,
                    $islti2);
            }
        }
    }

    // Allow request params to be updated by sub-plugins.
    $plugins = core_component::get_plugin_list('ltisource');
    foreach (array_keys($plugins) as $plugin) {
        $pluginparams = component_callback('ltisource_'.$plugin, 'before_launch',
            array($instance, $endpoint, $requestparams), array());

        if (!empty($pluginparams) && is_array($pluginparams)) {
            $requestparams = array_merge($requestparams, $pluginparams);
        }
    }

    if ((!empty($key) && !empty($secret)) || ($ltiversion === LTI_VERSION_1P3)) {
        if ($ltiversion !== LTI_VERSION_1P3) {
            $parms = lti_sign_parameters($requestparams, $endpoint, 'POST', $key, $secret);
        } else {
            $parms = lti_sign_jwt($requestparams, $endpoint, $key, $typeid, $nonce);
        }

        $endpointurl = new \moodle_url($endpoint);
        $endpointparams = $endpointurl->params();

        // Strip querystring params in endpoint url from $parms to avoid duplication.
        if (!empty($endpointparams) && !empty($parms)) {
            foreach (array_keys($endpointparams) as $paramname) {
                if (isset($parms[$paramname])) {
                    unset($parms[$paramname]);
                }
            }
        }

    } else {
        // If no key and secret, do the launch unsigned.
        $returnurlparams['unsigned'] = '1';
        $parms = $requestparams;
    }

    return array($endpoint, $parms);
}

/**
 * Launch an external tool activity.
 *
 * @param  stdClass $instance the external tool activity settings
 * @return string The HTML code containing the javascript code for the launch
 */
function lti_launch_tool($instance) {

    list($endpoint, $parms) = lti_get_launch_data($instance);
    $debuglaunch = ( $instance->debuglaunch == 1 );

    $content = lti_post_launch_html($parms, $endpoint, $debuglaunch);

    echo $content;
}

/**
 * Prepares an LTI registration request message
 *
 * @param object $toolproxy  Tool Proxy instance object
 */
function lti_register($toolproxy) {
    $endpoint = $toolproxy->regurl;

    // Change the status to pending.
    $toolproxy->state = LTI_TOOL_PROXY_STATE_PENDING;
    lti_update_tool_proxy($toolproxy);

    $requestparams = lti_build_registration_request($toolproxy);

    $content = lti_post_launch_html($requestparams, $endpoint, false);

    echo $content;
}


/**
 * Gets the parameters for the regirstration request
 *
 * @param object $toolproxy Tool Proxy instance object
 * @return array Registration request parameters
 */
function lti_build_registration_request($toolproxy) {
    $key = $toolproxy->guid;
    $secret = $toolproxy->secret;

    $requestparams = array();
    $requestparams['lti_message_type'] = 'ToolProxyRegistrationRequest';
    $requestparams['lti_version'] = 'LTI-2p0';
    $requestparams['reg_key'] = $key;
    $requestparams['reg_password'] = $secret;
    $requestparams['reg_url'] = $toolproxy->regurl;

    // Add the profile URL.
    $profileservice = lti_get_service_by_name('profile');
    $profileservice->set_tool_proxy($toolproxy);
    $requestparams['tc_profile_url'] = $profileservice->parse_value('$ToolConsumerProfile.url');

    // Add the return URL.
    $returnurlparams = array('id' => $toolproxy->id, 'sesskey' => sesskey());
    $url = new \moodle_url('/mod/lti/externalregistrationreturn.php', $returnurlparams);
    $returnurl = $url->out(false);

    $requestparams['launch_presentation_return_url'] = $returnurl;

    return $requestparams;
}


/** get Organization ID using default if no value provided
 * @param object $typeconfig
 * @return string
 */
function lti_get_organizationid($typeconfig) {
    global $CFG;
    // Default the organizationid if not specified.
    if (empty($typeconfig['organizationid'])) {
        if (($typeconfig['organizationid_default'] ?? LTI_DEFAULT_ORGID_SITEHOST) == LTI_DEFAULT_ORGID_SITEHOST) {
            $urlparts = parse_url($CFG->wwwroot);
            return $urlparts['host'];
        } else {
            return md5(get_site_identifier());
        }
    }
    return $typeconfig['organizationid'];
}

/**
 * Build source ID
 *
 * @param int $instanceid
 * @param int $userid
 * @param string $servicesalt
 * @param null|int $typeid
 * @param null|int $launchid
 * @return stdClass
 */
function lti_build_sourcedid($instanceid, $userid, $servicesalt, $typeid = null, $launchid = null) {
    $data = new \stdClass();

    $data->instanceid = $instanceid;
    $data->userid = $userid;
    $data->typeid = $typeid;
    if (!empty($launchid)) {
        $data->launchid = $launchid;
    } else {
        $data->launchid = mt_rand();
    }

    $json = json_encode($data);

    $hash = hash('sha256', $json . $servicesalt, false);

    $container = new \stdClass();
    $container->data = $data;
    $container->hash = $hash;

    return $container;
}

/**
 * This function builds the request that must be sent to the tool producer
 *
 * @param object    $instance       Basic LTI instance object
 * @param array     $typeconfig     Basic LTI tool configuration
 * @param object    $course         Course object
 * @param int|null  $typeid         Basic LTI tool ID
 * @param boolean   $islti2         True if an LTI 2 tool is being launched
 *
 * @return array                    Request details
 */
function lti_build_request($instance, $typeconfig, $course, $typeid = null, $islti2 = false) {
    global $USER, $CFG;

    if (empty($instance->cmid)) {
        $instance->cmid = 0;
    }

    $role = lti_get_ims_role($USER, $instance->cmid, $instance->course, $islti2);

    $requestparams = array(
        'user_id' => $USER->id,
        'lis_person_sourcedid' => $USER->idnumber,
        'roles' => $role,
        'context_id' => $course->id,
        'context_label' => trim(html_to_text($course->shortname, 0)),
        'context_title' => trim(html_to_text($course->fullname, 0)),
    );
    if (!empty($instance->name)) {
        $requestparams['resource_link_title'] = trim(html_to_text($instance->name, 0));
    }
    if (!empty($instance->cmid)) {
        $intro = format_module_intro('lti', $instance, $instance->cmid);
        $intro = trim(html_to_text($intro, 0, false));

        // This may look weird, but this is required for new lines
        // so we generate the same OAuth signature as the tool provider.
        $intro = str_replace("\n", "\r\n", $intro);
        $requestparams['resource_link_description'] = $intro;
    }
    if (!empty($instance->id)) {
        $requestparams['resource_link_id'] = $instance->id;
    }
    if (!empty($instance->resource_link_id)) {
        $requestparams['resource_link_id'] = $instance->resource_link_id;
    }
    if ($course->format == 'site') {
        $requestparams['context_type'] = 'Group';
    } else {
        $requestparams['context_type'] = 'CourseSection';
        $requestparams['lis_course_section_sourcedid'] = $course->idnumber;
    }

    if (!empty($instance->id) && !empty($instance->servicesalt) && ($islti2 ||
            $typeconfig['acceptgrades'] == LTI_SETTING_ALWAYS ||
            ($typeconfig['acceptgrades'] == LTI_SETTING_DELEGATE && $instance->instructorchoiceacceptgrades == LTI_SETTING_ALWAYS))
    ) {
        $placementsecret = $instance->servicesalt;
        $sourcedid = json_encode(lti_build_sourcedid($instance->id, $USER->id, $placementsecret, $typeid));
        $requestparams['lis_result_sourcedid'] = $sourcedid;

        // Add outcome service URL.
        $serviceurl = new \moodle_url('/mod/lti/service.php');
        $serviceurl = $serviceurl->out();

        $forcessl = false;
        if (!empty($CFG->mod_lti_forcessl)) {
            $forcessl = true;
        }

        if ((isset($typeconfig['forcessl']) && ($typeconfig['forcessl'] == '1')) or $forcessl) {
            $serviceurl = lti_ensure_url_is_https($serviceurl);
        }

        $requestparams['lis_outcome_service_url'] = $serviceurl;
    }

    // Send user's name and email data if appropriate.
    if ($islti2 || $typeconfig['sendname'] == LTI_SETTING_ALWAYS ||
        ($typeconfig['sendname'] == LTI_SETTING_DELEGATE && isset($instance->instructorchoicesendname)
            && $instance->instructorchoicesendname == LTI_SETTING_ALWAYS)
    ) {
        $requestparams['lis_person_name_given'] = $USER->firstname;
        $requestparams['lis_person_name_family'] = $USER->lastname;
        $requestparams['lis_person_name_full'] = fullname($USER);
        $requestparams['ext_user_username'] = $USER->username;
    }

    if ($islti2 || $typeconfig['sendemailaddr'] == LTI_SETTING_ALWAYS ||
        ($typeconfig['sendemailaddr'] == LTI_SETTING_DELEGATE && isset($instance->instructorchoicesendemailaddr)
            && $instance->instructorchoicesendemailaddr == LTI_SETTING_ALWAYS)
    ) {
        $requestparams['lis_person_contact_email_primary'] = $USER->email;
    }

    return $requestparams;
}

/**
 * This function builds the request that must be sent to an LTI 2 tool provider
 *
 * @param object    $tool           Basic LTI tool object
 * @param array     $params         Custom launch parameters
 *
 * @return array                    Request details
 */
function lti_build_request_lti2($tool, $params) {

    $requestparams = array();

    $capabilities = lti_get_capabilities();
    $enabledcapabilities = explode("\n", $tool->enabledcapability);
    foreach ($enabledcapabilities as $capability) {
        if (array_key_exists($capability, $capabilities)) {
            $val = $capabilities[$capability];
            if ($val && (substr($val, 0, 1) != '$')) {
                if (isset($params[$val])) {
                    $requestparams[$capabilities[$capability]] = $params[$capabilities[$capability]];
                }
            }
        }
    }

    return $requestparams;

}

/**
 * This function builds the standard parameters for an LTI 1 or 2 request that must be sent to the tool producer
 *
 * @param stdClass  $instance       Basic LTI instance object
 * @param string    $orgid          Organisation ID
 * @param boolean   $islti2         True if an LTI 2 tool is being launched
 * @param string    $messagetype    The request message type. Defaults to basic-lti-launch-request if empty.
 *
 * @return array                    Request details
 * @deprecated since Moodle 3.7 MDL-62599 - please do not use this function any more.
 * @see lti_build_standard_message()
 */
function lti_build_standard_request($instance, $orgid, $islti2, $messagetype = 'basic-lti-launch-request') {
    if (!$islti2) {
        $ltiversion = LTI_VERSION_1;
    } else {
        $ltiversion = LTI_VERSION_2;
    }
    return lti_build_standard_message($instance, $orgid, $ltiversion, $messagetype);
}

/**
 * This function builds the standard parameters for an LTI message that must be sent to the tool producer
 *
 * @param stdClass  $instance       Basic LTI instance object
 * @param string    $orgid          Organisation ID
 * @param boolean   $ltiversion     LTI version to be used for tool messages
 * @param string    $messagetype    The request message type. Defaults to basic-lti-launch-request if empty.
 *
 * @return array                    Message parameters
 */
function lti_build_standard_message($instance, $orgid, $ltiversion, $messagetype = 'basic-lti-launch-request') {
    global $CFG;

    $requestparams = array();

    if ($instance) {
        $requestparams['resource_link_id'] = $instance->id;
        if (property_exists($instance, 'resource_link_id') and !empty($instance->resource_link_id)) {
            $requestparams['resource_link_id'] = $instance->resource_link_id;
        }
    }

    $requestparams['launch_presentation_locale'] = current_language();

    // Make sure we let the tool know what LMS they are being called from.
    $requestparams['ext_lms'] = 'moodle-2';
    $requestparams['tool_consumer_info_product_family_code'] = 'moodle';
    $requestparams['tool_consumer_info_version'] = strval($CFG->version);

    // Add oauth_callback to be compliant with the 1.0A spec.
    $requestparams['oauth_callback'] = 'about:blank';

    $requestparams['lti_version'] = $ltiversion;
    $requestparams['lti_message_type'] = $messagetype;

    if ($orgid) {
        $requestparams["tool_consumer_instance_guid"] = $orgid;
    }
    if (!empty($CFG->mod_lti_institution_name)) {
        $requestparams['tool_consumer_instance_name'] = trim(html_to_text($CFG->mod_lti_institution_name, 0));
    } else {
        $requestparams['tool_consumer_instance_name'] = get_site()->shortname;
    }
    $requestparams['tool_consumer_instance_description'] = trim(html_to_text(get_site()->fullname, 0));

    return $requestparams;
}

/**
 * This function builds the custom parameters
 *
 * @param object    $toolproxy      Tool proxy instance object
 * @param object    $tool           Tool instance object
 * @param object    $instance       Tool placement instance object
 * @param array     $params         LTI launch parameters
 * @param string    $customstr      Custom parameters defined for tool
 * @param string    $instructorcustomstr      Custom parameters defined for this placement
 * @param boolean   $islti2         True if an LTI 2 tool is being launched
 *
 * @return array                    Custom parameters
 */
function lti_build_custom_parameters($toolproxy, $tool, $instance, $params, $customstr, $instructorcustomstr, $islti2) {

    // Concatenate the custom parameters from the administrator and the instructor
    // Instructor parameters are only taken into consideration if the administrator
    // has given permission.
    $custom = array();
    if ($customstr) {
        $custom = lti_split_custom_parameters($toolproxy, $tool, $params, $customstr, $islti2);
    }
    if ($instructorcustomstr) {
        $custom = array_merge(lti_split_custom_parameters($toolproxy, $tool, $params,
            $instructorcustomstr, $islti2), $custom);
    }
    if ($islti2) {
        $custom = array_merge(lti_split_custom_parameters($toolproxy, $tool, $params,
            $tool->parameter, true), $custom);
        $settings = lti_get_tool_settings($tool->toolproxyid);
        $custom = array_merge($custom, lti_get_custom_parameters($toolproxy, $tool, $params, $settings));
        if (!empty($instance->course)) {
            $settings = lti_get_tool_settings($tool->toolproxyid, $instance->course);
            $custom = array_merge($custom, lti_get_custom_parameters($toolproxy, $tool, $params, $settings));
            if (!empty($instance->id)) {
                $settings = lti_get_tool_settings($tool->toolproxyid, $instance->course, $instance->id);
                $custom = array_merge($custom, lti_get_custom_parameters($toolproxy, $tool, $params, $settings));
            }
        }
    }

    return $custom;
}

/**
 * Builds a standard LTI Content-Item selection request.
 *
 * @param int $id The tool type ID.
 * @param stdClass $course The course object.
 * @param moodle_url $returnurl The return URL in the tool consumer (TC) that the tool provider (TP)
 *                              will use to return the Content-Item message.
 * @param string $title The tool's title, if available.
 * @param string $text The text to display to represent the content item. This value may be a long description of the content item.
 * @param array $mediatypes Array of MIME types types supported by the TC. If empty, the TC will support ltilink by default.
 * @param array $presentationtargets Array of ways in which the selected content item(s) can be requested to be opened
 *                                   (via the presentationDocumentTarget element for a returned content item).
 *                                   If empty, "frame", "iframe", and "window" will be supported by default.
 * @param bool $autocreate Indicates whether any content items returned by the TP would be automatically persisted without
 * @param bool $multiple Indicates whether the user should be permitted to select more than one item. False by default.
 *                         any option for the user to cancel the operation. False by default.
 * @param bool $unsigned Indicates whether the TC is willing to accept an unsigned return message, or not.
 *                       A signed message should always be required when the content item is being created automatically in the
 *                       TC without further interaction from the user. False by default.
 * @param bool $canconfirm Flag for can_confirm parameter. False by default.
 * @param bool $copyadvice Indicates whether the TC is able and willing to make a local copy of a content item. False by default.
 * @param string $nonce
 * @return stdClass The object containing the signed request parameters and the URL to the TP's Content-Item selection interface.
 * @throws moodle_exception When the LTI tool type does not exist.`
 * @throws coding_exception For invalid media type and presentation target parameters.
 */
function lti_build_content_item_selection_request($id, $course, moodle_url $returnurl, $title = '', $text = '', $mediatypes = [],
                                                  $presentationtargets = [], $autocreate = false, $multiple = true,
                                                  $unsigned = false, $canconfirm = false, $copyadvice = false, $nonce = '') {
    global $USER;

    $tool = lti_get_type($id);
    // Validate parameters.
    if (!$tool) {
        throw new moodle_exception('errortooltypenotfound', 'mod_lti');
    }
    if (!is_array($mediatypes)) {
        throw new coding_exception('The list of accepted media types should be in an array');
    }
    if (!is_array($presentationtargets)) {
        throw new coding_exception('The list of accepted presentation targets should be in an array');
    }

    // Check title. If empty, use the tool's name.
    if (empty($title)) {
        $title = $tool->name;
    }

    $typeconfig = lti_get_type_config($id);
    $key = '';
    $secret = '';
    $islti2 = false;
    $islti13 = false;
    if (isset($tool->toolproxyid)) {
        $islti2 = true;
        $toolproxy = lti_get_tool_proxy($tool->toolproxyid);
        $key = $toolproxy->guid;
        $secret = $toolproxy->secret;
    } else {
        $islti13 = $tool->ltiversion === LTI_VERSION_1P3;
        $toolproxy = null;
        if ($islti13 && !empty($tool->clientid)) {
            $key = $tool->clientid;
        } else if (!$islti13 && !empty($typeconfig['resourcekey'])) {
            $key = $typeconfig['resourcekey'];
        }
        if (!empty($typeconfig['password'])) {
            $secret = $typeconfig['password'];
        }
    }
    $tool->enabledcapability = '';
    if (!empty($typeconfig['enabledcapability_ContentItemSelectionRequest'])) {
        $tool->enabledcapability = $typeconfig['enabledcapability_ContentItemSelectionRequest'];
    }

    $tool->parameter = '';
    if (!empty($typeconfig['parameter_ContentItemSelectionRequest'])) {
        $tool->parameter = $typeconfig['parameter_ContentItemSelectionRequest'];
    }

    // Set the tool URL.
    if (!empty($typeconfig['toolurl_ContentItemSelectionRequest'])) {
        $toolurl = new moodle_url($typeconfig['toolurl_ContentItemSelectionRequest']);
    } else {
        $toolurl = new moodle_url($typeconfig['toolurl']);
    }

    // Check if SSL is forced.
    if (!empty($typeconfig['forcessl'])) {
        // Make sure the tool URL is set to https.
        if (strtolower($toolurl->get_scheme()) === 'http') {
            $toolurl->set_scheme('https');
        }
        // Make sure the return URL is set to https.
        if (strtolower($returnurl->get_scheme()) === 'http') {
            $returnurl->set_scheme('https');
        }
    }
    $toolurlout = $toolurl->out(false);

    // Get base request parameters.
    $instance = new stdClass();
    $instance->course = $course->id;
    $requestparams = lti_build_request($instance, $typeconfig, $course, $id, $islti2);

    // Get LTI2-specific request parameters and merge to the request parameters if applicable.
    if ($islti2) {
        $lti2params = lti_build_request_lti2($tool, $requestparams);
        $requestparams = array_merge($requestparams, $lti2params);
    }

    // Get standard request parameters and merge to the request parameters.
    $orgid = lti_get_organizationid($typeconfig);
    $standardparams = lti_build_standard_message(null, $orgid, $tool->ltiversion, 'ContentItemSelectionRequest');
    $requestparams = array_merge($requestparams, $standardparams);

    // Get custom request parameters and merge to the request parameters.
    $customstr = '';
    if (!empty($typeconfig['customparameters'])) {
        $customstr = $typeconfig['customparameters'];
    }
    $customparams = lti_build_custom_parameters($toolproxy, $tool, $instance, $requestparams, $customstr, '', $islti2);
    $requestparams = array_merge($requestparams, $customparams);

    // Add the parameters configured by the LTI services.
    if ($id && !$islti2) {
        $services = lti_get_services();
        foreach ($services as $service) {
            $serviceparameters = $service->get_launch_parameters('ContentItemSelectionRequest',
                $course->id, $USER->id , $id);
            foreach ($serviceparameters as $paramkey => $paramvalue) {
                $requestparams['custom_' . $paramkey] = lti_parse_custom_parameter($toolproxy, $tool, $requestparams, $paramvalue,
                    $islti2);
            }
        }
    }

    // Allow request params to be updated by sub-plugins.
    $plugins = core_component::get_plugin_list('ltisource');
    foreach (array_keys($plugins) as $plugin) {
        $pluginparams = component_callback('ltisource_' . $plugin, 'before_launch', [$instance, $toolurlout, $requestparams], []);

        if (!empty($pluginparams) && is_array($pluginparams)) {
            $requestparams = array_merge($requestparams, $pluginparams);
        }
    }

    if (!$islti13) {
        // Media types. Set to ltilink by default if empty.
        if (empty($mediatypes)) {
            $mediatypes = [
                'application/vnd.ims.lti.v1.ltilink',
            ];
        }
        $requestparams['accept_media_types'] = implode(',', $mediatypes);
    } else {
        // Only LTI links are currently supported.
        $requestparams['accept_types'] = 'ltiResourceLink';
    }

    // Presentation targets. Supports frame, iframe, window by default if empty.
    if (empty($presentationtargets)) {
        $presentationtargets = [
            'frame',
            'iframe',
            'window',
        ];
    }
    $requestparams['accept_presentation_document_targets'] = implode(',', $presentationtargets);

    // Other request parameters.
    $requestparams['accept_copy_advice'] = $copyadvice === true ? 'true' : 'false';
    $requestparams['accept_multiple'] = $multiple === true ? 'true' : 'false';
    $requestparams['accept_unsigned'] = $unsigned === true ? 'true' : 'false';
    $requestparams['auto_create'] = $autocreate === true ? 'true' : 'false';
    $requestparams['can_confirm'] = $canconfirm === true ? 'true' : 'false';
    $requestparams['content_item_return_url'] = $returnurl->out(false);
    $requestparams['title'] = $title;
    $requestparams['text'] = $text;
    if (!$islti13) {
        $signedparams = lti_sign_parameters($requestparams, $toolurlout, 'POST', $key, $secret);
    } else {
        $signedparams = lti_sign_jwt($requestparams, $toolurlout, $key, $id, $nonce);
    }
    $toolurlparams = $toolurl->params();

    // Strip querystring params in endpoint url from $signedparams to avoid duplication.
    if (!empty($toolurlparams) && !empty($signedparams)) {
        foreach (array_keys($toolurlparams) as $paramname) {
            if (isset($signedparams[$paramname])) {
                unset($signedparams[$paramname]);
            }
        }
    }

    // Check for params that should not be passed. Unset if they are set.
    $unwantedparams = [
        'resource_link_id',
        'resource_link_title',
        'resource_link_description',
        'launch_presentation_return_url',
        'lis_result_sourcedid',
    ];
    foreach ($unwantedparams as $param) {
        if (isset($signedparams[$param])) {
            unset($signedparams[$param]);
        }
    }

    // Prepare result object.
    $result = new stdClass();
    $result->params = $signedparams;
    $result->url = $toolurlout;

    return $result;
}

/**
 * Verifies the OAuth signature of an incoming message.
 *
 * @param int $typeid The tool type ID.
 * @param string $consumerkey The consumer key.
 * @return stdClass Tool type
 * @throws moodle_exception
 * @throws lti\OAuthException
 */
function lti_verify_oauth_signature($typeid, $consumerkey) {
    $tool = lti_get_type($typeid);
    // Validate parameters.
    if (!$tool) {
        throw new moodle_exception('errortooltypenotfound', 'mod_lti');
    }
    $typeconfig = lti_get_type_config($typeid);

    if (isset($tool->toolproxyid)) {
        $toolproxy = lti_get_tool_proxy($tool->toolproxyid);
        $key = $toolproxy->guid;
        $secret = $toolproxy->secret;
    } else {
        $toolproxy = null;
        if (!empty($typeconfig['resourcekey'])) {
            $key = $typeconfig['resourcekey'];
        } else {
            $key = '';
        }
        if (!empty($typeconfig['password'])) {
            $secret = $typeconfig['password'];
        } else {
            $secret = '';
        }
    }

    if ($consumerkey !== $key) {
        throw new moodle_exception('errorincorrectconsumerkey', 'mod_lti');
    }

    $store = new lti\TrivialOAuthDataStore();
    $store->add_consumer($key, $secret);
    $server = new lti\OAuthServer($store);
    $method = new lti\OAuthSignatureMethod_HMAC_SHA1();
    $server->add_signature_method($method);
    $request = lti\OAuthRequest::from_request();
    try {
        $server->verify_request($request);
    } catch (lti\OAuthException $e) {
        throw new lti\OAuthException("OAuth signature failed: " . $e->getMessage());
    }

    return $tool;
}

/**
 * Verifies the JWT signature using a JWK keyset.
 *
 * @param string $jwtparam JWT parameter value.
 * @param string $keyseturl The tool keyseturl.
 * @param string $clientid The tool client id.
 *
 * @return object The JWT's payload as a PHP object
 * @throws moodle_exception
 * @throws UnexpectedValueException     Provided JWT was invalid
 * @throws SignatureInvalidException    Provided JWT was invalid because the signature verification failed
 * @throws BeforeValidException         Provided JWT is trying to be used before it's eligible as defined by 'nbf'
 * @throws BeforeValidException         Provided JWT is trying to be used before it's been created as defined by 'iat'
 * @throws ExpiredException             Provided JWT has since expired, as defined by the 'exp' claim
 */
function lti_verify_with_keyset($jwtparam, $keyseturl, $clientid) {
    // Attempts to retrieve cached keyset.
    $cache = cache::make('mod_lti', 'keyset');
    $keyset = $cache->get($clientid);

    try {
        if (empty($keyset)) {
            throw new moodle_exception('errornocachedkeysetfound', 'mod_lti');
        }
        $keysetarr = json_decode($keyset, true);
        $keys = JWK::parseKeySet($keysetarr);
        $jwt = JWT::decode($jwtparam, $keys, ['RS256']);
    } catch (Exception $e) {
        // Something went wrong, so attempt to update cached keyset and then try again.
        $keyset = file_get_contents($keyseturl);
        $keysetarr = json_decode($keyset, true);
        $keys = JWK::parseKeySet($keysetarr);
        $jwt = JWT::decode($jwtparam, $keys, ['RS256']);
        // If sucessful, updates the cached keyset.
        $cache->set($clientid, $keyset);
    }
    return $jwt;
}

/**
 * Verifies the JWT signature of an incoming message.
 *
 * @param int $typeid The tool type ID.
 * @param string $consumerkey The consumer key.
 * @param string $jwtparam JWT parameter value
 *
 * @return stdClass Tool type
 * @throws moodle_exception
 * @throws UnexpectedValueException     Provided JWT was invalid
 * @throws SignatureInvalidException    Provided JWT was invalid because the signature verification failed
 * @throws BeforeValidException         Provided JWT is trying to be used before it's eligible as defined by 'nbf'
 * @throws BeforeValidException         Provided JWT is trying to be used before it's been created as defined by 'iat'
 * @throws ExpiredException             Provided JWT has since expired, as defined by the 'exp' claim
 */
function lti_verify_jwt_signature($typeid, $consumerkey, $jwtparam) {
    $tool = lti_get_type($typeid);

    // Validate parameters.
    if (!$tool) {
        throw new moodle_exception('errortooltypenotfound', 'mod_lti');
    }
    if (isset($tool->toolproxyid)) {
        throw new moodle_exception('JWT security not supported with LTI 2');
    }

    $typeconfig = lti_get_type_config($typeid);

    $key = $tool->clientid ?? '';

    if ($consumerkey !== $key) {
        throw new moodle_exception('errorincorrectconsumerkey', 'mod_lti');
    }

    if (empty($typeconfig['keytype']) || $typeconfig['keytype'] === LTI_RSA_KEY) {
        $publickey = $typeconfig['publickey'] ?? '';
        if (empty($publickey)) {
            throw new moodle_exception('No public key configured');
        }
        // Attemps to verify jwt with RSA key.
        JWT::decode($jwtparam, $publickey, ['RS256']);
    } else if ($typeconfig['keytype'] === LTI_JWK_KEYSET) {
        $keyseturl = $typeconfig['publickeyset'] ?? '';
        if (empty($keyseturl)) {
            throw new moodle_exception('No public keyset configured');
        }
        // Attempts to verify jwt with jwk keyset.
        lti_verify_with_keyset($jwtparam, $keyseturl, $tool->clientid);
    } else {
        throw new moodle_exception('Invalid public key type');
    }

    return $tool;
}

/**
 * Converts LTI 1.1 Content Item for LTI Link to Form data.
 *
 * @param object $tool Tool for which the item is created for.
 * @param object $typeconfig The tool configuration.
 * @param object $item Item populated from JSON to be converted to Form form
 *
 * @return stdClass Form config for the item
 */
function content_item_to_form(object $tool, object $typeconfig, object $item) : stdClass {
    $config = new stdClass();
    $config->name = '';
    if (isset($item->title)) {
        $config->name = $item->title;
    }
    if (empty($config->name)) {
        $config->name = $tool->name;
    }
    if (isset($item->text)) {
        $config->introeditor = [
            'text' => $item->text,
            'format' => FORMAT_PLAIN
        ];
    } else {
        $config->introeditor = [
            'text' => '',
            'format' => FORMAT_PLAIN
        ];
    }
    if (isset($item->icon->{'@id'})) {
        $iconurl = new moodle_url($item->icon->{'@id'});
        // Assign item's icon URL to secureicon or icon depending on its scheme.
        if (strtolower($iconurl->get_scheme()) === 'https') {
            $config->secureicon = $iconurl->out(false);
        } else {
            $config->icon = $iconurl->out(false);
        }
    }
    if (isset($item->url)) {
        $url = new moodle_url($item->url);
        $config->toolurl = $url->out(false);
        $config->typeid = 0;
    } else {
        $config->typeid = $tool->id;
    }
    $config->instructorchoiceacceptgrades = LTI_SETTING_NEVER;
    $islti2 = $tool->ltiversion === LTI_VERSION_2;
    if (!$islti2 && isset($typeconfig->lti_acceptgrades)) {
        $acceptgrades = $typeconfig->lti_acceptgrades;
        if ($acceptgrades == LTI_SETTING_ALWAYS) {
            // We create a line item regardless if the definition contains one or not.
            $config->instructorchoiceacceptgrades = LTI_SETTING_ALWAYS;
            $config->grade_modgrade_point = 100;
        }
        if ($acceptgrades == LTI_SETTING_DELEGATE || $acceptgrades == LTI_SETTING_ALWAYS) {
            if (isset($item->lineItem)) {
                $lineitem = $item->lineItem;
                $config->instructorchoiceacceptgrades = LTI_SETTING_ALWAYS;
                $maxscore = 100;
                if (isset($lineitem->scoreConstraints)) {
                    $sc = $lineitem->scoreConstraints;
                    if (isset($sc->totalMaximum)) {
                        $maxscore = $sc->totalMaximum;
                    } else if (isset($sc->normalMaximum)) {
                        $maxscore = $sc->normalMaximum;
                    }
                }
                $config->grade_modgrade_point = $maxscore;
                $config->lineitemresourceid = '';
                $config->lineitemtag = '';
                if (isset($lineitem->assignedActivity) && isset($lineitem->assignedActivity->activityId)) {
                    $config->lineitemresourceid = $lineitem->assignedActivity->activityId?:'';
                }
                if (isset($lineitem->tag)) {
                    $config->lineitemtag = $lineitem->tag?:'';
                }
            }
        }
    }
    $config->instructorchoicesendname = LTI_SETTING_NEVER;
    $config->instructorchoicesendemailaddr = LTI_SETTING_NEVER;
    $config->launchcontainer = LTI_LAUNCH_CONTAINER_DEFAULT;
    if (isset($item->placementAdvice->presentationDocumentTarget)) {
        if ($item->placementAdvice->presentationDocumentTarget === 'window') {
            $config->launchcontainer = LTI_LAUNCH_CONTAINER_WINDOW;
        } else if ($item->placementAdvice->presentationDocumentTarget === 'frame') {
            $config->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS;
        } else if ($item->placementAdvice->presentationDocumentTarget === 'iframe') {
            $config->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED;
        }
    }
    if (isset($item->custom)) {
        $customparameters = [];
        foreach ($item->custom as $key => $value) {
            $customparameters[] = "{$key}={$value}";
        }
        $config->instructorcustomparameters = implode("\n", $customparameters);
    }
    // Including a JSON version of the form data to support adding many items in one submit.
    $config->contentitemjson = json_encode($item);
    return $config;
}

/**
 * Processes the tool provider's response to the ContentItemSelectionRequest and builds the configuration data from the
 * selected content item. This configuration data can be then used when adding a tool into the course.
 *
 * @param int $typeid The tool type ID.
 * @param string $messagetype The value for the lti_message_type parameter.
 * @param string $ltiversion The value for the lti_version parameter.
 * @param string $consumerkey The consumer key.
 * @param string $contentitemsjson The JSON string for the content_items parameter.
 * @return stdClass The array of module information objects.
 * @throws moodle_exception
 * @throws lti\OAuthException
 */
function lti_tool_configuration_from_content_item($typeid, $messagetype, $ltiversion, $consumerkey, $contentitemsjson) {
    $tool = lti_get_type($typeid);
    // Validate parameters.
    if (!$tool) {
        throw new moodle_exception('errortooltypenotfound', 'mod_lti');
    }
    // Check lti_message_type. Show debugging if it's not set to ContentItemSelection.
    // No need to throw exceptions for now since lti_message_type does not seem to be used in this processing at the moment.
    if ($messagetype !== 'ContentItemSelection') {
        debugging("lti_message_type is invalid: {$messagetype}. It should be set to 'ContentItemSelection'.",
            DEBUG_DEVELOPER);
    }

    // Check LTI versions from our side and the response's side. Show debugging if they don't match.
    // No need to throw exceptions for now since LTI version does not seem to be used in this processing at the moment.
    $expectedversion = $tool->ltiversion;
    $islti2 = ($expectedversion === LTI_VERSION_2);
    if ($ltiversion !== $expectedversion) {
        debugging("lti_version from response does not match the tool's configuration. Tool: {$expectedversion}," .
            " Response: {$ltiversion}", DEBUG_DEVELOPER);
    }

    $items = json_decode($contentitemsjson);
    if (empty($items)) {
        throw new moodle_exception('errorinvaliddata', 'mod_lti', '', $contentitemsjson);
    }
    if (!isset($items->{'@graph'}) || !is_array($items->{'@graph'})) {
        throw new moodle_exception('errorinvalidresponseformat', 'mod_lti');
    }

    $config = null;
    $items = $items->{'@graph'};
    if (!empty($items)) {
        $typeconfig = lti_get_type_type_config($tool->id);
        if (count($items) == 1) {
            $config = content_item_to_form($tool, $typeconfig, $items[0]);
        } else {
            $multiple = [];
            foreach ($items as $item) {
                $multiple[] = content_item_to_form($tool, $typeconfig, $item);
            }
            $config = new stdClass();
            $config->multiple = $multiple;
        }
    }
    return $config;
}

/**
 * Converts the new Deep-Linking format for Content-Items to the old format.
 *
 * @param string $param JSON string representing new Deep-Linking format
 * @return string  JSON representation of content-items
 */
function lti_convert_content_items($param) {
    $items = array();
    $json = json_decode($param);
    if (!empty($json) && is_array($json)) {
        foreach ($json as $item) {
            if (isset($item->type)) {
                $newitem = clone $item;
                switch ($item->type) {
                    case 'ltiResourceLink':
                        $newitem->{'@type'} = 'LtiLinkItem';
                        $newitem->mediaType = 'application\/vnd.ims.lti.v1.ltilink';
                        break;
                    case 'link':
                    case 'rich':
                        $newitem->{'@type'} = 'ContentItem';
                        $newitem->mediaType = 'text/html';
                        break;
                    case 'file':
                        $newitem->{'@type'} = 'FileItem';
                        break;
                }
                unset($newitem->type);
                if (isset($item->html)) {
                    $newitem->text = $item->html;
                    unset($newitem->html);
                }
                if (isset($item->iframe)) {
                    // DeepLinking allows multiple options to be declared as supported.
                    // We favor iframe over new window if both are specified.
                    $newitem->placementAdvice = new stdClass();
                    $newitem->placementAdvice->presentationDocumentTarget = 'iframe';
                    if (isset($item->iframe->width)) {
                        $newitem->placementAdvice->displayWidth = $item->iframe->width;
                    }
                    if (isset($item->iframe->height)) {
                        $newitem->placementAdvice->displayHeight = $item->iframe->height;
                    }
                    unset($newitem->iframe);
                    unset($newitem->window);
                } else if (isset($item->window)) {
                    $newitem->placementAdvice = new stdClass();
                    $newitem->placementAdvice->presentationDocumentTarget = 'window';
                    if (isset($item->window->targetName)) {
                        $newitem->placementAdvice->windowTarget = $item->window->targetName;
                    }
                    if (isset($item->window->width)) {
                        $newitem->placementAdvice->displayWidth = $item->window->width;
                    }
                    if (isset($item->window->height)) {
                        $newitem->placementAdvice->displayHeight = $item->window->height;
                    }
                    unset($newitem->window);
                } else if (isset($item->presentation)) {
                    // This may have been part of an early draft but is not in the final spec
                    // so keeping it around for now in case it's actually been used.
                    $newitem->placementAdvice = new stdClass();
                    if (isset($item->presentation->documentTarget)) {
                        $newitem->placementAdvice->presentationDocumentTarget = $item->presentation->documentTarget;
                    }
                    if (isset($item->presentation->windowTarget)) {
                        $newitem->placementAdvice->windowTarget = $item->presentation->windowTarget;
                    }
                    if (isset($item->presentation->width)) {
                        $newitem->placementAdvice->dislayWidth = $item->presentation->width;
                    }
                    if (isset($item->presentation->height)) {
                        $newitem->placementAdvice->dislayHeight = $item->presentation->height;
                    }
                    unset($newitem->presentation);
                }
                if (isset($item->icon) && isset($item->icon->url)) {
                    $newitem->icon->{'@id'} = $item->icon->url;
                    unset($newitem->icon->url);
                }
                if (isset($item->thumbnail) && isset($item->thumbnail->url)) {
                    $newitem->thumbnail->{'@id'} = $item->thumbnail->url;
                    unset($newitem->thumbnail->url);
                }
                if (isset($item->lineItem)) {
                    unset($newitem->lineItem);
                    $newitem->lineItem = new stdClass();
                    $newitem->lineItem->{'@type'} = 'LineItem';
                    $newitem->lineItem->reportingMethod = 'http://purl.imsglobal.org/ctx/lis/v2p1/Result#totalScore';
                    if (isset($item->lineItem->label)) {
                        $newitem->lineItem->label = $item->lineItem->label;
                    }
                    if (isset($item->lineItem->resourceId)) {
                        $newitem->lineItem->assignedActivity = new stdClass();
                        $newitem->lineItem->assignedActivity->activityId = $item->lineItem->resourceId;
                    }
                    if (isset($item->lineItem->tag)) {
                        $newitem->lineItem->tag = $item->lineItem->tag;
                    }
                    if (isset($item->lineItem->scoreMaximum)) {
                        $newitem->lineItem->scoreConstraints = new stdClass();
                        $newitem->lineItem->scoreConstraints->{'@type'} = 'NumericLimits';
                        $newitem->lineItem->scoreConstraints->totalMaximum = $item->lineItem->scoreMaximum;
                    }
                }
                $items[] = $newitem;
            }
        }
    }

    $newitems = new stdClass();
    $newitems->{'@context'} = 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem';
    $newitems->{'@graph'} = $items;

    return json_encode($newitems);
}

function lti_get_tool_table($tools, $id) {
    global $OUTPUT;
    $html = '';

    $typename = get_string('typename', 'lti');
    $baseurl = get_string('baseurl', 'lti');
    $action = get_string('action', 'lti');
    $createdon = get_string('createdon', 'lti');

    if (!empty($tools)) {
        $html .= "
        <div id=\"{$id}_tools_container\" style=\"margin-top:.5em;margin-bottom:.5em\">
            <table id=\"{$id}_tools\">
                <thead>
                    <tr>
                        <th>$typename</th>
                        <th>$baseurl</th>
                        <th>$createdon</th>
                        <th>$action</th>
                    </tr>
                </thead>
        ";

        foreach ($tools as $type) {
            $date = userdate($type->timecreated, get_string('strftimedatefullshort', 'core_langconfig'));
            $accept = get_string('accept', 'lti');
            $update = get_string('update', 'lti');
            $delete = get_string('delete', 'lti');

            if (empty($type->toolproxyid)) {
                $baseurl = new \moodle_url('/mod/lti/typessettings.php', array(
                        'action' => 'accept',
                        'id' => $type->id,
                        'sesskey' => sesskey(),
                        'tab' => $id
                    ));
                $ref = $type->baseurl;
            } else {
                $baseurl = new \moodle_url('/mod/lti/toolssettings.php', array(
                        'action' => 'accept',
                        'id' => $type->id,
                        'sesskey' => sesskey(),
                        'tab' => $id
                    ));
                $ref = $type->tpname;
            }

            $accepthtml = $OUTPUT->action_icon($baseurl,
                    new \pix_icon('t/check', $accept, '', array('class' => 'iconsmall')), null,
                    array('title' => $accept, 'class' => 'editing_accept'));

            $deleteaction = 'delete';

            if ($type->state == LTI_TOOL_STATE_CONFIGURED) {
                $accepthtml = '';
            }

            if ($type->state != LTI_TOOL_STATE_REJECTED) {
                $deleteaction = 'reject';
                $delete = get_string('reject', 'lti');
            }

            $updateurl = clone($baseurl);
            $updateurl->param('action', 'update');
            $updatehtml = $OUTPUT->action_icon($updateurl,
                    new \pix_icon('t/edit', $update, '', array('class' => 'iconsmall')), null,
                    array('title' => $update, 'class' => 'editing_update'));

            if (($type->state != LTI_TOOL_STATE_REJECTED) || empty($type->toolproxyid)) {
                $deleteurl = clone($baseurl);
                $deleteurl->param('action', $deleteaction);
                $deletehtml = $OUTPUT->action_icon($deleteurl,
                        new \pix_icon('t/delete', $delete, '', array('class' => 'iconsmall')), null,
                        array('title' => $delete, 'class' => 'editing_delete'));
            } else {
                $deletehtml = '';
            }
            $html .= "
            <tr>
                <td>
                    {$type->name}
                </td>
                <td>
                    {$ref}
                </td>
                <td>
                    {$date}
                </td>
                <td align=\"center\">
                    {$accepthtml}{$updatehtml}{$deletehtml}
                </td>
            </tr>
            ";
        }
        $html .= '</table></div>';
    } else {
        $html .= get_string('no_' . $id, 'lti');
    }

    return $html;
}

/**
 * This function builds the tab for a category of tool proxies
 *
 * @param object    $toolproxies    Tool proxy instance objects
 * @param string    $id             Category ID
 *
 * @return string                   HTML for tab
 */
function lti_get_tool_proxy_table($toolproxies, $id) {
    global $OUTPUT;

    if (!empty($toolproxies)) {
        $typename = get_string('typename', 'lti');
        $url = get_string('registrationurl', 'lti');
        $action = get_string('action', 'lti');
        $createdon = get_string('createdon', 'lti');

        $html = <<< EOD
        <div id="{$id}_tool_proxies_container" style="margin-top: 0.5em; margin-bottom: 0.5em">
            <table id="{$id}_tool_proxies">
                <thead>
                    <tr>
                        <th>{$typename}</th>
                        <th>{$url}</th>
                        <th>{$createdon}</th>
                        <th>{$action}</th>
                    </tr>
                </thead>
EOD;
        foreach ($toolproxies as $toolproxy) {
            $date = userdate($toolproxy->timecreated, get_string('strftimedatefullshort', 'core_langconfig'));
            $accept = get_string('register', 'lti');
            $update = get_string('update', 'lti');
            $delete = get_string('delete', 'lti');

            $baseurl = new \moodle_url('/mod/lti/registersettings.php', array(
                    'action' => 'accept',
                    'id' => $toolproxy->id,
                    'sesskey' => sesskey(),
                    'tab' => $id
                ));

            $registerurl = new \moodle_url('/mod/lti/register.php', array(
                    'id' => $toolproxy->id,
                    'sesskey' => sesskey(),
                    'tab' => 'tool_proxy'
                ));

            $accepthtml = $OUTPUT->action_icon($registerurl,
                    new \pix_icon('t/check', $accept, '', array('class' => 'iconsmall')), null,
                    array('title' => $accept, 'class' => 'editing_accept'));

            $deleteaction = 'delete';

            if ($toolproxy->state != LTI_TOOL_PROXY_STATE_CONFIGURED) {
                $accepthtml = '';
            }

            if (($toolproxy->state == LTI_TOOL_PROXY_STATE_CONFIGURED) || ($toolproxy->state == LTI_TOOL_PROXY_STATE_PENDING)) {
                $delete = get_string('cancel', 'lti');
            }

            $updateurl = clone($baseurl);
            $updateurl->param('action', 'update');
            $updatehtml = $OUTPUT->action_icon($updateurl,
                    new \pix_icon('t/edit', $update, '', array('class' => 'iconsmall')), null,
                    array('title' => $update, 'class' => 'editing_update'));

            $deleteurl = clone($baseurl);
            $deleteurl->param('action', $deleteaction);
            $deletehtml = $OUTPUT->action_icon($deleteurl,
                    new \pix_icon('t/delete', $delete, '', array('class' => 'iconsmall')), null,
                    array('title' => $delete, 'class' => 'editing_delete'));
            $html .= <<< EOD
            <tr>
                <td>
                    {$toolproxy->name}
                </td>
                <td>
                    {$toolproxy->regurl}
                </td>
                <td>
                    {$date}
                </td>
                <td align="center">
                    {$accepthtml}{$updatehtml}{$deletehtml}
                </td>
            </tr>
EOD;
        }
        $html .= '</table></div>';
    } else {
        $html = get_string('no_' . $id, 'lti');
    }

    return $html;
}

/**
 * Extracts the enabled capabilities into an array, including those implicitly declared in a parameter
 *
 * @param object $tool  Tool instance object
 *
 * @return array List of enabled capabilities
 */
function lti_get_enabled_capabilities($tool) {
    if (!isset($tool)) {
        return array();
    }
    if (!empty($tool->enabledcapability)) {
        $enabledcapabilities = explode("\n", $tool->enabledcapability);
    } else {
        $enabledcapabilities = array();
    }
    if (!empty($tool->parameter)) {
        $paramstr = str_replace("\r\n", "\n", $tool->parameter);
        $paramstr = str_replace("\n\r", "\n", $paramstr);
        $paramstr = str_replace("\r", "\n", $paramstr);
        $params = explode("\n", $paramstr);
        foreach ($params as $param) {
            $pos = strpos($param, '=');
            if (($pos === false) || ($pos < 1)) {
                continue;
            }
            $value = trim(core_text::substr($param, $pos + 1, strlen($param)));
            if (substr($value, 0, 1) == '$') {
                $value = substr($value, 1);
                if (!in_array($value, $enabledcapabilities)) {
                    $enabledcapabilities[] = $value;
                }
            }
        }
    }
    return $enabledcapabilities;
}

/**
 * Splits the custom parameters field to the various parameters
 *
 * @param object    $toolproxy      Tool proxy instance object
 * @param object    $tool           Tool instance object
 * @param array     $params         LTI launch parameters
 * @param string    $customstr      String containing the parameters
 * @param boolean   $islti2         True if an LTI 2 tool is being launched
 *
 * @return array of custom parameters
 */
function lti_split_custom_parameters($toolproxy, $tool, $params, $customstr, $islti2 = false) {
    $customstr = str_replace("\r\n", "\n", $customstr);
    $customstr = str_replace("\n\r", "\n", $customstr);
    $customstr = str_replace("\r", "\n", $customstr);
    $lines = explode("\n", $customstr);  // Or should this split on "/[\n;]/"?
    $retval = array();
    foreach ($lines as $line) {
        $pos = strpos($line, '=');
        if ( $pos === false || $pos < 1 ) {
            continue;
        }
        $key = trim(core_text::substr($line, 0, $pos));
        $val = trim(core_text::substr($line, $pos + 1, strlen($line)));
        $val = lti_parse_custom_parameter($toolproxy, $tool, $params, $val, $islti2);
        $key2 = lti_map_keyname($key);
        $retval['custom_'.$key2] = $val;
        if (($islti2 || ($tool->ltiversion === LTI_VERSION_1P3)) && ($key != $key2)) {
            $retval['custom_'.$key] = $val;
        }
    }
    return $retval;
}

/**
 * Adds the custom parameters to an array
 *
 * @param object    $toolproxy      Tool proxy instance object
 * @param object    $tool           Tool instance object
 * @param array     $params         LTI launch parameters
 * @param array     $parameters     Array containing the parameters
 *
 * @return array    Array of custom parameters
 */
function lti_get_custom_parameters($toolproxy, $tool, $params, $parameters) {
    $retval = array();
    foreach ($parameters as $key => $val) {
        $key2 = lti_map_keyname($key);
        $val = lti_parse_custom_parameter($toolproxy, $tool, $params, $val, true);
        $retval['custom_'.$key2] = $val;
        if ($key != $key2) {
            $retval['custom_'.$key] = $val;
        }
    }
    return $retval;
}

/**
 * Parse a custom parameter to replace any substitution variables
 *
 * @param object    $toolproxy      Tool proxy instance object
 * @param object    $tool           Tool instance object
 * @param array     $params         LTI launch parameters
 * @param string    $value          Custom parameter value
 * @param boolean   $islti2         True if an LTI 2 tool is being launched
 *
 * @return string Parsed value of custom parameter
 */
function lti_parse_custom_parameter($toolproxy, $tool, $params, $value, $islti2) {
    // This is required as {${$valarr[0]}->{$valarr[1]}}" may be using the USER or COURSE var.
    global $USER, $COURSE;

    if ($value) {
        if (substr($value, 0, 1) == '\\') {
            $value = substr($value, 1);
        } else if (substr($value, 0, 1) == '$') {
            $value1 = substr($value, 1);
            $enabledcapabilities = lti_get_enabled_capabilities($tool);
            if (!$islti2 || in_array($value1, $enabledcapabilities)) {
                $capabilities = lti_get_capabilities();
                if (array_key_exists($value1, $capabilities)) {
                    $val = $capabilities[$value1];
                    if ($val) {
                        if (substr($val, 0, 1) != '$') {
                            $value = $params[$val];
                        } else {
                            $valarr = explode('->', substr($val, 1), 2);
                            $value = "{${$valarr[0]}->{$valarr[1]}}";
                            $value = str_replace('<br />' , ' ', $value);
                            $value = str_replace('<br>' , ' ', $value);
                            $value = format_string($value);
                        }
                    } else {
                        $value = lti_calculate_custom_parameter($value1);
                    }
                } else {
                    $val = $value;
                    $services = lti_get_services();
                    foreach ($services as $service) {
                        $service->set_tool_proxy($toolproxy);
                        $service->set_type($tool);
                        $value = $service->parse_value($val);
                        if ($val != $value) {
                            break;
                        }
                    }
                }
            }
        }
    }
    return $value;
}

/**
 * Calculates the value of a custom parameter that has not been specified earlier
 *
 * @param string    $value          Custom parameter value
 *
 * @return string Calculated value of custom parameter
 */
function lti_calculate_custom_parameter($value) {
    global $USER, $COURSE;

    switch ($value) {
        case 'Moodle.Person.userGroupIds':
            return implode(",", groups_get_user_groups($COURSE->id, $USER->id)[0]);
        case 'Context.id.history':
            return implode(",", get_course_history($COURSE));
    }
    return null;
}

/**
 * Build the history chain for this course using the course originalcourseid.
 *
 * @param object $course course for which the history is returned.
 *
 * @return array ids of the source course in ancestry order, immediate parent 1st.
 */
function get_course_history($course) {
    global $DB;
    $history = [];
    $parentid = $course->originalcourseid;
    while (!empty($parentid) && !in_array($parentid, $history)) {
        $history[] = $parentid;
        $parentid = $DB->get_field('course', 'originalcourseid', array('id' => $parentid));
    }
    return $history;
}

/**
 * Used for building the names of the different custom parameters
 *
 * @param string $key   Parameter name
 * @param bool $tolower Do we want to convert the key into lower case?
 * @return string       Processed name
 */
function lti_map_keyname($key, $tolower = true) {
    if ($tolower) {
        $newkey = '';
        $key = core_text::strtolower(trim($key));
        foreach (str_split($key) as $ch) {
            if ( ($ch >= 'a' && $ch <= 'z') || ($ch >= '0' && $ch <= '9') ) {
                $newkey .= $ch;
            } else {
                $newkey .= '_';
            }
        }
    } else {
        $newkey = $key;
    }
    return $newkey;
}

/**
 * Gets the IMS role string for the specified user and LTI course module.
 *
 * @param mixed    $user      User object or user id
 * @param int      $cmid      The course module id of the LTI activity
 * @param int      $courseid  The course id of the LTI activity
 * @param boolean  $islti2    True if an LTI 2 tool is being launched
 *
 * @return string A role string suitable for passing with an LTI launch
 */
function lti_get_ims_role($user, $cmid, $courseid, $islti2) {
    $roles = array();

    if (empty($cmid)) {
        // If no cmid is passed, check if the user is a teacher in the course
        // This allows other modules to programmatically "fake" a launch without
        // a real LTI instance.
        $context = context_course::instance($courseid);

        if (has_capability('moodle/course:manageactivities', $context, $user)) {
            array_push($roles, 'Instructor');
        } else {
            array_push($roles, 'Learner');
        }
    } else {
        $context = context_module::instance($cmid);

        if (has_capability('mod/lti:manage', $context)) {
            array_push($roles, 'Instructor');
        } else {
            array_push($roles, 'Learner');
        }
    }

    if (is_siteadmin($user) || has_capability('mod/lti:admin', $context)) {
        // Make sure admins do not have the Learner role, then set admin role.
        $roles = array_diff($roles, array('Learner'));
        if (!$islti2) {
            array_push($roles, 'urn:lti:sysrole:ims/lis/Administrator', 'urn:lti:instrole:ims/lis/Administrator');
        } else {
            array_push($roles, 'http://purl.imsglobal.org/vocab/lis/v2/person#Administrator');
        }
    }

    return join(',', $roles);
}

/**
 * Returns configuration details for the tool
 *
 * @param int $typeid   Basic LTI tool typeid
 *
 * @return array        Tool Configuration
 */
function lti_get_type_config($typeid) {
    global $DB;

    $query = "SELECT name, value
                FROM {lti_types_config}
               WHERE typeid = :typeid1
           UNION ALL
              SELECT 'toolurl' AS name, baseurl AS value
                FROM {lti_types}
               WHERE id = :typeid2
           UNION ALL
              SELECT 'icon' AS name, icon AS value
                FROM {lti_types}
               WHERE id = :typeid3
           UNION ALL
              SELECT 'secureicon' AS name, secureicon AS value
                FROM {lti_types}
               WHERE id = :typeid4";

    $typeconfig = array();
    $configs = $DB->get_records_sql($query,
        array('typeid1' => $typeid, 'typeid2' => $typeid, 'typeid3' => $typeid, 'typeid4' => $typeid));

    if (!empty($configs)) {
        foreach ($configs as $config) {
            $typeconfig[$config->name] = $config->value;
        }
    }

    return $typeconfig;
}

function lti_get_tools_by_url($url, $state, $courseid = null) {
    $domain = lti_get_domain_from_url($url);

    return lti_get_tools_by_domain($domain, $state, $courseid);
}

function lti_get_tools_by_domain($domain, $state = null, $courseid = null) {
    global $DB, $SITE;

    $statefilter = '';
    $coursefilter = '';

    if ($state) {
        $statefilter = 'AND state = :state';
    }

    if ($courseid && $courseid != $SITE->id) {
        $coursefilter = 'OR course = :courseid';
    }

    $query = "SELECT *
                FROM {lti_types}
               WHERE tooldomain = :tooldomain
                 AND (course = :siteid $coursefilter)
                 $statefilter";

    return $DB->get_records_sql($query, array(
        'courseid' => $courseid,
        'siteid' => $SITE->id,
        'tooldomain' => $domain,
        'state' => $state
    ));
}

/**
 * Returns all basicLTI tools configured by the administrator
 *
 * @param int $course
 *
 * @return array
 */
function lti_filter_get_types($course) {
    global $DB;

    if (!empty($course)) {
        $where = "WHERE t.course = :course";
        $params = array('course' => $course);
    } else {
        $where = '';
        $params = array();
    }
    $query = "SELECT t.id, t.name, t.baseurl, t.state, t.toolproxyid, t.timecreated, tp.name tpname
                FROM {lti_types} t LEFT OUTER JOIN {lti_tool_proxies} tp ON t.toolproxyid = tp.id
                {$where}";
    return $DB->get_records_sql($query, $params);
}

/**
 * Given an array of tools, filter them based on their state
 *
 * @param array $tools An array of lti_types records
 * @param int $state One of the LTI_TOOL_STATE_* constants
 * @return array
 */
function lti_filter_tool_types(array $tools, $state) {
    $return = array();
    foreach ($tools as $key => $tool) {
        if ($tool->state == $state) {
            $return[$key] = $tool;
        }
    }
    return $return;
}

/**
 * Returns all lti types visible in this course
 *
 * @param int $courseid The id of the course to retieve types for
 * @param array $coursevisible options for 'coursevisible' field,
 *        default [LTI_COURSEVISIBLE_PRECONFIGURED, LTI_COURSEVISIBLE_ACTIVITYCHOOSER]
 * @return stdClass[] All the lti types visible in the given course
 */
function lti_get_lti_types_by_course($courseid, $coursevisible = null) {
    global $DB, $SITE;

    if ($coursevisible === null) {
        $coursevisible = [LTI_COURSEVISIBLE_PRECONFIGURED, LTI_COURSEVISIBLE_ACTIVITYCHOOSER];
    }

    list($coursevisiblesql, $coursevisparams) = $DB->get_in_or_equal($coursevisible, SQL_PARAMS_NAMED, 'coursevisible');
    $courseconds = [];
    if (has_capability('mod/lti:addmanualinstance', context_course::instance($courseid))) {
        $courseconds[] = "course = :courseid";
    }
    if (has_capability('mod/lti:addpreconfiguredinstance', context_course::instance($courseid))) {
        $courseconds[] = "course = :siteid";
    }
    if (!$courseconds) {
        return [];
    }
    $coursecond = implode(" OR ", $courseconds);
    $query = "SELECT *
                FROM {lti_types}
               WHERE coursevisible $coursevisiblesql
                 AND ($coursecond)
                 AND state = :active";

    return $DB->get_records_sql($query,
        array('siteid' => $SITE->id, 'courseid' => $courseid, 'active' => LTI_TOOL_STATE_CONFIGURED) + $coursevisparams);
}

/**
 * Returns tool types for lti add instance and edit page
 *
 * @return array Array of lti types
 */
function lti_get_types_for_add_instance() {
    global $COURSE;
    $admintypes = lti_get_lti_types_by_course($COURSE->id);

    $types = array();
    if (has_capability('mod/lti:addmanualinstance', context_course::instance($COURSE->id))) {
        $types[0] = (object)array('name' => get_string('automatic', 'lti'), 'course' => 0, 'toolproxyid' => null);
    }

    foreach ($admintypes as $type) {
        $types[$type->id] = $type;
    }

    return $types;
}

/**
 * Returns a list of configured types in the given course
 *
 * @param int $courseid The id of the course to retieve types for
 * @param int $sectionreturn section to return to for forming the URLs
 * @return array Array of lti types. Each element is object with properties: name, title, icon, help, helplink, link
 */
function lti_get_configured_types($courseid, $sectionreturn = 0) {
    global $OUTPUT;
    $types = array();
    $admintypes = lti_get_lti_types_by_course($courseid, [LTI_COURSEVISIBLE_ACTIVITYCHOOSER]);

    foreach ($admintypes as $ltitype) {
        $type           = new stdClass();
        $type->id       = $ltitype->id;
        $type->modclass = MOD_CLASS_ACTIVITY;
        $type->name     = 'lti_type_' . $ltitype->id;
        // Clean the name. We don't want tags here.
        $type->title    = clean_param($ltitype->name, PARAM_NOTAGS);
        $trimmeddescription = trim($ltitype->description);
        if ($trimmeddescription != '') {
            // Clean the description. We don't want tags here.
            $type->help     = clean_param($trimmeddescription, PARAM_NOTAGS);
            $type->helplink = get_string('modulename_shortcut_link', 'lti');
        }
        if (empty($ltitype->icon)) {
            $type->icon = $OUTPUT->pix_icon('icon', '', 'lti', array('class' => 'icon'));
        } else {
            $type->icon = html_writer::empty_tag('img', array('src' => $ltitype->icon, 'alt' => '', 'class' => 'icon'));
        }
        $type->link = new moodle_url('/course/modedit.php', array('add' => 'lti', 'return' => 0, 'course' => $courseid,
            'sr' => $sectionreturn, 'typeid' => $ltitype->id));
        $types[] = $type;
    }
    return $types;
}

function lti_get_domain_from_url($url) {
    $matches = array();

    if (preg_match(LTI_URL_DOMAIN_REGEX, $url, $matches)) {
        return $matches[1];
    }
}

function lti_get_tool_by_url_match($url, $courseid = null, $state = LTI_TOOL_STATE_CONFIGURED) {
    $possibletools = lti_get_tools_by_url($url, $state, $courseid);

    return lti_get_best_tool_by_url($url, $possibletools, $courseid);
}

function lti_get_url_thumbprint($url) {
    // Parse URL requires a schema otherwise everything goes into 'path'.  Fixed 5.4.7 or later.
    if (preg_match('/https?:\/\//', $url) !== 1) {
        $url = 'http://'.$url;
    }
    $urlparts = parse_url(strtolower($url));
    if (!isset($urlparts['path'])) {
        $urlparts['path'] = '';
    }

    if (!isset($urlparts['query'])) {
        $urlparts['query'] = '';
    }

    if (!isset($urlparts['host'])) {
        $urlparts['host'] = '';
    }

    if (substr($urlparts['host'], 0, 4) === 'www.') {
        $urlparts['host'] = substr($urlparts['host'], 4);
    }

    $urllower = $urlparts['host'] . '/' . $urlparts['path'];

    if ($urlparts['query'] != '') {
        $urllower .= '?' . $urlparts['query'];
    }

    return $urllower;
}

function lti_get_best_tool_by_url($url, $tools, $courseid = null) {
    if (count($tools) === 0) {
        return null;
    }

    $urllower = lti_get_url_thumbprint($url);

    foreach ($tools as $tool) {
        $tool->_matchscore = 0;

        $toolbaseurllower = lti_get_url_thumbprint($tool->baseurl);

        if ($urllower === $toolbaseurllower) {
            // 100 points for exact thumbprint match.
            $tool->_matchscore += 100;
        } else if (substr($urllower, 0, strlen($toolbaseurllower)) === $toolbaseurllower) {
            // 50 points if tool thumbprint starts with the base URL thumbprint.
            $tool->_matchscore += 50;
        }

        // Prefer course tools over site tools.
        if (!empty($courseid)) {
            // Minus 10 points for not matching the course id (global tools).
            if ($tool->course != $courseid) {
                $tool->_matchscore -= 10;
            }
        }
    }

    $bestmatch = array_reduce($tools, function($value, $tool) {
        if ($tool->_matchscore > $value->_matchscore) {
            return $tool;
        } else {
            return $value;
        }

    }, (object)array('_matchscore' => -1));

    // None of the tools are suitable for this URL.
    if ($bestmatch->_matchscore <= 0) {
        return null;
    }

    return $bestmatch;
}

function lti_get_shared_secrets_by_key($key) {
    global $DB;

    // Look up the shared secret for the specified key in both the types_config table (for configured tools)
    // And in the lti resource table for ad-hoc tools.
    $lti13 = LTI_VERSION_1P3;
    $query = "SELECT " . $DB->sql_compare_text('t2.value', 256) . " AS value
                FROM {lti_types_config} t1
                JOIN {lti_types_config} t2 ON t1.typeid = t2.typeid
                JOIN {lti_types} type ON t2.typeid = type.id
              WHERE t1.name = 'resourcekey'
                AND " . $DB->sql_compare_text('t1.value', 256) . " = :key1
                AND t2.name = 'password'
                AND type.state = :configured1
                AND type.ltiversion <> :ltiversion
               UNION
              SELECT tp.secret AS value
                FROM {lti_tool_proxies} tp
                JOIN {lti_types} t ON tp.id = t.toolproxyid
              WHERE tp.guid = :key2
                AND t.state = :configured2
               UNION
              SELECT password AS value
               FROM {lti}
              WHERE resourcekey = :key3";

    $sharedsecrets = $DB->get_records_sql($query, array('configured1' => LTI_TOOL_STATE_CONFIGURED, 'ltiversion' => $lti13,
        'configured2' => LTI_TOOL_STATE_CONFIGURED, 'key1' => $key, 'key2' => $key, 'key3' => $key));

    $values = array_map(function($item) {
        return $item->value;
    }, $sharedsecrets);

    // There should really only be one shared secret per key. But, we can't prevent
    // more than one getting entered. For instance, if the same key is used for two tool providers.
    return $values;
}

/**
 * Delete a Basic LTI configuration
 *
 * @param int $id   Configuration id
 */
function lti_delete_type($id) {
    global $DB;

    // We should probably just copy the launch URL to the tool instances in this case... using a single query.
    /*
    $instances = $DB->get_records('lti', array('typeid' => $id));
    foreach ($instances as $instance) {
        $instance->typeid = 0;
        $DB->update_record('lti', $instance);
    }*/

    $DB->delete_records('lti_types', array('id' => $id));
    $DB->delete_records('lti_types_config', array('typeid' => $id));
}

function lti_set_state_for_type($id, $state) {
    global $DB;

    $DB->update_record('lti_types', (object)array('id' => $id, 'state' => $state));
}

/**
 * Transforms a basic LTI object to an array
 *
 * @param object $ltiobject    Basic LTI object
 *
 * @return array Basic LTI configuration details
 */
function lti_get_config($ltiobject) {
    $typeconfig = (array)$ltiobject;
    $additionalconfig = lti_get_type_config($ltiobject->typeid);
    $typeconfig = array_merge($typeconfig, $additionalconfig);
    return $typeconfig;
}

/**
 *
 * Generates some of the tool configuration based on the instance details
 *
 * @param int $id
 *
 * @return object configuration
 *
 */
function lti_get_type_config_from_instance($id) {
    global $DB;

    $instance = $DB->get_record('lti', array('id' => $id));
    $config = lti_get_config($instance);

    $type = new \stdClass();
    $type->lti_fix = $id;
    if (isset($config['toolurl'])) {
        $type->lti_toolurl = $config['toolurl'];
    }
    if (isset($config['instructorchoicesendname'])) {
        $type->lti_sendname = $config['instructorchoicesendname'];
    }
    if (isset($config['instructorchoicesendemailaddr'])) {
        $type->lti_sendemailaddr = $config['instructorchoicesendemailaddr'];
    }
    if (isset($config['instructorchoiceacceptgrades'])) {
        $type->lti_acceptgrades = $config['instructorchoiceacceptgrades'];
    }
    if (isset($config['instructorchoiceallowroster'])) {
        $type->lti_allowroster = $config['instructorchoiceallowroster'];
    }

    if (isset($config['instructorcustomparameters'])) {
        $type->lti_allowsetting = $config['instructorcustomparameters'];
    }
    return $type;
}

/**
 * Generates some of the tool configuration based on the admin configuration details
 *
 * @param int $id
 *
 * @return stdClass Configuration details
 */
function lti_get_type_type_config($id) {
    global $DB;

    $basicltitype = $DB->get_record('lti_types', array('id' => $id));
    $config = lti_get_type_config($id);

    $type = new \stdClass();

    $type->lti_typename = $basicltitype->name;

    $type->typeid = $basicltitype->id;

    $type->toolproxyid = $basicltitype->toolproxyid;

    $type->lti_toolurl = $basicltitype->baseurl;

    $type->lti_ltiversion = $basicltitype->ltiversion;

    $type->lti_clientid = $basicltitype->clientid;
    $type->lti_clientid_disabled = $type->lti_clientid;

    $type->lti_description = $basicltitype->description;

    $type->lti_parameters = $basicltitype->parameter;

    $type->lti_icon = $basicltitype->icon;

    $type->lti_secureicon = $basicltitype->secureicon;

    if (isset($config['resourcekey'])) {
        $type->lti_resourcekey = $config['resourcekey'];
    }
    if (isset($config['password'])) {
        $type->lti_password = $config['password'];
    }
    if (isset($config['publickey'])) {
        $type->lti_publickey = $config['publickey'];
    }
    if (isset($config['publickeyset'])) {
        $type->lti_publickeyset = $config['publickeyset'];
    }
    if (isset($config['keytype'])) {
        $type->lti_keytype = $config['keytype'];
    }
    if (isset($config['initiatelogin'])) {
        $type->lti_initiatelogin = $config['initiatelogin'];
    }
    if (isset($config['redirectionuris'])) {
        $type->lti_redirectionuris = $config['redirectionuris'];
    }

    if (isset($config['sendname'])) {
        $type->lti_sendname = $config['sendname'];
    }
    if (isset($config['instructorchoicesendname'])) {
        $type->lti_instructorchoicesendname = $config['instructorchoicesendname'];
    }
    if (isset($config['sendemailaddr'])) {
        $type->lti_sendemailaddr = $config['sendemailaddr'];
    }
    if (isset($config['instructorchoicesendemailaddr'])) {
        $type->lti_instructorchoicesendemailaddr = $config['instructorchoicesendemailaddr'];
    }
    if (isset($config['acceptgrades'])) {
        $type->lti_acceptgrades = $config['acceptgrades'];
    }
    if (isset($config['instructorchoiceacceptgrades'])) {
        $type->lti_instructorchoiceacceptgrades = $config['instructorchoiceacceptgrades'];
    }
    if (isset($config['allowroster'])) {
        $type->lti_allowroster = $config['allowroster'];
    }
    if (isset($config['instructorchoiceallowroster'])) {
        $type->lti_instructorchoiceallowroster = $config['instructorchoiceallowroster'];
    }

    if (isset($config['customparameters'])) {
        $type->lti_customparameters = $config['customparameters'];
    }

    if (isset($config['forcessl'])) {
        $type->lti_forcessl = $config['forcessl'];
    }

    if (isset($config['organizationid_default'])) {
        $type->lti_organizationid_default = $config['organizationid_default'];
    } else {
        // Tool was configured before this option was available and the default then was host.
        $type->lti_organizationid_default = LTI_DEFAULT_ORGID_SITEHOST;
    }
    if (isset($config['organizationid'])) {
        $type->lti_organizationid = $config['organizationid'];
    }
    if (isset($config['organizationurl'])) {
        $type->lti_organizationurl = $config['organizationurl'];
    }
    if (isset($config['organizationdescr'])) {
        $type->lti_organizationdescr = $config['organizationdescr'];
    }
    if (isset($config['launchcontainer'])) {
        $type->lti_launchcontainer = $config['launchcontainer'];
    }

    if (isset($config['coursevisible'])) {
        $type->lti_coursevisible = $config['coursevisible'];
    }

    if (isset($config['contentitem'])) {
        $type->lti_contentitem = $config['contentitem'];
    }

    if (isset($config['toolurl_ContentItemSelectionRequest'])) {
        $type->lti_toolurl_ContentItemSelectionRequest = $config['toolurl_ContentItemSelectionRequest'];
    }

    if (isset($config['debuglaunch'])) {
        $type->lti_debuglaunch = $config['debuglaunch'];
    }

    if (isset($config['module_class_type'])) {
        $type->lti_module_class_type = $config['module_class_type'];
    }

    // Get the parameters from the LTI services.
    foreach ($config as $name => $value) {
        if (strpos($name, 'ltiservice_') === 0) {
            $type->{$name} = $config[$name];
        }
    }

    return $type;
}

function lti_prepare_type_for_save($type, $config) {
    if (isset($config->lti_toolurl)) {
        $type->baseurl = $config->lti_toolurl;
        $type->tooldomain = lti_get_domain_from_url($config->lti_toolurl);
    }
    if (isset($config->lti_description)) {
        $type->description = $config->lti_description;
    }
    if (isset($config->lti_typename)) {
        $type->name = $config->lti_typename;
    }
    if (isset($config->lti_ltiversion)) {
        $type->ltiversion = $config->lti_ltiversion;
    }
    if (isset($config->lti_clientid)) {
        $type->clientid = $config->lti_clientid;
    }
    if ((!empty($type->ltiversion) && $type->ltiversion === LTI_VERSION_1P3) && empty($type->clientid)) {
        $type->clientid = random_string(15);
    } else if (empty($type->clientid)) {
        $type->clientid = null;
    }
    if (isset($config->lti_coursevisible)) {
        $type->coursevisible = $config->lti_coursevisible;
    }

    if (isset($config->lti_icon)) {
        $type->icon = $config->lti_icon;
    }
    if (isset($config->lti_secureicon)) {
        $type->secureicon = $config->lti_secureicon;
    }

    $type->forcessl = !empty($config->lti_forcessl) ? $config->lti_forcessl : 0;
    $config->lti_forcessl = $type->forcessl;
    if (isset($config->lti_contentitem)) {
        $type->contentitem = !empty($config->lti_contentitem) ? $config->lti_contentitem : 0;
        $config->lti_contentitem = $type->contentitem;
    }
    if (isset($config->lti_toolurl_ContentItemSelectionRequest)) {
        if (!empty($config->lti_toolurl_ContentItemSelectionRequest)) {
            $type->toolurl_ContentItemSelectionRequest = $config->lti_toolurl_ContentItemSelectionRequest;
        } else {
            $type->toolurl_ContentItemSelectionRequest = '';
        }
        $config->lti_toolurl_ContentItemSelectionRequest = $type->toolurl_ContentItemSelectionRequest;
    }

    $type->timemodified = time();

    unset ($config->lti_typename);
    unset ($config->lti_toolurl);
    unset ($config->lti_description);
    unset ($config->lti_ltiversion);
    unset ($config->lti_clientid);
    unset ($config->lti_icon);
    unset ($config->lti_secureicon);
}

function lti_update_type($type, $config) {
    global $DB, $CFG;

    lti_prepare_type_for_save($type, $config);

    if (lti_request_is_using_ssl() && !empty($type->secureicon)) {
        $clearcache = !isset($config->oldicon) || ($config->oldicon !== $type->secureicon);
    } else {
        $clearcache = isset($type->icon) && (!isset($config->oldicon) || ($config->oldicon !== $type->icon));
    }
    unset($config->oldicon);

    if ($DB->update_record('lti_types', $type)) {
        foreach ($config as $key => $value) {
            if (substr($key, 0, 4) == 'lti_' && !is_null($value)) {
                $record = new \StdClass();
                $record->typeid = $type->id;
                $record->name = substr($key, 4);
                $record->value = $value;
                lti_update_config($record);
            }
            if (substr($key, 0, 11) == 'ltiservice_' && !is_null($value)) {
                $record = new \StdClass();
                $record->typeid = $type->id;
                $record->name = $key;
                $record->value = $value;
                lti_update_config($record);
            }
        }
        require_once($CFG->libdir.'/modinfolib.php');
        if ($clearcache) {
            $sql = "SELECT DISTINCT course
                      FROM {lti}
                     WHERE typeid = ?";

            $courses = $DB->get_fieldset_sql($sql, array($type->id));

            foreach ($courses as $courseid) {
                rebuild_course_cache($courseid, true);
            }
        }
    }
}

function lti_add_type($type, $config) {
    global $USER, $SITE, $DB;

    lti_prepare_type_for_save($type, $config);

    if (!isset($type->state)) {
        $type->state = LTI_TOOL_STATE_PENDING;
    }

    if (!isset($type->ltiversion)) {
        $type->ltiversion = LTI_VERSION_1;
    }

    if (!isset($type->timecreated)) {
        $type->timecreated = time();
    }

    if (!isset($type->createdby)) {
        $type->createdby = $USER->id;
    }

    if (!isset($type->course)) {
        $type->course = $SITE->id;
    }

    // Create a salt value to be used for signing passed data to extension services
    // The outcome service uses the service salt on the instance. This can be used
    // for communication with services not related to a specific LTI instance.
    $config->lti_servicesalt = uniqid('', true);

    $id = $DB->insert_record('lti_types', $type);

    if ($id) {
        foreach ($config as $key => $value) {
            if (!is_null($value)) {
                if (substr($key, 0, 4) === 'lti_') {
                    $fieldname = substr($key, 4);
                } else if (substr($key, 0, 11) !== 'ltiservice_') {
                    continue;
                } else {
                    $fieldname = $key;
                }

                $record = new \StdClass();
                $record->typeid = $id;
                $record->name = $fieldname;
                $record->value = $value;

                lti_add_config($record);
            }
        }
    }

    return $id;
}

/**
 * Given an array of tool proxies, filter them based on their state
 *
 * @param array $toolproxies An array of lti_tool_proxies records
 * @param int $state One of the LTI_TOOL_PROXY_STATE_* constants
 *
 * @return array
 */
function lti_filter_tool_proxy_types(array $toolproxies, $state) {
    $return = array();
    foreach ($toolproxies as $key => $toolproxy) {
        if ($toolproxy->state == $state) {
            $return[$key] = $toolproxy;
        }
    }
    return $return;
}

/**
 * Get the tool proxy instance given its GUID
 *
 * @param string  $toolproxyguid   Tool proxy GUID value
 *
 * @return object
 */
function lti_get_tool_proxy_from_guid($toolproxyguid) {
    global $DB;

    $toolproxy = $DB->get_record('lti_tool_proxies', array('guid' => $toolproxyguid));

    return $toolproxy;
}

/**
 * Get the tool proxy instance given its registration URL
 *
 * @param string $regurl Tool proxy registration URL
 *
 * @return array The record of the tool proxy with this url
 */
function lti_get_tool_proxies_from_registration_url($regurl) {
    global $DB;

    return $DB->get_records_sql(
        'SELECT * FROM {lti_tool_proxies}
        WHERE '.$DB->sql_compare_text('regurl', 256).' = :regurl',
        array('regurl' => $regurl)
    );
}

/**
 * Generates some of the tool proxy configuration based on the admin configuration details
 *
 * @param int $id
 *
 * @return mixed Tool Proxy details
 */
function lti_get_tool_proxy($id) {
    global $DB;

    $toolproxy = $DB->get_record('lti_tool_proxies', array('id' => $id));
    return $toolproxy;
}

/**
 * Returns lti tool proxies.
 *
 * @param bool $orphanedonly Only retrieves tool proxies that have no type associated with them
 * @return array of basicLTI types
 */
function lti_get_tool_proxies($orphanedonly) {
    global $DB;

    if ($orphanedonly) {
        $usedproxyids = array_values($DB->get_fieldset_select('lti_types', 'toolproxyid', 'toolproxyid IS NOT NULL'));
        $proxies = $DB->get_records('lti_tool_proxies', null, 'state DESC, timemodified DESC');
        foreach ($proxies as $key => $value) {
            if (in_array($value->id, $usedproxyids)) {
                unset($proxies[$key]);
            }
        }
        return $proxies;
    } else {
        return $DB->get_records('lti_tool_proxies', null, 'state DESC, timemodified DESC');
    }
}

/**
 * Generates some of the tool proxy configuration based on the admin configuration details
 *
 * @param int $id
 *
 * @return mixed  Tool Proxy details
 */
function lti_get_tool_proxy_config($id) {
    $toolproxy = lti_get_tool_proxy($id);

    $tp = new \stdClass();
    $tp->lti_registrationname = $toolproxy->name;
    $tp->toolproxyid = $toolproxy->id;
    $tp->state = $toolproxy->state;
    $tp->lti_registrationurl = $toolproxy->regurl;
    $tp->lti_capabilities = explode("\n", $toolproxy->capabilityoffered);
    $tp->lti_services = explode("\n", $toolproxy->serviceoffered);

    return $tp;
}

/**
 * Update the database with a tool proxy instance
 *
 * @param object   $config    Tool proxy definition
 *
 * @return int  Record id number
 */
function lti_add_tool_proxy($config) {
    global $USER, $DB;

    $toolproxy = new \stdClass();
    if (isset($config->lti_registrationname)) {
        $toolproxy->name = trim($config->lti_registrationname);
    }
    if (isset($config->lti_registrationurl)) {
        $toolproxy->regurl = trim($config->lti_registrationurl);
    }
    if (isset($config->lti_capabilities)) {
        $toolproxy->capabilityoffered = implode("\n", $config->lti_capabilities);
    } else {
        $toolproxy->capabilityoffered = implode("\n", array_keys(lti_get_capabilities()));
    }
    if (isset($config->lti_services)) {
        $toolproxy->serviceoffered = implode("\n", $config->lti_services);
    } else {
        $func = function($s) {
            return $s->get_id();
        };
        $servicenames = array_map($func, lti_get_services());
        $toolproxy->serviceoffered = implode("\n", $servicenames);
    }
    if (isset($config->toolproxyid) && !empty($config->toolproxyid)) {
        $toolproxy->id = $config->toolproxyid;
        if (!isset($toolproxy->state) || ($toolproxy->state != LTI_TOOL_PROXY_STATE_ACCEPTED)) {
            $toolproxy->state = LTI_TOOL_PROXY_STATE_CONFIGURED;
            $toolproxy->guid = random_string();
            $toolproxy->secret = random_string();
        }
        $id = lti_update_tool_proxy($toolproxy);
    } else {
        $toolproxy->state = LTI_TOOL_PROXY_STATE_CONFIGURED;
        $toolproxy->timemodified = time();
        $toolproxy->timecreated = $toolproxy->timemodified;
        if (!isset($toolproxy->createdby)) {
            $toolproxy->createdby = $USER->id;
        }
        $toolproxy->guid = random_string();
        $toolproxy->secret = random_string();
        $id = $DB->insert_record('lti_tool_proxies', $toolproxy);
    }

    return $id;
}

/**
 * Updates a tool proxy in the database
 *
 * @param object  $toolproxy   Tool proxy
 *
 * @return int    Record id number
 */
function lti_update_tool_proxy($toolproxy) {
    global $DB;

    $toolproxy->timemodified = time();
    $id = $DB->update_record('lti_tool_proxies', $toolproxy);

    return $id;
}

/**
 * Delete a Tool Proxy
 *
 * @param int $id   Tool Proxy id
 */
function lti_delete_tool_proxy($id) {
    global $DB;
    $DB->delete_records('lti_tool_settings', array('toolproxyid' => $id));
    $tools = $DB->get_records('lti_types', array('toolproxyid' => $id));
    foreach ($tools as $tool) {
        lti_delete_type($tool->id);
    }
    $DB->delete_records('lti_tool_proxies', array('id' => $id));
}

/**
 * Add a tool configuration in the database
 *
 * @param object $config   Tool configuration
 *
 * @return int Record id number
 */
function lti_add_config($config) {
    global $DB;

    return $DB->insert_record('lti_types_config', $config);
}

/**
 * Updates a tool configuration in the database
 *
 * @param object  $config   Tool configuration
 *
 * @return mixed Record id number
 */
function lti_update_config($config) {
    global $DB;

    $old = $DB->get_record('lti_types_config', array('typeid' => $config->typeid, 'name' => $config->name));

    if ($old) {
        $config->id = $old->id;
        $return = $DB->update_record('lti_types_config', $config);
    } else {
        $return = $DB->insert_record('lti_types_config', $config);
    }
    return $return;
}

/**
 * Gets the tool settings
 *
 * @param int  $toolproxyid   Id of tool proxy record (or tool ID if negative)
 * @param int  $courseid      Id of course (null if system settings)
 * @param int  $instanceid    Id of course module (null if system or context settings)
 *
 * @return array  Array settings
 */
function lti_get_tool_settings($toolproxyid, $courseid = null, $instanceid = null) {
    global $DB;

    $settings = array();
    if ($toolproxyid > 0) {
        $settingsstr = $DB->get_field('lti_tool_settings', 'settings', array('toolproxyid' => $toolproxyid,
            'course' => $courseid, 'coursemoduleid' => $instanceid));
    } else {
        $settingsstr = $DB->get_field('lti_tool_settings', 'settings', array('typeid' => -$toolproxyid,
            'course' => $courseid, 'coursemoduleid' => $instanceid));
    }
    if ($settingsstr !== false) {
        $settings = json_decode($settingsstr, true);
    }
    return $settings;
}

/**
 * Sets the tool settings (
 *
 * @param array  $settings      Array of settings
 * @param int    $toolproxyid   Id of tool proxy record (or tool ID if negative)
 * @param int    $courseid      Id of course (null if system settings)
 * @param int    $instanceid    Id of course module (null if system or context settings)
 */
function lti_set_tool_settings($settings, $toolproxyid, $courseid = null, $instanceid = null) {
    global $DB;

    $json = json_encode($settings);
    if ($toolproxyid >= 0) {
        $record = $DB->get_record('lti_tool_settings', array('toolproxyid' => $toolproxyid,
            'course' => $courseid, 'coursemoduleid' => $instanceid));
    } else {
        $record = $DB->get_record('lti_tool_settings', array('typeid' => -$toolproxyid,
            'course' => $courseid, 'coursemoduleid' => $instanceid));
    }
    if ($record !== false) {
        $DB->update_record('lti_tool_settings', (object)array('id' => $record->id, 'settings' => $json, 'timemodified' => time()));
    } else {
        $record = new \stdClass();
        if ($toolproxyid > 0) {
            $record->toolproxyid = $toolproxyid;
        } else {
            $record->typeid = -$toolproxyid;
        }
        $record->course = $courseid;
        $record->coursemoduleid = $instanceid;
        $record->settings = $json;
        $record->timecreated = time();
        $record->timemodified = $record->timecreated;
        $DB->insert_record('lti_tool_settings', $record);
    }
}

/**
 * Signs the petition to launch the external tool using OAuth
 *
 * @param array  $oldparms     Parameters to be passed for signing
 * @param string $endpoint     url of the external tool
 * @param string $method       Method for sending the parameters (e.g. POST)
 * @param string $oauthconsumerkey
 * @param string $oauthconsumersecret
 * @return array|null
 */
function lti_sign_parameters($oldparms, $endpoint, $method, $oauthconsumerkey, $oauthconsumersecret) {

    $parms = $oldparms;

    $testtoken = '';

    // TODO: Switch to core oauthlib once implemented - MDL-30149.
    $hmacmethod = new lti\OAuthSignatureMethod_HMAC_SHA1();
    $testconsumer = new lti\OAuthConsumer($oauthconsumerkey, $oauthconsumersecret, null);
    $accreq = lti\OAuthRequest::from_consumer_and_token($testconsumer, $testtoken, $method, $endpoint, $parms);
    $accreq->sign_request($hmacmethod, $testconsumer, $testtoken);

    $newparms = $accreq->get_parameters();

    return $newparms;
}

/**
 * Converts the message paramters to their equivalent JWT claim and signs the payload to launch the external tool using JWT
 *
 * @param array  $parms        Parameters to be passed for signing
 * @param string $endpoint     url of the external tool
 * @param string $oauthconsumerkey
 * @param string $typeid       ID of LTI tool type
 * @param string $nonce        Nonce value to use
 * @return array|null
 */
function lti_sign_jwt($parms, $endpoint, $oauthconsumerkey, $typeid = 0, $nonce = '') {
    global $CFG;

    if (empty($typeid)) {
        $typeid = 0;
    }
    $messagetypemapping = lti_get_jwt_message_type_mapping();
    if (isset($parms['lti_message_type']) && array_key_exists($parms['lti_message_type'], $messagetypemapping)) {
        $parms['lti_message_type'] = $messagetypemapping[$parms['lti_message_type']];
    }
    if (isset($parms['roles'])) {
        $roles = explode(',', $parms['roles']);
        $newroles = array();
        foreach ($roles as $role) {
            if (strpos($role, 'urn:lti:role:ims/lis/') === 0) {
                $role = 'http://purl.imsglobal.org/vocab/lis/v2/membership#' . substr($role, 21);
            } else if (strpos($role, 'urn:lti:instrole:ims/lis/') === 0) {
                $role = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#' . substr($role, 25);
            } else if (strpos($role, 'urn:lti:sysrole:ims/lis/') === 0) {
                $role = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#' . substr($role, 24);
            } else if ((strpos($role, '://') === false) && (strpos($role, 'urn:') !== 0)) {
                $role = "http://purl.imsglobal.org/vocab/lis/v2/membership#{$role}";
            }
            $newroles[] = $role;
        }
        $parms['roles'] = implode(',', $newroles);
    }

    $now = time();
    if (empty($nonce)) {
        $nonce = bin2hex(openssl_random_pseudo_bytes(10));
    }
    $claimmapping = lti_get_jwt_claim_mapping();
    $payload = array(
        'nonce' => $nonce,
        'iat' => $now,
        'exp' => $now + 60,
    );
    $payload['iss'] = $CFG->wwwroot;
    $payload['aud'] = $oauthconsumerkey;
    $payload[LTI_JWT_CLAIM_PREFIX . '/claim/deployment_id'] = strval($typeid);
    $payload[LTI_JWT_CLAIM_PREFIX . '/claim/target_link_uri'] = $endpoint;

    foreach ($parms as $key => $value) {
        $claim = LTI_JWT_CLAIM_PREFIX;
        if (array_key_exists($key, $claimmapping)) {
            $mapping = $claimmapping[$key];
            if ($mapping['isarray']) {
                $value = explode(',', $value);
                sort($value);
            }
            if (!empty($mapping['suffix'])) {
                $claim .= "-{$mapping['suffix']}";
            }
            $claim .= '/claim/';
            if (is_null($mapping['group'])) {
                $payload[$mapping['claim']] = $value;
            } else if (empty($mapping['group'])) {
                $payload["{$claim}{$mapping['claim']}"] = $value;
            } else {
                $claim .= $mapping['group'];
                $payload[$claim][$mapping['claim']] = $value;
            }
        } else if (strpos($key, 'custom_') === 0) {
            $payload["{$claim}/claim/custom"][substr($key, 7)] = $value;
        } else if (strpos($key, 'ext_') === 0) {
            $payload["{$claim}/claim/ext"][substr($key, 4)] = $value;
        }
    }

    $privatekey = get_config('mod_lti', 'privatekey');
    $kid = get_config('mod_lti', 'kid');
    $jwt = JWT::encode($payload, $privatekey, 'RS256', $kid);

    $newparms = array();
    $newparms['id_token'] = $jwt;

    return $newparms;
}

/**
 * Verfies the JWT and converts its claims to their equivalent message parameter.
 *
 * @param int    $typeid
 * @param string $jwtparam   JWT parameter
 *
 * @return array  message parameters
 * @throws moodle_exception
 */
function lti_convert_from_jwt($typeid, $jwtparam) {

    $params = array();
    $parts = explode('.', $jwtparam);
    $ok = (count($parts) === 3);
    if ($ok) {
        $payload = JWT::urlsafeB64Decode($parts[1]);
        $claims = json_decode($payload, true);
        $ok = !is_null($claims) && !empty($claims['iss']);
    }
    if ($ok) {
        lti_verify_jwt_signature($typeid, $claims['iss'], $jwtparam);
        $params['oauth_consumer_key'] = $claims['iss'];
        foreach (lti_get_jwt_claim_mapping() as $key => $mapping) {
            $claim = LTI_JWT_CLAIM_PREFIX;
            if (!empty($mapping['suffix'])) {
                $claim .= "-{$mapping['suffix']}";
            }
            $claim .= '/claim/';
            if (is_null($mapping['group'])) {
                $claim = $mapping['claim'];
            } else if (empty($mapping['group'])) {
                $claim .= $mapping['claim'];
            } else {
                $claim .= $mapping['group'];
            }
            if (isset($claims[$claim])) {
                $value = null;
                if (empty($mapping['group'])) {
                    $value = $claims[$claim];
                } else {
                    $group = $claims[$claim];
                    if (is_array($group) && array_key_exists($mapping['claim'], $group)) {
                        $value = $group[$mapping['claim']];
                    }
                }
                if (!empty($value) && $mapping['isarray']) {
                    if (is_array($value)) {
                        if (is_array($value[0])) {
                            $value = json_encode($value);
                        } else {
                            $value = implode(',', $value);
                        }
                    }
                }
                if (!is_null($value) && is_string($value) && (strlen($value) > 0)) {
                    $params[$key] = $value;
                }
            }
            $claim = LTI_JWT_CLAIM_PREFIX . '/claim/custom';
            if (isset($claims[$claim])) {
                $custom = $claims[$claim];
                if (is_array($custom)) {
                    foreach ($custom as $key => $value) {
                        $params["custom_{$key}"] = $value;
                    }
                }
            }
            $claim = LTI_JWT_CLAIM_PREFIX . '/claim/ext';
            if (isset($claims[$claim])) {
                $ext = $claims[$claim];
                if (is_array($ext)) {
                    foreach ($ext as $key => $value) {
                        $params["ext_{$key}"] = $value;
                    }
                }
            }
        }
    }
    if (isset($params['content_items'])) {
        $params['content_items'] = lti_convert_content_items($params['content_items']);
    }
    $messagetypemapping = lti_get_jwt_message_type_mapping();
    if (isset($params['lti_message_type']) && array_key_exists($params['lti_message_type'], $messagetypemapping)) {
        $params['lti_message_type'] = $messagetypemapping[$params['lti_message_type']];
    }
    return $params;
}

/**
 * Posts the launch petition HTML
 *
 * @param array $newparms   Signed parameters
 * @param string $endpoint  URL of the external tool
 * @param bool $debug       Debug (true/false)
 * @return string
 */
function lti_post_launch_html($newparms, $endpoint, $debug=false) {
    $r = "<form action=\"" . $endpoint .
        "\" name=\"ltiLaunchForm\" id=\"ltiLaunchForm\" method=\"post\" encType=\"application/x-www-form-urlencoded\">\n";

    // Contruct html for the launch parameters.
    foreach ($newparms as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        if ( $key == "ext_submit" ) {
            $r .= "<input type=\"submit\"";
        } else {
            $r .= "<input type=\"hidden\" name=\"{$key}\"";
        }
        $r .= " value=\"";
        $r .= $value;
        $r .= "\"/>\n";
    }

    if ( $debug ) {
        $r .= "<script language=\"javascript\"> \n";
        $r .= "  //<![CDATA[ \n";
        $r .= "function basicltiDebugToggle() {\n";
        $r .= "    var ele = document.getElementById(\"basicltiDebug\");\n";
        $r .= "    if (ele.style.display == \"block\") {\n";
        $r .= "        ele.style.display = \"none\";\n";
        $r .= "    }\n";
        $r .= "    else {\n";
        $r .= "        ele.style.display = \"block\";\n";
        $r .= "    }\n";
        $r .= "} \n";
        $r .= "  //]]> \n";
        $r .= "</script>\n";
        $r .= "<a id=\"displayText\" href=\"javascript:basicltiDebugToggle();\">";
        $r .= get_string("toggle_debug_data", "lti")."</a>\n";
        $r .= "<div id=\"basicltiDebug\" style=\"display:none\">\n";
        $r .= "<b>".get_string("basiclti_endpoint", "lti")."</b><br/>\n";
        $r .= $endpoint . "<br/>\n&nbsp;<br/>\n";
        $r .= "<b>".get_string("basiclti_parameters", "lti")."</b><br/>\n";
        foreach ($newparms as $key => $value) {
            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value);
            $r .= "$key = $value<br/>\n";
        }
        $r .= "&nbsp;<br/>\n";
        $r .= "</div>\n";
    }
    $r .= "</form>\n";

    if ( ! $debug ) {
        $r .= " <script type=\"text/javascript\"> \n" .
            "  //<![CDATA[ \n" .
            "    document.ltiLaunchForm.submit(); \n" .
            "  //]]> \n" .
            " </script> \n";
    }
    return $r;
}

/**
 * Generate the form for initiating a login request for an LTI 1.3 message
 *
 * @param int            $courseid  Course ID
 * @param int            $id        LTI instance ID
 * @param stdClass|null  $instance  LTI instance
 * @param stdClass       $config    Tool type configuration
 * @param string         $messagetype   LTI message type
 * @param string         $title     Title of content item
 * @param string         $text      Description of content item
 * @return string
 */
function lti_initiate_login($courseid, $id, $instance, $config, $messagetype = 'basic-lti-launch-request', $title = '',
        $text = '') {
    global $SESSION;

    $params = lti_build_login_request($courseid, $id, $instance, $config, $messagetype);
    $SESSION->lti_message_hint = "{$courseid},{$config->typeid},{$id}," . base64_encode($title) . ',' .
        base64_encode($text);

    $r = "<form action=\"" . $config->lti_initiatelogin .
        "\" name=\"ltiInitiateLoginForm\" id=\"ltiInitiateLoginForm\" method=\"post\" " .
        "encType=\"application/x-www-form-urlencoded\">\n";

    foreach ($params as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        $r .= "  <input type=\"hidden\" name=\"{$key}\" value=\"{$value}\"/>\n";
    }
    $r .= "</form>\n";

    $r .= "<script type=\"text/javascript\">\n" .
        "//<![CDATA[\n" .
        "document.ltiInitiateLoginForm.submit();\n" .
        "//]]>\n" .
        "</script>\n";

    return $r;
}

/**
 * Prepares an LTI 1.3 login request
 *
 * @param int            $courseid  Course ID
 * @param int            $id        LTI instance ID
 * @param stdClass|null  $instance  LTI instance
 * @param stdClass       $config    Tool type configuration
 * @param string         $messagetype   LTI message type
 * @return array Login request parameters
 */
function lti_build_login_request($courseid, $id, $instance, $config, $messagetype) {
    global $USER, $CFG;

    if (!empty($instance)) {
        $endpoint = !empty($instance->toolurl) ? $instance->toolurl : $config->lti_toolurl;
    } else {
        $endpoint = $config->lti_toolurl;
        if (($messagetype === 'ContentItemSelectionRequest') && !empty($config->lti_toolurl_ContentItemSelectionRequest)) {
            $endpoint = $config->lti_toolurl_ContentItemSelectionRequest;
        }
    }
    $endpoint = trim($endpoint);

    // If SSL is forced make sure https is on the normal launch URL.
    if (isset($config->lti_forcessl) && ($config->lti_forcessl == '1')) {
        $endpoint = lti_ensure_url_is_https($endpoint);
    } else if (!strstr($endpoint, '://')) {
        $endpoint = 'http://' . $endpoint;
    }

    $params = array();
    $params['iss'] = $CFG->wwwroot;
    $params['target_link_uri'] = $endpoint;
    $params['login_hint'] = $USER->id;
    $params['lti_message_hint'] = $id;
    $params['client_id'] = $config->lti_clientid;
    $params['lti_deployment_id'] = $config->typeid;
    return $params;
}

function lti_get_type($typeid) {
    global $DB;

    return $DB->get_record('lti_types', array('id' => $typeid));
}

function lti_get_launch_container($lti, $toolconfig) {
    if (empty($lti->launchcontainer)) {
        $lti->launchcontainer = LTI_LAUNCH_CONTAINER_DEFAULT;
    }

    if ($lti->launchcontainer == LTI_LAUNCH_CONTAINER_DEFAULT) {
        if (isset($toolconfig['launchcontainer'])) {
            $launchcontainer = $toolconfig['launchcontainer'];
        }
    } else {
        $launchcontainer = $lti->launchcontainer;
    }

    if (empty($launchcontainer) || $launchcontainer == LTI_LAUNCH_CONTAINER_DEFAULT) {
        $launchcontainer = LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS;
    }

    $devicetype = core_useragent::get_device_type();

    // Scrolling within the object element doesn't work on iOS or Android
    // Opening the popup window also had some issues in testing
    // For mobile devices, always take up the entire screen to ensure the best experience.
    if ($devicetype === core_useragent::DEVICETYPE_MOBILE || $devicetype === core_useragent::DEVICETYPE_TABLET ) {
        $launchcontainer = LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW;
    }

    return $launchcontainer;
}

function lti_request_is_using_ssl() {
    global $CFG;
    return (stripos($CFG->wwwroot, 'https://') === 0);
}

function lti_ensure_url_is_https($url) {
    if (!strstr($url, '://')) {
        $url = 'https://' . $url;
    } else {
        // If the URL starts with http, replace with https.
        if (stripos($url, 'http://') === 0) {
            $url = 'https://' . substr($url, 7);
        }
    }

    return $url;
}

/**
 * Determines if we should try to log the request
 *
 * @param string $rawbody
 * @return bool
 */
function lti_should_log_request($rawbody) {
    global $CFG;

    if (empty($CFG->mod_lti_log_users)) {
        return false;
    }

    $logusers = explode(',', $CFG->mod_lti_log_users);
    if (empty($logusers)) {
        return false;
    }

    try {
        $xml = new \SimpleXMLElement($rawbody);
        $ns  = $xml->getNamespaces();
        $ns  = array_shift($ns);
        $xml->registerXPathNamespace('lti', $ns);
        $requestuserid = '';
        if ($node = $xml->xpath('//lti:userId')) {
            $node = $node[0];
            $requestuserid = clean_param((string) $node, PARAM_INT);
        } else if ($node = $xml->xpath('//lti:sourcedId')) {
            $node = $node[0];
            $resultjson = json_decode((string) $node);
            $requestuserid = clean_param($resultjson->data->userid, PARAM_INT);
        }
    } catch (Exception $e) {
        return false;
    }

    if (empty($requestuserid) or !in_array($requestuserid, $logusers)) {
        return false;
    }

    return true;
}

/**
 * Logs the request to a file in temp dir.
 *
 * @param string $rawbody
 */
function lti_log_request($rawbody) {
    if ($tempdir = make_temp_directory('mod_lti', false)) {
        if ($tempfile = tempnam($tempdir, 'mod_lti_request'.date('YmdHis'))) {
            $content  = "Request Headers:\n";
            foreach (moodle\mod\lti\OAuthUtil::get_headers() as $header => $value) {
                $content .= "$header: $value\n";
            }
            $content .= "Request Body:\n";
            $content .= $rawbody;

            file_put_contents($tempfile, $content);
            chmod($tempfile, 0644);
        }
    }
}

/**
 * Log an LTI response.
 *
 * @param string $responsexml The response XML
 * @param Exception $e If there was an exception, pass that too
 */
function lti_log_response($responsexml, $e = null) {
    if ($tempdir = make_temp_directory('mod_lti', false)) {
        if ($tempfile = tempnam($tempdir, 'mod_lti_response'.date('YmdHis'))) {
            $content = '';
            if ($e instanceof Exception) {
                $info = get_exception_info($e);

                $content .= "Exception:\n";
                $content .= "Message: $info->message\n";
                $content .= "Debug info: $info->debuginfo\n";
                $content .= "Backtrace:\n";
                $content .= format_backtrace($info->backtrace, true);
                $content .= "\n";
            }
            $content .= "Response XML:\n";
            $content .= $responsexml;

            file_put_contents($tempfile, $content);
            chmod($tempfile, 0644);
        }
    }
}

/**
 * Fetches LTI type configuration for an LTI instance
 *
 * @param stdClass $instance
 * @return array Can be empty if no type is found
 */
function lti_get_type_config_by_instance($instance) {
    $typeid = null;
    if (empty($instance->typeid)) {
        $tool = lti_get_tool_by_url_match($instance->toolurl, $instance->course);
        if ($tool) {
            $typeid = $tool->id;
        }
    } else {
        $typeid = $instance->typeid;
    }
    if (!empty($typeid)) {
        return lti_get_type_config($typeid);
    }
    return array();
}

/**
 * Enforce type config settings onto the LTI instance
 *
 * @param stdClass $instance
 * @param array $typeconfig
 */
function lti_force_type_config_settings($instance, array $typeconfig) {
    $forced = array(
        'instructorchoicesendname'      => 'sendname',
        'instructorchoicesendemailaddr' => 'sendemailaddr',
        'instructorchoiceacceptgrades'  => 'acceptgrades',
    );

    foreach ($forced as $instanceparam => $typeconfigparam) {
        if (array_key_exists($typeconfigparam, $typeconfig) && $typeconfig[$typeconfigparam] != LTI_SETTING_DELEGATE) {
            $instance->$instanceparam = $typeconfig[$typeconfigparam];
        }
    }
}

/**
 * Initializes an array with the capabilities supported by the LTI module
 *
 * @return array List of capability names (without a dollar sign prefix)
 */
function lti_get_capabilities() {

    $capabilities = array(
       'basic-lti-launch-request' => '',
       'ContentItemSelectionRequest' => '',
       'ToolProxyRegistrationRequest' => '',
       'Context.id' => 'context_id',
       'Context.title' => 'context_title',
       'Context.label' => 'context_label',
       'Context.id.history' => null,
       'Context.sourcedId' => 'lis_course_section_sourcedid',
       'Context.longDescription' => '$COURSE->summary',
       'Context.timeFrame.begin' => '$COURSE->startdate',
       'CourseSection.title' => 'context_title',
       'CourseSection.label' => 'context_label',
       'CourseSection.sourcedId' => 'lis_course_section_sourcedid',
       'CourseSection.longDescription' => '$COURSE->summary',
       'CourseSection.timeFrame.begin' => '$COURSE->startdate',
       'ResourceLink.id' => 'resource_link_id',
       'ResourceLink.title' => 'resource_link_title',
       'ResourceLink.description' => 'resource_link_description',
       'User.id' => 'user_id',
       'User.username' => '$USER->username',
       'Person.name.full' => 'lis_person_name_full',
       'Person.name.given' => 'lis_person_name_given',
       'Person.name.family' => 'lis_person_name_family',
       'Person.email.primary' => 'lis_person_contact_email_primary',
       'Person.sourcedId' => 'lis_person_sourcedid',
       'Person.name.middle' => '$USER->middlename',
       'Person.address.street1' => '$USER->address',
       'Person.address.locality' => '$USER->city',
       'Person.address.country' => '$USER->country',
       'Person.address.timezone' => '$USER->timezone',
       'Person.phone.primary' => '$USER->phone1',
       'Person.phone.mobile' => '$USER->phone2',
       'Person.webaddress' => '$USER->url',
       'Membership.role' => 'roles',
       'Result.sourcedId' => 'lis_result_sourcedid',
       'Result.autocreate' => 'lis_outcome_service_url',
       'BasicOutcome.sourcedId' => 'lis_result_sourcedid',
       'BasicOutcome.url' => 'lis_outcome_service_url',
       'Moodle.Person.userGroupIds' => null);

    return $capabilities;

}

/**
 * Initializes an array with the services supported by the LTI module
 *
 * @return array List of services
 */
function lti_get_services() {

    $services = array();
    $definedservices = core_component::get_plugin_list('ltiservice');
    foreach ($definedservices as $name => $location) {
        $classname = "\\ltiservice_{$name}\\local\\service\\{$name}";
        $services[] = new $classname();
    }

    return $services;

}

/**
 * Initializes an instance of the named service
 *
 * @param string $servicename Name of service
 *
 * @return bool|\mod_lti\local\ltiservice\service_base Service
 */
function lti_get_service_by_name($servicename) {

    $service = false;
    $classname = "\\ltiservice_{$servicename}\\local\\service\\{$servicename}";
    if (class_exists($classname)) {
        $service = new $classname();
    }

    return $service;

}

/**
 * Finds a service by id
 *
 * @param \mod_lti\local\ltiservice\service_base[] $services Array of services
 * @param string $resourceid  ID of resource
 *
 * @return mod_lti\local\ltiservice\service_base Service
 */
function lti_get_service_by_resource_id($services, $resourceid) {

    $service = false;
    foreach ($services as $aservice) {
        foreach ($aservice->get_resources() as $resource) {
            if ($resource->get_id() === $resourceid) {
                $service = $aservice;
                break 2;
            }
        }
    }

    return $service;

}

/**
 * Initializes an array with the scopes for services supported by the LTI module
 *
 * @param object $type  LTI tool type
 * @param array  $typeconfig  LTI tool type configuration
 *
 * @return array List of scopes
 */
function lti_get_permitted_service_scopes($type, $typeconfig) {

    $services = lti_get_services();
    $scopes = array();
    foreach ($services as $service) {
        $service->set_type($type);
        $service->set_typeconfig($typeconfig);
        $servicescopes = $service->get_permitted_scopes();
        if (!empty($servicescopes)) {
            $scopes = array_merge($scopes, $servicescopes);
        }
    }

    return $scopes;

}

/**
 * Extracts the named contexts from a tool proxy
 *
 * @param object $json
 *
 * @return array Contexts
 */
function lti_get_contexts($json) {

    $contexts = array();
    if (isset($json->{'@context'})) {
        foreach ($json->{'@context'} as $context) {
            if (is_object($context)) {
                $contexts = array_merge(get_object_vars($context), $contexts);
            }
        }
    }

    return $contexts;

}

/**
 * Converts an ID to a fully-qualified ID
 *
 * @param array $contexts
 * @param string $id
 *
 * @return string Fully-qualified ID
 */
function lti_get_fqid($contexts, $id) {

    $parts = explode(':', $id, 2);
    if (count($parts) > 1) {
        if (array_key_exists($parts[0], $contexts)) {
            $id = $contexts[$parts[0]] . $parts[1];
        }
    }

    return $id;

}

/**
 * Returns the icon for the given tool type
 *
 * @param stdClass $type The tool type
 *
 * @return string The url to the tool type's corresponding icon
 */
function get_tool_type_icon_url(stdClass $type) {
    global $OUTPUT;

    $iconurl = $type->secureicon;

    if (empty($iconurl)) {
        $iconurl = $type->icon;
    }

    if (empty($iconurl)) {
        $iconurl = $OUTPUT->image_url('icon', 'lti')->out();
    }

    return $iconurl;
}

/**
 * Returns the edit url for the given tool type
 *
 * @param stdClass $type The tool type
 *
 * @return string The url to edit the tool type
 */
function get_tool_type_edit_url(stdClass $type) {
    $url = new moodle_url('/mod/lti/typessettings.php',
                          array('action' => 'update', 'id' => $type->id, 'sesskey' => sesskey(), 'returnto' => 'toolconfigure'));
    return $url->out();
}

/**
 * Returns the edit url for the given tool proxy.
 *
 * @param stdClass $proxy The tool proxy
 *
 * @return string The url to edit the tool type
 */
function get_tool_proxy_edit_url(stdClass $proxy) {
    $url = new moodle_url('/mod/lti/registersettings.php',
                          array('action' => 'update', 'id' => $proxy->id, 'sesskey' => sesskey(), 'returnto' => 'toolconfigure'));
    return $url->out();
}

/**
 * Returns the course url for the given tool type
 *
 * @param stdClass $type The tool type
 *
 * @return string The url to the course of the tool type, void if it is a site wide type
 */
function get_tool_type_course_url(stdClass $type) {
    if ($type->course != 1) {
        $url = new moodle_url('/course/view.php', array('id' => $type->course));
        return $url->out();
    }
    return null;
}

/**
 * Returns the icon and edit urls for the tool type and the course url if it is a course type.
 *
 * @param stdClass $type The tool type
 *
 * @return array The urls of the tool type
 */
function get_tool_type_urls(stdClass $type) {
    $courseurl = get_tool_type_course_url($type);

    $urls = array(
        'icon' => get_tool_type_icon_url($type),
        'edit' => get_tool_type_edit_url($type),
    );

    if ($courseurl) {
        $urls['course'] = $courseurl;
    }

    $url = new moodle_url('/mod/lti/certs.php');
    $urls['publickeyset'] = $url->out();
    $url = new moodle_url('/mod/lti/token.php');
    $urls['accesstoken'] = $url->out();
    $url = new moodle_url('/mod/lti/auth.php');
    $urls['authrequest'] = $url->out();

    return $urls;
}

/**
 * Returns the icon and edit urls for the tool proxy.
 *
 * @param stdClass $proxy The tool proxy
 *
 * @return array The urls of the tool proxy
 */
function get_tool_proxy_urls(stdClass $proxy) {
    global $OUTPUT;

    $urls = array(
        'icon' => $OUTPUT->image_url('icon', 'lti')->out(),
        'edit' => get_tool_proxy_edit_url($proxy),
    );

    return $urls;
}

/**
 * Returns information on the current state of the tool type
 *
 * @param stdClass $type The tool type
 *
 * @return array An array with a text description of the state, and boolean for whether it is in each state:
 * pending, configured, rejected, unknown
 */
function get_tool_type_state_info(stdClass $type) {
    $isconfigured = false;
    $ispending = false;
    $isrejected = false;
    $isunknown = false;
    switch ($type->state) {
        case LTI_TOOL_STATE_CONFIGURED:
            $state = get_string('active', 'mod_lti');
            $isconfigured = true;
            break;
        case LTI_TOOL_STATE_PENDING:
            $state = get_string('pending', 'mod_lti');
            $ispending = true;
            break;
        case LTI_TOOL_STATE_REJECTED:
            $state = get_string('rejected', 'mod_lti');
            $isrejected = true;
            break;
        default:
            $state = get_string('unknownstate', 'mod_lti');
            $isunknown = true;
            break;
    }

    return array(
        'text' => $state,
        'pending' => $ispending,
        'configured' => $isconfigured,
        'rejected' => $isrejected,
        'unknown' => $isunknown
    );
}

/**
 * Returns information on the configuration of the tool type
 *
 * @param stdClass $type The tool type
 *
 * @return array An array with configuration details
 */
function get_tool_type_config($type) {
    global $CFG;
    $platformid = $CFG->wwwroot;
    $clientid = $type->clientid;
    $deploymentid = $type->id;
    $publickeyseturl = new moodle_url('/mod/lti/certs.php');
    $publickeyseturl = $publickeyseturl->out();

    $accesstokenurl = new moodle_url('/mod/lti/token.php');
    $accesstokenurl = $accesstokenurl->out();

    $authrequesturl = new moodle_url('/mod/lti/auth.php');
    $authrequesturl = $authrequesturl->out();

    return array(
        'platformid' => $platformid,
        'clientid' => $clientid,
        'deploymentid' => $deploymentid,
        'publickeyseturl' => $publickeyseturl,
        'accesstokenurl' => $accesstokenurl,
        'authrequesturl' => $authrequesturl
    );
}

/**
 * Returns a summary of each LTI capability this tool type requires in plain language
 *
 * @param stdClass $type The tool type
 *
 * @return array An array of text descriptions of each of the capabilities this tool type requires
 */
function get_tool_type_capability_groups($type) {
    $capabilities = lti_get_enabled_capabilities($type);
    $groups = array();
    $hascourse = false;
    $hasactivities = false;
    $hasuseraccount = false;
    $hasuserpersonal = false;

    foreach ($capabilities as $capability) {
        // Bail out early if we've already found all groups.
        if (count($groups) >= 4) {
            continue;
        }

        if (!$hascourse && preg_match('/^CourseSection/', $capability)) {
            $hascourse = true;
            $groups[] = get_string('courseinformation', 'mod_lti');
        } else if (!$hasactivities && preg_match('/^ResourceLink/', $capability)) {
            $hasactivities = true;
            $groups[] = get_string('courseactivitiesorresources', 'mod_lti');
        } else if (!$hasuseraccount && preg_match('/^User/', $capability) || preg_match('/^Membership/', $capability)) {
            $hasuseraccount = true;
            $groups[] = get_string('useraccountinformation', 'mod_lti');
        } else if (!$hasuserpersonal && preg_match('/^Person/', $capability)) {
            $hasuserpersonal = true;
            $groups[] = get_string('userpersonalinformation', 'mod_lti');
        }
    }

    return $groups;
}


/**
 * Returns the ids of each instance of this tool type
 *
 * @param stdClass $type The tool type
 *
 * @return array An array of ids of the instances of this tool type
 */
function get_tool_type_instance_ids($type) {
    global $DB;

    return array_keys($DB->get_fieldset_select('lti', 'id', 'typeid = ?', array($type->id)));
}

/**
 * Serialises this tool type
 *
 * @param stdClass $type The tool type
 *
 * @return array An array of values representing this type
 */
function serialise_tool_type(stdClass $type) {
    global $CFG;

    $capabilitygroups = get_tool_type_capability_groups($type);
    $instanceids = get_tool_type_instance_ids($type);
    // Clean the name. We don't want tags here.
    $name = clean_param($type->name, PARAM_NOTAGS);
    if (!empty($type->description)) {
        // Clean the description. We don't want tags here.
        $description = clean_param($type->description, PARAM_NOTAGS);
    } else {
        $description = get_string('editdescription', 'mod_lti');
    }
    return array(
        'id' => $type->id,
        'name' => $name,
        'description' => $description,
        'urls' => get_tool_type_urls($type),
        'state' => get_tool_type_state_info($type),
        'platformid' => $CFG->wwwroot,
        'clientid' => $type->clientid,
        'deploymentid' => $type->id,
        'hascapabilitygroups' => !empty($capabilitygroups),
        'capabilitygroups' => $capabilitygroups,
        // Course ID of 1 means it's not linked to a course.
        'courseid' => $type->course == 1 ? 0 : $type->course,
        'instanceids' => $instanceids,
        'instancecount' => count($instanceids)
    );
}

/**
 * Serialises this tool proxy.
 *
 * @param stdClass $proxy The tool proxy
 *
 * @return array An array of values representing this type
 */
function serialise_tool_proxy(stdClass $proxy) {
    return array(
        'id' => $proxy->id,
        'name' => $proxy->name,
        'description' => get_string('activatetoadddescription', 'mod_lti'),
        'urls' => get_tool_proxy_urls($proxy),
        'state' => array(
            'text' => get_string('pending', 'mod_lti'),
            'pending' => true,
            'configured' => false,
            'rejected' => false,
            'unknown' => false
        ),
        'hascapabilitygroups' => true,
        'capabilitygroups' => array(),
        'courseid' => 0,
        'instanceids' => array(),
        'instancecount' => 0
    );
}

/**
 * Loads the cartridge information into the tool type, if the launch url is for a cartridge file
 *
 * @param stdClass $type The tool type object to be filled in
 * @since Moodle 3.1
 */
function lti_load_type_if_cartridge($type) {
    if (!empty($type->lti_toolurl) && lti_is_cartridge($type->lti_toolurl)) {
        lti_load_type_from_cartridge($type->lti_toolurl, $type);
    }
}

/**
 * Loads the cartridge information into the new tool, if the launch url is for a cartridge file
 *
 * @param stdClass $lti The tools config
 * @since Moodle 3.1
 */
function lti_load_tool_if_cartridge($lti) {
    if (!empty($lti->toolurl) && lti_is_cartridge($lti->toolurl)) {
        lti_load_tool_from_cartridge($lti->toolurl, $lti);
    }
}

/**
 * Determines if the given url is for a IMS basic cartridge
 *
 * @param  string $url The url to be checked
 * @return True if the url is for a cartridge
 * @since Moodle 3.1
 */
function lti_is_cartridge($url) {
    // If it is empty, it's not a cartridge.
    if (empty($url)) {
        return false;
    }
    // If it has xml at the end of the url, it's a cartridge.
    if (preg_match('/\.xml$/', $url)) {
        return true;
    }
    // Even if it doesn't have .xml, load the url to check if it's a cartridge..
    try {
        $toolinfo = lti_load_cartridge($url,
            array(
                "launch_url" => "launchurl"
            )
        );
        if (!empty($toolinfo['launchurl'])) {
            return true;
        }
    } catch (moodle_exception $e) {
        return false; // Error loading the xml, so it's not a cartridge.
    }
    return false;
}

/**
 * Allows you to load settings for an external tool type from an IMS cartridge.
 *
 * @param  string   $url     The URL to the cartridge
 * @param  stdClass $type    The tool type object to be filled in
 * @throws moodle_exception if the cartridge could not be loaded correctly
 * @since Moodle 3.1
 */
function lti_load_type_from_cartridge($url, $type) {
    $toolinfo = lti_load_cartridge($url,
        array(
            "title" => "lti_typename",
            "launch_url" => "lti_toolurl",
            "description" => "lti_description",
            "icon" => "lti_icon",
            "secure_icon" => "lti_secureicon"
        ),
        array(
            "icon_url" => "lti_extension_icon",
            "secure_icon_url" => "lti_extension_secureicon"
        )
    );
    // If an activity name exists, unset the cartridge name so we don't override it.
    if (isset($type->lti_typename)) {
        unset($toolinfo['lti_typename']);
    }

    // Always prefer cartridge core icons first, then, if none are found, look at the extension icons.
    if (empty($toolinfo['lti_icon']) && !empty($toolinfo['lti_extension_icon'])) {
        $toolinfo['lti_icon'] = $toolinfo['lti_extension_icon'];
    }
    unset($toolinfo['lti_extension_icon']);

    if (empty($toolinfo['lti_secureicon']) && !empty($toolinfo['lti_extension_secureicon'])) {
        $toolinfo['lti_secureicon'] = $toolinfo['lti_extension_secureicon'];
    }
    unset($toolinfo['lti_extension_secureicon']);

    // Ensure Custom icons aren't overridden by cartridge params.
    if (!empty($type->lti_icon)) {
        unset($toolinfo['lti_icon']);
    }

    if (!empty($type->lti_secureicon)) {
        unset($toolinfo['lti_secureicon']);
    }

    foreach ($toolinfo as $property => $value) {
        $type->$property = $value;
    }
}

/**
 * Allows you to load in the configuration for an external tool from an IMS cartridge.
 *
 * @param  string   $url    The URL to the cartridge
 * @param  stdClass $lti    LTI object
 * @throws moodle_exception if the cartridge could not be loaded correctly
 * @since Moodle 3.1
 */
function lti_load_tool_from_cartridge($url, $lti) {
    $toolinfo = lti_load_cartridge($url,
        array(
            "title" => "name",
            "launch_url" => "toolurl",
            "secure_launch_url" => "securetoolurl",
            "description" => "intro",
            "icon" => "icon",
            "secure_icon" => "secureicon"
        ),
        array(
            "icon_url" => "extension_icon",
            "secure_icon_url" => "extension_secureicon"
        )
    );
    // If an activity name exists, unset the cartridge name so we don't override it.
    if (isset($lti->name)) {
        unset($toolinfo['name']);
    }

    // Always prefer cartridge core icons first, then, if none are found, look at the extension icons.
    if (empty($toolinfo['icon']) && !empty($toolinfo['extension_icon'])) {
        $toolinfo['icon'] = $toolinfo['extension_icon'];
    }
    unset($toolinfo['extension_icon']);

    if (empty($toolinfo['secureicon']) && !empty($toolinfo['extension_secureicon'])) {
        $toolinfo['secureicon'] = $toolinfo['extension_secureicon'];
    }
    unset($toolinfo['extension_secureicon']);

    foreach ($toolinfo as $property => $value) {
        $lti->$property = $value;
    }
}

/**
 * Search for a tag within an XML DOMDocument
 *
 * @param  string $url The url of the cartridge to be loaded
 * @param  array  $map The map of tags to keys in the return array
 * @param  array  $propertiesmap The map of properties to keys in the return array
 * @return array An associative array with the given keys and their values from the cartridge
 * @throws moodle_exception if the cartridge could not be loaded correctly
 * @since Moodle 3.1
 */
function lti_load_cartridge($url, $map, $propertiesmap = array()) {
    global $CFG;
    require_once($CFG->libdir. "/filelib.php");

    $curl = new curl();
    $response = $curl->get($url);

    // TODO MDL-46023 Replace this code with a call to the new library.
    $origerrors = libxml_use_internal_errors(true);
    $origentity = libxml_disable_entity_loader(true);
    libxml_clear_errors();

    $document = new DOMDocument();
    @$document->loadXML($response, LIBXML_DTDLOAD | LIBXML_DTDATTR);

    $cartridge = new DomXpath($document);

    $errors = libxml_get_errors();

    libxml_clear_errors();
    libxml_use_internal_errors($origerrors);
    libxml_disable_entity_loader($origentity);

    if (count($errors) > 0) {
        $message = 'Failed to load cartridge.';
        foreach ($errors as $error) {
            $message .= "\n" . trim($error->message, "\n\r\t .") . " at line " . $error->line;
        }
        throw new moodle_exception('errorreadingfile', '', '', $url, $message);
    }

    $toolinfo = array();
    foreach ($map as $tag => $key) {
        $value = get_tag($tag, $cartridge);
        if ($value) {
            $toolinfo[$key] = $value;
        }
    }
    if (!empty($propertiesmap)) {
        foreach ($propertiesmap as $property => $key) {
            $value = get_tag("property", $cartridge, $property);
            if ($value) {
                $toolinfo[$key] = $value;
            }
        }
    }

    return $toolinfo;
}

/**
 * Search for a tag within an XML DOMDocument
 *
 * @param  stdClass $tagname The name of the tag to search for
 * @param  XPath    $xpath   The XML to find the tag in
 * @param  XPath    $attribute The attribute to search for (if we should search for a child node with the given
 * value for the name attribute
 * @since Moodle 3.1
 */
function get_tag($tagname, $xpath, $attribute = null) {
    if ($attribute) {
        $result = $xpath->query('//*[local-name() = \'' . $tagname . '\'][@name="' . $attribute . '"]');
    } else {
        $result = $xpath->query('//*[local-name() = \'' . $tagname . '\']');
    }
    if ($result->length > 0) {
        return $result->item(0)->nodeValue;
    }
    return null;
}

/**
 * Create a new access token.
 *
 * @param int $typeid Tool type ID
 * @param string[] $scopes Scopes permitted for new token
 *
 * @return stdClass Access token
 */
function lti_new_access_token($typeid, $scopes) {
    global $DB;

    // Make sure the token doesn't exist (even if it should be almost impossible with the random generation).
    $numtries = 0;
    do {
        $numtries ++;
        $generatedtoken = md5(uniqid(rand(), 1));
        if ($numtries > 5) {
            throw new moodle_exception('Failed to generate LTI access token');
        }
    } while ($DB->record_exists('lti_access_tokens', array('token' => $generatedtoken)));
    $newtoken = new stdClass();
    $newtoken->typeid = $typeid;
    $newtoken->scope = json_encode(array_values($scopes));
    $newtoken->token = $generatedtoken;

    $newtoken->timecreated = time();
    $newtoken->validuntil = $newtoken->timecreated + LTI_ACCESS_TOKEN_LIFE;
    $newtoken->lastaccess = null;

    $DB->insert_record('lti_access_tokens', $newtoken);

    return $newtoken;

}
