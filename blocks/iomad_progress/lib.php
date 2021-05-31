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
 * Progress Bar block common configuration and helper functions
 *
 * Instructions for adding new modules so they can be monitored
 * ====================================================================================================================
 * Activities that can be monitored (all resources are treated together) are defined in the
 * block_iomad_progress_monitorable_modules() function.
 *
 * Modules can be added with:
 *  - defaultTime (deadline from module if applicable),
 *  - actions (array if action-query pairs) and
 *  - defaultAction (selected by default in config page and needed for backwards compatibility)
 *
 * The module name needs to be the same as the table name for module in the database.
 *
 * Queries need to produce at least one result for completeness to go green, ie there is a record
 * in the DB that indicates the user's completion.
 *
 * Queries may include the following placeholders that are substituted when the query is run. Note
 * that each placeholder can only be used once in each query.
 *  :eventid (the id of the activity in the DB table that relates to it, eg., an assignment id)
 *  :cmid (the course module id that identifies the instance of the module within the course),
 *  :userid (the current user's id) and
 *  :courseid (the current course id)
 *
 * When you add a new module, you need to add a translation for it in the lang file.
 * If you add new action names, you need to add a translation for these in the lang file.
 *
 * Note: Activity completion is automatically available when enabled (sitewide setting) and set for
 * an activity.
 *
 * Passing relies on a passing grade being set for an activity in the Gradebook.
 *
 * If you have added a new module to this array and think other's may benefit from the query you
 * have created, please share it by sending it to michaeld@moodle.com
 * ====================================================================================================================
 *
 * @package    contrib
 * @subpackage block_iomad_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Provides information about monitorable modules
 *
 * @return array
 */
function block_iomad_progress_monitorable_modules() {
    global $DB;

    return array(
        'aspirelist' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'aspirelist'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_aspirelist'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'assign' => array(
            'defaultTime' => 'duedate',
            'actions' => array(
                'submitted'    => "SELECT id
                                     FROM {assign_submission}
                                    WHERE assignment = :eventid
                                      AND userid = :userid
                                      AND status = 'submitted'",
                'marked'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assign'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'passed'       => "SELECT g.finalgrade, i.gradepass
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assign'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'passedby'     => "SELECT g.finalgrade, i.gradepass
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assign'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)
                                      AND g.finalgrade >= i.gradepass",
            ),
            'defaultAction' => 'submitted'
        ),
        'assignment' => array(
            'defaultTime' => 'timedue',
            'actions' => array(
                'submitted'    => "SELECT id
                                     FROM {assignment_submissions}
                                    WHERE assignment = :eventid
                                      AND userid = :userid
                                      AND (
                                          numfiles >= 1
                                          OR {$DB->sql_compare_text('data2')} <> ''
                                      )",
                'marked'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assignment'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'passed'       => "SELECT g.finalgrade, i.gradepass
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assignment'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'passedby'     => "SELECT g.finalgrade, i.gradepass
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'assignment'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)
                                      AND g.finalgrade >= i.gradepass",
            ),
            'defaultAction' => 'submitted'
        ),
        'bigbluebuttonbn' => array(
            'defaultTime' => 'timedue',
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'bigbluebuttonbn'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_bigbluebuttonbn'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'recordingsbn' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'recordingsbn'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_recordingsbn'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'book' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'book'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_book'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'certificate' => array(
            'actions' => array(
                'awarded'      => "SELECT id
                                     FROM {certificate_issues}
                                    WHERE certificateid = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'awarded'
        ),
        'chat' => array(
            'actions' => array(
                'posted_to'    => "SELECT id
                                     FROM {chat_messages}
                                    WHERE chatid = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'posted_to'
        ),
        'choice' => array(
            'defaultTime' => 'timeclose',
            'actions' => array(
                'answered'     => "SELECT id
                                     FROM {choice_answers}
                                    WHERE choiceid = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'answered'
        ),
        'data' => array(
            'defaultTime' => 'timeviewto',
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'data'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_data'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'feedback' => array(
            'defaultTime' => 'timeclose',
            'actions' => array(
                'responded_to' => "SELECT id
                                     FROM {feedback_completed}
                                    WHERE feedback = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'responded_to'
        ),
        'resource' => array(  // AKA file.
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'resource'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_resource'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'flashcardtrainer' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'flashcardtrainer'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_flashcardtrainer'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'folder' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'folder'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_folder'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'forum' => array(
            'defaultTime' => 'assesstimefinish',
            'actions' => array(
                'posted_to'    => "SELECT id
                                     FROM {forum_posts}
                                    WHERE userid = :userid AND discussion IN (
                                          SELECT id
                                            FROM {forum_discussions}
                                           WHERE forum = :eventid
                                    )"
            ),
            'defaultAction' => 'posted_to'
        ),
        'glossary' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'glossary'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_glossary'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'hotpot' => array(
            'defaultTime' => 'timeclose',
            'actions' => array(
                'attempted'    => "SELECT id
                                     FROM {hotpot_attempts}
                                    WHERE hotpotid = :eventid
                                      AND userid = :userid",
                'finished'     => "SELECT id
                                     FROM {hotpot_attempts}
                                    WHERE hotpotid = :eventid
                                      AND userid = :userid
                                      AND timefinish <> 0",
            ),
            'defaultAction' => 'finished'
        ),
        'hsuforum' => array(
            'defaultTime' => 'assesstimefinish',
            'actions' => array(
                'posted_to'    => "SELECT id
                                     FROM {hsuforum_posts}
                                    WHERE userid = :userid AND discussion IN (
                                          SELECT id
                                            FROM {hsuforum_discussions}
                                           WHERE forum = :eventid
                                    )"
            ),
            'defaultAction' => 'posted_to'
        ),
        'imscp' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'imscp'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_imscp'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'journal' => array(
            'actions' => array(
                'posted_to'    => "SELECT id
                                     FROM {journal_entries}
                                    WHERE journal = :eventid
                                      AND userid = :userid"
            ),
            'defaultAction' => 'posted_to'
        ),
        'lesson' => array(
            'defaultTime' => 'deadline',
            'actions' => array(
                'attempted'    => "SELECT id
                                     FROM {lesson_attempts}
                                    WHERE lessonid = :eventid
                                      AND userid = :userid
                                UNION ALL
                                   SELECT id
                                     FROM {lesson_branch}
                                    WHERE lessonid = :eventid1
                                      AND userid = :userid1",
                'graded'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'lesson'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'passed'       => "SELECT g.finalgrade, i.gradepass
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'lesson'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'passedby'     => "SELECT g.finalgrade, i.gradepass
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'lesson'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)
                                      AND g.finalgrade >= i.gradepass",
            ),
            'defaultAction' => 'attempted'
        ),
        'lti' => array(
            'actions' => array(
                'graded'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'lti'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'lti'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_lti'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'graded'
        ),
        'page' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'page'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_page'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'panopto' => array(
            'actions' => array(
               'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'panopto'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_panopto'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'questionnaire' => array(
            'defaultTime' => 'closedate',
            'actions' => array(
                'attempted'    => "SELECT id
                                     FROM {questionnaire_attempts}
                                    WHERE qid = :eventid
                                      AND userid = :userid",
                'finished'     => "SELECT id
                                     FROM {questionnaire_response}
                                    WHERE complete = 'y'
                                      AND username = :userid
                                      AND survey_id = :eventid",
            ),
            'defaultAction' => 'finished'
        ),
        'quiz' => array(
            'defaultTime' => 'timeclose',
            'actions' => array(
                'attempted'    => "SELECT id
                                     FROM {quiz_attempts}
                                    WHERE quiz = :eventid
                                      AND userid = :userid",
                'finished'     => "SELECT id
                                     FROM {quiz_attempts}
                                    WHERE quiz = :eventid
                                      AND userid = :userid
                                      AND timefinish <> 0",
                'graded'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'quiz'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'passed'       => "SELECT g.finalgrade, i.gradepass
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'quiz'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
                'passedby'     => "SELECT g.finalgrade, i.gradepass
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'quiz'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)
                                      AND g.finalgrade >= i.gradepass",
            ),
            'defaultAction' => 'finished'
        ),
        'scorm' => array(
            'actions' => array(
                'attempted'    => "SELECT id
                                     FROM {scorm_scoes_track}
                                    WHERE scormid = :eventid
                                      AND userid = :userid",
                'completed'    => "SELECT id
                                     FROM {scorm_scoes_track}
                                    WHERE scormid = :eventid
                                      AND userid = :userid
                                      AND element = 'cmi.core.lesson_status'
                                      AND {$DB->sql_compare_text('value')} = 'completed'",
                'passedscorm'  => "SELECT id
                                     FROM {scorm_scoes_track}
                                    WHERE scormid = :eventid
                                      AND userid = :userid
                                      AND element = 'cmi.core.lesson_status'
                                      AND {$DB->sql_compare_text('value')} = 'passed'"
            ),
            'defaultAction' => 'attempted'
        ),
        'turnitintool' => array(
            'defaultTime' => 'defaultdtdue',
            'actions' => array(
                'submitted'    => "SELECT id
                                     FROM {turnitintool_submissions}
                                    WHERE turnitintoolid = :eventid
                                      AND userid = :userid
                                      AND submission_score IS NOT NULL"
            ),
            'defaultAction' => 'submitted'
        ),
        'url' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'url'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_url'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'wiki' => array(
            'actions' => array(
                'viewed' => array (
                    'logstore_legacy'     => "SELECT id
                                                FROM {log}
                                               WHERE course = :courseid
                                                 AND module = 'wiki'
                                                 AND action = 'view'
                                                 AND cmid = :cmid
                                                 AND userid = :userid",
                    'sql_internal_reader' => "SELECT id
                                                FROM {log}
                                               WHERE courseid = :courseid
                                                 AND component = 'mod_wiki'
                                                 AND action = 'viewed'
                                                 AND objectid = :eventid
                                                 AND userid = :userid",
                ),
            ),
            'defaultAction' => 'viewed'
        ),
        'workshop' => array(
            'defaultTime' => 'assessmentend',
            'actions' => array(
                'submitted'    => "SELECT id
                                     FROM {workshop_submissions}
                                    WHERE workshopid = :eventid
                                      AND authorid = :userid",
                'assessed'     => "SELECT s.id
                                     FROM {workshop_assessments} a, {workshop_submissions} s
                                    WHERE s.workshopid = :eventid
                                      AND s.id = a.submissionid
                                      AND a.reviewerid = :userid
                                      AND a.grade IS NOT NULL",
                'graded'       => "SELECT g.rawgrade
                                     FROM {grade_grades} g, {grade_items} i
                                    WHERE i.itemmodule = 'workshop'
                                      AND i.iteminstance = :eventid
                                      AND i.id = g.itemid
                                      AND g.userid = :userid
                                      AND (g.finalgrade IS NOT NULL OR g.excluded <> 0)",
            ),
            'defaultAction' => 'submitted'
        ),
    );
}

