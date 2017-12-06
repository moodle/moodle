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
 * @package   mod_scorm
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** SCORM_TYPE_LOCAL = local */
define('SCORM_TYPE_LOCAL', 'local');
/** SCORM_TYPE_LOCALSYNC = localsync */
define('SCORM_TYPE_LOCALSYNC', 'localsync');
/** SCORM_TYPE_EXTERNAL = external */
define('SCORM_TYPE_EXTERNAL', 'external');
/** SCORM_TYPE_AICCURL = external AICC url */
define('SCORM_TYPE_AICCURL', 'aiccurl');

define('SCORM_TOC_SIDE', 0);
define('SCORM_TOC_HIDDEN', 1);
define('SCORM_TOC_POPUP', 2);
define('SCORM_TOC_DISABLED', 3);

// Used to show/hide navigation buttons and set their position.
define('SCORM_NAV_DISABLED', 0);
define('SCORM_NAV_UNDER_CONTENT', 1);
define('SCORM_NAV_FLOATING', 2);

// Used to check what SCORM version is being used.
define('SCORM_12', 1);
define('SCORM_13', 2);
define('SCORM_AICC', 3);

// List of possible attemptstatusdisplay options.
define('SCORM_DISPLAY_ATTEMPTSTATUS_NO', 0);
define('SCORM_DISPLAY_ATTEMPTSTATUS_ALL', 1);
define('SCORM_DISPLAY_ATTEMPTSTATUS_MY', 2);
define('SCORM_DISPLAY_ATTEMPTSTATUS_ENTRY', 3);

/**
 * Return an array of status options
 *
 * Optionally with translated strings
 *
 * @param   bool    $with_strings   (optional)
 * @return  array
 */
