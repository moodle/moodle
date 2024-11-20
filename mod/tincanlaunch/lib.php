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
 * Library of interface functions and constants for module tincanlaunch
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the tincanlaunch specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// TinCanPHP - required for interacting with the LRS in tincanlaunch_get_statements.
require_once($CFG->dirroot . '/mod/tincanlaunch/tincanphp/autoload.php');

// SCORM library from the SCORM module. Required for its xml2Array class by tincanlaunch_process_new_package.
require_once($CFG->dirroot . '/mod/scorm/datamodels/scormlib.php');

global $tincanlaunchsettings;
$tincanlaunchsettings = null;

// Moodle Core API.

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function tincanlaunch_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the tincanlaunch into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $tincanlaunch An object from the form in mod_form.php
 * @param object $mform
 * @return int The id of the newly inserted tincanlaunch record
 */
function tincanlaunch_add_instance($tincanlaunch, $mform=null) {
    global $DB;

    $tincanlaunch->timecreated = time();

    // Need the id of the newly created instance to return (and use if override defaults checkbox is checked).
    $tincanlaunch->id = $DB->insert_record('tincanlaunch', $tincanlaunch);

    $tincanlaunchlrs = tincanlaunch_build_lrs_settings($tincanlaunch);

    // Determine if override defaults checkbox is checked or we need to save watershed creds.
    if ($tincanlaunch->overridedefaults == '1' || $tincanlaunchlrs->lrsauthentication == '2') {
        $tincanlaunchlrs->tincanlaunchid = $tincanlaunch->id;

        // Insert data into tincanlaunch_lrs table.
        if (!$DB->insert_record('tincanlaunch_lrs', $tincanlaunchlrs)) {
            return false;
        }
    }

    // Process uploaded file.
    if (!empty($tincanlaunch->packagefile)) {
        tincanlaunch_process_new_package($tincanlaunch);
    }

    return $tincanlaunch->id;
}

/**
 * Updates an instance of the tincanlaunch in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $tincanlaunch An object from the form in mod_form.php
 * @param object $mform
 * @return boolean Success/Fail
 */
function tincanlaunch_update_instance($tincanlaunch, $mform = null) {
    global $DB;

    $tincanlaunch->timemodified = time();
    $tincanlaunch->id = $tincanlaunch->instance;

    $tincanlaunchlrs = tincanlaunch_build_lrs_settings($tincanlaunch);

    // Determine if override defaults checkbox is checked.
    if ($tincanlaunch->overridedefaults == '1') {
        // Check to see if there is a record of this instance in the table.
        $tincanlaunchlrsid = $DB->get_field(
            'tincanlaunch_lrs',
            'id',
            array('tincanlaunchid' => $tincanlaunch->instance),
            IGNORE_MISSING
        );
        // If not, will need to insert_record.
        if (!$tincanlaunchlrsid) {
            if (!$DB->insert_record('tincanlaunch_lrs', $tincanlaunchlrs)) {
                return false;
            }
        } else { // If it does exist, update it.
            $tincanlaunchlrs->id = $tincanlaunchlrsid;

            if (!$DB->update_record('tincanlaunch_lrs', $tincanlaunchlrs)) {
                return false;
            }
        }
    }

    if (!$DB->update_record('tincanlaunch', $tincanlaunch)) {
        return false;
    }

    // Process uploaded file.
    if (!empty($tincanlaunch->packagefile)) {
        tincanlaunch_process_new_package($tincanlaunch);
    }

    return true;
}

/**
 * Add a get_coursemodule_info function in case any tincanlaunch type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses will know about (most noticeably, an icon).
 */
