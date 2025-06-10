<?php
/**
 * 
 *
 * @package    mod
 * @subpackage mylabmastering
 * @copyright  
 * @author     
 * @license    
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in URL module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function mylabmastering_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        case FEATURE_MOD_INTRO:               return false;

        default: return null;
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
function mylabmastering_add_instance($mm, $mform) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/lti/locallib.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/blocks/mylabmastering/locallib.php');
    require_once($CFG->dirroot.'/lib/modinfolib.php');
    
    $mm_c = $mm->mmcourseid;
    $mm_s = $mm->mmsection;
    $launchpresentation = 4;
    if (isset($mm->inframe)) {
    	$launchpresentation = 2;
    }

    $templatelink = $DB->get_record('lti_types', array('id' => $mm->selectedlink), '*', MUST_EXIST);
    	
	$ltilink = new stdClass;
	$ltilink->course = $templatelink->course;
	
	$linkname = '';
	if (isset($mm->linktitle) && trim($mm->linktitle)!=='') {
		$linkname = $mm->linktitle;
	}
	else {
		$linkname = $templatelink->name;
	}	
	
	$ltilink->name = $linkname;
	
	$ltilink->timecreated = time();
    $ltilink->timemodified = $ltilink->timecreated;
    $ltilink->typeid = 0;
	$ltilink->toolurl = $templatelink->baseurl;
	if (strpos($ltilink->toolurl, 'https') === 0) {
		$ltilink->instructorchoicesendname = 1;
		$ltilink->instructorchoicesendemailaddr = 1;
	}
	else {
		$ltilink->instructorchoicesendname = 0;
		$ltilink->instructorchoicesendemailaddr = 0;
	}
		
	$strCustomParams = $DB->get_field('lti_types_config', 'value', array('typeid' => $templatelink->id, 'name' => 'customparameters'), IGNORE_MISSING);
	
	if ($strCustomParams != null) {
		$ltilink->instructorcustomparameters = $strCustomParams;
	}
	
	$ltilink->launchcontainer = $launchpresentation;
	$ltilink->resourcekey = $CFG->mylabmastering_key;
	$ltilink->password = $CFG->mylabmastering_secret;
	$ltilink->debuglaunch = 0;
	$ltilink->showtitlelaunch = 0;
	$ltilink->showdescriptionlaunch = 0;
		
	$use_icons = $CFG->mylabmastering_use_icons;
	if ($use_icons) {
		$ltilink->icon = $CFG->wwwroot.'/blocks/mylabmastering/pix/icon.jpg';
	}	
	
	$ltilink->id = $DB->insert_record('lti', $ltilink);
	
	$cm = new stdClass;
	$cm->course = $mm_c;
	$cm->module = mylabmastering_get_lti_module();
	$cm->instance = $ltilink->id;
	$cm->section = $mm_s;
	$cm->idnumber = mylabmastering_construct_id_for_lti($ltilink->id,$mm_c,$templatelink->id,'t');
	$cm->added = time();
	$cm->score = 0;
	$cm->indent = 0;
	$cm->visible = 1;
	$cm->visibleold = 1;	
	$cm->groupmode = 0;
	$cm->groupingid = 0;
	$cm->groupmembersonly = 0;
	$cm->completion = 0;
	$cm->completionview = 0;
	$cm->completionexpected = 0;
	$cm->showavailability = 0;
	$cm->showdescription = 0;
	$cm->coursemodule = $mm->coursemodule;
	$cm->id = $mm->coursemodule;
	
	$sectionid = course_add_cm_to_section($cm->course, $cm->coursemodule, $cm->section, null);
	$DB->update_record("course_modules", $cm);
	$DB->set_field("course_modules", "section", $sectionid, array("id" => $cm->coursemodule));
	
	rebuild_course_cache($mm_c);	    	
	
    return $ltilink->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function mylabmastering_update_instance($lti, $mform) {
	// this should never be called
    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function mylabmastering_delete_instance($id) {
	// this should never be called
    return true;
}

/**
 * Given a coursemodule object, this function returns the extra
 * information needed to print this activity in various places.
 * For this module we just need to support external urls as
 * activity icons
 *
 * @param cm_info $coursemodule
 * @return cached_cm_info info
 */
function mylabmastering_get_coursemodule_info($coursemodule) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    if (!$lti = $DB->get_record('lti', array('id' => $coursemodule->instance),
            'icon, secureicon, intro, introformat, name')) {
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

    $info->name = $lti->name;

    return $info;
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
function mylabmastering_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @uses $CFG
 * @return boolean
 **/
function mylabmastering_cron () {
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
function mylabmastering_grades($basicltiid) {
    return null;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of basiclti. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $basicltiid ID of an instance of this module
 * @return mixed boolean/array of students
 *
 * @TODO: implement this moodle function
 **/
function mylabmastering_get_participants($basicltiid) {
    return false;
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
function mylabmastering_scale_used ($basicltiid, $scaleid) {
    $return = false;

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
function mylabmastering_scale_used_anywhere($scaleid) {
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
function mylabmastering_install() {
     return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function mylabmastering_uninstall() {
    return true;
}