/**
 * Checks if a variable has a value and returns a default value if it doesn't
 *
 * @param mixed $var The variable to check
 * @param mixed $def Default value if $var is not set
 * @return string
 */
function iomad_progress_default_value(&$var, $def = null) {
    return isset($var)?$var:$def;
}

/**
 * Filters the modules list to those installed in Moodle instance and used in current course
 *
 * @return array
 */
function block_iomad_progress_modules_in_use($course) {
    global $DB;

    $dbmanager = $DB->get_manager(); // Used to check if tables exist.
    $modules = block_iomad_progress_monitorable_modules();
    $modulesinuse = array();

    foreach ($modules as $module => $details) {
        if (
            $dbmanager->table_exists($module) &&
            $DB->record_exists($module, array('course' => $course))
        ) {
            $modulesinuse[$module] = $details;
        }
    }
    return $modulesinuse;
}

/**
 * Gets event information about modules monitored by an instance of a Progress Bar block
 *
 * @param stdClass $config  The block instance configuration values
 * @param array    $modules The modules used in the course
 * @param stdClass $course  The current course
 * @param int      $userid  The user's ID
 * @return mixed   returns array of visible events monitored,
 *                 empty array if none of the events are visible,
 *                 null if all events are configured to "no" monitoring and
 *                 0 if events are available but no config is set
 */
