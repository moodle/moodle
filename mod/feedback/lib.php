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
 * Library of functions and constants for module feedback
 * includes the main-part of feedback-functions
 *
 * @package mod_feedback
 * @copyright Andreas Grabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Include eventslib.php */
require_once($CFG->libdir.'/eventslib.php');
/** Include calendar/lib.php */
require_once($CFG->dirroot.'/calendar/lib.php');
// Include forms lib.
require_once($CFG->libdir.'/formslib.php');

define('FEEDBACK_ANONYMOUS_YES', 1);
define('FEEDBACK_ANONYMOUS_NO', 2);
define('FEEDBACK_MIN_ANONYMOUS_COUNT_IN_GROUP', 2);
define('FEEDBACK_DECIMAL', '.');
define('FEEDBACK_THOUSAND', ',');
define('FEEDBACK_RESETFORM_RESET', 'feedback_reset_data_');
define('FEEDBACK_RESETFORM_DROP', 'feedback_drop_feedback_');
define('FEEDBACK_MAX_PIX_LENGTH', '400'); //max. Breite des grafischen Balkens in der Auswertung
define('FEEDBACK_DEFAULT_PAGE_COUNT', 20);

/**
 * Returns all other caps used in module.
 *
 * @return array
 */
function feedback_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function feedback_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES:    return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * this will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $feedback the object given by mod_feedback_mod_form
 * @return int
 */
function feedback_add_instance($feedback) {
    global $DB;

    $feedback->timemodified = time();
    $feedback->id = '';

    if (empty($feedback->site_after_submit)) {
        $feedback->site_after_submit = '';
    }

    //saving the feedback in db
    $feedbackid = $DB->insert_record("feedback", $feedback);

    $feedback->id = $feedbackid;

    feedback_set_events($feedback);

    if (!isset($feedback->coursemodule)) {
        $cm = get_coursemodule_from_id('feedback', $feedback->id);
        $feedback->coursemodule = $cm->id;
    }
    $context = context_module::instance($feedback->coursemodule);

    $editoroptions = feedback_get_editor_options();

    // process the custom wysiwyg editor in page_after_submit
    if ($draftitemid = $feedback->page_after_submit_editor['itemid']) {
        $feedback->page_after_submit = file_save_draft_area_files($draftitemid, $context->id,
                                                    'mod_feedback', 'page_after_submit',
                                                    0, $editoroptions,
                                                    $feedback->page_after_submit_editor['text']);

        $feedback->page_after_submitformat = $feedback->page_after_submit_editor['format'];
    }
    $DB->update_record('feedback', $feedback);

    return $feedbackid;
}

/**
 * this will update a given instance
 *
 * @global object
 * @param object $feedback the object given by mod_feedback_mod_form
 * @return boolean
 */
function feedback_update_instance($feedback) {
    global $DB;

    $feedback->timemodified = time();
    $feedback->id = $feedback->instance;

    if (empty($feedback->site_after_submit)) {
        $feedback->site_after_submit = '';
    }

    //save the feedback into the db
    $DB->update_record("feedback", $feedback);

    //create or update the new events
    feedback_set_events($feedback);

    $context = context_module::instance($feedback->coursemodule);

    $editoroptions = feedback_get_editor_options();

    // process the custom wysiwyg editor in page_after_submit
    if ($draftitemid = $feedback->page_after_submit_editor['itemid']) {
        $feedback->page_after_submit = file_save_draft_area_files($draftitemid, $context->id,
                                                    'mod_feedback', 'page_after_submit',
                                                    0, $editoroptions,
                                                    $feedback->page_after_submit_editor['text']);

        $feedback->page_after_submitformat = $feedback->page_after_submit_editor['format'];
    }
    $DB->update_record('feedback', $feedback);

    return true;
}

/**
 * Serves the files included in feedback items like label. Implements needed access control ;-)
 *
 * There are two situations in general where the files will be sent.
 * 1) filearea = item, 2) filearea = template
 *
 * @package  mod_feedback
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function feedback_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($filearea === 'item' or $filearea === 'template') {
        $itemid = (int)array_shift($args);
        //get the item what includes the file
        if (!$item = $DB->get_record('feedback_item', array('id'=>$itemid))) {
            return false;
        }
        $feedbackid = $item->feedback;
        $templateid = $item->template;
    }

    if ($filearea === 'page_after_submit' or $filearea === 'item') {
        if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
            return false;
        }

        $feedbackid = $feedback->id;

        //if the filearea is "item" so we check the permissions like view/complete the feedback
        $canload = false;
        //first check whether the user has the complete capability
        if (has_capability('mod/feedback:complete', $context)) {
            $canload = true;
        }

        //now we check whether the user has the view capability
        if (has_capability('mod/feedback:view', $context)) {
            $canload = true;
        }

        //if the feedback is on frontpage and anonymous and the fullanonymous is allowed
        //so the file can be loaded too.
        if (isset($CFG->feedback_allowfullanonymous)
                    AND $CFG->feedback_allowfullanonymous
                    AND $course->id == SITEID
                    AND $feedback->anonymous == FEEDBACK_ANONYMOUS_YES ) {
            $canload = true;
        }

        if (!$canload) {
            return false;
        }
    } else if ($filearea === 'template') { //now we check files in templates
        if (!$template = $DB->get_record('feedback_template', array('id'=>$templateid))) {
            return false;
        }

        //if the file is not public so the capability edititems has to be there
        if (!$template->ispublic) {
            if (!has_capability('mod/feedback:edititems', $context)) {
                return false;
            }
        } else { //on public templates, at least the user has to be logged in
            if (!isloggedin()) {
                return false;
            }
        }
    } else {
        return false;
    }

    if ($context->contextlevel == CONTEXT_MODULE) {
        if ($filearea !== 'item' and $filearea !== 'page_after_submit') {
            return false;
        }
    }

    if ($context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea !== 'template') {
            return false;
        }
    }

    $relativepath = implode('/', $args);
    if ($filearea === 'page_after_submit') {
        $fullpath = "/{$context->id}/mod_feedback/$filearea/$relativepath";
    } else {
        $fullpath = "/{$context->id}/mod_feedback/$filearea/{$item->id}/$relativepath";
    }

    $fs = get_file_storage();

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // finally send the file
    send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!

    return false;
}

/**
 * this will delete a given instance.
 * all referenced data also will be deleted
 *
 * @global object
 * @param int $id the instanceid of feedback
 * @return boolean
 */
function feedback_delete_instance($id) {
    global $DB;

    //get all referenced items
    $feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$id));

    //deleting all referenced items and values
    if (is_array($feedbackitems)) {
        foreach ($feedbackitems as $feedbackitem) {
            $DB->delete_records("feedback_value", array("item"=>$feedbackitem->id));
            $DB->delete_records("feedback_valuetmp", array("item"=>$feedbackitem->id));
        }
        if ($delitems = $DB->get_records("feedback_item", array("feedback"=>$id))) {
            foreach ($delitems as $delitem) {
                feedback_delete_item($delitem->id, false);
            }
        }
    }

    //deleting the completeds
    $DB->delete_records("feedback_completed", array("feedback"=>$id));

    //deleting the unfinished completeds
    $DB->delete_records("feedback_completedtmp", array("feedback"=>$id));

    //deleting old events
    $DB->delete_records('event', array('modulename'=>'feedback', 'instance'=>$id));
    return $DB->delete_records("feedback", array("id"=>$id));
}

/**
 * this is called after deleting all instances if the course will be deleted.
 * only templates have to be deleted
 *
 * @global object
 * @param object $course
 * @return boolean
 */
function feedback_delete_course($course) {
    global $DB;

    //delete all templates of given course
    return $DB->delete_records('feedback_template', array('course'=>$course->id));
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $feedback
 * @return object
 */
function feedback_user_outline($course, $user, $mod, $feedback) {
    return null;
}

/**
 * Returns all users who has completed a specified feedback since a given time
 * many thanks to Manolescu Dorel, who contributed these two functions
 *
 * @global object
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @param array $activities Passed by reference
 * @param int $index Passed by reference
 * @param int $timemodified Timestamp
 * @param int $courseid
 * @param int $cmid
 * @param int $userid
 * @param int $groupid
 * @return void
 */
function feedback_get_recent_mod_activity(&$activities, &$index,
                                          $timemodified, $courseid,
                                          $cmid, $userid="", $groupid="") {

    global $CFG, $COURSE, $USER, $DB;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id'=>$courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];

    $sqlargs = array();

    $userfields = user_picture::fields('u', null, 'useridagain');
    $sql = " SELECT fk . * , fc . * , $userfields
                FROM {feedback_completed} fc
                    JOIN {feedback} fk ON fk.id = fc.feedback
                    JOIN {user} u ON u.id = fc.userid ";

    if ($groupid) {
        $sql .= " JOIN {groups_members} gm ON  gm.userid=u.id ";
    }

    $sql .= " WHERE fc.timemodified > ?
                AND fk.id = ?
                AND fc.anonymous_response = ?";
    $sqlargs[] = $timemodified;
    $sqlargs[] = $cm->instance;
    $sqlargs[] = FEEDBACK_ANONYMOUS_NO;

    if ($userid) {
        $sql .= " AND u.id = ? ";
        $sqlargs[] = $userid;
    }

    if ($groupid) {
        $sql .= " AND gm.groupid = ? ";
        $sqlargs[] = $groupid;
    }

    if (!$feedbackitems = $DB->get_records_sql($sql, $sqlargs)) {
        return;
    }

    $cm_context = context_module::instance($cm->id);

    if (!has_capability('mod/feedback:view', $cm_context)) {
        return;
    }

    $accessallgroups = has_capability('moodle/site:accessallgroups', $cm_context);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $cm_context);
    $groupmode       = groups_get_activity_groupmode($cm, $course);

    $aname = format_string($cm->name, true);
    foreach ($feedbackitems as $feedbackitem) {
        if ($feedbackitem->userid != $USER->id) {

            if ($groupmode == SEPARATEGROUPS and !$accessallgroups) {
                $usersgroups = groups_get_all_groups($course->id,
                                                     $feedbackitem->userid,
                                                     $cm->groupingid);
                if (!is_array($usersgroups)) {
                    continue;
                }
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }

        $tmpactivity = new stdClass();

        $tmpactivity->type      = 'feedback';
        $tmpactivity->cmid      = $cm->id;
        $tmpactivity->name      = $aname;
        $tmpactivity->sectionnum= $cm->sectionnum;
        $tmpactivity->timestamp = $feedbackitem->timemodified;

        $tmpactivity->content = new stdClass();
        $tmpactivity->content->feedbackid = $feedbackitem->id;
        $tmpactivity->content->feedbackuserid = $feedbackitem->userid;

        $tmpactivity->user = user_picture::unalias($feedbackitem, null, 'useridagain');
        $tmpactivity->user->fullname = fullname($feedbackitem, $viewfullnames);

        $activities[$index++] = $tmpactivity;
    }

    return;
}

/**
 * Prints all users who has completed a specified feedback since a given time
 * many thanks to Manolescu Dorel, who contributed these two functions
 *
 * @global object
 * @param object $activity
 * @param int $courseid
 * @param string $detail
 * @param array $modnames
 * @return void Output is echo'd
 */
function feedback_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $CFG, $OUTPUT;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="forum-recent">';

    echo "<tr><td class=\"userpicture\" valign=\"top\">";
    echo $OUTPUT->user_picture($activity->user, array('courseid'=>$courseid));
    echo "</td><td>";

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo "<img src=\"" . $OUTPUT->pix_url('icon', $activity->type) . "\" ".
             "class=\"icon\" alt=\"$modname\" />";
        echo "<a href=\"$CFG->wwwroot/mod/feedback/view.php?id={$activity->cmid}\">{$activity->name}</a>";
        echo '</div>';
    }

    echo '<div class="title">';
    echo '</div>';

    echo '<div class="user">';
    echo "<a href=\"$CFG->wwwroot/user/view.php?id={$activity->user->id}&amp;course=$courseid\">"
         ."{$activity->user->fullname}</a> - ".userdate($activity->timestamp);
    echo '</div>';

    echo "</td></tr></table>";

    return;
}

