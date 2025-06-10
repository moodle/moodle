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
 * Plugin's system and internal functions.
 *
 * @package    mod_adaptivequiz
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/engine/lib.php');

use mod_adaptivequiz\local\attempt\attempt_state;

/**
 * Option controlling what options are offered on the quiz settings form.
 */
define('ADAPTIVEQUIZMAXATTEMPT', 10);
define('ADAPTIVEQUIZNAME', 'adaptivequiz');

/**
 * Options determining how the grades from individual attempts are combined to give
 * the overall grade for a user
 */
define('ADAPTIVEQUIZ_GRADEHIGHEST', '1');
define('ADAPTIVEQUIZ_ATTEMPTFIRST', '3');
define('ADAPTIVEQUIZ_ATTEMPTLAST',  '4');

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature: FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function adaptivequiz_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS: {
            return true;
        }
        case FEATURE_GROUPINGS: {
            return true;
        }
        case FEATURE_GROUPMEMBERSONLY: {
            return true;
        }
        case FEATURE_MOD_INTRO: {
            return true;
        }
        case FEATURE_BACKUP_MOODLE2: {
            return true;
        }
        case FEATURE_SHOW_DESCRIPTION: {
            return true;
        }
        case FEATURE_GRADE_HAS_GRADE: {
            return true;
        }
        case FEATURE_USES_QUESTIONS: {
            return true;
        }
        case FEATURE_MOD_PURPOSE: {
            return MOD_PURPOSE_ASSESSMENT;
        }
        case FEATURE_COMPLETION_HAS_RULES: {
            return true;
        }
        default: {
            return null;
        }
    }
}

/**
 * Saves a new instance of the adaptivequiz into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $adaptivequiz: An object from the form in mod_form.php
 * @param mod_adaptivequiz_mod_form $mform: A formslib object
 * @return int The id of the newly inserted adaptivequiz record
 */
function adaptivequiz_add_instance(stdClass $adaptivequiz, mod_adaptivequiz_mod_form $mform = null) {
    global $DB;

    $time = time();
    $adaptivequiz->timecreated = $time;
    $adaptivequiz->timemodified = $time;
    $adaptivequiz->attemptfeedbackformat = 0;

    $instance = $DB->insert_record('adaptivequiz', $adaptivequiz);

    if (empty($instance) && is_int($instance)) {
        return $instance;
    }
    $adaptivequiz->id = $instance;

    // Save question tag association data.
    adaptivequiz_add_questcat_association($adaptivequiz->id, $adaptivequiz);

    // Update related grade item.
    adaptivequiz_grade_item_update($adaptivequiz);

    return $instance;
}

/**
 * This function creates question category association record(s).
 *
 * @param int $instance Activity instance id.
 * @param stdClass $adaptivequiz An object from the form in mod_form.php.
 */
function adaptivequiz_add_questcat_association(int $instance, stdClass $adaptivequiz): void {
    global $DB;

    if (0 != $instance && !empty($adaptivequiz->questionpool)) {
        $qtag = new stdClass();
        $qtag->instance = $instance;

        foreach ($adaptivequiz->questionpool as $questioncatid) {
            $qtag->questioncategory = $questioncatid;
            $DB->insert_record('adaptivequiz_question', $qtag);
        }
    }
}

/**
 * This function updates the question category association records.
 *
 * @param int $instance Activity instance id.
 * @param stdClass $adaptivequiz An object from the form in mod_form.php.
 */
function adaptivequiz_update_questcat_association(int $instance, stdClass $adaptivequiz): void {
    global $DB;

    // Remove old references.
    if (!empty($instance)) {
        $DB->delete_records('adaptivequiz_question', ['instance' => $instance]);
    }

    // Insert new references.
    adaptivequiz_add_questcat_association($instance, $adaptivequiz);
}

/**
 * Updates an instance of the adaptivequiz in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $adaptivequiz: An object from the form in mod_form.php
 * @param mod_adaptivequiz_mod_form $mform: A formslib object
 * @return boolean Success/Fail
 */
function adaptivequiz_update_instance(stdClass $adaptivequiz, mod_adaptivequiz_mod_form $mform = null) {
    global $DB;

    $adaptivequiz->timemodified = time();
    $adaptivequiz->id = $adaptivequiz->instance;

    // Get the current value, so we can see what changed.
    $oldquiz = $DB->get_record('adaptivequiz', array('id' => $adaptivequiz->instance));

    $instanceid = $DB->update_record('adaptivequiz', $adaptivequiz);

    // Save question tag association data.
    adaptivequiz_update_questcat_association($adaptivequiz->id, $adaptivequiz);

    // Update related grade item.
    if ($oldquiz->grademethod != $adaptivequiz->grademethod) {
        adaptivequiz_update_grades($adaptivequiz);
    } else {
        adaptivequiz_grade_item_update($adaptivequiz);
    }

    return $instanceid;
}

