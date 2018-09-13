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
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

class questionnaire {

    // Class Properties.

    /**
     * @var \mod_questionnaire\question\base[] $quesitons
     */
    public $questions = [];

    /**
     * The survey record.
     * @var object $survey
     */
     // Todo var $survey; TODO.

    /**
     * @var $renderer Contains the page renderer when loaded, or false if not.
     */
    public $renderer = false;

    /**
     * @var $page Contains the renderable, templatable page when loaded, or false if not.
     */
    public $page = false;

    // Class Methods.

    /*
     * The class constructor
     *
     */
    public function __construct($id = 0, $questionnaire = null, &$course, &$cm, $addquestions = true) {
        global $DB;

        if ($id) {
            $questionnaire = $DB->get_record('questionnaire', array('id' => $id));
        }

        if (is_object($questionnaire)) {
            $properties = get_object_vars($questionnaire);
            foreach ($properties as $property => $value) {
                $this->$property = $value;
            }
        }

        if (!empty($this->sid)) {
            $this->add_survey($this->sid);
        }

        $this->course = $course;
        $this->cm = $cm;
        // When we are creating a brand new questionnaire, we will not yet have a context.
        if (!empty($cm) && !empty($this->id)) {
            $this->context = context_module::instance($cm->id);
        } else {
            $this->context = null;
        }

        if ($addquestions && !empty($this->sid)) {
            $this->add_questions($this->sid);
        }

        // Load the capabilities for this user and questionnaire, if not creating a new one.
        if (!empty($this->cm->id)) {
            $this->capabilities = questionnaire_load_capabilities($this->cm->id);
        }
    }

    /**
     * Adding a survey record to the object.
     *
     */
    public function add_survey($sid = 0, $survey = null) {
        global $DB;

        if ($sid) {
            $this->survey = $DB->get_record('questionnaire_survey', array('id' => $sid));
        } else if (is_object($survey)) {
            $this->survey = clone($survey);
        }
    }

    /**
     * Adding questions to the object.
     */
    public function add_questions($sid = false) {
        global $DB;

        if ($sid === false) {
            $sid = $this->sid;
        }

        if (!isset($this->questions)) {
            $this->questions = [];
            $this->questionsbysec = [];
        }

        $select = 'survey_id = ? AND deleted = ?';
        $params = [$sid, 'n'];
        if ($records = $DB->get_records_select('questionnaire_question', $select, $params, 'position')) {
            $sec = 1;
            $isbreak = false;
            foreach ($records as $record) {

                $this->questions[$record->id] = \mod_questionnaire\question\base::question_builder($record->type_id,
                    $record, $this->context);

                if ($record->type_id != QUESPAGEBREAK) {
                    $this->questionsbysec[$sec][$record->id] = &$this->questions[$record->id];
                    $isbreak = false;
                } else {
                    // Sanity check: no section break allowed as first position, no 2 consecutive section breaks.
                    if ($record->position != 1 && $isbreak == false) {
                        $sec++;
                        $isbreak = true;
                    }
                }
            }
        }
    }

    /**
     * Add the renderer to the questionnaire object.
     * @param \plugin_renderer_base $renderer The module renderer, extended from core renderer.
     */
    public function add_renderer($renderer) {
        $this->renderer = $renderer;
    }

    /**
     * Add the templatable page to the questionnaire object.
     * @param \renderable, \templatable $page The page to rendere, implementing core classes.
     */
    public function add_page($page) {
        $this->page = $page;
    }

    public function view() {
        global $CFG, $USER, $PAGE;

        $PAGE->set_title(format_string($this->name));
        $PAGE->set_heading(format_string($this->course->fullname));

        // Initialise the JavaScript.
        $PAGE->requires->js_init_call('M.mod_questionnaire.init_attempt_form', null, false, questionnaire_get_js_module());

        $questionnaire = $this;

        if (!$this->capabilities->view) {
            $this->page->add_to_page('notifications',
                $this->renderer->notification(get_string('noteligible', 'questionnaire', $this->name),
                \core\output\notification::NOTIFY_ERROR));
        } else if (!$this->is_active()) {
            $this->page->add_to_page('notifications',
                $this->renderer->notification(get_string('notavail', 'questionnaire'), \core\output\notification::NOTIFY_ERROR));
        } else if (!$this->is_open()) {
            $this->page->add_to_page('notifications',
                $this->renderer->notification(get_string('notopen', 'questionnaire', userdate($this->opendate)),
                \core\output\notification::NOTIFY_ERROR));
        } else if ($this->is_closed()) {
            $this->page->add_to_page('notifications',
                $this->renderer->notification(get_string('closed', 'questionnaire', userdate($this->closedate)),
                \core\output\notification::NOTIFY_ERROR));
        } else if (!$this->user_is_eligible($USER->id)) {
            $this->page->add_to_page('notifications',
                $this->renderer->notification(get_string('noteligible', 'questionnaire'), \core\output\notification::NOTIFY_ERROR));
        } else if ($this->survey->realm == 'template') {
            $this->page->add_to_page('notifications',
                $this->renderer->notification(get_string('templatenotviewable', 'questionnaire'),
                \core\output\notification::NOTIFY_ERROR));
        } else if (!$this->user_can_take($USER->id)) {
            switch ($this->qtype) {
                case QUESTIONNAIREDAILY:
                    $msgstring = ' '.get_string('today', 'questionnaire');
                    break;
                case QUESTIONNAIREWEEKLY:
                    $msgstring = ' '.get_string('thisweek', 'questionnaire');
                    break;
                case QUESTIONNAIREMONTHLY:
                    $msgstring = ' '.get_string('thismonth', 'questionnaire');
                    break;
                default:
                    $msgstring = '';
                    break;
            }
            $this->page->add_to_page('notifications',
                $this->renderer->notification(get_string('alreadyfilled', 'questionnaire', $msgstring),
                \core\output\notification::NOTIFY_ERROR));
        } else {
            // Handle the main questionnaire completion page.
            $quser = $USER->id;

            $msg = $this->print_survey($USER->id, $quser);

            // If Questionnaire was submitted with all required fields completed ($msg is empty),
            // then record the submittal.
            $viewform = data_submitted($CFG->wwwroot."/mod/questionnaire/complete.php");
            if (!empty($viewform->rid)) {
                $viewform->rid = (int)$viewform->rid;
            }
            if (!empty($viewform->sec)) {
                $viewform->sec = (int)$viewform->sec;
            }
            if (data_submitted() && confirm_sesskey() && isset($viewform->submit) && isset($viewform->submittype) &&
                ($viewform->submittype == "Submit Survey") && empty($msg)) {
                $this->response_delete($viewform->rid, $viewform->sec);
                $this->rid = $this->response_insert($this->survey->id, $viewform->sec, $viewform->rid, $quser);
                $this->response_commit($this->rid);

                // If it was a previous save, rid is in the form...
                if (!empty($viewform->rid) && is_numeric($viewform->rid)) {
                    $rid = $viewform->rid;

                    // Otherwise its in this object.
                } else {
                    $rid = $this->rid;
                }

                questionnaire_record_submission($this, $USER->id, $rid);

                if ($this->grade != 0) {
                    $questionnaire = new stdClass();
                    $questionnaire->id = $this->id;
                    $questionnaire->name = $this->name;
                    $questionnaire->grade = $this->grade;
                    $questionnaire->cmidnumber = $this->cm->idnumber;
                    $questionnaire->courseid = $this->course->id;
                    questionnaire_update_grades($questionnaire, $quser);
                }

                // Update completion state.
                $completion = new completion_info($this->course);
                if ($completion->is_enabled($this->cm) && $this->completionsubmit) {
                    $completion->update_state($this->cm, COMPLETION_COMPLETE);
                }

                // Log this submitted response.
                $context = context_module::instance($this->cm->id);
                $anonymous = $this->respondenttype == 'anonymous';
                $params = array(
                                'context' => $context,
                                'courseid' => $this->course->id,
                                'relateduserid' => $USER->id,
                                'anonymous' => $anonymous,
                                'other' => array('questionnaireid' => $questionnaire->id)
                );
                $event = \mod_questionnaire\event\attempt_submitted::create($params);
                $event->trigger();

                $this->submission_notify($this->rid);
                $this->response_goto_thankyou();
            }
        }
    }

    /*
    * Function to view an entire responses data.
    *
    */
    public function view_response($rid, $referer= '', $blankquestionnaire = false, $resps = '', $compare = false,
                        $isgroupmember = false, $allresponses = false, $currentgroupid = 0) {
        $this->print_survey_start('', 1, 1, 0, $rid, false);

        $data = new stdClass();
        $i = 0;
        $this->response_import_all($rid, $data);
        if ($referer != 'print') {
            $feedbackmessages = $this->response_analysis($rid, $resps, $compare, $isgroupmember, $allresponses, $currentgroupid);

            if ($feedbackmessages) {
                $msgout = '';
                foreach ($feedbackmessages as $msg) {
                    $msgout .= $msg;
                }
                $this->page->add_to_page('feedbackmessages', $msgout);
            }

            if ($this->survey->feedbacknotes) {
                $text = file_rewrite_pluginfile_urls($this->survey->feedbacknotes, 'pluginfile.php',
                    $this->context->id, 'mod_questionnaire', 'feedbacknotes', $this->survey->id);
                $this->page->add_to_page('feedbacknotes', $this->renderer->box(format_text($text, FORMAT_HTML)));
            }
        }
        foreach ($this->questions as $question) {
            if ($question->type_id < QUESPAGEBREAK) {
                $i++;
            }
            if ($question->type_id != QUESPAGEBREAK) {
                $this->page->add_to_page('responses', $this->renderer->response_output($question, $data, $i));
            }
        }
    }

    /*
    * Function to view an entire responses data.
    *
    * $value is unused, but is needed in order to get the $key elements of the array. Suppress PHPMD warning.
    *
    * @SuppressWarnings(PHPMD.UnusedLocalVariable)
    */
    public function view_all_responses($resps) {
        $this->print_survey_start('', 1, 1, 0);

        // If a student's responses have been deleted by teacher while student was viewing the report,
        // then responses may have become empty, hence this test is necessary.
        if ($resps) {
            foreach ($resps as $resp) {
                $data[$resp->id] = new stdClass();
                $this->response_import_all($resp->id, $data[$resp->id]);
            }

            $i = 0;

            $allrespdata = [];
            foreach ($this->questions as $question) {
                if ($question->type_id < QUESPAGEBREAK) {
                    $i++;
                }
                $qid = preg_quote('q'.$question->id, '/');
                if ($question->type_id != QUESPAGEBREAK) {
                    $allrespdata[$i] = [];
                    $allrespdata[$i]['question'] = $question;
                    foreach ($data as $respid => $respdata) {
                        $hasresp = false;
                        foreach ($respdata as $key => $value) {
                            if ($hasresp = preg_match("/$qid(_|$)/", $key)) {
                                break;
                            }
                        }
                        // Do not display empty responses.
                        if ($hasresp) {
                            $allrespdata[$i][] = [
                                'respdate' => userdate($resps[$respid]->submitted),
                                'respdata' => $respdata
                            ];
                        }
                    }
                }
            }
            $this->page->add_to_page('responses', $this->renderer->all_response_output($allrespdata));
        } else {
            $this->page->add_to_page('responses', $this->renderer->all_response_output(get_string('noresponses', 'questionnaire')));
        }

        $this->print_survey_end(1, 1);
    }

    // Access Methods.
    public function is_active() {
        return (!empty($this->survey));
    }

    public function is_open() {
        return ($this->opendate > 0) ? ($this->opendate < time()) : true;
    }

    public function is_closed() {
        return ($this->closedate > 0) ? ($this->closedate < time()) : false;
    }

    public function user_can_take($userid) {

        if (!$this->is_active() || !$this->user_is_eligible($userid)) {
            return false;
        } else if ($this->qtype == QUESTIONNAIREUNLIMITED) {
            return true;
        } else if ($userid > 0) {
            return $this->user_time_for_new_attempt($userid);
        } else {
            return false;
        }
    }

    public function user_is_eligible($userid) {
        return ($this->capabilities->view && $this->capabilities->submit);
    }

    public function user_has_saved_response($userid) {
        global $DB;

        return $DB->record_exists('questionnaire_response',
            ['survey_id' => $this->survey->id, 'userid' => $userid, 'complete' => 'n']);
    }

    public function user_time_for_new_attempt($userid) {
        global $DB;

        $params = array('qid' => $this->id, 'userid' => $userid);
        if (!($attempts = $DB->get_records('questionnaire_attempts', $params, 'timemodified DESC'))) {
            return true;
        }

        $attempt = reset($attempts);
        $timenow = time();

        switch ($this->qtype) {

            case QUESTIONNAIREUNLIMITED:
                $cantake = true;
                break;

            case QUESTIONNAIREONCE:
                $cantake = false;
                break;

            case QUESTIONNAIREDAILY:
                $attemptyear = date('Y', $attempt->timemodified);
                $currentyear = date('Y', $timenow);
                $attemptdayofyear = date('z', $attempt->timemodified);
                $currentdayofyear = date('z', $timenow);
                $cantake = (($attemptyear < $currentyear) ||
                            (($attemptyear == $currentyear) && ($attemptdayofyear < $currentdayofyear)));
                break;

            case QUESTIONNAIREWEEKLY:
                $attemptyear = date('Y', $attempt->timemodified);
                $currentyear = date('Y', $timenow);
                $attemptweekofyear = date('W', $attempt->timemodified);
                $currentweekofyear = date('W', $timenow);
                $cantake = (($attemptyear < $currentyear) ||
                            (($attemptyear == $currentyear) && ($attemptweekofyear < $currentweekofyear)));
                break;

            case QUESTIONNAIREMONTHLY:
                $attemptyear = date('Y', $attempt->timemodified);
                $currentyear = date('Y', $timenow);
                $attemptmonthofyear = date('n', $attempt->timemodified);
                $currentmonthofyear = date('n', $timenow);
                $cantake = (($attemptyear < $currentyear) ||
                            (($attemptyear == $currentyear) && ($attemptmonthofyear < $currentmonthofyear)));
                break;

            default:
                $cantake = false;
                break;
        }

        return $cantake;
    }

    public function is_survey_owner() {
        return (!empty($this->survey->courseid) && ($this->course->id == $this->survey->courseid));
    }

