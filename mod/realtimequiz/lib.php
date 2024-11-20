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
 * Library of functions and constants for module realtimequiz
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_realtimequiz
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $realtimequiz An object from the form in mod.html
 * @return int The id of the newly inserted realtimequiz record
 **/
function realtimequiz_add_instance($realtimequiz) {
    global $DB;

    $realtimequiz->timemodified = time();
    $realtimequiz->timecreated = time();

    $realtimequiz->status = 0;
    $realtimequiz->currentquestion = 0;
    $realtimequiz->nextendtime = 0;
    $realtimequiz->currentsessionid = 0;
    $realtimequiz->classresult = 0;
    $realtimequiz->questionresult = 0;

    return $DB->insert_record('realtimequiz', $realtimequiz);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 *
 * @param object $realtimequiz An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function realtimequiz_update_instance($realtimequiz) {
    global $DB;

    $realtimequiz->timemodified = time();
    $realtimequiz->id = $realtimequiz->instance;

    $realtimequiz->status = 0;
    $realtimequiz->currentquestion = 0;
    $realtimequiz->nextendtime = 0;
    $realtimequiz->currentsessionid = 0;
    $realtimequiz->classresult = 0;
    $realtimequiz->questionresult = 0;

    return $DB->update_record('realtimequiz', $realtimequiz);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function realtimequiz_delete_instance($id) {
    global $DB;

    if (!$realtimequiz = $DB->get_record('realtimequiz', ['id' => $id])) {
        return false;
    }

    $result = true;

    $questions = $DB->get_records('realtimequiz_question', ['quizid' => $id]);
    foreach ($questions as $question) { // Get each question.
        $answers = $DB->get_records('realtimequiz_answer', ['questionid' => $question->id]);
        foreach ($answers as $answer) { // Get each answer for that question
            // Delete each submission for that answer.
            $DB->delete_records('realtimequiz_submitted', ['answerid' => $answer->id]);
        }
        $DB->delete_records('realtimequiz_answer', ['questionid' => $question->id]); // Delete each answer.
    }
    $DB->delete_records('realtimequiz_question', ['quizid' => $id]); // Delete each question.
    $DB->delete_records('realtimequiz_session', ['quizid' => $id]); // Delete each session.
    $DB->delete_records('realtimequiz', ['id' => $realtimequiz->id]);

    return $result;
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
 * @param object $realtimequiz
 * @return null
 */
function realtimequiz_user_outline($course, $user, $mod, $realtimequiz) {
    return null;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $realtimequiz
 * @return boolean
 */
function realtimequiz_user_complete($course, $user, $mod, $realtimequiz) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in realtimequiz activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @param object $course
 * @param bool $isteacher
 * @param int $timestart
 * @return boolean
 */
function realtimequiz_print_recent_activity($course, $isteacher, $timestart) {
    return false;  // True if anything was printed, otherwise false.
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 **/
function realtimequiz_cron() {
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
 * @param int $realtimequizid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function realtimequiz_grades($realtimequizid) {
    return null;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of realtimequiz. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $realtimequizid ID of an instance of this module
 * @return mixed boolean/array of students
 **/
function realtimequiz_get_participants($realtimequizid) {
    return false;
}

/**
 * This function returns if a scale is being used by one realtimequiz
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $realtimequizid ID of an instance of this module
 * @param int $scaleid
 * @return mixed
 **/
function realtimequiz_scale_used($realtimequizid, $scaleid) {
    $return = false;

    return $return;
}

/**
 * Is the given scale used?
 * @param int $scaleid
 * @return false
 */
function realtimequiz_scale_used_anywhere($scaleid) {
    return false;
}

// Any other realtimequiz functions go here.  Each of them must have a name that
// starts with realtimequiz_ .

/**
 * Output the tabs.
 * @param string $currenttab
 * @param int $cmid
 * @param context $context
 * @throws coding_exception
 * @throws moodle_exception
 */
function realtimequiz_view_tabs($currenttab, $cmid, $context) {
    $tabs = [];
    $row = [];
    $inactive = [];
    $activated = [];

    if (has_capability('mod/realtimequiz:attempt', $context)) {
        $row[] = new tabobject('view', new moodle_url('/mod/realtimequiz/view.php', ['id' => $cmid]),
                               get_string('view', 'realtimequiz'));
    }
    if (has_capability('mod/realtimequiz:editquestions', $context)) {
        $row[] = new tabobject('edit', new moodle_url('/mod/realtimequiz/edit.php', ['id' => $cmid]),
                               get_string('edit', 'realtimequiz'));
    }
    if (has_capability('mod/realtimequiz:seeresponses', $context)) {
        $row[] = new tabobject('responses', new moodle_url('/mod/realtimequiz/responses.php', ['id' => $cmid]),
                               get_string('responses', 'realtimequiz'));
    }

    if ($currenttab == 'view' && count($row) == 1) {
        // No tabs for students.
        echo '<br />';
    } else {
        $tabs[] = $row;
    }

    if ($currenttab == 'responses') {
        $activated[] = 'responses';
    }

    if ($currenttab == 'edit') {
        $activated[] = 'edit';
    }

    if ($currenttab == 'view') {
        $activated[] = 'view';
    }

    print_tabs($tabs, $currenttab, $inactive, $activated);
}

/**
 * Serve the requested file
 * @param object $course
 * @param object $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return false
 * @throws coding_exception
 * @throws dml_exception
 */
function realtimequiz_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    if ($filearea != 'question') {
        return false;
    }

    require_course_login($course, true, $cm);

    $questionid = (int)array_shift($args);

    if (!$quiz = $DB->get_record('realtimequiz', ['id' => $cm->instance])) {
        return false;
    }

    if (!$question = $DB->get_record('realtimequiz_question', ['id' => $questionid, 'quizid' => $cm->instance])) {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_realtimequiz/$filearea/$questionid/$relativepath";
    if (!($file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
        return false;
    }

    // Finally send the file.
    send_stored_file($file);
    return false;
}

/**
 * Features supported by this plugin
 * @param string $feature
 * @return bool|null
 */
function realtimequiz_supports($feature) {

    if (!defined('FEATURE_PLAGIARISM')) {
        define('FEATURE_PLAGIARISM', 'plagiarism');
    }

    switch ($feature) {
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_RATE:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_PLAGIARISM:
            return false;

        default:
            return null;
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $checklistnode The node to add module settings to
 */
function realtimequiz_extend_settings_navigation(settings_navigation $settings, navigation_node $checklistnode) {
    global $CFG;
    if ($CFG->branch < 400) {
        return;
    }
    $cm = $settings->get_page()->cm;
    if (has_capability('mod/realtimequiz:editquestions', $cm->context)) {
        $checklistnode->add(get_string('edit', 'mod_realtimequiz'),
                            new moodle_url('/mod/realtimequiz/edit.php', ['id' => $cm->id]),
                            navigation_node::TYPE_SETTING, null, 'edit');
    }
    if (has_capability('mod/realtimequiz:seeresponses', $cm->context)) {
        $checklistnode->add(get_string('responses', 'mod_realtimequiz'),
                            new moodle_url('/mod/realtimequiz/responses.php', ['id' => $cm->id]),
                            navigation_node::TYPE_SETTING, null, 'responses');
    }
}
