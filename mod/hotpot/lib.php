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
// GNU General Public License for more detail.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of hotpot module functions needed by Moodle core and other subsystems
 *
 * All the functions neeeded by Moodle core, gradebook, file subsystem etc
 * are placed here.
 *
 * @package    mod
 * @subpackage hotpot
 * @copyright  2009 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information if the module supports a feature
 *
 * the very latest Moodle 2.x expects "mod_hotpot_supports"
 * but since this module may also be run in early Moodle 2.x
 * we leave this function with its legacy name "hotpot_supports"
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @see init_features() in course/moodleform_mod.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function hotpot_supports($feature) {

    // these constants are defined in "lib/moodlelib.php"
    // they are not all defined in Moodle 2.0, so we
    // check each one is defined before trying to use it
    $constants = array(
        'FEATURE_ADVANCED_GRADING' => false,
        'FEATURE_BACKUP_MOODLE2'   => true, // default=false
        'FEATURE_COMMENT'          => true,
        'FEATURE_COMPLETION_HAS_RULES' => true,
        'FEATURE_COMPLETION_TRACKS_VIEWS' => true,
        'FEATURE_CONTROLS_GRADE_VISIBILITY' => false,
        'FEATURE_GRADE_HAS_GRADE'  => true, // default=false
        'FEATURE_GRADE_OUTCOMES'   => true,
        'FEATURE_GROUPINGS'        => true, // default=false
        'FEATURE_GROUPMEMBERSONLY' => true, // default=false
        'FEATURE_GROUPS'           => true,
        'FEATURE_IDNUMBER'         => true,
        'FEATURE_MOD_ARCHETYPE'    => MOD_ARCHETYPE_OTHER,
        'FEATURE_MOD_INTRO'        => false, // default=true
        'FEATURE_MODEDIT_DEFAULT_COMPLETION' => true,
        'FEATURE_NO_VIEW_LINK'     => false,
        'FEATURE_PLAGIARISM'       => false,
        'FEATURE_RATE'             => false,
        'FEATURE_SHOW_DESCRIPTION' => true, // default=false (Moodle 2.2)
        'FEATURE_USES_QUESTIONS'   => false
    );
    foreach ($constants as $constant => $value) {
        if (defined($constant) && $feature==constant($constant)) {
            return $value;
        }
    }
    return false;
}

/**
 * Saves a new instance of the hotpot into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will save a new instance and return the id number
 * of the new instance.
 *
 * @param stdclass $data An object from the form in mod_form.php
 * @return int The id of the newly inserted hotpot record
 */