function block_iomad_progress_event_information($config, $modules, $course, $userid = 0) {
    global $DB, $USER;
    $events = array();
    $numevents = 0;
    $numeventsconfigured = 0;

    if ($userid === 0) {
        $userid = $USER->id;
    }

    // Get section information for the course module layout.
    $sections = block_iomad_progress_course_sections($course);

    // Check each known module (described in lib.php).
    foreach ($modules as $module => $details) {
        $fields = 'id, name';
        if (array_key_exists('defaultTime', $details)) {
            $fields .= ', '.$details['defaultTime'].' as due';
        }

        // Check if this type of module is used in the course, gather instance info.
        $records = $DB->get_records($module, array('course' => $course), '', $fields);
        foreach ($records as $record) {

            // Is the module being monitored?
            if (isset($config->{'monitor_'.$module.$record->id})) {
                $numeventsconfigured++;
            }
            if (iomad_progress_default_value($config->{'monitor_'.$module.$record->id}, 0) == 1) {
                $numevents++;
                // Check the time the module is due.
                if (
                    isset($details['defaultTime']) &&
                    $record->due != 0 &&
                    iomad_progress_default_value($config->{'locked_'.$module.$record->id}, 0)
                ) {
                    $expected = iomad_progress_default_value($record->due);
                } else {
                    $expected = $config->{'date_time_'.$module.$record->id};
                }

                // Gather together module information.
                $coursemodule = block_iomad_progress_get_coursemodule($module, $record->id, $course);
                $events[] = array(
                    'expected' => $expected,
                    'type'     => $module,
                    'id'       => $record->id,
                    'name'     => format_string($record->name),
                    'cm'       => $coursemodule,
                    'section'  => $sections[$coursemodule->section]->section,
                    'position' => array_search($coursemodule->id, $sections[$coursemodule->section]->sequence),
                );
            }
        }
    }

    if ($numeventsconfigured == 0) {
        return 0;
    }
    if ($numevents == 0) {
        return null;
    }

    // Sort by first value in each element, which is time due.
    if (isset($config->orderby) && $config->orderby == 'orderbycourse') {
        usort($events, 'block_iomad_progress_compare_events');
    } else {
        usort($events, 'block_iomad_progress_compare_times');
    }
    return $events;
}