/**
 * Obtains the automatic completion state for this feedback based on the condition
 * in feedback settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function feedback_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;

    // Get feedback details
    $feedback = $DB->get_record('feedback', array('id'=>$cm->instance), '*', MUST_EXIST);

    // If completion option is enabled, evaluate it and return true/false
    if ($feedback->completionsubmit) {
        $params = array('userid'=>$userid, 'feedback'=>$feedback->id);
        return $DB->record_exists('feedback_completed', $params);
    } else {
        // Completion option is not enabled so just return $type
        return $type;
    }
}


/**
 * Print a detailed representation of what a  user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $feedback
 * @return bool
 */
function feedback_user_complete($course, $user, $mod, $feedback) {
    return true;
}

/**
 * @return bool true
 */
function feedback_cron () {
    return true;
}

/**
 * @return bool false
 */
function feedback_scale_used ($feedbackid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of feedback
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any assignment
 */
function feedback_scale_used_anywhere($scaleid) {
    return false;
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
function feedback_get_view_actions() {
    return array('view', 'view all');
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
function feedback_get_post_actions() {
    return array('submit');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all responses from the specified feedback
 * and clean up any related data.
 *
 * @global object
 * @global object
 * @uses FEEDBACK_RESETFORM_RESET
 * @uses FEEDBACK_RESETFORM_DROP
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function feedback_reset_userdata($data) {
    global $CFG, $DB;

    $resetfeedbacks = array();
    $dropfeedbacks = array();
    $status = array();
    $componentstr = get_string('modulenameplural', 'feedback');

    //get the relevant entries from $data
    foreach ($data as $key => $value) {
        switch(true) {
            case substr($key, 0, strlen(FEEDBACK_RESETFORM_RESET)) == FEEDBACK_RESETFORM_RESET:
                if ($value == 1) {
                    $templist = explode('_', $key);
                    if (isset($templist[3])) {
                        $resetfeedbacks[] = intval($templist[3]);
                    }
                }
            break;
            case substr($key, 0, strlen(FEEDBACK_RESETFORM_DROP)) == FEEDBACK_RESETFORM_DROP:
                if ($value == 1) {
                    $templist = explode('_', $key);
                    if (isset($templist[3])) {
                        $dropfeedbacks[] = intval($templist[3]);
                    }
                }
            break;
        }
    }

    //reset the selected feedbacks
    foreach ($resetfeedbacks as $id) {
        $feedback = $DB->get_record('feedback', array('id'=>$id));
        feedback_delete_all_completeds($feedback);
        $status[] = array('component'=>$componentstr.':'.$feedback->name,
                        'item'=>get_string('resetting_data', 'feedback'),
                        'error'=>false);
    }

    return $status;
}

/**
 * Called by course/reset.php
 *
 * @global object
 * @uses FEEDBACK_RESETFORM_RESET
 * @param object $mform form passed by reference
 */
function feedback_reset_course_form_definition(&$mform) {
    global $COURSE, $DB;

    $mform->addElement('header', 'feedbackheader', get_string('modulenameplural', 'feedback'));

    if (!$feedbacks = $DB->get_records('feedback', array('course'=>$COURSE->id), 'name')) {
        return;
    }

    $mform->addElement('static', 'hint', get_string('resetting_data', 'feedback'));
    foreach ($feedbacks as $feedback) {
        $mform->addElement('checkbox', FEEDBACK_RESETFORM_RESET.$feedback->id, $feedback->name);
    }
}

/**
 * Course reset form defaults.
 *
 * @global object
 * @uses FEEDBACK_RESETFORM_RESET
 * @param object $course
 */
function feedback_reset_course_form_defaults($course) {
    global $DB;

    $return = array();
    if (!$feedbacks = $DB->get_records('feedback', array('course'=>$course->id), 'name')) {
        return;
    }
    foreach ($feedbacks as $feedback) {
        $return[FEEDBACK_RESETFORM_RESET.$feedback->id] = true;
    }
    return $return;
}

/**
 * Called by course/reset.php and shows the formdata by coursereset.
 * it prints checkboxes for each feedback available at the given course
 * there are two checkboxes:
 * 1) delete userdata and keep the feedback
 * 2) delete userdata and drop the feedback
 *
 * @global object
 * @uses FEEDBACK_RESETFORM_RESET
 * @uses FEEDBACK_RESETFORM_DROP
 * @param object $course
 * @return void
 */
function feedback_reset_course_form($course) {
    global $DB, $OUTPUT;

    echo get_string('resetting_feedbacks', 'feedback'); echo ':<br />';
    if (!$feedbacks = $DB->get_records('feedback', array('course'=>$course->id), 'name')) {
        return;
    }

    foreach ($feedbacks as $feedback) {
        echo '<p>';
        echo get_string('name', 'feedback').': '.$feedback->name.'<br />';
        echo html_writer::checkbox(FEEDBACK_RESETFORM_RESET.$feedback->id,
                                1, true,
                                get_string('resetting_data', 'feedback'));
        echo '<br />';
        echo html_writer::checkbox(FEEDBACK_RESETFORM_DROP.$feedback->id,
                                1, false,
                                get_string('drop_feedback', 'feedback'));
        echo '</p>';
    }
}

/**
 * This gets an array with default options for the editor
 *
 * @return array the options
 */
function feedback_get_editor_options() {
    return array('maxfiles' => EDITOR_UNLIMITED_FILES,
                'trusttext'=>true);
}

/**
 * This creates new events given as timeopen and closeopen by $feedback.
 *
 * @global object
 * @param object $feedback
 * @return void
 */
function feedback_set_events($feedback) {
    global $DB;

    // adding the feedback to the eventtable (I have seen this at quiz-module)
    $DB->delete_records('event', array('modulename'=>'feedback', 'instance'=>$feedback->id));

    if (!isset($feedback->coursemodule)) {
        $cm = get_coursemodule_from_id('feedback', $feedback->id);
        $feedback->coursemodule = $cm->id;
    }

    // the open-event
    if ($feedback->timeopen > 0) {
        $event = new stdClass();
        $event->name         = get_string('start', 'feedback').' '.$feedback->name;
        $event->description  = format_module_intro('feedback', $feedback, $feedback->coursemodule);
        $event->courseid     = $feedback->course;
        $event->groupid      = 0;
        $event->userid       = 0;
        $event->modulename   = 'feedback';
        $event->instance     = $feedback->id;
        $event->eventtype    = 'open';
        $event->timestart    = $feedback->timeopen;
        $event->visible      = instance_is_visible('feedback', $feedback);
        if ($feedback->timeclose > 0) {
            $event->timeduration = ($feedback->timeclose - $feedback->timeopen);
        } else {
            $event->timeduration = 0;
        }

        calendar_event::create($event);
    }

    // the close-event
    if ($feedback->timeclose > 0) {
        $event = new stdClass();
        $event->name         = get_string('stop', 'feedback').' '.$feedback->name;
        $event->description  = format_module_intro('feedback', $feedback, $feedback->coursemodule);
        $event->courseid     = $feedback->course;
        $event->groupid      = 0;
        $event->userid       = 0;
        $event->modulename   = 'feedback';
        $event->instance     = $feedback->id;
        $event->eventtype    = 'close';
        $event->timestart    = $feedback->timeclose;
        $event->visible      = instance_is_visible('feedback', $feedback);
        $event->timeduration = 0;

        calendar_event::create($event);
    }
}

/**
 * this function is called by {@link feedback_delete_userdata()}
 * it drops the feedback-instance from the course_module table
 *
 * @global object
 * @param int $id the id from the coursemodule
 * @return boolean
 */
function feedback_delete_course_module($id) {
    global $DB;

    if (!$cm = $DB->get_record('course_modules', array('id'=>$id))) {
        return true;
    }
    return $DB->delete_records('course_modules', array('id'=>$cm->id));
}



////////////////////////////////////////////////
//functions to handle capabilities
////////////////////////////////////////////////

/**
 * returns the context-id related to the given coursemodule-id
 *
 * @deprecated since 3.1
 * @staticvar object $context
 * @param int $cmid the coursemodule-id
 * @return object $context
 */
function feedback_get_context($cmid) {
    debugging('Function feedback_get_context() is deprecated because it was not used.',
            DEBUG_DEVELOPER);
    static $context;

    if (isset($context)) {
        return $context;
    }

    $context = context_module::instance($cmid);
    return $context;
}

/**
 *  returns true if the current role is faked by switching role feature
 *
 * @global object
 * @return boolean
 */
function feedback_check_is_switchrole() {
    global $USER;
    if (isset($USER->switchrole) AND
            is_array($USER->switchrole) AND
            count($USER->switchrole) > 0) {

        return true;
    }
    return false;
}

/**
 * count users which have not completed the feedback
 *
 * @global object
 * @uses CONTEXT_MODULE
 * @param cm_info $cm Course-module object
 * @param int $group single groupid
 * @param string $sort
 * @param int $startpage
 * @param int $pagecount
 * @return object the userrecords
 */
function feedback_get_incomplete_users(cm_info $cm,
                                       $group = false,
                                       $sort = '',
                                       $startpage = false,
                                       $pagecount = false) {

    global $DB;

    $context = context_module::instance($cm->id);

    //first get all user who can complete this feedback
    $cap = 'mod/feedback:complete';
    $fields = 'u.id, u.username';
    if (!$allusers = get_users_by_capability($context,
                                            $cap,
                                            $fields,
                                            $sort,
                                            '',
                                            '',
                                            $group,
                                            '',
                                            true)) {
        return false;
    }
    // Filter users that are not in the correct group/grouping.
    $info = new \core_availability\info_module($cm);
    $allusers = $info->filter_user_list($allusers);

    $allusers = array_keys($allusers);

    //now get all completeds
    $params = array('feedback'=>$cm->instance);
    if (!$completedusers = $DB->get_records_menu('feedback_completed', $params, '', 'userid,id')) {
        return $allusers;
    }
    $completedusers = array_keys($completedusers);

    //now strike all completedusers from allusers
    $allusers = array_diff($allusers, $completedusers);

    //for paging I use array_slice()
    if ($startpage !== false AND $pagecount !== false) {
        $allusers = array_slice($allusers, $startpage, $pagecount);
    }

    return $allusers;
}

/**
 * count users which have not completed the feedback
 *
 * @global object
 * @param object $cm
 * @param int $group single groupid
 * @return int count of userrecords
 */
function feedback_count_incomplete_users($cm, $group = false) {
    if ($allusers = feedback_get_incomplete_users($cm, $group)) {
        return count($allusers);
    }
    return 0;
}

/**
 * count users which have completed a feedback
 *
 * @global object
 * @uses FEEDBACK_ANONYMOUS_NO
 * @param object $cm
 * @param int $group single groupid
 * @return int count of userrecords
 */
function feedback_count_complete_users($cm, $group = false) {
    global $DB;

    $params = array(FEEDBACK_ANONYMOUS_NO, $cm->instance);

    $fromgroup = '';
    $wheregroup = '';
    if ($group) {
        $fromgroup = ', {groups_members} g';
        $wheregroup = ' AND g.groupid = ? AND g.userid = c.userid';
        $params[] = $group;
    }

    $sql = 'SELECT COUNT(u.id) FROM {user} u, {feedback_completed} c'.$fromgroup.'
              WHERE anonymous_response = ? AND u.id = c.userid AND c.feedback = ?
              '.$wheregroup;

    return $DB->count_records_sql($sql, $params);

}

/**
 * get users which have completed a feedback
 *
 * @global object
 * @uses CONTEXT_MODULE
 * @uses FEEDBACK_ANONYMOUS_NO
 * @param object $cm
 * @param int $group single groupid
 * @param string $where a sql where condition (must end with " AND ")
 * @param array parameters used in $where
 * @param string $sort a table field
 * @param int $startpage
 * @param int $pagecount
 * @return object the userrecords
 */
function feedback_get_complete_users($cm,
                                     $group = false,
                                     $where = '',
                                     array $params = null,
                                     $sort = '',
                                     $startpage = false,
                                     $pagecount = false) {

    global $DB;

    $context = context_module::instance($cm->id);

    $params = (array)$params;

    $params['anon'] = FEEDBACK_ANONYMOUS_NO;
    $params['instance'] = $cm->instance;

    $fromgroup = '';
    $wheregroup = '';
    if ($group) {
        $fromgroup = ', {groups_members} g';
        $wheregroup = ' AND g.groupid = :group AND g.userid = c.userid';
        $params['group'] = $group;
    }

    if ($sort) {
        $sortsql = ' ORDER BY '.$sort;
    } else {
        $sortsql = '';
    }

    $ufields = user_picture::fields('u');
    $sql = 'SELECT DISTINCT '.$ufields.', c.timemodified as completed_timemodified
            FROM {user} u, {feedback_completed} c '.$fromgroup.'
            WHERE '.$where.' anonymous_response = :anon
                AND u.id = c.userid
                AND c.feedback = :instance
              '.$wheregroup.$sortsql;

    if ($startpage === false OR $pagecount === false) {
        $startpage = false;
        $pagecount = false;
    }
    return $DB->get_records_sql($sql, $params, $startpage, $pagecount);
}

/**
 * get users which have the viewreports-capability
 *
 * @uses CONTEXT_MODULE
 * @param int $cmid
 * @param mixed $groups single groupid or array of groupids - group(s) user is in
 * @return object the userrecords
 */
function feedback_get_viewreports_users($cmid, $groups = false) {

    $context = context_module::instance($cmid);

    //description of the call below:
    //get_users_by_capability($context, $capability, $fields='', $sort='', $limitfrom='',
    //                          $limitnum='', $groups='', $exceptions='', $doanything=true)
    return get_users_by_capability($context,
                            'mod/feedback:viewreports',
                            '',
                            'lastname',
                            '',
                            '',
                            $groups,
                            '',
                            false);
}

/**
 * get users which have the receivemail-capability
 *
 * @uses CONTEXT_MODULE
 * @param int $cmid
 * @param mixed $groups single groupid or array of groupids - group(s) user is in
 * @return object the userrecords
 */
function feedback_get_receivemail_users($cmid, $groups = false) {

    $context = context_module::instance($cmid);

    //description of the call below:
    //get_users_by_capability($context, $capability, $fields='', $sort='', $limitfrom='',
    //                          $limitnum='', $groups='', $exceptions='', $doanything=true)
    return get_users_by_capability($context,
                            'mod/feedback:receivemail',
                            '',
                            'lastname',
                            '',
                            '',
                            $groups,
                            '',
                            false);
}

////////////////////////////////////////////////
//functions to handle the templates
////////////////////////////////////////////////
////////////////////////////////////////////////

/**
 * creates a new template-record.
 *
 * @global object
 * @param int $courseid
 * @param string $name the name of template shown in the templatelist
 * @param int $ispublic 0:privat 1:public
 * @return int the new templateid
 */
function feedback_create_template($courseid, $name, $ispublic = 0) {
    global $DB;

    $templ = new stdClass();
    $templ->course   = ($ispublic ? 0 : $courseid);
    $templ->name     = $name;
    $templ->ispublic = $ispublic;

    $templid = $DB->insert_record('feedback_template', $templ);
    return $DB->get_record('feedback_template', array('id'=>$templid));
}

/**
 * creates new template items.
 * all items will be copied and the attribute feedback will be set to 0
 * and the attribute template will be set to the new templateid
 *
 * @global object
 * @uses CONTEXT_MODULE
 * @uses CONTEXT_COURSE
 * @param object $feedback
 * @param string $name the name of template shown in the templatelist
 * @param int $ispublic 0:privat 1:public
 * @return boolean
 */
function feedback_save_as_template($feedback, $name, $ispublic = 0) {
    global $DB;
    $fs = get_file_storage();

    if (!$feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id))) {
        return false;
    }

    if (!$newtempl = feedback_create_template($feedback->course, $name, $ispublic)) {
        return false;
    }

    //files in the template_item are in the context of the current course or
    //if the template is public the files are in the system context
    //files in the feedback_item are in the feedback_context of the feedback
    if ($ispublic) {
        $s_context = context_system::instance();
    } else {
        $s_context = context_course::instance($newtempl->course);
    }
    $cm = get_coursemodule_from_instance('feedback', $feedback->id);
    $f_context = context_module::instance($cm->id);

    //create items of this new template
    //depend items we are storing temporary in an mapping list array(new id => dependitem)
    //we also store a mapping of all items array(oldid => newid)
    $dependitemsmap = array();
    $itembackup = array();
    foreach ($feedbackitems as $item) {

        $t_item = clone($item);

        unset($t_item->id);
        $t_item->feedback = 0;
        $t_item->template     = $newtempl->id;
        $t_item->id = $DB->insert_record('feedback_item', $t_item);
        //copy all included files to the feedback_template filearea
        $itemfiles = $fs->get_area_files($f_context->id,
                                    'mod_feedback',
                                    'item',
                                    $item->id,
                                    "id",
                                    false);
        if ($itemfiles) {
            foreach ($itemfiles as $ifile) {
                $file_record = new stdClass();
                $file_record->contextid = $s_context->id;
                $file_record->component = 'mod_feedback';
                $file_record->filearea = 'template';
                $file_record->itemid = $t_item->id;
                $fs->create_file_from_storedfile($file_record, $ifile);
            }
        }

        $itembackup[$item->id] = $t_item->id;
        if ($t_item->dependitem) {
            $dependitemsmap[$t_item->id] = $t_item->dependitem;
        }

    }

    //remapping the dependency
    foreach ($dependitemsmap as $key => $dependitem) {
        $newitem = $DB->get_record('feedback_item', array('id'=>$key));
        $newitem->dependitem = $itembackup[$newitem->dependitem];
        $DB->update_record('feedback_item', $newitem);
    }

    return true;
}

/**
 * deletes all feedback_items related to the given template id
 *
 * @global object
 * @uses CONTEXT_COURSE
 * @param object $template the template
 * @return void
 */
function feedback_delete_template($template) {
    global $DB;

    //deleting the files from the item is done by feedback_delete_item
    if ($t_items = $DB->get_records("feedback_item", array("template"=>$template->id))) {
        foreach ($t_items as $t_item) {
            feedback_delete_item($t_item->id, false, $template);
        }
    }
    $DB->delete_records("feedback_template", array("id"=>$template->id));
}

/**
 * creates new feedback_item-records from template.
 * if $deleteold is set true so the existing items of the given feedback will be deleted
 * if $deleteold is set false so the new items will be appanded to the old items
 *
 * @global object
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_MODULE
 * @param object $feedback
 * @param int $templateid
 * @param boolean $deleteold
 */
function feedback_items_from_template($feedback, $templateid, $deleteold = false) {
    global $DB, $CFG;

    require_once($CFG->libdir.'/completionlib.php');

    $fs = get_file_storage();

    if (!$template = $DB->get_record('feedback_template', array('id'=>$templateid))) {
        return false;
    }
    //get all templateitems
    if (!$templitems = $DB->get_records('feedback_item', array('template'=>$templateid))) {
        return false;
    }

    //files in the template_item are in the context of the current course
    //files in the feedback_item are in the feedback_context of the feedback
    if ($template->ispublic) {
        $s_context = context_system::instance();
    } else {
        $s_context = context_course::instance($feedback->course);
    }
    $course = $DB->get_record('course', array('id'=>$feedback->course));
    $cm = get_coursemodule_from_instance('feedback', $feedback->id);
    $f_context = context_module::instance($cm->id);

    //if deleteold then delete all old items before
    //get all items
    if ($deleteold) {
        if ($feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id))) {
            //delete all items of this feedback
            foreach ($feedbackitems as $item) {
                feedback_delete_item($item->id, false);
            }

            $params = array('feedback'=>$feedback->id);
            if ($completeds = $DB->get_records('feedback_completed', $params)) {
                $completion = new completion_info($course);
                foreach ($completeds as $completed) {
                    // Update completion state
                    if ($completion->is_enabled($cm) && $feedback->completionsubmit) {
                        $completion->update_state($cm, COMPLETION_INCOMPLETE, $completed->userid);
                    }
                    $DB->delete_records('feedback_completed', array('id'=>$completed->id));
                }
            }
            $DB->delete_records('feedback_completedtmp', array('feedback'=>$feedback->id));
        }
        $positionoffset = 0;
    } else {
        //if the old items are kept the new items will be appended
        //therefor the new position has an offset
        $positionoffset = $DB->count_records('feedback_item', array('feedback'=>$feedback->id));
    }

    //create items of this new template
    //depend items we are storing temporary in an mapping list array(new id => dependitem)
    //we also store a mapping of all items array(oldid => newid)
    $dependitemsmap = array();
    $itembackup = array();
    foreach ($templitems as $t_item) {
        $item = clone($t_item);
        unset($item->id);
        $item->feedback = $feedback->id;
        $item->template = 0;
        $item->position = $item->position + $positionoffset;

        $item->id = $DB->insert_record('feedback_item', $item);

        //moving the files to the new item
        $templatefiles = $fs->get_area_files($s_context->id,
                                        'mod_feedback',
                                        'template',
                                        $t_item->id,
                                        "id",
                                        false);
        if ($templatefiles) {
            foreach ($templatefiles as $tfile) {
                $file_record = new stdClass();
                $file_record->contextid = $f_context->id;
                $file_record->component = 'mod_feedback';
                $file_record->filearea = 'item';
                $file_record->itemid = $item->id;
                $fs->create_file_from_storedfile($file_record, $tfile);
            }
        }

        $itembackup[$t_item->id] = $item->id;
        if ($item->dependitem) {
            $dependitemsmap[$item->id] = $item->dependitem;
        }
    }

    //remapping the dependency
    foreach ($dependitemsmap as $key => $dependitem) {
        $newitem = $DB->get_record('feedback_item', array('id'=>$key));
        $newitem->dependitem = $itembackup[$newitem->dependitem];
        $DB->update_record('feedback_item', $newitem);
    }
}

