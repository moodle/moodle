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
 * Updates the contents of the survey with the provided data. If no data is provided, it checks for posted data.
 *
 * This library replaces the phpESP application with Moodle specific code. It will eventually
 * replace all of the phpESP application, removing the dependency on that.
 *
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/calendar/lib.php');
// Constants.

define ('QUESTIONNAIREUNLIMITED', 0);
define ('QUESTIONNAIREONCE', 1);
define ('QUESTIONNAIREDAILY', 2);
define ('QUESTIONNAIREWEEKLY', 3);
define ('QUESTIONNAIREMONTHLY', 4);

define ('QUESTIONNAIRE_STUDENTVIEWRESPONSES_NEVER', 0);
define ('QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENANSWERED', 1);
define ('QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENCLOSED', 2);
define ('QUESTIONNAIRE_STUDENTVIEWRESPONSES_ALWAYS', 3);

define('QUESTIONNAIRE_MAX_EVENT_LENGTH', 5 * 24 * 60 * 60);   // 5 days maximum.

define('QUESTIONNAIRE_DEFAULT_PAGE_COUNT', 20);

global $questionnairetypes;
$questionnairetypes = array (QUESTIONNAIREUNLIMITED => get_string('qtypeunlimited', 'questionnaire'),
                              QUESTIONNAIREONCE => get_string('qtypeonce', 'questionnaire'),
                              QUESTIONNAIREDAILY => get_string('qtypedaily', 'questionnaire'),
                              QUESTIONNAIREWEEKLY => get_string('qtypeweekly', 'questionnaire'),
                              QUESTIONNAIREMONTHLY => get_string('qtypemonthly', 'questionnaire'));

global $questionnairerespondents;
$questionnairerespondents = array ('fullname' => get_string('respondenttypefullname', 'questionnaire'),
                                    'anonymous' => get_string('respondenttypeanonymous', 'questionnaire'));

global $questionnairerealms;
$questionnairerealms = array ('private' => get_string('private', 'questionnaire'),
                               'public' => get_string('public', 'questionnaire'),
                               'template' => get_string('template', 'questionnaire'));

global $questionnaireresponseviewers;
$questionnaireresponseviewers = array (
            QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENANSWERED => get_string('responseviewstudentswhenanswered', 'questionnaire'),
            QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENCLOSED => get_string('responseviewstudentswhenclosed', 'questionnaire'),
            QUESTIONNAIRE_STUDENTVIEWRESPONSES_ALWAYS => get_string('responseviewstudentsalways', 'questionnaire'),
            QUESTIONNAIRE_STUDENTVIEWRESPONSES_NEVER => get_string('responseviewstudentsnever', 'questionnaire'));

global $autonumbering;
$autonumbering = array (0 => get_string('autonumberno', 'questionnaire'),
        1 => get_string('autonumberquestions', 'questionnaire'),
        2 => get_string('autonumberpages', 'questionnaire'),
        3 => get_string('autonumberpagesandquestions', 'questionnaire'));

/**
 * Return the choice values for the content.
 * @param string $content
 * @return stdClass
 */
function questionnaire_choice_values($content) {

    // If we run the content through format_text first, any filters we want to use (e.g. multilanguage) should work.
    // examines the content of a possible answer from radio button, check boxes or rate question
    // returns ->text to be displayed, ->image if present, ->modname name of modality, image ->title.
    $contents = new stdClass();
    $contents->text = '';
    $contents->image = '';
    $contents->modname = '';
    $contents->title = '';
    // Has image.
    if (preg_match('/(<img)\s .*(src="(.[^"]{1,})")/isxmU', $content, $matches)) {
        $contents->image = $matches[0];
        $imageurl = $matches[3];
        // Image has a title or alt text: use one of them.
        if (preg_match('/(title=.)([^"]{1,})/', $content, $matches)
             || preg_match('/(alt=.)([^"]{1,})/', $content, $matches) ) {
            $contents->title = $matches[2];
        } else {
            // Image has no title nor alt text: use its filename (without the extension).
            preg_match("/.*\/(.*)\..*$/", $imageurl, $matches);
            $contents->title = $matches[1];
        }
        // Content has text or named modality plus an image.
        if (preg_match('/(.*)(<img.*)/', $content, $matches)) {
            $content = $matches[1];
        } else {
            // Just an image.
            return $contents;
        }
    }

    // Check for score value first (used e.g. by personality test feature).
    $r = preg_match_all("/^(\d{1,2}=)(.*)$/", $content, $matches);
    if ($r) {
        $content = $matches[2][0];
    }

    // Look for named modalities.
    $contents->text = $content;
    // DEV JR from version 2.5, a double colon :: must be used here instead of the equal sign.
    if ($pos = strpos($content, '::')) {
        $contents->text = substr($content, $pos + 2);
        $contents->modname = substr($content, 0, $pos);
    }
    return $contents;
}

