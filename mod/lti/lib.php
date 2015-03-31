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
 * This file contains a library of functions and constants for the lti module
 *
 * @package mod_lti
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

/**
 * Returns all other caps used in module.
 *
 * @return array
 */
function lti_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * List of features supported in URL module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function lti_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted basiclti record
 **/
function lti_add_instance($lti, $mform) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    if (!isset($lti->toolurl)) {
        $lti->toolurl = '';
    }

    $lti->timecreated = time();
    $lti->timemodified = $lti->timecreated;
    $lti->servicesalt = uniqid('', true);

    lti_force_type_config_settings($lti, lti_get_type_config_by_instance($lti));

    if (empty($lti->typeid) && isset($lti->urlmatchedtypeid)) {
        $lti->typeid = $lti->urlmatchedtypeid;
    }

    if (!isset($lti->instructorchoiceacceptgrades) || $lti->instructorchoiceacceptgrades != LTI_SETTING_ALWAYS) {
        // The instance does not accept grades back from the provider, so set to "No grade" value 0.
        $lti->grade = 0;
    }

    $lti->id = $DB->insert_record('lti', $lti);

    if (isset($lti->instructorchoiceacceptgrades) && $lti->instructorchoiceacceptgrades == LTI_SETTING_ALWAYS) {
        if (!isset($lti->cmidnumber)) {
            $lti->cmidnumber = '';
        }

        lti_grade_item_update($lti);
    }

    return $lti->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function lti_update_instance($lti, $mform) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    $lti->timemodified = time();
    $lti->id = $lti->instance;

    if (!isset($lti->showtitlelaunch)) {
        $lti->showtitlelaunch = 0;
    }

    if (!isset($lti->showdescriptionlaunch)) {
        $lti->showdescriptionlaunch = 0;
    }

    lti_force_type_config_settings($lti, lti_get_type_config_by_instance($lti));

    if (isset($lti->instructorchoiceacceptgrades) && $lti->instructorchoiceacceptgrades == LTI_SETTING_ALWAYS) {
        lti_grade_item_update($lti);
    } else {
        // Instance is no longer accepting grades from Provider, set grade to "No grade" value 0.
        $lti->grade = 0;
        $lti->instructorchoiceacceptgrades = 0;

        lti_grade_item_delete($lti);
    }

    if ($lti->typeid == 0 && isset($lti->urlmatchedtypeid)) {
        $lti->typeid = $lti->urlmatchedtypeid;
    }

    return $DB->update_record('lti', $lti);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function lti_delete_instance($id) {
    global $DB;

    if (! $basiclti = $DB->get_record("lti", array("id" => $id))) {
        return false;
    }

    $result = true;

    // Delete any dependent records here.
    lti_grade_item_delete($basiclti);

    $ltitype = $DB->get_record('lti_types', array('id' => $basiclti->typeid));
    $DB->delete_records('lti_tool_settings',
        array('toolproxyid' => $ltitype->toolproxyid, 'course' => $basiclti->course, 'coursemoduleid' => $id));

    return $DB->delete_records("lti", array("id" => $basiclti->id));
}

function lti_get_types() {
    global $OUTPUT;

    $subtypes = array();
    foreach (get_plugin_list('ltisource') as $name => $dir) {
        if ($moretypes = component_callback("ltisource_$name", 'get_types')) {
            $subtypes = array_merge($subtypes, $moretypes);
        }
    }
    if (empty($subtypes)) {
        return MOD_SUBTYPE_NO_CHILDREN;
    }

    $types = array();

    $type           = new stdClass();
    $type->modclass = MOD_CLASS_ACTIVITY;
    $type->type     = 'lti_group_start';
    $type->typestr  = '--'.get_string('modulenameplural', 'mod_lti');
    $types[]        = $type;

    $link     = get_string('modulename_link', 'mod_lti');
    $linktext = get_string('morehelp');
    $help     = get_string('modulename_help', 'mod_lti');
    $help    .= html_writer::tag('div', $OUTPUT->doc_link($link, $linktext, true), array('class' => 'helpdoclink'));

    $type           = new stdClass();
    $type->modclass = MOD_CLASS_ACTIVITY;
    $type->type     = '';
    $type->typestr  = get_string('generaltool', 'mod_lti');
    $type->help     = $help;
    $types[]        = $type;

    $types = array_merge($types, $subtypes);

    $type           = new stdClass();
    $type->modclass = MOD_CLASS_ACTIVITY;
    $type->type     = 'lti_group_end';
    $type->typestr  = '--';
    $types[]        = $type;

    return $types;
}

/**
 * Given a coursemodule object, this function returns the extra
 * information needed to print this activity in various places.
 * For this module we just need to support external urls as
 * activity icons
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info info
 */
