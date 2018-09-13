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

// Library of functions and constants for module questionnaire.

/**
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('QUESTIONNAIRE_RESETFORM_RESET', 'questionnaire_reset_data_');
define('QUESTIONNAIRE_RESETFORM_DROP', 'questionnaire_drop_questionnaire_');

function questionnaire_supports($feature) {
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * @return array all other caps used in module
 */
function questionnaire_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

function questionnaire_add_instance($questionnaire) {
    // Given an object containing all the necessary data,
    // (defined by the form in mod.html) this function
    // will create a new instance and return the id number
    // of the new instance.
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');
    require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

    // Check the realm and set it to the survey if it's set.

    if (empty($questionnaire->sid)) {
        // Create a new survey.
        $course = get_course($questionnaire->course);
        $cm = new stdClass();
        $qobject = new questionnaire(0, $questionnaire, $course, $cm);

        if ($questionnaire->create == 'new-0') {
            $sdata = new stdClass();
            $sdata->name = $questionnaire->name;
            $sdata->realm = 'private';
            $sdata->title = $questionnaire->name;
            $sdata->subtitle = '';
            $sdata->info = '';
            $sdata->theme = ''; // Theme is deprecated.
            $sdata->thanks_page = '';
            $sdata->thank_head = '';
            $sdata->thank_body = '';
            $sdata->email = '';
            $sdata->feedbacknotes = '';
            $sdata->courseid = $course->id;
            if (!($sid = $qobject->survey_update($sdata))) {
                print_error('couldnotcreatenewsurvey', 'questionnaire');
            }
        } else {
            $copyid = explode('-', $questionnaire->create);
            $copyrealm = $copyid[0];
            $copyid = $copyid[1];
            if (empty($qobject->survey)) {
                $qobject->add_survey($copyid);
                $qobject->add_questions($copyid);
            }
            // New questionnaires created as "use public" should not create a new survey instance.
            if ($copyrealm == 'public') {
                $sid = $copyid;
            } else {
                $sid = $qobject->sid = $qobject->survey_copy($course->id);
                // All new questionnaires should be created as "private".
                // Even if they are *copies* of public or template questionnaires.
                $DB->set_field('questionnaire_survey', 'realm', 'private', array('id' => $sid));
            }
            // If the survey has dependency data, need to set the questionnaire to allow dependencies.
            if ($DB->count_records('questionnaire_dependency', ['surveyid' => $sid]) > 0) {
                $questionnaire->navigate = 1;
            }
        }
        $questionnaire->sid = $sid;
    }

    $questionnaire->timemodified = time();

    // May have to add extra stuff in here.
    if (empty($questionnaire->useopendate)) {
        $questionnaire->opendate = 0;
    }
    if (empty($questionnaire->useclosedate)) {
        $questionnaire->closedate = 0;
    }

    if ($questionnaire->resume == '1') {
        $questionnaire->resume = 1;
    } else {
        $questionnaire->resume = 0;
    }

    if (!$questionnaire->id = $DB->insert_record("questionnaire", $questionnaire)) {
        return false;
    }

    questionnaire_set_events($questionnaire);

    $completiontimeexpected = !empty($questionnaire->completionexpected) ? $questionnaire->completionexpected : null;
    \core_completion\api::update_completion_date_event($questionnaire->coursemodule, 'questionnaire', $questionnaire->id, $completiontimeexpected);

    return $questionnaire->id;
}

// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will update an existing instance with new data.
function questionnaire_update_instance($questionnaire) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

    // Check the realm and set it to the survey if its set.
    if (!empty($questionnaire->sid) && !empty($questionnaire->realm)) {
        $DB->set_field('questionnaire_survey', 'realm', $questionnaire->realm, array('id' => $questionnaire->sid));
    }

    $questionnaire->timemodified = time();
    $questionnaire->id = $questionnaire->instance;

    // May have to add extra stuff in here.
    if (empty($questionnaire->useopendate)) {
        $questionnaire->opendate = 0;
    }
    if (empty($questionnaire->useclosedate)) {
        $questionnaire->closedate = 0;
    }

    if ($questionnaire->resume == '1') {
        $questionnaire->resume = 1;
    } else {
        $questionnaire->resume = 0;
    }

    // Get existing grade item.
    questionnaire_grade_item_update($questionnaire);

    questionnaire_set_events($questionnaire);

    $completiontimeexpected = !empty($questionnaire->completionexpected) ? $questionnaire->completionexpected : null;
    \core_completion\api::update_completion_date_event($questionnaire->coursemodule, 'questionnaire', $questionnaire->id, $completiontimeexpected);

    return $DB->update_record("questionnaire", $questionnaire);
}