function hotpot_add_instance(stdclass $data, $mform) {
    global $DB;

    hotpot_process_formdata($data, $mform);

    // insert the new record so we get the id
    $data->id = $DB->insert_record('hotpot', $data);

    // update calendar events
    hotpot_update_events_wrapper($data);

    // update gradebook item
    hotpot_grade_item_update($data);

    if (class_exists('\core_completion\api')) {
        $completiontimeexpected = (empty($data->completionexpected) ? null : $data->completionexpected);
        \core_completion\api::update_completion_date_event($data->coursemodule, 'hotpot', $data->id, $completiontimeexpected);
    }

    return $data->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdclass $data An object from the form in mod_form.php
 * @return bool success
 */
function hotpot_update_instance(stdclass $data, $mform) {
    global $DB;

    hotpot_process_formdata($data, $mform);

    $data->id = $data->instance;
    $DB->update_record('hotpot', $data);

    // update calendar events
    hotpot_update_events_wrapper($data);

    // update gradebook item
    if ($data->grademethod==$mform->get_original_value('grademethod', 0)) {
        hotpot_grade_item_update($data);
    } else {
        // recalculate grades for all users
        hotpot_update_grades($data);
    }

    if (class_exists('\core_completion\api')) {
        $completiontimeexpected = (empty($data->completionexpected) ? null : $data->completionexpected);
        \core_completion\api::update_completion_date_event($data->coursemodule, 'hotpot', $data->id, $completiontimeexpected);
    }

    return true;
}

/**
 * Set secondary fields (i.e. fields derived from the form fields)
 * for this HotPot acitivity
 *
 * @param stdclass $data (passed by reference)
 * @param moodle_form $mform
 */
function hotpot_process_formdata(stdclass &$data, $mform) {
    global $CFG;
    require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

    if ($mform->is_add()) {
        $data->timecreated = time();
    } else {
        $data->timemodified = time();
    }

    // get context for this HotPot instance
    $context = hotpot_get_context(CONTEXT_MODULE, $data->coursemodule);

    $sourcefile = null;
    $data->sourcefile = '';
    $data->sourcetype = '';
    if ($data->sourceitemid) {
        $options = hotpot::sourcefile_options();
        file_save_draft_area_files($data->sourceitemid, $context->id, 'mod_hotpot', 'sourcefile', 0, $options);

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_hotpot', 'sourcefile');

        // do we need to remove the draft files ?
        // otherwise the "files" table seems to get full of "draft" records
        // $fs->delete_area_files($context->id, 'user', 'draft', $data->sourceitemid);

        foreach ($files as $hash => $file) {
            if ($file->get_sortorder()==1) {
                $data->sourcefile = $file->get_filepath().$file->get_filename();
                $data->sourcetype = hotpot::get_sourcetype($file);
                $sourcefile = $file;
                break;
            }
        }
        unset($fs, $files, $file, $hash, $options);
    }

    if (is_null($sourcefile) || $data->sourcefile=='' || $data->sourcetype=='') {
        // sourcefile was missing or not a recognized type - shouldn't happen !!
    }

    // process text fields that may come from source file
    $source = false;
    $textfields = array('name', 'entrytext', 'exittext');
    foreach($textfields as $textfield) {

        $textsource = $textfield.'source';
        if (! isset($data->$textsource)) {
            $data->$textsource = hotpot::TEXTSOURCE_SPECIFIC;
        }

        switch ($data->$textsource) {
            case hotpot::TEXTSOURCE_FILE:
                if ($data->sourcetype && $sourcefile && empty($source)) {
                    $class = 'hotpot_source_'.$data->sourcetype;
                    $source = new $class($sourcefile, $data);
                }
                $method = 'get_'.$textfield;
                if ($source && method_exists($source, $method)) {
                    $data->$textfield = $source->$method();
                } else {
                    $data->$textfield = '';
                }
                break;
            case hotpot::TEXTSOURCE_FILENAME:
                $data->$textfield = basename($data->sourcefile);
                break;
            case hotpot::TEXTSOURCE_FILEPATH:
                $data->$textfield = str_replace(array('/', '\\'), ' ', $data->sourcefile);
                break;
            case hotpot::TEXTSOURCE_SPECIFIC:
            default:
                if (isset($data->$textfield)) {
                    $data->$textfield = trim($data->$textfield);
                } else {
                    $data->$textfield = $mform->get_original_value($textfield, '');
                }
        }

        // default activity name is simply "HotPot"
        if ($textfield=='name' && $data->$textfield=='') {
            $data->$textfield = get_string('modulename', 'mod_hotpot');
        }
    }

    // process entry/exit page settings
    foreach (hotpot::text_page_types() as $type) {

        // show page (boolean switch)
        $pagefield = $type.'page';
        if (! isset($data->$pagefield)) {
            $data->$pagefield = 0;
        }

        // set field names
        $textfield = $type.'text';
        $formatfield = $type.'format';
        $editorfield = $type.'editor';
        $sourcefield = $type.'textsource';
        $optionsfield = $type.'options';

        // ensure text, format and option fields are set
        // (these fields can't be null in the database)
        if (! isset($data->$textfield)) {
            $data->$textfield = $mform->get_original_value($textfield, '');
        }
        if (! isset($data->$formatfield)) {
            $data->$formatfield = $mform->get_original_value($formatfield, FORMAT_HTML);
        }
        if (! isset($data->$optionsfield)) {
            $data->$optionsfield = $mform->get_original_value($optionsfield, 0);
        }

        // set text and format fields
        if ($data->$sourcefield==hotpot::TEXTSOURCE_SPECIFIC) {

            // transfer wysiwyg editor text
            if ($itemid = $data->{$editorfield}['itemid']) {
                if (isset($data->{$editorfield}['text'])) {
                    // get the text that was sent from the browser
                    $editoroptions = hotpot::text_editors_options($context);
                    $text = file_save_draft_area_files($itemid, $context->id, 'mod_hotpot', $type, 0, $editoroptions, $data->{$editorfield}['text']);

                    // remove leading and trailing white space,
                    //  - empty html paragraphs (from IE)
                    //  - and blank lines (from Firefox)
                    $text = preg_replace('/^((<p>\s*<\/p>)|(<br[^>]*>)|\s)+/is', '', $text);
                    $text = preg_replace('/((<p>\s*<\/p>)|(<br[^>]*>)|\s)+$/is', '', $text);

                    $data->$textfield = $text;
                    $data->$formatfield = $data->{$editorfield}['format'];
                }
            }
        }

        // set entry/exit page options
        foreach (hotpot::text_page_options($type) as $name => $mask) {
            $optionfield = $type.'_'.$name;
            if ($data->$pagefield) {
                if (empty($data->$optionfield)) {
                    // disable this option
                    $data->$optionsfield = $data->$optionsfield & ~$mask;
                } else {
                    // enable this option
                    $data->$optionsfield = $data->$optionsfield | $mask;
                }
            }
        }

        // don't show exit page if no content is specified
        if ($type=='exit' && empty($data->$optionsfield) && empty($data->$textfield)) {
            $data->$pagefield = 0;
        }
    }

    // timelimit
    if ($data->timelimit==hotpot::TIME_SPECIFIC) {
        $data->timelimit = $data->timelimitspecific;
    }

    // delay3
    if ($data->delay3==hotpot::TIME_SPECIFIC) {
        $data->delay3 = $data->delay3specific;
    }

    // set stopbutton and stoptext
    if (empty($data->stopbutton_yesno)) {
        $data->stopbutton = hotpot::STOPBUTTON_NONE;
        $data->stoptext = $mform->get_original_value('stoptext', '');
    } else {
        if (! isset($data->stopbutton_type)) {
            $data->stopbutton_type = '';
        }
        if (! isset($data->stopbutton_text)) {
            $data->stopbutton_text = '';
        }
        if ($data->stopbutton_type=='specific') {
            $data->stopbutton = hotpot::STOPBUTTON_SPECIFIC;
            $data->stoptext = $data->stopbutton_text;
        } else {
            $data->stopbutton = hotpot::STOPBUTTON_LANGPACK;
            $data->stoptext = $data->stopbutton_type;
        }
    }

    // set review options
    $data->reviewoptions = 0;
    list($times, $items) = hotpot::reviewoptions_times_items();
    foreach ($times as $timename => $timevalue) {
        foreach ($items as $itemname => $itemvalue) {
            $name = $timename.$itemname; // e.g. duringattemptresponses
            if (isset($data->$name)) {
                if ($data->$name) {
                    $data->reviewoptions += ($timevalue & $itemvalue);
                }
                unset($data->$name);
            }
        }
    }

    // save these form settings as user preferences
    $preferences = array();
    foreach (hotpot::user_preferences_fieldnames() as $fieldname) {
        if (isset($data->$fieldname)) {
            $preferences['hotpot_'.$fieldname] = $data->$fieldname;
        }
    }
    set_user_preferences($preferences);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function hotpot_delete_instance($id) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/lib/gradelib.php');

    // check the hotpot $id is valid
    if (! $hotpot = $DB->get_record('hotpot', array('id' => $id))) {
        return false;
    }

    // delete all associated hotpot questions
    $DB->delete_records('hotpot_questions', array('hotpotid' => $hotpot->id));

    // delete all associated hotpot attempts, details and responses
    if ($attempts = $DB->get_records('hotpot_attempts', array('hotpotid' => $hotpot->id), '', 'id')) {
        $ids = array_keys($attempts);
        $DB->delete_records_list('hotpot_details',   'attemptid', $ids);
        $DB->delete_records_list('hotpot_responses', 'attemptid', $ids);
        $DB->delete_records_list('hotpot_attempts',  'id',        $ids);
    }

    // remove records from the hotpot cache
    $DB->delete_records('hotpot_cache', array('hotpotid' => $hotpot->id));

    // finally remove the hotpot record itself
    $DB->delete_records('hotpot', array('id' => $hotpot->id));

    // gradebook cleanup
    grade_update('mod/hotpot', $hotpot->course, 'mod', 'hotpot', $hotpot->id, 0, null, array('deleted' => true));

    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @global object $DB
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $hotpot
 * @return stdclass|null
 */
function hotpot_user_outline($course, $user, $mod, $hotpot) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

    $conditions = array('hotpotid'=>$hotpot->id, 'userid'=>$user->id);
    if (! $attempts = $DB->get_records('hotpot_attempts', $conditions, "timestart ASC", 'id,score,timestart')) {
        return null;
    }

    $time = 0;
    $info = null;

    $scores = array();
    foreach ($attempts as $attempt){
        if ($time==0) {
            $time = $attempt->timestart;
        }
        $scores[] = hotpot::format_score($attempt);
    }
    if (count($scores)) {
        $info = get_string('score', 'mod_hotpot').': '.implode(', ', $scores);
    } else {
        $info = get_string('noactivity', 'mod_hotpot');
    }

    return (object)array('time'=>$time, 'info'=>$info);
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return string HTML
 */
function hotpot_user_complete($course, $user, $mod, $hotpot) {
    $report = hotpot_user_outline($course, $user, $mod, $hotpot);
    if (empty($report)) {
        echo get_string("noactivity", 'mod_hotpot');
    } else {
        $date = userdate($report->time, get_string('strftimerecentfull'));
        echo $report->info.' '.get_string('mostrecently').': '.$date;
    }
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in hotpot activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @param stdclass $course
 * @param bool $viewfullnames
 * @param int $timestart
 * @return boolean
 */
function hotpot_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $DB, $OUTPUT;
    $result = false;

    // the Moodle "logs" table contains the following fields:
    //     time, userid, course, ip, module, cmid, action, url, info

    // this function utilitizes the following index on the log table
    //     log_timcoumodact_ix : time, course, module, action

    // log records are added by the following function in "lib/datalib.php":
    //     hotpot_add_to_log($courseid, $module, $action, $url='', $info='', $cm=0, $user=0)

    // log records are added by the following HotPot scripts:
    //     (scriptname : log action)
    //     attempt.php : attempt
    //     index.php   : index
    //     report.php  : report
    //     review.php  : review
    //     submit.php  : submit
    //     view.php    : view
    // all these actions have a record in the "log_display" table

    $select = "time > ? AND course = ? AND module = ? AND action IN (?, ?, ?, ?, ?)";
    $params = array($timestart, $course->id, 'hotpot', 'add', 'update', 'view', 'attempt', 'submit');

    if ($logs = $DB->get_records_select('log', $select, $params, 'time ASC')) {

        $modinfo = get_fast_modinfo($course);
        $cmids   = array_keys($modinfo->get_cms());

        $stats = array();
        foreach ($logs as $log) {
            $cmid = $log->cmid;
            if (! in_array($cmid, $cmids)) {
                continue; // invalid $cmid - shouldn't happen !!
            }
            $cm = $modinfo->get_cm($cmid);
            if (! $cm->uservisible) {
                continue; // coursemodule is hidden from user
            }
            $sortorder = array_search($cmid, $cmids);
            if (! array_key_exists($sortorder, $stats)) {
                if (has_capability('mod/hotpot:reviewmyattempts', $cm->context) || has_capability('mod/hotpot:reviewallattempts', $cm->context)) {
                    $viewreport = true;
                } else {
                    $viewreport = false;
                }
                $options = array('context' => $cm->context);
                if (method_exists($cm, 'get_formatted_name')) {
                    $name = $cm->get_formatted_name($options);
                } else {
                    $name = format_string($cm->name, true,  $options);
                }
                $stats[$sortorder] = (object)array(
                    'name'    => $name,
                    'cmid'    => $cmid,
                    'add'     => 0,
                    'update'  => 0,
                    'view'    => 0,
                    'attempt' => 0,
                    'submit'  => 0,
                    'users'   => array(),
                    'viewreport' => $viewreport
                );
            }
            $action = $log->action;
            switch ($action) {
                case 'add':
                case 'update':
                    // store most recent time
                    $stats[$sortorder]->$action = $log->time;
                    break;
                case 'view':
                case 'attempt':
                case 'submit':
                    // increment counter
                    $stats[$sortorder]->$action ++;
                    break;
            }
            $stats[$sortorder]->users[$log->userid] = true;
        }

        $strusers     = get_string('users');
        $stradded     = get_string('added',    'mod_hotpot');
        $strupdated   = get_string('updated',  'mod_hotpot');
        $strviews     = get_string('views',    'mod_hotpot');
        $strattempts  = get_string('attempts', 'mod_hotpot');
        $strsubmits   = get_string('submits',  'mod_hotpot');

        $print_headline = true;
        ksort($stats);
        foreach ($stats as $stat) {
            $li = array();
            if ($stat->add) {
                $li[] = $stradded.': '.userdate($stat->add);
            }
            if ($stat->update) {
                $li[] = $strupdated.': '.userdate($stat->update);
            }
            if ($stat->viewreport) {
                // link to a detailed report of recent activity for this hotpot
                $url = new moodle_url(
                    '/course/recent.php',
                    array('id'=>$course->id, 'modid'=>$stat->cmid, 'date'=>$timestart)
                );
                if ($count = count($stat->users)) {
                    $li[] = $strusers.': '.html_writer::link($url, $count);
                }
                if ($stat->view) {
                    $li[] = $strviews.': '.html_writer::link($url, $stat->view);
                }
                if ($stat->attempt) {
                    $li[] = $strattempts.': '.html_writer::link($url, $stat->attempt);
                }
                if ($stat->submit) {
                    $li[] = $strsubmits.': '.html_writer::link($url, $stat->submit);
                }
            }
            if (count($li)) {
                if ($print_headline) {
                    $print_headline = false;
                    echo $OUTPUT->heading(get_string('modulenameplural', 'mod_hotpot').':', 3);
                }

                $url = new moodle_url('/mod/hotpot/view.php', array('id'=>$stat->cmid));
                $link = html_writer::link($url, format_string($stat->name));

                $text = html_writer::tag('p', $link).html_writer::alist($li);
                echo html_writer::tag('div', $text, array('class'=>'hotpotrecentactivity'));

                $result = true;
            }
        }
    }
    return $result;
}

/**
 * Returns all activity in course hotpots since a given time
 * This function  returns activity for all hotpots since a given time.
 * It is initiated from the "Full report of recent activity" link in the "Recent Activity" block.
 * Using the "Advanced Search" page (cousre/recent.php?id=99&advancedfilter=1),
 * results may be restricted to a particular course module, user or group
 *
 * This function is called from: {@link course/recent.php}
 *
 * @param array(object) $activities sequentially indexed array of course module objects
 * @param integer $index length of the $activities array
 * @param integer $timestart start date, as a UNIX date
 * @param integer $courseid id in the "course" table
 * @param integer $coursemoduleid id in the "course_modules" table
 * @param integer $userid id in the "users" table (default = 0)
 * @param integer $groupid id in the "groups" table (default = 0)
 * @return void adds items into $activities and increments $index
 *     for each hotpot attempt, an $activity object is appended
 *     to the $activities array and the $index is incremented
 *     $activity->type : module type (always "hotpot")
 *     $activity->defaultindex : index of this object in the $activities array
 *     $activity->instance : id in the "hotpot" table;
 *     $activity->name : name of this hotpot
 *     $activity->section : section number in which this hotpot appears in the course
 *     $activity->content : array(object) containing information about hotpot attempts to be printed by {@link print_recent_mod_activity()}
 *         $activity->content->attemptid : id in the "hotpot_quiz_attempts" table
 *         $activity->content->attempt : the number of this attempt at this quiz by this user
 *         $activity->content->score : the score for this attempt
 *         $activity->content->timestart : the server time at which this attempt started
 *         $activity->content->timefinish : the server time at which this attempt finished
 *     $activity->user : object containing user information
 *         $activity->user->userid : id in the "user" table
 *         $activity->user->fullname : the full name of the user (see {@link lib/moodlelib.php}::{@link fullname()})
 *         $activity->user->picture : $record->picture;
 *     $activity->timestamp : the time that the content was recorded in the database
 */
function hotpot_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $coursemoduleid=0, $userid=0, $groupid=0) {
    global $CFG, $DB, $OUTPUT, $USER;

    // CONTRIB-4025 don't allow students to see each other's scores
    $coursecontext = hotpot_get_context(CONTEXT_COURSE, $courseid);
    if (! has_capability('mod/hotpot:reviewmyattempts', $coursecontext)) {
        return; // can't view recent activity
    }
    if (! has_capability('mod/hotpot:reviewallattempts', $coursecontext)) {
        $userid = $USER->id; // force this user only
    }

    // we want to detect Moodle >= 2.4
    // method_exists('course_modinfo', 'get_used_module_names')
    // method_exists('cm_info', 'get_modue_type_name')
    // method_exists('cm_info', 'is_user_access_restricted_by_capability')

    $reflector = new ReflectionFunction('get_fast_modinfo');
    if ($reflector->getNumberOfParameters() >= 3) {
        // Moodle >= 2.4 has 3rd parameter ($resetonly)
        $modinfo = get_fast_modinfo($courseid);
        $course  = $modinfo->get_course();
    } else {
        // Moodle <= 2.3
        $course = $DB->get_record('course', array('id' => $courseid));
        $modinfo = get_fast_modinfo($course);
    }
    $cms = $modinfo->get_cms();

    $hotpots = array(); // hotpotid => cmid
    $users   = array(); // cmid => array(userids)

    foreach ($cms as $cmid => $cm) {
        if ($cm->modname=='hotpot' && ($coursemoduleid==0 || $coursemoduleid==$cmid)) {
            // save mapping from hotpotid => coursemoduleid
            $hotpots[$cm->instance] = $cmid;
            // initialize array of users who have recently attempted this HotPot
            $users[$cmid] = array();
        } else {
            // we are not interested in this mod
            unset($cms[$cmid]);
        }
    }

    if (empty($hotpots)) {
        return; // no hotpots
    }

    $select = 'ha.*, (ha.timemodified - ha.timestart) AS duration, ';
    if (class_exists('user_picture')) {
        // Moodle >= 2.6
        $select .= user_picture::fields('u', null, 'useruserid');
    } else {
        // Moodle <= 2.5
        $select .= 'u.firstname, u.lastname, u.picture, u.imagealt, u.email';
    }
    $from   = '{hotpot_attempts} ha JOIN {user} u ON ha.userid = u.id';
    list($where, $params) = $DB->get_in_or_equal(array_keys($hotpots));
    $where  = 'ha.hotpotid '.$where;
    $order  = 'ha.userid, ha.attempt';

    if ($groupid) {
        // restrict search to a users from a particular group
        $from   .= ', {groups_members} gm';
        $where  .= ' AND ha.userid = gm.userid AND gm.id = ?';
        $params[] = $groupid;
    }
    if ($userid) {
        // restrict search to a single user
        $where .= ' AND ha.userid = ?';
        $params[] = $userid;
    }
    $where .= ' AND ha.timemodified > ?';
    $params[] = $timestart;

    if (! $attempts = $DB->get_records_sql("SELECT $select FROM $from WHERE $where ORDER BY $order", $params)) {
        return; // no recent attempts at these hotpots
    }

    foreach (array_keys($attempts) as $attemptid) {
        $attempt = &$attempts[$attemptid];

        if (! array_key_exists($attempt->hotpotid, $hotpots)) {
            continue; // invalid hotpotid - shouldn't happen !!
        }

        $cmid = $hotpots[$attempt->hotpotid];
        $userid = $attempt->userid;
        if (! array_key_exists($userid, $users[$cmid])) {
            $users[$cmid][$userid] = (object)array(
                'userid'   => $userid,
                'fullname' => fullname($attempt),
                'picture'  => $OUTPUT->user_picture($attempt, array('courseid' => $courseid)),
                'attempts' => array(),
            );
        }
        // add this attempt by this user at this course module
        $users[$cmid][$userid]->attempts[$attempt->attempt] = &$attempt;
    }

    foreach ($cms as $cmid => $cm) {
        if (empty($users[$cmid])) {
            continue;
        }
        // add an activity object for each user's attempts at this hotpot
        foreach ($users[$cmid] as $userid => $user) {

            // get index of last (=most recent) attempt
            $max_unumber = max(array_keys($user->attempts));

            $options = array('context' => $cm->context);
            if (method_exists($cm, 'get_formatted_name')) {
                $name = $cm->get_formatted_name($options);
            } else {
                $name = format_string($cm->name, true,  $options);
            }

            $activities[$index++] = (object)array(
                'type' => 'hotpot',
                'cmid' => $cmid,
                'name' => $name,
                'user' => $user,
                'attempts'  => $user->attempts,
                'timestamp' => $user->attempts[$max_unumber]->timemodified
            );
        }
    }
}

/**
 * Print single activity item prepared by {@see hotpot_get_recent_mod_activity()}
 *
 * This function is called from: {@link course/recent.php}
 *
 * @param object $activity an object created by {@link get_recent_mod_activity()}
 * @param integer $courseid id in the "course" table
 * @param boolean $detail
 *         true : print a link to the hotpot activity
 *         false : do no print a link to the hotpot activity
 * @param xxx $modnames
 * @param xxx $viewfullnames
 * @return no return value is required
 */
function hotpot_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
    global $CFG, $OUTPUT;
    require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

    static $dateformat = null;
    if (is_null($dateformat)) {
        $dateformat = get_string('strftimerecentfull');
    }

    $table = new html_table();
    $table->cellpadding = 3;
    $table->cellspacing = 0;

    if ($detail) {
        $row = new html_table_row();

        $cell = new html_table_cell('&nbsp;', array('width'=>15));
        $row->cells[] = $cell;

        // activity icon and link to activity
        if (method_exists($OUTPUT, 'image_icon')) {
            // Moodle >= 3.3
            $img = $OUTPUT->image_icon('icon', $modnames[$activity->type], $activity->type);
        } else {
            // Moodle <= 3.2
            $img = $OUTPUT->pix_icon('icon', $modnames[$activity->type], $activity->type);
        }

        // link to activity
        $href = new moodle_url('/mod/hotpot/view.php', array('id' => $activity->cmid));
        $link = html_writer::link($href, $activity->name);

        $cell = new html_table_cell("$img $link");
        $cell->colspan = 6;
        $row->cells[] = $cell;

        $table->data[] = new html_table_row(array(
            new html_table_cell('&nbsp;', array('width'=>15)),
            new html_table_cell("$img $link")
        ));

        $table->data[] = $row;
    }


    $row = new html_table_row();

    // set rowspan to (number of attempts) + 1
    $rowspan = count($activity->attempts) + 1;

    $cell = new html_table_cell('&nbsp;', array('width'=>15));
    $cell->rowspan = $rowspan;
    $row->cells[] = $cell;

    $cell = new html_table_cell($activity->user->picture, array('width'=>35, 'valign'=>'top', 'class'=>'forumpostpicture'));
    $cell->rowspan = $rowspan;
    $row->cells[] = $cell;

    $href = new moodle_url('/user/view.php', array('id'=>$activity->user->userid, 'course'=>$courseid));
    $cell = new html_table_cell(html_writer::link($href, $activity->user->fullname));
    $cell->colspan = 5;
    $row->cells[] = $cell;

    $table->data[] = $row;

    foreach ($activity->attempts as $attempt) {
        if ($attempt->duration) {
            $duration = '('.hotpot::format_time($attempt->duration).')';
        } else {
            $duration = '&nbsp;';
        }

        $href = new moodle_url('/mod/hotpot/review.php', array('id'=>$attempt->id));
        $link = html_writer::link($href, userdate($attempt->timemodified, $dateformat));

        $table->data[] = new html_table_row(array(
            new html_table_cell($attempt->attempt),
            new html_table_cell($attempt->score.'%'),
            new html_table_cell(hotpot::format_status($attempt->status, true)),
            new html_table_cell($link),
            new html_table_cell($duration)
        ));
    }

    echo html_writer::table($table);
}

