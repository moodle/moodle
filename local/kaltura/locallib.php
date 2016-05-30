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

/**
 * Kaltura local library of functions.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

global $CFG; // should be defined in config.php

require_once($CFG->dirroot.'/mod/lti/locallib.php');

define('KALTURA_PLUGIN_NAME', 'local_kaltura');
define('KALTURA_DEFAULT_URI', 'www.kaltura.com');
define('KALTURA_REPORT_DEFAULT_URI', 'http://apps.kaltura.com/hosted_pages');
define('KAF_MYMEDIA_MODULE', 'mymedia');
define('KAF_MEDIAGALLERY_MODULE', 'coursegallery');
define('KAF_BROWSE_EMBED_MODULE', 'browseembed');
define('KAF_MYMEDIA_ENDPOINT', 'hosted/index/my-media');
define('KAF_MEDIAGALLERY_ENDPOINT', 'hosted/index/course-gallery');
define('KAF_BROWSE_EMBED_ENDPOINT', 'browseandembed/index/browseandembed');
define('KALTURA_LOG_REQUEST', 'REQ');
define('KALTURA_LOG_RESPONSE', 'RES');
define('KALTURA_PANEL_HEIGHT', 580);
define('KALTURA_PANEL_WIDTH', 1100);
define('KALTURA_LTI_LEARNER_ROLE', 'Learner');
define('KALTURA_LTI_INSTRUCTOR_ROLE', 'Instructor');
define('KALTURA_LTI_ADMIN_ROLE', 'urn:lti:sysrole:ims/lis/Administrator');
define('KALTURA_REPO_NAME', 'kaltura');
// For KALTURA_URI_TOKEN
// 1. Do not use characters that are used in regular expressions like {}[]()
// 2. Moodle cleans up urls that look like relative links into complete urls by inserting $CFG->wwwroot
define('KALTURA_URI_TOKEN', 'kaltura-kaf-uri.com');

/**
 * This function validates whether a requested KAF module is valid.
 * @param string $module The name of the module.
 * @return bool True if valid, otherwise false.
 */
function local_kaltura_validate_kaf_module_request($module) {
    $valid = false;

    switch ($module) {
        case KAF_MYMEDIA_MODULE:
            $valid = true;
            break;
        case KAF_MEDIAGALLERY_MODULE:
            $valid = true;
            break;
        case KAF_BROWSE_EMBED_MODULE:
            $valid = true;
            break;
    }
    return $valid;
}

/**
 * This function calls @see lti_get_launch_container() to an LTI launch container to display the content.
 * @param bool $withblocks Set to true to dislay embed content with Moodle blocks.  Otherwise set to false.
 * @return int Container value
 */
function local_kaltura_get_lti_launch_container($withblocks = true) {
    $lti = new stdClass();
    $container = 0;

    if (!empty($withblocks)) {
        $lti->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED;
        $container = lti_get_launch_container($lti, array('launchcontainer' => LTI_LAUNCH_CONTAINER_EMBED));
    } else {
        $lti->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS;
        $container = lti_get_launch_container($lti, array('launchcontainer' => LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS));
    }

    return $container;
}

/**
 * This function validates the parameters to see if all of the requirements for the module are met.
 * @param array $params An array of parameters
 * @return bool true if valid, otherwise false
 */
function local_kaltura_validate_mymedia_required_params($params) {
    $valid = true;

    $expectedkeys = array(
        // The activity instance id
        'id' => '',
        // The KAL module requested
        'module' => '',
        'course' => new stdClass(),
        'title' => '',
        'width' => '',
        'height' => '',
        'cmid' => '',
        'custom_publishdata' => '',
    );

    // Get keys that reside in both parameters and expectedkeys
    $matchingkeys = array_intersect_key($params, $expectedkeys);

    // The number of keys in the result should equal the number of expectedkeys
    if (count($expectedkeys) != count($matchingkeys)) {
        return false;
    }

    $invalid = !is_numeric($params['id']) || !is_numeric($params['width']) || !is_numeric($params['height']) || !is_numeric($params['cmid']) || !is_object($params['course']);

    if ($invalid) {
        return false;
    }

    return true;
}

/**
 * This function validates the parameters to see if all of the requirements for the module are met.
 * @param array $params An array of parameters
 * @return bool true if valid, otherwise false
 */