/**
 * get the list of available templates.
 * if the $onlyown param is set true so only templates from own course will be served
 * this is important for droping templates
 *
 * @global object
 * @param object $course
 * @param string $onlyownorpublic
 * @return array the template recordsets
 */
function feedback_get_template_list($course, $onlyownorpublic = '') {
    global $DB, $CFG;

    switch($onlyownorpublic) {
        case '':
            $templates = $DB->get_records_select('feedback_template',
                                                 'course = ? OR ispublic = 1',
                                                 array($course->id),
                                                 'name');
            break;
        case 'own':
            $templates = $DB->get_records('feedback_template',
                                          array('course'=>$course->id),
                                          'name');
            break;
        case 'public':
            $templates = $DB->get_records('feedback_template', array('ispublic'=>1), 'name');
            break;
    }
    return $templates;
}

////////////////////////////////////////////////
//Handling der Items
////////////////////////////////////////////////
////////////////////////////////////////////////

/**
 * load the lib.php from item-plugin-dir and returns the instance of the itemclass
 *
 * @param string $typ
 * @return feedback_item_base the instance of itemclass
 */
function feedback_get_item_class($typ) {
    global $CFG;

    //get the class of item-typ
    $itemclass = 'feedback_item_'.$typ;
    //get the instance of item-class
    if (!class_exists($itemclass)) {
        require_once($CFG->dirroot.'/mod/feedback/item/'.$typ.'/lib.php');
    }
    return new $itemclass();
}