/*
* This function defines what log actions will be selected from the Moodle logs
* and displayed for course -> report -> activity module -> HotPOt -> View OR All actions
*
* This function is called from: {@link course/report/participation/index.php}
* @return array(string) of text strings used to log HotPot view actions
*/
function hotpot_get_view_actions() {
    return array('view', 'index', 'report', 'review');
}

/*
* This function defines what log actions will be selected from the Moodle logs
* and displayed for course -> report -> activity module -> Hot Potatoes Quiz -> Post OR All actions
*
* This function is called from: {@link course/report/participation/index.php}
* @return array(string) of text strings used to log HotPot post actions
*/
function hotpot_get_post_actions() {
    return array('submit');
}

/*
 * For the given list of courses, this function creates an HTML report
 * of which HotPot activities have been completed and which have not

 * This function is called from: {@link course/lib.php}
 *
 * @param array(object) $courses records from the "course" table
 * @param array(array(string)) $htmlarray array, indexed by courseid, of arrays, indexed by module name (e,g, "hotpot), of HTML strings
 *     each HTML string shows a list of the following information about each open HotPot in the course
 *         HotPot name and link to the activity  + open/close dates, if any
 *             for teachers:
 *                 how many students have attempted/completed the HotPot
 *             for students:
 *                 which HotPots have been completed
 *                 which HotPots have not been completed yet
 *                 the time remaining for incomplete HotPots
 * @return no return value is required, but $htmlarray may be updated
 */