function tincanlaunch_get_coursemodule_info($coursemodule) {
    global $DB;

    $dbparams = ['id' => $coursemodule->instance];
    $fields = 'id, course, name, intro, introformat, tincanlaunchurl, tincanactivityid, tincanverbid, tincanexpiry,
        overridedefaults, tincanmultipleregs, tincansimplelaunchnav, timecreated, timemodified';

    if (!$tincanlaunch = $DB->get_record('tincanlaunch', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $tincanlaunch->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $result->content = format_module_intro('tincanlaunch', $tincanlaunch, $coursemodule->id, false);
    }

    // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['tincancompletionverb'] = $tincanlaunch->tincanverbid;
        $result->customdata['customcompletionrules']['tincancompletioexpiry'] = $tincanlaunch->tincanexpiry;
    }

    return $result;
}

/**
 * Builds the tincanlaunch_lrs object.
 *
 * @param stdClass $tincanlaunch The tincanlaunch object (record).
 * @return stdClass An object with the LRS settings.
 */
function tincanlaunch_build_lrs_settings(stdClass $tincanlaunch) {

    // Data for tincanlaunch_lrs table.
    $tincanlaunchlrs = new stdClass();
    $tincanlaunchlrs->lrsendpoint = $tincanlaunch->tincanlaunchlrsendpoint;
    $tincanlaunchlrs->lrsauthentication = $tincanlaunch->tincanlaunchlrsauthentication;
    $tincanlaunchlrs->customacchp = $tincanlaunch->tincanlaunchcustomacchp;
    $tincanlaunchlrs->useactoremail = $tincanlaunch->tincanlaunchuseactoremail;
    $tincanlaunchlrs->lrsduration = $tincanlaunch->tincanlaunchlrsduration;
    $tincanlaunchlrs->tincanlaunchid = $tincanlaunch->instance;
    $tincanlaunchlrs->lrslogin = $tincanlaunch->tincanlaunchlrslogin;
    $tincanlaunchlrs->lrspass = $tincanlaunch->tincanlaunchlrspass;

    return $tincanlaunchlrs;
}

/**
 * Removes an instance of the tincanlaunch from the database
 *
 * Given an ID of an instance of this module, this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function tincanlaunch_delete_instance($id) {
    global $DB;

    if (! $tincanlaunch = $DB->get_record('tincanlaunch', array('id' => $id))) {
        return false;
    }

    // Determine if there is a record of this (ever) in the tincanlaunch_lrs table.
    $strictness = IGNORE_MISSING;
    $tincanlaunchlrsid = $DB->get_field('tincanlaunch_lrs', 'id', array('tincanlaunchid' => $id), $strictness);
    if ($tincanlaunchlrsid) {
        // If there is, delete it.
        $DB->delete_records('tincanlaunch_lrs', array('id' => $tincanlaunchlrsid));
    }

    $DB->delete_records('tincanlaunch', array('id' => $tincanlaunch->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function tincanlaunch_user_outline() {
    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in tincanlaunch activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function tincanlaunch_print_recent_activity() {
    return false;  // True if anything was printed, otherwise false.
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 **/
function tincanlaunch_cron() {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @return array
 */
function tincanlaunch_get_extra_capabilities() {
    return array();
}

// File API.

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function tincanlaunch_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('areacontent', 'scorm');
    $areas['package'] = get_string('areapackage', 'scorm');
    return $areas;
}

/**
 * File browsing support for tincanlaunch file areas
 *
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $context
 * @param string $filearea
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function tincanlaunch_get_file_info($browser, $areas, $context, $filearea, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'package') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_tincanlaunch', 'package', 0, $filepath, $filename)) {
            if ($filepath === '/' && $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_tincanlaunch', 'package', 0);
            } else {
                // Not found.
                return null;
            }
        }
        return new file_info_stored($browser, $context, $storedfile, $urlbase, $areas[$filearea], false, true, false, false);
    }

    return false;
}

/**
 * Serves Tin Can content, introduction images and packages. Implements needed access control ;-)
 *
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function tincanlaunch_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    $filename = array_pop($args);
    $filepath = implode('/', $args);
    if ($filearea === 'content') {
        $lifetime = null;
    } else if ($filearea === 'package') {
        $lifetime = 0; // No caching here.
    } else {
        return false;
    }

    $fs = get_file_storage();
    $storedfile = $fs->get_file($context->id, 'mod_tincanlaunch', $filearea, 0, '/'.$filepath.'/', $filename);

    if (!$storedfile || $storedfile->is_directory()) {
        if ($filearea === 'content') { // Return file not found straight away to improve performance.
            send_header_404();
            die;
        }
        return false;
    }

    // Finally send the file.
    send_stored_file($storedfile, $lifetime, 0, false, $options);
}

/**
 * Export file resource contents for web service access.
 *
 * @param cm_info $cm Course module object.
 * @param string $baseurl Base URL for Moodle.
 * @return array array of file content
 */