/**
 * Used to compare two activities/resources based on order on course page
 *
 * @param array $a array of event information
 * @param array $b array of event information
 * @return <0, 0 or >0 depending on order of activities/resources on course page
 */
function block_iomad_progress_compare_events($a, $b) {
    if ($a['section'] != $b['section']) {
        return $a['section'] - $b['section'];
    } else {
        return $a['position'] - $b['position'];
    }
}

/**
 * Used to compare two activities/resources based their expected completion times
 *
 * @param array $a array of event information
 * @param array $b array of event information
 * @return <0, 0 or >0 depending on time then order of activities/resources
 */
function block_iomad_progress_compare_times($a, $b) {
    if ($a['expected'] != $b['expected']) {
        return $a['expected'] - $b['expected'];
    } else {
        return block_iomad_progress_compare_events($a, $b);
    }
}

/**
 * Checked if a user has attempted/viewed/etc. an activity/resource
 *
 * @param array    $modules  The modules used in the course
 * @param stdClass $config   The blocks configuration settings
 * @param array    $events   The possible events that can occur for modules
 * @param int      $userid   The user's id
 * @param int      $instance The instance of the block
 * @return array   an describing the user's attempts based on module+instance identifiers
 */
function block_iomad_progress_attempts($modules, $config, $events, $userid, $course) {
    global $DB;
    $attempts = array();
    $modernlogging = false;
    $cachingused = false;

    // Get readers for 2.7 onwards.
    if (function_exists('get_log_manager')) {
        $modernlogging = true;
        $logmanager = get_log_manager();
        $readers = $logmanager->get_readers();
        $numreaders = count($readers);
    }

    // Get cache store if caching is working 2.4 onwards.
    if (class_exists('cache')) {
        $cachingused = true;
        $cachedlogs = cache::make('block_iomad_progress', 'cachedlogs');
        $cachedlogviews = $cachedlogs->get($userid);
        if (empty($cachedlogviews)) {
            $cachedlogviews = array();
        }
        $cachedlogsupdated = false;
    }

    foreach ($events as $event) {
        $module = $modules[$event['type']];
        $uniqueid = $event['type'].$event['id'];
        $parameters = array('courseid' => $course, 'courseid1' => $course,
                            'userid' => $userid, 'userid1' => $userid,
                            'eventid' => $event['id'], 'eventid1' => $event['id'],
                            'cmid' => $event['cm']->id, 'cmid1' => $event['cm']->id,
                      );

        // Check for passing grades as unattempted, passed or failed.
        if (isset($config->{'action_'.$uniqueid}) && $config->{'action_'.$uniqueid} == 'passed') {
            $query = $module['actions'][$config->{'action_'.$uniqueid}];
            $graderesult = $DB->get_record_sql($query, $parameters);
            if ($graderesult === false || $graderesult->finalgrade === null) {
                $attempts[$uniqueid] = false;
            } else {
                $attempts[$uniqueid] = $graderesult->finalgrade >= $graderesult->gradepass ? true : 'failed';
            }
        }

        // Checked view actions in the log table/store/cache.
        else if (isset($config->{'action_'.$uniqueid}) && $config->{'action_'.$uniqueid} == 'viewed') {
            $attempts[$uniqueid] = false;

            // Check if the value is cached.
            if ($cachingused && array_key_exists($uniqueid, $cachedlogviews) && $cachedlogviews[$uniqueid]) {
                $attempts[$uniqueid] = true;
            }

            // Check in the logs.
            else {
                if ($modernlogging) {
                    foreach ($readers as $logstore => $reader) {
                        if (
                            $reader instanceof \core\log\sql_internal_table_reader ||
                            $reader instanceof \core\log\sql_internal_reader
                        ) {
                            $logtable = '{'.$reader->get_internal_log_table_name().'}';
                            $query = preg_replace('/\{log\}/', $logtable, $module['actions']['viewed']['sql_internal_reader']);
                        }
                        else if ($reader instanceof logstore_legacy\log\store) {
                            $query = $module['actions']['viewed']['logstore_legacy'];
                        }
                        else {
                            // No logs available.
                            continue;
                        }
                        $attempts[$uniqueid] = $DB->record_exists_sql($query, $parameters) ? true : false;
                        if ($attempts[$uniqueid]) {
                            $cachedlogviews[$uniqueid] = true;
                            $cachedlogsupdated = true;
                            break;
                        }
                    }
                } else {
                    $query = $module['actions']['viewed']['logstore_legacy'];
                    $attempts[$uniqueid] = $DB->record_exists_sql($query, $parameters) ? true : false;
                    if ($cachingused && $attempts[$uniqueid]) {
                        $cachedlogviews[$uniqueid] = true;
                        $cachedlogsupdated = true;
                    }
                }
            }
        } else {

            // If activity completion is used, check completions table.
            if (isset($config->{'action_'.$uniqueid}) && $config->{'action_'.$uniqueid} == 'activity_completion') {
                $query = 'SELECT id
                            FROM {course_modules_completion}
                           WHERE userid = :userid
                             AND coursemoduleid = :cmid
                             AND completionstate >= 1';
            }

            // Determine the set action and develop a query.
            else {
                $action = isset($config->{'action_'.$uniqueid})?
                          $config->{'action_'.$uniqueid}:
                          $module['defaultAction'];
                $query = $module['actions'][$action];
            }

             // Check if the user has attempted the module.
            $attempts[$uniqueid] = $DB->record_exists_sql($query, $parameters) ? true : false;
        }
    }

    // Update log cache if new values were added.
    if ($cachingused && $cachedlogsupdated) {
        $cachedlogs->set($userid, $cachedlogviews);
    }

    return $attempts;
}