function hotpot_print_overview($courses, &$htmlarray) {
    global $CFG, $DB, $USER;
    require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

    if (empty($CFG->hotpot_enablemymoodle)) {
        return; // HotPots are not shown on MyMoodle on this site
    }

    if (! isset($courses) || ! is_array($courses) || ! count($courses)) {
        return; // no courses
    }

    if (! $hotpots = get_all_instances_in_courses('hotpot', $courses)) {
        return; // no hotpots
    }

    $strhotpot     = get_string('modulename', 'mod_hotpot');
    $strtimeopen   = get_string('timeopen',   'mod_hotpot');
    $strtimeclose  = get_string('timeclose',  'mod_hotpot');
    $strdateformat = get_string('strftimerecentfull');
    $strattempted  = get_string('attempted',  'mod_hotpot');
    $strcompleted  = get_string('completed',  'mod_hotpot');
    $strnotattemptedyet = get_string('notattemptedyet', 'mod_hotpot');

    $now = time();
    foreach ($hotpots as $hotpot) {

        if ($hotpot->timeopen > $now || $hotpot->timeclose < $now) {
            continue; // skip activities that are not open, or are closed
        }

        $str = ''
            .'<div class="hotpot overview">'
            .'<div class="name">'.$strhotpot. ': '
            .'<a '.($hotpot->visible ? '':' class="dimmed"')
            .'title="'.$strhotpot.'" href="'.$CFG->wwwroot
            .'/mod/hotpot/view.php?id='.$hotpot->coursemodule.'">'
            .format_string($hotpot->name).'</a></div>'
        ;
        if ($hotpot->timeopen) {
            $str .= '<div class="info">'.$strtimeopen.': '.userdate($hotpot->timeopen, $strdateformat).'</div>';
        }
        if ($hotpot->timeclose) {
            $str .= '<div class="info">'.$strtimeclose.': '.userdate($hotpot->timeclose, $strdateformat).'</div>';
        }

        $modulecontext = hotpot_get_context(CONTEXT_MODULE, $hotpot->coursemodule);
        if (has_capability('mod/hotpot:reviewallattempts', $modulecontext)) {
            // manager: show class grades stats
            // attempted: 99/99, completed: 99/99
            if ($students = get_users_by_capability($modulecontext, 'mod/hotpot:attempt', 'u.id,u.id', 'u.id', '', '', 0, '', false)) {
                $count = count($students);
                $attempted = 0;
                $completed = 0;
                // search hotpot_attempts for highest status for each userid
                list($where, $params) = $DB->get_in_or_equal(array_keys($students));
                $select = 'userid, SUM(CASE WHEN status = '.hotpot::STATUS_COMPLETED.' THEN 1 ELSE 0 END) AS iscompleted';
                $from   = '{hotpot_attempts}';
                $where  = 'userid '.$where.' AND hotpotid = ?';
                $params[] = $hotpot->id;
                if ($attempts = $DB->get_records_sql("SELECT $select FROM $from WHERE $where GROUP BY userid", $params)) {
                    $attempted = count($attempts);
                    foreach ($attempts as $attempt) {
                        if ($attempt->iscompleted) {
                            $completed++;
                        }
                    }
                }
                unset($attempts);
                unset($students);
                $str .= '<div class="info">'.$strattempted.': '.$attempted.' / '.$count.', '.$strcompleted.': '.$completed.' / '.$count.'</div>';
            }
        } else {
            // student: show grade and status
            if ($grade = hotpot_get_grades($hotpot, $USER->id, 'timestart')) {
                $grade = $grade[$USER->id];
                $href = new moodle_url('/mod/hotpot/report.php', array('hp' => $hotpot->id));
                if ($hotpot->gradeweighting) {
                    $str .= '<div class="info">'.get_string('grade').': '.'<a href="'.$href.'">'.$grade->rawgrade.'%</a></div>';
                }
                $str .= '<div class="info">'.get_string('status', 'hotpot').': '.'<a href="'.$href.'">'.hotpot::format_status($grade->maxstatus).'</a></div>';
            }
        }
        $str .= "</div>\n";

        if (empty($htmlarray[$hotpot->course]['hotpot'])) {
            $htmlarray[$hotpot->course]['hotpot'] = $str;
        } else {
            $htmlarray[$hotpot->course]['hotpot'] .= $str;
        }
    }
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function hotpot_cron () {
    return true;
}

/**
 * Returns an array of user ids who are participanting in this hotpot
 *
 * @param int $hotpotid ID of an instance of this module
 * @return array of user ids, empty if there are no participants
 */
function hotpot_get_participants($hotpotid) {
    global $DB;

    $select = 'DISTINCT u.id, u.id';
    $from   = '{user} u, {hotpot_attempts} a';
    $where  = 'u.id=a.userid AND a.hotpot=?';
    $params = array($hotpotid);

    return $DB->get_records_sql("SELECT $select FROM $from WHERE $where", $params);
}

/**
 * Is a given scale used by the instance of hotpot?
 *
 * The function asks all installed grading strategy subplugins. The hotpot
 * core itself does not use scales. Both grade for submission and grade for
 * assessments do not use scales.
 *
 * @param int $hotpotid id of hotpot instance
 * @param int $scaleid id of the scale to check
 * @return bool
 */
function hotpot_scale_used($hotpotid, $scaleid) {
    return false;
}

/**
 * Is a given scale used by any instance of hotpot?
 *
 * The function asks all installed grading strategy subplugins. The hotpot
 * core itself does not use scales. Both grade for submission and grade for
 * assessments do not use scales.
 *
 * @param int $scaleid id of the scale to check
 * @return bool
 */
function hotpot_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @return array
 */
function hotpot_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Creates or updates grade items for the given hotpot instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php.
 * Also used by {@link hotpot_update_grades()}.
 *
 * @param stdclass $hotpot instance object with extra cmidnumber and modname property
 * @return void
 */
function hotpot_grade_item_update($hotpot, $grades=null) {
    global $CFG;
    require_once($CFG->dirroot.'/lib/gradelib.php');

    // sanity check on $hotpot->id
    if (empty($hotpot->id) || empty($hotpot->course)) {
        return;
    }

    // set up params for grade_update()
    $params = array(
        'itemname' => $hotpot->name
    );
    if ($grades==='reset') {
        $params['reset'] = true;
        $grades = null;
    }
    if (isset($hotpot->cmidnumber)) {
        //cmidnumber may not be always present
        $params['idnumber'] = $hotpot->cmidnumber;
    }
    if ($hotpot->gradeweighting) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $hotpot->gradeweighting;
        $params['grademin']  = 0;
    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
        // Note: when adding a new activity, a gradeitem will *not*
        // be created in the grade book if gradetype==GRADE_TYPE_NONE
        // A gradeitem will be created later if gradetype changes to GRADE_TYPE_VALUE
        // However, the gradeitem will *not* be deleted if the activity's
        // gradetype changes back from GRADE_TYPE_VALUE to GRADE_TYPE_NONE
        // Therefore, we force the removal of empty gradeitems
        $params['deleted'] = true;
    }
    return grade_update('mod/hotpot', $hotpot->course, 'mod', 'hotpot', $hotpot->id, 0, $grades, $params);
}

/**
 * hotpot_get_grades
 *
 * @param  stdclass  $hotpot      instance object with extra cmidnumber and modname property
 * @param  integer   $userid      >0 update grade of specific user only, 0 means all participants
 * @return array     $grades
 */
function hotpot_get_grades($hotpot, $userid, $timefield='timefinish') {
    global $DB;

    if ($hotpot->grademethod==hotpot::GRADEMETHOD_AVERAGE || $hotpot->gradeweighting<100) {
        $precision = 1;
    } else {
        $precision = 0;
    }
    $weighting = $hotpot->gradeweighting / 100;

    // set the SQL string to determine the $grade
    switch ($hotpot->grademethod) {
        case hotpot::GRADEMETHOD_HIGHEST:
            $gradefield = "ROUND(MAX(score) * $weighting, $precision) AS rawgrade";
            break;
        case hotpot::GRADEMETHOD_AVERAGE:
            // the 'AVG' function skips abandoned quizzes, so use SUM(score)/COUNT(id)
            $gradefield = "ROUND(SUM(score)/COUNT(id) * $weighting, $precision) AS rawgrade";
            break;
        case hotpot::GRADEMETHOD_FIRST:
            $gradefield = "ROUND(score * $weighting, $precision)";
            $gradefield = $DB->sql_concat('timestart', "'_'", $gradefield);
            $gradefield = "MIN($gradefield) AS rawgrade";
            break;
        case hotpot::GRADEMETHOD_LAST:
            $gradefield = "ROUND(score * $weighting, $precision)";
            $gradefield = $DB->sql_concat('timestart', "'_'", $gradefield);
            $gradefield = "MAX($gradefield) AS rawgrade";
            break;
        default:
            return false; // shouldn't happen !!
    }
    $statusfield = 'MAX(status) AS maxstatus';

    $select = "$timefield > ? AND hotpotid= ?";
    $params = array(0, $hotpot->id);
    if ($userid) {
        $select .= ' AND userid = ?';
        $params[] = $userid;
    }
    $sql = "SELECT userid, $gradefield, $statusfield FROM {hotpot_attempts} WHERE $select GROUP BY userid";

    $grades = array();
    if ($aggregates = $DB->get_records_sql($sql, $params)) {
        foreach ($aggregates as $hotpotuserid => $aggregate) {
            if ($hotpot->grademethod==hotpot::GRADEMETHOD_FIRST || $hotpot->grademethod==hotpot::GRADEMETHOD_LAST) {
                // remove left hand characters in $gradefield (up to and including the underscore)
                $pos = strpos($aggregate->rawgrade, '_') + 1;
                $aggregate->rawgrade = substr($aggregate->rawgrade, $pos);
            }
            $grades[$hotpotuserid] = (object)array('userid'=>$hotpotuserid, 'rawgrade'=>$aggregate->rawgrade, 'maxstatus' => $aggregate->maxstatus);
        }
    }
    return $grades;
}