function lti_get_coursemodule_info($coursemodule) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    if (!$lti = $DB->get_record('lti', array('id' => $coursemodule->instance),
            'icon, secureicon, intro, introformat, name, toolurl, launchcontainer')) {
        return null;
    }

    $info = new cached_cm_info();

    // We want to use the right icon based on whether the
    // current page is being requested over http or https.
    if (lti_request_is_using_ssl() && !empty($lti->secureicon)) {
        $info->iconurl = new moodle_url($lti->secureicon);
    } else if (!empty($lti->icon)) {
        $info->iconurl = new moodle_url($lti->icon);
    }

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('lti', $lti, $coursemodule->id, false);
    }

    // Does the link open in a new window?
    $tool = lti_get_tool_by_url_match($lti->toolurl);
    if ($tool) {
        $toolconfig = lti_get_type_config($tool->id);
    } else {
        $toolconfig = array();
    }
    $launchcontainer = lti_get_launch_container($lti, $toolconfig);
    if ($launchcontainer == LTI_LAUNCH_CONTAINER_WINDOW) {
        $launchurl = new moodle_url('/mod/lti/launch.php', array('id' => $coursemodule->id));
        $info->onclick = "window.open('" . $launchurl->out(false) . "', 'lti'); return false;";
    }

    $info->name = $lti->name;

    return $info;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @TODO: implement this moodle function (if needed)
 **/
function lti_user_outline($course, $user, $mod, $basiclti) {
    return null;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @TODO: implement this moodle function (if needed)
 **/
function lti_user_complete($course, $user, $mod, $basiclti) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in basiclti activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @uses $CFG
 * @return boolean
 * @TODO: implement this moodle function
 **/
function lti_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false.
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @uses $CFG
 * @return boolean
 **/
function lti_cron () {
    return true;
}

/**
 * Must return an array of grades for a given instance of this module,
 * indexed by user.  It also returns a maximum allowed grade.
 *
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $basicltiid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 *
 * @TODO: implement this moodle function (if needed)
 **/
function lti_grades($basicltiid) {
    return null;
}

/**
 * This function returns if a scale is being used by one basiclti
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $basicltiid ID of an instance of this module
 * @return mixed
 *
 * @TODO: implement this moodle function (if needed)
 **/
function lti_scale_used ($basicltiid, $scaleid) {
    $return = false;

    // $rec = get_record("basiclti","id","$basicltiid","scale","-$scaleid");
    //
    // if (!empty($rec)  && !empty($scaleid)) {
    //     $return = true;
    // }

    return $return;
}

/**
 * Checks if scale is being used by any instance of basiclti.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any basiclti
 *
 */
function lti_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('lti', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function lti_install() {
     return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function lti_uninstall() {
    return true;
}

/**
 * Returns available Basic LTI types
 *
 * @return array of basicLTI types
 */
function lti_get_lti_types() {
    global $DB;

    return $DB->get_records('lti_types');
}

/**
 * Create grade item for given basiclti
 *
 * @category grade
 * @param object $basiclti object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function lti_grade_item_update($basiclti, $grades = null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $params = array('itemname' => $basiclti->name, 'idnumber' => $basiclti->cmidnumber);

    if ($basiclti->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $basiclti->grade;
        $params['grademin']  = 0;

    } else if ($basiclti->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$basiclti->grade;

    } else {
        $params['gradetype'] = GRADE_TYPE_TEXT; // Allow text comments only.
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/lti', $basiclti->course, 'mod', 'lti', $basiclti->id, 0, $grades, $params);
}

/**
 * Delete grade item for given basiclti
 *
 * @category grade
 * @param object $basiclti object
 * @return object basiclti
 */
function lti_grade_item_delete($basiclti) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/lti', $basiclti->course, 'mod', 'lti', $basiclti->id, 0, null, array('deleted' => 1));
}

function lti_extend_settings_navigation($settings, $parentnode) {
    global $PAGE;

    if (has_capability('mod/lti:manage', context_module::instance($PAGE->cm->id))) {
        $keys = $parentnode->get_children_key_list();

        $node = navigation_node::create('Submissions',
            new moodle_url('/mod/lti/grade.php', array('id' => $PAGE->cm->id)),
            navigation_node::TYPE_SETTING, null, 'mod_lti_submissions');

        $parentnode->add_node($node, $keys[1]);
    }
}

/**
 * Log post actions
 *
 * @return array
 */
function lti_get_post_actions() {
    return array();
}

/**
 * Log view actions
 *
 * @return array
 */
function lti_get_view_actions() {
    return array('view all', 'view');
}