/**
 * Draws a iomad_progress bar
 *
 * @param array    $modules  The modules used in the course
 * @param stdClass $config   The blocks configuration settings
 * @param array    $events   The possible events that can occur for modules
 * @param int      $userid   The user's id
 * @param int      instance  The block instance (in case more than one is being displayed)
 * @param array    $attempts The user's attempts on course activities
 * @param bool     $simple   Controls whether instructions are shown below a iomad_progress bar
 * @return string  Progress Bar HTML content
 */
function block_iomad_progress_bar($modules, $config, $events, $userid, $instance, $attempts, $course, $simple = false) {
    global $OUTPUT, $CFG;
    $now = time();
    $numevents = count($events);
    $dateformat = get_string('strftimerecentfull', 'langconfig');
    $tableoptions = array('class' => 'iomad_progressBarProgressTable',
                          'cellpadding' => '0',
                          'cellspacing' => '0');

    // Get colours and use defaults if they are not set in global settings.
    $colournames = array(
        'attempted_colour' => 'attempted_colour',
        'notattempted_colour' => 'notAttempted_colour',
        'futurenotattempted_colour' => 'futureNotAttempted_colour'
    );
    $colours = array();
    foreach ($colournames as $name => $stringkey) {
        if (get_config('block_iomad_progress', $name)) {
            $colours[$name] = get_config('block_iomad_progress', $name);
        }
        else {
            $colours[$name] = get_string('block_iomad_progress', $stringkey);
        }
    }

    // Place now arrow.
    if ((!isset($config->orderby) || $config->orderby == 'orderbytime') && $config->displayNow == 1 && !$simple) {

        $content = HTML_WRITER::start_tag('table', $tableoptions);

        // Find where to put now arrow.
        $nowpos = 0;
        while ($nowpos < $numevents && $now > $events[$nowpos]['expected']) {
            $nowpos++;
        }
        $content .= HTML_WRITER::start_tag('tr');
        $nowstring = get_string('now_indicator', 'block_iomad_progress');
        if ($nowpos < $numevents / 2) {
            for ($i = 0; $i < $nowpos; $i++) {
                $content .= HTML_WRITER::tag('td', '&nbsp;', array('class' => 'iomad_progressBarHeader'));
            }
            $celloptions = array('colspan' => $numevents - $nowpos,
                                 'class' => 'iomad_progressBarHeader',
                                 'style' => 'text-align:left;');
            $content .= HTML_WRITER::start_tag('td', $celloptions);
            $content .= $OUTPUT->pix_icon('left', $nowstring, 'block_iomad_progress');
            $content .= $nowstring;
            $content .= HTML_WRITER::end_tag('td');
        } else {
            $celloptions = array('colspan' => $nowpos,
                                 'class' => 'iomad_progressBarHeader',
                                 'style' => 'text-align:right;');
            $content .= HTML_WRITER::start_tag('td', $celloptions);
            $content .= $nowstring;
            $content .= $OUTPUT->pix_icon('right', $nowstring, 'block_iomad_progress');
            $content .= HTML_WRITER::end_tag('td');
            for ($i = $nowpos; $i < $numevents; $i++) {
                $content .= HTML_WRITER::tag('td', '&nbsp;', array('class' => 'iomad_progressBarHeader'));
            }
        }
        $content .= HTML_WRITER::end_tag('tr');
    }
    else {
        $tableoptions['class'] = 'iomad_progressBarProgressTable noNow';
        $content = HTML_WRITER::start_tag('table', $tableoptions);
    }

    // Start iomad_progress bar.
    $width = 100 / $numevents;
    $content .= HTML_WRITER::start_tag('tr');
    $counter = 1;
    foreach ($events as $event) {
        $attempted = $attempts[$event['type'].$event['id']];
        $action = isset($config->{'action_'.$event['type'].$event['id']})?
                  $config->{'action_'.$event['type'].$event['id']}:
                  $modules[$event['type']]['defaultAction'];

        // A cell in the iomad_progress bar.
        $celloptions = array(
            'class' => 'iomad_progressBarCell',
            'id' => '',
            'width' => $width.'%',
            'onmouseover' => 'M.block_iomad_progress.showInfo('.$instance.','.$userid.','.$event['cm']->id.');',
             'style' => 'background-color:');
        if ($attempted === true) {
            $celloptions['style'] .= $colours['attempted_colour'].';';
            $cellcontent = $OUTPUT->pix_icon(
                               isset($config->iomad_progressBarIcons) && $config->iomad_progressBarIcons == 1 ?
                               'tick' : 'blank', '', 'block_iomad_progress');
        }
        else if (((!isset($config->orderby) || $config->orderby == 'orderbytime') && $event['expected'] < $now) ||
                 ($attempted === 'failed')) {
            $celloptions['style'] .= $colours['notattempted_colour'].';';
            $cellcontent = $OUTPUT->pix_icon(
                               isset($config->iomad_progressBarIcons) && $config->iomad_progressBarIcons == 1 ?
                               'cross':'blank', '', 'block_iomad_progress');
        }
        else {
            $celloptions['style'] .= $colours['futurenotattempted_colour'].';';
            $cellcontent = $OUTPUT->pix_icon('blank', '', 'block_iomad_progress');
        }
        if (!empty($event['cm']->available)) {
            $celloptions['onclick'] = 'document.location=\''.
                $CFG->wwwroot.'/mod/'.$event['type'].'/view.php?id='.$event['cm']->id.'\';';
        }
        if ($counter == 1) {
            $celloptions['id'] .= 'first';
        }
        if ($counter == $numevents) {
            $celloptions['id'] .= 'last';
        }
        $counter++;
        $content .= HTML_WRITER::tag('td', $cellcontent, $celloptions);
    }
    $content .= HTML_WRITER::end_tag('tr');
    $content .= HTML_WRITER::end_tag('table');

    // Add the info box below the table.
    $divoptions = array('class' => 'iomad_progressEventInfo',
                        'id' => 'iomad_progressBarInfo'.$instance.'-'.$userid.'-info');
    $content .= HTML_WRITER::start_tag('div', $divoptions);
    if (!$simple) {
        if (isset($config->showpercentage) && $config->showpercentage == 1) {
            $iomad_progress = block_iomad_progress_percentage($events, $attempts);
            $content .= get_string('iomad_progress', 'block_iomad_progress').': ';
            $content .= $iomad_progress.'%'.HTML_WRITER::empty_tag('br');
        }
        $content .= get_string('mouse_over_prompt', 'block_iomad_progress');
    }
    $content .= HTML_WRITER::end_tag('div');

    // Add hidden divs for activity information.
    $displaydate = (!isset($config->orderby) || $config->orderby == 'orderbytime') &&
                   (!isset($config->displayNow) || $config->displayNow == 1);
    foreach ($events as $event) {
        $attempted = $attempts[$event['type'].$event['id']];
        $action = isset($config->{'action_'.$event['type'].$event['id']})?
                  $config->{'action_'.$event['type'].$event['id']}:
                  $modules[$event['type']]['defaultAction'];
        $divoptions = array('class' => 'iomad_progressEventInfo',
                            'id' => 'iomad_progressBarInfo'.$instance.'-'.$userid.'-'.$event['cm']->id,
                            'style' => 'display: none;');
        $content .= HTML_WRITER::start_tag('div', $divoptions);
        $link = '/mod/'.$event['type'].'/view.php?id='.$event['cm']->id;
        $text = $OUTPUT->pix_icon('icon', '', $event['type'], array('class' => 'moduleIcon')).s($event['name']);
        if (!empty($event['cm']->available)) {
            $content .= $OUTPUT->action_link($link, $text);
        } else {
            $content .= $text;
        }
        $content .= HTML_WRITER::empty_tag('br');
        $content .= get_string($action, 'block_iomad_progress').'&nbsp;';
        $icon = ($attempted && $attempted !== 'failed' ? 'tick' : 'cross');
        $content .= $OUTPUT->pix_icon($icon, '', 'block_iomad_progress');
        $content .= HTML_WRITER::empty_tag('br');
        if ($displaydate) {
            $content .= HTML_WRITER::start_tag('div', array('class' => 'expectedBy'));
            $content .= get_string('time_expected', 'block_iomad_progress').': ';
            $content .= userdate($event['expected'], $dateformat, $CFG->timezone);
            $content .= HTML_WRITER::end_tag('div');
        }
        $content .= HTML_WRITER::end_tag('div');
    }

    return $content;
}

