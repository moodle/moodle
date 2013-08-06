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
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu

/**
 * This file contains the library of functions and constants for the lti module
 *
 * @package    mod
 * @subpackage lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// TODO: Switch to core oauthlib once implemented - MDL-30149
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
    }

    if ($typeid) {
        $typeconfig = lti_get_type_config($typeid);
    } else {
        //There is no admin configuration for this tool. Use configuration in the lti instance record plus some defaults.
        $typeconfig = (array)$instance;

        $typeconfig['sendname'] = $instance->instructorchoicesendname;
        $typeconfig['sendemailaddr'] = $instance->instructorchoicesendemailaddr;
        $typeconfig['customparameters'] = $instance->instructorcustomparameters;
        $typeconfig['acceptgrades'] = $instance->instructorchoiceacceptgrades;
        $typeconfig['allowroster'] = $instance->instructorchoiceallowroster;
        $typeconfig['forcessl'] = '0';
    }

    //Default the organizationid if not specified
    if (empty($typeconfig['organizationid'])) {
        $urlparts = parse_url($CFG->wwwroot);

        $typeconfig['organizationid'] = $urlparts['host'];
    }

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

    $endpoint = !empty($instance->toolurl) ? $instance->toolurl : $typeconfig['toolurl'];
    $endpoint = trim($endpoint);

    //If the current request is using SSL and a secure tool URL is specified, use it
    if (lti_request_is_using_ssl() && !empty($instance->securetoolurl)) {
        $endpoint = trim($instance->securetoolurl);
    }

    //If SSL is forced, use the secure tool url if specified. Otherwise, make sure https is on the normal launch URL.
    if ($typeconfig['forcessl'] == '1') {
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
    $requestparams = lti_build_request($instance, $typeconfig, $course);

    $launchcontainer = lti_get_launch_container($instance, $typeconfig);
    $returnurlparams = array('course' => $course->id, 'launch_container' => $launchcontainer, 'instanceid' => $instance->id);

    if ( $orgid ) {
        $requestparams["tool_consumer_instance_guid"] = $orgid;
    }

    if (empty($key) || empty($secret)) {
        $returnurlparams['unsigned'] = '1';
    }

    // Add the return URL. We send the launch container along to help us avoid frames-within-frames when the user returns.
    $url = new moodle_url('/mod/lti/return.php', $returnurlparams);
    $returnurl = $url->out(false);

    if ($typeconfig['forcessl'] == '1') {
        $returnurl = lti_ensure_url_is_https($returnurl);
    }

    $requestparams['launch_presentation_return_url'] = $returnurl;

    if (!empty($key) && !empty($secret)) {
        $parms = lti_sign_parameters($requestparams, $endpoint, "POST", $key, $secret);

        $endpointurl = new moodle_url($endpoint);
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
        //If no key and secret, do the launch unsigned.
        $parms = $requestparams;
    }

    $debuglaunch = ( $instance->debuglaunch == 1 );

    $content = lti_post_launch_html($parms, $endpoint, $debuglaunch);

    echo $content;
}

function lti_build_sourcedid($instanceid, $userid, $launchid = null, $servicesalt) {
    $data = new stdClass();

    $data->instanceid = $instanceid;
    $data->userid = $userid;
    if (!empty($launchid)) {
        $data->launchid = $launchid;
    } else {
        $data->launchid = mt_rand();
    }

    $json = json_encode($data);

    $hash = hash('sha256', $json . $servicesalt, false);

    $container = new stdClass();
    $container->data = $data;
    $container->hash = $hash;

    return $container;
}

/**
 * This function builds the request that must be sent to the tool producer
 *
 * @param object    $instance       Basic LTI instance object
 * @param object    $typeconfig     Basic LTI tool configuration
 * @param object    $course         Course object
 *
 * @return array    $request        Request details
 */