function scorm_status_options($withstrings = false) {
    // Id's are important as they are bits.
    $options = array(
        2 => 'passed',
        4 => 'completed'
    );

    if ($withstrings) {
        foreach ($options as $key => $value) {
            $options[$key] = get_string('completionstatus_'.$value, 'scorm');
        }
    }

    return $options;
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global stdClass
 * @global object
 * @uses CONTEXT_MODULE
 * @uses SCORM_TYPE_LOCAL
 * @uses SCORM_TYPE_LOCALSYNC
 * @uses SCORM_TYPE_EXTERNAL
 * @param object $scorm Form data
 * @param object $mform
 * @return int new instance id
 */
function scorm_add_instance($scorm, $mform=null) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    if (empty($scorm->timeopen)) {
        $scorm->timeopen = 0;
    }
    if (empty($scorm->timeclose)) {
        $scorm->timeclose = 0;
    }
    if (empty($scorm->completionstatusallscos)) {
        $scorm->completionstatusallscos = 0;
    }
    $cmid       = $scorm->coursemodule;
    $cmidnumber = $scorm->cmidnumber;
    $courseid   = $scorm->course;

    $context = context_module::instance($cmid);

    $scorm = scorm_option2text($scorm);
    $scorm->width  = (int)str_replace('%', '', $scorm->width);
    $scorm->height = (int)str_replace('%', '', $scorm->height);

    if (!isset($scorm->whatgrade)) {
        $scorm->whatgrade = 0;
    }

    $id = $DB->insert_record('scorm', $scorm);

    // Update course module record - from now on this instance properly exists and all function may be used.
    $DB->set_field('course_modules', 'instance', $id, array('id' => $cmid));

    // Reload scorm instance.
    $record = $DB->get_record('scorm', array('id' => $id));

    // Store the package and verify.
    if ($record->scormtype === SCORM_TYPE_LOCAL) {
        if (!empty($scorm->packagefile)) {
            $fs = get_file_storage();
            $fs->delete_area_files($context->id, 'mod_scorm', 'package');
            file_save_draft_area_files($scorm->packagefile, $context->id, 'mod_scorm', 'package',
                0, array('subdirs' => 0, 'maxfiles' => 1));
            // Get filename of zip that was uploaded.
            $files = $fs->get_area_files($context->id, 'mod_scorm', 'package', 0, '', false);
            $file = reset($files);
            $filename = $file->get_filename();
            if ($filename !== false) {
                $record->reference = $filename;
            }
        }

    } else if ($record->scormtype === SCORM_TYPE_LOCALSYNC) {
        $record->reference = $scorm->packageurl;
    } else if ($record->scormtype === SCORM_TYPE_EXTERNAL) {
        $record->reference = $scorm->packageurl;
    } else if ($record->scormtype === SCORM_TYPE_AICCURL) {
        $record->reference = $scorm->packageurl;
        $record->hidetoc = SCORM_TOC_DISABLED; // TOC is useless for direct AICCURL so disable it.
    } else {
        return false;
    }

    // Save reference.
    $DB->update_record('scorm', $record);

    // Extra fields required in grade related functions.
    $record->course     = $courseid;
    $record->cmidnumber = $cmidnumber;
    $record->cmid       = $cmid;

    scorm_parse($record, true);

    scorm_grade_item_update($record);
    scorm_update_calendar($record, $cmid);
    if (!empty($scorm->completionexpected)) {
        \core_completion\api::update_completion_date_event($cmid, 'scorm', $record, $scorm->completionexpected);
    }

    return $record->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global stdClass
 * @global object
 * @uses CONTEXT_MODULE
 * @uses SCORM_TYPE_LOCAL
 * @uses SCORM_TYPE_LOCALSYNC
 * @uses SCORM_TYPE_EXTERNAL
 * @param object $scorm Form data
 * @param object $mform
 * @return bool
 */
function scorm_update_instance($scorm, $mform=null) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    if (empty($scorm->timeopen)) {
        $scorm->timeopen = 0;
    }
    if (empty($scorm->timeclose)) {
        $scorm->timeclose = 0;
    }
    if (empty($scorm->completionstatusallscos)) {
        $scorm->completionstatusallscos = 0;
    }

    $cmid       = $scorm->coursemodule;
    $cmidnumber = $scorm->cmidnumber;
    $courseid   = $scorm->course;

    $scorm->id = $scorm->instance;

    $context = context_module::instance($cmid);

    if ($scorm->scormtype === SCORM_TYPE_LOCAL) {
        if (!empty($scorm->packagefile)) {
            $fs = get_file_storage();
            $fs->delete_area_files($context->id, 'mod_scorm', 'package');
            file_save_draft_area_files($scorm->packagefile, $context->id, 'mod_scorm', 'package',
                0, array('subdirs' => 0, 'maxfiles' => 1));
            // Get filename of zip that was uploaded.
            $files = $fs->get_area_files($context->id, 'mod_scorm', 'package', 0, '', false);
            $file = reset($files);
            $filename = $file->get_filename();
            if ($filename !== false) {
                $scorm->reference = $filename;
            }
        }

    } else if ($scorm->scormtype === SCORM_TYPE_LOCALSYNC) {
        $scorm->reference = $scorm->packageurl;
    } else if ($scorm->scormtype === SCORM_TYPE_EXTERNAL) {
        $scorm->reference = $scorm->packageurl;
    } else if ($scorm->scormtype === SCORM_TYPE_AICCURL) {
        $scorm->reference = $scorm->packageurl;
        $scorm->hidetoc = SCORM_TOC_DISABLED; // TOC is useless for direct AICCURL so disable it.
    } else {
        return false;
    }

    $scorm = scorm_option2text($scorm);
    $scorm->width        = (int)str_replace('%', '', $scorm->width);
    $scorm->height       = (int)str_replace('%', '', $scorm->height);
    $scorm->timemodified = time();

    if (!isset($scorm->whatgrade)) {
        $scorm->whatgrade = 0;
    }

    $DB->update_record('scorm', $scorm);
    // We need to find this out before we blow away the form data.
    $completionexpected = (!empty($scorm->completionexpected)) ? $scorm->completionexpected : null;

    $scorm = $DB->get_record('scorm', array('id' => $scorm->id));

    // Extra fields required in grade related functions.
    $scorm->course   = $courseid;
    $scorm->idnumber = $cmidnumber;
    $scorm->cmid     = $cmid;

    scorm_parse($scorm, (bool)$scorm->updatefreq);

    scorm_grade_item_update($scorm);
    scorm_update_grades($scorm);
    scorm_update_calendar($scorm, $cmid);
    \core_completion\api::update_completion_date_event($cmid, 'scorm', $scorm, $completionexpected);

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global stdClass
 * @global object
 * @param int $id Scorm instance id
 * @return boolean
 */
function scorm_delete_instance($id) {
    global $CFG, $DB;

    if (! $scorm = $DB->get_record('scorm', array('id' => $id))) {
        return false;
    }

    $result = true;

    // Delete any dependent records.
    if (! $DB->delete_records('scorm_scoes_track', array('scormid' => $scorm->id))) {
        $result = false;
    }
    if ($scoes = $DB->get_records('scorm_scoes', array('scorm' => $scorm->id))) {
        foreach ($scoes as $sco) {
            if (! $DB->delete_records('scorm_scoes_data', array('scoid' => $sco->id))) {
                $result = false;
            }
        }
        $DB->delete_records('scorm_scoes', array('scorm' => $scorm->id));
    }
    if (! $DB->delete_records('scorm', array('id' => $scorm->id))) {
        $result = false;
    }

    /*if (! $DB->delete_records('scorm_sequencing_controlmode', array('scormid'=>$scorm->id))) {
        $result = false;
    }
    if (! $DB->delete_records('scorm_sequencing_rolluprules', array('scormid'=>$scorm->id))) {
        $result = false;
    }
    if (! $DB->delete_records('scorm_sequencing_rolluprule', array('scormid'=>$scorm->id))) {
        $result = false;
    }
    if (! $DB->delete_records('scorm_sequencing_rollupruleconditions', array('scormid'=>$scorm->id))) {
        $result = false;
    }
    if (! $DB->delete_records('scorm_sequencing_rolluprulecondition', array('scormid'=>$scorm->id))) {
        $result = false;
    }
    if (! $DB->delete_records('scorm_sequencing_rulecondition', array('scormid'=>$scorm->id))) {
        $result = false;
    }
    if (! $DB->delete_records('scorm_sequencing_ruleconditions', array('scormid'=>$scorm->id))) {
        $result = false;
    }*/

    scorm_grade_item_delete($scorm);

    return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * @global stdClass
 * @param int $course Course id
 * @param int $user User id
 * @param int $mod
 * @param int $scorm The scorm id
 * @return mixed
 */
function scorm_user_outline($course, $user, $mod, $scorm) {
    global $CFG;
    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    require_once("$CFG->libdir/gradelib.php");
    $grades = grade_get_grades($course->id, 'mod', 'scorm', $scorm->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        $result = new stdClass();
        $result->info = get_string('grade') . ': '. $grade->str_long_grade;

        // Datesubmitted == time created. dategraded == time modified or time overridden
        // if grade was last modified by the user themselves use date graded. Otherwise use date submitted.
        // TODO: move this copied & pasted code somewhere in the grades API. See MDL-26704.
        if ($grade->usermodified == $user->id || empty($grade->datesubmitted)) {
            $result->time = $grade->dategraded;
        } else {
            $result->time = $grade->datesubmitted;
        }

        return $result;
    }
    return null;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @global stdClass
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $scorm
 * @return boolean
 */
function scorm_user_complete($course, $user, $mod, $scorm) {
    global $CFG, $DB, $OUTPUT;
    require_once("$CFG->libdir/gradelib.php");

    $liststyle = 'structlist';
    $now = time();
    $firstmodify = $now;
    $lastmodify = 0;
    $sometoreport = false;
    $report = '';

    // First Access and Last Access dates for SCOs.
    require_once($CFG->dirroot.'/mod/scorm/locallib.php');
    $timetracks = scorm_get_sco_runtime($scorm->id, false, $user->id);
    $firstmodify = $timetracks->start;
    $lastmodify = $timetracks->finish;

    $grades = grade_get_grades($course->id, 'mod', 'scorm', $scorm->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        echo $OUTPUT->container(get_string('grade').': '.$grade->str_long_grade);
        if ($grade->str_feedback) {
            echo $OUTPUT->container(get_string('feedback').': '.$grade->str_feedback);
        }
    }

    if ($orgs = $DB->get_records_select('scorm_scoes', 'scorm = ? AND '.
                                         $DB->sql_isempty('scorm_scoes', 'launch', false, true).' AND '.
                                         $DB->sql_isempty('scorm_scoes', 'organization', false, false),
                                         array($scorm->id), 'sortorder, id', 'id, identifier, title')) {
        if (count($orgs) <= 1) {
            unset($orgs);
            $orgs = array();
            $org = new stdClass();
            $org->identifier = '';
            $orgs[] = $org;
        }
        $report .= html_writer::start_div('mod-scorm');
        foreach ($orgs as $org) {
            $conditions = array();
            $currentorg = '';
            if (!empty($org->identifier)) {
                $report .= html_writer::div($org->title, 'orgtitle');
                $currentorg = $org->identifier;
                $conditions['organization'] = $currentorg;
            }
            $report .= html_writer::start_tag('ul', array('id' => '0', 'class' => $liststyle));
                $conditions['scorm'] = $scorm->id;
            if ($scoes = $DB->get_records('scorm_scoes', $conditions, "sortorder, id")) {
                // Drop keys so that we can access array sequentially.
                $scoes = array_values($scoes);
                $level = 0;
                $sublist = 1;
                $parents[$level] = '/';
                foreach ($scoes as $pos => $sco) {
                    if ($parents[$level] != $sco->parent) {
                        if ($level > 0 && $parents[$level - 1] == $sco->parent) {
                            $report .= html_writer::end_tag('ul').html_writer::end_tag('li');
                            $level--;
                        } else {
                            $i = $level;
                            $closelist = '';
                            while (($i > 0) && ($parents[$level] != $sco->parent)) {
                                $closelist .= html_writer::end_tag('ul').html_writer::end_tag('li');
                                $i--;
                            }
                            if (($i == 0) && ($sco->parent != $currentorg)) {
                                $report .= html_writer::start_tag('li');
                                $report .= html_writer::start_tag('ul', array('id' => $sublist, 'class' => $liststyle));
                                $level++;
                            } else {
                                $report .= $closelist;
                                $level = $i;
                            }
                            $parents[$level] = $sco->parent;
                        }
                    }
                    $report .= html_writer::start_tag('li');
                    if (isset($scoes[$pos + 1])) {
                        $nextsco = $scoes[$pos + 1];
                    } else {
                        $nextsco = false;
                    }
                    if (($nextsco !== false) && ($sco->parent != $nextsco->parent) &&
                            (($level == 0) || (($level > 0) && ($nextsco->parent == $sco->identifier)))) {
                        $sublist++;
                    } else {
                        $report .= $OUTPUT->spacer(array("height" => "12", "width" => "13"));
                    }

                    if ($sco->launch) {
                        $score = '';
                        $totaltime = '';
                        if ($usertrack = scorm_get_tracks($sco->id, $user->id)) {
                            if ($usertrack->status == '') {
                                $usertrack->status = 'notattempted';
                            }
                            $strstatus = get_string($usertrack->status, 'scorm');
                            $report .= $OUTPUT->pix_icon($usertrack->status, $strstatus, 'scorm');
                        } else {
                            if ($sco->scormtype == 'sco') {
                                $report .= $OUTPUT->pix_icon('notattempted', get_string('notattempted', 'scorm'), 'scorm');
                            } else {
                                $report .= $OUTPUT->pix_icon('asset', get_string('asset', 'scorm'), 'scorm');
                            }
                        }
                        $report .= "&nbsp;$sco->title $score$totaltime".html_writer::end_tag('li');
                        if ($usertrack !== false) {
                            $sometoreport = true;
                            $report .= html_writer::start_tag('li').html_writer::start_tag('ul', array('class' => $liststyle));
                            foreach ($usertrack as $element => $value) {
                                if (substr($element, 0, 3) == 'cmi') {
                                    $report .= html_writer::tag('li', $element.' => '.s($value));
                                }
                            }
                            $report .= html_writer::end_tag('ul').html_writer::end_tag('li');
                        }
                    } else {
                        $report .= "&nbsp;$sco->title".html_writer::end_tag('li');
                    }
                }
                for ($i = 0; $i < $level; $i++) {
                    $report .= html_writer::end_tag('ul').html_writer::end_tag('li');
                }
            }
            $report .= html_writer::end_tag('ul').html_writer::empty_tag('br');
        }
        $report .= html_writer::end_div();
    }
    if ($sometoreport) {
        if ($firstmodify < $now) {
            $timeago = format_time($now - $firstmodify);
            echo get_string('firstaccess', 'scorm').': '.userdate($firstmodify).' ('.$timeago.")".html_writer::empty_tag('br');
        }
        if ($lastmodify > 0) {
            $timeago = format_time($now - $lastmodify);
            echo get_string('lastaccess', 'scorm').': '.userdate($lastmodify).' ('.$timeago.")".html_writer::empty_tag('br');
        }
        echo get_string('report', 'scorm').":".html_writer::empty_tag('br');
        echo $report;
    } else {
        print_string('noactivity', 'scorm');
    }

    return true;
}

/**
 * Function to be run periodically according to the moodle Tasks API
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @global stdClass
 * @global object
 * @return boolean
 */
function scorm_cron_scheduled_task () {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    $sitetimezone = core_date::get_server_timezone();
    // Now see if there are any scorm updates to be done.

    if (!isset($CFG->scorm_updatetimelast)) {    // To catch the first time.
        set_config('scorm_updatetimelast', 0);
    }

    $timenow = time();
    $updatetime = usergetmidnight($timenow, $sitetimezone);

    if ($CFG->scorm_updatetimelast < $updatetime and $timenow > $updatetime) {

        set_config('scorm_updatetimelast', $timenow);

        mtrace('Updating scorm packages which require daily update');// We are updating.

        $scormsupdate = $DB->get_records('scorm', array('updatefreq' => SCORM_UPDATE_EVERYDAY));
        foreach ($scormsupdate as $scormupdate) {
            scorm_parse($scormupdate, true);
        }

        // Now clear out AICC session table with old session data.
        $cfgscorm = get_config('scorm');
        if (!empty($cfgscorm->allowaicchacp)) {
            $expiretime = time() - ($cfgscorm->aicchacpkeepsessiondata * 24 * 60 * 60);
            $DB->delete_records_select('scorm_aicc_session', 'timemodified < ?', array($expiretime));
        }
    }

    return true;
}

/**
 * Return grade for given user or all users.
 *
 * @global stdClass
 * @global object
 * @param int $scormid id of scorm
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function scorm_get_user_grades($scorm, $userid=0) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    $grades = array();
    if (empty($userid)) {
        $scousers = $DB->get_records_select('scorm_scoes_track', "scormid=? GROUP BY userid",
                                            array($scorm->id), "", "userid,null");
        if ($scousers) {
            foreach ($scousers as $scouser) {
                $grades[$scouser->userid] = new stdClass();
                $grades[$scouser->userid]->id         = $scouser->userid;
                $grades[$scouser->userid]->userid     = $scouser->userid;
                $grades[$scouser->userid]->rawgrade = scorm_grade_user($scorm, $scouser->userid);
            }
        } else {
            return false;
        }

    } else {
        $preattempt = $DB->get_records_select('scorm_scoes_track', "scormid=? AND userid=? GROUP BY userid",
                                                array($scorm->id, $userid), "", "userid,null");
        if (!$preattempt) {
            return false; // No attempt yet.
        }
        $grades[$userid] = new stdClass();
        $grades[$userid]->id         = $userid;
        $grades[$userid]->userid     = $userid;
        $grades[$userid]->rawgrade = scorm_grade_user($scorm, $userid);
    }

    return $grades;
}

/**
 * Update grades in central gradebook
 *
 * @category grade
 * @param object $scorm
 * @param int $userid specific user only, 0 mean all
 * @param bool $nullifnone
 */
function scorm_update_grades($scorm, $userid=0, $nullifnone=true) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->libdir.'/completionlib.php');

    if ($grades = scorm_get_user_grades($scorm, $userid)) {
        scorm_grade_item_update($scorm, $grades);
        // Set complete.
        scorm_set_completion($scorm, $userid, COMPLETION_COMPLETE, $grades);
    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = null;
        scorm_grade_item_update($scorm, $grade);
        // Set incomplete.
        scorm_set_completion($scorm, $userid, COMPLETION_INCOMPLETE);
    } else {
        scorm_grade_item_update($scorm);
    }
}

/**
 * Update/create grade item for given scorm
 *
 * @category grade
 * @uses GRADE_TYPE_VALUE
 * @uses GRADE_TYPE_NONE
 * @param object $scorm object with extra cmidnumber
 * @param mixed $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return object grade_item
 */
function scorm_grade_item_update($scorm, $grades=null) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/mod/scorm/locallib.php');
    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir.'/gradelib.php');
    }

    $params = array('itemname' => $scorm->name);
    if (isset($scorm->cmidnumber)) {
        $params['idnumber'] = $scorm->cmidnumber;
    }

    if ($scorm->grademethod == GRADESCOES) {
        $maxgrade = $DB->count_records_select('scorm_scoes', 'scorm = ? AND '.
                                                $DB->sql_isnotempty('scorm_scoes', 'launch', false, true), array($scorm->id));
        if ($maxgrade) {
            $params['gradetype'] = GRADE_TYPE_VALUE;
            $params['grademax']  = $maxgrade;
            $params['grademin']  = 0;
        } else {
            $params['gradetype'] = GRADE_TYPE_NONE;
        }
    } else {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $scorm->maxgrade;
        $params['grademin']  = 0;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/scorm', $scorm->course, 'mod', 'scorm', $scorm->id, 0, $grades, $params);
}