/**
 * Calculates an overall percentage of iomad_progress
 *
 * @param array $events   The possible events that can occur for modules
 * @param array $attempts The user's attempts on course activities
 * @return int  Progress value as a percentage
 */
function block_iomad_progress_percentage($events, $attempts) {
    $attemptcount = 0;

    foreach ($events as $event) {
        if ($attempts[$event['type'].$event['id']] == 1) {
            $attemptcount++;
        }
    }

    $iomad_progressvalue = $attemptcount == 0 ? 0 : $attemptcount / count($events);

    return (int)round($iomad_progressvalue * 100);
}

/**
 * Gathers the course section and activity/resource information for ordering
 *
 * @return array section information
 */
function block_iomad_progress_course_sections($course) {
    global $DB;

    $sections = $DB->get_records('course_sections', array('course' => $course), 'section', 'id,section,name,sequence');
    foreach ($sections as $key => $section) {
        if ($section->sequence != '') {
            $sections[$key]->sequence = explode(',', $section->sequence);
        }
        else {
            $sections[$key]->sequence = null;
        }
    }

    return $sections;
}

/**
 * Filters events that a user cannot see due to grouping constraints
 *
 * @param array  $events The possible events that can occur for modules
 * @param array  $userid The user's id
 * @param string $coursecontext the context value of the course
 * @param string $course the course for filtering visibility
 * @return array The array with restricted events removed
 */
