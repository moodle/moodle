<?php
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
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the library of functions and constants for the basiclti module
 *
 * @package lti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/lti/OAuth.php');

define('LTI_URL_DOMAIN_REGEX', '/(?:https?:\/\/)?(?:www\.)?([^\/]+)(?:\/|$)/i');

define('LTI_LAUNCH_CONTAINER_DEFAULT', 1);
define('LTI_LAUNCH_CONTAINER_EMBED', 2);
define('LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS', 3);
define('LTI_LAUNCH_CONTAINER_WINDOW', 4);

define('LTI_TOOL_STATE_ANY', 0);
define('LTI_TOOL_STATE_CONFIGURED', 1);
define('LTI_TOOL_STATE_PENDING', 2);
define('LTI_TOOL_STATE_REJECTED', 3);

define('LTI_SETTING_NEVER', 0);
define('LTI_SETTING_ALWAYS', 1);
define('LTI_SETTING_DEFAULT', 2);

/**
 * Prints a Basic LTI activity
 *
 * $param int $basicltiid       Basic LTI activity id
 */
function lti_view($instance, $makeobject=false) {
    global $PAGE, $CFG;

    if(empty($instance->typeid)){
        $tool = lti_get_tool_by_url_match($instance->toolurl);
        if($tool){
            $typeid = $tool->id;
        } else {
            $typeid = null;
        }
    } else {
        $typeid = $instance->typeid;
    }
    
    if($typeid){
        $typeconfig = lti_get_type_config($typeid);
    } else {
        //There is no admin configuration for this tool. Use configuration in the lti instance record plus some defaults.
        $typeconfig = (array)$instance;
        
        $typeconfig['sendname'] = $instance->instructorchoicesendname;
        $typeconfig['sendemailaddr'] = $instance->instructorchoicesendemailaddr;
        $typeconfig['customparameters'] = $instance->instructorcustomparameters;
    }
    
    //Default the organizationid if not specified
    if(empty($typeconfig['organizationid'])){
        $urlparts = parse_url($CFG->wwwroot);
        
        $typeconfig['organizationid'] = $urlparts['host'];
    }
    
    $endpoint = !empty($instance->toolurl) ? $instance->toolurl : $typeconfig['toolurl'];
    $key = !empty($instance->resourcekey) ? $instance->resourcekey : $typeconfig['resourcekey'];
    $secret = !empty($instance->password) ? $instance->password : $typeconfig['password'];
    $orgid = $typeconfig['organizationid'];
    /* Suppress this for now - Chuck
    $orgdesc = $typeconfig['organizationdescr'];
    */

    $course = $PAGE->course;
    $requestparams = lti_build_request($instance, $typeconfig, $course);

    // Make sure we let the tool know what LMS they are being called from
    $requestparams["ext_lms"] = "moodle-2";

    // Add oauth_callback to be compliant with the 1.0A spec
    $requestparams["oauth_callback"] = "about:blank";

    $submittext = get_string('press_to_submit', 'lti');
    $parms = sign_parameters($requestparams, $endpoint, "POST", $key, $secret, $submittext, $orgid /*, $orgdesc*/);

    $debuglaunch = ( $instance->debuglaunch == 1 );
    
    $content = post_launch_html($parms, $endpoint, $debuglaunch);
    
    echo $content;
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

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $role = lti_get_ims_role($USER, $context);

    $locale = $course->lang;
    if ( strlen($locale) < 1 ) {
         $locale = $CFG->lang;
    }

    $requestparams = array(
        "resource_link_id" => $instance->id,
        "resource_link_title" => $instance->name,
        "resource_link_description" => $instance->intro,
        "user_id" => $USER->id,
        "roles" => $role,
        "context_id" => $course->id,
        "context_label" => $course->shortname,
        "context_title" => $course->fullname,
        "launch_presentation_locale" => $locale,
    );

    $placementsecret = $typeconfig['servicesalt'];
    if ( isset($placementsecret) ) {
        $suffix = ':::' . $USER->id . ':::' . $instance->id;
        $plaintext = $placementsecret . $suffix;
        $hashsig = hash('sha256', $plaintext, false);
        $sourcedid = $hashsig . $suffix;
    }

    if ( isset($placementsecret) &&
         ( $typeconfig['acceptgrades'] == 1 ||
         ( $typeconfig['acceptgrades'] == 2 && $instance->instructorchoiceacceptgrades == 1 ) ) ) {
        $requestparams["lis_result_sourcedid"] = $sourcedid;
        $requestparams["ext_ims_lis_basic_outcome_url"] = $CFG->wwwroot.'/mod/lti/service.php';
    }

    if ( isset($placementsecret) &&
         ( $typeconfig['allowroster'] == 1 ||
         ( $typeconfig['allowroster'] == 2 && $instance->instructorchoiceallowroster == 1 ) ) ) {
        $requestparams["ext_ims_lis_memberships_id"] = $sourcedid;
        $requestparams["ext_ims_lis_memberships_url"] = $CFG->wwwroot.'/mod/lti/service.php';
    }

    // Send user's name and email data if appropriate
    if ( $typeconfig['sendname'] == 1 ||
         ( $typeconfig['sendname'] == 2 && $instance->instructorchoicesendname == 1 ) ) {
        $requestparams["lis_person_name_given"] =  $USER->firstname;
        $requestparams["lis_person_name_family"] =  $USER->lastname;
        $requestparams["lis_person_name_full"] =  $USER->firstname." ".$USER->lastname;
    }

    if ( $typeconfig['sendemailaddr'] == 1 ||
         ( $typeconfig['sendemailaddr'] == 2 && $instance->instructorchoicesendemailaddr == 1 ) ) {
        $requestparams["lis_person_contact_email_primary"] = $USER->email;
    }

    // Concatenate the custom parameters from the administrator and the instructor
    // Instructor parameters are only taken into consideration if the administrator
    // has giver permission
    $customstr = $typeconfig['customparameters'];
    $instructorcustomstr = $instance->instructorcustomparameters;
    $custom = array();
    $instructorcustom = array();
    if ($customstr) {
        $custom = split_custom_parameters($customstr);
    }
    if (!isset($typeconfig['allowinstructorcustom']) || $typeconfig['allowinstructorcustom'] == 0) {
        $requestparams = array_merge($custom, $requestparams);
    } else {
        if ($instructorcustomstr) {
            $instructorcustom = split_custom_parameters($instructorcustomstr);
        }
        foreach ($instructorcustom as $key => $val) {
            if (array_key_exists($key, $custom)) {
                // Ignore the instructor's parameter
            } else {
                $custom[$key] = $val;
            }
        }
        $requestparams = array_merge($custom, $requestparams);
    }

    return $requestparams;
}