// Given an ID of an instance of this module,
// this function will permanently delete the instance
// and any data that depends on it.
function questionnaire_delete_instance($id) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

    if (! $questionnaire = $DB->get_record('questionnaire', array('id' => $id))) {
        return false;
    }

    $result = true;

    if ($events = $DB->get_records('event', array("modulename" => 'questionnaire', "instance" => $questionnaire->id))) {
        foreach ($events as $event) {
            $event = calendar_event::load($event);
            $event->delete();
        }
    }

    if (! $DB->delete_records('questionnaire', array('id' => $questionnaire->id))) {
        $result = false;
    }

    if ($survey = $DB->get_record('questionnaire_survey', array('id' => $questionnaire->sid))) {
        // If this survey is owned by this course, delete all of the survey records and responses.
        if ($survey->courseid == $questionnaire->course) {
            $result = $result && questionnaire_delete_survey($questionnaire->sid, $questionnaire->id);
        }
    }

    return $result;
}

// Return a small object with summary information about what a
// user has done with a given particular instance of this module
// Used for user activity reports.
// $return->time = the time they did it
// $return->info = a short text description.
/**
 * $course and $mod are unused, but API requires them. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_user_outline($course, $user, $mod, $questionnaire) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/questionnaire/locallib.php');

    $result = new stdClass();
    if ($responses = questionnaire_get_user_responses($questionnaire->sid, $user->id, true)) {
        $n = count($responses);
        if ($n == 1) {
            $result->info = $n.' '.get_string("response", "questionnaire");
        } else {
            $result->info = $n.' '.get_string("responses", "questionnaire");
        }
        $lastresponse = array_pop($responses);
        $result->time = $lastresponse->submitted;
    } else {
        $result->info = get_string("noresponses", "questionnaire");
    }
    return $result;
}

// Print a detailed representation of what a  user has done with
// a given particular instance of this module, for user activity reports.
/**
 * $course and $mod are unused, but API requires them. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_user_complete($course, $user, $mod, $questionnaire) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/questionnaire/locallib.php');

    if ($responses = questionnaire_get_user_responses($questionnaire->sid, $user->id, false)) {
        foreach ($responses as $response) {
            if ($response->complete == 'y') {
                echo get_string('submitted', 'questionnaire').' '.userdate($response->submitted).'<br />';
            } else {
                echo get_string('attemptstillinprogress', 'questionnaire').' '.userdate($response->submitted).'<br />';
            }
        }
    } else {
        print_string('noresponses', 'questionnaire');
    }

    return true;
}

// Given a course and a time, this module should find recent activity
// that has occurred in questionnaire activities and print it out.
// Return true if there was output, or false is there was none.
/**
 * $course, $isteacher and $timestart are unused, but API requires them. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_print_recent_activity($course, $isteacher, $timestart) {
    return false;  // True if anything was printed, otherwise false.
}

// Must return an array of grades for a given instance of this module,
// indexed by user.  It also returns a maximum allowed grade.
/**
 * $questionnaireid is unused, but API requires it. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_grades($questionnaireid) {
    return null;
}

/**
 * Return grade for given user or all users.
 *
 * @param int $questionnaireid id of assignment
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function questionnaire_get_user_grades($questionnaire, $userid=0) {
    global $DB;
    $params = array();
    $usersql = '';
    if (!empty($userid)) {
        $usersql = "AND u.id = ?";
        $params[] = $userid;
    }

    $sql = "SELECT a.id, u.id AS userid, r.grade AS rawgrade, r.submitted AS dategraded, r.submitted AS datesubmitted
            FROM {user} u, {questionnaire_attempts} a, {questionnaire_response} r
            WHERE u.id = a.userid AND a.qid = $questionnaire->id AND r.id = a.rid $usersql";
    return $DB->get_records_sql($sql, $params);
}

/**
 * Update grades by firing grade_updated event
 *
 * @param object $assignment null means all assignments
 * @param int $userid specific user only, 0 mean all
 *
 * $nullifnone is unused, but API requires it. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_update_grades($questionnaire=null, $userid=0, $nullifnone=true) {
    global $CFG, $DB;

    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir.'/gradelib.php');
    }

    if ($questionnaire != null) {
        if ($graderecs = questionnaire_get_user_grades($questionnaire, $userid)) {
            $grades = array();
            foreach ($graderecs as $v) {
                if (!isset($grades[$v->userid])) {
                    $grades[$v->userid] = new stdClass();
                    if ($v->rawgrade == -1) {
                        $grades[$v->userid]->rawgrade = null;
                    } else {
                        $grades[$v->userid]->rawgrade = $v->rawgrade;
                    }
                    $grades[$v->userid]->userid = $v->userid;
                } else if (isset($grades[$v->userid]) && ($v->rawgrade > $grades[$v->userid]->rawgrade)) {
                    $grades[$v->userid]->rawgrade = $v->rawgrade;
                }
            }
            questionnaire_grade_item_update($questionnaire, $grades);
        } else {
            questionnaire_grade_item_update($questionnaire);
        }

    } else {
        $sql = "SELECT q.*, cm.idnumber as cmidnumber, q.course as courseid
                  FROM {questionnaire} q, {course_modules} cm, {modules} m
                 WHERE m.name='questionnaire' AND m.id=cm.module AND cm.instance=q.id";
        if ($rs = $DB->get_recordset_sql($sql)) {
            foreach ($rs as $questionnaire) {
                if ($questionnaire->grade != 0) {
                    questionnaire_update_grades($questionnaire);
                } else {
                    questionnaire_grade_item_update($questionnaire);
                }
            }
            $rs->close();
        }
    }
}

/**
 * Create grade item for given questionnaire
 *
 * @param object $questionnaire object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function questionnaire_grade_item_update($questionnaire, $grades = null) {
    global $CFG;
    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir.'/gradelib.php');
    }

    if (!isset($questionnaire->courseid)) {
        $questionnaire->courseid = $questionnaire->course;
    }

    if ($questionnaire->cmidnumber != '') {
        $params = array('itemname' => $questionnaire->name, 'idnumber' => $questionnaire->cmidnumber);
    } else {
        $params = array('itemname' => $questionnaire->name);
    }

    if ($questionnaire->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $questionnaire->grade;
        $params['grademin']  = 0;

    } else if ($questionnaire->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$questionnaire->grade;

    } else if ($questionnaire->grade == 0) { // No Grade..be sure to delete the grade item if it exists.
        $grades = null;
        $params = array('deleted' => 1);

    } else {
        $params = null; // Allow text comments only.
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/questionnaire', $questionnaire->courseid, 'mod', 'questionnaire',
                    $questionnaire->id, 0, $grades, $params);
}

/**
 * This function returns if a scale is being used by one questionnaire
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 * @param $questionnaireid int
 * @param $scaleid int
 * @return boolean True if the scale is used by any questionnaire
 *
 * Function parameters are unused, but API requires them. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_scale_used ($questionnaireid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of questionnaire
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any questionnaire
 *
 * Function parameters are unused, but API requires them. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Serves the questionnaire attachments. Implements needed access control ;-)
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 *
 * $forcedownload is unused, but API requires it. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    $fileareas = ['intro', 'info', 'thankbody', 'question', 'feedbacknotes', 'sectionheading', 'feedback'];
    if (!in_array($filearea, $fileareas)) {
        return false;
    }

    $componentid = (int)array_shift($args);

    if ($filearea == 'question') {
        if (!$DB->record_exists('questionnaire_question', ['id' => $componentid])) {
            return false;
        }
    } else if ($filearea == 'sectionheading') {
        if (!$DB->record_exists('questionnaire_fb_sections', ['id' => $componentid])) {
            return false;
        }
    } else if ($filearea == 'feedback') {
        if (!$DB->record_exists('questionnaire_feedback', ['id' => $componentid])) {
            return false;
        }
    } else {
        if (!$DB->record_exists('questionnaire_survey', ['id' => $componentid])) {
            return false;
        }
    }

    if (!$DB->record_exists('questionnaire', ['id' => $cm->instance])) {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_questionnaire/$filearea/$componentid/$relativepath";
    if (!($file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
        return false;
    }

    // Finally send the file.
    send_stored_file($file, 0, 0, true); // Download MUST be forced - security!
}
/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $questionnairenode The node to add module settings to
 *
 * $settings is unused, but API requires it. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_extend_settings_navigation(settings_navigation $settings,
        navigation_node $questionnairenode) {

    global $PAGE, $DB, $USER, $CFG;
    $individualresponse = optional_param('individualresponse', false, PARAM_INT);
    $rid = optional_param('rid', false, PARAM_INT); // Response id.
    $currentgroupid = optional_param('group', 0, PARAM_INT); // Group id.

    require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

    $context = $PAGE->cm->context;
    $cmid = $PAGE->cm->id;
    $cm = $PAGE->cm;
    $course = $PAGE->course;

    if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $cm->instance))) {
        print_error('invalidcoursemodule');
    }

    $courseid = $course->id;
    $questionnaire = new questionnaire(0, $questionnaire, $course, $cm);

    if ($owner = $DB->get_field('questionnaire_survey', 'courseid', ['id' => $questionnaire->sid])) {
        $owner = (trim($owner) == trim($courseid));
    } else {
        $owner = true;
    }

    // On view page, currentgroupid is not yet sent as an optional_param, so get it.
    $groupmode = groups_get_activity_groupmode($cm, $course);
    if ($groupmode > 0 && $currentgroupid == 0) {
        $currentgroupid = groups_get_activity_group($questionnaire->cm);
        if (!groups_is_member($currentgroupid, $USER->id)) {
            $currentgroupid = 0;
        }
    }

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $questionnairenode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if (($i === false) && array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    if (has_capability('mod/questionnaire:manage', $context) && $owner) {
        $url = '/mod/questionnaire/qsettings.php';
        $node = navigation_node::create(get_string('advancedsettings'),
            new moodle_url($url, array('id' => $cmid)),
            navigation_node::TYPE_SETTING, null, 'advancedsettings',
            new pix_icon('t/edit', ''));
        $questionnairenode->add_node($node, $beforekey);
    }

    if (has_capability('mod/questionnaire:editquestions', $context) && $owner) {
        $url = '/mod/questionnaire/questions.php';
        $node = navigation_node::create(get_string('questions', 'questionnaire'),
            new moodle_url($url, array('id' => $cmid)),
            navigation_node::TYPE_SETTING, null, 'questions',
            new pix_icon('t/edit', ''));
        $questionnairenode->add_node($node, $beforekey);
    }

    if (has_capability('mod/questionnaire:preview', $context)) {
        $url = '/mod/questionnaire/preview.php';
        $node = navigation_node::create(get_string('preview_label', 'questionnaire'),
            new moodle_url($url, array('id' => $cmid)),
            navigation_node::TYPE_SETTING, null, 'preview',
            new pix_icon('t/preview', ''));
        $questionnairenode->add_node($node, $beforekey);
    }

    if ($questionnaire->user_can_take($USER->id)) {
        $url = '/mod/questionnaire/complete.php';
        if ($questionnaire->user_has_saved_response($USER->id)) {
            $args = ['id' => $cmid, 'resume' => 1];
            $text = get_string('resumesurvey', 'questionnaire');
        } else {
            $args = ['id' => $cmid];
            $text = get_string('answerquestions', 'questionnaire');
        }
        $node = navigation_node::create($text, new moodle_url($url, $args),
            navigation_node::TYPE_SETTING, null, '', new pix_icon('i/info', 'answerquestions'));
        $questionnairenode->add_node($node, $beforekey);
    }
    $usernumresp = $questionnaire->count_submissions($USER->id);

    if ($questionnaire->capabilities->readownresponses && ($usernumresp > 0)) {
        $url = '/mod/questionnaire/myreport.php';

        if ($usernumresp > 1) {
            $urlargs = array('instance' => $questionnaire->id, 'userid' => $USER->id,
                'byresponse' => 0, 'action' => 'summary', 'group' => $currentgroupid);
            $node = navigation_node::create(get_string('yourresponses', 'questionnaire'),
                new moodle_url($url, $urlargs), navigation_node::TYPE_SETTING, null, 'yourresponses');
            $myreportnode = $questionnairenode->add_node($node, $beforekey);

            $urlargs = array('instance' => $questionnaire->id, 'userid' => $USER->id,
                'byresponse' => 0, 'action' => 'summary', 'group' => $currentgroupid);
            $myreportnode->add(get_string('summary', 'questionnaire'), new moodle_url($url, $urlargs));

            $urlargs = array('instance' => $questionnaire->id, 'userid' => $USER->id,
                'byresponse' => 1, 'action' => 'vresp', 'group' => $currentgroupid);
            $byresponsenode = $myreportnode->add(get_string('viewindividualresponse', 'questionnaire'),
                new moodle_url($url, $urlargs));

            $urlargs = array('instance' => $questionnaire->id, 'userid' => $USER->id,
                'byresponse' => 0, 'action' => 'vall', 'group' => $currentgroupid);
            $myreportnode->add(get_string('myresponses', 'questionnaire'), new moodle_url($url, $urlargs));
            if ($questionnaire->capabilities->downloadresponses) {
                $urlargs = array('instance' => $questionnaire->id, 'user' => $USER->id,
                    'action' => 'dwnpg', 'group' => $currentgroupid);
                $myreportnode->add(get_string('downloadtext'), new moodle_url('/mod/questionnaire/report.php', $urlargs));
            }
        } else {
            $urlargs = array('instance' => $questionnaire->id, 'userid' => $USER->id,
                'byresponse' => 1, 'action' => 'vresp', 'group' => $currentgroupid);
            $node = navigation_node::create(get_string('yourresponse', 'questionnaire'),
                new moodle_url($url, $urlargs), navigation_node::TYPE_SETTING, null, 'yourresponse');
            $myreportnode = $questionnairenode->add_node($node, $beforekey);
        }
    }

    // If questionnaire is set to separate groups, prevent user who is not member of any group
    // and is not a non-editing teacher to view All responses.
    if ($questionnaire->can_view_all_responses($usernumresp)) {

        $url = '/mod/questionnaire/report.php';
        $node = navigation_node::create(get_string('viewallresponses', 'questionnaire'),
            new moodle_url($url, array('instance' => $questionnaire->id, 'action' => 'vall')),
            navigation_node::TYPE_SETTING, null, 'vall');
        $reportnode = $questionnairenode->add_node($node, $beforekey);

        if ($questionnaire->capabilities->viewsingleresponse) {
            $summarynode = $reportnode->add(get_string('summary', 'questionnaire'),
                new moodle_url('/mod/questionnaire/report.php',
                    array('instance' => $questionnaire->id, 'action' => 'vall')));
        } else {
            $summarynode = $reportnode;
        }
        $summarynode->add(get_string('order_default', 'questionnaire'),
            new moodle_url('/mod/questionnaire/report.php',
                array('instance' => $questionnaire->id, 'action' => 'vall', 'group' => $currentgroupid)));
        $summarynode->add(get_string('order_ascending', 'questionnaire'),
            new moodle_url('/mod/questionnaire/report.php',
                array('instance' => $questionnaire->id, 'action' => 'vallasort', 'group' => $currentgroupid)));
        $summarynode->add(get_string('order_descending', 'questionnaire'),
            new moodle_url('/mod/questionnaire/report.php',
                array('instance' => $questionnaire->id, 'action' => 'vallarsort', 'group' => $currentgroupid)));

        if ($questionnaire->capabilities->deleteresponses) {
            $summarynode->add(get_string('deleteallresponses', 'questionnaire'),
                new moodle_url('/mod/questionnaire/report.php',
                    array('instance' => $questionnaire->id, 'action' => 'delallresp', 'group' => $currentgroupid)));
        }

        if ($questionnaire->capabilities->downloadresponses) {
            $summarynode->add(get_string('downloadtextformat', 'questionnaire'),
                new moodle_url('/mod/questionnaire/report.php',
                    array('instance' => $questionnaire->id, 'action' => 'dwnpg', 'group' => $currentgroupid)));
        }
        if ($questionnaire->capabilities->viewsingleresponse) {
            $byresponsenode = $reportnode->add(get_string('viewbyresponse', 'questionnaire'),
                new moodle_url('/mod/questionnaire/report.php',
                    array('instance' => $questionnaire->id, 'action' => 'vresp', 'byresponse' => 1, 'group' => $currentgroupid)));

            $byresponsenode->add(get_string('view', 'questionnaire'),
                new moodle_url('/mod/questionnaire/report.php',
                    array('instance' => $questionnaire->id, 'action' => 'vresp', 'byresponse' => 1, 'group' => $currentgroupid)));

            if ($individualresponse) {
                $byresponsenode->add(get_string('deleteresp', 'questionnaire'),
                    new moodle_url('/mod/questionnaire/report.php',
                        array('instance' => $questionnaire->id, 'action' => 'dresp', 'byresponse' => 1,
                            'rid' => $rid, 'group' => $currentgroupid, 'individualresponse' => 1)));
            }
        }
    }

    $canviewgroups = true;
    $groupmode = groups_get_activity_groupmode($cm, $course);
    if ($groupmode == 1) {
        $canviewgroups = groups_has_membership($cm, $USER->id);
    }
    $canviewallgroups = has_capability('moodle/site:accessallgroups', $context);
    if ($questionnaire->capabilities->viewsingleresponse && ($canviewallgroups || $canviewgroups)) {
        $url = '/mod/questionnaire/show_nonrespondents.php';
        $node = navigation_node::create(get_string('show_nonrespondents', 'questionnaire'),
            new moodle_url($url, array('id' => $cmid)),
            navigation_node::TYPE_SETTING, null, 'nonrespondents');
        $questionnairenode->add_node($node, $beforekey);

    }
}

// Any other questionnaire functions go here.  Each of them must have a name that
// starts with questionnaire_.

function questionnaire_get_view_actions() {
    return array('view', 'view all');
}

function questionnaire_get_post_actions() {
    return array('submit', 'update');
}

function questionnaire_get_recent_mod_activity(&$activities, &$index, $timestart,
                $courseid, $cmid, $userid = 0, $groupid = 0) {

    global $CFG, $COURSE, $USER, $DB;
    require_once($CFG->dirroot . '/mod/questionnaire/locallib.php');

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];
    $questionnaire = $DB->get_record('questionnaire', array('id' => $cm->instance));

    $context = context_module::instance($cm->id);
    $grader = has_capability('mod/questionnaire:viewsingleresponse', $context);

    // If this is a copy of a public questionnaire whose original is located in another course,
    // current user (teacher) cannot view responses.
    if ($grader && $survey = $DB->get_record('questionnaire_survey', array('id' => $questionnaire->sid))) {
        // For a public questionnaire, look for the original public questionnaire that it is based on.
        if ($survey->realm == 'public' && $survey->courseid != $course->id) {
            // For a public questionnaire, look for the original public questionnaire that it is based on.
            $originalquestionnaire = $DB->get_record('questionnaire', ['sid' => $survey->id, 'course' => $survey->courseid]);
            $cmoriginal = get_coursemodule_from_instance("questionnaire", $originalquestionnaire->id, $survey->courseid);
            $contextoriginal = context_course::instance($survey->courseid, MUST_EXIST);
            if (!has_capability('mod/questionnaire:viewsingleresponse', $contextoriginal)) {
                $tmpactivity = new stdClass();
                $tmpactivity->type = 'questionnaire';
                $tmpactivity->cmid = $cm->id;
                $tmpactivity->cannotview = true;
                $tmpactivity->anonymous = false;
                $activities[$index++] = $tmpactivity;
                return $activities;
            }
        }
    }

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
    $params['questionnaireid'] = $questionnaire->sid;

    $ufields = user_picture::fields('u', null, 'useridagain');
    if (!$attempts = $DB->get_records_sql("
                    SELECT qr.*,
                    {$ufields}
                    FROM {questionnaire_response} qr
                    JOIN {user} u ON u.id = qr.userid
                    $groupjoin
                    WHERE qr.submitted > :timestart
                    AND qr.survey_id = :questionnaireid
                    $userselect
                    $groupselect
                    ORDER BY qr.submitted ASC", $params)) {
        return;
    }

    $accessallgroups = has_capability('moodle/site:accessallgroups', $context);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $context);
    $groupmode       = groups_get_activity_groupmode($cm, $course);

    $usersgroups = null;
    $aname = format_string($cm->name, true);
    $userattempts = array();
    foreach ($attempts as $attempt) {
        if ($questionnaire->respondenttype != 'anonymous') {
            if (!isset($userattempts[$attempt->lastname])) {
                $userattempts[$attempt->lastname] = 1;
            } else {
                $userattempts[$attempt->lastname]++;
            }
        }
        if ($attempt->userid != $USER->id) {
            if (!$grader) {
                // View complete individual responses permission required.
                continue;
            }

            if (($groupmode == SEPARATEGROUPS) && !$accessallgroups) {
                if ($usersgroups === null) {
                    $usersgroups = groups_get_all_groups($course->id,
                    $attempt->userid, $cm->groupingid);
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

        $tmpactivity->type       = 'questionnaire';
        $tmpactivity->cmid       = $cm->id;
        $tmpactivity->cminstance = $cm->instance;
        // Current user is admin - or teacher enrolled in original public course.
        if (isset($cmoriginal)) {
            $tmpactivity->cminstance = $cmoriginal->instance;
        }
        $tmpactivity->cannotview = false;
        $tmpactivity->anonymous  = false;
        $tmpactivity->name       = $aname;
        $tmpactivity->sectionnum = $cm->sectionnum;
        $tmpactivity->timestamp  = $attempt->submitted;
        $tmpactivity->groupid    = $groupid;
        if (isset($userattempts[$attempt->lastname])) {
            $tmpactivity->nbattempts = $userattempts[$attempt->lastname];
        }

        $tmpactivity->content = new stdClass();
        $tmpactivity->content->attemptid = $attempt->id;

        $userfields = explode(',', user_picture::fields());
        $tmpactivity->user = new stdClass();
        foreach ($userfields as $userfield) {
            if ($userfield == 'id') {
                $tmpactivity->user->{$userfield} = $attempt->userid;
            } else {
                if (!empty($attempt->{$userfield})) {
                    $tmpactivity->user->{$userfield} = $attempt->{$userfield};
                } else {
                    $tmpactivity->user->{$userfield} = null;
                }
            }
        }
        if ($questionnaire->respondenttype != 'anonymous') {
            $tmpactivity->user->fullname  = fullname($attempt, $viewfullnames);
        } else {
            $tmpactivity->user = '';
            unset ($tmpactivity->user);
            $tmpactivity->anonymous = true;
        }
        $activities[$index++] = $tmpactivity;
    }
}

/**
 * Prints all users who have completed a specified questionnaire since a given time
 *
 * @global object
 * @param object $activity
 * @param int $courseid
 * @param string $detail not used but needed for compability
 * @param array $modnames
 * @return void Output is echo'd
 *
 * $details and $modenames are unused, but API requires them. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $OUTPUT;

    // If the questionnaire is "anonymous", then $activity->user won't have been set, so do not display respondent info.
    if ($activity->anonymous) {
        $stranonymous = ' ('.get_string('anonymous', 'questionnaire').')';
        $activity->nbattempts = '';
    } else {
        $stranonymous = '';
    }
    // Current user cannot view responses to public questionnaire.
    if ($activity->cannotview) {
        $strcannotview = get_string('cannotviewpublicresponses', 'questionnaire');
    }
    echo html_writer::start_tag('div');
    echo html_writer::start_tag('span', array('class' => 'clearfix',
                    'style' => 'margin-top:0px; background-color: white; display: inline-block;'));

    if (!$activity->anonymous && !$activity->cannotview) {
        echo html_writer::tag('div', $OUTPUT->user_picture($activity->user, array('courseid' => $courseid)),
                        array('style' => 'float: left; padding-right: 10px;'));
    }
    if (!$activity->cannotview) {
        echo html_writer::start_tag('div');
        echo html_writer::start_tag('div');

        $urlparams = array('action' => 'vresp', 'instance' => $activity->cminstance,
                        'group' => $activity->groupid, 'rid' => $activity->content->attemptid, 'individualresponse' => 1);

        $context = context_module::instance($activity->cmid);
        if (has_capability('mod/questionnaire:viewsingleresponse', $context)) {
            $report = 'report.php';
        } else {
            $report = 'myreport.php';
        }
        echo html_writer::tag('a', get_string('response', 'questionnaire').' '.$activity->nbattempts.$stranonymous,
                        array('href' => new moodle_url('/mod/questionnaire/'.$report, $urlparams)));
        echo html_writer::end_tag('div');
    } else {
        echo html_writer::start_tag('div');
        echo html_writer::start_tag('div');
        echo html_writer::tag('div', $strcannotview);
        echo html_writer::end_tag('div');
    }
    if (!$activity->anonymous  && !$activity->cannotview) {
        $url = new moodle_url('/user/view.php', array('course' => $courseid, 'id' => $activity->user->id));
        $name = $activity->user->fullname;
        $link = html_writer::link($url, $name);
        echo html_writer::start_tag('div', array('class' => 'user'));
        echo $link .' - '. userdate($activity->timestamp);
        echo html_writer::end_tag('div');
    }

    echo html_writer::end_tag('div');
    echo html_writer::end_tag('span');
    echo html_writer::end_tag('div');

    return;
}

/**
 * Prints questionnaire summaries on 'My home' page
 *
 * Prints questionnaire name, due date and attempt information on
 * questionnaires that have a deadline that has not already passed
 * and it is available for taking.
 *
 * @global object
 * @global stdClass
 * @global object
 * @uses CONTEXT_MODULE
 * @param array $courses An array of course objects to get questionnaire instances from
 * @param array $htmlarray Store overview output array( course ID => 'questionnaire' => HTML output )
 * @return void
 */