function local_kaltura_validate_mediagallery_required_params($params) {
    $valid = true;

    $expectedkeys = array(
        // The activity instance id
        'id' => '',
        // The KAL module requested
        'module' => '',
        'course' => new stdClass(),
        'title' => '',
        'width' => '',
        'height' => '',
        'cmid' => '',
        'custom_publishdata' => '',
    );

    // Get keys that reside in both parameters and expectedkeys
    $matchingkeys = array_intersect_key($params, $expectedkeys);

    // The number of keys in the result should equal the number of expectedkeys
    if (count($expectedkeys) != count($matchingkeys)) {
        return false;
    }

    $invalid = !is_numeric($params['id']) || !is_numeric($params['width']) || !is_numeric($params['height']) || !is_numeric($params['cmid']) || !is_object($params['course']);

    if ($invalid) {
        return false;
    }

    return true;
}

/**
 * This function validates the parameters to see if all of the requirements for the module are met.
 * @param array $params An array of parameters
 * @return bool true if valid, otherwise false
 */
function local_kaltura_validate_browseembed_required_params($params) {
    $valid = true;

    $expectedkeys = array(
        // The activity instance id
        'id' => '',
        // The KAL module requested
        'module' => '',
        'course' => new stdClass(),
        'title' => '',
        'width' => '',
        'height' => '',
        'cmid' => '',
        'custom_publishdata' => '',
    );

    // Get keys that reside in both parameters and expectedkeys
    $matchingkeys = array_intersect_key($params, $expectedkeys);

    // The number of keys in the result should equal the number of expectedkeys
    if (count($expectedkeys) != count($matchingkeys)) {
        return false;
    }

    $invalid = !is_numeric($params['id']) || !is_numeric($params['width']) || !is_numeric($params['height']) || !is_numeric($params['cmid']) || !is_object($params['course']);

    if ($invalid) {
        return false;
    }

    return true;
}

/**
 * This function returns the endpoint URL belonging to the module that was requested.
 * @param string $module The name of the module being requested.
 * @param string Part of the URL that makes up the endpoint pertaining to the module requested.
 * @return string Part of the URL for the end point designated for the module.  Otherwise an empty string.
 */
function local_kaltura_get_endpoint($module) {
    switch ($module) {
        case KAF_MYMEDIA_MODULE:
            return KAF_MYMEDIA_ENDPOINT;
            break;
        case KAF_MEDIAGALLERY_MODULE:
            return KAF_MEDIAGALLERY_ENDPOINT;
            break;
        case KAF_BROWSE_EMBED_MODULE:
            return KAF_BROWSE_EMBED_ENDPOINT;
            break;
    }
    return '';
}

/**
 * This function replaces the KALTURA_TOKEN_URI in a source URL with KAF URI domain.
 * @param string $url A url which need the kaf_uri added.
 * @return string Returns url with added KAF URI domain.
 */
function local_kaltura_add_kaf_uri_token($url) {
    $configsettings = local_kaltura_get_config();
    // For records that have been migrated from old kaf uri to token format by search and replace.
    if (preg_match('/https?:\/\/'.KALTURA_URI_TOKEN.'/', $url)) {
        $url = preg_replace('/https?:\/\/'.KALTURA_URI_TOKEN.'/', $configsettings->kaf_uri, $url);
    }
    return $url;
}

/**
 * This function formats and returns an object that will be passed to mod_lti locallib.php functions.
 * @param array $ltirequest An array of parameters to be converted into a properly formatted mod_lti instance.
 * @return object Returns an object that meets the requirements for use with mod_lti locallib.php functions.
 */
