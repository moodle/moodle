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

defined('MOODLE_INTERNAL') || die();

use mod_questionnaire\generator\question_response,
    mod_questionnaire\generator\question_response_rank,
    mod_questionnaire\question\question;

global $CFG;
require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');
require_once($CFG->dirroot . '/mod/questionnaire/classes/question/question.php');

/**
 * The mod_questionnaire data generator.
 *
 * @package    mod_questionnaire
 * @copyright  2015 Mike Churchward (mike@churchward.ca)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_questionnaire_generator extends testing_module_generator {

    /**
     * @var int Current position of assigned options.
     */
    protected $curpos = 0;

    /**
     * @var int keep track of how many questions have been created.
     */
    protected $questioncount = 0;

    /**
     * @var int
     */
    protected $responsecount = 0;

    /**
     * @var questionnaire[]
     */
    protected $questionnaires = [];

    /**
     * To be called from data reset code only, do not use in tests.
     * @return void
     */
    public function reset() {
        $this->questioncount = 0;
        $this->responsecount = 0;
        $this->questionnaires = [];
        parent::reset();
    }

    /**
     * Acessor for questionnaires.
     * @return array
     */
    public function questionnaires() {
        return $this->questionnaires;
    }

    /**
     * Create a questionnaire activity.
     * @param array $record Will be changed in this function.
     * @param array $options
     * @return questionnaire
     */
    public function create_instance($record = null, array $options = null) {
        $record = (object)(array)$record;

        $defaultquestionnairesettings = array(
            'qtype' => 0,
            'respondenttype' => 'fullname',
            'resp_eligible' => 'all',
            'resp_view' => 0,
            'opendate' => 0,
            'closedate' => 0,
            'resume' => 0,
            'navigate' => 0,
            'grade' => 0,
            'sid' => 0,
            'timemodified' => time(),
            'completionsubmit' => 0,
            'autonum' => 3,
            'create' => 'new-0', // Used in form only to indicate a new, empty instance.
        );

        foreach ($defaultquestionnairesettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        $instance = parent::create_instance($record, (array)$options);
        $cm = get_coursemodule_from_instance('questionnaire', $instance->id);
        $course = get_course($cm->course);
        $questionnaire = new \questionnaire($course, $cm, 0, $instance, false);

        $this->questionnaires[$instance->id] = $questionnaire;

        return $questionnaire;
    }

    /**
     * Create a survey instance with data from an existing questionnaire object.
     * @param questionnaire $questionnaire
     * @param array $record
     * @return bool|int
     */
    public function create_content($questionnaire, $record = array()) {
        global $DB;

        $survey = $DB->get_record('questionnaire_survey', array('id' => $questionnaire->sid), '*', MUST_EXIST);
        foreach ($record as $name => $value) {
            $survey->{$name} = $value;
        }
        return $questionnaire->survey_update($survey);
    }

    /**
     * Function to create a question.
     *
     * @param questionnaire $questionnaire
     * @param array|stdClass $record
     * @param array|stdClass $data - accompanying data for question - e.g. choices
     * @return \mod_questionnaire\question\question the question object
     */
    public function create_question(questionnaire $questionnaire, $record = null, $data = null) {
        global $DB;

        // Increment the question count.
        $this->questioncount++;

        $record = (array)$record;

        $record['position'] = count($questionnaire->questions);

        if (!isset($record['surveyid'])) {
            throw new coding_exception('surveyid must be present in phpunit_util::create_question() $record');
        }

        if (!isset($record['name'])) {
            throw new coding_exception('name must be present in phpunit_util::create_question() $record');
        }

        if (!isset($record['type_id'])) {
            throw new coding_exception('typeid must be present in phpunit_util::create_question() $record');
        }

        if (!isset($record['content'])) {
            $record['content'] = 'Random '.$this->type_str($record['type_id']).' '.uniqid();
        }

        // Get question type.
        $typeid = $record['type_id'];

        if ($typeid === QUESRATE && !isset($record['length'])) {
            $record['length'] = 5;
        }

        if ($typeid !== QUESPAGEBREAK && $typeid !== QUESSECTIONTEXT) {
            $qtype = $DB->get_record('questionnaire_question_type', ['id' => $typeid]);
            if (!$qtype) {
                throw new coding_exception('Could not find question type with id ' . $typeid);
            }
            // Throw an error if this requires choices and it hasn't got them.
            $this->validate_question($qtype->typeid, $data);
        }

        $record = (object)$record;

        // Add the question.
        $record->id = $DB->insert_record('questionnaire_question', $record);

        $question = \mod_questionnaire\question\question::question_builder($record->type_id, $record->id, $record);

        // Add the question choices if required.
        if ($typeid !== QUESPAGEBREAK && $typeid !== QUESSECTIONTEXT) {
            if ($question->has_choices()) {
                $this->add_question_choices($question, $data);
                $record->opts = $data;
            }
        }

        // Update questionnaire.
        $questionnaire->add_questions();

        return $question;
    }

    /**
     * Create a questionnaire with questions and response data for use in other tests.
     * @param stdClass $course
     * @param null|int $qtype
     * @param array $questiondata
     * @param null|array|stdClass $choicedata
     * @return questionnaire
     */
    public function create_test_questionnaire($course, $qtype = null, $questiondata = array(), $choicedata = null) {
        $questionnaire = $this->create_instance(array('course' => $course->id));
        $cm = get_coursemodule_from_instance('questionnaire', $questionnaire->id);
        if ($qtype !== null) {
            $questiondata['type_id'] = $qtype;
            $questiondata['surveyid'] = $questionnaire->sid;
            $questiondata['name'] = isset($questiondata['name']) ? $questiondata['name'] : 'Q1';
            $questiondata['content'] = isset($questiondata['content']) ? $questiondata['content'] : 'Test content';
            $this->create_question($questionnaire, $questiondata, $choicedata);
        }
        $questionnaire = new \questionnaire($course, $cm, $questionnaire->id, null, true);
        return $questionnaire;
    }

    /**
     * Create a reponse to the supplied question.
     * @param questionnaire $questionnaire
     * @param question $question
     * @param int|array $respval
     * @param int $userid
     * @param int $section
     * @return false|mixed|stdClass
     */
    public function create_question_response($questionnaire, $question, $respval, $userid = 1, $section = 1) {
        global $DB;
        $currentrid = 0;
        if (!is_array($respval)) {
            $respval = ['q'.$question->id => $respval];
        }
        $respdata = (object)(array_merge(['sec' => $section, 'rid' => $currentrid, 'a' => $questionnaire->id], $respval));
        $responseid = $questionnaire->response_insert($respdata, $userid);
        $this->response_commit($questionnaire, $responseid);
        return $DB->get_record('questionnaire_response', array('id' => $responseid));
    }

    /**
     * Need to create a method to access a private questionnaire method.
     * TO DO - may not need this with above "TO DO".
     * @param questionnaire $questionnaire
     * @param int $responseid
     * @return mixed
     */
    private function response_commit($questionnaire, $responseid) {
        $method = new ReflectionMethod('questionnaire', 'response_commit');
        $method->setAccessible(true);
        return $method->invoke($questionnaire, $responseid);
    }

    /**
     * Validate choice question type
     * @param array $data
     * @throws coding_exception
     */
    protected function validate_question_choice($data) {
        if (empty($data)) {
            throw new coding_exception('You must pass in an array of choices for the choice question type');
        }
    }

    /**
     * Validate radio question type
     * @param array $data
     * @throws coding_exception
     */
    protected function validate_question_radio($data) {
        if (empty($data)) {
            throw new coding_exception('You must pass in an array of choices for the radio question type');
        }
    }

    /**
     * Validate checkbox question type
     * @param array $data
     * @throws coding_exception
     */
    protected function validate_question_check($data) {
        if (empty($data)) {
            throw new coding_exception('You must pass in an array of choices for the checkbox question type');
        }
    }

    /**
     * Validate rating question type
     * @param array $data
     * @throws coding_exception
     */
    protected function validate_question_rate($data) {
        if (empty($data)) {
            throw new coding_exception('You must pass in an array of choices for the rate question type');
        }
    }

    /**
     * Thrown an error if the question isn't receiving the data it should receive.
     * @param string $typeid
     * @param array $data
     */
    protected function validate_question($typeid, $data) {
        if ($typeid == QUESCHOOSE) {
            $this->validate_question_choice($data);
        } else if ($typeid === QUESRADIO) {
            $this->validate_question_radio($data);
        } else if ($typeid === QUESCHECK) {
            $this->validate_question_check($data);
        } else if ($typeid === QUESRATE) {
            $this->validate_question_rate($data);
        }
    }

    /**
     * Add choices to question.
     *
     * @param \mod_questionnaire\question\question $question
     * @param array $data
     */
    protected function add_question_choices($question, $data) {
        foreach ($data as $content) {
            if (!is_object($content)) {
                $content = (object)[
                    'content' => $content,
                    'value' => $content
                ];
            }
            $record = (object)[
                'question_id' => $question->id,
                'content' => $content->content,
                'value' => $content->value
            ];
            $question->add_choice($record);
        }
    }

    /**
     * Does this question have choices.
     * TODO - use question object
     * @param int $typeid
     * @return bool
     */
    public function question_has_choices($typeid) {
        $choicequestions = [QUESCHOOSE, QUESRADIO, QUESCHECK, QUESDROP, QUESRATE];
        return in_array($typeid, $choicequestions);
    }

    /**
     * Return a string value for the int id.
     * @param int $qtypeid
     * @return string
     */
    public function type_str($qtypeid) {
        switch ($qtypeid) {
            case QUESYESNO:
                $qtype = 'yesno';
                break;
            case QUESTEXT:
                $qtype = 'textbox';
                break;
            case QUESESSAY:
                $qtype = 'essaybox';
                break;
            case QUESRADIO:
                $qtype = 'radiobuttons';
                break;
            case QUESCHECK:
                $qtype = 'checkboxes';
                break;
            case QUESDROP:
                $qtype = 'dropdown';
                break;
            case QUESRATE:
                $qtype = 'ratescale';
                break;
            case QUESDATE:
                $qtype = 'date';
                break;
            case QUESNUMERIC:
                $qtype = 'numeric';
                break;
            case QUESSECTIONTEXT:
                $qtype = 'sectiontext';
                break;
            case QUESPAGEBREAK:
                $qtype = 'sectionbreak';
                break;
            case QUESSLIDER:
                $qtype = 'Slider';
                break;
        }
        return $qtype;
    }

    /**
     * Return a display string for the int id.
     * @param int $qtypeid
     * @return string
     */
    public function type_name($qtypeid) {
        switch ($qtypeid) {
            case QUESYESNO:
                $qtype = 'Yes / No';
                break;
            case QUESTEXT:
                $qtype = 'Text Box';
                break;
            case QUESESSAY:
                $qtype = 'Essay Box';
                break;
            case QUESRADIO:
                $qtype = 'Radio Buttons';
                break;
            case QUESCHECK:
                $qtype = 'Check Boxes';
                break;
            case QUESDROP:
                $qtype = 'Drop Down';
                break;
            case QUESRATE:
                $qtype = 'Rate Scale';
                break;
            case QUESDATE:
                $qtype = 'Date';
                break;
            case QUESNUMERIC:
                $qtype = 'Numeric';
                break;
            case QUESSECTIONTEXT:
                $qtype = 'Section Text';
                break;
            case QUESPAGEBREAK:
                $qtype = 'Section Break';
                break;
            case QUESSLIDER:
                $qtype = 'Slider';
                break;
        }
        return $qtype;
    }

    /**
     * Add the response choice.
     * @param \mod_questionnaire\responsetype\response\response $questionresponse
     * @param int $responseid
     */
    protected function add_response_choice($questionresponse, $responseid) {
        global $DB;

        $question = $DB->get_record('questionnaire_question', ['id' => $questionresponse->questionid]);
        $qtype = intval($question->type_id);

        if (is_array($questionresponse->response)) {
            foreach ($questionresponse->response as $choice) {
                $newresponse = clone($questionresponse);
                $newresponse->response = $choice;
                $this->add_response_choice($newresponse, $responseid);
            }
            return;
        }

        if ($qtype === QUESCHOOSE || $qtype === QUESRADIO || $qtype === QUESDROP || $qtype === QUESCHECK || $qtype === QUESRATE) {
            if (is_int($questionresponse->response)) {
                $choiceid = $questionresponse->response;
            } else {
                if ($qtype === QUESRATE) {
                    if (!$questionresponse->response instanceof question_response_rank) {
                        throw new coding_exception('Question response for ranked choice should be of type question_response_rank');
                    }
                    $choiceval = $questionresponse->response->choice->content;
                } else {
                    if (!is_object($questionresponse->response)) {
                        $choiceval = $questionresponse->response;
                    } else {
                        if ($questionresponse->response->content.'' === '') {
                            throw new coding_exception('Question response cannot be null for question type '.$qtype);
                        }
                        $choiceval = $questionresponse->response->content;
                    }

                }

                // Lookup the choice id.
                $comptext = $DB->sql_compare_text('content');
                $select = 'WHERE question_id = ? AND '.$comptext.' = ?';

                $params = [intval($question->id), $choiceval];
                $rs = $DB->get_records_sql("SELECT * FROM {questionnaire_quest_choice} $select", $params, 0, 1);
                $choice = reset($rs);
                if (!$choice) {
                    throw new coding_exception('Could not find choice for "'.$choiceval.
                        '" (question_id = '.$question->id.')', var_export($choiceval, true));
                }
                $choiceid = $choice->id;

            }
            if ($qtype == QUESRATE) {
                $DB->insert_record('questionnaire_response_rank', [
                        'response_id' => $responseid,
                        'question_id' => $questionresponse->questionid,
                        'choice_id' => $choiceid,
                        'rankvalue' => $questionresponse->response->rankvalue
                    ]
                );
            } else {
                if ($qtype === QUESCHOOSE || $qtype === QUESRADIO || $qtype === QUESDROP) {
                    $instable = 'questionnaire_resp_single';
                } else if ($qtype === QUESCHECK) {
                    $instable = 'questionnaire_resp_multiple';
                }
                $DB->insert_record($instable, [
                        'response_id' => $responseid,
                        'question_id' => $questionresponse->questionid,
                        'choice_id' => $choiceid
                    ]
                );
            }
        } else {
            $DB->insert_record('questionnaire_response_text', [
                    'response_id' => $responseid,
                    'question_id' => $questionresponse->questionid,
                    'response' => $questionresponse->response
                ]
            );
        }
    }

    /**
     * Create response to questionnaire.
     *
     * @param array $questionresponses
     * @param array|stdClass $record
     * @param boolean $complete Whether the response is complete or not.
     * @return stdClass the discussion object
     */
    public function create_response($questionresponses, $record = null, $complete = true) {
        global $DB;

        // Increment the response count.
        $this->responsecount++;

        $record = (array)$record;

        if (!isset($record['questionnaireid'])) {
            throw new coding_exception('questionnaireid must be present in phpunit_util::create_response() $record');
        }

        if (!isset($record['userid'])) {
            throw new coding_exception('userid must be present in phpunit_util::create_response() $record');
        }

        $record['submitted'] = time() + $this->responsecount;

        // Add the response.
        $record['id'] = $DB->insert_record('questionnaire_response', $record);
        $responseid = $record['id'];

        foreach ($questionresponses as $questionresponse) {
            if (!$questionresponse instanceof question_response) {
                throw new coding_exception('Question responses must have an instance of question_response'.
                    var_export($questionresponse, true));
            }
            $this->add_response_choice($questionresponse, $responseid);
        }

        // Mark response as complete.
        $record['complete'] = ($complete) ? 'y' : 'n';
        $DB->update_record('questionnaire_response', $record);

        return $record;
    }


    /**
     * Generate an array of assigned options;
     * @param int $number
     */
    public function assign_opts($number = 5) {
        $opts = 'blue, red, yellow, orange, green, purple, white, black, earth, wind, fire, space, car, truck, train' .
            ', van, tram, one, two, three, four, five, six, seven, eight, nine, ten, eleven, twelve, thirteen' .
            ', fourteen, fifteen, sixteen, seventeen, eighteen, nineteen, twenty, happy, sad, jealous, angry';
        $opts = explode (', ', $opts);
        $numopts = count($opts);

        if ($number > (count($opts) / 2)) {
            throw new coding_exception('Maxiumum number of options is '.(count($opts) / 2));
        }

        $retopts = [];
        while (count($retopts) < $number) {
            $retopts[] = $opts[$this->curpos];
            $retopts = array_unique($retopts);
            if (++$this->curpos == $numopts) {
                $this->curpos = 0;
            }
        }
        // Return re-indexed version of array (otherwise you can get a weird index of 1,2,5,9, etc).
        return array_values($retopts);
    }

    /**
     * Generate a response.
     * @param questionnaire $questionnaire
     * @param \mod_questionnaire\question\question[] $questions
     * @param int $userid
     * @param bool $complete
     * @return stdClass
     */
    public function generate_response($questionnaire, $questions, $userid, $complete = true) {
        $responses = [];
        foreach ($questions as $question) {

            $choices = [];
            if ($question->has_choices()) {
                $choices = array_values($question->choices);
            }

            switch ($question->type_id) {
                case QUESTEXT :
                    $responses[] = new question_response($question->id, 'Test answer');
                    break;
                case QUESESSAY :
                    $resptext = '<h1>Some header text</h1><p>Some paragraph text</p>';
                    $responses[] = new question_response($question->id, $resptext);
                    break;
                case QUESNUMERIC :
                    $responses[] = new question_response($question->id, 83);
                    break;
                case QUESDATE :
                    $date = mktime(0, 0, 0, 12, 28, 2017);
                    $dateformat = get_string('strfdate', 'questionnaire');
                    $datestr = userdate ($date, $dateformat, '1', false);
                    $responses[] = new question_response($question->id, $datestr);
                    break;
                case QUESRADIO :
                case QUESDROP :
                    $optidx = count($choices) - 1;
                    $responses[] = new question_response($question->id, $choices[$optidx]);
                    break;
                case QUESCHECK :
                    $answers = [];
                    for ($a = 0; $a < count($choices) - 1; $a++) {
                        $optidx = count($choices) - 1;
                        $answers[] = $choices[$optidx]->content;
                    }

                    $answers = array_unique($answers);

                    $responses[] = new question_response($question->id, $answers);
                    break;
                case QUESRATE :
                    $answers = [];
                    for ($a = 0; $a < count($choices) - 1; $a++) {
                        $answers[] = new question_response_rank($choices[$a], (($a % 5) + 1));
                    }
                    $responses[] = new question_response($question->id, $answers);
                    break;
                case QUESSLIDER :
                    $responses[] = new question_response($question->id, 5);
                    break;
            }

        }
        return $this->create_response($responses, ['questionnaireid' => $questionnaire->id, 'userid' => $userid], $complete);
    }

    /**
     * Create fully defined questionnaires into the test database.
     * @param int $coursecount
     * @param int $studentcount
     * @param int $questionnairecount
     * @param int $questionspertype
     * @param array $profilefields in format ['<shortname>' => '<name>']
     */
    public function create_and_fully_populate($coursecount = 4, $studentcount = 20, $questionnairecount = 2,
                                              $questionspertype = 5, $profilefields = []) {
        global $DB;

        $dg = $this->datagenerator;
        $qdg = $this;

        $this->curpos = 0;
        $questiontypes = [QUESTEXT, QUESESSAY, QUESNUMERIC, QUESDATE, QUESRADIO, QUESDROP, QUESCHECK, QUESRATE, QUESSLIDER];
        $students = [];
        $courses = [];
        $questionnaires = [];

        if (!empty($profilefields)) {
            // Create profile fields and set them to show for user identity.
            $fields = [];
            foreach ($profilefields as $field => $name) {
                $dg->create_custom_profile_field(['datatype' => 'text',
                    'shortname' => $field, 'name' => $name]);
                $fields[] = "profile_field_{$field}";
            }
            set_config('showuseridentity', implode(',', $fields));
        }

        for ($u = 0; $u < $studentcount; $u++) {
            $user = ['firstname' => 'Testy'];
            // Set values for the profile fields.
            foreach ($profilefields as $field => $name) {
                $user["profile_field_{$field}"] = "{$field}{$u}";
            }
            $students[] = $dg->create_user($user);
        }

        $manplugin = enrol_get_plugin('manual');

        // Create courses.
        for ($c = 0; $c < $coursecount; $c++) {
            $course = $dg->create_course();
            $courses[] = $course;

            // Enrol students on course.
            $manualenrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
            foreach ($students as $student) {
                $studentrole = $DB->get_record('role', array('shortname' => 'student'));
                $manplugin->enrol_user($manualenrol, $student->id, $studentrole->id);
            }
        }

        // Create questionnaires in each course.
        $qname = 1000;
        for ($q = 0; $q < $questionnairecount; $q++) {
            foreach ($courses as $course) {
                $questionnaire = $qdg->create_instance(['course' => $course->id]);
                $questionnaires[] = $questionnaire;
                $questions = [];
                foreach ($questiontypes as $questiontype) {
                    // Add section text for this question.
                    $qdg->create_question(
                        $questionnaire,
                        [
                            'surveyid' => $questionnaire->sid,
                            'name' => $qdg->type_name($questiontype),
                            'type_id' => QUESSECTIONTEXT
                        ]
                    );
                    // Create questions.
                    for ($qpt = 0; $qpt < $questionspertype; $qpt++) {
                        $opts = null;
                        if ($qdg->question_has_choices($questiontype)) {
                            $opts = $qdg->assign_opts(10);
                        }
                        $questions[] = $qdg->create_question(
                            $questionnaire,
                            [
                                'surveyid' => $questionnaire->sid,
                                'name' => $qdg->type_name($questiontype).' '.$qname++,
                                'type_id' => $questiontype
                            ],
                            $opts
                        );
                    }
                    // Add page break.
                    $qdg->create_question(
                        $questionnaire,
                        [
                            'surveyid' => $questionnaire->sid,
                            'name' => 'pagebreak '.$qname++,
                            'type_id' => QUESPAGEBREAK
                        ]
                    );
                }

                // Create responses.
                $count = 1;
                foreach ($students as $student) {
                    // Make the last response an "incomplete" response.
                    if ($count < $studentcount) {
                        $qdg->generate_response($questionnaire, $questions, $student->id);
                    } else {
                        $qdg->generate_response($questionnaire, $questions, $student->id, false);
                    }
                    $count++;
                }
            }
        }
    }
}