function block_iomad_progress_filter_visibility($events, $userid, $coursecontext, $course = 0) {
    global $CFG, $USER;
    $filteredevents = array();

    // Check if the events are empty or none are selected.
    if ($events === 0) {
        return 0;
    }
    if ($events === null) {
        return null;
    }

    // Keep only events that are visible.
    foreach ($events as $key => $event) {

        // Determine the correct user info to check.
        if ($userid == $USER->id) {
            $coursemodule = $event['cm'];
        }
        else {
            $coursemodule = block_iomad_progress_get_coursemodule($event['type'], $event['id'], $course->id, $userid);
        }

        // Check visibility in course.
        if (!$coursemodule->visible && !has_capability('moodle/course:viewhiddenactivities', $coursecontext, $userid)) {
            continue;
        }

        // Check availability, allowing for visible, but not accessible items.
        if (!empty($CFG->enableavailability)) {
            if (
                isset($coursemodule->available) && !$coursemodule->available && empty($coursemodule->availableinfo) &&
                !has_capability('moodle/course:viewhiddenactivities', $coursecontext, $userid)
            ) {
                continue;
            }
        }
        // Check visibility by grouping constraints (includes capability check).
        if (!empty($CFG->enablegroupmembersonly)) {
            if (isset($coursemodule->uservisible)) {
                if ($coursemodule->uservisible != 1 && empty($coursemodule->availableinfo)) {
                    continue;
                }
            }
            else if (!groups_course_module_visible($coursemodule, $userid)) {
                continue;
            }
        }

        // Save the visible event.
        $filteredevents[] = $event;
    }
    return $filteredevents;
}