function local_kaltura_format_lti_instance_object($ltirequest) {
    $configsettings = local_kaltura_get_config();

    // Convert request parameters into mod_lti friendly format for consumption.
    $lti = new stdClass();
    $lti->course = $ltirequest['course']->id;
    $lti->id = $ltirequest['id'];
    $lti->name = $ltirequest['title'];
    $lti->intro = isset($ltirequest['intro']) ? $ltirequest['intro'] : '';
    $lti->instructorchoicesendname = LTI_SETTING_ALWAYS;
    $lti->instructorchoicesendemailaddr = LTI_SETTING_ALWAYS;
    $lti->custom_publishdata = '';
    $lti->instructorcustomparameters = '';
    $lti->instructorchoiceacceptgrades = LTI_SETTING_NEVER;
    $lti->instructorchoiceallowroster = LTI_SETTING_NEVER;
    $lti->resourcekey  = $configsettings->partner_id;
    $lti->password = $configsettings->adminsecret;
    // The Kaltura tool URL includes the account partner id.
    $newuri = $configsettings->kaf_uri;
    $lti->toolurl = $newuri;
    if (!preg_match('/\/$/',$newuri)) {
        $lti->toolurl .= '/';
    }
    $lti->toolurl .= local_kaltura_get_endpoint($ltirequest['module']);
    // Do not force SSL. At the module level.
    $lti->forcessl = 0;
    $lti->cmid = $ltirequest['cmid'];

    // Check if a source URL was passed.  This means that a plug-in has requested to view a media entry and not a KAF interface.
    if (!isset($ltirequest['source']) || empty($ltirequest['source'])) {
        // If the Moodle site is configured to use HTTPS then this property will be used.
        $lti->securetool = 'https://'.local_kaltura_format_uri(trim($lti->toolurl));
        $lti->toolurl = 'http://'.local_kaltura_format_uri(trim($lti->toolurl));
    } else {
        $url = local_kaltura_format_uri($ltirequest['source']);
        // If the Moodle site is configured to use HTTPS then this property will be used.
        $lti->securetool = 'https://'.trim($url);
        $lti->toolurl = 'http://'.trim($url);
    }

    return $lti;
}

/**
 * This function formats an array that is passed to mod_lti locallib.php functions.
 * @param object $lti An object returned from @see local_kaltura_format_lti_instance_object().
 * @param bool $withblocks Set to true to display blocks.  Otherwise false.
 * @return array An array formatted for use by mod_lti locallib.php functions.
 */
function local_kaltura_format_typeconfig($lti, $withblocks = true) {
    $typeconfig = array();
    $typeconfig['sendname'] = $lti->instructorchoicesendname;
    $typeconfig['sendemailaddr'] = $lti->instructorchoicesendemailaddr;
    $typeconfig['customparameters'] = $lti->instructorcustomparameters;
    $typeconfig['acceptgrades'] = $lti->instructorchoiceacceptgrades;
    $typeconfig['allowroster'] = $lti->instructorchoiceallowroster;
    $typeconfig['launchcontainer'] = local_kaltura_get_lti_launch_container($withblocks);
    return $typeconfig;
}

/**
 * This function is based off of the code from @see lti_view().
 * @param string $endpoint The URL to access the KAF LTI tool.
 * @param string $params The signed parameters returned by @see lti_sign_parameters().
 */
function local_kaltura_strip_querystring($endpoint, $params) {
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
}

/**
 * This function converts an LTI request object into a properly formatted LTI request that can be consumed by Moodle's LTI local library.
 * The function is modeled closely after @see lti_view().  The code was refactored because the original function relied too heavily on
 * there being an LTI tool defined in the LTI activity instance table.
 * @param array $ltirequest An array with parameters specifying some required information for an LTI launch.
 * @param array $withblocks True if Moodle blocks are to be included on the page else false.
 * @return string Returns HTML required to initiate an LTI launch.
 */