/**
 * Removes an instance of the adaptivequiz from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id: Id of the module instance
 * @return boolean Success/Failure
 */
function adaptivequiz_delete_instance($id) {
    global $DB;

    $adaptivequiz = $DB->get_record('adaptivequiz', array('id' => $id));
    if (!$adaptivequiz) {
        return false;
    }

    // Remove question_usage_by_activity records.
    $attempts = $DB->get_records('adaptivequiz_attempt', array('instance' => $id));

    if (!empty($attempts)) {
        foreach ($attempts as $attempt) {
            question_engine::delete_questions_usage_by_activity($attempt->uniqueid);
        }

        // Remove attempts data.
        $DB->delete_records('adaptivequiz_attempt', array('instance' => $id));
    }

    // Remove association table data.
    if ($DB->record_exists('adaptivequiz_question', array ('instance' => $id))) {
        $DB->delete_records('adaptivequiz_question', array('instance' => $id));
    }

    // Delete the quiz record itself.
    $DB->delete_records('adaptivequiz', array('id' => $id));

    // Delete the grade item.
    adaptivequiz_grade_item_delete($adaptivequiz);

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
function adaptivequiz_user_outline($course, $user, $mod, $adaptivequiz) {
    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course: the current course record
 * @param stdClass $user: the record of the user we are generating report for
 * @param cm_info $mod: course module info
 * @param stdClass $adaptivequiz: the module instance record
 * @return void, is supposed to echp directly
 */
function adaptivequiz_user_complete($course, $user, $mod, $adaptivequiz) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in adaptivequiz activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function adaptivequiz_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  // True if anything was printed, otherwise false.
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link adaptivequiz_print_recent_mod_activity()}.
 *
 * @param array $activities: sequentially indexed array of objects with the 'cmid' property
 * @param int $index: the index in the $activities to use for the next record
 * @param int $timestart: append activity since this time
 * @param int $courseid: the id of the course we produce the report for
 * @param int $cmid: course module id
 * @param int $userid: check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid: check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function adaptivequiz_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0) {
    global $COURSE, $DB, $USER;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];
    $adaptivequiz = $DB->get_record('adaptivequiz', array('id' => $cm->instance));

    if ($userid) {
        $userselect = "AND u.id = :userid";
        $params['userid'] = $userid;
    } else {
        $userselect = '';
    }

    if ($groupid) {
        $groupselect = 'AND gm.groupid = :groupid';
        $groupjoin   = 'JOIN {groups_members} gm ON  gm.userid=u.id';
        $params['groupid'] = $groupid;
    } else {
        $groupselect = '';
        $groupjoin   = '';
    }

    $params['timestart'] = $timestart;
    $params['instance'] = $adaptivequiz->id;

    $sql = "SELECT aa.*, u.firstname, u.lastname, u.email, u.picture, u.imagealt
              FROM {adaptivequiz_attempt} aa
                   JOIN {user} u ON u.id = aa.userid
                   $groupjoin
             WHERE aa.timemodified > :timestart
                   AND aa.instance = :instance
                   $userselect
                   $groupselect
          ORDER BY aa.timemodified ASC";
    $rs = $DB->get_recordset_sql($sql, $params);

    // Check if recordset contains records.
    if (!$rs->valid()) {
        return;
    }

    $context         = context_module::instance($cm->id);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $context);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $context);
    $viewreport      = has_capability('mod/adaptivequiz:viewreport', $context);
    $groupmode       = groups_get_activity_groupmode($cm, $course);

    if (is_null($modinfo->groups)) {
        // Load all my groups and cache it in modinfo.
        $modinfo->groups = groups_get_user_groups($course->id);
    }

    $usersgroups = null;
    $aname = format_string($cm->name, true);

    foreach ($rs as $attempt) {
        if ($attempt->userid != $USER->id) {
            if (!$viewreport) {
                // View report permission required to view activity other user attempts.
                continue;
            }

            if ($groupmode == SEPARATEGROUPS && !$accessallgroups) {
                if (is_null($usersgroups)) {
                    $usersgroups = groups_get_all_groups($course->id, $attempt->userid, $cm->groupingid);
                    if (is_array($usersgroups)) {
                        $usersgroups = array_keys($usersgroups);
                    } else {
                        $usersgroups = array();
                    }
                }
                if (!array_intersect($usersgroups, $modinfo->groups[$cm->id])) {
                    continue;
                }
            }
        }

        $tmpactivity = new stdClass();
        $tmpactivity->content = new stdClass();
        $tmpactivity->user = new stdClass();

        $tmpactivity->type       = 'adaptivequiz';
        $tmpactivity->cmid       = $cm->id;
        $tmpactivity->name       = $aname;
        $tmpactivity->sectionnum = $cm->sectionnum;
        $tmpactivity->timestamp  = $attempt->timemodified;

        $tmpactivity->content->attemptid = $attempt->id;
        $tmpactivity->content->attemptstate = get_string('recent'.$attempt->attemptstate, 'adaptivequiz');
        $tmpactivity->content->questionsattempted = $attempt->questionsattempted;

        $tmpactivity->user->id        = $attempt->userid;
        $tmpactivity->user->firstname = $attempt->firstname;
        $tmpactivity->user->lastname  = $attempt->lastname;
        $tmpactivity->user->picture   = $attempt->picture;
        $tmpactivity->user->imagealt  = $attempt->imagealt;
        $tmpactivity->user->email     = $attempt->email;

        $activities[$index++] = $tmpactivity;
    }

    $rs->close();

    return;
}