function questionnaire_print_overview($courses, &$htmlarray) {
    global $USER, $CFG, $DB, $OUTPUT;

    require_once($CFG->dirroot . '/mod/questionnaire/locallib.php');

    if (!$questionnaires = get_all_instances_in_courses('questionnaire', $courses)) {
        return;
    }

    // Get Necessary Strings.
    $strquestionnaire       = get_string('modulename', 'questionnaire');
    $strnotattempted = get_string('noattempts', 'questionnaire');
    $strattempted    = get_string('attempted', 'questionnaire');
    $strsavedbutnotsubmitted = get_string('savedbutnotsubmitted', 'questionnaire');

    $now = time();
    foreach ($questionnaires as $questionnaire) {

        // The questionnaire has a deadline.
        if (($questionnaire->closedate != 0)
                        // And it is before the deadline has been met.
                        && ($questionnaire->closedate >= $now)
                        // And the questionnaire is available.
                        && (($questionnaire->opendate == 0) || ($questionnaire->opendate <= $now))) {
            if (!$questionnaire->visible) {
                $class = ' class="dimmed"';
            } else {
                $class = '';
            }
            $str = $OUTPUT->box("$strquestionnaire:
                            <a$class href=\"$CFG->wwwroot/mod/questionnaire/view.php?id=$questionnaire->coursemodule\">".
                            format_string($questionnaire->name).'</a>', 'name');

            // Deadline.
            $str .= $OUTPUT->box(get_string('closeson', 'questionnaire', userdate($questionnaire->closedate)), 'info');
            $select = 'qid = '.$questionnaire->id.' AND userid = '.$USER->id;
            $attempts = $DB->get_records_select('questionnaire_attempts', $select);
            $nbattempts = count($attempts);

            // Do not display a questionnaire as due if it can only be sumbitted once and it has already been submitted!
            if ($nbattempts != 0 && $questionnaire->qtype == QUESTIONNAIREONCE) {
                continue;
            }

            // Attempt information.
            if (has_capability('mod/questionnaire:manage', context_module::instance($questionnaire->coursemodule))) {
                // Number of user attempts.
                $attempts = $DB->count_records('questionnaire_attempts', array('id' => $questionnaire->id));
                $str .= $OUTPUT->box(get_string('numattemptsmade', 'questionnaire', $attempts), 'info');
            } else {
                if ($responses = questionnaire_get_user_responses($questionnaire->sid, $USER->id, false)) {
                    foreach ($responses as $response) {
                        if ($response->complete == 'y') {
                            $str .= $OUTPUT->box($strattempted, 'info');
                            break;
                        } else {
                            $str .= $OUTPUT->box($strsavedbutnotsubmitted, 'info');
                        }
                    }
                } else {
                    $str .= $OUTPUT->box($strnotattempted, 'info');
                }
            }
            $str = $OUTPUT->box($str, 'questionnaire overview');

            if (empty($htmlarray[$questionnaire->course]['questionnaire'])) {
                $htmlarray[$questionnaire->course]['questionnaire'] = $str;
            } else {
                $htmlarray[$questionnaire->course]['questionnaire'] .= $str;
            }
        }
    }
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the questionnaire.
 *
 * @param $mform the course reset form that is being built.
 */
function questionnaire_reset_course_form_definition($mform) {
    $mform->addElement('header', 'questionnaireheader', get_string('modulenameplural', 'questionnaire'));
    $mform->addElement('advcheckbox', 'reset_questionnaire',
                    get_string('removeallquestionnaireattempts', 'questionnaire'));
}

/**
 * Course reset form defaults.
 * @return array the defaults.
 *
 * Function parameters are unused, but API requires them. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_reset_course_form_defaults($course) {
    return array('reset_questionnaire' => 1);
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * questionnaire responses for course $data->courseid, if $data->reset_questionnaire_attempts is
 * set and true.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function questionnaire_reset_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/questionlib.php');
    require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

    $componentstr = get_string('modulenameplural', 'questionnaire');
    $status = array();

    if (!empty($data->reset_questionnaire)) {
        $surveys = questionnaire_get_survey_list($data->courseid, '');

        // Delete responses.
        foreach ($surveys as $survey) {
            // Get all responses for this questionnaire.
            $sql = "SELECT R.id, R.survey_id, R.submitted, R.userid
                 FROM {questionnaire_response} R
                 WHERE R.survey_id = ?
                 ORDER BY R.id";
            $resps = $DB->get_records_sql($sql, array($survey->id));
            if (!empty($resps)) {
                $questionnaire = $DB->get_record("questionnaire", ["sid" => $survey->id, "course" => $survey->courseid]);
                $questionnaire->course = $DB->get_record("course", array("id" => $questionnaire->course));
                foreach ($resps as $response) {
                    questionnaire_delete_response($response, $questionnaire);
                }
            }
            // Remove this questionnaire's grades (and feedback) from gradebook (if any).
            $select = "itemmodule = 'questionnaire' AND iteminstance = ".$survey->qid;
            $fields = 'id';
            if ($itemid = $DB->get_record_select('grade_items', $select, null, $fields)) {
                $itemid = $itemid->id;
                $DB->delete_records_select('grade_grades', 'itemid = '.$itemid);

            }
        }
        $status[] = array(
                        'component' => $componentstr,
                        'item' => get_string('deletedallresp', 'questionnaire'),
                        'error' => false);

        $status[] = array(
                        'component' => $componentstr,
                        'item' => get_string('gradesdeleted', 'questionnaire'),
                        'error' => false);
    }
    return $status;
}

/**
 * Obtains the automatic completion state for this questionnaire based on the condition
 * in questionnaire settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 *
 * $course is unused, but API requires it. Suppress PHPMD warning.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function questionnaire_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // Get questionnaire details.
    $questionnaire = $DB->get_record('questionnaire', array('id' => $cm->instance), '*', MUST_EXIST);

    // If completion option is enabled, evaluate it and return true/false.
    if ($questionnaire->completionsubmit) {
        $params = array('userid' => $userid, 'qid' => $questionnaire->id);
        return $DB->record_exists('questionnaire_attempts', $params);
    } else {
        // Completion option is not enabled so just return $type.
        return $type;
    }
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
function mod_questionnaire_core_calendar_provide_event_action(calendar_event $event,
                                                            \core_calendar\action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['questionnaire'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
            get_string('view'),
            new \moodle_url('/mod/questionnaire/view.php', ['id' => $cm->id]),
            1,
            true
    );
}