function local_kaltura_request_lti_launch($ltirequest, $withblocks = true, $editor = null) {
    global $CFG, $USER;
    
    if(is_null($editor))
    {
        $editor = 'tinymce';
    }

    $requestparams = array();

    $lti = local_kaltura_format_lti_instance_object($ltirequest);

    $typeconfig = local_kaltura_format_typeconfig($lti, $withblocks);

    // This line was taken from @see lti_add_type.
    // Create a salt value to be used for signing passed data to extension services
    // The outcome service uses the service salt on the instance. This can be used
    // for communication with services not related to a specific LTI instance.
    $lti->servicesalt = uniqid('', true);

    // If SSL is forced, use HTTPS.
    $endpoint = $lti->toolurl;
    if (lti_request_is_using_ssl()) {
        $endpoint = $lti->securetool;
    }

    $requestparams = array_merge(lti_build_standard_request((object) $lti, null, false), lti_build_request((object) $lti, $typeconfig, $ltirequest['course']));
    if(!isset($requestparams['resource_link_id'])) // fix to moodle 2.8 issue where this function (lti_build_request) does not set resource_link_id value
    {
        $requestparams['resource_link_id'] = $lti->id;
    }

    // Moodle by default uses the Moodle user id.  Overriding this parameter to user the Moodle username.
    $requestparams['user_id'] = $USER->username;

    // This block of code is loosly based off code from @see lti_view().
    $urlparts = parse_url($CFG->wwwroot);
    $requestparams['tool_consumer_instance_guid'] = $urlparts['host'];

    $returnurlparams['unsigned'] = '0';
    $returnurlparams['editor'] = $editor;

    // Add the return URL. We send the launch container along to help us avoid frames-within-frames when the user returns.
    $url = new moodle_url('/local/kaltura/service.php', $returnurlparams);
    $requestparams['launch_presentation_return_url'] = $url->out(false);

    $serviceurl = new moodle_url('/local/kaltura/service.php');
    $requestparams['lis_outcome_service_url'] = $serviceurl->out(false);

    // Add custom parameters
    $requestparams['custom_publishdata'] = local_kaltura_get_kaf_publishing_data();
    $requestparams['custom_publishdata_encoded'] = '1';

    // Specific settings for video presentation requests.
    if (isset($ltirequest['custom_disable_add_new'])) {
        $requestparams['custom_disable_add_new'] = $ltirequest['custom_disable_add_new'];
    }

    if (isset($ltirequest['custom_filter_type'])) {
        $requestparams['custom_filter_type'] = $ltirequest['custom_filter_type'];
    }

    $params = lti_sign_parameters($requestparams, $endpoint, 'POST', $lti->resourcekey, $lti->password);

    local_kaltura_strip_querystring($endpoint, $params);

    $debuglaunch = 0;

    $content = lti_post_launch_html($params, $endpoint, $debuglaunch);

    // Check if debugging is enabled.
    $enablelogging = get_config(KALTURA_PLUGIN_NAME, 'enable_logging');
    if (!empty($enablelogging)) {
        local_kaltura_log_data($ltirequest['module'], $endpoint, $params, true);
    }

    return $content;
}

/**
 * Writes data to the log table.
 * @param string $module The module where the request originated from.
 * @param string $endpoint The URL the request went out to.
 * @param array $data All parameters used to created the request.
 * @param bool $request Set to true if this is a request.  Set to false if it is a response.
 * @return bool True if the log entry was created.  Otherwise false.
 */
function local_kaltura_log_data($module, $endpoint, $data, $request = true) {
    global $DB;

    if (!is_array($data)) {
        return false;
    }

    $record = new stdClass();
    $record->type = KALTURA_LOG_RESPONSE;

    // If this is a request being sent out, validate the module and make sure it is a supported module.
    if (!empty($request)) {
        // Validate whether the module is one that is supported.
        if (!local_kaltura_validate_kaf_module_request($module)) {
            return false;
        }

        $record->type = KALTURA_LOG_REQUEST;
    }

    $record->module = $module;
    $record->timecreated = time();
    $record->endpoint = $endpoint;
    $record->data = serialize($data);
    $DB->insert_record('local_kaltura_log', $record);

    return true;
}

/**
 * This functions removes the HTTP protocol and the trailing slash from a URI.
 * @param string $uri The URI to format.
 * @return string The formatted URI with the protocol and trailing slash removed.
 */
function local_kaltura_format_uri($uri) {
    $newuri = str_replace('https://', '', $uri);
    $newuri = str_replace('http://', '', $newuri);
    $newuri = str_replace('www.', '', $newuri);
    $newuri = rtrim($newuri, '/');
    return $newuri;
}

/**
 * This function creates a JSON string of the courses the user is enrolled in and the LTI roles the user has in the course.
 * The JSON string is cached in the user's session global for efficiency purposes.
 * @return string A JSON data structure outlining the user's LTI roles in all of their enroled courses.
 */
function local_kaltura_get_kaf_publishing_data() {
    global $USER, $SITE;

    $role = is_siteadmin($USER->id) ? KALTURA_LTI_ADMIN_ROLE : KALTURA_LTI_INSTRUCTOR_ROLE;
    $json = new stdClass();
    $json->courses = array();
    $hascap = false;

    // If the user is not an admin then retrieve all of the user's enroled courses.
    if (KALTURA_LTI_ADMIN_ROLE != $role) {
        $courses = enrol_get_users_courses($USER->id, true, 'id,fullname', 'fullname ASC');
    } else {
        // Calling refactored code that allows for a limit on the number of courses returned.
        $courses = local_kaltura_get_user_capability_course('moodle/course:manageactivities', $USER->id, true, 'id,fullname', 'fullname ASC');
    }

    foreach ($courses as $course) {
        if ($course->id === $SITE->id) {
            // Don't want to include the site id in this list
            continue;
        }

        if (KALTURA_LTI_ADMIN_ROLE != $role) {
            // Check if the user has the manage capability in the course.
            $hascap = has_capability('moodle/course:manageactivities', context_course::instance($course->id), $USER->id, false);
            $role = $hascap ? KALTURA_LTI_INSTRUCTOR_ROLE : KALTURA_LTI_LEARNER_ROLE;
        }

        // The properties must be nameed "courseId", "courseName" and "roles".
        $data = new stdClass();
        $data->courseId = $course->id;
        $data->courseName = $course->fullname;
        $data->roles = $role;
        $json->courses[$course->id] = $data;
    }

    // Return an array with no pre-defined keys to structure the JSON the way Kaltura needs it to be.
    $json->courses = array_values($json->courses);

    return base64_encode(json_encode($json));
}