function tincanlaunch_export_contents($cm, $baseurl) {
    $contents = array();
    $context = context_module::instance($cm->id);

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_tincanlaunch', 'package', 0, 'sortorder DESC, id ASC', false);

    foreach ($files as $fileinfo) {
        $file = array();
        $file['type'] = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $fileurl = new moodle_url(
            $baseurl . '/'.$context->id.'/mod_tincanlaunch/package'. $fileinfo->get_filepath().$fileinfo->get_filename());
        $file['fileurl']      = $fileurl;
        $file['timecreated']  = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder']    = $fileinfo->get_sortorder();
        $file['userid']       = $fileinfo->get_userid();
        $file['author']       = $fileinfo->get_author();
        $file['license']      = $fileinfo->get_license();
        $contents[] = $file;
    }

    return $contents;
}

// TinCanLaunch specific functions.

/*
The functions below should really be in locallib, however they are required for one
or more of the functions above so need to be here.
It looks like the standard Quiz module does that same thing, so I don't feel so bad.
*/

/**
 * Handles uploaded zip packages when a module is added or updated. Unpacks the zip contents
 * and extracts the launch url and activity id from the tincan.xml file.
 * Note: This takes the *first* activity from the tincan.xml file to be the activity intended
 * to be launched. It will not go hunting for launch URLs any activities listed below.
 * Based closely on code from the SCORM and (to a lesser extent) Resource modules.
 *
 * @param object $tincanlaunch An object from the form in mod_form.php
 * @return array empty if no issue is found. Array of error message otherwise
 */
function tincanlaunch_process_new_package($tincanlaunch) {
    global $DB, $CFG;

    $cmid = $tincanlaunch->coursemodule;
    $context = context_module::instance($cmid);

    // Reload TinCan instance.
    $record = $DB->get_record('tincanlaunch', array('id' => $tincanlaunch->id));

    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_tincanlaunch', 'package');
    file_save_draft_area_files(
        $tincanlaunch->packagefile,
        $context->id,
        'mod_tincanlaunch',
        'package',
        0,
        array('subdirs' => 0, 'maxfiles' => 1)
    );

    // Get filename of zip that was uploaded.
    $files = $fs->get_area_files($context->id, 'mod_tincanlaunch', 'package', 0, '', false);
    if (count($files) < 1) {
        return false;
    }

    $zipfile = reset($files);
    $zipfilename = $zipfile->get_filename();

    $packagefile = false;

    $packagefile = $fs->get_file($context->id, 'mod_tincanlaunch', 'package', 0, '/', $zipfilename);

    $fs->delete_area_files($context->id, 'mod_tincanlaunch', 'content');

    $packer = get_file_packer('application/zip');
    $packagefile->extract_to_storage($packer, $context->id, 'mod_tincanlaunch', 'content', 0, '/');

    // If the tincan.xml file isn't there, don't do try to use it.
    // This is unlikely as it should have been checked when the file was validated.
    if ($manifestfile = $fs->get_file($context->id, 'mod_tincanlaunch', 'content', 0, '/', 'tincan.xml')) {
        $xmltext = $manifestfile->get_content();

        $pattern = '/&(?!\w{2,6};)/';
        $replacement = '&amp;';
        $xmltext = preg_replace($pattern, $replacement, $xmltext);

        $objxml = new xml2Array();
        $manifest = $objxml->parse($xmltext);

        // Update activity id from the first activity in tincan.xml, if it is found.
        // Skip without error if not. (The Moodle admin will need to enter the id manually).
        if (isset($manifest[0]["children"][0]["children"][0]["attrs"]["ID"])) {
            $record->tincanactivityid = $manifest[0]["children"][0]["children"][0]["attrs"]["ID"];
        }

        // Update launch from the first activity in tincan.xml, if it is found.
        // Skip if not. (The Moodle admin will need to enter the url manually).
        foreach ($manifest[0]["children"][0]["children"][0]["children"] as $property) {
            if ($property["name"] === "LAUNCH") {
                $record->tincanlaunchurl = $CFG->wwwroot."/pluginfile.php/".$context->id."/mod_tincanlaunch/"
                .$manifestfile->get_filearea()."/".$property["tagData"];
            }
        }
    }
    // Save reference.
    return $DB->update_record('tincanlaunch', $record);
}