function lti_build_request($instance, $typeconfig, $course) {
    global $USER, $CFG;

    if (empty($instance->cmid)) {
        $instance->cmid = 0;
    }

    $role = lti_get_ims_role($USER, $instance->cmid, $instance->course);

    $requestparams = array(
        'resource_link_id' => $instance->id,
        'resource_link_title' => $instance->name,
        'resource_link_description' => $instance->intro,
        'user_id' => $USER->id,
        'roles' => $role,
        'context_id' => $course->id,
        'context_label' => $course->shortname,
        'context_title' => $course->fullname,
        'launch_presentation_locale' => current_language()
    );

    $placementsecret = $instance->servicesalt;

    if ( isset($placementsecret) ) {
        $sourcedid = json_encode(lti_build_sourcedid($instance->id, $USER->id, null, $placementsecret));
    }

    if ( isset($placementsecret) &&
         ( $typeconfig['acceptgrades'] == LTI_SETTING_ALWAYS ||
         ( $typeconfig['acceptgrades'] == LTI_SETTING_DELEGATE && $instance->instructorchoiceacceptgrades == LTI_SETTING_ALWAYS ) ) ) {
        $requestparams['lis_result_sourcedid'] = $sourcedid;

        //Add outcome service URL
        $serviceurl = new moodle_url('/mod/lti/service.php');
        $serviceurl = $serviceurl->out();

        if ($typeconfig['forcessl'] == '1') {
            $serviceurl = lti_ensure_url_is_https($serviceurl);
        }

        $requestparams['lis_outcome_service_url'] = $serviceurl;
    }

    // Send user's name and email data if appropriate
    if ( $typeconfig['sendname'] == LTI_SETTING_ALWAYS ||
         ( $typeconfig['sendname'] == LTI_SETTING_DELEGATE && $instance->instructorchoicesendname == LTI_SETTING_ALWAYS ) ) {
        $requestparams['lis_person_name_given'] =  $USER->firstname;
        $requestparams['lis_person_name_family'] =  $USER->lastname;
        $requestparams['lis_person_name_full'] =  $USER->firstname." ".$USER->lastname;
    }

    if ( $typeconfig['sendemailaddr'] == LTI_SETTING_ALWAYS ||
         ( $typeconfig['sendemailaddr'] == LTI_SETTING_DELEGATE && $instance->instructorchoicesendemailaddr == LTI_SETTING_ALWAYS ) ) {
        $requestparams['lis_person_contact_email_primary'] = $USER->email;
    }

    // Concatenate the custom parameters from the administrator and the instructor
    // Instructor parameters are only taken into consideration if the administrator
    // has giver permission
    $customstr = $typeconfig['customparameters'];
    $instructorcustomstr = $instance->instructorcustomparameters;
    $custom = array();
    $instructorcustom = array();
    if ($customstr) {
        $custom = lti_split_custom_parameters($customstr);
    }
    if (isset($typeconfig['allowinstructorcustom']) && $typeconfig['allowinstructorcustom'] == LTI_SETTING_NEVER) {
        $requestparams = array_merge($custom, $requestparams);
    } else {
        if ($instructorcustomstr) {
            $instructorcustom = lti_split_custom_parameters($instructorcustomstr);
        }
        foreach ($instructorcustom as $key => $val) {
            // Ignore the instructor's parameter
            if (!array_key_exists($key, $custom)) {
                $custom[$key] = $val;
            }
        }
        $requestparams = array_merge($custom, $requestparams);
    }

    // Make sure we let the tool know what LMS they are being called from
    $requestparams["ext_lms"] = "moodle-2";
    $requestparams['tool_consumer_info_product_family_code'] = 'moodle';
    $requestparams['tool_consumer_info_version'] = strval($CFG->version);

    // Add oauth_callback to be compliant with the 1.0A spec
    $requestparams['oauth_callback'] = 'about:blank';

    //The submit button needs to be part of the signature as it gets posted with the form.
    //This needs to be here to support launching without javascript.
    $submittext = get_string('press_to_submit', 'lti');
    $requestparams['ext_submit'] = $submittext;

    $requestparams['lti_version'] = 'LTI-1p0';
    $requestparams['lti_message_type'] = 'basic-lti-launch-request';

    return $requestparams;
}