/**
 * Update hotpot grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdclass  $hotpot      instance object with extra cmidnumber and modname property
 * @param integer   $userid      >0 update grade of specific user only, 0 means all participants
 * @param boolean   $nullifnone  TRUE = force creation of NULL grade if this user has no grade
 * @return boolean  TRUE if successful, FALSE otherwise
 * @return void
 */
function hotpot_update_grades($hotpot=null, $userid=0, $nullifnone=true) {
    global $CFG, $DB;

    // get hotpot object
    require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

    if ($hotpot===null) {
        // update/create grades for all hotpots

        // set up sql strings
        $strupdating = get_string('updatinggrades', 'mod_hotpot');
        $select = 'h.*, cm.idnumber AS cmidnumber';
        $from   = '{hotpot} h, {course_modules} cm, {modules} m';
        $where  = 'h.id = cm.instance AND cm.module = m.id AND m.name = ?';
        $params = array('hotpot');

        // get previous record index (if any)
        $configname = 'update_grades';
        $configvalue = get_config('mod_hotpot', $configname);
        if (is_numeric($configvalue)) {
            $i_min = intval($configvalue);
        } else {
            $i_min = 0;
        }

        if ($i_max = $DB->count_records_sql("SELECT COUNT('x') FROM $from WHERE $where", $params)) {
            if ($rs = $DB->get_recordset_sql("SELECT $select FROM $from WHERE $where", $params)) {
                if (defined('CLI_SCRIPT') && CLI_SCRIPT) {
                    $bar = false;
                } else {
                    $bar = new progress_bar('hotpotupgradegrades', 500, true);
                }
                $i = 0;
                foreach ($rs as $hotpot) {

                    // update grade
                    if ($i >= $i_min) {
                        upgrade_set_timeout(); // apply for more time (3 mins)
                        hotpot_update_grades($hotpot, $userid, $nullifnone);
                    }

                    // update progress bar
                    $i++;
                    if ($bar) {
                        $bar->update($i, $i_max, $strupdating.": ($i/$i_max)");
                    }

                    // update record index
                    if ($i > $i_min) {
                        set_config($configname, $i, 'mod_hotpot');
                    }
                }
                $rs->close();
            }
        }

        // delete the record index
        unset_config($configname, 'mod_hotpot');

        return; // finish here
    }

    // sanity check on $hotpot->id
    if (! isset($hotpot->id)) {
        return false;
    }

    $grades = hotpot_get_grades($hotpot, $userid);

    if (count($grades)) {
        hotpot_grade_item_update($hotpot, $grades);

    } else if ($userid && $nullifnone) {
        // no grades for this user, but we must force the creation of a "null" grade record
        hotpot_grade_item_update($hotpot, (object)array('userid'=>$userid, 'rawgrade'=>null));

    } else {
        // no grades and no userid
        hotpot_grade_item_update($hotpot);
    }
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area hotpot_intro for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdclass $course
 * @param stdclass $cm
 * @param stdclass $context
 * @return array of [(string)filearea] => (string)description
 */
function hotpot_get_file_areas($course, $cm, $context) {
    return array(
        'entry'      => get_string('entrytext',  'mod_hotpot'),
        'exit'       => get_string('exittext',   'mod_hotpot'),
        'sourcefile' => get_string('sourcefile', 'mod_hotpot')
    );
}

/**
 * Serves the plugin files from the specified $filearea
 *
 * @package  mod_hotpot
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
function hotpot_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options=array()) {
    global $CFG;

    require_course_login($course, true, $cm);

    switch ($filearea) {
        case 'entry':      $capability = 'mod/hotpot:view'; break;
        case 'exit':       $capability = 'mod/hotpot:attempt'; break;
        case 'sourcefile': $capability = 'mod/hotpot:attempt'; break;
        default: send_file_not_found(); // invalid $filearea !!
    }

    require_capability($capability, $context);

    $fs = get_file_storage();
    $component = 'mod_hotpot';
    $filename = array_pop($args);
    $filepath = $args ? '/'.implode('/', $args).'/' : '/';

    // Note: $lifetime is the frequency at which files are synched
    if (isset($CFG->filelifetime)) {
        $lifetime =  $CFG->filelifetime;
    } else {
        $lifetime =  DAYSECS; // DAYSECS = 86400 secs = 24 hours
    }
    $filter   = 0; // don't apply filters

    if ($file = $fs->get_file($context->id, $component, $filearea, 0, $filepath, $filename)) {
        // file found - this is what we expect to happen
        send_stored_file($file, $lifetime, $filter, $forcedownload, $options);
    }

    /////////////////////////////////////////////////////////////
    // If we get to this point, it is because the requested file
    // is not where is was supposed to be, so we will search for
    // it in some other likely locations.
    // If we find it, we will copy it across to where it is
    // supposed to be, so it can be found more quickly next time
    /////////////////////////////////////////////////////////////

    $file_record = array(
        'contextid'=>$context->id, 'component'=>$component, 'filearea'=>$filearea,
        'sortorder'=>0, 'itemid'=>0, 'filepath'=>$filepath, 'filename'=>$filename
    );

    // search in external directory
    if ($file = hotpot_pluginfile_externalfile($context, $component, $filearea, $filepath, $filename, $file_record)) {
        send_stored_file($file, $lifetime, $filter, $forcedownload, $options);
    }

    // search course legacy files
    $coursecontext = hotpot_get_context(CONTEXT_COURSE, $course->id);
    if ($file = $fs->get_file($coursecontext->id, 'course', 'legacy', 0, $filepath, $filename)) {
        if ($file = $fs->create_file_from_storedfile($file_record, $file)) {
            //send_stored_file($file, $lifetime, 0);
            send_stored_file($file, $lifetime, $filter, $forcedownload, $options);
        }
    }

    // search local file system
    $oldfilepath = $CFG->dataroot.'/'.$course->id.$filepath.$filename;
    if (file_exists($oldfilepath)) {
        if ($file = $fs->create_file_from_pathname($file_record, $oldfilepath)) {
            send_stored_file($file, $lifetime, 0);
        }
    }

    // search other fileareas for this HotPot
    $hotpot_fileareas = hotpot_get_file_areas($course, $cm, $context);
    $hotpot_fileareas = array_keys($hotpot_fileareas);
    foreach($hotpot_fileareas as $hotpot_filearea) {
        if ($hotpot_filearea==$filearea) {
            continue; // we have already checked this filearea
        }
        if ($file = $fs->get_file($context->id, $component, $hotpot_filearea, 0, $filepath, $filename)) {
            if ($file = $fs->create_file_from_storedfile($file_record, $file)) {
                send_stored_file($file, $lifetime, 0);
            }
        }
    }

    // file not found :-(
    send_file_not_found();
}

/**
 * Gets main file in a file area
 *
 * if the main file is a link from an external repository
 * look for the target file in the main file's repository
 * Note: this functionality only exists in Moodle 2.3+
 *
 * @param stdclass $context
 * @param string $component 'mod_hotpot'
 * @param string $filearea  'sourcefile', 'entrytext' or 'exittext'
 * @param string $filepath  despite the name, this is a dir path with leading and trailing "/"
 * @param string $filename
 * @return stdclass if external file found, false otherwise
 */
function hotpot_pluginfile_externalfile($context, $component, $filearea, $filepath, $filename) {

    // get file storage
    $fs = get_file_storage();

    // get main file for this $component/$filearea
    // typically this will be the HotPot quiz file
    $mainfile = hotpot_pluginfile_mainfile($context, $component, $filearea);

    // get repository - cautiously :-)
    if (! $mainfile) {
        return false; // no main file - shouldn't happen !!
    }
    if (! method_exists($mainfile, 'get_repository_id')) {
        return false; // no file linking in Moodle 2.0 - 2.2
    }
    if (! $repositoryid = $mainfile->get_repository_id()) {
        return false; // $mainfile is not from an external repository
    }
    if (! $repository = repository::get_repository_by_id($repositoryid, $context)) {
        return false; // $repository is not accessible in this context - shouldn't happen !!
    }

    // get repository type
    switch (true) {
        case isset($repository->options['type']):
            $type = $repository->options['type'];
            break;
        case isset($repository->instance->typeid):
            $type = repository::get_type_by_id($repository->instance->typeid);
            $type = $type->get_typename();
            break;
        default:
            $type = ''; // shouldn't happen !!
    }

    // "user" and "coursefiles" repositories
    // will set this flag to TRUE
    $encodepath = false;

    // "filesytem" repository on Moodle >= 3.1
    // will set this flag to 'browse'
    $nodepathmode = '';

    // set paths (within repository) to required file
    // how we do this depends on the repository $typename
    // "filesystem" path is in plain text, others are encoded

    $mainreference = $mainfile->get_reference();
    switch ($type) {
        case 'filesystem':
            $maindirname = dirname($mainreference);
            if (method_exists($repository, 'build_node_path')) {
                $nodepathmode = 'browse';
            }
            break;
        case 'coursefiles':
        case 'user':
            $params      = file_storage::unpack_reference($mainreference, true);
            $maindirname = $params['filepath'];
            $encodepath  = true;
            break;
        default:
            echo 'unknown repository type in hotpot_pluginfile_externalfile(): '.$type;
            die;
    }

    // remove leading and trailing "/" from dir names
    $maindirname = trim($maindirname, '/');
    $dirname = trim($filepath, '/');

    // assume path to target dir is same as path to main dir
    $path = explode('/', $maindirname);

    // traverse back up folder hierarchy if necessary
    $count = count(explode('/', $dirname));
    array_splice($path, -$count);

    // reconstruct expected dir path for source file
    if ($dirname) {
        $path[] = $dirname;
    }
    $source = $path;
    $source[] = $filename;
    $source = implode('/', $source);
    $path = implode('/', $path);

    // filepaths in the repository to search for the file
    $paths = array();

    // add to the list of possible paths
    $paths[$path] = $source;

    if ($dirname) {
        $paths[$dirname] = $dirname.'/'.$filename;
    }
    if ($maindirname) {
        $paths[$maindirname] = $maindirname.'/'.$filename;
    }
    if ($maindirname && $dirname) {
        $paths[$maindirname.'/'.$dirname] = $maindirname.'/'.$dirname.'/'.$filename;
        $paths[$dirname.'/'.$maindirname] = $dirname.'/'.$maindirname.'/'.$filename;
    }

    // add leading and trailing "/" to dir names
    $dirname = ($dirname=='' ? '/' : '/'.$dirname.'/');
    $maindirname = ($maindirname=='' ? '/' : '/'.$maindirname.'/');

    // locate $dirname within $maindirname
    // typically it will be absent or occur just once,
    // but it could possibly occur several times
    $search = '/'.preg_quote($dirname, '/').'/i';
    if (preg_match_all($search, $maindirname, $matches, PREG_OFFSET_CAPTURE)) {

        $i_max = count($matches[0]);
        for ($i=0; $i<$i_max; $i++) {
            list($match, $start) = $matches[0][$i];
            $path = substr($maindirname, 0, $start).$match;
            $path = trim($path, '/'); // e.g. hp6.2/html_files
            $paths[$path] = $path.'/'.$filename;
        }
    }

    // setup $params for path encoding, if necessary
    $params = array();
    if ($encodepath) {
        $listing = $repository->get_listing();
        switch (true) {
            case isset($listing['list'][0]['source']): $param = 'source'; break; // file
            case isset($listing['list'][0]['path']):   $param = 'path';   break; // dir
            default: return false; // shouldn't happen !!
        }
        $params = $listing['list'][0][$param];
        $params = json_decode(base64_decode($params), true);
    }

    foreach ($paths as $path => $source) {

        if (! hotpot_pluginfile_dirpath_exists($path, $repository, $type, $encodepath, $nodepathmode, $params)) {
            continue;
        }

        if ($encodepath) {
            $params['filepath'] = '/'.$path.($path=='' ? '' : '/');
            $params['filename'] = '.'; // "." signifies a directory
            $path = base64_encode(json_encode($params));
        }

        if ($nodepathmode) {
            // for "filesystem" repository on Moodle >= 3.1
            // the following code mimics the protected method
            // $repository->build_node_path($nodepathmode, $dirpath)
            $path = $nodepathmode.':'.base64_encode($path).':';
        }

        $listing = $repository->get_listing($path);
        foreach ($listing['list'] as $file) {

            switch (true) {
                case isset($file['source']): $param = 'source'; break; // file
                case isset($file['path']):   $param = 'path';   break; // dir
                default: continue; // shouldn't happen !!
            }

            if ($encodepath) {
                $file[$param] = json_decode(base64_decode($file[$param]), true);
                $file[$param] = trim($file[$param]['filepath'], '/').'/'.$file[$param]['filename'];
            }

            if ($file[$param]==$source) {

                if ($encodepath) {
                    $params['filename'] = $filename;
                    $source = file_storage::pack_reference($params);
                }

                $file_record = array(
                    'contextid' => $context->id, 'component' => $component, 'filearea' => $filearea,
                    'sortorder' => 0, 'itemid' => 0, 'filepath' => $filepath, 'filename' => $filename
                );

                if ($file = $fs->create_file_from_reference($file_record, $repositoryid, $source)) {
                    return $file;
                }
                break; // couldn't create file, so give up and try a different $path
            }
        }
    }

    // external file not found (or found but not created)
    return false;
}

/**
 * Determine if dir path exists or not in repository
 *
 * @param string   $dirpath
 * @param stdclass $repository
 * @param string   $type ("user" or "coursefiles")
 * @param boolean  $encodepath
 * @param array    $params
 * @return boolean true if dir path exists in repository, false otherwise
 */
function hotpot_pluginfile_dirpath_exists($dirpath, $repository, $type, $encodepath, $nodepathmode, $params) {
    $dirs = explode('/', $dirpath);
    foreach ($dirs as $i => $dir) {
        $dirpath = implode('/', array_slice($dirs, 0, $i));

        if ($encodepath) {
            $params['filepath'] = '/'.$dirpath.($dirpath=='' ? '' : '/');
            $params['filename'] = '.'; // "." signifies a directory
            $dirpath = base64_encode(json_encode($params));
        }

        if ($nodepathmode) {
            // for "filesystem" repository on Moodle >= 3.1
            // the following code mimics the protected method
            // $repository->build_node_path($nodepathmode, $dirpath)
            $dirpath = $nodepathmode.':'.base64_encode($dirpath).':';
        }

        $exists = false;
        $listing = $repository->get_listing($dirpath);
        foreach ($listing['list'] as $file) {
            if (empty($file['source']) && $file['title']==$dir) {
                $exists = true;
                break;
            }
        }
        if (! $exists) {
            return false;
        }
    }
    // all dirs in path exist - success !!
    return true;
}

/**
 * Gets main file in a file area
 *
 * @param stdclass $context
 * @param string $component e.g. 'mod_hotpot'
 * @param string $filearea
 * @return stdclass if main file found, false otherwise
 */
function hotpot_pluginfile_mainfile($context, $component, $filearea) {
    $fs = get_file_storage();

    // the main file for this HotPot activity
    // (file with lowest sortorder in $filearea)
    $mainfile = false;

    // these file types can't be the mainfile
    $media_filetypes = array('fla', 'flv', 'gif', 'jpeg', 'jpg', 'mp3', 'png', 'swf', 'wav');

    $area_files = $fs->get_area_files($context->id, $component, $filearea);
    foreach ($area_files as $file) {
        if ($file->is_directory()) {
            continue;
        }
        $filename = $file->get_filename();
        if (substr($filename, 0, 1)=='.') {
            continue; // hidden file
        }
        $filetype = strtolower(substr($filename, -3));
        if (in_array($filetype, $media_filetypes)) {
            continue; // media file
        }
        if (empty($mainfile)) { // || $mainfile->get_content()==''
            $mainfile = $file;
        } else if ($file->get_sortorder()==0) {
            // unsorted file - do nothing
        } else if ($mainfile->get_sortorder()==0) {
            $mainfile = $file;
        } else if ($file->get_sortorder() < $mainfile->get_sortorder()) {
            $mainfile = $file;
        }
    }

    return $mainfile;
}

/**
 * Serves the files from the hotpot file areas
 *
 * hotpot files may be media inserted into entrypage, exitpage and sourcefile content
 *
 * @param stdclass $course
 * @param stdclass $cm
 * @param stdclass $context
 * @param string $filearea
 * @param array $args filepath split into folder and file names
 * @param bool $forcedownload
 * @param array $options
 * @return void this should never return to the caller
 */
function mod_hotpot_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options=array()) {
    hotpot_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options);
}