/**
 * NOTE: This function is refactored from @see get_user_capability_course() from accesslib.php.  The difference is the ability to
 * limit the number of records returned.
 *
 * This function gets the list of courses that this user has a particular capability in.
 * It is still not very efficient.
 *
 * @param string $capability Capability in question.
 * @param int $userid User ID or null for current user.
 * @param bool $doanything True if 'doanything' is permitted (default).
 * @param string $fieldsexceptid Leave blank if you only need 'id' in the course records;
 *   otherwise use a comma-separated list of the fields you require, not including id.
 * @param string $orderby If set, use a comma-separated list of fields from course
 *   table with sql modifiers (DESC) if needed.
 * @param string $limit Limit the set of data returned.
 * @return array Array of courses, may have zero entries. Or false if query failed.
 */
function local_kaltura_get_user_capability_course($capability, $userid = null, $doanything = true, $fieldsexceptid = '', $orderby = '', $limit = 200) {
    global $DB;

    // Convert fields list and ordering.
    $fieldlist = '';
    if ($fieldsexceptid) {
        $fields = explode(',', $fieldsexceptid);
        foreach($fields as $field) {
            $fieldlist .= ',c.'.$field;
        }
    }
    if ($orderby) {
        $fields = explode(',', $orderby);
        $orderby = '';
        foreach($fields as $field) {
            if ($orderby) {
                $orderby .= ',';
            }
            $orderby .= 'c.'.$field;
        }
        $orderby = 'ORDER BY '.$orderby;
    }

    // Obtain a list of everything relevant about all courses including context.
    // Note the result can be used directly as a context (we are going to), the course
    // fields are just appended.

    $contextpreload = context_helper::get_preload_record_columns_sql('x');

    $courses = array();
    $sql = "SELECT c.id $fieldlist, $contextpreload
              FROM {course} c
              JOIN {context} x ON (c.id=x.instanceid
                   AND x.contextlevel=".CONTEXT_COURSE.")
                   $orderby";
    $rs = $DB->get_recordset_sql($sql, null, 0, $limit);

    // Check capability for each course in turn.
    foreach ($rs as $course) {
        context_helper::preload_from_record($course);
        $context = context_course::instance($course->id);
        if (has_capability($capability, $context, $userid, $doanything)) {
            // We've got the capability. Make the record look like a course record
            // and store it
            $courses[] = $course;
        }
    }
    $rs->close();
    return empty($courses) ? array() : $courses;
}

/**
 * This function gets the local configuration and sanitizes the settings.
 * @return object Returns object containing configuration settings for kaltura local plugin.
 */
function local_kaltura_get_config() {
    $configsettings = get_config(KALTURA_PLUGIN_NAME);
    if (empty($configsettings->kaf_uri)) {
        $configsettings->kaf_uri = "";
    }
    // If a https url is needed for kaf_uri it should be entered into the kaf_uri setting as https://.
    if (!empty($configsettings->kaf_uri) && !preg_match('#^https?://#', $configsettings->kaf_uri)) {
        $configsettings->kaf_uri = 'http://'.$configsettings->kaf_uri;
    }
    return $configsettings;
}

/**
 * This functions checks if a URL contains the host name that is configiured for the plug-in.
 * @param string $url The URL to validate.
 * @return bool Returns true if the URL contains the configured host name.  Otherwise false.
 */
function local_kaltura_url_contains_configured_hostname($url) {
    $configuration = local_kaltura_get_config();
    $configuri = local_kaltura_format_uri($configuration->kaf_uri);

    if (empty($configuri)) {
        return false;
    }
    $position = strpos($url, $configuri);
    if (false === $position) {
        return false;
    }

    return true;
}