/**
 * load the available item plugins from given subdirectory of $CFG->dirroot
 * the default is "mod/feedback/item"
 *
 * @global object
 * @param string $dir the subdir
 * @return array pluginnames as string
 */
function feedback_load_feedback_items($dir = 'mod/feedback/item') {
    global $CFG;
    $names = get_list_of_plugins($dir);
    $ret_names = array();

    foreach ($names as $name) {
        require_once($CFG->dirroot.'/'.$dir.'/'.$name.'/lib.php');
        if (class_exists('feedback_item_'.$name)) {
            $ret_names[] = $name;
        }
    }
    return $ret_names;
}

/**
 * load the available item plugins to use as dropdown-options
 *
 * @global object
 * @return array pluginnames as string
 */
function feedback_load_feedback_items_options() {
    global $CFG;

    $feedback_options = array("pagebreak" => get_string('add_pagebreak', 'feedback'));

    if (!$feedback_names = feedback_load_feedback_items('mod/feedback/item')) {
        return array();
    }

    foreach ($feedback_names as $fn) {
        $feedback_options[$fn] = get_string($fn, 'feedback');
    }
    asort($feedback_options);
    return $feedback_options;
}

/**
 * load the available items for the depend item dropdown list shown in the edit_item form
 *
 * @global object
 * @param object $feedback
 * @param object $item the item of the edit_item form
 * @return array all items except the item $item, labels and pagebreaks
 */
function feedback_get_depend_candidates_for_item($feedback, $item) {
    global $DB;
    //all items for dependitem
    $where = "feedback = ? AND typ != 'pagebreak' AND hasvalue = 1";
    $params = array($feedback->id);
    if (isset($item->id) AND $item->id) {
        $where .= ' AND id != ?';
        $params[] = $item->id;
    }
    $dependitems = array(0 => get_string('choose'));
    $feedbackitems = $DB->get_records_select_menu('feedback_item',
                                                  $where,
                                                  $params,
                                                  'position',
                                                  'id, label');

    if (!$feedbackitems) {
        return $dependitems;
    }
    //adding the choose-option
    foreach ($feedbackitems as $key => $val) {
        if (trim(strval($val)) !== '') {
            $dependitems[$key] = format_string($val);
        }
    }
    return $dependitems;
}

/**
 * creates a new item-record
 *
 * @deprecated since 3.1
 * @param object $data the data from edit_item_form
 * @return int the new itemid
 */
function feedback_create_item($data) {
    debugging('Function feedback_create_item() is deprecated because it was not used.',
            DEBUG_DEVELOPER);
    global $DB;

    $item = new stdClass();
    $item->feedback = $data->feedbackid;

    $item->template=0;
    if (isset($data->templateid)) {
            $item->template = intval($data->templateid);
    }

    $itemname = trim($data->itemname);
    $item->name = ($itemname ? $data->itemname : get_string('no_itemname', 'feedback'));

    if (!empty($data->itemlabel)) {
        $item->label = trim($data->itemlabel);
    } else {
        $item->label = get_string('no_itemlabel', 'feedback');
    }

    $itemobj = feedback_get_item_class($data->typ);
    $item->presentation = ''; //the date comes from postupdate() of the itemobj

    $item->hasvalue = $itemobj->get_hasvalue();

    $item->typ = $data->typ;
    $item->position = $data->position;

    $item->required=0;
    if (!empty($data->required)) {
        $item->required = $data->required;
    }

    $item->id = $DB->insert_record('feedback_item', $item);

    //move all itemdata to the data
    $data->id = $item->id;
    $data->feedback = $item->feedback;
    $data->name = $item->name;
    $data->label = $item->label;
    $data->required = $item->required;
    return $itemobj->postupdate($data);
}

/**
 * save the changes of a given item.
 *
 * @global object
 * @param object $item
 * @return boolean
 */
function feedback_update_item($item) {
    global $DB;
    return $DB->update_record("feedback_item", $item);
}

/**
 * deletes an item and also deletes all related values
 *
 * @global object
 * @uses CONTEXT_MODULE
 * @param int $itemid
 * @param boolean $renumber should the kept items renumbered Yes/No
 * @param object $template if the template is given so the items are bound to it
 * @return void
 */
function feedback_delete_item($itemid, $renumber = true, $template = false) {
    global $DB;

    $item = $DB->get_record('feedback_item', array('id'=>$itemid));

    //deleting the files from the item
    $fs = get_file_storage();

    if ($template) {
        if ($template->ispublic) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($template->course);
        }
        $templatefiles = $fs->get_area_files($context->id,
                                    'mod_feedback',
                                    'template',
                                    $item->id,
                                    "id",
                                    false);

        if ($templatefiles) {
            $fs->delete_area_files($context->id, 'mod_feedback', 'template', $item->id);
        }
    } else {
        if (!$cm = get_coursemodule_from_instance('feedback', $item->feedback)) {
            return false;
        }
        $context = context_module::instance($cm->id);

        $itemfiles = $fs->get_area_files($context->id,
                                    'mod_feedback',
                                    'item',
                                    $item->id,
                                    "id", false);

        if ($itemfiles) {
            $fs->delete_area_files($context->id, 'mod_feedback', 'item', $item->id);
        }
    }

    $DB->delete_records("feedback_value", array("item"=>$itemid));
    $DB->delete_records("feedback_valuetmp", array("item"=>$itemid));

    //remove all depends
    $DB->set_field('feedback_item', 'dependvalue', '', array('dependitem'=>$itemid));
    $DB->set_field('feedback_item', 'dependitem', 0, array('dependitem'=>$itemid));

    $DB->delete_records("feedback_item", array("id"=>$itemid));
    if ($renumber) {
        feedback_renumber_items($item->feedback);
    }
}

/**
 * deletes all items of the given feedbackid
 *
 * @global object
 * @param int $feedbackid
 * @return void
 */
function feedback_delete_all_items($feedbackid) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/completionlib.php');

    if (!$feedback = $DB->get_record('feedback', array('id'=>$feedbackid))) {
        return false;
    }

    if (!$cm = get_coursemodule_from_instance('feedback', $feedback->id)) {
        return false;
    }

    if (!$course = $DB->get_record('course', array('id'=>$feedback->course))) {
        return false;
    }

    if (!$items = $DB->get_records('feedback_item', array('feedback'=>$feedbackid))) {
        return;
    }
    foreach ($items as $item) {
        feedback_delete_item($item->id, false);
    }
    if ($completeds = $DB->get_records('feedback_completed', array('feedback'=>$feedback->id))) {
        $completion = new completion_info($course);
        foreach ($completeds as $completed) {
            // Update completion state
            if ($completion->is_enabled($cm) && $feedback->completionsubmit) {
                $completion->update_state($cm, COMPLETION_INCOMPLETE, $completed->userid);
            }
            $DB->delete_records('feedback_completed', array('id'=>$completed->id));
        }
    }

    $DB->delete_records('feedback_completedtmp', array('feedback'=>$feedbackid));

}

/**
 * this function toggled the item-attribute required (yes/no)
 *
 * @global object
 * @param object $item
 * @return boolean
 */
function feedback_switch_item_required($item) {
    global $DB, $CFG;

    $itemobj = feedback_get_item_class($item->typ);

    if ($itemobj->can_switch_require()) {
        $new_require_val = (int)!(bool)$item->required;
        $params = array('id'=>$item->id);
        $DB->set_field('feedback_item', 'required', $new_require_val, $params);
    }
    return true;
}

/**
 * renumbers all items of the given feedbackid
 *
 * @global object
 * @param int $feedbackid
 * @return void
 */
function feedback_renumber_items($feedbackid) {
    global $DB;

    $items = $DB->get_records('feedback_item', array('feedback'=>$feedbackid), 'position');
    $pos = 1;
    if ($items) {
        foreach ($items as $item) {
            $DB->set_field('feedback_item', 'position', $pos, array('id'=>$item->id));
            $pos++;
        }
    }
}

/**
 * this decreases the position of the given item
 *
 * @global object
 * @param object $item
 * @return bool
 */
function feedback_moveup_item($item) {
    global $DB;

    if ($item->position == 1) {
        return true;
    }

    $params = array('feedback'=>$item->feedback);
    if (!$items = $DB->get_records('feedback_item', $params, 'position')) {
        return false;
    }

    $itembefore = null;
    foreach ($items as $i) {
        if ($i->id == $item->id) {
            if (is_null($itembefore)) {
                return true;
            }
            $itembefore->position = $item->position;
            $item->position--;
            feedback_update_item($itembefore);
            feedback_update_item($item);
            feedback_renumber_items($item->feedback);
            return true;
        }
        $itembefore = $i;
    }
    return false;
}

/**
 * this increased the position of the given item
 *
 * @global object
 * @param object $item
 * @return bool
 */
function feedback_movedown_item($item) {
    global $DB;

    $params = array('feedback'=>$item->feedback);
    if (!$items = $DB->get_records('feedback_item', $params, 'position')) {
        return false;
    }

    $movedownitem = null;
    foreach ($items as $i) {
        if (!is_null($movedownitem) AND $movedownitem->id == $item->id) {
            $movedownitem->position = $i->position;
            $i->position--;
            feedback_update_item($movedownitem);
            feedback_update_item($i);
            feedback_renumber_items($item->feedback);
            return true;
        }
        $movedownitem = $i;
    }
    return false;
}

/**
 * here the position of the given item will be set to the value in $pos
 *
 * @global object
 * @param object $moveitem
 * @param int $pos
 * @return boolean
 */
function feedback_move_item($moveitem, $pos) {
    global $DB;

    $params = array('feedback'=>$moveitem->feedback);
    if (!$allitems = $DB->get_records('feedback_item', $params, 'position')) {
        return false;
    }
    if (is_array($allitems)) {
        $index = 1;
        foreach ($allitems as $item) {
            if ($index == $pos) {
                $index++;
            }
            if ($item->id == $moveitem->id) {
                $moveitem->position = $pos;
                feedback_update_item($moveitem);
                continue;
            }
            $item->position = $index;
            feedback_update_item($item);
            $index++;
        }
        return true;
    }
    return false;
}