/**
 * Get the information about the standard questionnaire JavaScript module.
 * @return array a standard jsmodule structure.
 */
function questionnaire_get_js_module() {
    return [
            'name' => 'mod_questionnaire',
            'fullpath' => '/mod/questionnaire/module.js',
            'requires' => ['base', 'dom', 'event-delegate', 'event-key',
                    'core_question_engine', 'moodle-core-formchangechecker'],
            'strings' => [
                    ['cancel', 'moodle'],
                    ['flagged', 'question'],
                    ['functiondisabledbysecuremode', 'quiz'],
                    ['startattempt', 'quiz'],
                    ['timesup', 'quiz'],
                    ['changesmadereallygoaway', 'moodle'],
                    ['leftpart', 'questionnaire'],
                    ['leftpartdefault', 'questionnaire'],
                    ['middlepart', 'questionnaire'],
                    ['middlepartdefault', 'questionnaire'],
                    ['middlepartwithtwovalues', 'questionnaire'],
                    ['middlepartwithtwovaluesdefault', 'questionnaire'],
                    ['rightpart', 'questionnaire'],
                    ['rightpartdefault', 'questionnaire'],
                    ['where', 'questionnaire'],
            ],
    ];
}

/**
 * Get all the questionnaire responses for a user.
 * @param int $questionnaireid
 * @param int $userid
 * @param bool $complete
 * @return array
 */