/**
 * File browsing support for hotpot file areas
 *
 * @param stdclass $browser
 * @param stdclass $areas
 * @param stdclass $course
 * @param stdclass $cm
 * @param stdclass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return stdclass file_info instance or null if not found
 */
function hotpot_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding hotpot nodes if there is a relevant content
 * These settings are added to the "Navigation" menu
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the hotpot module instance
 * @param stdclass $course
 * @param stdclass $module
 * @param cm_info  $cm
 */
function hotpot_extend_navigation(navigation_node $hotpotnode, stdclass $course, stdclass $module, cm_info $cm) {
    global $CFG, $DB;

    // don't add nodes in Moodle >= 2.5, because they will
    // be added to the Administration menu by
    // "hotpot_extend_settings_navigation()" ... see below
    if (isset($CFG->branch) && $CFG->branch >= '25') {
        return;
    }

    // make sure we have HotPot's locallib.php
    require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

    $hotpot = $DB->get_record('hotpot', array('id' => $cm->instance), '*', MUST_EXIST);
    $hotpot = hotpot::create($hotpot, $cm, $course);

    if ($hotpot->can_preview()) {
        $text = get_string('preview');
        $action = $hotpot->attempt_url();
        $type = navigation_node::TYPE_SETTING;
        $icon = new pix_icon('t/preview', '');
        $hotpotnode->add($text, $action, $type, null, 'preview', $icon);
    }

    if ($hotpot->can_reviewattempts()) {
        $type = navigation_node::TYPE_SETTING;

        // create report parent node
        $modes = $hotpot->get_report_modes();
        $mode = key($modes); // first report
        $params = array('text' => get_string('reports'),
                        'action' => $hotpot->report_url($mode),
                        'key' => 'reports',
                        'type' => $type,
                        'icon' => new pix_icon('i/report', ''));
        $node = new navigation_node($params);

        $icon = new pix_icon('i/item', '');
        foreach ($modes as $mode) {
            $text = get_string($mode.'report', 'mod_hotpot');
            $action = $hotpot->report_url($mode);
            $node->add($text, $action, $type, null, $mode.'report', $icon);
        }
        if (method_exists($hotpotnode, 'add_node')) {
            $hotpotnode->add_node($node); // Moodle >= 2.2
        } else {
            $node->key = $hotpotnode->children->count();
            $hotpotnode->nodetype = navigation_node::NODETYPE_BRANCH;
            $hotpotnode->children->add($node);
        }
    }
}

/**
 * Extends the settings navigation with the Hotpot settings
 * These settings are added to the "Administration" menu

 * This function is called when the context for the page is a hotpot module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $hotpotnode {@link navigation_node}
 */