/**
 * Delete grade item for given scorm
 *
 * @category grade
 * @param object $scorm object
 * @return object grade_item
 */
function scorm_grade_item_delete($scorm) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/scorm', $scorm->course, 'mod', 'scorm', $scorm->id, 0, null, array('deleted' => 1));
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function scorm_get_view_actions() {
    return array('pre-view', 'view', 'view all', 'report');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function scorm_get_post_actions() {
    return array();
}

/**
 * @param object $scorm
 * @return object $scorm
 */
function scorm_option2text($scorm) {
    $scormpopoupoptions = scorm_get_popup_options_array();

    if (isset($scorm->popup)) {
        if ($scorm->popup == 1) {
            $optionlist = array();
            foreach ($scormpopoupoptions as $name => $option) {
                if (isset($scorm->$name)) {
                    $optionlist[] = $name.'='.$scorm->$name;
                } else {
                    $optionlist[] = $name.'=0';
                }
            }
            $scorm->options = implode(',', $optionlist);
        } else {
            $scorm->options = '';
        }
    } else {
        $scorm->popup = 0;
        $scorm->options = '';
    }
    return $scorm;
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the scorm.
 *
 * @param object $mform form passed by reference
 */
function scorm_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'scormheader', get_string('modulenameplural', 'scorm'));
    $mform->addElement('advcheckbox', 'reset_scorm', get_string('deleteallattempts', 'scorm'));
}