function lti_get_tool_table($tools, $id) {
    global $CFG, $OUTPUT, $USER;
    $html = '';

    $typename = get_string('typename', 'lti');
    $baseurl = get_string('baseurl', 'lti');
    $action = get_string('action', 'lti');
    $createdon = get_string('createdon', 'lti');

    if ($id == 'lti_configured') {
        $html .= '<div><a style="margin-top:.25em" href="'.$CFG->wwwroot.'/mod/lti/typessettings.php?action=add&amp;sesskey='.$USER->sesskey.'">'.get_string('addtype', 'lti').'</a></div>';
    }

    if (!empty($tools)) {
        $html .= "
        <div id=\"{$id}_container\" style=\"margin-top:.5em;margin-bottom:.5em\">
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
            $date = userdate($type->timecreated);
            $accept = get_string('accept', 'lti');
            $update = get_string('update', 'lti');
            $delete = get_string('delete', 'lti');

            $baseurl = new moodle_url('/mod/lti/typessettings.php', array(
                    'action' => 'accept',
                    'id' => $type->id,
                    'sesskey' => sesskey(),
                    'tab' => $id
                ));

            $accepthtml = $OUTPUT->action_icon($baseurl,
                    new pix_icon('t/check', $accept, '', array('class' => 'iconsmall')), null,
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
                    new pix_icon('t/edit', $update, '', array('class' => 'iconsmall')), null,
                    array('title' => $update, 'class' => 'editing_update'));

            $deleteurl = clone($baseurl);
            $deleteurl->param('action', $deleteaction);
            $deletehtml = $OUTPUT->action_icon($deleteurl,
                    new pix_icon('t/delete', $delete, '', array('class' => 'iconsmall')), null,
                    array('title' => $delete, 'class' => 'editing_delete'));
            $html .= "
            <tr>
                <td>
                    {$type->name}
                </td>
                <td>
                    {$type->baseurl}
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
 * Splits the custom parameters field to the various parameters
 *
 * @param string $customstr     String containing the parameters
 *
 * @return Array of custom parameters
 */
function lti_split_custom_parameters($customstr) {
    $lines = preg_split("/[\n;]/", $customstr);
    $retval = array();
    foreach ($lines as $line) {
        $pos = strpos($line, "=");
        if ( $pos === false || $pos < 1 ) {
            continue;
        }
        $key = trim(textlib::substr($line, 0, $pos));
        $val = trim(textlib::substr($line, $pos+1, strlen($line)));
        $key = lti_map_keyname($key);
        $retval['custom_'.$key] = $val;
    }
    return $retval;
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
    $key = textlib::strtolower(trim($key));
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
 * @param mixed $user User object or user id
 * @param int $cmid The course module id of the LTI activity
 * @return string A role string suitable for passing with an LTI launch
 */
function lti_get_ims_role($user, $cmid, $courseid) {
    $roles = array();

    if (empty($cmid)) {
        //If no cmid is passed, check if the user is a teacher in the course
        //This allows other modules to programmatically "fake" a launch without
        //a real LTI instance
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
        array_push($roles, 'urn:lti:sysrole:ims/lis/Administrator');
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
        $filter = array('course' => $course);
    } else {
        $filter = array();
    }

    return $DB->get_records('lti_types', $filter);
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

    $admintypes = $DB->get_records_sql($query, array('siteid' => $SITE->id, 'courseid' => $COURSE->id, 'active' => LTI_TOOL_STATE_CONFIGURED));

    $types = array();
    $types[0] = (object)array('name' => get_string('automatic', 'lti'), 'course' => $SITE->id);

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
            //100 points for exact thumbprint match
            $tool->_matchscore += 100;
        } else if (substr($urllower, 0, strlen($toolbaseurllower)) === $toolbaseurllower) {
            //50 points if tool thumbprint starts with the base URL thumbprint
            $tool->_matchscore += 50;
        }

        //Prefer course tools over site tools
        if (!empty($courseid)) {
            //Minus 10 points for not matching the course id (global tools)
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

    //None of the tools are suitable for this URL
    if ($bestmatch->_matchscore <= 0) {
        return null;
    }

    return $bestmatch;
}

function lti_get_shared_secrets_by_key($key) {
    global $DB;

    //Look up the shared secret for the specified key in both the types_config table (for configured tools)
    //And in the lti resource table for ad-hoc tools
    $query = "SELECT t2.value
                FROM {lti_types_config} t1
                JOIN {lti_types_config} t2 ON t1.typeid = t2.typeid
                JOIN {lti_types} type ON t2.typeid = type.id
              WHERE t1.name = 'resourcekey'
                AND t1.value = :key1
                AND t2.name = 'password'
                AND type.state = :configured
              UNION
             SELECT password AS value
               FROM {lti}
              WHERE resourcekey = :key2";

    $sharedsecrets = $DB->get_records_sql($query, array('configured' => LTI_TOOL_STATE_CONFIGURED, 'key1' => $key, 'key2' => $key));

    $values = array_map(function($item) {
        return $item->value;
    }, $sharedsecrets);

    //There should really only be one shared secret per key. But, we can't prevent
    //more than one getting entered. For instance, if the same key is used for two tool providers.
    return $values;
}

/**
 * Delete a Basic LTI configuration
 *
 * @param int $id   Configuration id
 */
function lti_delete_type($id) {
    global $DB;

    //We should probably just copy the launch URL to the tool instances in this case... using a single query
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

    $type = new stdClass();
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

    $type = new stdClass();

    $type->lti_typename = $basicltitype->name;

    $type->typeid = $basicltitype->id;

    $type->lti_toolurl = $basicltitype->baseurl;

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
    $type->baseurl = $config->lti_toolurl;
    $type->tooldomain = lti_get_domain_from_url($config->lti_toolurl);
    $type->name = $config->lti_typename;

    $type->coursevisible = !empty($config->lti_coursevisible) ? $config->lti_coursevisible : 0;
    $config->lti_coursevisible = $type->coursevisible;

    $type->forcessl = !empty($config->lti_forcessl) ? $config->lti_forcessl : 0;
    $config->lti_forcessl = $type->forcessl;

    $type->timemodified = time();

    unset ($config->lti_typename);
    unset ($config->lti_toolurl);
}

function lti_update_type($type, $config) {
    global $DB;

    lti_prepare_type_for_save($type, $config);

    if ($DB->update_record('lti_types', $type)) {
        foreach ($config as $key => $value) {
            if (substr($key, 0, 4)=='lti_' && !is_null($value)) {
                $record = new StdClass();
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

    //Create a salt value to be used for signing passed data to extension services
    //The outcome service uses the service salt on the instance. This can be used
    //for communication with services not related to a specific LTI instance.
    $config->lti_servicesalt = uniqid('', true);

    $id = $DB->insert_record('lti_types', $type);

    if ($id) {
        foreach ($config as $key => $value) {
            if (substr($key, 0, 4)=='lti_' && !is_null($value)) {
                $record = new StdClass();
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
 * Add a tool configuration in the database
 *
 * @param $config   Tool configuration
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
 * @param $config   Tool configuration
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
 * Signs the petition to launch the external tool using OAuth
 *
 * @param $oldparms     Parameters to be passed for signing
 * @param $endpoint     url of the external tool
 * @param $method       Method for sending the parameters (e.g. POST)
 * @param $oauth_consumoer_key          Key
 * @param $oauth_consumoer_secret       Secret
 * @param $submittext  The text for the submit button
 * @param $orgid       LMS name
 * @param $orgdesc     LMS key
 */
function lti_sign_parameters($oldparms, $endpoint, $method, $oauthconsumerkey, $oauthconsumersecret) {
    //global $lastbasestring;
    $parms = $oldparms;

    $testtoken = '';

    // TODO: Switch to core oauthlib once implemented - MDL-30149
    $hmacmethod = new lti\OAuthSignatureMethod_HMAC_SHA1();
    $testconsumer = new lti\OAuthConsumer($oauthconsumerkey, $oauthconsumersecret, null);
    $accreq = lti\OAuthRequest::from_consumer_and_token($testconsumer, $testtoken, $method, $endpoint, $parms);
    $accreq->sign_request($hmacmethod, $testconsumer, $testtoken);

    // Pass this back up "out of band" for debugging
    //$lastbasestring = $accreq->get_signature_base_string();

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
    $r = "<form action=\"".$endpoint."\" name=\"ltiLaunchForm\" id=\"ltiLaunchForm\" method=\"post\" encType=\"application/x-www-form-urlencoded\">\n";

    $submittext = $newparms['ext_submit'];

    // Contruct html for the launch parameters
    foreach ($newparms as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        if ( $key == "ext_submit" ) {
            $r .= "<input type=\"submit\" name=\"";
        } else {
            $r .= "<input type=\"hidden\" name=\"";
        }
        $r .= $key;
        $r .= "\" value=\"";
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
        $r .=  "<b>".get_string("basiclti_endpoint", "lti")."</b><br/>\n";
        $r .= $endpoint . "<br/>\n&nbsp;<br/>\n";
        $r .=  "<b>".get_string("basiclti_parameters", "lti")."</b><br/>\n";
        foreach ($newparms as $key => $value) {
            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value);
            $r .= "$key = $value<br/>\n";
        }
        $r .= "&nbsp;<br/>\n";
        //$r .= "<p><b>".get_string("basiclti_base_string", "lti")."</b><br/>\n".$lastbasestring."</p>\n";
        $r .= "</div>\n";
    }
    $r .= "</form>\n";

    if ( ! $debug ) {
        $ext_submit = "ext_submit";
        $ext_submit_text = $submittext;
        $r .= " <script type=\"text/javascript\"> \n" .
            "  //<![CDATA[ \n" .
            "    document.getElementById(\"ltiLaunchForm\").style.display = \"none\";\n" .
            "    nei = document.createElement('input');\n" .
            "    nei.setAttribute('type', 'hidden');\n" .
            "    nei.setAttribute('name', '".$ext_submit."');\n" .
            "    nei.setAttribute('value', '".$ext_submit_text."');\n" .
            "    document.getElementById(\"ltiLaunchForm\").appendChild(nei);\n" .
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

    $devicetype = get_device_type();

    //Scrolling within the object element doesn't work on iOS or Android
    //Opening the popup window also had some issues in testing
    //For mobile devices, always take up the entire screen to ensure the best experience
    if ($devicetype === 'mobile' || $devicetype === 'tablet' ) {
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
        //If the URL starts with http, replace with https
        if (stripos($url, 'http://') === 0) {
            $url = 'https://' . substr($url, 8);
        }
    }

    return $url;
}