/**
 * Prints single activity item prepared by {@see adaptivequiz_get_recent_mod_activity()}
 * @param stdClass $activity an object whose properties come from {@see adaptivequiz_get_recent_mod_activity()}
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail set to true to show more detail for the recent activity
 * @param array $modnames an array of module names
 * @param bool $viewfullnames true if the user has the capability to view full names
 * @param bool $return set to true to return output, else false to echo the output
 * @return string|void HTML markup
 */
function adaptivequiz_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames, $return = false) {
    global $CFG, $OUTPUT;

    $output = '';
    $cols = '';
    $contect = '';

    // Define table.
    $attr = array('border' => '0', 'cellpadding' => '3', 'cellspacing' => '0', 'class' => 'adaptivequiz-recent');
    $output .= html_writer::start_tag('table', $attr);

    // Define table columns.
    $attr = array('class' => 'userpicture', 'valign' => 'top');
    $content = $OUTPUT->user_picture($activity->user, array('courseid' => $courseid));
    $cols .= html_writer::tag('td', $content, $attr);

    $content = '';

    if ($detail) {
        $modname = $modnames[$activity->type];
        // Start div.
        $attr = array('class' => 'title');
        $content .= html_writer::start_tag('div', $attr);
        // Create img markup.
        $attr = array('src' => $OUTPUT->image_url('icon', $activity->type), 'class' => 'icon', 'alt' => $modname);
        $content .= html_writer::empty_tag('img', $attr);
        // Create anchor markup.
        $attr = array('href' => "{$CFG->wwwroot}/mod/adaptivequiz/view.php?id={$activity->cmid}",
            'class' => 'icon', 'alt' => $modname);
        $content .= html_writer::tag('a', $activity->name, $attr);
        // End div.
        $content .= html_writer::end_tag('div');
    }

    // Create div with the state of the attempt.
    $attr = array('class' => 'attemptstate');
    $string = get_string('recentattemptstate', 'adaptivequiz');
    $content .= html_writer::tag('div', $string.'&nbsp;'.$activity->content->attemptstate, $attr);
    // Create div with the number of questions attempted.
    $attr = array('class' => 'questionsattempted');
    $string = get_string('recentactquestionsattempted', 'adaptivequiz', $activity->content->questionsattempted);
    $content .= html_writer::tag('div', $string, $attr);

    // Start div.
    $attr = array('class' => 'user');
    $content .= html_writer::start_tag('div', $attr);
    // Create anchor for link to user's profile.
    $attr = array('href' => $CFG->wwwroot.'/user/view.php?id='.$activity->user->id.'&amp;course='.$courseid);
    $fullname = fullname($activity->user, $viewfullnames);
    $content .= html_writer::tag('a', $fullname, $attr);

    // Add timestamp.
    $content .= '&nbsp'.userdate($activity->timestamp);
    // End div.
    $content .= html_writer::end_tag('div');
    // Add all of the data for the columns to the table row.
    $cols .= html_writer::tag('td', $content);
    $output .= html_writer::tag('tr', $cols);
    // End table.
    $output .= html_writer::end_tag('table');

    if (!empty($return)) {
        // The return statemtn is not required, but it here so that this function can be PHPUnit testsed.
        return $output;
    } else {
        // Echo output to the page.
        echo $output;
    }
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 **/
function adaptivequiz_cron() {
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function adaptivequiz_get_extra_capabilities() {
    return array();
}

/**
 * Extends the global navigation tree by adding adaptivequiz nodes if there is a relevant content
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the adaptivequiz module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function adaptivequiz_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * A system callback, allows to add custom nodes to the settings navigation.
 *
 * @param settings_navigation $settingsnav
 * @param navigation_node $adaptivequiznode
 */
function adaptivequiz_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $adaptivequiznode): void {
    if (!has_capability('mod/adaptivequiz:viewreport', $settingsnav->get_page()->cm->context)) {
        return;
    }

    $node = navigation_node::create(get_string('questionanalysisbtn', 'adaptivequiz'),
        new moodle_url('/mod/adaptivequiz/questionanalysis/overview.php', ['cmid' => $settingsnav->get_page()->cm->id]),
        navigation_node::TYPE_SETTING, null, 'mod_adaptivequiz_question_analysis', new pix_icon('i/report', ''));
    $adaptivequiznode->add_node($node);
}

/**
 * Delete the grade item for given quiz
 *
 * @category grade
 * @param object $adaptivequiz object
 * @return int 0 if ok, error code otherwise
 */
function adaptivequiz_grade_item_delete(stdClass $adaptivequiz) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $params = array('deleted' => 1);
    return grade_update('mod/adaptivequiz', $adaptivequiz->course, 'mod', 'adaptivequiz', $adaptivequiz->id, 0, null, $params);
}