/**
 * Check that a Zip file contains a tincan.xml file in the right place. Used in mod_form.php.
 * Heavily based on scorm_validate_package in /mod/scorm/lib.php
 *
 * @param stored_file $file a Zip file.
 * @return array empty if no issue is found. Array of error message otherwise.
 */
function tincanlaunch_validate_package($file) {
    $packer = get_file_packer('application/zip');
    $errors = array();
    $filelist = $file->list_files($packer);
    if (!is_array($filelist)) {
        $errors['packagefile'] = get_string('badarchive', 'tincanlaunch');
    } else {
        $badmanifestpresent = false;
        foreach ($filelist as $info) {
            if ($info->pathname == 'tincan.xml') {
                return array();
            } else if (strpos($info->pathname, 'tincan.xml') !== false) {
                // This package has tincan xml file inside a folder of the package.
                $badmanifestpresent = true;
            }
            if (preg_match('/\.cst$/', $info->pathname)) {
                return array();
            }
        }
        if ($badmanifestpresent) {
            $errors['packagefile'] = get_string('badimsmanifestlocation', 'tincanlaunch');
        } else {
            $errors['packagefile'] = get_string('nomanifest', 'tincanlaunch');
        }
    }
    return $errors;
}

/**
 * Fetches Statements from the LRS. This is used for completion tracking -
 * we check for a statement matching certain criteria for each learner.
 *
 * @param string $url LRS endpoint URL
 * @param string $basiclogin login/key for the LRS
 * @param string $basicpass pass/secret for the LRS
 * @param string $version version of xAPI to use
 * @param string $activityid Activity Id to filter by
 * @param TinCan $agent Aagent Agent to filter by
 * @param string $verb Verb Id to filter by
 * @param string $since Since date to filter by
 * @return TinCan LRS Response
 */
function tincanlaunch_get_statements($url, $basiclogin, $basicpass, $version, $activityid, $agent, $verb, $since = null) {

    $lrs = new \TinCan\RemoteLRS($url, $version, $basiclogin, $basicpass);

    $statementsquery = array(
        "agent" => $agent,
        "verb" => new \TinCan\Verb(array("id" => trim($verb))),
        "activity" => new \TinCan\Activity(array("id" => trim($activityid))),
        "related_activities" => "false",
        "format" => "ids"
    );

    if (!is_null($since)) {
        $statementsquery["since"] = $since;
    }

    // Get all the statements from the LRS.
    $statementsresponse = $lrs->queryStatements($statementsquery);

    if ($statementsresponse->success == false) {
        return $statementsresponse;
    }

    $allthestatements = $statementsresponse->content->getStatements();
    $morestatementsurl = $statementsresponse->content->getMore();
    while (!empty($morestatementsurl)) {
        $morestmtsresponse = $lrs->moreStatements($morestatementsurl);
        if ($morestmtsresponse->success == false) {
            return $morestmtsresponse;
        }
        $morestatements = $morestmtsresponse->content->getStatements();
        $morestatementsurl = $morestmtsresponse->content->getMore();
        // Note: due to the structure of the arrays, array_merge does not work as expected.
        foreach ($morestatements as $morestatement) {
            array_push($allthestatements, $morestatement);
        }
    }

    return new \TinCan\LRSResponse(
        $statementsresponse->success,
        $allthestatements,
        $statementsresponse->httpResponse
    );
}