/**
 * Splits the custom parameters field to the various parameters
 *
 * @param string $customstr     String containing the parameters
 *
 * @return Array of custom parameters
 */
function split_custom_parameters($customstr) {
    $textlib = textlib_get_instance();

    $lines = preg_split("/[\n;]/", $customstr);
    $retval = array();
    foreach ($lines as $line) {
        $pos = strpos($line, "=");
        if ( $pos === false || $pos < 1 ) {
            continue;
        }
        $key = trim($textlib->substr($line, 0, $pos));
        $val = trim($textlib->substr($line, $pos+1));
        $key = map_keyname($key);
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
function map_keyname($key) {
    $textlib = textlib_get_instance();

    $newkey = "";
    $key = $textlib->strtolower(trim($key));
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
 * Returns the IMS user role in a given context
 *
 * This function queries Moodle for an user role and
 * returns the correspondant IMS role
 *
 * @param StdClass $user          Moodle user instance
 * @param StdClass $context       Moodle context
 *
 * @return string                 IMS Role
 *
 */
function lti_get_ims_role($user, $context) {

    $roles = get_user_roles($context, $user->id);
    $rolesname = array();
    foreach ($roles as $role) {
        $rolesname[] = $role->shortname;
    }

    if (in_array('admin', $rolesname) || in_array('coursecreator', $rolesname)) {
        return get_string('imsroleadmin', 'lti');
    }

    if (in_array('editingteacher', $rolesname) || in_array('teacher', $rolesname)) {
        return get_string('imsroleinstructor', 'lti');
    }

    return get_string('imsrolelearner', 'lti');
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

    $typeconfig = array();
    $configs = $DB->get_records('lti_types_config', array('typeid' => $typeid));
    if (!empty($configs)) {
        foreach ($configs as $config) {
            $typeconfig[$config->name] = $config->value;
        }
    }
    return $typeconfig;
}

function lti_get_tools_by_url($url, $state){
    $domain = lti_get_domain_from_url($url);
    
    return lti_get_tools_by_domain($domain, $state);
}

function lti_get_tools_by_domain($domain, $state = null, $courseid = null){
    global $DB, $SITE;
    
    $filters = array('tooldomain' => $domain);
    
    $statefilter = '';
    $coursefilter = '';
    
    if($state){
        $statefilter = 'AND state = :state';
    }
  
    if($courseid && $courseid != $SITE->id){
        $coursefilter = 'OR course = :courseid';
    }
    
    $query = <<<QUERY
        SELECT * FROM {lti_types}
        WHERE
            tooldomain = :tooldomain
        AND (course = :siteid $coursefilter)
        $statefilter
QUERY;
    
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
function lti_filter_get_types() {
    global $DB;

    return $DB->get_records('lti_types');
}

function lti_get_types_for_add_instance(){
    global $DB;
    $admintypes = $DB->get_records('lti_types', array('coursevisible' => 1));
    
    $types = array();
    $types[0] = get_string('automatic', 'lti');
    
    foreach($admintypes as $type) {
        $types[$type->id] = $type->name;
    }
    
    return $types;
}

function lti_get_domain_from_url($url){
    $matches = array();
    
    if(preg_match(LTI_URL_DOMAIN_REGEX, $url, $matches)){
        return $matches[1];
    }
}

function lti_get_tool_by_url_match($url, $courseid = null, $state = LTI_TOOL_STATE_CONFIGURED){
    $possibletools = lti_get_tools_by_url($url, $state, $courseid);
    
    return lti_get_best_tool_by_url($url, $possibletools);
}

function lti_get_url_thumbprint($url){
    $urlparts = parse_url(strtolower($url));
    if(!isset($urlparts['path'])){
        $urlparts['path'] = '';
    }
    
    if(substr($urlparts['host'], 0, 3) === 'www'){
        $urllparts['host'] = substr(3);
    }
    
    return $urllower = $urlparts['host'] . '/' . $urlparts['path'];
}

function lti_get_best_tool_by_url($url, $tools){
    if(count($tools) === 0){
        return null;
    }
    
    $urllower = lti_get_url_thumbprint($url);
    
    foreach($tools as $tool){
        $tool->_matchscore = 0;
         
        $toolbaseurllower = lti_get_url_thumbprint($tool->baseurl);
        
        if($urllower === $toolbaseurllower){
            //100 points for exact match
            $tool->_matchscore += 100;
        } else if(substr($urllower, 0, strlen($toolbaseurllower)) === $toolbaseurllower){
            //50 points if it starts with the base URL
            $tool->_matchscore += 50;
        }
    }
    
    $bestmatch = array_reduce($tools, function($value, $tool){
        if($tool->_matchscore > $value->_matchscore){
            return $tool;
        } else {
            return $value;
        }
        
    }, (object)array('_matchscore' => -1));
    
    //None of the tools are suitable for this URL
    if($bestmatch->_matchscore <= 0){
        return null;
    }
    
    return $bestmatch;
}

/**
 * Prints the various configured tool types
 *
 */
function lti_filter_print_types() {
    global $CFG;

    $types = lti_filter_get_types();
    if (!empty($types)) {
        echo '<ul>';
        foreach ($types as $type) {
            echo '<li>'.
            $type->name.
            '<span class="commands">'.
            '<a class="editing_update" href="typessettings.php?action=update&amp;id='.$type->id.'&amp;sesskey='.sesskey().'" title="Update">'.
            '<img class="iconsmall" alt="Update" src="'.$CFG->wwwroot.'/pix/t/edit.gif"/>'.
            '</a>'.
            '<a class="editing_delete" href="typessettings.php?action=delete&amp;id='.$type->id.'&amp;sesskey='.sesskey().'" title="Delete">'.
            '<img class="iconsmall" alt="Delete" src="'.$CFG->wwwroot.'/pix/t/delete.gif"/>'.
            '</a>'.
            '</span>'.
            '</li>';

        }
        echo '</ul>';
    } else {
        echo '<div class="message">';
        echo get_string('notypes', 'lti');
        echo '</div>';
    }
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

function lti_set_state_for_type($id, $state){
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

    $type->lti_typename = $basicltitype->name;
    
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
    if (isset($config['instructorchoicesendname'])){
        $type->lti_instructorchoicesendname = $config['instructorchoicesendname'];
    }
    if (isset($config['sendemailaddr'])){
        $type->lti_sendemailaddr = $config['sendemailaddr'];
    }
    if (isset($config['instructorchoicesendemailaddr'])){
        $type->lti_instructorchoicesendemailaddr = $config['instructorchoicesendemailaddr'];
    }
    if (isset($config['acceptgrades'])){
        $type->lti_acceptgrades = $config['acceptgrades'];
    }
    if (isset($config['instructorchoiceacceptgrades'])){
        $type->lti_instructorchoiceacceptgrades = $config['instructorchoiceacceptgrades'];
    }
    if (isset($config['allowroster'])){
        $type->lti_allowroster = $config['allowroster'];
    }
    if (isset($config['instructorchoiceallowroster'])){
        $type->lti_instructorchoiceallowroster = $config['instructorchoiceallowroster'];
    }

    if (isset($config['customparameters'])) {
        $type->lti_customparameters = $config['customparameters'];
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

function lti_prepare_type_for_save($type, $config){
    $type->baseurl = $config->lti_toolurl;
    $type->tooldomain = lti_get_domain_from_url($config->lti_toolurl);
    $type->name = $config->lti_typename;
    
    $type->coursevisible = !empty($config->lti_coursevisible) ? $config->lti_coursevisible : 0;
    $config->lti_coursevisible = $type->coursevisible;
    
    $type->timemodified = time();
    
    unset ($config->lti_typename);
    unset ($config->lti_toolurl);
}

function lti_update_type($type, $config){
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

function lti_add_type($type, $config){
    global $USER, $SITE, $DB;
    
    lti_prepare_type_for_save($type, $config);
    
    if(!isset($type->state)){
        $type->state = LTI_TOOL_STATE_PENDING;
    }
    
    if(!isset($type->timecreated)){
        $type->timecreated = time();
    }
    
    if(!isset($type->createdby)){
        $type->createdby = $USER->id;
    }
    
    if(!isset($type->course)){
        $type->course = $SITE->id;
    }
    
    //Create a salt value to be used for signing passed data to extension services
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
function sign_parameters($oldparms, $endpoint, $method, $oauthconsumerkey, $oauthconsumersecret, $submittext, $orgid /*, $orgdesc*/) {
    global $lastbasestring;
    $parms = $oldparms;
    $parms["lti_version"] = "LTI-1p0";
    $parms["lti_message_type"] = "basic-lti-launch-request";
    if ( $orgid ) {
        $parms["tool_consumer_instance_guid"] = $orgid;
    }
    /* Suppress this for now - Chuck
    if ( $orgdesc ) $parms["tool_consumer_instance_description"] = $orgdesc;
    */
    $parms["ext_submit"] = $submittext;

    $testtoken = '';

    $hmacmethod = new OAuthSignatureMethod_HMAC_SHA1();
    $testconsumer = new OAuthConsumer($oauthconsumerkey, $oauthconsumersecret, null);

    $accreq = OAuthRequest::from_consumer_and_token($testconsumer, $testtoken, $method, $endpoint, $parms);
    $accreq->sign_request($hmacmethod, $testconsumer, $testtoken);

    // Pass this back up "out of band" for debugging
    $lastbasestring = $accreq->get_signature_base_string();

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
function post_launch_html($newparms, $endpoint, $debug=false) {
    global $lastbasestring;
    
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
        $r .= "    if(ele.style.display == \"block\") {\n";
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
        $r .= "<p><b>".get_string("basiclti_base_string", "lti")."</b><br/>\n".$lastbasestring."</p>\n";
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

/**
 * Returns a link with info about the state of the basiclti submissions
 *
 * This is used by view_header to put this link at the top right of the page.
 * For teachers it gives the number of submitted assignments with a link
 * For students it gives the time of their submission.
 * This will be suitable for most assignment types.
 *
 * @global object
 * @global object
 * @param bool $allgroup print all groups info if user can access all groups, suitable for index.php
 * @return string
 */
function submittedlink($cm, $allgroups=false) {
    global $CFG;

    $submitted = '';
    $urlbase = "{$CFG->wwwroot}/mod/lti/";

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (has_capability('mod/lti:grade', $context)) {
        if ($allgroups and has_capability('moodle/site:accessallgroups', $context)) {
            $group = 0;
        } else {
            $group = groups_get_activity_group($cm);
        }

        $submitted = '<a href="'.$urlbase.'submissions.php?id='.$cm->id.'">'.
                     get_string('viewsubmissions', 'lti').'</a>';
    } else {
        if (isloggedin()) {
            // TODO Insert code for students if needed
        }
    }

    return $submitted;
}