function questionnaire_get_user_responses($questionnaireid, $userid, $complete=true) {
    global $DB;
    $andcomplete = '';
    if ($complete) {
        $andcomplete = " AND complete = 'y' ";
    }
    return $DB->get_records_sql ("SELECT *
        FROM {questionnaire_response}
        WHERE questionnaireid = ?
        AND userid = ?
        ".$andcomplete."
        ORDER BY submitted ASC ", array($questionnaireid, $userid)) ?? [];
}

/**
 * get the capabilities for the questionnaire
 * @param int $cmid
 * @return object the available capabilities from current user
 */
function questionnaire_load_capabilities($cmid) {
    static $cb;

    if (isset($cb)) {
        return $cb;
    }

    $context = questionnaire_get_context($cmid);

    $cb = new stdClass();
    $cb->view = has_capability('mod/questionnaire:view', $context);
    $cb->submit = has_capability('mod/questionnaire:submit', $context);
    $cb->viewsingleresponse = has_capability('mod/questionnaire:viewsingleresponse', $context);
    $cb->submissionnotification = has_capability('mod/questionnaire:submissionnotification', $context);
    $cb->downloadresponses = has_capability('mod/questionnaire:downloadresponses', $context);
    $cb->deleteresponses = has_capability('mod/questionnaire:deleteresponses', $context);
    $cb->manage = has_capability('mod/questionnaire:manage', $context);
    $cb->editquestions = has_capability('mod/questionnaire:editquestions', $context);
    $cb->createtemplates = has_capability('mod/questionnaire:createtemplates', $context);
    $cb->createpublic = has_capability('mod/questionnaire:createpublic', $context);
    $cb->readownresponses = has_capability('mod/questionnaire:readownresponses', $context);
    $cb->readallresponses = has_capability('mod/questionnaire:readallresponses', $context);
    $cb->readallresponseanytime = has_capability('mod/questionnaire:readallresponseanytime', $context);
    $cb->printblank = has_capability('mod/questionnaire:printblank', $context);
    $cb->preview = has_capability('mod/questionnaire:preview', $context);

    $cb->viewhiddenactivities = has_capability('moodle/course:viewhiddenactivities', $context, null, false);

    return $cb;
}

/**
 * returns the context-id related to the given coursemodule-id
 * @param int $cmid the coursemodule-id
 * @return object $context
 */
function questionnaire_get_context($cmid) {
    static $context;

    if (isset($context)) {
        return $context;
    }

    if (!$context = context_module::instance($cmid)) {
        throw new \moodle_exception('badcontext', 'mod_questionnaire');
    }
    return $context;
}

/**
 * This function *really* shouldn't be needed, but since sometimes we can end up with
 * orphaned surveys, this will clean them up.
 * @return bool
 * @throws dml_exception
 */
function questionnaire_cleanup() {
    global $DB;

    // Find surveys that don't have questionnaires associated with them.
    $sql = 'SELECT qs.* FROM {questionnaire_survey} qs '.
           'LEFT JOIN {questionnaire} q ON q.sid = qs.id '.
           'WHERE q.sid IS NULL';

    if ($surveys = $DB->get_records_sql($sql)) {
        foreach ($surveys as $survey) {
            questionnaire_delete_survey($survey->id, 0);
        }
    }
    // Find deleted questions and remove them from database (with their associated choices, etc.).
    return true;
}

/**
 * Delete the survey.
 * @param int $sid
 * @param int $questionnaireid
 * @return bool
 */
function questionnaire_delete_survey($sid, $questionnaireid) {
    global $DB;
    $status = true;
    // Delete all survey attempts and responses.
    if ($responses = $DB->get_records('questionnaire_response', ['questionnaireid' => $questionnaireid], 'id')) {
        foreach ($responses as $response) {
            $status = $status && questionnaire_delete_response($response);
        }
    }

    // There really shouldn't be any more, but just to make sure...
    $DB->delete_records('questionnaire_response', ['questionnaireid' => $questionnaireid]);

    // Delete all question data for the survey.
    if ($questions = $DB->get_records('questionnaire_question', ['surveyid' => $sid], 'id')) {
        foreach ($questions as $question) {
            $DB->delete_records('questionnaire_quest_choice', ['question_id' => $question->id]);
            questionnaire_delete_dependencies($question->id);
        }
        $status = $status && $DB->delete_records('questionnaire_question', ['surveyid' => $sid]);
        // Just to make sure.
        $status = $status && $DB->delete_records('questionnaire_dependency', ['surveyid' => $sid]);
    }

    // Delete all feedback sections and feedback messages for the survey.
    if ($fbsections = $DB->get_records('questionnaire_fb_sections', ['surveyid' => $sid], 'id')) {
        foreach ($fbsections as $fbsection) {
            $DB->delete_records('questionnaire_feedback', ['sectionid' => $fbsection->id]);
        }
        $status = $status && $DB->delete_records('questionnaire_fb_sections', ['surveyid' => $sid]);
    }

    $status = $status && $DB->delete_records('questionnaire_survey', ['id' => $sid]);

    return $status;
}

/**
 * Delete the response.
 * @param stdClass $response
 * @param string $questionnaire
 * @return bool
 */
function questionnaire_delete_response($response, $questionnaire='') {
    global $DB;
    $status = true;
    $cm = '';
    $rid = $response->id;
    // The questionnaire_delete_survey function does not send the questionnaire array.
    if ($questionnaire != '') {
        $cm = get_coursemodule_from_instance("questionnaire", $questionnaire->id, $questionnaire->course->id);
    }

    // Delete all of the response data for a response.
    $DB->delete_records('questionnaire_response_bool', array('response_id' => $rid));
    $DB->delete_records('questionnaire_response_date', array('response_id' => $rid));
    $DB->delete_records('questionnaire_resp_multiple', array('response_id' => $rid));
    $DB->delete_records('questionnaire_response_other', array('response_id' => $rid));
    $DB->delete_records('questionnaire_response_rank', array('response_id' => $rid));
    $DB->delete_records('questionnaire_resp_single', array('response_id' => $rid));
    $DB->delete_records('questionnaire_response_text', array('response_id' => $rid));

    $status = $status && $DB->delete_records('questionnaire_response', array('id' => $rid));

    if ($status && $cm) {
        // Update completion state if necessary.
        $completion = new completion_info($questionnaire->course);
        if ($completion->is_enabled($cm) == COMPLETION_TRACKING_AUTOMATIC && $questionnaire->completionsubmit) {
            $completion->update_state($cm, COMPLETION_INCOMPLETE, $response->userid);
        }
    }

    return $status;
}

/**
 * Delete all responses for the questionnaire.
 * @param int $qid
 * @return bool
 */
function questionnaire_delete_responses($qid) {
    global $DB;

    // Delete all of the response data for a question.
    $DB->delete_records('questionnaire_response_bool', ['question_id' => $qid]);
    $DB->delete_records('questionnaire_response_date', ['question_id' => $qid]);
    $DB->delete_records('questionnaire_resp_multiple', ['question_id' => $qid]);
    $DB->delete_records('questionnaire_response_other', ['question_id' => $qid]);
    $DB->delete_records('questionnaire_response_rank', ['question_id' => $qid]);
    $DB->delete_records('questionnaire_resp_single', ['question_id' => $qid]);
    $DB->delete_records('questionnaire_response_text', ['question_id' => $qid]);

    return true;
}

/**
 * Delete all dependencies for the questionnaire.
 * @param int $qid
 * @return bool
 */
function questionnaire_delete_dependencies($qid) {
    global $DB;

    // Delete all dependencies for this question.
    $DB->delete_records('questionnaire_dependency', ['questionid' => $qid]);
    $DB->delete_records('questionnaire_dependency', ['dependquestionid' => $qid]);

    return true;
}

/**
 * Get a survey selection records.
 * @param int $courseid
 * @param string $type
 * @return array|false
 */
function questionnaire_get_survey_list($courseid=0, $type='') {
    global $DB;

    if ($courseid == 0) {
        if (isadmin()) {
            $sql = "SELECT id,name,courseid,realm,status " .
                   "{questionnaire_survey} " .
                   "ORDER BY realm,name ";
            $params = null;
        } else {
            return false;
        }
    } else {
        if ($type == 'public') {
            $sql = "SELECT s.id,s.name,s.courseid,s.realm,s.status,s.title,q.id as qid,q.name as qname " .
                   "FROM {questionnaire} q " .
                   "INNER JOIN {questionnaire_survey} s ON s.id = q.sid AND s.courseid = q.course " .
                   "WHERE realm = ? " .
                   "ORDER BY realm,name ";
            $params = [$type];
        } else if ($type == 'template') {
            $sql = "SELECT s.id,s.name,s.courseid,s.realm,s.status,s.title,q.id as qid,q.name as qname " .
                   "FROM {questionnaire} q " .
                   "INNER JOIN {questionnaire_survey} s ON s.id = q.sid AND s.courseid = q.course " .
                   "WHERE (realm = ?) " .
                   "ORDER BY realm,name ";
            $params = [$type];
        } else if ($type == 'private') {
            $sql = "SELECT s.id,s.name,s.courseid,s.realm,s.status,q.id as qid,q.name as qname " .
                "FROM {questionnaire} q " .
                "INNER JOIN {questionnaire_survey} s ON s.id = q.sid " .
                "WHERE s.courseid = ? and realm = ? " .
                "ORDER BY realm,name ";
            $params = [$courseid, $type];

        } else {
            // Current get_survey_list is called from function questionnaire_reset_userdata so we need to get a
            // complete list of all questionnaires in current course to reset them.
            $sql = "SELECT s.id,s.name,s.courseid,s.realm,s.status,q.id as qid,q.name as qname " .
                   "FROM {questionnaire} q " .
                    "INNER JOIN {questionnaire_survey} s ON s.id = q.sid AND s.courseid = q.course " .
                   "WHERE s.courseid = ? " .
                   "ORDER BY realm,name ";
            $params = [$courseid];
        }
    }
    return $DB->get_records_sql($sql, $params) ?? [];
}

/**
 * Get survey selection list.
 * @param int $courseid
 * @param string $type
 * @return array
 */
function questionnaire_get_survey_select($courseid=0, $type='') {
    global $OUTPUT, $DB;

    $surveylist = array();

    if ($surveys = questionnaire_get_survey_list($courseid, $type)) {
        $strpreview = get_string('preview_questionnaire', 'questionnaire');
        foreach ($surveys as $survey) {
            $originalcourse = $DB->get_record('course', ['id' => $survey->courseid]);
            if (!$originalcourse) {
                // This should not happen, but we found a case where a public survey
                // still existed in a course that had been deleted, and so this
                // code lead to a notice, and a broken link. Since that is useless
                // we just skip surveys like this.
                continue;
            }

            // Prevent creating a copy of a public questionnaire IN THE SAME COURSE as the original.
            if (($type == 'public') && ($survey->courseid == $courseid)) {
                continue;
            } else {
                $args = "sid={$survey->id}&popup=1";
                if (!empty($survey->qid)) {
                    $args .= "&qid={$survey->qid}";
                }
                $link = new moodle_url("/mod/questionnaire/preview.php?{$args}");
                $action = new popup_action('click', $link);
                $label = $OUTPUT->action_link($link, $survey->qname.' ['.$originalcourse->fullname.']',
                    $action, array('title' => $strpreview));
                $surveylist[$type.'-'.$survey->id] = $label;
            }
        }
    }
    return $surveylist;
}

/**
 * Return the language string for the specified question type.
 * @param int $id
 * @return lang_string|mixed|string
 * @throws coding_exception
 */
function questionnaire_get_type ($id) {
    switch ($id) {
        case 1:
            return get_string('yesno', 'questionnaire');
        case 2:
            return get_string('textbox', 'questionnaire');
        case 3:
            return get_string('essaybox', 'questionnaire');
        case 4:
            return get_string('radiobuttons', 'questionnaire');
        case 5:
            return get_string('checkboxes', 'questionnaire');
        case 6:
            return get_string('dropdown', 'questionnaire');
        case 8:
            return get_string('ratescale', 'questionnaire');
        case 9:
            return get_string('date', 'questionnaire');
        case 10:
            return get_string('numeric', 'questionnaire');
        case 11:
            return get_string('slider', 'questionnaire');
        case 100:
            return get_string('sectiontext', 'questionnaire');
        case 99:
            return get_string('sectionbreak', 'questionnaire');
        default:
        return $id;
    }
}

/**
 * This creates new events given as opendate and closedate by $questionnaire.
 * @param object $questionnaire
 * @return void
 */
function questionnaire_set_events($questionnaire) {
    // Adding the questionnaire to the eventtable.
    global $DB;
    if ($events = $DB->get_records('event', array('modulename' => 'questionnaire', 'instance' => $questionnaire->id))) {
        foreach ($events as $event) {
            $event = calendar_event::load($event);
            $event->delete();
        }
    }

    // The open-event.
    $event = new stdClass;
    $event->description = $questionnaire->name;
    $event->courseid = $questionnaire->course;
    $event->groupid = 0;
    $event->userid = 0;
    $event->modulename = 'questionnaire';
    $event->instance = $questionnaire->id;
    $event->eventtype = 'open';
    $event->type = CALENDAR_EVENT_TYPE_ACTION;
    $event->timestart = $questionnaire->opendate;
    $event->visible = instance_is_visible('questionnaire', $questionnaire);
    $event->timeduration = ($questionnaire->closedate - $questionnaire->opendate);

    if ($questionnaire->closedate && $questionnaire->opendate && ($event->timeduration <= QUESTIONNAIRE_MAX_EVENT_LENGTH)) {
        // Single event for the whole questionnaire.
        $event->name = $questionnaire->name;
        $event->timesort = $questionnaire->opendate;
        calendar_event::create($event);
    } else {
        // Separate start and end events.
        $event->timeduration = 0;
        if ($questionnaire->opendate) {
            $event->name = $questionnaire->name.' ('.get_string('questionnaireopens', 'questionnaire').')';
            $event->timesort = $questionnaire->opendate;
            calendar_event::create($event);
            unset($event->id); // So we can use the same object for the close event.
        }
        if ($questionnaire->closedate) {
            $event->name = $questionnaire->name.' ('.get_string('questionnairecloses', 'questionnaire').')';
            $event->timestart = $questionnaire->closedate;
            $event->timesort = $questionnaire->closedate;
            $event->eventtype = 'close';
            calendar_event::create($event);
        }
    }
}

/**
 * Get users who have not completed the questionnaire
 *
 * @param object $cm
 * @param int $sid
 * @param bool $group single groupid
 * @param string $sort
 * @param bool $startpage
 * @param bool $pagecount
 * @return object the userrecords
 * @throws coding_exception
 * @throws dml_exception
 */
function questionnaire_get_incomplete_users($cm, $sid,
                $group = false,
                $sort = '',
                $startpage = false,
                $pagecount = false) {

    global $DB;

    $context = context_module::instance($cm->id);

    // First get all users who can complete this questionnaire.
    $cap = 'mod/questionnaire:submit';
    $fields = 'u.id, u.username';
    if (!$allusers = get_enrolled_users($context, $cap, $group, $fields, $sort)) {
        return false;
    }
    $allusers = array_keys($allusers);

    // Nnow get all completed questionnaires.
    $params = array('questionnaireid' => $cm->instance, 'complete' => 'y');
    $sql = "SELECT userid FROM {questionnaire_response} " .
           "WHERE questionnaireid = :questionnaireid AND complete = :complete " .
           "GROUP BY userid ";

    if (!$completedusers = $DB->get_records_sql($sql, $params)) {
        return $allusers;
    }
    $completedusers = array_keys($completedusers);
    // Now strike all completedusers from allusers.
    $allusers = array_diff($allusers, $completedusers);
    // For paging I use array_slice().
    if (($startpage !== false) && ($pagecount !== false)) {
        $allusers = array_slice($allusers, $startpage, $pagecount);
    }
    return $allusers;
}

/**
 * Called by HTML editor in showrespondents and Essay question. Based on question/essay/renderer.
 * Pending general solution to using the HTML editor outside of moodleforms in Moodle pages.
 * @param int $context
 * @return array
 */
function questionnaire_get_editor_options($context) {
    return array(
                    'subdirs' => 0,
                    'maxbytes' => 0,
                    'maxfiles' => -1,
                    'context' => $context,
                    'noclean' => 0,
                    'trusttext' => 0
    );
}

/**
 * Get the parent of a child question.
 * @param stdClass $question
 * @return array
 */
function questionnaire_get_parent ($question) {
    global $DB;
    $qid = $question->id;
    $parent = array();
    $dependquestion = $DB->get_record('questionnaire_question', ['id' => $question->dependquestionid],
        'id, position, name, type_id');
    if (is_object($dependquestion)) {
        $qdependchoice = '';
        switch ($dependquestion->type_id) {
            case QUESRADIO:
            case QUESDROP:
            case QUESCHECK:
                $dependchoice = $DB->get_record('questionnaire_quest_choice', ['id' => $question->dependchoiceid], 'id,content');
                $qdependchoice = $dependchoice->id;
                $dependchoice = $dependchoice->content;

                $contents = questionnaire_choice_values($dependchoice);
                if ($contents->modname) {
                    $dependchoice = $contents->modname;
                }
                break;
            case QUESYESNO:
                switch ($question->dependchoiceid) {
                    case 0:
                        $dependchoice = get_string('yes');
                        $qdependchoice = 'y';
                        break;
                    case 1:
                        $dependchoice = get_string('no');
                        $qdependchoice = 'n';
                        break;
                }
                break;
        }
        // Qdependquestion, parenttype and qdependchoice fields to be used in preview mode.
        $parent[$qid]['qdependquestion'] = 'q'.$dependquestion->id;
        $parent[$qid]['qdependchoice'] = $qdependchoice;
        $parent[$qid]['parenttype'] = $dependquestion->type_id;
        // Other fields to be used in Questions edit mode.
        $parent[$qid]['position'] = $question->position;
        $parent[$qid]['name'] = $question->name;
        $parent[$qid]['content'] = $question->content;
        $parent[$qid]['parentposition'] = $dependquestion->position;
        $parent[$qid]['parent'] = format_string($dependquestion->name) . '->' . format_string ($dependchoice);
    }
    return $parent;
}

/**
 * Get parent position of all child questions in current questionnaire.
 * Use the parent with the largest position value.
 *
 * @param array $questions
 * @return array An array with Child-ID->Parentposition.
 */
function questionnaire_get_parent_positions ($questions) {
    $parentpositions = array();
    foreach ($questions as $question) {
        foreach ($question->dependencies as $dependency) {
            $dependquestion = $dependency->dependquestionid;
            if (isset($dependquestion) && $dependquestion != 0) {
                $childid = $question->id;
                $parentpos = $questions[$dependquestion]->position;

                if (!isset($parentpositions[$childid])) {
                    $parentpositions[$childid] = $parentpos;
                }
                if (isset ($parentpositions[$childid]) && $parentpos > $parentpositions[$childid]) {
                    $parentpositions[$childid] = $parentpos;
                }
            }
        }
    }
    return $parentpositions;
}

/**
 * Get child position of all parent questions in current questionnaire.
 * Use the child with the smallest position value.
 *
 * @param array $questions
 * @return array An array with Parent-ID->Childposition.
 */
function questionnaire_get_child_positions ($questions) {
    $childpositions = array();
    foreach ($questions as $question) {
        foreach ($question->dependencies as $dependency) {
            $dependquestion = $dependency->dependquestionid;
            if (isset($dependquestion) && $dependquestion != 0) {
                $parentid = $questions[$dependquestion]->id; // Equals $dependquestion?.
                $childpos = $question->position;

                if (!isset($childpositions[$parentid])) {
                    $childpositions[$parentid] = $childpos;
                }

                if (isset ($childpositions[$parentid]) && $childpos < $childpositions[$parentid]) {
                    $childpositions[$parentid] = $childpos;
                }
            }
        }
    }
    return $childpositions;
}

/**
 * Check that the needed page breaks are present to separate child questions.
 * @param stdClass $questionnaire
 * @return false|lang_string|string
 */
function questionnaire_check_page_breaks($questionnaire) {
    global $DB;
    $msg = '';
    // Store the new page breaks ids.
    $newpbids = array();
    $delpb = 0;
    $sid = $questionnaire->survey->id;
    $positions = array();
    if ($questions = $DB->get_records('questionnaire_question', ['surveyid' => $sid, 'deleted' => 'n'], 'position')) {
        foreach ($questions as $key => $qu) {
            $newqu = new stdClass();
            $newqu->question_id = $key;
            $newqu->type_id = $qu->type_id;
            $newqu->qname = $qu->name;
            $newqu->qpos = $qu->position;

            $dependencies = $DB->get_records('questionnaire_dependency', ['questionid' => $key, 'surveyid' => $sid],
                    'id ASC', 'id, dependquestionid, dependchoiceid, dependlogic');
            $newqu->dependencies = $dependencies ?? [];
            $positions[] = (array)$newqu;
        }
    }
    $count = count($positions);

    for ($i = $count - 1; $i >= 0; $i--) {
        $qu = $positions[$i];
        $questionnb = $i;
        $prevqu = null;
        $prevtypeid = null;
        if ($i > 0) {
            $prevqu = $positions[$i - 1];
            $prevtypeid = $prevqu['type_id'];
        }
        if ($qu['type_id'] == QUESPAGEBREAK) {
            $questionnb--;
            // If more than one consecutive page breaks, remove extra one(s).
            // Remove that extra page break in 1st position.
            if ($prevtypeid == QUESPAGEBREAK || $i == $count - 1 || $qu['qpos'] == 1) {
                $qid = $qu['question_id'];
                $delpb ++;
                $msg .= get_string("checkbreaksremoved", "questionnaire", $delpb).'<br />';
                // Need to reload questions.
                if ($questions = $DB->get_records('questionnaire_question', ['surveyid' => $sid, 'deleted' => 'n'],  'id')) {
                    $DB->set_field('questionnaire_question', 'deleted', 'y', ['id' => $qid, 'surveyid' => $sid]);
                    $select = 'surveyid = ' . $sid . ' AND deleted = \'n\' AND position > ' .
                            $questions[$qid]->position;
                    if ($records = $DB->get_records_select('questionnaire_question', $select, null, 'position ASC')) {
                        foreach ($records as $record) {
                            $DB->set_field('questionnaire_question', 'position', $record->position - 1, ['id' => $record->id]);
                        }
                    }
                }
            }
        }
        // Add pagebreak between question child and not dependent question that follows.
        if ($qu['type_id'] != QUESPAGEBREAK) {
            if ($prevqu) {
                $prevdependencies = $prevqu['dependencies'];
                $outerdependencies = count($qu['dependencies']) >= count($prevdependencies) ?
                    $qu['dependencies'] : $prevdependencies;
                $innerdependencies = count($qu['dependencies']) < count($prevdependencies) ?
                    $qu['dependencies'] : $prevdependencies;

                $okeys = [];
                $ikeys = [];
                foreach ($outerdependencies as $okey => $outerdependency) {
                    foreach ($innerdependencies as $ikey => $innerdependency) {
                        if ($outerdependency->dependquestionid === $innerdependency->dependquestionid &&
                                $outerdependency->dependchoiceid === $innerdependency->dependchoiceid &&
                                $outerdependency->dependlogic === $innerdependency->dependlogic) {
                            $okeys[] = $okey;
                            $ikeys[] = $ikey;
                        }
                    }
                }

                foreach ($okeys as $key) {
                    if (key_exists($key, $outerdependencies)) {
                        unset($outerdependencies[$key]);
                    }
                }
                foreach ($ikeys as $key) {
                    if (key_exists($key, $innerdependencies)) {
                        unset($innerdependencies[$key]);
                    }
                }

                $diffdependencies = count($outerdependencies) + count($innerdependencies);

                if (($prevtypeid != QUESPAGEBREAK && $diffdependencies != 0)
                        || (!isset($qu['dependencies']) && isset($prevdependencies))) {
                    $sql = 'SELECT MAX(position) as maxpos FROM {questionnaire_question} ' .
                        'WHERE surveyid = ' . $questionnaire->survey->id . ' AND deleted = \'n\'';
                    if ($record = $DB->get_record_sql($sql)) {
                        $pos = $record->maxpos + 1;
                    } else {
                        $pos = 1;
                    }
                    $question = new stdClass();
                    $question->surveyid = $questionnaire->survey->id;
                    $question->type_id = QUESPAGEBREAK;
                    $question->position = $pos;
                    $question->content = 'break';

                    if (!($newqid = $DB->insert_record('questionnaire_question', $question))) {
                        return (false);
                    }
                    $newpbids[] = $newqid;
                    $questionnaire = new questionnaire($course, $cm, $questionnaire->id, null);
                    $questionnaire->move_question($newqid, $qu['qpos']);
                }
            }
        }
    }
    if (empty($newpbids) && !$msg) {
        $msg = get_string('checkbreaksok', 'questionnaire');
    } else if ($newpbids) {
        $msg .= get_string('checkbreaksadded', 'questionnaire').'&nbsp;';
        $newpbids = array_reverse ($newpbids);
        $questionnaire = new questionnaire($course, $cm, $questionnaire->id, null);
        foreach ($newpbids as $newpbid) {
            $msg .= $questionnaire->questions[$newpbid]->position.'&nbsp;';
        }
    }
    return($msg);
}

/**
 * Code snippet used to set up the questionform.
 * @param stdClass $questionnaire
 * @param int $qid
 * @param int $qtype
 * @return mixed|\mod_questionnaire\question\question
 */
function questionnaire_prep_for_questionform($questionnaire, $qid, $qtype) {
    $context = context_module::instance($questionnaire->cm->id);
    if ($qid != 0) {
        $question = clone($questionnaire->questions[$qid]);
        $question->qid = $question->id;
        $question->sid = $questionnaire->survey->id;
        $question->id = $questionnaire->cm->id;
        $draftideditor = file_get_submitted_draft_itemid('question');
        $content = file_prepare_draft_area($draftideditor, $context->id, 'mod_questionnaire', 'question',
                                           $qid, array('subdirs' => true), $question->content);
        $question->content = array('text' => $content, 'format' => FORMAT_HTML, 'itemid' => $draftideditor);

        if (isset($question->dependencies)) {
            foreach ($question->dependencies as $dependencies) {
                if ($dependencies->dependandor === "and") {
                    $question->dependquestions_and[] = $dependencies->dependquestionid.','.$dependencies->dependchoiceid;
                    $question->dependlogic_and[] = $dependencies->dependlogic;
                } else if ($dependencies->dependandor === "or") {
                    $question->dependquestions_or[] = $dependencies->dependquestionid.','.$dependencies->dependchoiceid;
                    $question->dependlogic_or[] = $dependencies->dependlogic;
                }
            }
        }
    } else {
        $question = \mod_questionnaire\question\question::question_builder($qtype);
        $question->sid = $questionnaire->survey->id;
        $question->id = $questionnaire->cm->id;
        $question->type_id = $qtype;
        $question->type = '';
        $draftideditor = file_get_submitted_draft_itemid('question');
        $content = file_prepare_draft_area($draftideditor, $context->id, 'mod_questionnaire', 'question',
                                           null, array('subdirs' => true), '');
        $question->content = array('text' => $content, 'format' => FORMAT_HTML, 'itemid' => $draftideditor);
    }
    return $question;
}

/**
 * Get the standard page contructs and check for validity.
 * @param int $id The coursemodule id.
 * @param int $a  The module instance id.
 * @return array An array with the $cm, $course, and $questionnaire records in that order.
 */
function questionnaire_get_standard_page_items($id = null, $a = null) {
    global $DB;

    if ($id) {
        if (! $cm = get_coursemodule_from_id('questionnaire', $id)) {
            throw new \moodle_exception('invalidcoursemodule', 'mod_questionnaire');
        }

        if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
            throw new \moodle_exception('coursemisconf', 'mod_questionnaire');
        }

        if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $cm->instance))) {
            throw new \moodle_exception('invalidcoursemodule', 'mod_questionnaire');
        }

    } else {
        if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $a))) {
            throw new \moodle_exception('invalidcoursemodule', 'mod_questionnaire');
        }
        if (! $course = $DB->get_record("course", array("id" => $questionnaire->course))) {
            throw new \moodle_exception('coursemisconf', 'mod_questionnaire');
        }
        if (! $cm = get_coursemodule_from_instance("questionnaire", $questionnaire->id, $course->id)) {
            throw new \moodle_exception('invalidcoursemodule', 'mod_questionnaire');
        }
    }

    return (array($cm, $course, $questionnaire));
}