/**
 * prints the given item as a preview.
 * each item-class has an own print_item_preview function implemented.
 *
 * @deprecated since Moodle 3.1
 * @global object
 * @param object $item the item what we want to print out
 * @return void
 */
function feedback_print_item_preview($item) {
    debugging('Function feedback_print_item_preview() is deprecated and does nothing. '
            . 'Items must implement complete_form_element()', DEBUG_DEVELOPER);
}

/**
 * prints the given item in the completion form.
 * each item-class has an own print_item_complete function implemented.
 *
 * @deprecated since Moodle 3.1
 * @param object $item the item what we want to print out
 * @param mixed $value the value
 * @param boolean $highlightrequire if this set true and the value are false on completing so the item will be highlighted
 * @return void
 */
function feedback_print_item_complete($item, $value = false, $highlightrequire = false) {
    debugging('Function feedback_print_item_complete() is deprecated and does nothing. '
            . 'Items must implement complete_form_element()', DEBUG_DEVELOPER);
}

/**
 * prints the given item in the show entries page.
 * each item-class has an own print_item_show_value function implemented.
 *
 * @deprecated since Moodle 3.1
 * @param object $item the item what we want to print out
 * @param mixed $value
 * @return void
 */
function feedback_print_item_show_value($item, $value = false) {
    debugging('Function feedback_print_item_show_value() is deprecated and does nothing. '
            . 'Items must implement complete_form_element()', DEBUG_DEVELOPER);
}

/**
 * if the user completes a feedback and there is a pagebreak so the values are saved temporary.
 * the values are not saved permanently until the user click on save button
 *
 * @global object
 * @param object $feedbackcompleted
 * @return object temporary saved completed-record
 */
function feedback_set_tmp_values($feedbackcompleted) {
    global $DB;
    debugging('Function feedback_set_tmp_values() is deprecated and since it is '
            . 'no longer used in mod_feedback', DEBUG_DEVELOPER);

    //first we create a completedtmp
    $tmpcpl = new stdClass();
    foreach ($feedbackcompleted as $key => $value) {
        $tmpcpl->{$key} = $value;
    }
    unset($tmpcpl->id);
    $tmpcpl->timemodified = time();
    $tmpcpl->id = $DB->insert_record('feedback_completedtmp', $tmpcpl);
    //get all values of original-completed
    if (!$values = $DB->get_records('feedback_value', array('completed'=>$feedbackcompleted->id))) {
        return;
    }
    foreach ($values as $value) {
        unset($value->id);
        $value->completed = $tmpcpl->id;
        $DB->insert_record('feedback_valuetmp', $value);
    }
    return $tmpcpl;
}

/**
 * this saves the temporary saved values permanently
 *
 * @global object
 * @param object $feedbackcompletedtmp the temporary completed
 * @param object $feedbackcompleted the target completed
 * @return int the id of the completed
 */
function feedback_save_tmp_values($feedbackcompletedtmp, $feedbackcompleted) {
    global $DB;

    $tmpcplid = $feedbackcompletedtmp->id;
    if ($feedbackcompleted) {
        //first drop all existing values
        $DB->delete_records('feedback_value', array('completed'=>$feedbackcompleted->id));
        //update the current completed
        $feedbackcompleted->timemodified = time();
        $DB->update_record('feedback_completed', $feedbackcompleted);
    } else {
        $feedbackcompleted = clone($feedbackcompletedtmp);
        $feedbackcompleted->id = '';
        $feedbackcompleted->timemodified = time();
        $feedbackcompleted->id = $DB->insert_record('feedback_completed', $feedbackcompleted);
    }

    $allitems = $DB->get_records('feedback_item', array('feedback' => $feedbackcompleted->feedback));

    //save all the new values from feedback_valuetmp
    //get all values of tmp-completed
    $params = array('completed'=>$feedbackcompletedtmp->id);
    $values = $DB->get_records('feedback_valuetmp', $params);
    foreach ($values as $value) {
        //check if there are depend items
        $item = $DB->get_record('feedback_item', array('id'=>$value->item));
        if ($item->dependitem > 0 && isset($allitems[$item->dependitem])) {
            $check = feedback_compare_item_value($tmpcplid,
                                        $allitems[$item->dependitem],
                                        $item->dependvalue,
                                        true);
        } else {
            $check = true;
        }
        if ($check) {
            unset($value->id);
            $value->completed = $feedbackcompleted->id;
            $DB->insert_record('feedback_value', $value);
        }
    }
    //drop all the tmpvalues
    $DB->delete_records('feedback_valuetmp', array('completed'=>$tmpcplid));
    $DB->delete_records('feedback_completedtmp', array('id'=>$tmpcplid));

    // Trigger event for the delete action we performed.
    $cm = get_coursemodule_from_instance('feedback', $feedbackcompleted->feedback);
    $event = \mod_feedback\event\response_submitted::create_from_record($feedbackcompleted, $cm);
    $event->trigger();
    return $feedbackcompleted->id;

}

/**
 * deletes the given temporary completed and all related temporary values
 *
 * @deprecated since Moodle 3.1
 *
 * @param int $tmpcplid
 * @return void
 */
function feedback_delete_completedtmp($tmpcplid) {
    global $DB;

    debugging('Function feedback_delete_completedtmp() is deprecated because it is no longer used',
            DEBUG_DEVELOPER);

    $DB->delete_records('feedback_valuetmp', array('completed'=>$tmpcplid));
    $DB->delete_records('feedback_completedtmp', array('id'=>$tmpcplid));
}

////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////
//functions to handle the pagebreaks
////////////////////////////////////////////////

/**
 * this creates a pagebreak.
 * a pagebreak is a special kind of item
 *
 * @global object
 * @param int $feedbackid
 * @return mixed false if there already is a pagebreak on last position or the id of the pagebreak-item
 */
function feedback_create_pagebreak($feedbackid) {
    global $DB;

    //check if there already is a pagebreak on the last position
    $lastposition = $DB->count_records('feedback_item', array('feedback'=>$feedbackid));
    if ($lastposition == feedback_get_last_break_position($feedbackid)) {
        return false;
    }

    $item = new stdClass();
    $item->feedback = $feedbackid;

    $item->template=0;

    $item->name = '';

    $item->presentation = '';
    $item->hasvalue = 0;

    $item->typ = 'pagebreak';
    $item->position = $lastposition + 1;

    $item->required=0;

    return $DB->insert_record('feedback_item', $item);
}

/**
 * get all positions of pagebreaks in the given feedback
 *
 * @global object
 * @param int $feedbackid
 * @return array all ordered pagebreak positions
 */
function feedback_get_all_break_positions($feedbackid) {
    global $DB;

    $params = array('typ'=>'pagebreak', 'feedback'=>$feedbackid);
    $allbreaks = $DB->get_records_menu('feedback_item', $params, 'position', 'id, position');
    if (!$allbreaks) {
        return false;
    }
    return array_values($allbreaks);
}

/**
 * get the position of the last pagebreak
 *
 * @param int $feedbackid
 * @return int the position of the last pagebreak
 */
function feedback_get_last_break_position($feedbackid) {
    if (!$allbreaks = feedback_get_all_break_positions($feedbackid)) {
        return false;
    }
    return $allbreaks[count($allbreaks) - 1];
}

/**
 * this returns the position where the user can continue the completing.
 *
 * @deprecated since Moodle 3.1
 * @global object
 * @global object
 * @global object
 * @param int $feedbackid
 * @param int $courseid
 * @param string $guestid this id will be saved temporary and is unique
 * @return int the position to continue
 */
function feedback_get_page_to_continue($feedbackid, $courseid = false, $guestid = false) {
    global $CFG, $USER, $DB;

    debugging('Function feedback_get_page_to_continue() is deprecated and since it is '
            . 'no longer used in mod_feedback', DEBUG_DEVELOPER);

    //is there any break?

    if (!$allbreaks = feedback_get_all_break_positions($feedbackid)) {
        return false;
    }

    $params = array();
    if ($courseid) {
        $courseselect = "AND fv.course_id = :courseid";
        $params['courseid'] = $courseid;
    } else {
        $courseselect = '';
    }

    if ($guestid) {
        $userselect = "AND fc.guestid = :guestid";
        $usergroup = "GROUP BY fc.guestid";
        $params['guestid'] = $guestid;
    } else {
        $userselect = "AND fc.userid = :userid";
        $usergroup = "GROUP BY fc.userid";
        $params['userid'] = $USER->id;
    }

    $sql =  "SELECT MAX(fi.position)
               FROM {feedback_completedtmp} fc, {feedback_valuetmp} fv, {feedback_item} fi
              WHERE fc.id = fv.completed
                    $userselect
                    AND fc.feedback = :feedbackid
                    $courseselect
                    AND fi.id = fv.item
         $usergroup";
    $params['feedbackid'] = $feedbackid;

    $lastpos = $DB->get_field_sql($sql, $params);

    //the index of found pagebreak is the searched pagenumber
    foreach ($allbreaks as $pagenr => $br) {
        if ($lastpos < $br) {
            return $pagenr;
        }
    }
    return count($allbreaks);
}

////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////
//functions to handle the values
////////////////////////////////////////////////

/**
 * cleans the userinput while submitting the form.
 *
 * @deprecated since Moodle 3.1
 * @param mixed $value
 * @return mixed
 */
function feedback_clean_input_value($item, $value) {
    debugging('Function feedback_clean_input_value() is deprecated and does nothing. '
            . 'Items must implement complete_form_element()', DEBUG_DEVELOPER);
}

/**
 * this saves the values of an completed.
 * if the param $tmp is set true so the values are saved temporary in table feedback_valuetmp.
 * if there is already a completed and the userid is set so the values are updated.
 * on all other things new value records will be created.
 *
 * @deprecated since Moodle 3.1
 *
 * @param int $usrid
 * @param boolean $tmp
 * @return mixed false on error or the completeid
 */
function feedback_save_values($usrid, $tmp = false) {
    global $DB;

    debugging('Function feedback_save_values() was deprecated because it did not have '.
            'enough arguments, was not suitable for non-temporary table and was taking '.
            'data directly from input', DEBUG_DEVELOPER);

    $completedid = optional_param('completedid', 0, PARAM_INT);
    $tmpstr = $tmp ? 'tmp' : '';
    $time = time();
    $timemodified = mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time));

    if ($usrid == 0) {
        return feedback_create_values($usrid, $timemodified, $tmp);
    }
    $completed = $DB->get_record('feedback_completed'.$tmpstr, array('id'=>$completedid));
    if (!$completed) {
        return feedback_create_values($usrid, $timemodified, $tmp);
    } else {
        $completed->timemodified = $timemodified;
        return feedback_update_values($completed, $tmp);
    }
}

/**
 * this saves the values from anonymous user such as guest on the main-site
 *
 * @deprecated since Moodle 3.1
 *
 * @param string $guestid the unique guestidentifier
 * @return mixed false on error or the completeid
 */
function feedback_save_guest_values($guestid) {
    global $DB;

    debugging('Function feedback_save_guest_values() was deprecated because it did not have '.
            'enough arguments, was not suitable for non-temporary table and was taking '.
            'data directly from input', DEBUG_DEVELOPER);

    $completedid = optional_param('completedid', false, PARAM_INT);

    $timemodified = time();
    if (!$completed = $DB->get_record('feedback_completedtmp', array('id'=>$completedid))) {
        return feedback_create_values(0, $timemodified, true, $guestid);
    } else {
        $completed->timemodified = $timemodified;
        return feedback_update_values($completed, true);
    }
}