/**
 * Checks whether the current page is the My home page.
 *
 * @return bool True when on the My home page.
 */
function block_iomad_progress_on_my_page() {
    global $SCRIPT;

    return $SCRIPT === '/my/index.php';
}

/**
 * Gets the course context, allowing for old and new Moodle instances.
 *
 * @param int $courseid The course ID
 * @return stdClass The context object
 */
function block_iomad_progress_get_course_context($courseid) {
    if (class_exists('context_course')) {
        return context_course::instance($courseid);
    } else {
        return get_context_instance(CONTEXT_COURSE, $courseid);
    }
}

/**
 * Gets the block context, allowing for old and new Moodle instances.
 *
 * @param int $block The block ID
 * @return stdClass The context object
 */
function block_iomad_progress_get_block_context($blockid) {
    if (class_exists('context_block')) {
        return context_block::instance($blockid);
    } else {
        return get_context_instance(CONTEXT_BLOCK, $blockid);
    }
}

/**
 * Gets the course module in a backwards compatible way.
 *
 * @param int $module   the type of module (eg, assign, quiz...)
 * @param int $recordid the instance ID (from its table)
 * @param int $courseid the course ID
 * @return stdClass The course module object
 */
function block_iomad_progress_get_coursemodule($module, $recordid, $courseid, $userid = 0) {
    global $CFG;

    if ($CFG->version >= 2012120300) {
        return get_fast_modinfo($courseid, $userid)->instances[$module][$recordid];
    }
    else {
        return get_coursemodule_from_instance($module, $recordid, $courseid);
    }
}