    public function can_view_response($rid) {
        global $USER, $DB;

        if (!empty($rid)) {
            $response = $DB->get_record('questionnaire_response', array('id' => $rid));

            // If the response was not found, can't view it.
            if (empty($response)) {
                return false;
            }

            // If the response belongs to a different survey than this one, can't view it.
            if ($response->survey_id != $this->survey->id) {
                return false;
            }

            // If you can view all responses always, then you can view it.
            if ($this->capabilities->readallresponseanytime) {
                return true;
            }

            // If you are allowed to view this response for another user.
            // If resp_view is set to QUESTIONNAIRE_STUDENTVIEWRESPONSES_NEVER, then this will always be false.
            if ($this->capabilities->readallresponses &&
                ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_ALWAYS ||
                 ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENCLOSED && $this->is_closed()) ||
                 ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENANSWERED  && !$this->user_can_take($USER->id)))) {
                return true;
            }

             // If you can read your own response.
            if (($response->userid == $USER->id) && $this->capabilities->readownresponses &&
                            ($this->count_submissions($USER->id) > 0)) {
                return true;
            }

        } else {
            // If you can view all responses always, then you can view it.
            if ($this->capabilities->readallresponseanytime) {
                return true;
            }

            // If you are allowed to view this response for another user.
            // If resp_view is set to QUESTIONNAIRE_STUDENTVIEWRESPONSES_NEVER, then this will always be false.
            if ($this->capabilities->readallresponses &&
                ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_ALWAYS ||
                 ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENCLOSED && $this->is_closed()) ||
                 ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENANSWERED  && !$this->user_can_take($USER->id)))) {
                return true;
            }

             // If you can read your own response.
            if ($this->capabilities->readownresponses && ($this->count_submissions($USER->id) > 0)) {
                return true;
            }
        }
    }

    public function can_view_all_responses($usernumresp = null) {
        global $USER, $DB, $SESSION;

        if ($owner = $DB->get_field('questionnaire_survey', 'courseid', ['id' => $this->sid])) {
            $owner = ($owner == $this->course->id);
        } else {
            $owner = true;
        }
        $numresp = $this->count_submissions();
        if ($usernumresp === null) {
            $usernumresp = $this->count_submissions($USER->id);
        }

        // Number of Responses in currently selected group (or all participants etc.).
        if (isset($SESSION->questionnaire->numselectedresps)) {
            $numselectedresps = $SESSION->questionnaire->numselectedresps;
        } else {
            $numselectedresps = $numresp;
        }

        // If questionnaire is set to separate groups, prevent user who is not member of any group
        // to view All responses.
        $canviewgroups = true;
        $groupmode = groups_get_activity_groupmode($this->cm, $this->course);
        if ($groupmode == 1) {
            $canviewgroups = groups_has_membership($this->cm, $USER->id);
        }

        $canviewallgroups = has_capability('moodle/site:accessallgroups', $this->context);
        return (( // Teacher or non-editing teacher (if can view all groups).
                 ($canviewallgroups ||
                  // Non-editing teacher (with canviewallgroups capability removed), if member of a group.
                  ($canviewgroups && $this->capabilities->readallresponseanytime)) &&
                 ($numresp > 0) && $owner && ($numselectedresps > 0)) ||
                ($this->capabilities->readallresponses && ($numresp > 0) && $canviewgroups &&
                 // If resp_view is set to QUESTIONNAIRE_STUDENTVIEWRESPONSES_NEVER, then this will always be false.
                 ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_ALWAYS ||
                  ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENCLOSED && $this->is_closed()) ||
                  ($this->resp_view == QUESTIONNAIRE_STUDENTVIEWRESPONSES_WHENANSWERED && ($usernumresp > 0))) &&
                 $this->is_survey_owner()));
    }

    public function count_submissions($userid=false) {
        global $DB;

        if (!$userid) {
            // Provide for groups setting.
            return $DB->count_records('questionnaire_response', array('survey_id' => $this->sid, 'complete' => 'y'));
        } else {
            return $DB->count_records('questionnaire_response', array('survey_id' => $this->sid, 'userid' => $userid,
                                      'complete' => 'y'));
        }
    }

    private function has_required($section = 0) {
        if (empty($this->questions)) {
            return false;
        } else if ($section <= 0) {
            foreach ($this->questions as $question) {
                if ($question->required()) {
                    return true;
                }
            }
        } else {
            foreach ($this->questionsbysec[$section] as $question) {
                if ($question->required()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if current questionnaire has dependencies set and any question has dependencies.
     *
     * @return boolean Whether dependencies are set or not.
     */
    public function has_dependencies() {
        $hasdependencies = false;
        if (($this->navigate > 0) && isset($this->questions) && !empty($this->questions)) {
            foreach ($this->questions as $question) {
                if ($question->has_dependencies()) {
                    $hasdependencies = true;
                    break;
                }
            }
        }
        return $hasdependencies;
    }

    /**
     * @param $questionid
     * @return array
     */
    public function get_all_dependants($questionid) {
        $directids = $this->get_dependants($questionid);
        $directs = [];
        $indirects = [];
        foreach ($directids as $directid) {
            $this->load_parents($this->questions[$directid]);
            $indirectids = $this->get_dependants($directid);
            foreach ($this->questions[$directid]->dependencies as $dep) {
                if ($dep->dependquestionid == $questionid) {
                    $directs[$directid][] = $dep;
                }
            }
            foreach ($indirectids as $indirectid) {
                $this->load_parents($this->questions[$indirectid]);
                foreach ($this->questions[$indirectid]->dependencies as $dep) {
                    if ($dep->dependquestionid != $questionid) {
                        $indirects[$indirectid][] = $dep;
                    }
                }
            }
        }
        $alldependants = new stdClass();
        $alldependants->directs = $directs;
        $alldependants->indirects = $indirects;
        return($alldependants);
    }

    /**
     * @param $questionid
     * @return array
     */
    public function get_dependants($questionid) {
        $qu = [];
        // Create an array which shows for every question the child-IDs.
        foreach ($this->questions as $question) {
            if ($question->has_dependencies()) {
                foreach ($question->dependencies as $dependency) {
                    if (($dependency->dependquestionid == $questionid) && !in_array($question->id, $qu)) {
                        $qu[] = $question->id;
                    }
                }
            }
        }
        return($qu);
    }

    /**
     * Function to sort descendants array in get_dependants function.
     * @param $a
     * @param $b
     * @return int
     */
    private static function cmp($a, $b) {
        if ($a == $b) {
            return 0;
        } else if ($a < $b) {
            return -1;
        } else {
            return 1;
        }
    }

    /**
     * Get all descendants and choices for questions with descendants.
     * @return array
     */
    public function get_dependants_and_choices() {
        $questions = array_reverse($this->questions, true);
        $parents = [];
        foreach ($questions as $question) {
            foreach ($question->dependencies as $dependency) {
                $child = new stdClass();
                $child->choiceid = $dependency->dependchoiceid;
                $child->logic = $dependency->dependlogic;
                $child->andor = $dependency->dependandor;
                $parents[$dependency->dependquestionid][$question->id][] = $child;
            }
        }
        return($parents);
    }

    /**
     * Load needed parent question information into the dependencies structure for the requested question.
     * @param $question
     * @return bool
     */
    public function load_parents($question) {
        foreach ($question->dependencies as $did => $dependency) {
            $dependquestion = $this->questions[$dependency->dependquestionid];
            $qdependchoice = '';
            switch ($dependquestion->type_id) {
                case QUESRADIO:
                case QUESDROP:
                case QUESCHECK:
                    $qdependchoice = $dependency->dependchoiceid;
                    $dependchoice = $dependquestion->choices[$dependency->dependchoiceid]->content;

                    $contents = questionnaire_choice_values($dependchoice);
                    if ($contents->modname) {
                        $dependchoice = $contents->modname;
                    }
                    break;
                case QUESYESNO:
                    switch ($dependency->dependchoiceid) {
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
            $question->dependencies[$did]->qdependquestion = 'q'.$dependquestion->id;
            $question->dependencies[$did]->qdependchoice = $qdependchoice;
            $question->dependencies[$did]->parenttype = $dependquestion->type_id;
            // Other fields to be used in Questions edit mode.
            $question->dependencies[$did]->position = $question->position;
            $question->dependencies[$did]->name = $question->name;
            $question->dependencies[$did]->content = $question->content;
            $question->dependencies[$did]->parentposition = $dependquestion->position;
            $question->dependencies[$did]->parent = $dependquestion->name.'->'.$dependchoice;
        }
        return true;
    }

    /**
     * Are there any eligible questions to be displayed on the specified page/section.
     * @param $secnum The section number to check.
     * @param $rid The current response id.
     * @return boolean
     */
    public function eligible_questions_on_page($secnum, $rid) {
        $questionstodisplay = false;

        foreach ($this->questionsbysec[$secnum] as $question) {
            if ($question->dependency_fulfilled($rid, $this->questions)) {
                $questionstodisplay = true;
                break;
            }
        }
        return $questionstodisplay;
    }

    // Display Methods.

    public function print_survey($userid=false, $quser) {
        global $SESSION, $CFG;

        $formdata = new stdClass();
        if (data_submitted() && confirm_sesskey()) {
            $formdata = data_submitted();
        }
        $formdata->rid = $this->get_response($quser);
        // If student saved a "resume" questionnaire OR left a questionnaire unfinished
        // and there are more pages than one find the page of the last answered question.
        if (!empty($formdata->rid) && (empty($formdata->sec) || intval($formdata->sec) < 1)) {
            $formdata->sec = $this->response_select_max_sec($formdata->rid);
        }
        if (empty($formdata->sec)) {
            $formdata->sec = 1;
        } else {
            $formdata->sec = (intval($formdata->sec) > 0) ? intval($formdata->sec) : 1;
        }

        $numsections = isset($this->questionsbysec) ? count($this->questionsbysec) : 0;    // Indexed by section.
        $msg = '';
        $action = $CFG->wwwroot.'/mod/questionnaire/complete.php?id='.$this->cm->id;

        // TODO - Need to rework this. Too much crossover with ->view method.

        // Skip logic :: if this is page 1, it cannot be the end page with no questions on it!
        if ($formdata->sec == 1) {
            $SESSION->questionnaire->end = false;
        }

        if (!empty($formdata->submit)) {
            // Skip logic: we have reached the last page without any questions on it.
            if (isset($SESSION->questionnaire->end) && $SESSION->questionnaire->end == true) {
                return;
            }

            $msg = $this->response_check_format($formdata->sec, $formdata);
            if (empty($msg)) {
                return;
            }
        }

        if (!empty($formdata->resume) && ($this->resume)) {
            $this->response_delete($formdata->rid, $formdata->sec);
            $formdata->rid = $this->response_insert($this->survey->id, $formdata->sec, $formdata->rid, $quser, $resume = true);
            $this->response_goto_saved($action);
            return;
        }

        // Save each section 's $formdata somewhere in case user returns to that page when navigating the questionnaire.
        if (!empty($formdata->next)) {
            $msg = $this->response_check_format($formdata->sec, $formdata);
            if ($msg) {
                $formdata->next = '';
            } else {
                $this->response_delete($formdata->rid, $formdata->sec);
                $formdata->rid = $this->response_insert($this->survey->id, $formdata->sec, $formdata->rid, $quser);
                // Skip logic.
                $formdata->sec++;
                if ($this->has_dependencies()) {
                    while (!$this->eligible_questions_on_page($formdata->sec, $formdata->rid)) {
                        $this->response_delete($formdata->rid, $formdata->sec);
                        $formdata->sec++;
                        // We have reached the end of questionnaire on a page without any question left.
                        if ($formdata->sec > $numsections) {
                            $SESSION->questionnaire->end = true; // End of questionnaire reached on a no questions page.
                            break;
                        }
                    }
                }
            }
        }

        if (!empty($formdata->prev)) {
            // If skip logic and this is last page reached with no questions,
            // unlock questionnaire->end to allow navigate back to previous page.
            if (isset($SESSION->questionnaire->end) && ($SESSION->questionnaire->end == true)) {
                $SESSION->questionnaire->end = false;
                $formdata->sec--;
            }

            // Prevent navigation to previous page if wrong format in answered questions).
            $msg = $this->response_check_format($formdata->sec, $formdata, $checkmissing = false, $checkwrongformat = true);
            if ($msg) {
                $formdata->prev = '';
            } else {
                $this->response_delete($formdata->rid, $formdata->sec);
                $formdata->rid = $this->response_insert($this->survey->id, $formdata->sec, $formdata->rid, $quser);
                $formdata->sec--;
                // Skip logic.
                if ($this->has_dependencies()) {
                    while (($formdata->sec > 0) && !$this->eligible_questions_on_page($formdata->sec, $formdata->rid)) {
                        $formdata->sec--;
                    }
                }
            }
        }

        if (!empty($formdata->rid)) {
            $this->response_import_sec($formdata->rid, $formdata->sec, $formdata);
        }

        $formdatareferer = !empty($formdata->referer) ? htmlspecialchars($formdata->referer) : '';
        $formdatarid = isset($formdata->rid) ? $formdata->rid : '0';
        $this->page->add_to_page('formstart', $this->renderer->complete_formstart($action, ['referer' => $formdatareferer,
            'a' => $this->id, 'sid' => $this->survey->id, 'rid' => $formdatarid, 'sec' => $formdata->sec, 'sesskey' => sesskey()]));
        if (isset($this->questions) && $numsections) { // Sanity check.
            $this->survey_render($formdata->sec, $msg, $formdata);
            $controlbuttons = [];
            if ($formdata->sec > 1) {
                $controlbuttons['prev'] = ['type' => 'submit', 'value' => '<< '.get_string('previouspage', 'questionnaire')];
            }
            if ($this->resume) {
                $controlbuttons['resume'] = ['type' => 'submit', 'value' => get_string('save', 'questionnaire')];
            }

            // Add a 'hidden' variable for the mod's 'view.php', and use a language variable for the submit button.

            if ($formdata->sec == $numsections) {
                $controlbuttons['submittype'] = ['type' => 'hidden', 'value' => 'Submit Survey'];
                $controlbuttons['submit'] = ['type' => 'submit', 'value' => get_string('submitsurvey', 'questionnaire')];
            } else {
                $controlbuttons['next'] = ['type' => 'submit', 'value' => get_string('nextpage', 'questionnaire').' >>'];
            }
            $this->page->add_to_page('controlbuttons', $this->renderer->complete_controlbuttons($controlbuttons));
        } else {
            $this->page->add_to_page('controlbuttons',
                $this->renderer->complete_controlbuttons(get_string('noneinuse', 'questionnaire')));
        }
        $this->page->add_to_page('formend', $this->renderer->complete_formend());

        return $msg;
    }

    private function survey_render($section = 1, $message = '', &$formdata) {

        $this->usehtmleditor = null;

        if (empty($section)) {
            $section = 1;
        }
        $numsections = isset($this->questionsbysec) ? count($this->questionsbysec) : 0;
        if ($section > $numsections) {
            $formdata->sec = $numsections;
            $this->page->add_to_page('notifications',
                $this->renderer->notification(get_string('finished', 'questionnaire'), \core\output\notification::NOTIFY_WARNING));
            return(false);  // Invalid section.
        }

        // Check to see if there are required questions.
        $hasrequired = $this->has_required($section);

        // Find out what question number we are on $i New fix for question numbering.
        $i = 0;
        if ($section > 1) {
            for ($j = 2; $j <= $section; $j++) {
                foreach ($this->questionsbysec[$j - 1] as $question) {
                    if ($question->type_id < QUESPAGEBREAK) {
                        $i++;
                    }
                }
            }
        }

        $this->print_survey_start($message, $section, $numsections, $hasrequired, '', 1);
        foreach ($this->questionsbysec[$section] as $question) {
            if ($question->type_id != QUESSECTIONTEXT) {
                $i++;
            }
            // Need questionnaire id to get the questionnaire object in sectiontext (Label) question class.
            $formdata->questionnaire_id = $this->id;
            $this->page->add_to_page('questions',
                $this->renderer->question_output($question, $formdata, [], $i, $this->usehtmleditor));
        }

        $this->print_survey_end($section, $numsections);

        return;
    }

    private function print_survey_start($message, $section, $numsections, $hasrequired, $rid='', $blankquestionnaire=false) {
        global $CFG, $DB;
        require_once($CFG->libdir.'/filelib.php');

        $userid = '';
        $resp = '';
        $groupname = '';
        $currentgroupid = 0;
        $timesubmitted = '';
        // Available group modes (0 = no groups; 1 = separate groups; 2 = visible groups).
        if ($rid) {
            $courseid = $this->course->id;
            if ($resp = $DB->get_record('questionnaire_response', array('id' => $rid)) ) {
                if ($this->respondenttype == 'fullname') {
                    $userid = $resp->userid;
                    // Display name of group(s) that student belongs to... if questionnaire is set to Groups separate or visible.
                    if (groups_get_activity_groupmode($this->cm, $this->course)) {
                        if ($groups = groups_get_all_groups($courseid, $resp->userid)) {
                            if (count($groups) == 1) {
                                $group = current($groups);
                                $currentgroupid = $group->id;
                                $groupname = ' ('.get_string('group').': '.$group->name.')';
                            } else {
                                $groupname = ' ('.get_string('groups').': ';
                                foreach ($groups as $group) {
                                    $groupname .= $group->name.', ';
                                }
                                $groupname = substr($groupname, 0, strlen($groupname) - 2).')';
                            }
                        } else {
                            $groupname = ' ('.get_string('groupnonmembers').')';
                        }
                    }

                    $params = array(
                                    'objectid' => $this->survey->id,
                                    'context' => $this->context,
                                    'courseid' => $this->course->id,
                                    'relateduserid' => $userid,
                                    'other' => array('action' => 'vresp', 'currentgroupid' => $currentgroupid, 'rid' => $rid)
                    );
                    $event = \mod_questionnaire\event\response_viewed::create($params);
                    $event->trigger();
                }
            }
        }
        $ruser = '';
        if ($resp && !$blankquestionnaire) {
            if ($userid) {
                if ($user = $DB->get_record('user', array('id' => $userid))) {
                    $ruser = fullname($user);
                }
            }
            if ($this->respondenttype == 'anonymous') {
                $ruser = '- '.get_string('anonymous', 'questionnaire').' -';
            } else {
                // JR DEV comment following line out if you do NOT want time submitted displayed in Anonymous surveys.
                if ($resp->submitted) {
                    $timesubmitted = '&nbsp;'.get_string('submitted', 'questionnaire').'&nbsp;'.userdate($resp->submitted);
                }
            }
        }
        if ($ruser) {
            $respinfo = get_string('respondent', 'questionnaire').': <strong>'.$ruser.'</strong>';
            if ($this->survey->realm == 'public') {
                // For a public questionnaire, look for the course that used it.
                $coursename = '';
                $sql = 'SELECT q.id, q.course, c.fullname '.
                       'FROM {questionnaire} q, {questionnaire_attempts} qa, {course} c '.
                       'WHERE qa.rid = ? AND q.id = qa.qid AND c.id = q.course';
                if ($record = $DB->get_record_sql($sql, array($rid))) {
                    $coursename = $record->fullname;
                }
                $respinfo .= ' '.get_string('course'). ': '.$coursename;
            }
            $respinfo .= $groupname;
            $respinfo .= $timesubmitted;
            $this->page->add_to_page('respondentinfo', $this->renderer->respondent_info($respinfo));
        }

        // We don't want to display the print icon in the print popup window itself!
        if ($this->capabilities->printblank && $blankquestionnaire && $section == 1) {
            // Open print friendly as popup window.
            $linkname = '&nbsp;'.get_string('printblank', 'questionnaire');
            $title = get_string('printblanktooltip', 'questionnaire');
            $url = '/mod/questionnaire/print.php?qid='.$this->id.'&amp;rid=0&amp;'.'courseid='.$this->course->id.'&amp;sec=1';
            $options = array('menubar' => true, 'location' => false, 'scrollbars' => true, 'resizable' => true,
                    'height' => 600, 'width' => 800, 'title' => $title);
            $name = 'popup';
            $link = new moodle_url($url);
            $action = new popup_action('click', $link, $name, $options);
            $class = "floatprinticon";
            $this->page->add_to_page('printblank',
                $this->renderer->action_link($link, $linkname, $action, array('class' => $class, 'title' => $title),
                new pix_icon('t/print', $title)));
        }
        if ($section == 1) {
            if (!empty($this->survey->title)) {
                $this->page->add_to_page('title', clean_text($this->survey->title, FORMAT_HTML));
            }
            if (!empty($this->survey->subtitle)) {
                $this->page->add_to_page('subtitle', clean_text($this->survey->subtitle, FORMAT_HTML));
            }
            if ($this->survey->info) {
                $infotext = file_rewrite_pluginfile_urls($this->survey->info, 'pluginfile.php',
                                $this->context->id, 'mod_questionnaire', 'info', $this->survey->id);
                $this->page->add_to_page('addinfo', $infotext);
            }
        }

        if ($message) {
            $this->page->add_to_page('message', $this->renderer->notification($message, \core\output\notification::NOTIFY_ERROR));
        }
    }

    private function print_survey_end($section, $numsections) {
        $autonum = $this->autonum;
        // If no questions autonumbering.
        if ($autonum < 3) {
            return;
        }
        if ($numsections > 1) {
            $a = new stdClass();
            $a->page = $section;
            $a->totpages = $numsections;
            $this->page->add_to_page('pageinfo',
                $this->renderer->container(get_string('pageof', 'questionnaire', $a).'&nbsp;&nbsp;', 'surveyPage'));
        }
    }

    // Blankquestionnaire : if we are printing a blank questionnaire.
    public function survey_print_render($message = '', $referer='', $courseid, $rid=0, $blankquestionnaire=false) {
        global $DB, $CFG;

        if (! $course = $DB->get_record("course", array("id" => $courseid))) {
            print_error('incorrectcourseid', 'questionnaire');
        }

        $this->course = $course;

        if (!empty($rid)) {
            // If we're viewing a response, use this method.
            $this->view_response($rid, $referer, $blankquestionnaire);
            return;
        }

        if (empty($section)) {
            $section = 1;
        }

        if (isset($this->questionsbysec)) {
            $numsections = count($this->questionsbysec);
        } else {
            $numsections = 0;
        }

        if ($section > $numsections) {
            return(false);  // Invalid section.
        }

        $hasrequired = $this->has_required();

        // Find out what question number we are on $i.
        $i = 1;
        for ($j = 2; $j <= $section; $j++) {
            $i += count($this->questionsbysec[$j - 1]);
        }

        $action = $CFG->wwwroot.'/mod/questionnaire/preview.php?id='.$this->cm->id;
        $this->page->add_to_page('formstart',
            $this->renderer->complete_formstart($action));
        // Print all sections.
        $formdata = new stdClass();
        $errors = 1;
        if (data_submitted()) {
            $formdata = data_submitted();
            $pageerror = '';
            $s = 1;
            $errors = 0;
            foreach ($this->questionsbysec as $section) {
                $errormessage = $this->response_check_format($s, $formdata);
                if ($errormessage) {
                    if ($numsections > 1) {
                        $pageerror = get_string('page', 'questionnaire').' '.$s.' : ';
                    }
                    $this->page->add_to_page('notifications',
                        $this->renderer->notification($pageerror.$errormessage, \core\output\notification::NOTIFY_ERROR));
                    $errors++;
                }
                $s ++;
            }
        }

        $this->print_survey_start($message, $section = 1, 1, $hasrequired, $rid = '');

        if (($referer == 'preview') && $this->has_dependencies()) {
            $allqdependants = $this->get_dependants_and_choices();
        } else {
            $allqdependants = [];
        }
        if ($errors == 0) {
            $this->page->add_to_page('message',
                $this->renderer->notification(get_string('submitpreviewcorrect', 'questionnaire'),
                    \core\output\notification::NOTIFY_SUCCESS));
        }

        $page = 1;
        foreach ($this->questionsbysec as $section) {
            $output = '';
            if ($numsections > 1) {
                $output .= $this->renderer->print_preview_pagenumber(get_string('page', 'questionnaire').' '.$page);
                $page++;
            }
            foreach ($section as $question) {
                if ($question->type_id == QUESSECTIONTEXT) {
                    $i--;
                }
                if (isset($allqdependants[$question->id])) {
                    $dependants = $allqdependants[$question->id];
                } else {
                    $dependants = [];
                }
                $output .= $this->renderer->question_output($question, $formdata, $dependants, $i++, null);
                $this->page->add_to_page('questions', $output);
                $output = '';
            }
        }
        // End of questions.
        if ($referer == 'preview' && !$blankquestionnaire) {
            $url = $CFG->wwwroot.'/mod/questionnaire/preview.php?id='.$this->cm->id;
            $this->page->add_to_page('formend',
                $this->renderer->print_preview_formend($url, get_string('submitpreview', 'questionnaire'), get_string('reset')));
        }
        return;
    }

    public function survey_update($sdata) {
        global $DB;

        $errstr = ''; // TODO: notused!

        // New survey.
        if (empty($this->survey->id)) {
            // Create a new survey in the database.
            $fields = array('name', 'realm', 'title', 'subtitle', 'email', 'theme', 'thanks_page', 'thank_head',
                            'thank_body', 'feedbacknotes', 'info', 'feedbacksections', 'feedbackscores', 'chart_type');
            // Theme field deprecated.
            $record = new stdClass();
            $record->id = 0;
            $record->courseid = $sdata->courseid;
            foreach ($fields as $f) {
                if (isset($sdata->$f)) {
                    $record->$f = $sdata->$f;
                }
            }

            $this->survey = new stdClass();
            $this->survey->id = $DB->insert_record('questionnaire_survey', $record);
            $this->add_survey($this->survey->id);

            if (!$this->survey->id) {
                $errstr = get_string('errnewname', 'questionnaire') .' [ :  ]'; // TODO: notused!
                return(false);
            }
        } else {
            if (empty($sdata->name) || empty($sdata->title)
                    || empty($sdata->realm)) {
                return(false);
            }
            if (!isset($sdata->chart_type)) {
                $sdata->chart_type = '';
            }

            $fields = array('name', 'realm', 'title', 'subtitle', 'email', 'theme', 'thanks_page',
                    'thank_head', 'thank_body', 'feedbacknotes', 'info', 'feedbacksections', 'feedbackscores', 'chart_type');
            $name = $DB->get_field('questionnaire_survey', 'name', array('id' => $this->survey->id));

            // Trying to change survey name.
            if (trim($name) != trim(stripslashes($sdata->name))) {  // $sdata will already have slashes added to it.
                $count = $DB->count_records('questionnaire_survey', array('name' => $sdata->name));
                if ($count != 0) {
                    $errstr = get_string('errnewname', 'questionnaire');  // TODO: notused!
                    return(false);
                }
            }

            // UPDATE the row in the DB with current values.
            $surveyrecord = new stdClass();
            $surveyrecord->id = $this->survey->id;
            foreach ($fields as $f) {
                $surveyrecord->$f = trim($sdata->{$f});
            }

            $result = $DB->update_record('questionnaire_survey', $surveyrecord);
            if (!$result) {
                $errstr = get_string('warning', 'questionnaire').' [ :  ]';  // TODO: notused!
                return(false);
            }
        }

        return($this->survey->id);
    }

    /* Creates an editable copy of a survey. */
    public function survey_copy($owner) {
        global $DB;

        // Clear the sid, clear the creation date, change the name, and clear the status.
        $survey = clone($this->survey);

        unset($survey->id);
        $survey->courseid = $owner;
        // Make sure that the survey name is not larger than the field size (CONTRIB-2999). Leave room for extra chars.
        $survey->name = core_text::substr($survey->name, 0, (64 - 10));

        $survey->name .= '_copy';
        $survey->status = 0;

        // Check for 'name' conflict, and resolve.
        $i = 0;
        $name = $survey->name;
        while ($DB->count_records('questionnaire_survey', array('name' => $name)) > 0) {
            $name = $survey->name.(++$i);
        }
        if ($i) {
            $survey->name .= $i;
        }

        // Create new survey.
        if (!($newsid = $DB->insert_record('questionnaire_survey', $survey))) {
            return(false);
        }

        // Make copies of all the questions.
        $pos = 1;
        // Skip logic: some changes needed here for dependencies down below.
        $qidarray = array();
        $cidarray = array();
        foreach ($this->questions as $question) {
            // Fix some fields first.
            $oldid = $question->id;
            unset($question->id);
            $question->survey_id = $newsid;
            $question->position = $pos++;

            // Copy question to new survey.
            if (!($newqid = $DB->insert_record('questionnaire_question', $question))) {
                return(false);
            }
            $qidarray[$oldid] = $newqid;
            foreach ($question->choices as $key => $choice) {
                $oldcid = $key;
                unset($choice->id);
                $choice->question_id = $newqid;
                if (!$newcid = $DB->insert_record('questionnaire_quest_choice', $choice)) {
                    return(false);
                }
                $cidarray[$oldcid] = $newcid;
            }
        }

        // Replicate all dependency data.
        $dependquestions = $DB->get_records('questionnaire_dependency', ['surveyid' => $this->survey->id], 'questionid');
        foreach ($dependquestions as $dquestion) {
            $record = new stdClass();
            $record->questionid = $qidarray[$dquestion->questionid];
            $record->surveyid = $newsid;
            $record->dependquestionid = $qidarray[$dquestion->dependquestionid];
            // The response may not use choice id's (example boolean). If not, just copy the value.
            $response = $this->questions[$dquestion->dependquestionid]->response;
            if ($response->transform_choiceid($dquestion->dependchoiceid) == $dquestion->dependchoiceid) {
                $record->dependchoiceid = $cidarray[$dquestion->dependchoiceid];
            } else {
                $record->dependchoiceid = $dquestion->dependchoiceid;
            }
            $record->dependlogic = $dquestion->dependlogic;
            $record->dependandor = $dquestion->dependandor;
            $DB->insert_record('questionnaire_dependency', $record);
        }

        // Replicate any feedback data.
        // TODO: Need to handle image attachments (same for other copies above).
        $fbsections = $DB->get_records('questionnaire_fb_sections', ['survey_id' => $this->survey->id], 'id');
        foreach ($fbsections as $fbsid => $fbsection) {
            $fbsection->survey_id = $newsid;
            $scorecalculation = unserialize($fbsection->scorecalculation);
            $newscorecalculation = [];
            foreach ($scorecalculation as $qid => $val) {
                $newscorecalculation[$qidarray[$qid]] = $val;
            }
            $fbsection->scorecalculation = serialize($newscorecalculation);
            unset($fbsection->id);
            $newfbsid = $DB->insert_record('questionnaire_fb_sections', $fbsection);
            $feedbackrecs = $DB->get_records('questionnaire_feedback', ['section_id' => $fbsid], 'id');
            foreach ($feedbackrecs as $feedbackrec) {
                $feedbackrec->section_id = $newfbsid;
                unset($feedbackrec->id);
                $DB->insert_record('questionnaire_feedback', $feedbackrec);
            }
        }

        return($newsid);
    }

    // RESPONSE LIBRARY.

    private function response_check_format($section, $formdata, $checkmissing = true, $checkwrongformat = true) {
        $missing = 0;
        $strmissing = '';     // Missing questions.
        $wrongformat = 0;
        $strwrongformat = ''; // Wrongly formatted questions (Numeric, 5:Check Boxes, Date).
        $i = 1;
        for ($j = 2; $j <= $section; $j++) {
            // ADDED A SIMPLE LOOP FOR MAKING SURE PAGE BREAKS (type 99) AND LABELS (type 100) ARE NOT ALLOWED.
            foreach ($this->questionsbysec[$j - 1] as $sectionrecord) {
                $tid = $sectionrecord->type_id;
                if ($tid < QUESPAGEBREAK) {
                    $i++;
                }
            }
        }
        $qnum = $i - 1;

        foreach ($this->questionsbysec[$section] as $question) {
            $qid = $question->id;
            $tid = $question->type_id;
            $lid = $question->length;
            $pid = $question->precise;
            if ($tid != QUESSECTIONTEXT) {
                $qnum++;
            }
            if (!$question->response_complete($formdata)) {
                $missing++;
                $strmissing .= get_string('num', 'questionnaire').$qnum.'. ';
            }
            if (!$question->response_valid($formdata)) {
                $wrongformat++;
                $strwrongformat .= get_string('num', 'questionnaire').$qnum.'. ';
            }
        }
        $message = '';
        $nonumbering = false;
        $autonum = $this->autonum;
        // If no questions autonumbering do not display missing question(s) number(s).
        if ($autonum != 1 && $autonum != 3) {
            $nonumbering = true;
        }
        if ($checkmissing && $missing) {
            if ($nonumbering) {
                $strmissing = '';
            }
            if ($missing == 1) {
                $message = get_string('missingquestion', 'questionnaire').$strmissing;
            } else {
                $message = get_string('missingquestions', 'questionnaire').$strmissing;
            }
            if ($wrongformat) {
                $message .= '<br />';
            }
        }
        if ($checkwrongformat && $wrongformat) {
            if ($nonumbering) {
                $message .= get_string('wronganswers', 'questionnaire');
            } else {
                if ($wrongformat == 1) {
                    $message .= get_string('wrongformat', 'questionnaire').$strwrongformat;
                } else {
                    $message .= get_string('wrongformats', 'questionnaire').$strwrongformat;
                }
            }
        }
        return ($message);
    }

    private function response_delete($rid, $sec = null) {
        global $DB;

        if (empty($rid)) {
            return;
        }

        if ($sec != null) {
            if ($sec < 1) {
                return;
            }

            // Skip logic.
            $numsections = isset($this->questionsbysec) ? count($this->questionsbysec) : 0;
            $sec = min($numsections , $sec);

            /* get question_id's in this section */
            $qids = array();
            foreach ($this->questionsbysec[$sec] as $question) {
                $qids[] = $question->id;
            }
            if (empty($qids)) {
                return;
            } else {
                list($qsql, $params) = $DB->get_in_or_equal($qids);
                $qsql = ' AND question_id ' . $qsql;
            }

        } else {
            /* delete all */
            $qsql = '';
            $params = array();
        }

        /* delete values */
        $select = 'response_id = \'' . $rid . '\' ' . $qsql;
        foreach (array('response_bool', 'resp_single', 'resp_multiple', 'response_rank', 'response_text',
                       'response_other', 'response_date') as $tbl) {
            $DB->delete_records_select('questionnaire_'.$tbl, $select, $params);
        }
    }

    private function response_import_sec($rid, $sec, &$varr) {
        if ($sec < 1 || !isset($this->questionsbysec[$sec])) {
            return;
        }
        $vals = $this->response_select($rid, 'content');
        reset($vals);
        foreach ($vals as $id => $arr) {
            if (isset($arr[0]) && is_array($arr[0])) {
                // Multiple.
                $varr->{'q'.$id} = array_map('array_pop', $arr);
            } else {
                $varr->{'q'.$id} = array_pop($arr);
            }
        }
    }

    private function response_import_all($rid, &$varr) {

        $vals = $this->response_select($rid, 'content');
        reset($vals);
        foreach ($vals as $id => $arr) {
            if (strstr($id, '_') && isset($arr[4])) { // Single OR multiple with !other choice selected.
                $varr->{'q'.$id} = $arr[4];
            } else {
                if (isset($arr[0]) && is_array($arr[0])) { // Multiple.
                    $varr->{'q'.$id} = array_map('array_pop', $arr);
                } else { // Boolean, rate and other.
                    $varr->{'q'.$id} = array_pop($arr);
                }
            }
        }
    }

    private function response_commit($rid) {
        global $DB;

        $record = new stdClass();
        $record->id = $rid;
        $record->complete = 'y';
        $record->submitted = time();

        if ($this->grade < 0) {
            $record->grade = 1;  // Don't know what to do if its a scale...
        } else {
            $record->grade = $this->grade;
        }
        return $DB->update_record('questionnaire_response', $record);
    }

    private function get_response($userid, $rid = 0) {
        global $DB;

        $rid = intval($rid);
        if ($rid != 0) {
            // Check for valid rid.
            $fields = 'id, userid';
            $select = 'id = '.$rid.' AND survey_id = '.$this->sid.' AND userid = '.$userid.' AND complete = \'n\'';
            return ($DB->get_record_select('questionnaire_response', $select, null, $fields) !== false) ? $rid : '';

        } else {
            // Find latest in progress rid.
            $select = 'survey_id = '.$this->sid.' AND complete = \'n\' AND userid = '.$userid;
            if ($records = $DB->get_records_select('questionnaire_response', $select, null, 'submitted DESC',
                                              'id,survey_id', 0, 1)) {
                $rec = reset($records);
                return $rec->id;
            } else {
                return '';
            }
        }
    }

    // Returns the number of the section in which questions have been answered in a response.
    private function response_select_max_sec($rid) {
        global $DB;

        $pos = $this->response_select_max_pos($rid);
        $select = 'survey_id = ? AND type_id = ? AND position < ? AND deleted = ?';
        $params = [$this->sid, QUESPAGEBREAK, $pos, 'n'];
        $max = $DB->count_records_select('questionnaire_question', $select, $params) + 1;

        return $max;
    }

    // Returns the position of the last answered question in a response.
    private function response_select_max_pos($rid) {
        global $DB;

        $max = 0;

        foreach (array('response_bool', 'resp_single', 'resp_multiple', 'response_rank', 'response_text',
                       'response_other', 'response_date') as $tbl) {
            $sql = 'SELECT MAX(q.position) as num FROM {questionnaire_'.$tbl.'} a, {questionnaire_question} q '.
                   'WHERE a.response_id = ? AND '.
                   'q.id = a.question_id AND '.
                   'q.survey_id = ? AND '.
                   'q.deleted = \'n\'';
            if ($record = $DB->get_record_sql($sql, array($rid, $this->sid))) {
                $newmax = (int)$record->num;
                if ($newmax > $max) {
                    $max = $newmax;
                }
            }
        }
        return $max;
    }

    /**
     * Handle all submission notification actions.
     * @param int $rid The id of the response record.
     * @return boolean Operation success.
     *
     */
    private function submission_notify($rid) {
        global $DB;

        $success = true;

        if (isset($this->survey)) {
            if (isset($this->survey->email)) {
                $email = $this->survey->email;
            } else {
                $email = $DB->get_field('questionnaire_survey', 'email', ['id' => $this->survey->id]);
            }
        } else {
            $email = '';
        }

        if (!empty($email)) {
            $success = $this->response_send_email($rid, $email);
        }

        if (!empty($this->notifications)) {
            // Handle notification of submissions.
            $success = $this->send_submission_notifications($rid) && $success;
        }

        return $success;
    }

    /**
     * Send submission notifications to users with "submissionnotification" capability.
     * @param int $rid The id of the response record.
     * @return boolean Operation success.
     *
     */
    private function send_submission_notifications($rid) {
        global $CFG, $USER;

        $answers = new stdClass();
        $this->response_import_all($rid, $answers);
        $message = '';

        if ($this->notifications == 2) {
            $message .= $this->get_full_submission_for_notifications($answers);
        }

        $success = true;
        if ($notifyusers = $this->get_notifiable_users($USER->id)) {
            $info = new stdClass();
            // Need to handle user differently for anonymous surveys.
            if ($this->respondenttype != 'anonymous') {
                $info->userfrom = $USER;
                $info->username = fullname($info->userfrom, true);
                $info->profileurl = $CFG->wwwroot.'/user/view.php?id='.$info->userfrom->id.'&course='.$this->course->id;
                $langstringtext = 'submissionnotificationtextuser';
                $langstringhtml = 'submissionnotificationhtmluser';
            } else {
                $info->userfrom = \core_user::get_noreply_user();
                $info->username = '';
                $info->profileurl = '';
                $langstringtext = 'submissionnotificationtextanon';
                $langstringhtml = 'submissionnotificationhtmlanon';
            }
            $info->name = format_string($this->name);
            $info->submissionurl = $CFG->wwwroot.'/mod/questionnaire/report.php?action=vresp&sid='.$this->survey->id.
                    '&rid='.$rid.'&instance='.$this->id;
            $info->coursename = $this->course->fullname;

            $info->postsubject = get_string('submissionnotificationsubject', 'questionnaire');
            $info->posttext = get_string($langstringtext, 'questionnaire', $info);
            $info->posthtml = '<p>' . get_string($langstringhtml, 'questionnaire', $info) . '</p>';
            if (!empty($message)) {
                $info->posttext .= html_to_text($message);
                $info->posthtml .= $message;
            }

            foreach ($notifyusers as $notifyuser) {
                $info->userto = $notifyuser;
                $this->send_message($info, 'notification');
            }
        }

        return $success;
    }

    /**
     * Message someone about something.
     *
     * @param object $info The information for the message.
     * @param string $eventtype
     * @return void
     */
    private function send_message($info, $eventtype) {
        global $USER;

        $eventdata = new \core\message\message();
        $eventdata->courseid         = $this->course->id;
        $eventdata->modulename       = 'questionnaire';
        $eventdata->userfrom         = $info->userfrom;
        $eventdata->userto           = $info->userto;
        $eventdata->subject          = $info->postsubject;
        $eventdata->fullmessage      = $info->posttext;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml  = $info->posthtml;
        $eventdata->smallmessage     = $info->postsubject;

        $eventdata->name            = $eventtype;
        $eventdata->component       = 'mod_questionnaire';
        $eventdata->notification    = 1;
        $eventdata->contexturl      = $info->submissionurl;
        $eventdata->contexturlname  = $info->name;

        message_send($eventdata);
    }

    /**
     * Returns a list of users that should receive notification about given submission.
     *
     * @param int $userid The submission to grade
     * @return array
     */
    protected function get_notifiable_users($userid) {
        // Potential users should be active users only.
        $potentialusers = get_enrolled_users($this->context, 'mod/questionnaire:submissionnotification',
            null, 'u.*', null, null, null, true);

        $notifiableusers = [];
        if (groups_get_activity_groupmode($this->cm) == SEPARATEGROUPS) {
            if ($groups = groups_get_all_groups($this->course->id, $userid, $this->cm->groupingid)) {
                foreach ($groups as $group) {
                    foreach ($potentialusers as $potentialuser) {
                        if ($potentialuser->id == $userid) {
                            // Do not send self.
                            continue;
                        }
                        if (groups_is_member($group->id, $potentialuser->id)) {
                            $notifiableusers[$potentialuser->id] = $potentialuser;
                        }
                    }
                }
            } else {
                // User not in group, try to find graders without group.
                foreach ($potentialusers as $potentialuser) {
                    if ($potentialuser->id == $userid) {
                        // Do not send self.
                        continue;
                    }
                    if (!groups_has_membership($this->cm, $potentialuser->id)) {
                        $notifiableusers[$potentialuser->id] = $potentialuser;
                    }
                }
            }
        } else {
            foreach ($potentialusers as $potentialuser) {
                if ($potentialuser->id == $userid) {
                    // Do not send self.
                    continue;
                }
                $notifiableusers[$potentialuser->id] = $potentialuser;
            }
        }
        return $notifiableusers;
    }

    /**
     * Return a formatted string containing all the questions and answers for a specific submission.
     * @param $answers The array of answers from import_all_responses.
     * @return string
     * @throws coding_exception
     */
    private function get_full_submission_for_notifications($answers) {
        $responses = $this->get_full_submission_for_export($answers);
        $message = '';
        foreach ($responses as $response) {
            $message .= html_to_text($response->questionname) . "<br />\n";
            $message .= get_string('question') . ': ' . html_to_text($response->questiontext) . "<br />\n";
            $message .= get_string('answers', 'questionnaire') . ":<br />\n";
            foreach ($response->answers as $answer) {
                $message .= html_to_text($answer) . "<br />\n";
            }
            $message .= "<br />\n";
        }

        return $message;
    }

    /**
     * Construct the response data for a given response and return a structured export.
     * @param $rid
     * @return string
     * @throws coding_exception
     */
    public function get_structured_response($rid) {
        $answers = new stdClass();
        $this->response_import_all($rid, $answers);
        return $this->get_full_submission_for_export($answers);
    }

    /**
     * Return a JSON structure containing all the questions and answers for a specific submission.
     * @param $answers The array of answers from import_all_responses.
     * @return string
     * @throws coding_exception
     */
    private function get_full_submission_for_export($answers) {
        $exportstructure = [];
        foreach ($this->questions as $question) {
            $rqid = 'q' . $question->id;
            $response = new stdClass();
            $response->questionname = $question->position . '. ' . $question->name;
            $response->questiontext = $question->content;
            $response->answers = [];
            if ($question->type_id == 8) {
                $choices = [];
                $cids = [];
                foreach ($question->choices as $cid => $choice) {
                    if (!empty($choice->value)) {
                        $choices[$choice->value] = substr($choice->content, (strpos($choice->content, '=') + 1));
                    } else {
                        $cids[$rqid . '_' . $cid] = $choice->content;
                    }
                }
                foreach ($cids as $rqid => $choice) {
                    if (isset($answers->$rqid)) {
                        $cid = substr($rqid, (strpos($rqid, '_') + 1));
                        if (isset($question->choices[$cid]) && isset($choices[$answers->$rqid + 1])) {
                            $rating = $choices[$answers->$rqid + 1];
                        } else {
                            $rating = $answers->$rqid + 1;
                        }
                        $response->answers[] = $question->choices[$cid]->content . ' = ' . $rating;
                    }
                }
            } else if ($question->has_choices()) {
                // Check for "other".
                foreach ($question->choices as $cid => $choice) {
                    if (strpos($choice->content, '!other=') !== false) {
                        $other = $cid;
                        break;
                    }
                }
                $answertext = '';
                if (isset($answers->$rqid) && is_array($answers->$rqid)) {
                    $i = 0;
                    foreach ($answers->$rqid as $answer) {
                        if ($i > 0) {
                            $answertext .= '; ';
                        }
                        if (isset($other) && ($answer == ('other_' . $other))) {
                            $answertext .= $answers->{$rqid . '_' . $other};
                        } else {
                            $answertext .= $question->choices[$answer]->content;
                        }
                        $i++;
                    }
                } else if (isset($answers->$rqid) && isset($other) && ($answers->$rqid == ('other_' . $other))) {
                    $answertext .= $answers->{$rqid . '_' . $other};
                } else if (isset($answers->$rqid) && isset($question->choices[$answers->$rqid])) {
                    $answertext .= $question->choices[$answers->$rqid]->content;
                }
                $response->answers[] = $answertext;

            } else if (isset($answers->$rqid)) {
                $response->answers[] = $answers->$rqid;
            }
            $exportstructure[] = $response;
        }

        return $exportstructure;
    }

    /**
     * Format the submission answers for legacy email delivery.
     * @param array $answers The array of response answers.
     * @return array The formatted set of answers as plain text and HTML.
     */
    private function get_formatted_answers_for_emails($answers) {
        global $USER;

        // Line endings for html and plaintext emails.
        $endhtml = "\r\n<br />";
        $endplaintext = "\r\n";

        reset($answers);

        $formatted = array('plaintext' => '', 'html' => '');
        for ($i = 0; $i < count($answers[0]); $i++) {
            $sep = ' : ';

            switch($i) {
                case 1:
                    $sep = ' ';
                    break;
                case 4:
                    $formatted['plaintext'] .= get_string('user').' ';
                    $formatted['html'] .= get_string('user').' ';
                    break;
                case 6:
                    if ($this->respondenttype != 'anonymous') {
                        $formatted['html'] .= get_string('email').$sep.$USER->email. $endhtml;
                        $formatted['plaintext'] .= get_string('email'). $sep. $USER->email. $endplaintext;
                    }
            }
            $formatted['html'] .= $answers[0][$i].$sep.$answers[1][$i]. $endhtml;
            $formatted['plaintext'] .= $answers[0][$i].$sep.$answers[1][$i]. $endplaintext;
        }

        return $formatted;
    }

    /**
     * Send the full response submission to the defined email addresses.
     * @param int $rid The id of the response record.
     * @param string $email The comma separated list of emails to send to.
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     */
    private function response_send_email($rid, $email) {
        global $CFG, $USER;

        $submission = $this->generate_csv($rid, '', null, 1, 0);
        if (!empty($submission)) {
            $answers = $this->get_formatted_answers_for_emails($submission);
        } else {
            $answers = ['html' => '', 'plaintext' => ''];
        }

        $name = s($this->name);
        if (empty($email)) {
            return(false);
        }

        // Line endings for html and plaintext emails.
        $endhtml = "\r\n<br>";
        $endplaintext = "\r\n";

        $subject = get_string('surveyresponse', 'questionnaire') .": $name [$rid]";
        $url = $CFG->wwwroot.'/mod/questionnaire/report.php?action=vresp&amp;sid='.$this->survey->id.
                '&amp;rid='.$rid.'&amp;instance='.$this->id;

        // Html and plaintext body.
        $bodyhtml        = '<a href="'.$url.'">'.$url.'</a>'.$endhtml;
        $bodyplaintext   = $url.$endplaintext;
        $bodyhtml       .= get_string('surveyresponse', 'questionnaire') .' "'.$name.'"'.$endhtml;
        $bodyplaintext  .= get_string('surveyresponse', 'questionnaire') .' "'.$name.'"'.$endplaintext;

        $bodyhtml .= $answers['html'];
        $bodyplaintext .= $answers['plaintext'];

        // Use plaintext version for altbody.
        $altbody = "\n$bodyplaintext\n";

        $return = true;
        $mailaddresses = preg_split('/,|;/', $email);
        foreach ($mailaddresses as $email) {
            $userto = new stdClass();
            $userto->email = trim($email);
            $userto->mailformat = 1;
            // Dummy userid to keep email_to_user happy in moodle 2.6.
            $userto->id = -10;
            $userfrom = $CFG->noreplyaddress;
            if (email_to_user($userto, $userfrom, $subject, $altbody, $bodyhtml)) {
                $return = $return && true;
            } else {
                $return = false;
            }
        }
        return $return;
    }

    public function response_insert($sid, $section, $rid, $userid, $resume=false) {
        global $DB;

        $record = new stdClass();
        $record->submitted = time();

        if (empty($rid)) {
            // Create a uniqe id for this response.
            $record->survey_id = $sid;
            $record->userid = $userid;
            $rid = $DB->insert_record('questionnaire_response', $record);
        } else {
            $record->id = $rid;
            $DB->update_record('questionnaire_response', $record);
        }
        if ($resume) {
            // Log this saved response.
            // Needed for the event logging.
            $context = context_module::instance($this->cm->id);
            $anonymous = $this->respondenttype == 'anonymous';
            $params = array(
                            'context' => $context,
                            'courseid' => $this->course->id,
                            'relateduserid' => $userid,
                            'anonymous' => $anonymous,
                            'other' => array('questionnaireid' => $this->id)
            );
            $event = \mod_questionnaire\event\attempt_saved::create($params);
            $event->trigger();
        }

        if (!empty($this->questionsbysec[$section])) {
            foreach ($this->questionsbysec[$section] as $question) {
                // NOTE *** $val really should be a value obtained from the caller or somewhere else.
                // Note that "optional_param" accepting arrays is deprecated for optional_param_array.
                if ($question->responsetable == 'resp_multiple') {
                    $val = optional_param_array('q'.$question->id, '', PARAM_RAW);
                } else {
                    $val = optional_param('q'.$question->id, '', PARAM_RAW);
                }
                $question->insert_response($rid, $val);
            }
        }
        return($rid);
    }

    private function response_select($rid, $col = null, $csvexport = false, $choicecodes=0, $choicetext=1) {
        global $DB;

        $sid = $this->survey->id;
        if ($col == null) {
            $col = '';
        }
        if (!is_array($col) && !empty($col)) {
            $col = explode(',', preg_replace("/\s/", '', $col));
        }
        if (is_array($col) && count($col) > 0) {
            $callback = function($a) {
                return 'q.'.$a;
            };
            $col = ',' . implode(',', array_map($callback, $col));
        }

        // Response_bool (yes/no).
        $values = \mod_questionnaire\response\boolean::response_select($rid, $col, $csvexport, $choicecodes, $choicetext);

        // Response_single (radio button or dropdown).
        $values += \mod_questionnaire\response\single::response_select($rid, $col, $csvexport, $choicecodes, $choicetext);

        // Response_multiple.
        $values += \mod_questionnaire\response\multiple::response_select($rid, $col, $csvexport, $choicecodes, $choicetext);

        // Response_rank.
        $values += \mod_questionnaire\response\rank::response_select($rid, $col, $csvexport, $choicecodes, $choicetext);

        // Response_text.
        $values += \mod_questionnaire\response\text::response_select($rid, $col, $csvexport, $choicecodes, $choicetext);

        // Response_date.
        $values += \mod_questionnaire\response\date::response_select($rid, $col, $csvexport, $choicecodes, $choicetext);

        return($values);
    }

    private function response_goto_thankyou() {
        global $CFG, $USER, $DB;

        $select = 'id = '.$this->survey->id;
        $fields = 'thanks_page, thank_head, thank_body';
        if ($result = $DB->get_record_select('questionnaire_survey', $select, null, $fields)) {
            $thankurl = $result->thanks_page;
            $thankhead = $result->thank_head;
            $thankbody = $result->thank_body;
        } else {
            $thankurl = '';
            $thankhead = '';
            $thankbody = '';
        }
        if (!empty($thankurl)) {
            if (!headers_sent()) {
                header("Location: $thankurl");
                exit;
            }
            echo '
                <script language="JavaScript" type="text/javascript">
                <!--
                window.location="'.$thankurl.'"
                //-->
                </script>
                <noscript>
                <h2 class="thankhead">Thank You for completing this survey.</h2>
                <blockquote class="thankbody">Please click
                <a href="'.$thankurl.'">here</a> to continue.</blockquote>
                </noscript>
            ';
            exit;
        }
        if (empty($thankhead)) {
            $thankhead = get_string('thank_head', 'questionnaire');
        }
        $this->page->add_to_page('title', $thankhead);
        $this->page->add_to_page('addinfo',
            format_text(file_rewrite_pluginfile_urls($thankbody, 'pluginfile.php',
            $this->context->id, 'mod_questionnaire', 'thankbody', $this->survey->id), FORMAT_HTML, ['noclean' => true]));
        // Default set currentgroup to view all participants.
        // TODO why not set to current respondent's groupid (if any)?
        $currentgroupid = 0;
        $currentgroupid = groups_get_activity_group($this->cm);
        if (!groups_is_member($currentgroupid, $USER->id)) {
            $currentgroupid = 0;
        }
        if ($this->capabilities->readownresponses) {
            $this->page->add_to_page('message',
                ('<a href="'.$CFG->wwwroot.'/mod/questionnaire/myreport.php?id='.
                $this->cm->id.'&amp;instance='.$this->cm->instance.'&amp;user='.$USER->id.'&byresponse=0&action=vresp">'.
                get_string("continue").'</a>'));
        } else {
            $this->page->add_to_page('message',
                ('<a href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'">'.
                get_string("continue").'</a>'));
        }
        return;
    }

    private function response_goto_saved($url) {
        global $CFG;
        $resumesurvey = get_string('resumesurvey', 'questionnaire');
        $savedprogress = get_string('savedprogress', 'questionnaire', '<strong>'.$resumesurvey.'</strong>');

        $this->page->add_to_page('notifications',
            $this->renderer->notification($savedprogress, \core\output\notification::NOTIFY_SUCCESS));
        $this->page->add_to_page('respondentinfo',
            $this->renderer->homelink($CFG->wwwroot.'/course/view.php?id='.$this->course->id,
                get_string("backto", "moodle", $this->course->fullname)));
        return;
    }

    // Survey Results Methods.

    public function survey_results_navbar_alpha($currrid, $currentgroupid, $cm, $byresponse) {
        global $CFG, $DB;

        $output = '';

        // Is this questionnaire set to fullname or anonymous?
        $isfullname = $this->respondenttype != 'anonymous';
        if ($isfullname) {
            $selectgroupid = '';
            $gmuserid = ', GM.userid ';
            $groupmembers = ', {groups_members} GM ';
            switch ($currentgroupid) {
                case 0:     // All participants.
                    $gmuserid = '';
                    $groupmembers = '';
                    break;
                default:     // Members of a specific group.
                    $selectgroupid = ' AND GM.groupid='.$currentgroupid.' AND R.userid = GM.userid ';
            }
            $sql = 'SELECT R.id AS responseid, R.submitted AS submitted, R.userid, U.username AS username,
                            U.id as userid '.$gmuserid.
            'FROM {questionnaire_response} R,
                  {user} U
                '.$groupmembers.
            'WHERE R.survey_id=' . $this->survey->id . ' AND complete = \'y\' AND U.id = R.userid ' . $selectgroupid .
            'ORDER BY U.lastname, U.firstname, R.submitted DESC';
        } else {
            $sql = 'SELECT R.id AS responseid, R.submitted
                   FROM {questionnaire_response} R
                   WHERE R.survey_id = ?
                   AND complete = ?
                   ORDER BY R.submitted DESC';
        }
        if (!$responses = $DB->get_records_sql ($sql, array('survey_id' => $this->survey->id, 'complete' => 'y'))) {
            return;
        }
        $total = count($responses);
        if ($total === 0) {
            return;
        }
        $rids = array();
        if ($isfullname) {
            $ridssub = array();
            $ridsuserfullname = array();
            $ridsuserid = array();
        }
        $i = 0;
        $currpos = -1;
        foreach ($responses as $response) {
            array_push($rids, $response->responseid);
            if ($isfullname) {
                $user = $DB->get_record('user', array('id' => $response->userid));
                $userfullname = fullname($user);
                array_push($ridssub, $response->submitted);
                array_push($ridsuserfullname, fullname($user));
                array_push($ridsuserid, $response->userid);
            }
            if ($response->responseid == $currrid) {
                $currpos = $i;
            }
            $i++;
        }

        $url = $CFG->wwwroot.'/mod/questionnaire/report.php?action=vresp&group='.$currentgroupid.'&individualresponse=1';
        if (!$byresponse) {     // Display navbar.
            // Build navbar.
            $navbar = new \stdClass();
            $prevrid = ($currpos > 0) ? $rids[$currpos - 1] : null;
            $nextrid = ($currpos < $total - 1) ? $rids[$currpos + 1] : null;
            $firstrid = $rids[0];
            $lastrid = $rids[$total - 1];
            $displaypos = 1;
            if ($prevrid != null) {
                $pos = $currpos - 1;
                $title = '';
                $firstuserfullname = '';
                $navbar->firstrespondent = ['url' => ($url.'&rid='.$firstrid)];
                $navbar->previous = ['url' => ($url.'&rid='.$prevrid)];
                if ($isfullname) {
                    $responsedate = userdate($ridssub[$pos]);
                    $title = $ridsuserfullname[$pos];
                    // Only add date if more than one response by a student.
                    if ($ridsuserid[$pos] == $ridsuserid[$currpos]) {
                        $title .= ' | '.$responsedate;
                    }
                    $firstuserfullname = $ridsuserfullname[0];
                }
                $navbar->firstrespondent['title'] = $firstuserfullname;
                $navbar->previous['title'] = $title;
            }
            $navbar->respnumber = ['currpos' => ($currpos + 1), 'total' => $total];
            if ($nextrid != null) {
                $pos = $currpos + 1;
                $responsedate = '';
                $title = '';
                $lastuserfullname = '';
                $navbar->lastrespondent = ['url' => ($url.'&rid='.$lastrid)];
                $navbar->next = ['url' => ($url.'&rid='.$nextrid)];
                if ($isfullname) {
                    $responsedate = userdate($ridssub[$pos]);
                    $title = $ridsuserfullname[$pos];
                    // Only add date if more than one response by a student.
                    if ($ridsuserid[$pos] == $ridsuserid[$currpos]) {
                        $title .= ' | '.$responsedate;
                    }
                    $lastuserfullname = $ridsuserfullname[$total - 1];
                }
                $navbar->lastrespondent['title'] = $lastuserfullname;
                $navbar->next['title'] = $title;
            }
            $url = $CFG->wwwroot.'/mod/questionnaire/report.php?action=vresp&byresponse=1&group='.$currentgroupid;
            // Display navbar.
            $navbar->listlink = $url;

            // Display a "print this response" icon here in prevision of total removal of tabs in version 2.6.
            $linkname = '&nbsp;'.get_string('print', 'questionnaire');
            $url = '/mod/questionnaire/print.php?qid='.$this->id.'&rid='.$currrid.
            '&courseid='.$this->course->id.'&sec=1';
            $title = get_string('printtooltip', 'questionnaire');
            $options = array('menubar' => true, 'location' => false, 'scrollbars' => true,
                            'resizable' => true, 'height' => 600, 'width' => 800);
            $name = 'popup';
            $link = new moodle_url($url);
            $action = new popup_action('click', $link, $name, $options);
            $actionlink = $this->renderer->action_link($link, $linkname, $action, ['title' => $title],
                new pix_icon('t/print', $title));
            $navbar->printaction = $actionlink;
            $this->page->add_to_page('navigationbar', $this->renderer->navigationbar($navbar));

        } else { // Display respondents list.
            $resparr = [];
            for ($i = 0; $i < $total; $i++) {
                if ($isfullname) {
                    $responsedate = userdate($ridssub[$i]);
                    $resparr[] = '<a title = "'.$responsedate.'" href="'.$url.'&amp;rid='.
                        $rids[$i].'&amp;individualresponse=1" >'.$ridsuserfullname[$i].'</a> ';
                } else {
                    $responsedate = '';
                    $resparr[] = '<a title = "'.$responsedate.'" href="'.$url.'&amp;rid='.
                        $rids[$i].'&amp;individualresponse=1" >'.
                        get_string('response', 'questionnaire').($i + 1).'</a> ';
                }
            }
            // Table formatting from http://wikkawiki.org/PageAndCategoryDivisionInACategory.
            $total = count($resparr);
            $entries = count($resparr);
            // Default max 3 columns, max 25 lines per column.
            // TODO make this setting customizable.
            $maxlines = 20;
            $maxcols = 3;
            if ($entries >= $maxlines) {
                $colnumber = min (intval($entries / $maxlines), $maxcols);
            } else {
                $colnumber = 1;
            }
            $lines = 0;
            $a = 0;
            // How many lines with an entry in every column do we have?
            while ($entries / $colnumber > 1) {
                $lines++;
                $entries = $entries - $colnumber;
            }
            // Prepare output.
            $respcols = new stdClass();
            for ($i = 0; $i < $colnumber; $i++) {
                $colname = 'respondentscolumn'.$i;
                for ($j = 0; $j < $lines; $j++) {
                    $respcols->{$colname}->respondentlink[] = $resparr[$a];
                    $a++;
                }
                // The rest of the entries (less than the number of cols).
                if ($entries) {
                    $respcols->{$colname}->respondentlink[] = $resparr[$a];
                    $entries--;
                    $a++;
                }
            }

            $this->page->add_to_page('responses', $this->renderer->responselist($respcols));
        }
    }

    // Display responses for current user (your responses).
    public function survey_results_navbar_student($currrid, $userid, $instance, $resps, $reporttype='myreport', $sid='') {
        global $DB;
        $stranonymous = get_string('anonymous', 'questionnaire');
        $output = '';

        $total = count($resps);
        $rids = array();
        $ridssub = array();
        $ridsusers = array();
        $i = 0;
        $currpos = -1;
        $title = '';
        foreach ($resps as $response) {
            array_push($rids, $response->id);
            array_push($ridssub, $response->submitted);
            $ruser = '';
            if ($reporttype == 'report') {
                if ($this->respondenttype != 'anonymous') {
                    if ($user = $DB->get_record('user', ['id' => $response->userid])) {
                        $ruser = ' | ' .fullname($user);
                    }
                } else {
                    $ruser = ' | ' . $stranonymous;
                }
            }
            array_push($ridsusers, $ruser);
            if ($response->id == $currrid) {
                $currpos = $i;
            }
            $i++;
        }
        $prevrid = ($currpos > 0) ? $rids[$currpos - 1] : null;
        $nextrid = ($currpos < $total - 1) ? $rids[$currpos + 1] : null;
        $rowsperpage = 1;

        if ($reporttype == 'myreport') {
            $url = 'myreport.php?instance='.$instance.'&user='.$userid.'&action=vresp&byresponse=1&individualresponse=1';
        } else {
            $url = 'report.php?instance='.$instance.'&user='.$userid.'&action=vresp&byresponse=1&individualresponse=1&sid='.$sid;
        }
        $linkarr = array();
        $navbar = new \stdClass();
        $displaypos = 1;
        if ($prevrid != null) {
            $title = userdate($ridssub[$currpos - 1].$ridsusers[$currpos - 1]);
            $navbar->previous = ['url' => ($url.'&rid='.$prevrid), 'title' => $title];
        }
        for ($i = 0; $i < $currpos; $i++) {
            $title = userdate($ridssub[$i]).$ridsusers[$i];
            $navbar->prevrespnumbers[] = ['url' => ($url.'&rid='.$rids[$i]), 'title' => $title, 'respnumber' => $displaypos];
            $displaypos++;
        }
        $navbar->currrespnumber = $displaypos;
        for (++$i; $i < $total; $i++) {
            $displaypos++;
            $title = userdate($ridssub[$i]).$ridsusers[$i];
            $navbar->nextrespnumbers[] = ['url' => ($url.'&rid='.$rids[$i]), 'title' => $title, 'respnumber' => $displaypos];
        }
        if ($nextrid != null) {
            $title = userdate($ridssub[$currpos + 1]).$ridsusers[$currpos + 1];
            $navbar->next = ['url' => ($url.'&rid='.$nextrid), 'title' => $title];
        }
        $this->page->add_to_page('navigationbar', $this->renderer->usernavigationbar($navbar));
        $this->page->add_to_page('bottomnavigationbar', $this->renderer->usernavigationbar($navbar));
    }

    /* {{{ proto string survey_results(int survey_id, int precision, bool show_totals, int question_id,
     * array choice_ids, int response_id)
        Builds HTML for the results for the survey. If a
        question id and choice id(s) are given, then the results
        are only calculated for respodants who chose from the
        choice ids for the given question id.
        Returns empty string on sucess, else returns an error
        string. */

    public function survey_results($precision = 1, $showtotals = 1, $qid = '', $cids = '', $rid = '',
                $uid=false, $currentgroupid='', $sort='') {
        global $SESSION, $DB;

        $SESSION->questionnaire->noresponses = false;
        if (empty($precision)) {
            $precision  = 1;
        }
        if ($showtotals === '') {
            $showtotals = 1;
        }

        if (is_int($cids)) {
            $cids = array($cids);
        }
        if (is_string($cids)) {
            $cids = preg_split("/ /", $cids); // Turn space seperated list into array.
        }

        // Build associative array holding whether each question
        // type has answer choices or not and the table the answers are in
        // TO DO - FIX BELOW TO USE STANDARD FUNCTIONS.
        $haschoices = array();
        $responsetable = array();
        if (!($types = $DB->get_records('questionnaire_question_type', array(), 'typeid', 'typeid, has_choices, response_table'))) {
            $errmsg = sprintf('%s [ %s: question_type ]',
                    get_string('errortable', 'questionnaire'), 'Table');
            return($errmsg);
        }
        foreach ($types as $type) {
            $haschoices[$type->typeid] = $type->has_choices; // TODO is that variable actually used?
            $responsetable[$type->typeid] = $type->response_table;
        }

        // Load survey title (and other globals).
        if (empty($this->survey)) {
            $errmsg = get_string('erroropening', 'questionnaire') ." [ ID:{$this->sid} R:";
            return($errmsg);
        }

        if (empty($this->questions)) {
            $errmsg = get_string('erroropening', 'questionnaire') .' '. 'No questions found.';
            return($errmsg);
        }

        // Find total number of survey responses and relevant response ID's.
        if (!empty($rid)) {
            $rids = $rid;
            if (is_array($rids)) {
                $navbar = false;
            } else {
                $navbar = true;
            }
            $total = 1;
        } else {
            $navbar = false;
            if ($uid !== false) { // One participant only.
                $sql = "SELECT r.id, r.survey_id
                          FROM {questionnaire_response} r
                         WHERE r.survey_id='{$this->survey->id}' AND
                               r.userid = $uid AND
                               r.complete='y'
                         ORDER BY r.id";
                // All participants or all members of a group.
            } else if ($currentgroupid == 0) {
                $sql = "SELECT r.id, r.survey_id, r.userid as userid
                          FROM {questionnaire_response} r
                         WHERE r.survey_id='{$this->survey->id}' AND
                               r.complete='y'
                         ORDER BY r.id";
            } else { // Members of a specific group.
                $sql = "SELECT r.id, r.survey_id
                          FROM {questionnaire_response} r,
                                {groups_members} gm
                         WHERE r.survey_id='{$this->survey->id}' AND
                               r.complete='y' AND
                               gm.groupid=".$currentgroupid." AND
                               r.userid=gm.userid
                         ORDER BY r.id";
            }
            if (!($rows = $DB->get_records_sql($sql))) {
                $this->page->add_to_page('respondentinfo',
                    $this->renderer->notification(get_string('noresponses', 'questionnaire'),
                    \core\output\notification::NOTIFY_ERROR));
                $SESSION->questionnaire->noresponses = true;
                return;
            }
            $total = count($rows);
            $this->page->add_to_page('respondentinfo',
                ' '.get_string('responses', 'questionnaire').': <strong>'.$total.'</strong>');
            if (empty($rows)) {
                $errmsg = get_string('erroropening', 'questionnaire') .' '. get_string('noresponsedata', 'questionnaire');
                    return($errmsg);
            }

            $rids = array();
            foreach ($rows as $row) {
                array_push($rids, $row->id);
            }
        }

        if ($navbar) {
            // Show response navigation bar.
            $this->survey_results_navbar($rid);
        }

        $this->page->add_to_page('title', clean_text($this->survey->title));
        if ($this->survey->subtitle) {
            $this->page->add_to_page('subtitle', clean_text($this->survey->subtitle));
        }
        if ($this->survey->info) {
            $infotext = file_rewrite_pluginfile_urls($this->survey->info, 'pluginfile.php',
                $this->context->id, 'mod_questionnaire', 'info', $this->survey->id);
            $this->page->add_to_page('addinfo', format_text($infotext, FORMAT_HTML, ['noclean' => true]));
        }

        $qnum = 0;

        $anonymous = $this->respondenttype == 'anonymous';

        foreach ($this->questions as $question) {
            if ($question->type_id == QUESPAGEBREAK) {
                continue;
            }
            $this->page->add_to_page('responses', $this->renderer->container_start('qn-container'));
            if ($question->type_id != QUESSECTIONTEXT) {
                $qnum++;
                $this->page->add_to_page('responses', $this->renderer->container_start('qn-info'));
                if ($question->type_id != QUESSECTIONTEXT) {
                    $this->page->add_to_page('responses', $this->renderer->heading($qnum, 2, 'qn-number'));
                }
                $this->page->add_to_page('responses', $this->renderer->container_end()); // End qn-info.
            }
            $this->page->add_to_page('responses', $this->renderer->container_start('qn-content'));
            // If question text is "empty", i.e. 2 non-breaking spaces were inserted, do not display any question text.
            if ($question->content == '<p>  </p>') {
                $question->content = '';
            }
            $this->page->add_to_page('responses',
                $this->renderer->container(format_text(file_rewrite_pluginfile_urls($question->content, 'pluginfile.php',
                $question->context->id, 'mod_questionnaire', 'question', $question->id),
                FORMAT_HTML, ['noclean' => true]), 'qn-question'));
            $this->page->add_to_page('responses', $this->renderer->results_output($question, $rids, $sort, $anonymous));
            $this->page->add_to_page('responses', $this->renderer->container_end()); // End qn-content.
            $this->page->add_to_page('responses', $this->renderer->container_end()); // End qn-container.
        }

        return;
    }

    /**
     * Get unique list of question types used in the current survey.
     *
     * @author: Guy Thomas
     * @param int $surveyid
     * @param bool $uniquebytable
     * @return array
     * @throws moodle_exception
     */
    protected function get_survey_questiontypes($uniquebytable = false) {

        $uniquetypes = [];
        $uniquetables = [];

        foreach ($this->questions as $question) {
            $type = $question->type_id;
            $responsetable = $question->responsetable;
            // Build SQL for this question type if not already done.
            if (!$uniquebytable || !in_array($responsetable, $uniquetables)) {
                if (!in_array($type, $uniquetypes)) {
                    $uniquetypes[] = $type;
                }
                if (!in_array($responsetable, $uniquetables)) {
                    $uniquetables[] = $responsetable;
                }
            }
        }

        return $uniquetypes;
    }

    /**
     * Return array of all types considered to be choices.
     *
     * @return array
     */
    protected function choice_types() {
        return [QUESRADIO, QUESDROP, QUESCHECK, QUESRATE];
    }

    /**
     * Return all the fields to be used for users in questionnaire sql.
     *
     * @author: Guy Thomas
     * @return array|string
     */
    protected function user_fields() {
        $userfieldsarr = get_all_user_name_fields();
        $userfieldsarr = array_merge($userfieldsarr, ['username', 'department', 'institution']);
        return $userfieldsarr;
    }

    /**
     * Get all survey responses in one go.
     *
     * @author: Guy Thomas
     * @param string $rid
     * @param string $userid
     * @return array
     */
    protected function get_survey_all_responses($rid = '', $userid = '', $groupid = false) {
        global $DB;
        $uniquetypes = $this->get_survey_questiontypes(true);
        $allresponsessql = "";
        $allresponsesparams = [];

        foreach ($uniquetypes as $type) {
            $question = \mod_questionnaire\question\base::question_builder($type);
            if (!isset($question->response)) {
                continue;
            }
            $allresponsessql .= $allresponsessql == '' ? '' : ' UNION ALL ';
            list ($sql, $params) = $question->response->get_bulk_sql($this->survey->id, $rid, $userid, $groupid);
            $allresponsesparams = array_merge($allresponsesparams, $params);
            $allresponsessql .= $sql;
        }

        $allresponsessql .= " ORDER BY usrid, id";
        $allresponses = $DB->get_recordset_sql($allresponsessql, $allresponsesparams);
        return $allresponses;
    }

    /**
     * Process individual row for csv output
     * @param array $outputrow output row
     * @param stdClass $resprow resultset row
     * @param int $currentgroupid
     * @param array $questionsbyposition
     * @param int $nbinfocols
     * @param int $numrespcols
     * @return array
     * @throws Exception
     * @throws coding_exception
     * @throws dml_exception
     * @throws dml_missing_record_exception
     * @throws dml_multiple_records_exception
     */
    protected function process_csv_row(array &$row,
                                       stdClass $resprow,
                                       $currentgroupid,
                                       array &$questionsbyposition,
                                       $nbinfocols,
                                       $numrespcols) {
        global $DB;

        static $config = null;

        if ($config === null) {
            $config = get_config('questionnaire', 'downloadoptions');
        }
        $options = empty($config) ? array() : explode(',', $config);

        $positioned = [];
        $user = new stdClass();
        foreach ($this->user_fields() as $userfield) {
            $user->$userfield = $resprow->$userfield;
        }
        $user->id = $resprow->userid;
        $isanonymous = $this->respondenttype == 'anonymous';

        // Moodle:
        // Get the course name that this questionnaire belongs to.
        if ($this->survey->realm != 'public') {
            $courseid = $this->course->id;
            $coursename = $this->course->fullname;
        } else {
            // For a public questionnaire, look for the course that used it.
            $sql = 'SELECT q.id, q.course, c.fullname '.
                'FROM {questionnaire} q, {questionnaire_attempts} qa, {course} c '.
                'WHERE qa.rid = ? AND q.id = qa.qid AND c.id = q.course';
            if ($record = $DB->get_record_sql($sql, [$resprow->rid])) {
                $courseid = $record->course;
                $coursename = $record->fullname;
            } else {
                $courseid = $this->course->id;
                $coursename = $this->course->fullname;
            }
        }

        // Moodle:
        // Determine if the user is a member of a group in this course or not.
        // TODO - review for performance.
        $groupname = '';
        if (groups_get_activity_groupmode($this->cm, $this->course)) {
            if ($currentgroupid > 0) {
                $groupname = groups_get_group_name($currentgroupid);
            } else {
                if ($user->id) {
                    if ($groups = groups_get_all_groups($courseid, $user->id)) {
                        foreach ($groups as $group) {
                            $groupname .= $group->name.', ';
                        }
                        $groupname = substr($groupname, 0, strlen($groupname) - 2);
                    } else {
                        $groupname = ' ('.get_string('groupnonmembers').')';
                    }
                }
            }
        }

        if ($isanonymous) {
            $fullname = get_string('anonymous', 'questionnaire');
            $username = '';
            $uid = '';
        } else {
            $uid = $user->id;
            $fullname = fullname($user);
            $username = $user->username;
        }

        if (in_array('response', $options)) {
            array_push($positioned, $resprow->rid);
        }
        if (in_array('submitted', $options)) {
            // For better compabitility & readability with Excel.
            $submitted = date(get_string('strfdateformatcsv', 'questionnaire'), $resprow->submitted);
            array_push($positioned, $submitted);
        }
        if (in_array('institution', $options)) {
            array_push($positioned, $user->institution);
        }
        if (in_array('department', $options)) {
            array_push($positioned, $user->department);
        }
        if (in_array('course', $options)) {
            array_push($positioned, $coursename);
        }
        if (in_array('group', $options)) {
            array_push($positioned, $groupname);
        }
        if (in_array('id', $options)) {
            array_push($positioned, $uid);
        }
        if (in_array('fullname', $options)) {
            array_push($positioned, $fullname);
        }
        if (in_array('username', $options)) {
            array_push($positioned, $username);
        }

        for ($c = $nbinfocols; $c < $numrespcols; $c++) {
            if (isset($row[$c])) {
                $positioned[] = $row[$c];
            } else if (isset($questionsbyposition[$c])) {
                $question = $questionsbyposition[$c];
                $qtype = intval($question->type_id);
                if ($qtype === QUESCHECK) {
                    $positioned[] = '0';
                } else {
                    $positioned[] = null;
                }
            } else {
                $positioned[] = null;
            }
        }
        return $positioned;
    }

    /* {{{ proto array survey_generate_csv(int survey_id)
    Exports the results of a survey to an array.
    */
    public function generate_csv($rid='', $userid='', $choicecodes=1, $choicetext=0, $currentgroupid) {
        global $DB;

        raise_memory_limit('1G');

        $output = array();
        $stringother = get_string('other', 'questionnaire');

        $config = get_config('questionnaire', 'downloadoptions');
        $options = empty($config) ? array() : explode(',', $config);
        $columns = array();
        $types = array();
        foreach ($options as $option) {
            if (in_array($option, array('response', 'submitted', 'id'))) {
                $columns[] = get_string($option, 'questionnaire');
                $types[] = 0;
            } else {
                $columns[] = get_string($option);
                $types[] = 1;
            }
        }
        $nbinfocols = count($columns);

        $idtocsvmap = array(
            '0',    // 0: unused
            '0',    // 1: bool -> boolean
            '1',    // 2: text -> string
            '1',    // 3: essay -> string
            '0',    // 4: radio -> string
            '0',    // 5: check -> string
            '0',    // 6: dropdn -> string
            '0',    // 7: rating -> number
            '0',    // 8: rate -> number
            '1',    // 9: date -> string
            '0'     // 10: numeric -> number.
        );

        if (!$survey = $DB->get_record('questionnaire_survey', array('id' => $this->survey->id))) {
            print_error ('surveynotexists', 'questionnaire');
        }

        // Get all responses for this survey in one go.
        $allresponsesrs = $this->get_survey_all_responses($rid, $userid, $currentgroupid);

        // Do we have any questions of type RADIO, DROP, CHECKBOX OR RATE? If so lets get all their choices in one go.
        $choicetypes = $this->choice_types();

        // Get unique list of question types used in this survey.
        $uniquetypes = $this->get_survey_questiontypes();

        if (count(array_intersect($choicetypes, $uniquetypes)) > 0) {
            $choiceparams = [$this->survey->id];
            $choicesql = "
                SELECT DISTINCT c.id as cid, q.id as qid, q.precise AS precise, q.name, c.content
                  FROM {questionnaire_question} q
                  JOIN {questionnaire_quest_choice} c ON question_id = q.id
                 WHERE q.survey_id = ? ORDER BY cid ASC
            ";
            $choicerecords = $DB->get_records_sql($choicesql, $choiceparams);
            $choicesbyqid = [];
            if (!empty($choicerecords)) {
                // Hash the options by question id.
                foreach ($choicerecords as $choicerecord) {
                    if (!isset($choicesbyqid[$choicerecord->qid])) {
                        // New question id detected, intialise empty array to store choices.
                        $choicesbyqid[$choicerecord->qid] = [];
                    }
                    $choicesbyqid[$choicerecord->qid][$choicerecord->cid] = $choicerecord;
                }
            }
        }

        $num = 1;

        $questionidcols = [];

        foreach ($this->questions as $question) {
            // Skip questions that aren't response capable.
            if (!isset($question->response)) {
                continue;
            }
            // Establish the table's field names.
            $qid = $question->id;
            $qpos = $question->position;
            $col = $question->name;
            $type = $question->type_id;
            if (in_array($type, $choicetypes)) {
                /* single or multiple or rate */
                if (!isset($choicesbyqid[$qid])) {
                    throw new coding_exception('Choice question has no choices!', 'question id '.$qid.' of type '.$type);
                }
                $choices = $choicesbyqid[$qid];

                $subqnum = 0;
                switch ($type) {

                    case QUESRADIO: // Single.
                    case QUESDROP:
                        $columns[][$qpos] = $col;
                        $questionidcols[][$qpos] = $qid;
                        array_push($types, $idtocsvmap[$type]);
                        $thisnum = 1;
                        foreach ($choices as $choice) {
                            $content = $choice->content;
                            // If "Other" add a column for the actual "other" text entered.
                            if (preg_match('/^!other/', $content)) {
                                $col = $choice->name.'_'.$stringother;
                                $columns[][$qpos] = $col;
                                $questionidcols[][$qpos] = null;
                                array_push($types, '0');
                            }
                        }
                        break;

                    case QUESCHECK: // Multiple.
                        $thisnum = 1;
                        foreach ($choices as $choice) {
                            $content = $choice->content;
                            $modality = '';
                            $contents = questionnaire_choice_values($content);
                            if ($contents->modname) {
                                $modality = $contents->modname;
                            } else if ($contents->title) {
                                $modality = $contents->title;
                            } else {
                                $modality = strip_tags($contents->text);
                            }
                            $col = $choice->name.'->'.$modality;
                            $columns[][$qpos] = $col;
                            $questionidcols[][$qpos] = $qid.'_'.$choice->cid;
                            array_push($types, '0');
                            // If "Other" add a column for the "other" checkbox.
                            // Then add a column for the actual "other" text entered.
                            if (preg_match('/^!other/', $content)) {
                                $content = $stringother;
                                $col = $choice->name.'->['.$content.']';
                                $columns[][$qpos] = $col;
                                $questionidcols[][$qpos] = null;
                                array_push($types, '0');
                            }
                        }
                        break;

                    case QUESRATE: // Rate.
                        foreach ($choices as $choice) {
                            $nameddegrees = 0;
                            $modality = '';
                            $content = $choice->content;
                            $osgood = false;
                            if ($choice->precise == 3) {
                                $osgood = true;
                            }
                            if (preg_match("/^[0-9]{1,3}=/", $content, $ndd)) {
                                $nameddegrees++;
                            } else {
                                if ($osgood) {
                                    list($contentleft, $contentright) = array_merge(preg_split('/[|]/', $content), array(' '));
                                    $contents = questionnaire_choice_values($contentleft);
                                    if ($contents->title) {
                                        $contentleft = $contents->title;
                                    }
                                    $contents = questionnaire_choice_values($contentright);
                                    if ($contents->title) {
                                        $contentright = $contents->title;
                                    }
                                    $modality = strip_tags($contentleft.'|'.$contentright);
                                    $modality = preg_replace("/[\r\n\t]/", ' ', $modality);
                                } else {
                                    $contents = questionnaire_choice_values($content);
                                    if ($contents->modname) {
                                        $modality = $contents->modname;
                                    } else if ($contents->title) {
                                        $modality = $contents->title;
                                    } else {
                                        $modality = strip_tags($contents->text);
                                        $modality = preg_replace("/[\r\n\t]/", ' ', $modality);
                                    }
                                }
                                $col = $choice->name.'->'.$modality;
                                $columns[][$qpos] = $col;
                                $questionidcols[][$qpos] = $qid.'_'.$choice->cid;
                                array_push($types, $idtocsvmap[$type]);
                            }
                        }
                        break;
                }
            } else {
                $columns[][$qpos] = $col;
                $questionidcols[][$qpos] = $qid;
                array_push($types, $idtocsvmap[$type]);
            }
            $num++;
        }

        array_push($output, $columns);
        $numrespcols = count($output[0]); // Number of columns used for storing question responses.

        // Flatten questionidcols.
        $tmparr = [];
        for ($c = 0; $c < $nbinfocols; $c++) {
            $tmparr[] = null; // Pad with non question columns.
        }
        foreach ($questionidcols as $i => $positions) {
            foreach ($positions as $position => $qid) {
                $tmparr[] = $qid;
            }
        }
        $questionidcols = $tmparr;

        // Create array of question positions hashed by question / question + choiceid.
        // And array of questions hashed by position.
        $questionpositions = [];
        $questionsbyposition = [];
        $p = 0;
        foreach ($questionidcols as $qid) {
            if ($qid === null) {
                // This is just padding, skip.
                $p++;
                continue;
            }
            $questionpositions[$qid] = $p;
            if (strpos($qid, '_') !== false) {
                $tmparr = explode ('_', $qid);
                $questionid = $tmparr[0];
            } else {
                $questionid = $qid;
            }
            $questionsbyposition[$p] = $this->questions[$questionid];
            $p++;
        }

        $formatoptions = new stdClass();
        $formatoptions->filter = false;  // To prevent any filtering in CSV output.

        // Get textual versions of responses, add them to output at the correct col position.
        $prevresprow = false; // Previous response row.
        $row = [];
        foreach ($allresponsesrs as $responserow) {
            $rid = $responserow->rid;
            $qid = $responserow->question_id;
            $question = $this->questions[$qid];
            $qtype = intval($question->type_id);
            $questionobj = $this->questions[$qid];

            if ($prevresprow !== false && $prevresprow->rid !== $rid) {
                $output[] = $this->process_csv_row($row, $prevresprow, $currentgroupid, $questionsbyposition,
                    $nbinfocols, $numrespcols);
                $row = [];
            }

            if ($qtype === QUESRATE || $qtype === QUESCHECK) {
                $key = $qid.'_'.$responserow->choice_id;
                $position = $questionpositions[$key];
                if ($qtype === QUESRATE) {
                    $choicetxt = $responserow->rank + 1;
                } else {
                    $content = $choicesbyqid[$qid][$responserow->choice_id]->content;
                    if (preg_match('/^!other/', $content)) {
                        // If this is an "other" column, put the text entered in the next position.
                        $row[$position + 1] = $responserow->response;
                        $choicetxt = empty($responserow->choice_id) ? '0' : '1';
                    } else if (!empty($responserow->choice_id)) {
                        $choicetxt = '1';
                    } else {
                        $choicetxt = '0';
                    }
                }
                $responsetxt = $choicetxt;
                $row[$position] = $responsetxt;
            } else {
                $position = $questionpositions[$qid];
                if ($questionobj->has_choices()) {
                    // This is choice type question, so process as so.
                    $c = 0;
                    if (in_array(intval($question->type_id), $choicetypes)) {
                        $choices = $choicesbyqid[$qid];
                        // Get position of choice.
                        foreach ($choices as $choice) {
                            $c++;
                            if ($responserow->choice_id === $choice->cid) {
                                break;
                            }
                        }
                    }

                    $content = $choicesbyqid[$qid][$responserow->choice_id]->content;
                    if (preg_match('/^!other/', $content)) {
                        // If this has an "other" text, use it.
                        $responsetxt = get_string('other', 'questionnaire');
                        $responsetxt1 = $responserow->response;
                    } else if (($choicecodes == 1) && ($choicetext == 1)) {
                        $responsetxt = $c.' : '.$content;
                    } else if ($choicecodes == 1) {
                        $responsetxt = $c;
                    } else {
                        $responsetxt = $content;
                    }
                } else if (intval($qtype) === QUESYESNO) {
                    // At this point, the boolean responses are returned as characters in the "response"
                    // field instead of "choice_id" for csv exports (CONTRIB-6436).
                    $responsetxt = $responserow->response === 'y' ? "1" : "0";
                } else {
                    // Strip potential html tags from modality name.
                    $responsetxt = $responserow->response;
                    if (!empty($responsetxt)) {
                        $responsetxt = $responserow->response;
                        $responsetxt = strip_tags($responsetxt);
                        $responsetxt = preg_replace("/[\r\n\t]/", ' ', $responsetxt);
                    }
                }
                $row[$position] = $responsetxt;
                // Check for "other" text and set it to the next position if present.
                if (!empty($responsetxt1)) {
                    $row[$position + 1] = $responsetxt1;
                    unset($responsetxt1);
                }
            }

            $prevresprow = $responserow;
        }

        if ($prevresprow !== false) {
            // Add final row to output. May not exist if no response data was ever present.
            $output[] = $this->process_csv_row($row, $prevresprow, $currentgroupid, $questionsbyposition,
                $nbinfocols, $numrespcols);
        }

        // Change table headers to incorporate actual question numbers.
        $numcol = 0;
        $numquestion = 0;
        $out = '';
        $oldkey = 0;

        for ($i = $nbinfocols; $i < $numrespcols; $i++) {
            $sep = '';
            $thisoutput = current($output[0][$i]);
            $thiskey = key($output[0][$i]);
            // Case of unnamed rate single possible answer (full stop char is used for support).
            if (strstr($thisoutput, '->.')) {
                $thisoutput = str_replace('->.', '', $thisoutput);
            }
            // If variable is not named no separator needed between Question number and potential sub-variables.
            if ($thisoutput == '' || strstr($thisoutput, '->.') || substr($thisoutput, 0, 2) == '->'
                || substr($thisoutput, 0, 1) == '_') {
                $sep = '';
            } else {
                $sep = '_';
            }
            if ($thiskey > $oldkey) {
                $oldkey = $thiskey;
                $numquestion++;
            }
            // Abbreviated modality name in multiple or rate questions (COLORS->blue=the color of the sky...).
            $pos = strpos($thisoutput, '=');
            if ($pos) {
                $thisoutput = substr($thisoutput, 0, $pos);
            }
            $other = $sep.$stringother;
            $out = 'Q'.sprintf("%02d", $numquestion).$sep.$thisoutput;
            $output[0][$i] = $out;
        }
        return $output;
    }

    /* {{{ proto bool survey_export_csv(int survey_id, string filename)
        Exports the results of a survey to a CSV file.
        Returns true on success.
        */

    private function export_csv($filename) {
        $umask = umask(0077);
        $fh = fopen($filename, 'w');
        umask($umask);
        if (!$fh) {
            return 0;
        }

        $data = survey_generate_csv($rid = '', $userid = '', $currentgroupid = '');

        foreach ($data as $row) {
            fputs($fh, join(', ', $row) . "\n");
        }

        fflush($fh);
        fclose($fh);

        return 1;
    }


    /**
     * Function to move a question to a new position.
     * Adapted from feedback plugin.
     *
     * @param int $moveqid The id of the question to be moved.
     * @param int $movetopos The position to move question to.
     *
     */

    public function move_question($moveqid, $movetopos) {
        global $DB;

        $questions = $this->questions;
        $movequestion = $this->questions[$moveqid];

        if (is_array($questions)) {
            $index = 1;
            foreach ($questions as $question) {
                if ($index == $movetopos) {
                    $index++;
                }
                if ($question->id == $movequestion->id) {
                    $movequestion->position = $movetopos;
                    $DB->update_record("questionnaire_question", $movequestion);
                    continue;
                }
                $question->position = $index;
                $DB->update_record("questionnaire_question", $question);
                $index++;
            }
            return true;
        }
        return false;
    }

    public function response_analysis($rid, $resps, $compare, $isgroupmember, $allresponses, $currentgroupid,
                                      $filteredsections = null) {
        global $DB, $CFG;
        require_once($CFG->libdir.'/tablelib.php');
        require_once($CFG->dirroot.'/mod/questionnaire/drawchart.php');

        // Find if there are any feedbacks in this questionnaire.
        $sql = "SELECT * FROM {questionnaire_fb_sections} WHERE survey_id = ? AND section IS NOT NULL";
        if (!$fbsections = $DB->get_records_sql($sql, [$this->survey->id])) {
            return '';
        }

        $action = optional_param('action', 'vall', PARAM_ALPHA);

        if ($resp = $DB->get_record('questionnaire_response', ['id' => $rid]) ) {
            $userid = $resp->userid;
            if ($user = $DB->get_record('user', ['id' => $userid])) {
                $ruser = fullname($user);
            }
        }
        // Available group modes (0 = no groups; 1 = separate groups; 2 = visible groups).
        $groupmode = groups_get_activity_groupmode($this->cm, $this->course);
        $groupname = get_string('allparticipants');
        if ($groupmode > 0) {
            if ($currentgroupid > 0) {
                $groupname = groups_get_group_name($currentgroupid);
            } else {
                $groupname = get_string('allparticipants');
            }
        }
        if ($this->survey->feedbackscores) {
            $table = new html_table();
            $table->size = [null, null];
            $table->align = ['left', 'right', 'right'];
            $table->head = [];
            $table->wrap = [];
            if ($compare) {
                $table->head = [get_string('feedbacksection', 'questionnaire'), $ruser, $groupname];
            } else {
                $table->head = [get_string('feedbacksection', 'questionnaire'), $groupname];
            }
        }

        $fbsectionsnb = array_keys($fbsections);

        // Get all response ids for all respondents.
        $rids = array();
        foreach ($resps as $key => $resp) {
            $rids[] = $key;
        }
        $nbparticipants = count($rids);
        $responsescores = [];

        // Calculate max score per question in questionnaire.
        $qmax = [];
        $maxtotalscore = 0;
        foreach ($this->questions as $question) {
            $qid = $question->id;
            if ($question->valid_feedback()) {
                $qmax[$qid] = $question->get_feedback_maxscore();
                $maxtotalscore += $qmax[$qid];
                // Get all the feedback scores for this question.
                $responsescores[$qid] = $question->get_feedback_scores($rids);
            }
        }
        // Just in case no values have been entered in the various questions possible answers field.
        if ($maxtotalscore === 0) {
            return '';
        }
        $feedbackmessages = [];

        // Get individual scores for each question in this responses set.
        $qscore = [];
        $allqscore = [];

        if (!$allresponses && $groupmode != 0) {
            $nbparticipants = max(1, $nbparticipants - !$isgroupmember);
        }
        foreach ($responsescores as $qid => $responsescore) {
            if (!empty($responsescore)) {
                foreach ($responsescore as $rrid => $response) {
                    // If this is current user's response OR if current user is viewing another group's results.
                    if ($rrid == $rid || $allresponses) {
                        if (!isset($qscore[$qid])) {
                            $qscore[$qid] = 0;
                        }
                        $qscore[$qid] = $response->score;
                    }
                    // Course score.
                    if (!isset($allqscore[$qid])) {
                        $allqscore[$qid] = 0;
                    }
                    // Only add current score if conditions below are met.
                    if ($groupmode == 0 || $isgroupmember || (!$isgroupmember && $rrid != $rid) || $allresponses) {
                        $allqscore[$qid] += $response->score;
                    }
                }
            }
        }
        $totalscore = array_sum($qscore);
        $scorepercent = round($totalscore / $maxtotalscore * 100);
        $oppositescorepercent = 100 - $scorepercent;
        $alltotalscore = array_sum($allqscore);
        $allscorepercent = round($alltotalscore / $nbparticipants / $maxtotalscore * 100);

        // No need to go further if feedback is global, i.e. only relying on total score.
        if ($this->survey->feedbacksections == 1) {
            $sectionid = $fbsectionsnb[0];
            $sectionlabel = $fbsections[$sectionid]->sectionlabel;

            $sectionheading = $fbsections[$sectionid]->sectionheading;
            $feedbacks = $DB->get_records('questionnaire_feedback', array('section_id' => $sectionid));
            $labels = array();
            foreach ($feedbacks as $feedback) {
                if ($feedback->feedbacklabel != '') {
                    $labels[] = $feedback->feedbacklabel;
                }
            }
            $feedback = $DB->get_record_select('questionnaire_feedback',
                            'section_id = ? AND minscore <= ? AND ? < maxscore', array($sectionid, $scorepercent, $scorepercent));

            // To eliminate all potential % chars in heading text (might interfere with the sprintf function).
            $sectionheading = str_replace('%', '', $sectionheading);
            // Replace section heading placeholders with their actual value (if any).
            $original = array('$scorepercent', '$oppositescorepercent');
            $result = array('%s%%', '%s%%');
            $sectionheading = str_replace($original, $result, $sectionheading);
            $sectionheading = sprintf($sectionheading , $scorepercent, $oppositescorepercent);
            $sectionheading = file_rewrite_pluginfile_urls($sectionheading, 'pluginfile.php',
                            $this->context->id, 'mod_questionnaire', 'sectionheading', $sectionid);
            $feedbackmessages[] = $this->renderer->box_start();
            $feedbackmessages[] = format_text($sectionheading, FORMAT_HTML, ['noclean' => true]);
            $feedbackmessages[] = $this->renderer->box_end();

            if (!empty($feedback->feedbacktext)) {
                // Clean the text, ready for display.
                $formatoptions = new stdClass();
                $formatoptions->noclean = true;
                $feedbacktext = file_rewrite_pluginfile_urls($feedback->feedbacktext, 'pluginfile.php',
                                $this->context->id, 'mod_questionnaire', 'feedback', $feedback->id);
                $feedbacktext = format_text($feedbacktext, $feedback->feedbacktextformat, $formatoptions);
                $feedbackmessages[] = $this->renderer->box_start();
                $feedbackmessages[] = $feedbacktext;
                $feedbackmessages[] = $this->renderer->box_end();
            }
            $score = array($scorepercent, 100 - $scorepercent);
            $allscore = null;
            if ($compare  || $allresponses) {
                $allscore = array($allscorepercent, 100 - $allscorepercent);
            }
            $usergraph = get_config('questionnaire', 'usergraph');
            if ($usergraph && $this->survey->chart_type) {
                $this->page->add_to_page('feedbackcharts',
                    draw_chart ($feedbacktype = 'global', $this->survey->chart_type, $labels,
                                $score, $allscore, $sectionlabel, $groupname, $allresponses));
            }
            // Display class or group score. Pending chart library decision to display?
            // Find out if this feedback sectionlabel has a pipe separator.
            $lb = explode("|", $sectionlabel);
            $oppositescore = '';
            $oppositeallscore = '';
            if (count($lb) > 1) {
                $sectionlabel = $lb[0].' | '.$lb[1];
                $oppositescore = ' | '.$score[1].'%';
                $oppositeallscore = ' | '.$allscore[1].'%';
            }
            if ($this->survey->feedbackscores) {
                if ($compare) {
                    $table->data[] = array($sectionlabel, $score[0].'%'.$oppositescore, $allscore[0].'%'.$oppositeallscore);
                } else {
                    $table->data[] = array($sectionlabel, $allscore[0].'%'.$oppositeallscore);
                }

                $this->page->add_to_page('feedbackscores', html_writer::table($table));
            }

            return $feedbackmessages;
        }

        // Now process scores for more than one section.

        // Initialize scores and maxscores to 0.
        $score = array();
        $allscore = array();
        $maxscore = array();
        $scorepercent = array();
        $allscorepercent = array();
        $oppositescorepercent = array();
        $alloppositescorepercent = array();
        $chartlabels = array();
        $chartscore = array();
        // sections where all questions are unseen because of the $advdependencies
        $nanscores = array();

        for ($i = 1; $i <= $this->survey->feedbacksections; $i++) {
            $score[$i] = 0;
            $allscore[$i] = 0;
            $maxscore[$i] = 0;
            $scorepercent[$i] = 0;
        }

        for ($section = 1; $section <= $this->survey->feedbacksections; $section++) {
            // get feedback messages only for this sections
            if (($filteredsections != null) && !in_array($section, $filteredsections)) {
                continue;
            }
            foreach ($fbsections as $key => $fbsection) {
                if ($fbsection->section == $section) {
                    $feedbacksectionid = $key;
                    $scorecalculation = unserialize($fbsection->scorecalculation);
                    $sectionheading = $fbsection->sectionheading;
                    $imageid = $fbsection->id;
                    $chartlabels [$section] = $fbsection->sectionlabel;
                }
            }
            foreach ($scorecalculation as $qid => $key) {
                // Just in case a question pertaining to a section has been deleted or made not required
                // after being included in scorecalculation.
                if (isset($qscore[$qid])) {
                    $key = ($key == 0) ? 1 : $key;
                    $score[$section] += round($qscore[$qid] * $key);
                    $maxscore[$section] += round($qmax[$qid] * $key);
                    if ($compare  || $allresponses) {
                        $allscore[$section] += round($allqscore[$qid] * $key);
                    }
                }
            }

            if ($maxscore[$section] == 0) {
                array_push($nanscores, $section);
            }

            $scorepercent[$section] = ($maxscore[$section] > 0) ? (round($score[$section] / $maxscore[$section] * 100)) : 0;
            $oppositescorepercent[$section] = 100 - $scorepercent[$section];

            if (($compare || $allresponses) && $nbparticipants != 0) {
                $allscorepercent[$section] = ($maxscore[$section] > 0) ? (round(($allscore[$section] / $nbparticipants) / $maxscore[$section] * 100)) : 0;
                $alloppositescorepercent[$section] = 100 - $allscorepercent[$section];
            }

            if (!$allresponses) {
                if (is_nan($scorepercent[$section])) {
                    // Info: all questions of $section are unseen
                    // -> $scorepercent[$section] = round($score[$section] / $maxscore[$section] * 100) == NAN
                    // -> $maxscore[$section] == 0 -> division by zero
                    // $DB->get_record_select(...) fails, don't show feedbackmessage.
                    continue;
                }
                // To eliminate all potential % chars in heading text (might interfere with the sprintf function).
                $sectionheading = str_replace('%', '', $sectionheading);

                // Replace section heading placeholders with their actual value (if any).
                $original = array('$scorepercent', '$oppositescorepercent');
                $result = array("$scorepercent[$section]%", "$oppositescorepercent[$section]%");
                $sectionheading = str_replace($original, $result, $sectionheading);
                $formatoptions = new stdClass();
                $formatoptions->noclean = true;
                $sectionheading = file_rewrite_pluginfile_urls($sectionheading, 'pluginfile.php',
                                $this->context->id, 'mod_questionnaire', 'sectionheading', $imageid);
                $sectionheading = format_text($sectionheading, 1, $formatoptions);
                $feedbackmessages[] = $this->renderer->box_start('reportQuestionTitle');
                $feedbackmessages[] = format_text($sectionheading, FORMAT_HTML, $formatoptions);
                $feedback = $DB->get_record_select('questionnaire_feedback',
                                'section_id = ? AND minscore <= ? AND ? < maxscore',
                                array($feedbacksectionid, $scorepercent[$section], $scorepercent[$section]),
                                'id,feedbacktext,feedbacktextformat');
                $feedbackmessages[] = $this->renderer->box_end();
                if (!empty($feedback->feedbacktext)) {
                    // Clean the text, ready for display.
                    $formatoptions = new stdClass();
                    $formatoptions->noclean = true;
                    $feedbacktext = file_rewrite_pluginfile_urls($feedback->feedbacktext, 'pluginfile.php',
                                    $this->context->id, 'mod_questionnaire', 'feedback', $feedback->id);
                    $feedbacktext = format_text($feedbacktext, $feedback->feedbacktextformat, $formatoptions);
                    $feedbackmessages[] = $this->renderer->box_start('feedbacktext');
                    $feedbackmessages[] = $feedbacktext;
                    $feedbackmessages[] = $this->renderer->box_end();
                }
            }
        }

        // Display class or group score.
        switch ($action) {
            case 'vallasort':
                asort($allscore);
                break;
            case 'vallarsort':
                arsort($allscore);
                break;
            default:
        }

        foreach ($allscore as $key => $sc) {
            if (isset($chartlabels[$key])) {
                $lb = explode("|", $chartlabels[$key]);
                $oppositescore = '';
                $oppositeallscore = '';
                if (count($lb) > 1) {
                    $sectionlabel = $lb[0] . ' | ' . $lb[1];
                    $oppositescore = ' | ' . $oppositescorepercent[$key] . '%';
                    $oppositeallscore = ' | ' . $alloppositescorepercent[$key] . '%';
                } else {
                    $sectionlabel = $chartlabels[$key];
                }
                // If all questions of $section are unseen then don't show feedbackscores for this section.
                if ($compare && !is_nan($scorepercent[$key])) {
                    $table->data[] = array($sectionlabel, $scorepercent[$key] . '%' . $oppositescore,
                        $allscorepercent[$key] . '%' . $oppositeallscore);
                } else if (isset($allscorepercent[$key]) && !is_nan($allscorepercent[$key])) {
                    $table->data[] = array($sectionlabel, $allscorepercent[$key] . '%' . $oppositeallscore);
                }
            }
        }
        $usergraph = get_config('questionnaire', 'usergraph');

        // Don't show feedbackcharts for sections in $nanscores -> remove sections from array.
        foreach ($nanscores as $val) {
            unset($chartlabels[$val]);
            unset($scorepercent[$val]);
            unset($allscorepercent[$val]);
        }

        if ($usergraph && $this->survey->chart_type) {
            $this->page->add_to_page('feedbackcharts',
                draw_chart($feedbacktype = 'sections', $this->survey->chart_type, array_values($chartlabels),
                array_values($scorepercent), array_values($allscorepercent), $sectionlabel, $groupname, $allresponses));
        }
        if ($this->survey->feedbackscores) {
            $this->page->add_to_page('feedbackscores', html_writer::table($table));
        }

        return $feedbackmessages;
    }

}