/**
 * Build a TinCan Agent based on the current user.
 *
 * @param object $instance tincanlaunch instance
 * @param object $user User object
 * @return TinCan $agent Agent
 */
function tincanlaunch_getactor($instance, $user = false) {
    global $USER, $CFG;

    // If Moodle cron didn't initiate this, user global $USER.
    if ($user == false) {
        $user = $USER;
    }

    $settings = tincanlaunch_settings($instance);

    if ($user->idnumber && $settings['tincanlaunchcustomacchp']) {
        $agent = array(
            "name" => fullname($user),
            "account" => array(
                "homePage" => $settings['tincanlaunchcustomacchp'],
                "name" => $user->idnumber
            ),
            "objectType" => "Agent"
        );
    } else if ($user->email && $settings['tincanlaunchuseactoremail']) {
        $agent = array(
            "name" => fullname($user),
            "mbox" => "mailto:".$user->email,
            "objectType" => "Agent"
        );
    } else {
        $agent = array(
            "name" => fullname($user),
            "account" => array(
                "homePage" => $CFG->wwwroot,
                "name" => $user->username
            ),
            "objectType" => "Agent"
        );
    }

    return new \TinCan\Agent($agent);
}


/**
 * Returns the LRS settings relating to a Tin Can Launch module instance
 *
 * @param string $instance The Moodle id for the Tin Can module instance.
 * @return array LRS settings to use
 */
function tincanlaunch_settings($instance) {
    global $DB, $tincanlaunchsettings;

    if (!is_null($tincanlaunchsettings)) {
        return $tincanlaunchsettings;
    }

    $expresult = array();
    $conditions = array('tincanlaunchid' => $instance);
    $fields = '*';
    $strictness = 'IGNORE_MISSING';
    $activitysettings = $DB->get_record('tincanlaunch_lrs', $conditions, $fields, $strictness);

    // If global settings are not used, retrieve activity settings.
    if (!use_global_lrs_settings($instance)) {
        $expresult['tincanlaunchlrsendpoint'] = $activitysettings->lrsendpoint;
        $expresult['tincanlaunchlrsauthentication'] = $activitysettings->lrsauthentication;
        $expresult['tincanlaunchlrslogin'] = $activitysettings->lrslogin;
        $expresult['tincanlaunchlrspass'] = $activitysettings->lrspass;
        $expresult['tincanlaunchcustomacchp'] = $activitysettings->customacchp;
        $expresult['tincanlaunchuseactoremail'] = $activitysettings->useactoremail;
        $expresult['tincanlaunchlrsduration'] = $activitysettings->lrsduration;
    } else { // Use global lrs settings.
        $result = $DB->get_records('config_plugins', array('plugin' => 'tincanlaunch'));
        foreach ($result as $value) {
            $expresult[$value->name] = $value->value;
        }
    }

    $expresult['tincanlaunchlrsversion'] = '1.0.0';

    $tincanlaunchsettings = $expresult;
    return $expresult;
}

/**
 * Should the global LRS settings be used instead of the instance specific ones?
 *
 * @param string $instance The Moodle id for the Tin Can module instance.
 * @return bool
 */
function use_global_lrs_settings($instance) {
    global $DB;
    // Determine if there is a row in tincanlaunch_lrs matching the current activity id.
    $activitysettings = $DB->get_record('tincanlaunch', array('id' => $instance));
    if ($activitysettings->overridedefaults == 1) {
        return false;
    }
    return true;
}