/**
 * This function returns the URL parameter with a protocol prefixed, if non was detected.  http:// is used by default if no protocol is found.
 * @param string $url The URL to verify.
 * @return string Returns the URL with the protocol.  An empty string is returned in the case of an exception being thrown.
 */
function local_kaltura_add_protocol_to_url($url) {
    $newurl = '';
    if (0 === strpos($url, 'https://')) {
        $newurl = $url;
    } else if (0 === strpos($url, 'http://')) {
        $newurl = $url;
    } else {
        $newurl = 'http://'.$url;
    }

    try {
        $newurl = validate_param($newurl, PARAM_URL);
    } catch (invalid_parameter_exception $e) {
        return '';
    }

    return $newurl;
}

/**
 * This function searlizes an object or array and base 64 encodes it for storage into a table.
 * @param array|object $object An object or array.
 * @return string A base 64 encoded string of the parameter.
 */
function local_kaltura_encode_object_for_storage($object) {
    // Check if the parameter either an object or array of if it's empty.
    $data = $object;
    if (!is_array($data)) {
        $data = (array) $data;
    }

    if (empty($data) || (!is_array($object) && !is_object($object))) {
        return '';
    }

    return base64_encode(serialize($object));
}

/**
 * This function base 64 decodes and unsearlizes an object.
 * @param string $object A base 64 encoded string.
 * @return array|object An array or object.
 */
function local_kaltura_decode_object_for_storage($object) {
    // Check if the parameter is empty.
    if (empty($object)) {
        return '';
    }

    return unserialize(base64_decode($object));
}

/**
 * This function takes a KalturaMediaEntry or KalturaDataEntry object and converts it into a Moodle metadata object.
 * @param KalturaMediaEntry $object A KalturaMediaEntry object
 * @return object|bool A slimed down version of the KalturaMediaEntry object, with slightly different object property names.  Or false if an error was found.
 */
function local_kaltura_convert_kaltura_base_entry_object($object) {
    $metadata = new stdClass;

    if ($object instanceof KalturaMediaEntry) {

        $metadata->url = '';
        $metadata->dataurl = $object->dataUrl;
        $metadata->width = $object->width;
        $metadata->height = $object->height;
        $metadata->entryid = $object->id;
        $metadata->title = $object->name;
        $metadata->thumbnailurl = $object->thumbnailUrl;
        $metadata->duration = $object->duration;
        $metadata->description = $object->description;
        $metadata->createdat = $object->createdAt;
        $metadata->owner = $object->creatorId;
        $metadata->tags = $object->tags;
        $metadata->showtitle = 'on';
        $metadata->showdescription = 'on';
        $metadata->showowner = 'on';
        $metadata->player = '';
        $metadata->size = '';
    } else if ($object instanceof KalturaDataEntry) {

        $metadata->url = '';
        $metadata->dataurl = '';
        $metadata->url = '';
        $metadata->width = 0;
        $metadata->height = 0;
        $metadata->entryid = $object->id;
        $metadata->title = $object->name;
        $metadata->thumbnailurl = $object->thumbnailUrl;
        $metadata->duration = 0;
        $metadata->description = $object->description;
        $metadata->createdat = $object->createdAt;
        $metadata->owner = $object->creatorId;
        $metadata->tags = $object->tags;
        $metadata->showtitle = 'on';
        $metadata->showdescription = 'on';
        $metadata->showowner = 'on';
        $metadata->player = '';
        $metadata->size = '';
    } else {
        $metadata = false;
    }

    return $metadata;
}

function local_kaltura_build_kaf_uri($source_url) {
    $kaf_uri = local_kaltura_get_config()->kaf_uri;
    $parsed_source_url = parse_url($source_url);
    if ($parsed_source_url['host'] == KALTURA_URI_TOKEN) {
        return $source_url;
    }
    if(!empty($parsed_source_url['path'])) {
        $kaf_uri = parse_url($kaf_uri);
        $source_host_and_path = $parsed_source_url['host'] . $parsed_source_url['path'];
        $kaf_uri_host_and_path = $kaf_uri['host'] . (isset($kaf_uri['path']) ? $kaf_uri['path'] : '');

        $source_url = str_replace($kaf_uri_host_and_path, '', $source_host_and_path);
        $source_url = 'http://' . KALTURA_URI_TOKEN . $source_url;
    }

    return $source_url;
}