/**
 * Course reset form defaults.
 *
 * @return array
 */
function scorm_reset_course_form_defaults($course) {
    return array('reset_scorm' => 1);
}

/**
 * Removes all grades from gradebook
 *
 * @global stdClass
 * @global object
 * @param int $courseid
 * @param string optional type
 */
function scorm_reset_gradebook($courseid, $type='') {
    global $CFG, $DB;

    $sql = "SELECT s.*, cm.idnumber as cmidnumber, s.course as courseid
              FROM {scorm} s, {course_modules} cm, {modules} m
             WHERE m.name='scorm' AND m.id=cm.module AND cm.instance=s.id AND s.course=?";

    if ($scorms = $DB->get_records_sql($sql, array($courseid))) {
        foreach ($scorms as $scorm) {
            scorm_grade_item_update($scorm, 'reset');
        }
    }
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * scorm attempts for course $data->courseid.
 *
 * @global stdClass
 * @global object
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function scorm_reset_userdata($data) {
    global $CFG, $DB;

    $componentstr = get_string('modulenameplural', 'scorm');
    $status = array();

    if (!empty($data->reset_scorm)) {
        $scormssql = "SELECT s.id
                         FROM {scorm} s
                        WHERE s.course=?";

        $DB->delete_records_select('scorm_scoes_track', "scormid IN ($scormssql)", array($data->courseid));

        // Remove all grades from gradebook.
        if (empty($data->reset_gradebook_grades)) {
            scorm_reset_gradebook($data->courseid);
        }

        $status[] = array('component' => $componentstr, 'item' => get_string('deleteallattempts', 'scorm'), 'error' => false);
    }

    // No dates to shift here.

    return $status;
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function scorm_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * Lists all file areas current user may browse
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @return array
 */
function scorm_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('areacontent', 'scorm');
    $areas['package'] = get_string('areapackage', 'scorm');
    return $areas;
}

/**
 * File browsing support for SCORM file areas
 *
 * @package  mod_scorm
 * @category files
 * @param file_browser $browser file browser instance
 * @param array $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function scorm_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        return null;
    }

    // No writing for now!

    $fs = get_file_storage();

    if ($filearea === 'content') {

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_scorm', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_scorm', 'content', 0);
            } else {
                // Not found.
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/scorm/locallib.php");
        return new scorm_package_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, false, false);

    } else if ($filearea === 'package') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_scorm', 'package', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_scorm', 'package', 0);
            } else {
                // Not found.
                return null;
            }
        }
        return new file_info_stored($browser, $context, $storedfile, $urlbase, $areas[$filearea], false, true, false, false);
    }

    // Scorm_intro handled in file_browser.

    return false;
}

/**
 * Serves scorm content, introduction images and packages. Implements needed access control ;-)
 *
 * @package  mod_scorm
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
function scorm_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    $canmanageactivity = has_capability('moodle/course:manageactivities', $context);
    $lifetime = null;

    // Check SCORM availability.
    if (!$canmanageactivity) {
        require_once($CFG->dirroot.'/mod/scorm/locallib.php');

        $scorm = $DB->get_record('scorm', array('id' => $cm->instance), 'id, timeopen, timeclose', MUST_EXIST);
        list($available, $warnings) = scorm_get_availability_status($scorm);
        if (!$available) {
            return false;
        }
    }

    if ($filearea === 'content') {
        $revision = (int)array_shift($args); // Prevents caching problems - ignored here.
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_scorm/content/0/$relativepath";
        // TODO: add any other access restrictions here if needed!

    } else if ($filearea === 'package') {
        // Check if the global setting for disabling package downloads is enabled.
        $protectpackagedownloads = get_config('scorm', 'protectpackagedownloads');
        if ($protectpackagedownloads and !$canmanageactivity) {
            return false;
        }
        $revision = (int)array_shift($args); // Prevents caching problems - ignored here.
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_scorm/package/0/$relativepath";
        $lifetime = 0; // No caching here.

    } else if ($filearea === 'imsmanifest') { // This isn't a real filearea, it's a url parameter for this type of package.
        $revision = (int)array_shift($args); // Prevents caching problems - ignored here.
        $relativepath = implode('/', $args);

        // Get imsmanifest file.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_scorm', 'package', 0, '', false);
        $file = reset($files);

        // Check that the package file is an imsmanifest.xml file - if not then this method is not allowed.
        $packagefilename = $file->get_filename();
        if (strtolower($packagefilename) !== 'imsmanifest.xml') {
            return false;
        }

        $file->send_relative_file($relativepath);
    } else {
        return false;
    }

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        if ($filearea === 'content') { // Return file not found straight away to improve performance.
            send_header_404();
            die;
        }
        return false;
    }

    // Finally send the file.
    send_stored_file($file, $lifetime, 0, false, $options);
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_COMPLETION_HAS_RULES
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function scorm_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES:    return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Get the filename for a temp log file
 *
 * @param string $type - type of log(aicc,scorm12,scorm13) used as prefix for filename
 * @param integer $scoid - scoid of object this log entry is for
 * @return string The filename as an absolute path
 */
function scorm_debug_log_filename($type, $scoid) {
    global $CFG, $USER;

    $logpath = $CFG->tempdir.'/scormlogs';
    $logfile = $logpath.'/'.$type.'debug_'.$USER->id.'_'.$scoid.'.log';
    return $logfile;
}

/**
 * writes log output to a temp log file
 *
 * @param string $type - type of log(aicc,scorm12,scorm13) used as prefix for filename
 * @param string $text - text to be written to file.
 * @param integer $scoid - scoid of object this log entry is for.
 */
function scorm_debug_log_write($type, $text, $scoid) {
    global $CFG;

    $debugenablelog = get_config('scorm', 'allowapidebug');
    if (!$debugenablelog || empty($text)) {
        return;
    }
    if (make_temp_directory('scormlogs/')) {
        $logfile = scorm_debug_log_filename($type, $scoid);
        @file_put_contents($logfile, date('Y/m/d H:i:s O')." DEBUG $text\r\n", FILE_APPEND);
        @chmod($logfile, $CFG->filepermissions);
    }
}

/**
 * Remove debug log file
 *
 * @param string $type - type of log(aicc,scorm12,scorm13) used as prefix for filename
 * @param integer $scoid - scoid of object this log entry is for
 * @return boolean True if the file is successfully deleted, false otherwise
 */
function scorm_debug_log_remove($type, $scoid) {

    $debugenablelog = get_config('scorm', 'allowapidebug');
    $logfile = scorm_debug_log_filename($type, $scoid);
    if (!$debugenablelog || !file_exists($logfile)) {
        return false;
    }

    return @unlink($logfile);
}

/**
 * writes overview info for course_overview block - displays upcoming scorm objects that have a due date
 *
 * @deprecated since 3.3
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57487.
 * @param object $type - type of log(aicc,scorm12,scorm13) used as prefix for filename
 * @param array $htmlarray
 * @return mixed
 */
function scorm_print_overview($courses, &$htmlarray) {
    global $USER, $CFG;

    debugging('The function scorm_print_overview() is now deprecated.', DEBUG_DEVELOPER);

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$scorms = get_all_instances_in_courses('scorm', $courses)) {
        return;
    }

    $strscorm   = get_string('modulename', 'scorm');
    $strduedate = get_string('duedate', 'scorm');

    foreach ($scorms as $scorm) {
        $time = time();
        $showattemptstatus = false;
        if ($scorm->timeopen) {
            $isopen = ($scorm->timeopen <= $time && $time <= $scorm->timeclose);
        }
        if ($scorm->displayattemptstatus == SCORM_DISPLAY_ATTEMPTSTATUS_ALL ||
                $scorm->displayattemptstatus == SCORM_DISPLAY_ATTEMPTSTATUS_MY) {
            $showattemptstatus = true;
        }
        if ($showattemptstatus || !empty($isopen) || !empty($scorm->timeclose)) {
            $str = html_writer::start_div('scorm overview').html_writer::div($strscorm. ': '.
                    html_writer::link($CFG->wwwroot.'/mod/scorm/view.php?id='.$scorm->coursemodule, $scorm->name,
                                        array('title' => $strscorm, 'class' => $scorm->visible ? '' : 'dimmed')), 'name');
            if ($scorm->timeclose) {
                $str .= html_writer::div($strduedate.': '.userdate($scorm->timeclose), 'info');
            }
            if ($showattemptstatus) {
                require_once($CFG->dirroot.'/mod/scorm/locallib.php');
                $str .= html_writer::div(scorm_get_attempt_status($USER, $scorm), 'details');
            }
            $str .= html_writer::end_div();
            if (empty($htmlarray[$scorm->course]['scorm'])) {
                $htmlarray[$scorm->course]['scorm'] = $str;
            } else {
                $htmlarray[$scorm->course]['scorm'] .= $str;
            }
        }
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function scorm_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $modulepagetype = array('mod-scorm-*' => get_string('page-mod-scorm-x', 'scorm'));
    return $modulepagetype;
}

/**
 * Returns the SCORM version used.
 * @param string $scormversion comes from $scorm->version
 * @param string $version one of the defined vars SCORM_12, SCORM_13, SCORM_AICC (or empty)
 * @return Scorm version.
 */
function scorm_version_check($scormversion, $version='') {
    $scormversion = trim(strtolower($scormversion));
    if (empty($version) || $version == SCORM_12) {
        if ($scormversion == 'scorm_12' || $scormversion == 'scorm_1.2') {
            return SCORM_12;
        }
        if (!empty($version)) {
            return false;
        }
    }
    if (empty($version) || $version == SCORM_13) {
        if ($scormversion == 'scorm_13' || $scormversion == 'scorm_1.3') {
            return SCORM_13;
        }
        if (!empty($version)) {
            return false;
        }
    }
    if (empty($version) || $version == SCORM_AICC) {
        if (strpos($scormversion, 'aicc')) {
            return SCORM_AICC;
        }
        if (!empty($version)) {
            return false;
        }
    }
    return false;
}

/**
 * Obtains the automatic completion state for this scorm based on any conditions
 * in scorm settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return
 *   value depends on comparison type)
 */
function scorm_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    $result = $type;

    // Get scorm.
    if (!$scorm = $DB->get_record('scorm', array('id' => $cm->instance))) {
        print_error('cannotfindscorm');
    }
    // Only check for existence of tracks and return false if completionstatusrequired or completionscorerequired
    // this means that if only view is required we don't end up with a false state.
    if ($scorm->completionstatusrequired !== null ||
        $scorm->completionscorerequired !== null) {
        // Get user's tracks data.
        $tracks = $DB->get_records_sql(
            "
            SELECT
                id,
                scoid,
                element,
                value
            FROM
                {scorm_scoes_track}
            WHERE
                scormid = ?
            AND userid = ?
            AND element IN
            (
                'cmi.core.lesson_status',
                'cmi.completion_status',
                'cmi.success_status',
                'cmi.core.score.raw',
                'cmi.score.raw'
            )
            ",
            array($scorm->id, $userid)
        );

        if (!$tracks) {
            return completion_info::aggregate_completion_states($type, $result, false);
        }
    }

    // Check for status.
    if ($scorm->completionstatusrequired !== null) {

        // Get status.
        $statuses = array_flip(scorm_status_options());
        $nstatus = 0;
        // Check any track for these values.
        $scostatus = array();
        foreach ($tracks as $track) {
            if (!in_array($track->element, array('cmi.core.lesson_status', 'cmi.completion_status', 'cmi.success_status'))) {
                continue;
            }
            if (array_key_exists($track->value, $statuses)) {
                $scostatus[$track->scoid] = true;
                $nstatus |= $statuses[$track->value];
            }
        }

        if (!empty($scorm->completionstatusallscos)) {
            // Iterate over all scos and make sure each has a lesson_status.
            $scos = $DB->get_records('scorm_scoes', array('scorm' => $scorm->id, 'scormtype' => 'sco'));
            foreach ($scos as $sco) {
                if (empty($scostatus[$sco->id])) {
                    return completion_info::aggregate_completion_states($type, $result, false);
                }
            }
            return completion_info::aggregate_completion_states($type, $result, true);
        } else if ($scorm->completionstatusrequired & $nstatus) {
            return completion_info::aggregate_completion_states($type, $result, true);
        } else {
            return completion_info::aggregate_completion_states($type, $result, false);
        }
    }

    // Check for score.
    if ($scorm->completionscorerequired !== null) {
        $maxscore = -1;

        foreach ($tracks as $track) {
            if (!in_array($track->element, array('cmi.core.score.raw', 'cmi.score.raw'))) {
                continue;
            }

            if (strlen($track->value) && floatval($track->value) >= $maxscore) {
                $maxscore = floatval($track->value);
            }
        }

        if ($scorm->completionscorerequired <= $maxscore) {
            return completion_info::aggregate_completion_states($type, $result, true);
        } else {
            return completion_info::aggregate_completion_states($type, $result, false);
        }
    }

    return $result;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function scorm_dndupload_register() {
    return array('files' => array(
        array('extension' => 'zip', 'message' => get_string('dnduploadscorm', 'scorm'))
    ));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function scorm_dndupload_handle($uploadinfo) {

    $context = context_module::instance($uploadinfo->coursemodule);
    file_save_draft_area_files($uploadinfo->draftitemid, $context->id, 'mod_scorm', 'package', 0);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_scorm', 'package', 0, 'sortorder, itemid, filepath, filename', false);
    $file = reset($files);

    // Validate the file, make sure it's a valid SCORM package!
    $errors = scorm_validate_package($file);
    if (!empty($errors)) {
        return false;
    }
    // Create a default scorm object to pass to scorm_add_instance()!
    $scorm = get_config('scorm');
    $scorm->course = $uploadinfo->course->id;
    $scorm->coursemodule = $uploadinfo->coursemodule;
    $scorm->cmidnumber = '';
    $scorm->name = $uploadinfo->displayname;
    $scorm->scormtype = SCORM_TYPE_LOCAL;
    $scorm->reference = $file->get_filename();
    $scorm->intro = '';
    $scorm->width = $scorm->framewidth;
    $scorm->height = $scorm->frameheight;

    return scorm_add_instance($scorm, null);
}

/**
 * Sets activity completion state
 *
 * @param object $scorm object
 * @param int $userid User ID
 * @param int $completionstate Completion state
 * @param array $grades grades array of users with grades - used when $userid = 0
 */
function scorm_set_completion($scorm, $userid, $completionstate = COMPLETION_COMPLETE, $grades = array()) {
    $course = new stdClass();
    $course->id = $scorm->course;
    $completion = new completion_info($course);

    // Check if completion is enabled site-wide, or for the course.
    if (!$completion->is_enabled()) {
        return;
    }

    $cm = get_coursemodule_from_instance('scorm', $scorm->id, $scorm->course);
    if (empty($cm) || !$completion->is_enabled($cm)) {
            return;
    }

    if (empty($userid)) { // We need to get all the relevant users from $grades param.
        foreach ($grades as $grade) {
            $completion->update_state($cm, $completionstate, $grade->userid);
        }
    } else {
        $completion->update_state($cm, $completionstate, $userid);
    }
}

/**
 * Check that a Zip file contains a valid SCORM package
 *
 * @param $file stored_file a Zip file.
 * @return array empty if no issue is found. Array of error message otherwise
 */
function scorm_validate_package($file) {
    $packer = get_file_packer('application/zip');
    $errors = array();
    if ($file->is_external_file()) { // Get zip file so we can check it is correct.
        $file->import_external_file_contents();
    }
    $filelist = $file->list_files($packer);

    if (!is_array($filelist)) {
        $errors['packagefile'] = get_string('badarchive', 'scorm');
    } else {
        $aiccfound = false;
        $badmanifestpresent = false;
        foreach ($filelist as $info) {
            if ($info->pathname == 'imsmanifest.xml') {
                return array();
            } else if (strpos($info->pathname, 'imsmanifest.xml') !== false) {
                // This package has an imsmanifest file inside a folder of the package.
                $badmanifestpresent = true;
            }
            if (preg_match('/\.cst$/', $info->pathname)) {
                return array();
            }
        }
        if (!$aiccfound) {
            if ($badmanifestpresent) {
                $errors['packagefile'] = get_string('badimsmanifestlocation', 'scorm');
            } else {
                $errors['packagefile'] = get_string('nomanifest', 'scorm');
            }
        }
    }
    return $errors;
}

/**
 * Check and set the correct mode and attempt when entering a SCORM package.
 *
 * @param object $scorm object
 * @param string $newattempt should a new attempt be generated here.
 * @param int $attempt the attempt number this is for.
 * @param int $userid the userid of the user.
 * @param string $mode the current mode that has been selected.
 */
function scorm_check_mode($scorm, &$newattempt, &$attempt, $userid, &$mode) {
    global $DB;

    if (($mode == 'browse')) {
        if ($scorm->hidebrowse == 1) {
            // Prevent Browse mode if hidebrowse is set.
            $mode = 'normal';
        } else {
            // We don't need to check attempts as browse mode is set.
            return;
        }
    }
    // Check if the scorm module is incomplete (used to validate user request to start a new attempt).
    $incomplete = true;
    $sql = "SELECT sc.id, t.value
              FROM {scorm_scoes} sc
         LEFT JOIN {scorm_scoes_track} t ON sc.scorm = t.scormid AND sc.id = t.scoid
                   AND t.element = 'cmi.core.lesson_status' AND t.userid = ? AND t.attempt = ?
             WHERE sc.scormtype = 'sco' AND sc.scorm = ?";
    $tracks = $DB->get_recordset_sql($sql, array($userid, $attempt, $scorm->id));

    foreach ($tracks as $track) {
        if (($track->value == 'completed') || ($track->value == 'passed') || ($track->value == 'failed')) {
            $incomplete = false;
        } else {
            $incomplete = true;
            break; // Found an incomplete sco, so the result as a whole is incomplete.
        }
    }
    $tracks->close();

    // Validate user request to start a new attempt.
    if ($incomplete === true) {
        // The option to start a new attempt should never have been presented. Force false.
        $newattempt = 'off';
    } else if (!empty($scorm->forcenewattempt)) {
        // A new attempt should be forced for already completed attempts.
        $newattempt = 'on';
    }

    if (($newattempt == 'on') && (($attempt < $scorm->maxattempt) || ($scorm->maxattempt == 0))) {
        $attempt++;
        $mode = 'normal';
    } else { // Check if review mode should be set.
        if ($incomplete === true) {
            $mode = 'normal';
        } else {
            $mode = 'review';
        }
    }
}

/**
 * Trigger the course_module_viewed event.
 *
 * @param  stdClass $scorm        scorm object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function scorm_view($scorm, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $scorm->id
    );

    $event = \mod_scorm\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('scorm', $scorm);
    $event->trigger();
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function scorm_check_updates_since(cm_info $cm, $from, $filter = array()) {
    global $DB, $USER, $CFG;
    require_once($CFG->dirroot . '/mod/scorm/locallib.php');

    $scorm = $DB->get_record($cm->modname, array('id' => $cm->instance), '*', MUST_EXIST);
    $updates = new stdClass();
    list($available, $warnings) = scorm_get_availability_status($scorm, true, $cm->context);
    if (!$available) {
        return $updates;
    }
    $updates = course_check_module_updates_since($cm, $from, array('package'), $filter);

    $updates->tracks = (object) array('updated' => false);
    $select = 'scormid = ? AND userid = ? AND timemodified > ?';
    $params = array($scorm->id, $USER->id, $from);
    $tracks = $DB->get_records_select('scorm_scoes_track', $select, $params, '', 'id');
    if (!empty($tracks)) {
        $updates->tracks->updated = true;
        $updates->tracks->itemids = array_keys($tracks);
    }

    // Now, teachers should see other students updates.
    if (has_capability('mod/scorm:viewreport', $cm->context)) {
        $select = 'scormid = ? AND timemodified > ?';
        $params = array($scorm->id, $from);

        if (groups_get_activity_groupmode($cm) == SEPARATEGROUPS) {
            $groupusers = array_keys(groups_get_activity_shared_group_members($cm));
            if (empty($groupusers)) {
                return $updates;
            }
            list($insql, $inparams) = $DB->get_in_or_equal($groupusers);
            $select .= ' AND userid ' . $insql;
            $params = array_merge($params, $inparams);
        }

        $updates->usertracks = (object) array('updated' => false);
        $tracks = $DB->get_records_select('scorm_scoes_track', $select, $params, '', 'id');
        if (!empty($tracks)) {
            $updates->usertracks->updated = true;
            $updates->usertracks->itemids = array_keys($tracks);
        }
    }
    return $updates;
}

/**
 * Get icon mapping for font-awesome.
 */
function mod_scorm_get_fontawesome_icon_map() {
    return [
        'mod_scorm:assetc' => 'fa-file-archive-o',
        'mod_scorm:asset' => 'fa-file-archive-o',
        'mod_scorm:browsed' => 'fa-book',
        'mod_scorm:completed' => 'fa-check-square-o',
        'mod_scorm:failed' => 'fa-times',
        'mod_scorm:incomplete' => 'fa-pencil-square-o',
        'mod_scorm:minus' => 'fa-minus',
        'mod_scorm:notattempted' => 'fa-square-o',
        'mod_scorm:passed' => 'fa-check',
        'mod_scorm:plus' => 'fa-plus',
        'mod_scorm:popdown' => 'fa-window-close-o',
        'mod_scorm:popup' => 'fa-window-restore',
        'mod_scorm:suspend' => 'fa-pause',
        'mod_scorm:wait' => 'fa-clock-o',
    ];
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every scorm event in the site is checked, else
 * only scorm events belonging to the course specified are checked.
 *
 * @param int $courseid
 * @param int|stdClass $instance scorm module instance or ID.
 * @param int|stdClass $cm Course module object or ID.
 * @return bool
 */
function scorm_refresh_events($courseid = 0, $instance = null, $cm = null) {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/mod/scorm/locallib.php');

    // If we have instance information then we can just update the one event instead of updating all events.
    if (isset($instance)) {
        if (!is_object($instance)) {
            $instance = $DB->get_record('scorm', array('id' => $instance), '*', MUST_EXIST);
        }
        if (isset($cm)) {
            if (!is_object($cm)) {
                $cm = (object)array('id' => $cm);
            }
        } else {
            $cm = get_coursemodule_from_instance('scorm', $instance->id);
        }
        scorm_update_calendar($instance, $cm->id);
        return true;
    }

    if ($courseid) {
        // Make sure that the course id is numeric.
        if (!is_numeric($courseid)) {
            return false;
        }
        if (!$scorms = $DB->get_records('scorm', array('course' => $courseid))) {
            return false;
        }
    } else {
        if (!$scorms = $DB->get_records('scorm')) {
            return false;
        }
    }

    foreach ($scorms as $scorm) {
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);
        scorm_update_calendar($scorm, $cm->id);
    }

    return true;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_scorm_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory) {
    global $CFG;

    require_once($CFG->dirroot . '/mod/scorm/locallib.php');

    $cm = get_fast_modinfo($event->courseid)->instances['scorm'][$event->instance];

    if (has_capability('mod/scorm:viewreport', $cm->context)) {
        // Teachers do not need to be reminded to complete a scorm.
        return null;
    }

    if (!empty($cm->customdata['timeclose']) && $cm->customdata['timeclose'] < time()) {
        // The scorm has closed so the user can no longer submit anything.
        return null;
    }

    // Restore scorm object from cached values in $cm, we only need id, timeclose and timeopen.
    $customdata = $cm->customdata ?: [];
    $customdata['id'] = $cm->instance;
    $scorm = (object)($customdata + ['timeclose' => 0, 'timeopen' => 0]);

    // Check that the SCORM activity is open.
    list($actionable, $warnings) = scorm_get_availability_status($scorm);

    return $factory->create_instance(
        get_string('enter', 'scorm'),
        new \moodle_url('/mod/scorm/view.php', array('id' => $cm->id)),
        1,
        $actionable
    );
}

/**
 * Add a get_coursemodule_info function in case any SCORM type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function scorm_get_coursemodule_info($coursemodule) {
    global $DB;

    $dbparams = ['id' => $coursemodule->instance];
    $fields = 'id, name, intro, introformat, completionstatusrequired, completionscorerequired, completionstatusallscos, '.
        'timeopen, timeclose';
    if (!$scorm = $DB->get_record('scorm', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $scorm->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $result->content = format_module_intro('scorm', $scorm, $coursemodule->id, false);
    }

    // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['completionstatusrequired'] = $scorm->completionstatusrequired;
        $result->customdata['customcompletionrules']['completionscorerequired'] = $scorm->completionscorerequired;
        $result->customdata['customcompletionrules']['completionstatusallscos'] = $scorm->completionstatusallscos;
    }
    // Populate some other values that can be used in calendar or on dashboard.
    if ($scorm->timeopen) {
        $result->customdata['timeopen'] = $scorm->timeopen;
    }
    if ($scorm->timeclose) {
        $result->customdata['timeclose'] = $scorm->timeclose;
    }

    return $result;
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_scorm_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (empty($cm->customdata['customcompletionrules'])
        || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    foreach ($cm->customdata['customcompletionrules'] as $key => $val) {
        switch ($key) {
            case 'completionstatusrequired':
                if (is_null($val)) {
                    continue;
                }
                // Determine the selected statuses using a bitwise operation.
                $cvalues = array();
                foreach (scorm_status_options(true) as $bit => $string) {
                    if (($val & $bit) == $bit) {
                        $cvalues[] = $string;
                    }
                }
                $statusstring = implode(', ', $cvalues);
                $descriptions[] = get_string('completionstatusrequireddesc', 'scorm', $statusstring);
                break;
            case 'completionscorerequired':
                if (is_null($val)) {
                    continue;
                }
                $descriptions[] = get_string('completionscorerequireddesc', 'scorm', $val);
                break;
            case 'completionstatusallscos':
                if (empty($val)) {
                    continue;
                }
                $descriptions[] = get_string('completionstatusallscos', 'scorm');
                break;
            default:
                break;
        }
    }
    return $descriptions;
}