/**
 * get the value from the given item related to the given completed.
 * the value can come as temporary or as permanently value. the deciding is done by $tmp
 *
 * @global object
 * @param int $completeid
 * @param int $itemid
 * @param boolean $tmp
 * @return mixed the value, the type depends on plugin-definition
 */
function feedback_get_item_value($completedid, $itemid, $tmp = false) {
    global $DB;

    $tmpstr = $tmp ? 'tmp' : '';
    $params = array('completed'=>$completedid, 'item'=>$itemid);
    return $DB->get_field('feedback_value'.$tmpstr, 'value', $params);
}

/**
 * compares the value of the itemid related to the completedid with the dependvalue.
 * this is used if a depend item is set.
 * the value can come as temporary or as permanently value. the deciding is done by $tmp.
 *
 * @param int $completedid
 * @param stdClass|int $item
 * @param mixed $dependvalue
 * @param bool $tmp
 * @return bool
 */
function feedback_compare_item_value($completedid, $item, $dependvalue, $tmp = false) {
    global $DB;

    if (is_int($item)) {
        $item = $DB->get_record('feedback_item', array('id' => $item));
    }

    $dbvalue = feedback_get_item_value($completedid, $item->id, $tmp);

    $itemobj = feedback_get_item_class($item->typ);
    return $itemobj->compare_value($item, $dbvalue, $dependvalue); //true or false
}

/**
 * this function checks the correctness of values.
 * the rules for this are implemented in the class of each item.
 * it can be the required attribute or the value self e.g. numeric.
 * the params first/lastitem are given to determine the visible range between pagebreaks.
 *
 * @global object
 * @param int $firstitem the position of firstitem for checking
 * @param int $lastitem the position of lastitem for checking
 * @return boolean
 */
function feedback_check_values($firstitem, $lastitem) {
    debugging('Function feedback_check_values() is deprecated and does nothing. '
            . 'Items must implement complete_form_element()', DEBUG_DEVELOPER);
    return true;
}

/**
 * this function create a complete-record and the related value-records.
 * depending on the $tmp (true/false) the values are saved temporary or permanently
 *
 * @deprecated since Moodle 3.1
 *
 * @param int $userid
 * @param int $timemodified
 * @param boolean $tmp
 * @param string $guestid a unique identifier to save temporary data
 * @return mixed false on error or the completedid
 */
function feedback_create_values($usrid, $timemodified, $tmp = false, $guestid = false) {
    global $DB;

    debugging('Function feedback_create_values() was deprecated because it did not have '.
            'enough arguments, was not suitable for non-temporary table and was taking '.
            'data directly from input', DEBUG_DEVELOPER);

    $tmpstr = $tmp ? 'tmp' : '';
    //first we create a new completed record
    $completed = new stdClass();
    $completed->feedback           = $feedbackid;
    $completed->userid             = $usrid;
    $completed->guestid            = $guestid;
    $completed->timemodified       = $timemodified;
    $completed->anonymous_response = $anonymous_response;

    $completedid = $DB->insert_record('feedback_completed'.$tmpstr, $completed);

    $completed = $DB->get_record('feedback_completed'.$tmpstr, array('id'=>$completedid));

    //the keys are in the form like abc_xxx
    //with explode we make an array with(abc, xxx) and (abc=typ und xxx=itemnr)

    //get the items of the feedback
    if (!$allitems = $DB->get_records('feedback_item', array('feedback'=>$completed->feedback))) {
        return false;
    }
    foreach ($allitems as $item) {
        if (!$item->hasvalue) {
            continue;
        }
        //get the class of item-typ
        $itemobj = feedback_get_item_class($item->typ);

        $keyname = $item->typ.'_'.$item->id;

        if ($item->typ === 'multichoice') {
            $itemvalue = optional_param_array($keyname, null, PARAM_INT);
        } else {
            $itemvalue = optional_param($keyname, null, PARAM_NOTAGS);
        }

        if (is_null($itemvalue)) {
            continue;
        }

        $value = new stdClass();
        $value->item = $item->id;
        $value->completed = $completed->id;
        $value->course_id = $courseid;

        //the kind of values can be absolutely different
        //so we run create_value directly by the item-class
        $value->value = $itemobj->create_value($itemvalue);
        $DB->insert_record('feedback_value'.$tmpstr, $value);
    }
    return $completed->id;
}

/**
 * this function updates a complete-record and the related value-records.
 * depending on the $tmp (true/false) the values are saved temporary or permanently
 *
 * @global object
 * @param object $completed
 * @param boolean $tmp
 * @return int the completedid
 */
function feedback_update_values($completed, $tmp = false) {
    global $DB;

    debugging('Function feedback_update_values() was deprecated because it did not have '.
            'enough arguments, was not suitable for non-temporary table and was taking '.
            'data directly from input', DEBUG_DEVELOPER);

    $courseid = optional_param('courseid', false, PARAM_INT);
    $tmpstr = $tmp ? 'tmp' : '';

    $DB->update_record('feedback_completed'.$tmpstr, $completed);
    //get the values of this completed
    $values = $DB->get_records('feedback_value'.$tmpstr, array('completed'=>$completed->id));

    //get the items of the feedback
    if (!$allitems = $DB->get_records('feedback_item', array('feedback'=>$completed->feedback))) {
        return false;
    }
    foreach ($allitems as $item) {
        if (!$item->hasvalue) {
            continue;
        }
        //get the class of item-typ
        $itemobj = feedback_get_item_class($item->typ);

        $keyname = $item->typ.'_'.$item->id;

        if ($item->typ === 'multichoice') {
            $itemvalue = optional_param_array($keyname, null, PARAM_INT);
        } else {
            $itemvalue = optional_param($keyname, null, PARAM_NOTAGS);
        }

        //is the itemvalue set (could be a subset of items because pagebreak)?
        if (is_null($itemvalue)) {
            continue;
        }

        $newvalue = new stdClass();
        $newvalue->item = $item->id;
        $newvalue->completed = $completed->id;
        $newvalue->course_id = $courseid;

        //the kind of values can be absolutely different
        //so we run create_value directly by the item-class
        $newvalue->value = $itemobj->create_value($itemvalue);

        //check, if we have to create or update the value
        $exist = false;
        foreach ($values as $value) {
            if ($value->item == $newvalue->item) {
                $newvalue->id = $value->id;
                $exist = true;
                break;
            }
        }
        if ($exist) {
            $DB->update_record('feedback_value'.$tmpstr, $newvalue);
        } else {
            $DB->insert_record('feedback_value'.$tmpstr, $newvalue);
        }
    }

    return $completed->id;
}

/**
 * get the values of an item depending on the given groupid.
 * if the feedback is anonymous so the values are shuffled
 *
 * @global object
 * @global object
 * @param object $item
 * @param int $groupid
 * @param int $courseid
 * @param bool $ignore_empty if this is set true so empty values are not delivered
 * @return array the value-records
 */
function feedback_get_group_values($item,
                                   $groupid = false,
                                   $courseid = false,
                                   $ignore_empty = false) {

    global $CFG, $DB;

    //if the groupid is given?
    if (intval($groupid) > 0) {
        $params = array();
        if ($ignore_empty) {
            $value = $DB->sql_compare_text('fbv.value');
            $ignore_empty_select = "AND $value != :emptyvalue AND $value != :zerovalue";
            $params += array('emptyvalue' => '', 'zerovalue' => '0');
        } else {
            $ignore_empty_select = "";
        }

        $query = 'SELECT fbv .  *
                    FROM {feedback_value} fbv, {feedback_completed} fbc, {groups_members} gm
                   WHERE fbv.item = :itemid
                         AND fbv.completed = fbc.id
                         AND fbc.userid = gm.userid
                         '.$ignore_empty_select.'
                         AND gm.groupid = :groupid
                ORDER BY fbc.timemodified';
        $params += array('itemid' => $item->id, 'groupid' => $groupid);
        $values = $DB->get_records_sql($query, $params);

    } else {
        $params = array();
        if ($ignore_empty) {
            $value = $DB->sql_compare_text('value');
            $ignore_empty_select = "AND $value != :emptyvalue AND $value != :zerovalue";
            $params += array('emptyvalue' => '', 'zerovalue' => '0');
        } else {
            $ignore_empty_select = "";
        }

        if ($courseid) {
            $select = "item = :itemid AND course_id = :courseid ".$ignore_empty_select;
            $params += array('itemid' => $item->id, 'courseid' => $courseid);
            $values = $DB->get_records_select('feedback_value', $select, $params);
        } else {
            $select = "item = :itemid ".$ignore_empty_select;
            $params += array('itemid' => $item->id);
            $values = $DB->get_records_select('feedback_value', $select, $params);
        }
    }
    $params = array('id'=>$item->feedback);
    if ($DB->get_field('feedback', 'anonymous', $params) == FEEDBACK_ANONYMOUS_YES) {
        if (is_array($values)) {
            shuffle($values);
        }
    }
    return $values;
}

/**
 * check for multiple_submit = false.
 * if the feedback is global so the courseid must be given
 *
 * @global object
 * @global object
 * @param int $feedbackid
 * @param int $courseid
 * @return boolean true if the feedback already is submitted otherwise false
 */
function feedback_is_already_submitted($feedbackid, $courseid = false) {
    global $USER, $DB;

    if (!isloggedin() || isguestuser()) {
        return false;
    }

    $params = array('userid' => $USER->id, 'feedback' => $feedbackid);
    if ($courseid) {
        $params['courseid'] = $courseid;
    }
    return $DB->record_exists('feedback_completed', $params);
}

/**
 * if the completion of a feedback will be continued eg.
 * by pagebreak or by multiple submit so the complete must be found.
 * if the param $tmp is set true so all things are related to temporary completeds
 *
 * @deprecated since Moodle 3.1
 * @param int $feedbackid
 * @param boolean $tmp
 * @param int $courseid
 * @param string $guestid
 * @return int the id of the found completed
 */
function feedback_get_current_completed($feedbackid,
                                        $tmp = false,
                                        $courseid = false,
                                        $guestid = false) {

    debugging('Function feedback_get_current_completed() is deprecated. Please use either '.
            'feedback_get_current_completed_tmp() or feedback_get_last_completed()',
            DEBUG_DEVELOPER);

    global $USER, $CFG, $DB;

    $tmpstr = $tmp ? 'tmp' : '';

    if (!$courseid) {
        if ($guestid) {
            $params = array('feedback'=>$feedbackid, 'guestid'=>$guestid);
            return $DB->get_record('feedback_completed'.$tmpstr, $params);
        } else {
            $params = array('feedback'=>$feedbackid, 'userid'=>$USER->id);
            return $DB->get_record('feedback_completed'.$tmpstr, $params);
        }
    }

    $params = array();

    if ($guestid) {
        $userselect = "AND fc.guestid = :guestid";
        $params['guestid'] = $guestid;
    } else {
        $userselect = "AND fc.userid = :userid";
        $params['userid'] = $USER->id;
    }
    //if courseid is set the feedback is global.
    //there can be more than one completed on one feedback
    $sql =  "SELECT DISTINCT fc.*
               FROM {feedback_value{$tmpstr}} fv, {feedback_completed{$tmpstr}} fc
              WHERE fv.course_id = :courseid
                    AND fv.completed = fc.id
                    $userselect
                    AND fc.feedback = :feedbackid";
    $params['courseid']   = intval($courseid);
    $params['feedbackid'] = $feedbackid;

    if (!$sqlresult = $DB->get_records_sql($sql, $params)) {
        return false;
    }
    foreach ($sqlresult as $r) {
        return $DB->get_record('feedback_completed'.$tmpstr, array('id'=>$r->id));
    }
}