/**
 * Create or update the grade item for given quiz
 *
 * @category grade
 * @param object $adaptivequiz object
 * @param mixed $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function adaptivequiz_grade_item_update(stdClass $adaptivequiz, $grades = null) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/adaptivequiz/locallib.php');
    require_once($CFG->libdir . '/gradelib.php');

    if (!empty($adaptivequiz->id)) { // May not be always present.
        $params = array('itemname' => $adaptivequiz->name, 'idnumber' => $adaptivequiz->id);
    } else {
        $params = array('itemname' => $adaptivequiz->name);
    }

    if ($adaptivequiz->highestlevel > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $adaptivequiz->highestlevel;
        $params['grademin']  = $adaptivequiz->lowestlevel;

    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/adaptivequiz', $adaptivequiz->course, 'mod', 'adaptivequiz', $adaptivequiz->id, 0, $grades, $params);
}

function adaptivequiz_update_grades(stdClass $adaptivequiz, $userid=0, $nullifnone = true) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/adaptivequiz/locallib.php');
    require_once($CFG->libdir.'/gradelib.php');

    if ($grades = adaptivequiz_get_user_grades($adaptivequiz, $userid)) {
        // Set all user grades.
        adaptivequiz_grade_item_update($adaptivequiz, $grades);
    } else if ($userid && $nullifnone) {
        // Reset all user grades.
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = null;
        adaptivequiz_grade_item_update($adaptivequiz, $grade);
    } else {
        // Don't change user grades.
        adaptivequiz_grade_item_update($adaptivequiz);
    }
}

/**
 * Called by course/reset.php
 */
function adaptivequiz_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'apaptivequizheader', get_string('modulenameplural', 'adaptivequiz'));
    $mform->addElement('checkbox', 'reset_adaptivequiz_all', get_string('resetadaptivequizsall', 'adaptivequiz'));
}

/**
 * Course reset form defaults.
 */
function adaptivequiz_reset_course_form_defaults($course) {
    return array('reset_adaptivequiz_all' => 0);
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all attempts from the specified adaptivequiz
 * and clean up any related data.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function adaptivequiz_reset_userdata($data) {
    global $CFG, $DB;

    $componentstr = get_string('modulenameplural', 'adaptivequiz');
    $status = array();

    // Delete our attempts.
    if (!empty($data->reset_adaptivequiz_all)) {
        $adaptivequizes = $DB->get_records('adaptivequiz', array('course' => $data->courseid));
        foreach ($adaptivequizes as $adaptivequiz) {
            $attempts = $DB->get_records('adaptivequiz_attempt', array('instance' => $adaptivequiz->id));
            if (!empty($attempts)) {
                // Remove question_usage_by_activity records.
                foreach ($attempts as $attempt) {
                    question_engine::delete_questions_usage_by_activity($attempt->uniqueid);
                }

                // Remove attempts data.
                $DB->delete_records('adaptivequiz_attempt', array('instance' => $adaptivequiz->id));
            }
        }
    }
    $status[] = array(
        'component' => $componentstr,
        'item' => get_string('all_attempts_deleted', 'adaptivequiz'),
        'error' => false,
    );

    // Delete our grades.
    if (!empty($data->reset_gradebook_grades)) {
        adaptivequiz_reset_gradebook($data->courseid);
        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('all_grades_removed', 'adaptivequiz'),
            'error' => false,
        );
    }

    return $status;
}