function hotpot_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $hotpotnode=null) {
    global $CFG, $DB, $PAGE;

    // don't add nodes in Moodle <= 2.4, because they were
    // already added to the Navigation menu by
    // "hotpot_settings_navigation()" ... see above
    if (empty($CFG->branch) || $CFG->branch <= '24') {
        return;
    }

    require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

    $hotpot = $DB->get_record('hotpot', array('id' => $PAGE->cm->instance), '*', MUST_EXIST);
    $hotpot = hotpot::create($hotpot, $PAGE->cm, $PAGE->course);

    $nodes = array();

    if ($hotpot->can_preview()) {
        $params = array('text' => get_string('preview'),
                        'action' => $hotpot->attempt_url(),
                        'key' => 'preview',
                        'type' => navigation_node::TYPE_SETTING,
                        'icon' => new pix_icon('t/preview', ''));
        $nodes[] = new navigation_node($params);
    }

    if ($hotpot->can_reviewattempts()) {
        $type = navigation_node::TYPE_SETTING;

        // create report parent node
        $modes = $hotpot->get_report_modes();
        $mode = key($modes); // first report
        $params = array('text' => get_string('reports'),
                        'action' => $hotpot->report_url($mode),
                        'key' => 'reports',
                        'type' => $type,
                        'icon' => new pix_icon('i/report', ''));
        $node = new navigation_node($params);

        // add reports
        $icon = new pix_icon('i/item', '');
        foreach ($modes as $mode) {
            $action = $hotpot->report_url($mode);
            $text = get_string($mode.'report', 'mod_hotpot');
            $node->add($text, $action, $type, null, $mode.'report', $icon);
        }
        $nodes[] = $node;
    }

    // only teachers/admins will have new nodes to add
    if (count($nodes)) {

        // We want to add these new nodes after the Edit settings node,
        // and before the locally assigned roles node.

        // detect Moodle >= 2.2 (it has an easy way to do what we want)
        if (method_exists($hotpotnode, 'get_children_key_list')) {

            // in Moodle >= 2.2, we can locate the "Edit settings" node by its key, and
            // use the key for the node AFTER that as the "beforekey" for the new nodes
            $keys = $hotpotnode->get_children_key_list();
            $key = 'modedit';
            $i = array_search($key, $keys);
            if ($i===false) {
                $i = 0; // shouldn't happen !!
            } else {
                $i = ($i + 1);
                $icon = new pix_icon('t/edit', '');
                $type = navigation_node::TYPE_SETTING;
                $hotpotnode->find($key, $type)->icon = $icon;
            }
            if (array_key_exists($i, $keys)) {
                $beforekey = $keys[$i];
            } else {
                $beforekey = null;
            }
            foreach ($nodes as $node) {
                $hotpotnode->add_node($node, $beforekey);
            }

        } else {
            // in Moodle 2.0 - 2.1, we don't have the $beforekey functionality,
            // so instead, we create a new collection of child nodes by copying
            // the current child nodes one by one and inserting our news nodes
            // after the node whose plain url ends with "/course/modedit.php"
            // Note: this would also work on Moodle >= 2.2, but is obviously
            // rather a hack and not the way things should to be done
            $found = false;
            $children = new navigation_node_collection();
            $max_i = ($hotpotnode->children->count() - 1);
            foreach ($hotpotnode->children as $i => $child) {
                $children->add($child);
                if ($found==false) {
                    $action = $child->action->out_omit_querystring();
                    if (($i==$max_i) || substr($action, -19)=='/course/modedit.php') {
                        $found = true;
                        foreach ($nodes as $node) {
                            $children->add($node);
                        }
                    }
                }
            }
            $hotpotnode->children = $children;
        }
    }
}

////////////////////////////////////////////////////////////////////////////////
// Reset API                                                                  //
////////////////////////////////////////////////////////////////////////////////

/**
 * hotpot_reset_course_form_definition
 *
 * @param xxx $mform (passed by reference)
 */
function hotpot_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'hotpotheader', get_string('modulenameplural', 'mod_hotpot'));
    $mform->addElement('checkbox', 'reset_hotpot_deleteallattempts', get_string('deleteallattempts', 'mod_hotpot'));
}

/**
 * hotpot_reset_course_form_defaults
 *
 * @param xxx $course
 * @return xxx
 */
function hotpot_reset_course_form_defaults($course) {
    return array('reset_hotpot_deleteallattempts' => 1);
}

/**
 * hotpot_reset_gradebook
 *
 * @param xxx $courseid
 * @param xxx $type (optional, default='')
 */
function hotpot_reset_gradebook($courseid, $type='') {
    global $DB;
    $sql = ''
        .'SELECT h.*, cm.idnumber AS cmidnumber, cm.course AS courseid '
        .'FROM {hotpot} h, {course_modules} cm, {modules} m '
        ."WHERE m.name='hotpot' AND m.id=cm.module AND cm.instance=h.id AND h.course=?"
    ;
    if ($hotpots = $DB->get_records_sql($sql, array($courseid))) {
        foreach ($hotpots as $hotpot) {
            hotpot_grade_item_update($hotpot, 'reset');
        }
    }
}

/**
 * hotpot_reset_userdata
 *
 * @param xxx $data
 * @return xxx
 */
function hotpot_reset_userdata($data) {
    global $DB;

    if (empty($data->reset_hotpot_deleteallattempts)) {
        return array();
    }

    if ($hotpots = $DB->get_records('hotpot', array('course' => $data->courseid), 'id', 'id')) {
        foreach ($hotpots as $hotpot) {
            if ($attempts = $DB->get_records('hotpot_attempts', array('hotpotid' => $hotpot->id), 'id', 'id')) {
                $ids = array_keys($attempts);
                $DB->delete_records_list('hotpot_details',   'attemptid', $ids);
                $DB->delete_records_list('hotpot_responses', 'attemptid', $ids);
                $DB->delete_records_list('hotpot_attempts',  'id',        $ids);
            }
        }
    }

    return array(array(
        'component' => get_string('modulenameplural', 'mod_hotpot'),
        'item' => get_string('deleteallattempts', 'mod_hotpot'),
        'error' => false
    ));
}

/*
* This standard function will check all instances of this module
* and make sure there are up-to-date events created for each of them.
* If courseid = 0, then every hotpot event in the site is checked, else
* only hotpot events belonging to the course specified are checked.
* This function is used, in its new format, by restore_refresh_events()
* in backup/backuplib.php
*
* @param int $courseid : relative path (below $CFG->dirroot) of folder holding class definitions
*/
function hotpot_refresh_events($courseid=0) {
    global $CFG, $DB;

    if ($courseid && is_numeric($courseid)) {
        $params = array('course'=>$courseid);
    } else {
        $params = array();
    }
    if (! $hotpots = $DB->get_records('hotpot', $params)) {
        return true; // no hotpots
    }

    // get previous ids for events for these hotpots
    list($filter, $params) = $DB->get_in_or_equal(array_keys($hotpots));
    if ($eventids = $DB->get_records_select('event', "modulename='hotpot' AND instance $filter", $params, 'id', 'id')) {
        $eventids = array_keys($eventids);
    } else {
        $eventids = array();
    }

    // we're going to count the hotpots so we can detect the last one
    $i = 0;
    $count = count($hotpots);

    // add events for these hotpot units
    // eventids will be reused where possible
    foreach ($hotpots as $hotpot) {
        $i++;
        $delete = ($i==$count);
        hotpot_update_events($hotpot, $eventids, $delete);
    }

    // all done
    return true;
}

/**
 * Update calendar events for a single HotPot activity
 * This function is intended to be called just after
 * a HotPot activity has been created or edited.
 *
 * @param xxx $hotpot
 */
function hotpot_update_events_wrapper($hotpot) {
    global $DB;
    if ($eventids = $DB->get_records('event', array('modulename'=>'hotpot', 'instance'=>$hotpot->id), 'id', 'id')) {
        $eventids = array_keys($eventids);
    } else {
        $eventids = array();
    }
    hotpot_update_events($hotpot, $eventids, true);
}

/**
 * hotpot_update_events
 *
 * @param xxx $hotpot (passed by reference)
 * @param xxx $eventids (passed by reference)
 * @param xxx $delete
 */
function hotpot_update_events(&$hotpot, &$eventids, $delete) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/calendar/lib.php');

    static $stropens = '';
    static $strcloses = '';
    static $maxduration = null;

    // check to see if this user is allowed
    // to manage calendar events in this course
    $capability = 'moodle/calendar:manageentries';
    if (has_capability($capability, hotpot_get_context(CONTEXT_SYSTEM))) {
        $can_manage_events = true; // site admin
    } else if (has_capability($capability, hotpot_get_context(CONTEXT_COURSE, $hotpot->course))) {
        $can_manage_events = true; // course admin/teacher
    } else {
        $can_manage_events = false; // not allowed to add/edit calendar events !!
    }

    // don't check calendar capabiltiies
    // whwne adding or updating events
    $checkcapabilties = false;

    // cache text strings and max duration (first time only)
    if (is_null($maxduration)) {
        if (isset($CFG->hotpot_maxeventlength)) {
            $maxeventlength = $CFG->hotpot_maxeventlength;
        } else {
            $maxeventlength = 5; // 5 days is default
        }
        // set $maxduration (secs) from $maxeventlength (days)
        $maxduration = $maxeventlength * 24 * 60 * 60;

        $stropens = get_string('activityopens', 'mod_hotpot');
        $strcloses = get_string('activitycloses', 'mod_hotpot');
    }

    // array to hold events for this hotpot
    $events = array();

    // only setup calendar events,
    // if this user is allowed to
    if ($can_manage_events) {

        // set duration
        if ($hotpot->timeclose && $hotpot->timeopen) {
            $duration = max(0, $hotpot->timeclose - $hotpot->timeopen);
        } else {
            $duration = 0;
        }

        if ($duration > $maxduration) {
            // long duration, two events
            $events[] = (object)array(
                'name' => $hotpot->name.' ('.$stropens.')',
                'eventtype' => 'open',
                'timestart' => $hotpot->timeopen,
                'timeduration' => 0
            );
            $events[] = (object)array(
                'name' => $hotpot->name.' ('.$strcloses.')',
                'eventtype' => 'close',
                'timestart' => $hotpot->timeclose,
                'timeduration' => 0
            );
        } else if ($duration) {
            // short duration, just a single event
            if ($duration < DAYSECS) {
                // less than a day (1:07 p.m.)
                $fmt = get_string('strftimetime');
            } else if ($duration < WEEKSECS) {
                // less than a week (Thu, 13:07)
                $fmt = get_string('strftimedaytime');
            } else if ($duration < YEARSECS) {
                // more than a week (2 Feb, 13:07)
                $fmt = get_string('strftimerecent');
            } else {
                // more than a year (Thu, 2 Feb 2012, 01:07 pm)
                $fmt = get_string('strftimerecentfull');
            }
            $events[] = (object)array(
                'name' => $hotpot->name.' ('.userdate($hotpot->timeopen, $fmt).' - '.userdate($hotpot->timeclose, $fmt).')',
                'eventtype' => 'open',
                'timestart' => $hotpot->timeopen,
                'timeduration' => $duration,
            );
        } else if ($hotpot->timeopen) {
            // only an open date
            $events[] = (object)array(
                'name' => $hotpot->name.' ('.$stropens.')',
                'eventtype' => 'open',
                'timestart' => $hotpot->timeopen,
                'timeduration' => 0,
            );
        } else if ($hotpot->timeclose) {
            // only a closing date
            $events[] = (object)array(
                'name' => $hotpot->name.' ('.$strcloses.')',
                'eventtype' => 'close',
                'timestart' => $hotpot->timeclose,
                'timeduration' => 0,
            );
        }
    }

    // cache description and visiblity (saves doing it twice for long events)
    if (empty($hotpot->entrytext)) {
        $description = '';
    } else {
        $description = $hotpot->entrytext;
    }
    $visible = instance_is_visible('hotpot', $hotpot);

    foreach ($events as $event) {
        $event->groupid = 0;
        $event->userid = 0;
        $event->courseid = $hotpot->course;
        $event->modulename = 'hotpot';
        $event->instance = $hotpot->id;
        $event->description = $description;
        $event->visible = $visible;
        if (count($eventids)) {
            $event->id = array_shift($eventids);
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event, $checkcapabilties);
        } else {
            calendar_event::create($event, $checkcapabilties);
        }
    }

    // delete surplus events, if required
    // (no need to check capabilities here)
    if ($delete) {
        while (count($eventids)) {
            $id = array_shift($eventids);
            $event = calendar_event::load($id);
            $event->delete();
        }
    }
}