/**
 * get the completeds depending on the given groupid.
 *
 * @global object
 * @global object
 * @param object $feedback
 * @param int $groupid
 * @param int $courseid
 * @return mixed array of found completeds otherwise false
 */
function feedback_get_completeds_group($feedback, $groupid = false, $courseid = false) {
    global $CFG, $DB;

    if (intval($groupid) > 0) {
        $query = "SELECT fbc.*
                    FROM {feedback_completed} fbc, {groups_members} gm
                   WHERE fbc.feedback = ?
                         AND gm.groupid = ?
                         AND fbc.userid = gm.userid";
        if ($values = $DB->get_records_sql($query, array($feedback->id, $groupid))) {
            return $values;
        } else {
            return false;
        }
    } else {
        if ($courseid) {
            $query = "SELECT DISTINCT fbc.*
                        FROM {feedback_completed} fbc, {feedback_value} fbv
                        WHERE fbc.id = fbv.completed
                            AND fbc.feedback = ?
                            AND fbv.course_id = ?
                        ORDER BY random_response";
            if ($values = $DB->get_records_sql($query, array($feedback->id, $courseid))) {
                return $values;
            } else {
                return false;
            }
        } else {
            if ($values = $DB->get_records('feedback_completed', array('feedback'=>$feedback->id))) {
                return $values;
            } else {
                return false;
            }
        }
    }
}

/**
 * get the count of completeds depending on the given groupid.
 *
 * @global object
 * @global object
 * @param object $feedback
 * @param int $groupid
 * @param int $courseid
 * @return mixed count of completeds or false
 */
function feedback_get_completeds_group_count($feedback, $groupid = false, $courseid = false) {
    global $CFG, $DB;

    if ($courseid > 0 AND !$groupid <= 0) {
        $sql = "SELECT id, COUNT(item) AS ci
                  FROM {feedback_value}
                 WHERE course_id  = ?
              GROUP BY item ORDER BY ci DESC";
        if ($foundrecs = $DB->get_records_sql($sql, array($courseid))) {
            $foundrecs = array_values($foundrecs);
            return $foundrecs[0]->ci;
        }
        return false;
    }
    if ($values = feedback_get_completeds_group($feedback, $groupid)) {
        return count($values);
    } else {
        return false;
    }
}

/**
 * deletes all completed-recordsets from a feedback.
 * all related data such as values also will be deleted
 *
 * @param stdClass|int $feedback
 * @param stdClass|cm_info $cm
 * @param stdClass $course
 * @return void
 */
function feedback_delete_all_completeds($feedback, $cm = null, $course = null) {
    global $DB;

    if (is_int($feedback)) {
        $feedback = $DB->get_record('feedback', array('id' => $feedback));
    }

    if (!$completeds = $DB->get_records('feedback_completed', array('feedback' => $feedback->id))) {
        return;
    }

    if (!$course && !($course = $DB->get_record('course', array('id' => $feedback->course)))) {
        return false;
    }

    if (!$cm && !($cm = get_coursemodule_from_instance('feedback', $feedback->id))) {
        return false;
    }

    foreach ($completeds as $completed) {
        feedback_delete_completed($completed, $feedback, $cm, $course);
    }
}

/**
 * deletes a completed given by completedid.
 * all related data such values or tracking data also will be deleted
 *
 * @param int|stdClass $completed
 * @param stdClass $feedback
 * @param stdClass|cm_info $cm
 * @param stdClass $course
 * @return boolean
 */
function feedback_delete_completed($completed, $feedback = null, $cm = null, $course = null) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/completionlib.php');

    if (!isset($completed->id)) {
        if (!$completed = $DB->get_record('feedback_completed', array('id' => $completed))) {
            return false;
        }
    }

    if (!$feedback && !($feedback = $DB->get_record('feedback', array('id' => $completed->feedback)))) {
        return false;
    }

    if (!$course && !($course = $DB->get_record('course', array('id' => $feedback->course)))) {
        return false;
    }

    if (!$cm && !($cm = get_coursemodule_from_instance('feedback', $feedback->id))) {
        return false;
    }

    //first we delete all related values
    $DB->delete_records('feedback_value', array('completed' => $completed->id));

    // Update completion state
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm) && $feedback->completionsubmit) {
        $completion->update_state($cm, COMPLETION_INCOMPLETE, $completed->userid);
    }
    // Last we delete the completed-record.
    $return = $DB->delete_records('feedback_completed', array('id' => $completed->id));

    // Trigger event for the delete action we performed.
    $event = \mod_feedback\event\response_deleted::create_from_record($completed, $cm, $feedback);
    $event->trigger();

    return $return;
}

////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////
//functions to handle sitecourse mapping
////////////////////////////////////////////////

/**
 * checks if the course and the feedback is in the table feedback_sitecourse_map.
 *
 * @deprecated since 3.1
 * @param int $feedbackid
 * @param int $courseid
 * @return int the count of records
 */
function feedback_is_course_in_sitecourse_map($feedbackid, $courseid) {
    debugging('Function feedback_is_course_in_sitecourse_map() is deprecated because it was not used.',
            DEBUG_DEVELOPER);
    global $DB;
    $params = array('feedbackid'=>$feedbackid, 'courseid'=>$courseid);
    return $DB->count_records('feedback_sitecourse_map', $params);
}

/**
 * checks if the feedback is in the table feedback_sitecourse_map.
 *
 * @deprecated since 3.1
 * @param int $feedbackid
 * @return boolean
 */
function feedback_is_feedback_in_sitecourse_map($feedbackid) {
    debugging('Function feedback_is_feedback_in_sitecourse_map() is deprecated because it was not used.',
            DEBUG_DEVELOPER);
    global $DB;
    return $DB->record_exists('feedback_sitecourse_map', array('feedbackid'=>$feedbackid));
}

/**
 * gets the feedbacks from table feedback_sitecourse_map.
 * this is used to show the global feedbacks on the feedback block
 * all feedbacks with the following criteria will be selected:<br />
 *
 * 1) all feedbacks which id are listed together with the courseid in sitecoursemap and<br />
 * 2) all feedbacks which not are listed in sitecoursemap
 *
 * @global object
 * @param int $courseid
 * @return array the feedback-records
 */
function feedback_get_feedbacks_from_sitecourse_map($courseid) {
    global $DB;

    //first get all feedbacks listed in sitecourse_map with named courseid
    $sql = "SELECT f.id AS id,
                   cm.id AS cmid,
                   f.name AS name,
                   f.timeopen AS timeopen,
                   f.timeclose AS timeclose
            FROM {feedback} f, {course_modules} cm, {feedback_sitecourse_map} sm, {modules} m
            WHERE f.id = cm.instance
                   AND f.course = '".SITEID."'
                   AND m.id = cm.module
                   AND m.name = 'feedback'
                   AND sm.courseid = ?
                   AND sm.feedbackid = f.id";

    if (!$feedbacks1 = $DB->get_records_sql($sql, array($courseid))) {
        $feedbacks1 = array();
    }

    //second get all feedbacks not listed in sitecourse_map
    $feedbacks2 = array();
    $sql = "SELECT f.id AS id,
                   cm.id AS cmid,
                   f.name AS name,
                   f.timeopen AS timeopen,
                   f.timeclose AS timeclose
            FROM {feedback} f, {course_modules} cm, {modules} m
            WHERE f.id = cm.instance
                   AND f.course = '".SITEID."'
                   AND m.id = cm.module
                   AND m.name = 'feedback'";
    if (!$allfeedbacks = $DB->get_records_sql($sql)) {
        $allfeedbacks = array();
    }
    foreach ($allfeedbacks as $a) {
        if (!$DB->record_exists('feedback_sitecourse_map', array('feedbackid'=>$a->id))) {
            $feedbacks2[] = $a;
        }
    }

    $feedbacks = array_merge($feedbacks1, $feedbacks2);
    $modinfo = get_fast_modinfo(SITEID);
    return array_filter($feedbacks, function($f) use ($modinfo) {
        return ($cm = $modinfo->get_cm($f->cmid)) && $cm->uservisible;
    });

}

/**
 * Gets the courses from table feedback_sitecourse_map
 *
 * @param int $feedbackid
 * @return array the course-records
 */
function feedback_get_courses_from_sitecourse_map($feedbackid) {
    global $DB;

    $sql = "SELECT c.id, c.fullname, c.shortname
              FROM {feedback_sitecourse_map} f, {course} c
             WHERE c.id = f.courseid
                   AND f.feedbackid = ?
          ORDER BY c.fullname";

    return $DB->get_records_sql($sql, array($feedbackid));

}

/**
 * Updates the course mapping for the feedback
 *
 * @param stdClass $feedback
 * @param array $courses array of course ids
 */
function feedback_update_sitecourse_map($feedback, $courses) {
    global $DB;
    if (empty($courses)) {
        $courses = array();
    }
    $currentmapping = $DB->get_fieldset_select('feedback_sitecourse_map', 'courseid', 'feedbackid=?', array($feedback->id));
    foreach (array_diff($courses, $currentmapping) as $courseid) {
        $DB->insert_record('feedback_sitecourse_map', array('feedbackid' => $feedback->id, 'courseid' => $courseid));
    }
    foreach (array_diff($currentmapping, $courses) as $courseid) {
        $DB->delete_records('feedback_sitecourse_map', array('feedbackid' => $feedback->id, 'courseid' => $courseid));
    }
    // TODO MDL-53574 add events.
}

/**
 * removes non existing courses or feedbacks from sitecourse_map.
 * it shouldn't be called all too often
 * a good place for it could be the mapcourse.php or unmapcourse.php
 *
 * @deprecated since 3.1
 * @global object
 * @return void
 */
function feedback_clean_up_sitecourse_map() {
    global $DB;
    debugging('Function feedback_clean_up_sitecourse_map() is deprecated because it was not used.',
            DEBUG_DEVELOPER);

    $maps = $DB->get_records('feedback_sitecourse_map');
    foreach ($maps as $map) {
        if (!$DB->get_record('course', array('id'=>$map->courseid))) {
            $params = array('courseid'=>$map->courseid, 'feedbackid'=>$map->feedbackid);
            $DB->delete_records('feedback_sitecourse_map', $params);
            continue;
        }
        if (!$DB->get_record('feedback', array('id'=>$map->feedbackid))) {
            $params = array('courseid'=>$map->courseid, 'feedbackid'=>$map->feedbackid);
            $DB->delete_records('feedback_sitecourse_map', $params);
            continue;
        }

    }
}

////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////
//not relatable functions
////////////////////////////////////////////////

/**
 * prints the option items of a selection-input item (dropdownlist).
 * @deprecated since 3.1
 * @param int $startval the first value of the list
 * @param int $endval the last value of the list
 * @param int $selectval which item should be selected
 * @param int $interval the stepsize from the first to the last value
 * @return void
 */