/**
 * Removes all grades from gradebook
 *
 * @param int $courseid The ID of the course to reset
 */
function adaptivequiz_reset_gradebook($courseid) {
    global $CFG, $DB;

    $adaptivequizes = $DB->get_records('adaptivequiz', array('course' => $courseid));
    foreach ($adaptivequizes as $adaptivequiz) {
        adaptivequiz_grade_item_update($adaptivequiz, 'reset');
    }
}

/**
 * Called via pluginfile.php -> question_pluginfile to serve files belonging to a question in a question_attempt when that attempt
 * is a quiz attempt.
 *
 * @param stdClass $course Course settings object.
 * @param context $context
 * @param string $component The name of the component we are serving files for.
 * @param string $filearea The name of the file area.
 * @param int $qubaid The attempt usage id.
 * @param int $slot The id of a question in this quiz attempt.
 * @param array $args The remaining bits of the file path.
 * @param bool $forcedownload Whether the user must be forced to download the file.
 * @param array $options Additional options affecting the file serving.
 * @return bool False if file not found, does not return if found - just send the file.
 */
function mod_adaptivequiz_question_pluginfile($course, context $context, $component, $filearea, $qubaid, $slot, $args,
    $forcedownload, array $options=[]) {
    global $CFG, $DB, $USER;

    $attemptrec = $DB->get_record('adaptivequiz_attempt', ['uniqueid' => $qubaid], '*', MUST_EXIST);
    $adaptivequiz  = $DB->get_record('adaptivequiz', ['id' => $attemptrec->instance], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $adaptivequiz->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('adaptivequiz', $adaptivequiz->id, $adaptivequiz->course, false, MUST_EXIST);

    require_login($course, true, $cm);

    $modcontext = context_module::instance($cm->id);

    // Check if the user has the attempt capability.
    if (!has_capability('mod/adaptivequiz:attempt', $modcontext) && !has_capability('mod/adaptivequiz:viewreport', $modcontext)) {
        throw new moodle_exception('nopermission', 'adaptivequiz');
    }

    // If we are reviewing an attempt, require the viewreport capability.
    if ($attemptrec->userid != $USER->id) {
        require_capability('mod/adaptivequiz:viewreport', $modcontext);
    } else {
        // Otherwise, check that the attempt is active.
        require_once($CFG->dirroot.'/mod/adaptivequiz/locallib.php');

        // Check if the user has any previous attempts at this activity.
        $count = adaptivequiz_count_user_previous_attempts($adaptivequiz->id, $USER->id);
        if (!adaptivequiz_allowed_attempt($adaptivequiz->attempts, $count)) {
            throw new moodle_exception('noattemptsallowed', 'adaptivequiz');
        }
        // Check if the uniqueid belongs to the same attempt record the user is currently using.
        if (!adaptivequiz_uniqueid_part_of_attempt($qubaid, $cm->instance, $USER->id)) {
            throw new moodle_exception('uniquenotpartofattempt', 'adaptivequiz');
        }
        // Verify that the attempt is still in progress.
        if ($attemptrec->attemptstate != attempt_state::IN_PROGRESS) {
            throw new moodle_exception('notinprogress', 'adaptivequiz');
        }
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/$component/$filearea/$relativepath";

    $file = $fs->get_file_by_hash(sha1($fullpath));
    if (!$file) {
        send_file_not_found();
    }
    if ($file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * A system callback.
 *
 * Given a course_module object, this function returns any "extra" information that may be needed when printing this activity
 * in a course listing. See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule the course module object (record).
 * @return false|cached_cm_info
 */
function adaptivequiz_get_coursemodule_info(stdClass $coursemodule) {
    global $DB;

    if (!$adaptivequiz = $DB->get_record('adaptivequiz', ['id' => $coursemodule->instance])) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $adaptivequiz->name;

    if ($coursemodule->showdescription) {
        $result->content = format_module_intro('adaptivequiz', $adaptivequiz, $coursemodule->id, false);
    }

    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['completionattemptcompleted'] = $adaptivequiz->completionattemptcompleted;
    }

    return $result;
}

/**
 * Definition of user preferences used by the plugin.
 *
 * @return array[]
 */
function mod_adaptivequiz_user_preferences(): array {
    return [
        '/^mod_adaptivequiz_answers_distribution_chart_settings_(\d)+$/' => [
            'isregex' => true,
            'type' => PARAM_RAW, // JSON.
            'default' => null,
            'permissioncallback' => function($user, $preferencename) {
                global $USER;

                return $user->id == $USER->id;
            },
        ],
    ];
}
