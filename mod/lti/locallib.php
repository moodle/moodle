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

require_once($CFG->dirroot.'/mod/lti/OAuth.php');

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

/**
 * Prints a Basic LTI activity
 *
 * $param int $basicltiid       Basic LTI activity id
 */
function lti_view($instance) {
    global $PAGE, $CFG;

    if (empty($instance->typeid)) {
        $tool = lti_get_tool_by_url_match($instance->toolurl, $instance->course);
        if ($tool) {
            $typeid = $tool->id;
        } else {
            $typeid = null;
        }
    } else {
        $typeid = $instance->typeid;
        $tool = lti_get_type($typeid);
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

    // Default the organizationid if not specified.
    if (empty($typeconfig['organizationid'])) {
        $urlparts = parse_url($CFG->wwwroot);

        $typeconfig['organizationid'] = $urlparts['host'];
    }

    if (isset($tool->toolproxyid)) {
        $toolproxy = lti_get_tool_proxy($tool->toolproxyid);
        $key = $toolproxy->guid;
        $secret = $toolproxy->secret;
    } else {
        $toolproxy = null;
        if (!empty($instance->resourcekey)) {
            $key = $instance->resourcekey;
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

    $orgid = $typeconfig['organizationid'];

    $course = $PAGE->course;
    $islti2 = isset($tool->toolproxyid);
    $allparams = lti_build_request($instance, $typeconfig, $course, $typeid, $islti2);
    if ($islti2) {
        $requestparams = lti_build_request_lti2($tool, $allparams);
    } else {
        $requestparams = $allparams;
    }
    $requestparams = array_merge($requestparams, lti_build_standard_request($instance, $orgid, $islti2));
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

    // Allow request params to be updated by sub-plugins.
    $plugins = core_component::get_plugin_list('ltisource');
    foreach (array_keys($plugins) as $plugin) {
        $pluginparams = component_callback('ltisource_'.$plugin, 'before_launch',
            array($instance, $endpoint, $requestparams), array());

        if (!empty($pluginparams) && is_array($pluginparams)) {
            $requestparams = array_merge($requestparams, $pluginparams);
        }
    }

    if (!empty($key) && !empty($secret)) {
        $parms = lti_sign_parameters($requestparams, $endpoint, "POST", $key, $secret);

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

    $debuglaunch = ( $instance->debuglaunch == 1 );

    $content = lti_post_launch_html($parms, $endpoint, $debuglaunch);

    echo $content;
}

/**
 * Prepares an LTI registration request message
 *
 * $param object $instance       Tool Proxy instance object
 */
function lti_register($toolproxy) {
    global $PAGE, $CFG;

    $key = $toolproxy->guid;
    $secret = $toolproxy->secret;
    $endpoint = $toolproxy->regurl;

    $requestparams = array();
    $requestparams['lti_message_type'] = 'ToolProxyRegistrationRequest';
    $requestparams['lti_version'] = 'LTI-2p0';
    $requestparams['reg_key'] = $key;
    $requestparams['reg_password'] = $secret;

    // Change the status to pending.
    $toolproxy->state = LTI_TOOL_PROXY_STATE_PENDING;
    lti_update_tool_proxy($toolproxy);

    // Add the profile URL.
    $profileservice = lti_get_service_by_name('profile');
    $profileservice->set_tool_proxy($toolproxy);
    $requestparams['tc_profile_url'] = $profileservice->parse_value('$ToolConsumerProfile.url');

    // Add the return URL.
    $returnurlparams = array('id' => $toolproxy->id, 'sesskey' => sesskey());
    $url = new \moodle_url('/mod/lti/registrationreturn.php', $returnurlparams);
    $returnurl = $url->out(false);

    $requestparams['launch_presentation_return_url'] = $returnurl;
    $content = lti_post_launch_html($requestparams, $endpoint, false);

    echo $content;
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

    $intro = '';
    if (!empty($instance->cmid)) {
        $intro = format_module_intro('lti', $instance, $instance->cmid);
        $intro = html_to_text($intro, 0, false);

        // This may look weird, but this is required for new lines
        // so we generate the same OAuth signature as the tool provider.
        $intro = str_replace("\n", "\r\n", $intro);
    }
    $requestparams = array(
        'resource_link_title' => $instance->name,
        'resource_link_description' => $intro,
        'user_id' => $USER->id,
        'lis_person_sourcedid' => $USER->idnumber,
        'roles' => $role,
        'context_id' => $course->id,
        'context_label' => $course->shortname,
        'context_title' => $course->fullname,
    );
    if ($course->format == 'site') {
        $requestparams['context_type'] = 'Group';
    } else {
        $requestparams['context_type'] = 'CourseSection';
        $requestparams['lis_course_section_sourcedid'] = $course->idnumber;
    }
    $placementsecret = $instance->servicesalt;

    if ( isset($placementsecret) && ($islti2 ||
         $typeconfig['acceptgrades'] == LTI_SETTING_ALWAYS ||
         ($typeconfig['acceptgrades'] == LTI_SETTING_DELEGATE && $instance->instructorchoiceacceptgrades == LTI_SETTING_ALWAYS))) {

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
         ( $typeconfig['sendname'] == LTI_SETTING_DELEGATE && $instance->instructorchoicesendname == LTI_SETTING_ALWAYS ) ) {
        $requestparams['lis_person_name_given'] = $USER->firstname;
        $requestparams['lis_person_name_family'] = $USER->lastname;
        $requestparams['lis_person_name_full'] = $USER->firstname . ' ' . $USER->lastname;
        $requestparams['ext_user_username'] = $USER->username;
    }

    if ($islti2 || $typeconfig['sendemailaddr'] == LTI_SETTING_ALWAYS ||
         ($typeconfig['sendemailaddr'] == LTI_SETTING_DELEGATE && $instance->instructorchoicesendemailaddr == LTI_SETTING_ALWAYS)) {
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
 * @param object    $instance       Basic LTI instance object
 * @param string    $orgid          Organisation ID
 * @param boolean   $islti2         True if an LTI 2 tool is being launched
 *
 * @return array                    Request details
 */
function lti_build_standard_request($instance, $orgid, $islti2) {
    global $CFG;

    $requestparams = array();

    $requestparams['resource_link_id'] = $instance->id;
    if (property_exists($instance, 'resource_link_id') and !empty($instance->resource_link_id)) {
        $requestparams['resource_link_id'] = $instance->resource_link_id;
    }

    $requestparams['launch_presentation_locale'] = current_language();

    // Make sure we let the tool know what LMS they are being called from.
    $requestparams['ext_lms'] = 'moodle-2';
    $requestparams['tool_consumer_info_product_family_code'] = 'moodle';
    $requestparams['tool_consumer_info_version'] = strval($CFG->version);

    // Add oauth_callback to be compliant with the 1.0A spec.
    $requestparams['oauth_callback'] = 'about:blank';

    if (!$islti2) {
        $requestparams['lti_version'] = 'LTI-1p0';
    } else {
        $requestparams['lti_version'] = 'LTI-2p0';
    }
    $requestparams['lti_message_type'] = 'basic-lti-launch-request';

    if ($orgid) {
        $requestparams["tool_consumer_instance_guid"] = $orgid;
    }
    if (!empty($CFG->mod_lti_institution_name)) {
        $requestparams['tool_consumer_instance_name'] = $CFG->mod_lti_institution_name;
    } else {
        $requestparams['tool_consumer_instance_name'] = get_site()->fullname;
    }

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
    if (!isset($typeconfig['allowinstructorcustom']) || $typeconfig['allowinstructorcustom'] != LTI_SETTING_NEVER) {
        if ($instructorcustomstr) {
            $custom = array_merge(lti_split_custom_parameters($toolproxy, $tool, $params,
                $instructorcustomstr, $islti2), $custom);
        }
    }
    if ($islti2) {
        $custom = array_merge(lti_split_custom_parameters($toolproxy, $tool, $params,
            $tool->parameter, true), $custom);
        $settings = lti_get_tool_settings($tool->toolproxyid);
        $custom = array_merge($custom, lti_get_custom_parameters($toolproxy, $tool, $params, $settings));
        $settings = lti_get_tool_settings($tool->toolproxyid, $instance->course);
        $custom = array_merge($custom, lti_get_custom_parameters($toolproxy, $tool, $params, $settings));
        $settings = lti_get_tool_settings($tool->toolproxyid, $instance->course, $instance->id);
        $custom = array_merge($custom, lti_get_custom_parameters($toolproxy, $tool, $params, $settings));
    }

    return $custom;
}

function lti_get_tool_table($tools, $id) {
    global $CFG, $OUTPUT, $USER;
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
 * @param object    $tool           Tool instance object
 *
 * @return Array of enabled capabilities
 */
function lti_get_enabled_capabilities($tool) {
    if (!empty($tool->enabledcapability)) {
        $enabledcapabilities = explode("\n", $tool->enabledcapability);
    } else {
        $enabledcapabilities = array();
    }
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
 * @return Array of custom parameters
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
        if ($islti2 && ($key != $key2)) {
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
 * @return Parsed value of custom parameter
 */
function lti_parse_custom_parameter($toolproxy, $tool, $params, $value, $islti2) {
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
                            $value = "{${$valarr[0]}->$valarr[1]}";
                            $value = str_replace('<br />' , ' ', $value);
                            $value = str_replace('<br>' , ' ', $value);
                            $value = format_string($value);
                        }
                    }
                } else if ($islti2) {
                    $val = $value;
                    $services = lti_get_services();
                    foreach ($services as $service) {
                        $service->set_tool_proxy($toolproxy);
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
 * Used for building the names of the different custom parameters
 *
 * @param string $key   Parameter name
 *
 * @return string       Processed name
 */
function lti_map_keyname($key) {
    $newkey = "";
    $key = core_text::strtolower(trim($key));
    foreach (str_split($key) as $ch) {
        if ( ($ch >= 'a' && $ch <= 'z') || ($ch >= '0' && $ch <= '9') ) {
            $newkey .= $ch;
        } else {
            $newkey .= '_';
        }
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
        $coursecontext = context_course::instance($courseid);

        if (has_capability('moodle/course:manageactivities', $coursecontext)) {
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

    if (is_siteadmin($user)) {
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
              SELECT 'toolurl' AS name, " . $DB->sql_compare_text('baseurl', 1333) . " AS value
                FROM {lti_types}
               WHERE id = :typeid2";

    $typeconfig = array();
    $configs = $DB->get_records_sql($query, array('typeid1' => $typeid, 'typeid2' => $typeid));

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

    $filters = array('tooldomain' => $domain);

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

function lti_get_types_for_add_instance() {
    global $DB, $SITE, $COURSE;

    $query = "SELECT *
                FROM {lti_types}
               WHERE coursevisible = 1
                 AND (course = :siteid OR course = :courseid)
                 AND state = :active";

    $admintypes = $DB->get_records_sql($query,
        array('siteid' => $SITE->id, 'courseid' => $COURSE->id, 'active' => LTI_TOOL_STATE_CONFIGURED));

    $types = array();
    $types[0] = (object)array('name' => get_string('automatic', 'lti'), 'course' => 0, 'toolproxyid' => null);

    foreach ($admintypes as $type) {
        $types[$type->id] = $type;
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

    if (!isset($urlparts['host'])) {
        $urlparts['host'] = '';
    }

    if (substr($urlparts['host'], 0, 4) === 'www.') {
        $urlparts['host'] = substr($urlparts['host'], 4);
    }

    return $urllower = $urlparts['host'] . '/' . $urlparts['path'];
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
    $query = "SELECT t2.value
                FROM {lti_types_config} t1
                JOIN {lti_types_config} t2 ON t1.typeid = t2.typeid
                JOIN {lti_types} type ON t2.typeid = type.id
              WHERE t1.name = 'resourcekey'
                AND t1.value = :key1
                AND t2.name = 'password'
                AND type.state = :configured1
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

    $sharedsecrets = $DB->get_records_sql($query, array('configured1' => LTI_TOOL_STATE_CONFIGURED,
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

    $DB->update_record('lti_types', array('id' => $id, 'state' => $state));
}

/**
 * Transforms a basic LTI object to an array
 *
 * @param object $ltiobject    Basic LTI object
 *
 * @return array Basic LTI configuration details
 */
function lti_get_config($ltiobject) {
    $typeconfig = array();
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
 * @return Instance configuration
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
 * @return Configuration details
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

    $type->lti_parameters = $basicltitype->parameter;

    if (isset($config['resourcekey'])) {
        $type->lti_resourcekey = $config['resourcekey'];
    }
    if (isset($config['password'])) {
        $type->lti_password = $config['password'];
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

    if (isset($config['debuglaunch'])) {
        $type->lti_debuglaunch = $config['debuglaunch'];
    }

    if (isset($config['module_class_type'])) {
        $type->lti_module_class_type = $config['module_class_type'];
    }

    return $type;
}

function lti_prepare_type_for_save($type, $config) {
    if (isset($config->lti_toolurl)) {
        $type->baseurl = $config->lti_toolurl;
        $type->tooldomain = lti_get_domain_from_url($config->lti_toolurl);
    }
    if (isset($config->lti_typename)) {
        $type->name = $config->lti_typename;
    }
    $type->coursevisible = !empty($config->lti_coursevisible) ? $config->lti_coursevisible : 0;
    $config->lti_coursevisible = $type->coursevisible;

    if (isset($config->lti_forcessl)) {
        $type->forcessl = !empty($config->lti_forcessl) ? $config->lti_forcessl : 0;
        $config->lti_forcessl = $type->forcessl;
    }

    $type->timemodified = time();

    unset ($config->lti_typename);
    unset ($config->lti_toolurl);
}

function lti_update_type($type, $config) {
    global $DB;

    lti_prepare_type_for_save($type, $config);

    if ($DB->update_record('lti_types', $type)) {
        foreach ($config as $key => $value) {
            if (substr($key, 0, 4) == 'lti_' && !is_null($value)) {
                $record = new \StdClass();
                $record->typeid = $type->id;
                $record->name = substr($key, 4);
                $record->value = $value;
                lti_update_config($record);
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
            if (substr($key, 0, 4) == 'lti_' && !is_null($value)) {
                $record = new \StdClass();
                $record->typeid = $id;
                $record->name = substr($key, 4);
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
 * Generates some of the tool proxy configuration based on the admin configuration details
 *
 * @param int $id
 *
 * @return Tool Proxy details
 */
function lti_get_tool_proxy($id) {
    global $DB;

    $toolproxy = $DB->get_record('lti_tool_proxies', array('id' => $id));
    return $toolproxy;
}

/**
 * Generates some of the tool proxy configuration based on the admin configuration details
 *
 * @param int $id
 *
 * @return Tool Proxy details
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
    }
    if (isset($config->lti_services)) {
        $toolproxy->serviceoffered = implode("\n", $config->lti_services);
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
 * @return Record id number
 */
function lti_update_config($config) {
    global $DB;

    $return = true;
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
 * @param int  $toolproxyid   Id of tool proxy record
 * @param int  $courseid      Id of course (null if system settings)
 * @param int  $instanceid    Id of course module (null if system or context settings)
 *
 * @return array  Array settings
 */
function lti_get_tool_settings($toolproxyid, $courseid = null, $instanceid = null) {
    global $DB;

    $settings = array();
    $settingsstr = $DB->get_field('lti_tool_settings', 'settings', array('toolproxyid' => $toolproxyid,
        'course' => $courseid, 'coursemoduleid' => $instanceid));
    if ($settingsstr !== false) {
        $settings = json_decode($settingsstr, true);
    }
    return $settings;
}

/**
 * Sets the tool settings (
 *
 * @param array  $settings      Array of settings
 * @param int    $toolproxyid   Id of tool proxy record
 * @param int    $courseid      Id of course (null if system settings)
 * @param int    $instanceid    Id of course module (null if system or context settings)
 */
function lti_set_tool_settings($settings, $toolproxyid, $courseid = null, $instanceid = null) {
    global $DB;

    $json = json_encode($settings);
    $record = $DB->get_record('lti_tool_settings', array('toolproxyid' => $toolproxyid,
        'course' => $courseid, 'coursemoduleid' => $instanceid));
    if ($record !== false) {
        $DB->update_record('lti_tool_settings', array('id' => $record->id, 'settings' => $json, 'timemodified' => time()));
    } else {
        $record = new \stdClass();
        $record->toolproxyid = $toolproxyid;
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
 * @param $oldparms     Parameters to be passed for signing
 * @param $endpoint     url of the external tool
 * @param $method       Method for sending the parameters (e.g. POST)
 * @param $oauth_consumoer_key          Key
 * @param $oauth_consumoer_secret       Secret
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
 * Posts the launch petition HTML
 *
 * @param $newparms     Signed parameters
 * @param $endpoint     URL of the external tool
 * @param $debug        Debug (true/false)
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
    return (stripos($CFG->httpswwwroot, 'https://') === 0);
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
 * Logs the request to a file in temp dir
 *
 * @param string $rawbody
 */
function lti_log_request($rawbody) {
    if ($tempdir = make_temp_directory('mod_lti', false)) {
        if ($tempfile = tempnam($tempdir, 'mod_lti_request'.date('YmdHis'))) {
            file_put_contents($tempfile, $rawbody);
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
       'Context.id' => 'context_id',
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
       'Result.autocreate' => 'lis_outcome_service_url');

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
 * @return mod_lti\local\ltiservice\service_base Service
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
 * @param array  $services    Array of services
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