/**
 * context
 *
 * a wrapper method to offer consistent API to get contexts
 * in Moodle 2.0 and 2.1, we use get_context_instance() function
 * in Moodle >= 2.2, we use static context_xxx::instance() method
 *
 * @param integer $contextlevel
 * @param integer $instanceid (optional, default=0)
 * @param int $strictness (optional, default=0 i.e. IGNORE_MISSING)
 * @return required context
 * @todo Finish documenting this function
 */
function hotpot_get_context($contextlevel, $instanceid=0, $strictness=0) {
    if (class_exists('context_helper')) {
        // use call_user_func() to prevent syntax error in PHP 5.2.x
        // return $classname::instance($instanceid, $strictness);
        $class = context_helper::get_class_for_level($contextlevel);
        return call_user_func(array($class, 'instance'), $instanceid, $strictness);
    } else {
        return get_context_instance($contextlevel, $instanceid);
    }
}

/**
 * textlib
 *
 * a wrapper method to offer consistent API for textlib class
 * in Moodle 2.0 - 2.1, $textlib is first initiated, then called.
 * in Moodle 2.2 - 2.5, we use only static methods of the "textlib" class.
 * in Moodle >= 2.6, we use only static methods of the "core_text" class.
 *
 * @param string $method
 * @param mixed any extra params that are required by the textlib $method
 * @return result from the textlib $method
 * @todo Finish documenting this function
 */
function hotpot_textlib() {
    if (class_exists('core_text')) {
        // Moodle >= 2.6
        $textlib = 'core_text';
    } else if (method_exists('textlib', 'textlib')) {
        // Moodle 2.0 - 2.1
        $textlib = textlib_get_instance();
    } else {
        // Moodle 2.2 - 2.5
        $textlib = 'textlib';
    }
    $args = func_get_args();
    $method = array_shift($args);
    if (method_exists($textlib, $method)) {
        $callback = array($textlib, $method);
        return call_user_func_array($callback, $args);
    }
    if ($method=='utf8ord') {
        // Moodle <= 2.4
        $args = array($args[0], true); // force decimal entity
        $callback = array($textlib, 'utf8_to_entities');
        $str = call_user_func_array($callback, $args);
        if (substr($str, 0, 2)=='&#' && substr($str, -1)==';') {
            return intval(substr($str, 2, -1));
        }
        return ord($str);
    }
    debugging("Textlib method does not exist: $method");
    die;
}

/**
 * hotpot_add_to_log
 *
 * @param integer $courseid
 * @param string  $module name e.g. "hotpot"
 * @param string  $action
 * @param string  $url (optional, default='')
 * @param string  $info (optional, default='') often a hotpot id
 * @param string  $cmid (optional, default=0)
 * @param integer $userid (optional, default=0)
 */
function hotpot_add_to_log($courseid, $module, $action, $url='', $info='', $cmid=0, $userid=0) {
    global $DB, $PAGE;

    // detect new event API (Moodle >= 2.6)
    if (function_exists('get_log_manager')) {

        // map old $action to new $eventname
        switch ($action) {
            case 'attempt':  $eventname = 'attempt_started';      break;
            case 'report':   $eventname = 'report_viewed';        break;
            case 'review':   $eventname = 'attempt_reviewed';     break;
            case 'submit':   $eventname = 'attempt_submitted';    break;
            case 'view':     $eventname = 'course_module_viewed'; break;
            case 'index':    // legacy $action
            case 'view all': $eventname = 'course_module_instance_list_viewed'; break;
            default: $eventname = $action;
        }

        $classname = '\\mod_hotpot\\event\\'.$eventname;
        if (class_exists($classname)) {

            $context = null;
            $course = null;
            $hotpot = null;
            $params = null;
            $objectid = 0;

            if ($action=='index' || $action=='view all') {
                // course context
                if (isset($PAGE->course) && $PAGE->course->id==$courseid) {
                    // normal Moodle use
                    $context  = $PAGE->context;
                    $course   = $PAGE->course;
                } else if ($courseid) {
                    // Moodle upgrade
                    $context  = hotpot_get_context(CONTEXT_COURSE, $courseid);
                    $course   = $DB->get_record('course', array('id' => $courseid));
                }
                if ($context) {
                    $params = array('context' => $context);
                }
            } else {
                // course module context
                if (isset($PAGE->cm) && $PAGE->cm->id==$cmid) {
                    // normal Moodle use
                    $objectid = $PAGE->cm->instance;
                    $context  = $PAGE->context;
                    $course   = $PAGE->course;
                    $hotpot   = $PAGE->activityrecord;
                } else if ($cmid) {
                    // Moodle upgrade
                    $objectid = $DB->get_field('course_modules', 'instance', array('id' => $cmid));
                    $context  = hotpot_get_context(CONTEXT_MODULE, $cmid);
                    $course   = $DB->get_record('course', array('id' => $courseid));
                    $hotpot   = $DB->get_record('hotpot', array('id' => $objectid));
                }
                if ($context && $objectid) {
                    $params = array('context' => $context, 'objectid' => $objectid);
                }
            }

            if ($params) {
                if ($userid) {
                    $params['relateduserid'] = $userid;
                }
                // use call_user_func() to prevent syntax error in PHP 5.2.x
                $event = call_user_func(array($classname, 'create'), $params);
                if ($course) {
                    $event->add_record_snapshot('course', $course);
                }
                if ($hotpot) {
                    $event->add_record_snapshot('hotpot', $hotpot);
                }
                $event->trigger();
            }
        }

    } else if (function_exists('add_to_log')) {
        // Moodle <= 2.5
        add_to_log($courseid, $module, $action, $url, $info, $cmid, $userid);
    }
}

/**
 * Obtains the automatic completion state for this hotpot
 * based on the conditions in hotpot settings.
 *
 * @param  object  $course record from "course" table
 * @param  object  $cm     record from "course_modules" table
 * @param  integer $userid id from "user" table
 * @param  bool    $type   of comparison (used as return value if there are no conditions)
 *                         COMPLETION_AND (=true) or COMPLETION_OR (=false)
 * @return mixed   TRUE if completed, FALSE if not, or $type if no conditions are set
 */
function hotpot_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

    // set default return $state
    $state = $type;

    // get the hotpot record
    if ($hotpot = $DB->get_record('hotpot', array('id' => $cm->instance))) {

        // get grade, if necessary
        $grade = false;
        if ($hotpot->completionmingrade > 0.0 || $hotpot->completionpass) {
            require_once($CFG->dirroot.'/lib/gradelib.php');
            $params = array('courseid'     => $course->id,
                            'itemtype'     => 'mod',
                            'itemmodule'   => 'hotpot',
                            'iteminstance' => $cm->instance);
            if ($grade_item = grade_item::fetch($params)) {
                $grades = grade_grade::fetch_users_grades($grade_item, array($userid), false);
                if (isset($grades[$userid])) {
                    $grade = $grades[$userid];
                }
                unset($grades);
            }
            unset($grade_item);
        }

        // the HotPot completion conditions
        $conditions = array('completionmingrade',
                            'completionpass',
                            'completioncompleted');

        foreach ($conditions as $condition) {
            // decimal (e.g. completionmingrade) fields are returned by MySQL as a string
            // and since empty('0.0') returns false (!!), so we must use numeric comparison
            if (empty($hotpot->$condition) || floatval($hotpot->$condition)==0.0) {
                continue;
            }
            switch ($condition) {
                case 'completionmingrade':
                    $state = ($grade && $grade->finalgrade >= $hotpot->completionmingrade);
                    break;
                case 'completionpass':
                    $state = ($grade && $grade->is_passed());
                    break;
                case 'completioncompleted':
                    $params = array('hotpotid' => $cm->instance,
                                    'userid'   => $userid,
                                    'status'   => hotpot::STATUS_COMPLETED);
                    $state = $DB->record_exists('hotpot_attempts', $params);
                    break;

            }
            // finish early if possible
            if ($type==COMPLETION_AND && $state==false) {
                return false;
            }
            if ($type==COMPLETION_OR && $state) {
                return true;
            }
        }
    }

    return $state;
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
function mod_hotpot_core_calendar_provide_event_action(calendar_event $event,
                                                            \core_calendar\action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['hotpot'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
            get_string('view'),
            new \moodle_url('/mod/hotpot/view.php', array('id' => $cm->id)),
            1,
            true
    );
}