function feedback_print_numeric_option_list($startval, $endval, $selectval = '', $interval = 1) {
    debugging('Function feedback_print_numeric_option_list() is deprecated because it was not used.',
            DEBUG_DEVELOPER);
    for ($i = $startval; $i <= $endval; $i += $interval) {
        if ($selectval == ($i)) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        echo '<option '.$selected.'>'.$i.'</option>';
    }
}

/**
 * sends an email to the teachers of the course where the given feedback is placed.
 *
 * @global object
 * @global object
 * @uses FEEDBACK_ANONYMOUS_NO
 * @uses FORMAT_PLAIN
 * @param object $cm the coursemodule-record
 * @param object $feedback
 * @param object $course
 * @param stdClass|int $user
 * @return void
 */
function feedback_send_email($cm, $feedback, $course, $user) {
    global $CFG, $DB;

    if ($feedback->email_notification == 0) {  // No need to do anything
        return;
    }

    if (is_int($user)) {
        $user = $DB->get_record('user', array('id' => $user));
    }

    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        $groupmode =  $cm->groupmode;
    } else {
        $groupmode = $course->groupmode;
    }

    if ($groupmode == SEPARATEGROUPS) {
        $groups = $DB->get_records_sql_menu("SELECT g.name, g.id
                                               FROM {groups} g, {groups_members} m
                                              WHERE g.courseid = ?
                                                    AND g.id = m.groupid
                                                    AND m.userid = ?
                                           ORDER BY name ASC", array($course->id, $user->id));
        $groups = array_values($groups);

        $teachers = feedback_get_receivemail_users($cm->id, $groups);
    } else {
        $teachers = feedback_get_receivemail_users($cm->id);
    }

    if ($teachers) {

        $strfeedbacks = get_string('modulenameplural', 'feedback');
        $strfeedback  = get_string('modulename', 'feedback');

        if ($feedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
            $printusername = fullname($user);
        } else {
            $printusername = get_string('anonymous_user', 'feedback');
        }

        foreach ($teachers as $teacher) {
            $info = new stdClass();
            $info->username = $printusername;
            $info->feedback = format_string($feedback->name, true);
            $info->url = $CFG->wwwroot.'/mod/feedback/show_entries.php?'.
                            'id='.$cm->id.'&'.
                            'userid=' . $user->id;

            $a = array('username' => $info->username, 'feedbackname' => $feedback->name);

            $postsubject = get_string('feedbackcompleted', 'feedback', $a);
            $posttext = feedback_send_email_text($info, $course);

            if ($teacher->mailformat == 1) {
                $posthtml = feedback_send_email_html($info, $course, $cm);
            } else {
                $posthtml = '';
            }

            if ($feedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
                $eventdata = new stdClass();
                $eventdata->name             = 'submission';
                $eventdata->component        = 'mod_feedback';
                $eventdata->userfrom         = $user;
                $eventdata->userto           = $teacher;
                $eventdata->subject          = $postsubject;
                $eventdata->fullmessage      = $posttext;
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml  = $posthtml;
                $eventdata->smallmessage     = '';
                message_send($eventdata);
            } else {
                $eventdata = new stdClass();
                $eventdata->name             = 'submission';
                $eventdata->component        = 'mod_feedback';
                $eventdata->userfrom         = $teacher;
                $eventdata->userto           = $teacher;
                $eventdata->subject          = $postsubject;
                $eventdata->fullmessage      = $posttext;
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml  = $posthtml;
                $eventdata->smallmessage     = '';
                message_send($eventdata);
            }
        }
    }
}

/**
 * sends an email to the teachers of the course where the given feedback is placed.
 *
 * @global object
 * @uses FORMAT_PLAIN
 * @param object $cm the coursemodule-record
 * @param object $feedback
 * @param object $course
 * @return void
 */
function feedback_send_email_anonym($cm, $feedback, $course) {
    global $CFG;

    if ($feedback->email_notification == 0) { // No need to do anything
        return;
    }

    $teachers = feedback_get_receivemail_users($cm->id);

    if ($teachers) {

        $strfeedbacks = get_string('modulenameplural', 'feedback');
        $strfeedback  = get_string('modulename', 'feedback');
        $printusername = get_string('anonymous_user', 'feedback');

        foreach ($teachers as $teacher) {
            $info = new stdClass();
            $info->username = $printusername;
            $info->feedback = format_string($feedback->name, true);
            $info->url = $CFG->wwwroot.'/mod/feedback/show_entries.php?id=' . $cm->id;

            $a = array('username' => $info->username, 'feedbackname' => $feedback->name);

            $postsubject = get_string('feedbackcompleted', 'feedback', $a);
            $posttext = feedback_send_email_text($info, $course);

            if ($teacher->mailformat == 1) {
                $posthtml = feedback_send_email_html($info, $course, $cm);
            } else {
                $posthtml = '';
            }

            $eventdata = new stdClass();
            $eventdata->name             = 'submission';
            $eventdata->component        = 'mod_feedback';
            $eventdata->userfrom         = $teacher;
            $eventdata->userto           = $teacher;
            $eventdata->subject          = $postsubject;
            $eventdata->fullmessage      = $posttext;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml  = $posthtml;
            $eventdata->smallmessage     = '';
            message_send($eventdata);
        }
    }
}

/**
 * send the text-part of the email
 *
 * @param object $info includes some infos about the feedback you want to send
 * @param object $course
 * @return string the text you want to post
 */
function feedback_send_email_text($info, $course) {
    $coursecontext = context_course::instance($course->id);
    $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
    $posttext  = $courseshortname.' -> '.get_string('modulenameplural', 'feedback').' -> '.
                    $info->feedback."\n";
    $posttext .= '---------------------------------------------------------------------'."\n";
    $posttext .= get_string("emailteachermail", "feedback", $info)."\n";
    $posttext .= '---------------------------------------------------------------------'."\n";
    return $posttext;
}


/**
 * send the html-part of the email
 *
 * @global object
 * @param object $info includes some infos about the feedback you want to send
 * @param object $course
 * @return string the text you want to post
 */
function feedback_send_email_html($info, $course, $cm) {
    global $CFG;
    $coursecontext = context_course::instance($course->id);
    $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
    $course_url = $CFG->wwwroot.'/course/view.php?id='.$course->id;
    $feedback_all_url = $CFG->wwwroot.'/mod/feedback/index.php?id='.$course->id;
    $feedback_url = $CFG->wwwroot.'/mod/feedback/view.php?id='.$cm->id;

    $posthtml = '<p><font face="sans-serif">'.
            '<a href="'.$course_url.'">'.$courseshortname.'</a> ->'.
            '<a href="'.$feedback_all_url.'">'.get_string('modulenameplural', 'feedback').'</a> ->'.
            '<a href="'.$feedback_url.'">'.$info->feedback.'</a></font></p>';
    $posthtml .= '<hr /><font face="sans-serif">';
    $posthtml .= '<p>'.get_string('emailteachermailhtml', 'feedback', $info).'</p>';
    $posthtml .= '</font><hr />';
    return $posthtml;
}

/**
 * @param string $url
 * @return string
 */
function feedback_encode_target_url($url) {
    if (strpos($url, '?')) {
        list($part1, $part2) = explode('?', $url, 2); //maximal 2 parts
        return $part1 . '?' . htmlentities($part2);
    } else {
        return $url;
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $feedbacknode The node to add module settings to
 */
function feedback_extend_settings_navigation(settings_navigation $settings,
                                             navigation_node $feedbacknode) {

    global $PAGE;

    if (!$context = context_module::instance($PAGE->cm->id, IGNORE_MISSING)) {
        print_error('badcontext');
    }

    if (has_capability('mod/feedback:edititems', $context)) {
        $questionnode = $feedbacknode->add(get_string('questions', 'feedback'));

        $questionnode->add(get_string('edit_items', 'feedback'),
                    new moodle_url('/mod/feedback/edit.php',
                                    array('id' => $PAGE->cm->id,
                                          'do_show' => 'edit')));

        $questionnode->add(get_string('export_questions', 'feedback'),
                    new moodle_url('/mod/feedback/export.php',
                                    array('id' => $PAGE->cm->id,
                                          'action' => 'exportfile')));

        $questionnode->add(get_string('import_questions', 'feedback'),
                    new moodle_url('/mod/feedback/import.php',
                                    array('id' => $PAGE->cm->id)));

        $questionnode->add(get_string('templates', 'feedback'),
                    new moodle_url('/mod/feedback/edit.php',
                                    array('id' => $PAGE->cm->id,
                                          'do_show' => 'templates')));
    }

    if (has_capability('mod/feedback:mapcourse', $context) && $PAGE->course->id == SITEID) {
        $feedbacknode->add(get_string('mappedcourses', 'feedback'),
                    new moodle_url('/mod/feedback/mapcourse.php',
                                    array('id' => $PAGE->cm->id)));
    }

    if (has_capability('mod/feedback:viewreports', $context)) {
        $feedback = $PAGE->activityrecord;
        if ($feedback->course == SITEID) {
            $feedbacknode->add(get_string('analysis', 'feedback'),
                    new moodle_url('/mod/feedback/analysis_course.php',
                                    array('id' => $PAGE->cm->id)));
        } else {
            $feedbacknode->add(get_string('analysis', 'feedback'),
                    new moodle_url('/mod/feedback/analysis.php',
                                    array('id' => $PAGE->cm->id)));
        }

        $feedbacknode->add(get_string('show_entries', 'feedback'),
                    new moodle_url('/mod/feedback/show_entries.php',
                                    array('id' => $PAGE->cm->id)));

        if ($feedback->anonymous == FEEDBACK_ANONYMOUS_NO AND $feedback->course != SITEID) {
            $feedbacknode->add(get_string('show_nonrespondents', 'feedback'),
                        new moodle_url('/mod/feedback/show_nonrespondents.php',
                                        array('id' => $PAGE->cm->id)));
        }
    }
}

function feedback_init_feedback_session() {
    //initialize the feedback-Session - not nice at all!!
    global $SESSION;
    if (!empty($SESSION)) {
        if (!isset($SESSION->feedback) OR !is_object($SESSION->feedback)) {
            $SESSION->feedback = new stdClass();
        }
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function feedback_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-feedback-*'=>get_string('page-mod-feedback-x', 'feedback'));
    return $module_pagetype;
}

/**
 * Move save the items of the given $feedback in the order of $itemlist.
 * @param string $itemlist a comma separated list with item ids
 * @param stdClass $feedback
 * @return bool true if success
 */
function feedback_ajax_saveitemorder($itemlist, $feedback) {
    global $DB;

    $result = true;
    $position = 0;
    foreach ($itemlist as $itemid) {
        $position++;
        $result = $result && $DB->set_field('feedback_item',
                                            'position',
                                            $position,
                                            array('id'=>$itemid, 'feedback'=>$feedback->id));
    }
    return $result;
}

/**
 * Checks if current user is able to view feedback on this course.
 *
 * @param stdClass $feedback
 * @param context_module $context
 * @param int $courseid
 * @return bool
 */
function feedback_can_view_analysis($feedback, $context, $courseid = false) {
    if (has_capability('mod/feedback:viewreports', $context)) {
        return true;
    }

    if (intval($feedback->publish_stats) != 1 ||
            !has_capability('mod/feedback:viewanalysepage', $context)) {
        return false;
    }

    if (!isloggedin() || isguestuser()) {
        // There is no tracking for the guests, assume that they can view analysis if condition above is satisfied.
        return $feedback->course == SITEID;
    }

    return feedback_is_already_submitted($feedback->id, $courseid);
}
