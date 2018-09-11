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

namespace mod_questionnaire\question;
use mod_questionnaire\edit_question_form;
use \questionnaire;

defined('MOODLE_INTERNAL') || die();
use \html_writer;

/**
 * This file contains the parent class for questionnaire question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

/**
 * Class for describing a question
 *
 * @author Mike Churchward
 * @package questiontypes
 */

 // Constants.
define('QUESCHOOSE', 0);
define('QUESYESNO', 1);
define('QUESTEXT', 2);
define('QUESESSAY', 3);
define('QUESRADIO', 4);
define('QUESCHECK', 5);
define('QUESDROP', 6);
define('QUESRATE', 8);
define('QUESDATE', 9);
define('QUESNUMERIC', 10);
define('QUESPAGEBREAK', 99);
define('QUESSECTIONTEXT', 100);

global $idcounter, $CFG;
$idcounter = 0;

require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

abstract class base {

    // Class Properties.
    /** @var int $id The database id of this question. */
    public $id          = 0;

    /** @var int $survey_id The database id of the survey this question belongs to. */
    // public $survey_id   = 0;  Commenting out for now, as '_' not allowed.

    /** @var string $name The name of this question. */
    public $name        = '';

    /** @var string $type The name of the question type. */
    public $type        = '';

    /** @var array $choices Array holding any choices for this question. */
    public $choices     = [];

    /** @var array $dependencies Array holding any dependencies for this question. */
    public $dependencies = [];

    /** @var string $responsetable The table name for responses. */
    public $responsetable = '';

    /** @var int $length The length field. */
    public $length      = 0;

    /** @var int $precise The precision field. */
    public $precise     = 0;

    /** @var int $position Position in the questionnaire */
    public $position    = 0;

    /** @var string $content The question's content. */
    public $content     = '';

    /** @var string $allchoices The list of all question's choices. */
    public $allchoices  = '';

    /** @var boolean $required The required flag. */
    public $required    = 'n';

    /** @var boolean $deleted The deleted flag. */
    public $deleted     = 'n';

    /** @var array $qtypenames List of all question names. */
    private static $qtypenames = [
        QUESYESNO => 'yesno',
        QUESTEXT => 'text',
        QUESESSAY => 'essay',
        QUESRADIO => 'radio',
        QUESCHECK => 'check',
        QUESDROP => 'drop',
        QUESRATE => 'rate',
        QUESDATE => 'date',
        QUESNUMERIC => 'numerical',
        QUESPAGEBREAK => 'pagebreak',
        QUESSECTIONTEXT => 'sectiontext'
    ];

    /** @var array $notifications Array of extra messages for display purposes. */
    private $notifications = [];

    // Class Methods.

    /**
     * The class constructor
     *
     */
    public function __construct($id = 0, $question = null, $context = null, $params = []) {
        global $DB;
        static $qtypes = null;

        if ($qtypes === null) {
            $qtypes = $DB->get_records('questionnaire_question_type', [], 'typeid',
                                       'typeid, type, has_choices, response_table');
        }

        if ($id) {
            $question = $DB->get_record('questionnaire_question', ['id' => $id]);
        }

        if (is_object($question)) {
            $this->id = $question->id;
            $this->survey_id = $question->survey_id;
            $this->name = $question->name;
            $this->length = $question->length;
            $this->precise = $question->precise;
            $this->position = $question->position;
            $this->content = $question->content;
            $this->required = $question->required;
            $this->deleted = $question->deleted;

            $this->type_id = $question->type_id;
            $this->type = $qtypes[$this->type_id]->type;
            $this->responsetable = $qtypes[$this->type_id]->response_table;
            if ($qtypes[$this->type_id]->has_choices == 'y') {
                $this->get_choices();
            }
            // Added for dependencies.
            $this->get_dependencies();
        }
        $this->context = $context;

        foreach ($params as $property => $value) {
            $this->$property = $value;
        }

        if ($respclass = $this->responseclass()) {
            $this->response = new $respclass($this);
        }
    }

    /**
     * Short name for this question type - no spaces, etc..
     * @return string
     */
    abstract public function helpname();

    /**
     * Build a question from data.
     * @var int $qtype The question type code.
     * @var int|array|object $qdata Either the id of the record, or a structure containing the question data, or null.
     * @var object $context The context for the question.
     * @return A question object.
     */
    static public function question_builder($qtype, $qdata = null, $context = null) {
        $qclassname = '\\mod_questionnaire\\question\\'.self::qtypename($qtype);
        $qid = 0;
        if (!empty($qdata) && is_array($qdata)) {
            $qdata = (object)$qdata;
        } else if (!empty($qdata) && is_int($qdata)) {
            $qid = $qdata;
        }
        return new $qclassname($qid, $qdata, $context, ['type_id' => $qtype]);
    }

    /**
     * Return the different question type names.
     * @return array
     */
    static public function qtypename($qtype) {
        if (array_key_exists($qtype, self::$qtypenames)) {
            return self::$qtypenames[$qtype];
        } else {
            return('');
        }
    }

    /**
     * Override and return true if the question has choices.
     */
    public function has_choices() {
        return false;
    }

    private function get_choices() {
        global $DB;

        if ($choices = $DB->get_records('questionnaire_quest_choice', ['question_id' => $this->id], 'id ASC')) {
            foreach ($choices as $choice) {
                $this->choices[$choice->id] = new \stdClass();
                $this->choices[$choice->id]->content = $choice->content;
                $this->choices[$choice->id]->value = $choice->value;
            }
        } else {
            $this->choices = [];
        }
    }

    /**
     * Return true if this question has been marked as required.
     * @return boolean
     */
    public function required() {
        return ($this->required == 'y');
    }

    /**
     * Return true if the question has defined dependencies.
     * @return boolean
     */
    public function has_dependencies() {
        return !empty($this->dependencies);
    }

    /**
     * Override this and return true if the question type allows dependent questions.
     * @return boolean
     */
    public function allows_dependents() {
        return false;
    }

    private function get_dependencies() {
        global $DB;

        $this->dependencies = [];
        $dependencies = $DB->get_records('questionnaire_dependency',
            ['questionid' => $this->id , 'surveyid' => $this->survey_id], 'id ASC');
        foreach ($dependencies as $dependency) {
            $this->dependencies[$dependency->id] = new \stdClass();
            $this->dependencies[$dependency->id]->dependquestionid = $dependency->dependquestionid;
            $this->dependencies[$dependency->id]->dependchoiceid = $dependency->dependchoiceid;
            $this->dependencies[$dependency->id]->dependlogic = $dependency->dependlogic;
            $this->dependencies[$dependency->id]->dependandor = $dependency->dependandor;
        }
    }

    /**
     * Returns an array of dependency options for the question as an array of id value / display value pairs. Override in specific
     * question types that support this differently.
     * @return array An array of valid pair options.
     */
    protected function get_dependency_options() {
        $options = [];
        if ($this->allows_dependents() && $this->has_choices()) {
            foreach ($this->choices as $key => $choice) {
                $contents = questionnaire_choice_values($choice->content);
                if (!empty($contents->modname)) {
                    $choice->content = $contents->modname;
                } else if (!empty($contents->title)) { // Must be an image; use its title for the dropdown list.
                    $choice->content = $contents->title;
                } else {
                    $choice->content = $contents->text;
                }
                $options[$this->id . ',' . $key] = $this->name . '->' . $choice->content;
            }
        }
        return $options;
    }

    /**
     * Return true if all dependencies or this question have been fulfilled, or there aren't any.
     * @param int $rid The response ID to check.
     * @param array $questions An array containing all possible parent question objects.
     * @return bool
     */
    public function dependency_fulfilled($rid, $questions) {
        if (!$this->has_dependencies()) {
            $fulfilled = true;
        } else {
            foreach ($this->dependencies as $dependency) {
                $choicematches = $questions[$dependency->dependquestionid]->response_has_choice($rid, $dependency->dependchoiceid);

                // Note: dependencies are sorted, first all and-dependencies, then or-dependencies.
                if ($dependency->dependandor == 'and') {
                    $dependencyandfulfilled = false;
                    // This answer given.
                    if (($dependency->dependlogic == 1) && $choicematches) {
                        $dependencyandfulfilled = true;
                    }

                    // This answer NOT given.
                    if (($dependency->dependlogic == 0) && !$choicematches) {
                        $dependencyandfulfilled = true;
                    }

                    // Something mandatory not fulfilled? Stop looking and continue to next question.
                    if ($dependencyandfulfilled == false) {
                        break;
                    }

                    // In case we have no or-dependencies.
                    $dependencyorfulfilled = true;
                }

                // Note: dependencies are sorted, first all and-dependencies, then or-dependencies.
                if ($dependency->dependandor == 'or') {
                    $dependencyorfulfilled = false;
                    // To reach this point, the and-dependencies have all been fultilled or do not exist, so set them ok.
                    $dependencyandfulfilled = true;
                    // This answer given
                    if (($dependency->dependlogic == 1) && $choicematches) {
                        $dependencyorfulfilled = true;
                    }

                    // This answer NOT given
                    if (($dependency->dependlogic == 0) && !$choicematches) {
                        $dependencyorfulfilled = true;
                    }

                    // Something fulfilled? A single match is sufficient so continue to next question.
                    if ($dependencyorfulfilled == true) {
                        break;
                    }
                }

            }
            $fulfilled = ($dependencyandfulfilled && $dependencyorfulfilled);
        }
        return $fulfilled;
    }

    public function response_table() {
        return $this->response->response_table();
    }

    /**
     * Return true if the specified response for this question contains the specified choice.
     * @param $rid
     * @param $choiceid
     * @return bool
     */
    public function response_has_choice($rid, $choiceid) {
        global $DB;
        $choiceval = $this->response->transform_choiceid($choiceid);
        return $DB->record_exists($this->response_table(),
            ['response_id' => $rid, 'question_id' => $this->id, 'choice_id' => $choiceval]);
    }

    /**
     * Insert response data method.
     */
    public function insert_response($rid, $val) {
        if (isset ($this->response) && is_object($this->response) &&
            is_subclass_of($this->response, '\\mod_questionnaire\\response\\base')) {
            return $this->response->insert_response($rid, $val);
        } else {
            return false;
        }
    }

    /**
     * Get results data method.
     */
    public function get_results($rids = false) {
        if (isset ($this->response) && is_object($this->response) &&
            is_subclass_of($this->response, '\\mod_questionnaire\\response\\base')) {
            return $this->response->get_results($rids);
        } else {
            return false;
        }
    }

    /**
     * Display results method.
     */
    public function display_results($rids=false, $sort='', $anonymous=false) {
        if (isset ($this->response) && is_object($this->response) &&
            is_subclass_of($this->response, '\\mod_questionnaire\\response\\base')) {
            return $this->response->display_results($rids, $sort, $anonymous);
        } else {
            return false;
        }
    }

    /**
     * Add a notification.
     * @param string $message
     */
    public function add_notification($message) {
        $this->notifications[] = $message;
    }

    /**
     * Get any notifications.
     * @return array | boolean The notifications array or false.
     */
    public function get_notifications() {
        if (empty($this->notifications)) {
            return false;
        } else {
            return $this->notifications;
        }
    }

    /**
     * Each question type must define its response class.
     *
     * @return object The response object based off of questionnaire_response_base.
     *
     */
    abstract protected function responseclass();

    /**
     * True if question type allows responses.
     */
    public function supports_responses() {
        return !empty($this->responseclass());
    }

    /**
     * True if question type supports feedback options. False by default.
     */
    public function supports_feedback() {
        return false;
    }

    /**
     * True if question type supports feedback scores and weights. Same as supports_feedback() by default.
     */
    public function supports_feedback_scores() {
        return $this->supports_feedback();
    }

    /**
     * True if the question supports feedback and has valid settings for feedback. Override if the default logic is not enough.
     */
    public function valid_feedback() {
        if ($this->supports_feedback() && $this->has_choices() && $this->required()) {
            foreach ($this->choices as $choice) {
                if ($choice->value != null) {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Provide the feedback scores for all requested response id's. This should be provided only by questions that provide feedback.
     * @param array $rids
     * @return array | boolean
     */
    public function get_feedback_scores(array $rids) {
        if ($this->valid_feedback() && isset($this->response) && is_object($this->response) &&
            is_subclass_of($this->response, '\\mod_questionnaire\\response\\base')) {
            return $this->response->get_feedback_scores($rids);
        } else {
            return false;
        }
    }

    /**
     * Get the maximum score possible for feedback if appropriate. Override if default behaviour is not correct.
     * @return int | boolean
     */
    public function get_feedback_maxscore() {
        if ($this->valid_feedback()) {
            $maxscore = 0;
            foreach ($this->choices as $choice) {
                if (isset($choice->value) && ($choice->value != null)) {
                    if ($choice->value > $maxscore) {
                        $maxscore = $choice->value;
                    }
                }
            }
        } else {
            $maxscore = false;
        }
        return $maxscore;
    }

    /**
     * Check question's form data for complete response.
     *
     * @param object $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_complete($responsedata) {
        return !($this->required() && ($this->deleted == 'n') &&
                 (!isset($responsedata->{'q'.$this->id}) || $responsedata->{'q'.$this->id} == ''));
    }

    /**
     * Check question's form data for valid response. Override this if type has specific format requirements.
     *
     * @param object $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_valid($responsedata) {
        return true;
    }

    /**
     * Update data record from object or optional question data.
     *
     * @param object $questionrecord An object with all updated question record data.
     * @param boolean $updatechoices True if choices should also be updated.
     */
    public function update($questionrecord = null, $updatechoices = true) {
        global $DB;

        if ($questionrecord === null) {
            $questionrecord = new \stdClass();
            $questionrecord->id = $this->id;
            $questionrecord->survey_id = $this->survey_id;
            $questionrecord->name = $this->name;
            $questionrecord->type_id = $this->type_id;
            $questionrecord->result_id = $this->result_id;
            $questionrecord->length = $this->length;
            $questionrecord->precise = $this->precise;
            $questionrecord->position = $this->position;
            $questionrecord->content = $this->content;
            $questionrecord->required = $this->required;
            $questionrecord->deleted = $this->deleted;
            $questionrecord->dependquestion = $this->dependquestion;
            $questionrecord->dependchoice = $this->dependchoice;
        } else {
            // Make sure the "id" field is this question's.
            if (isset($this->qid) && ($this->qid > 0)) {
                $questionrecord->id = $this->qid;
            } else {
                $questionrecord->id = $this->id;
            }
        }
        $DB->update_record('questionnaire_question', $questionrecord);

        if ($updatechoices && $this->has_choices()) {
            $this->update_choices();
        }
    }

    /**
     * Add the question to the database from supplied arguments.
     * @param object $questionrecord The required data for adding the question.
     * @param array $choicerecords An array of choice records with 'content' and 'value' properties.
     * @param boolean $calcposition Whether or not to calculate the next available position in the survey.
     */
    public function add($questionrecord, array $choicerecords = null, $calcposition = true) {
        global $DB;

        // Create new question.
        if ($calcposition) {
            // Set the position to the end.
            $sql = 'SELECT MAX(position) as maxpos '.
                   'FROM {questionnaire_question} '.
                   'WHERE survey_id = ? AND deleted = ?';
            $params = ['survey_id' => $questionrecord->survey_id, 'deleted' => 'n'];
            if ($record = $DB->get_record_sql($sql, $params)) {
                $questionrecord->position = $record->maxpos + 1;
            } else {
                $questionrecord->position = 1;
            }
        }

        // Make sure we add all necessary data.
        if (!isset($questionrecord->type_id) || empty($questionrecord->type_id)) {
            $questionrecord->type_id = $this->type_id;
        }

        $this->qid = $DB->insert_record('questionnaire_question', $questionrecord);

        if ($this->has_choices() && !empty($choicerecords)) {
            foreach ($choicerecords as $choicerecord) {
                $choicerecord->question_id = $this->qid;
                $this->add_choice($choicerecord);
            }
        }
    }

    public function update_choices() {
        global $DB;

        $retvalue = true;
        if ($this->has_choices() && isset($this->choices)) {
            // Need to fix this messed-up qid/id issue.
            if (isset($this->qid) && ($this->qid > 0)) {
                $qid = $this->qid;
            } else {
                $qid = $this->id;
            }
            foreach ($this->choices as $key => $choice) {
                $choicerecord = new \stdClass();
                $choicerecord->id = $key;
                $choicerecord->question_id = $qid;
                $choicerecord->content = $choice->content;
                $choicerecord->value = $choice->value;
                $retvalue &= $this->update_choice($choicerecord);
            }
        }
        return $retvalue;
    }

    public function update_choice($choicerecord) {
        global $DB;
        return $DB->update_record('questionnaire_quest_choice', $choicerecord);
    }

    public function add_choice($choicerecord) {
        global $DB;
        $retvalue = true;
        if ($cid = $DB->insert_record('questionnaire_quest_choice', $choicerecord)) {
            $this->choices[$cid] = new \stdClass();
            $this->choices[$cid]->content = $choicerecord->content;
            $this->choices[$cid]->value = isset($choicerecord->value) ? $choicerecord->value : null;
        } else {
            $retvalue = false;
        }
        return $retvalue;
    }

    /**
     * Delete the choice from the question object and the database.
     *
     * @param integer|object $choice Either the integer id of the choice, or the choice record.
     */
    public function delete_choice($choice) {
        global $DB;

        $retvalue = true;
        if (is_int($choice)) {
            $cid = $choice;
        } else {
            $cid = $choice->id;
        }
        if ($DB->delete_records('questionnaire_quest_choice', ['id' => $cid])) {
            unset($this->choices[$cid]);
        } else {
            $retvalue = false;
        }
        return $retvalue;
    }

    public function update_dependency($dependencyrecord) {
        global $DB;
        return $DB->update_record('questionnaire_dependency', $dependencyrecord);
    }

    public function add_dependency($dependencyrecord) {
        global $DB;

        $retvalue = true;
        if ($did = $DB->insert_record('questionnaire_dependency', $dependencyrecord)) {
            $this->dependencies[$did] = new \stdClass();
            $this->dependencies[$did]->dependquestionid = $dependencyrecord->dependquestionid;
            $this->dependencies[$did]->dependchoiceid = $dependencyrecord->dependchoiceid;
            $this->dependencies[$did]->dependlogic = $dependencyrecord->dependlogic;
            $this->dependencies[$did]->dependandor = $dependencyrecord->dependandor;
        } else {
            $retvalue = false;
        }
        return $retvalue;
    }

    /**
     * Delete the dependency from the question object and the database.
     *
     * @param integer|object $dependency Either the integer id of the dependency, or the dependency record.
     */
    public function delete_dependency($dependency) {
        global $DB;

        $retvalue = true;
        if (is_int($dependency)) {
            $did = $dependency;
        } else {
            $did = $dependency->id;
        }
        if ($DB->delete_records('questionnaire_dependency', ['id' => $did])) {
            unset($this->dependencies[$did]);
        } else {
            $retvalue = false;
        }
        return $retvalue;
    }

    /**
     * Set the question required field in the object and database.
     *
     * @param boolean $required Whether question should be required or not.
     */
    public function set_required($required) {
        global $DB;
        $rval = $required ? 'y' : 'n';
        // Need to fix this messed-up qid/id issue.
        if (isset($this->qid) && ($this->qid > 0)) {
            $qid = $this->qid;
        } else {
            $qid = $this->id;
        }
        $this->required = $rval;
        return $DB->set_field('questionnaire_question', 'required', $rval, ['id' => $qid]);
    }

    /**
     * Question specific display method.
     *
     * @param object $formdata
     * @param array $descendantdata
     * @param boolean $blankquestionnaire
     *
     */
    abstract protected function question_survey_display($formdata, $descendantsdata, $blankquestionnaire);

    /**
     * Question specific response display method.
     *
     * @param object $data
     * @param integer $qnum
     *
     */
    abstract protected function response_survey_display($data);

    /**
     * Override and return a form template if provided. Output of question_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function question_template() {
        return false;
    }

    /**
     * Override and return a form template if provided. Output of response_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function response_template() {
        return false;
    }

    /**
     * Get the output for question renderers / templates.
     * @param object $formdata
     * @param array $dependants Array of all questions/choices depending on this question.
     * @param integer $qnum
     * @param boolean $blankquestionnaire
     */
    public function question_output($formdata, $dependants=[], $qnum='', $blankquestionnaire) {
        $pagetags = $this->questionstart_survey_display($qnum, $formdata);
        $pagetags->qformelement = $this->question_survey_display($formdata, $dependants, $blankquestionnaire);
        return $pagetags;
    }

    /**
     * Get the output for question renderers / templates.
     * @param object $formdata
     * @param string $descendantdata
     * @param integer $qnum
     * @param boolean $blankquestionnaire
     */
    public function response_output($data, $qnum='') {
        $pagetags = $this->questionstart_survey_display($qnum, $data);
        $pagetags->qformelement = $this->response_survey_display($data);
        return $pagetags;
    }

    /**
     * Get the output for the start of the questions in a survey.
     * @param integer $qnum
     * @param object $formdata
     */
    public function questionstart_survey_display($qnum, $formdata='') {
        global $OUTPUT, $SESSION, $questionnaire, $PAGE;

        $pagetags = new \stdClass();
        $currenttab = $SESSION->questionnaire->current_tab;
        $pagetype = $PAGE->pagetype;
        $skippedquestion = false;
        $skippedclass = '';
        $autonum = $questionnaire->autonum;
        // If no questions autonumbering.
        $nonumbering = false;
        if ($autonum != 1 && $autonum != 3) {
            $qnum = '';
            $nonumbering = true;
        }
        // If we are on report page and this questionnaire has dependquestions and this question was skipped.
        if (($pagetype == 'mod-questionnaire-myreport' || $pagetype == 'mod-questionnaire-report') &&
            ($nonumbering == false) && !empty($formdata) && !empty($this->dependencies) &&
            !array_key_exists('q'.$this->id, $formdata)) {
            $skippedquestion = true;
            $skippedclass = ' unselected';
            $qnum = '<span class="'.$skippedclass.'">('.$qnum.')</span>';
        }
        // In preview mode, hide children questions that have not been answered.
        // In report mode, If questionnaire is set to no numbering,
        // also hide answers to questions that have not been answered.
        $displayclass = 'qn-container';
        if ($pagetype == 'mod-questionnaire-preview' || ($nonumbering &&
            ($currenttab == 'mybyresponse' || $currenttab == 'individualresp'))) {
            // This needs to be done to ensure all dependency data is loaded.
            // TODO - Perhaps this should be a function called by the questionnaire after it loads all questions?
            $questionnaire->load_parents($this);
            // Want this to come from the renderer, meaning we need $questionnaire.
            $pagetags->dependencylist = $questionnaire->renderer->get_dependency_html($this->id, $this->dependencies);
        }

        $pagetags->fieldset = (object)['id' => $this->id, 'class' => $displayclass];

        // Do not display the info box for the label question type.
        if ($this->type_id != QUESSECTIONTEXT) {
            if (!$nonumbering) {
                $pagetags->qnum = $qnum;
            }
            $required = '';
            if ($this->required()) {
                $required = html_writer::start_tag('div', ['class' => 'accesshide']);
                $required .= get_string('required', 'questionnaire');
                $required .= html_writer::end_tag('div');
                $required .= html_writer::empty_tag('img', ['class' => 'req', 'title' => get_string('required', 'questionnaire'),
                    'alt' => get_string('required', 'questionnaire'), 'src' => $OUTPUT->image_url('req')]);
            }
            $pagetags->required = $required; // Need to replace this with better renderer / template?
        }
        // If question text is "empty", i.e. 2 non-breaking spaces were inserted, empty it.
        if ($this->content == '<p>  </p>') {
            $this->content = '';
        }
        $pagetags->skippedclass = $skippedclass;
        if ($this->type_id == QUESNUMERIC || $this->type_id == QUESTEXT) {
            $pagetags->label = (object)['for' => self::qtypename($this->type_id) . $this->id];
        } else if ($this->type_id == QUESDROP) {
            $pagetags->label = (object)['for' => self::qtypename($this->type_id) . $this->name];
        } else if ($this->type_id == QUESESSAY) {
            $pagetags->label = (object)['for' => 'edit-q' . $this->id];
        }
        $options = ['noclean' => true, 'para' => false, 'filter' => true, 'context' => $this->context, 'overflowdiv' => true];
        $content = format_text(file_rewrite_pluginfile_urls($this->content, 'pluginfile.php',
            $this->context->id, 'mod_questionnaire', 'question', $this->id), FORMAT_HTML, $options);
        $pagetags->qcontent = $content;

        return $pagetags;
    }

    private function response_check_required ($data) {
        // JR check all question types
        if ($this->type_id == QUESRATE) { // Rate is a special case.
            foreach ($this->choices as $cid => $choice) {
                $str = 'q'."{$this->id}_$cid";
                if (isset($data->$str)) {
                    return ('&nbsp;');
                }
            }
        }
        if ($this->required() &&  empty($data->{'q'.$this->id}) ) {
            return ('*');
        } else {
            return ('&nbsp;');
        }
    }

    // This section contains functions for editing the specific question types.
    // There are required methods that must be implemented, and helper functions that can be used.

    // Required functions that can be overridden by the question type.

    /**
     * Override this, or any of the internal methods, to provide specific form data for editing the question type.
     * The structure of the elements here is the default layout for the question form.
     * @param edit_question_form $form The main moodleform object.
     * @param questionnaire $questionnaire The questionnaire being edited.
     * @return bool
     */
    public function edit_form(edit_question_form $form, questionnaire $questionnaire) {
        $mform =& $form->_form;
        $this->form_header($mform);
        $this->form_name($mform);
        $this->form_required($mform);
        $this->form_length($mform);
        $this->form_precise($mform);
        $this->form_question_text($mform, $form->_customdata['modcontext']);

        if ($this->has_choices()) {
            $this->allchoices = $this->form_choices($mform, $this->choices);
        }

        // Added for advanced dependencies, parameter $editformobject is needed to use repeat_elements.
        if ($questionnaire->navigate > 0) {
            $this->form_dependencies($form, $questionnaire->questions);
        }

        // Exclude the save/cancel buttons from any collapsing sections.
        $mform->closeHeaderBefore('buttonar');

        // Hidden fields.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'qid', 0);
        $mform->setType('qid', PARAM_INT);
        $mform->addElement('hidden', 'sid', 0);
        $mform->setType('sid', PARAM_INT);
        $mform->addElement('hidden', 'type_id', $this->type_id);
        $mform->setType('type_id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'question');
        $mform->setType('action', PARAM_ALPHA);

        // Buttons.
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        if (isset($this->qid)) {
            $buttonarray[] = &$mform->createElement('submit', 'makecopy', get_string('saveasnew', 'questionnaire'));
        }
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);

        return true;
    }

    protected function form_header(\MoodleQuickForm $mform, $helpname = '') {
        // Display different messages for new question creation and existing question modification.
        if (isset($this->qid) && !empty($this->qid)) {
            $header = get_string('editquestion', 'questionnaire', questionnaire_get_type($this->type_id));
        } else {
            $header = get_string('addnewquestion', 'questionnaire', questionnaire_get_type($this->type_id));
        }
        if (empty($helpname)) {
            $helpname = $this->helpname();
        }

        $mform->addElement('header', 'questionhdredit', $header);
        $mform->addHelpButton('questionhdredit', $helpname, 'questionnaire');
    }

    protected function form_name(\MoodleQuickForm $mform) {
        $mform->addElement('text', 'name', get_string('optionalname', 'questionnaire'),
                        ['size' => '30', 'maxlength' => '30']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addHelpButton('name', 'optionalname', 'questionnaire');
        return $mform;
    }

    protected function form_required(\MoodleQuickForm $mform) {
        $reqgroup = [];
        $reqgroup[] =& $mform->createElement('radio', 'required', '', get_string('yes'), 'y');
        $reqgroup[] =& $mform->createElement('radio', 'required', '', get_string('no'), 'n');
        $mform->addGroup($reqgroup, 'reqgroup', get_string('required', 'questionnaire'), ' ', false);
        $mform->addHelpButton('reqgroup', 'required', 'questionnaire');
        return $mform;
    }

    protected function form_length(\MoodleQuickForm $mform, $helpname = '') {
        self::form_length_text($mform, $helpname);
    }

    protected function form_precise(\MoodleQuickForm $mform, $helpname = '') {
        self::form_precise_text($mform, $helpname);
    }

    /**
     * @param \MoodleQuickForm $mform The moodle form to add elements to.
     * @param $questionnaire
     * @param $editquestionformobject
     * @return bool
     */
    protected function form_dependencies($form, $questions) {
        // Create a new area for multiple dependencies.
        $mform = $form->_form;
        $position = ($this->position !== 0) ? $this->position : count($questions) + 1;
        $dependencies = [];
        $dependencies[''][0] = get_string('choosedots');
        foreach ($questions as $question) {
            if (($question->position < $position) && !empty($question->name) &&
                !empty($dependopts = $question->get_dependency_options())) {
                $dependencies[$question->name] = $dependopts;
            }
        }

        $children = [];
        if (isset($this->qid)) {
            // Use also for the delete dialogue later.
            foreach ($questions as $questionlistitem) {
                if ($questionlistitem->has_dependencies()) {
                    foreach ($questionlistitem->dependencies as $key => $outerdependencies) {
                        if ($outerdependencies->dependquestionid == $this->qid) {
                            $children[$key] = $outerdependencies;
                        }
                    }
                }
            }
        }

        if (count($dependencies) > 1) {
            $mform->addElement('header', 'dependencies_hdr', get_string('dependencies', 'questionnaire'));
            $mform->setExpanded('dependencies_hdr');
            $mform->closeHeaderBefore('qst_and_choices_hdr');

            $dependenciescountand = 0;
            $dependenciescountor = 0;

            foreach ($this->dependencies as $dependency) {
                if ($dependency->dependandor == "and") {
                    $dependenciescountand++;
                } else if ($dependency->dependandor == "or") {
                    $dependenciescountor++;
                }
            }

            /* I decided to allow changing dependencies of parent questions, because forcing the editor to remove dependencies
             * bottom up, starting at the lowest child question is a pain for large questionnaires.
             * So the following "if" becomes the default and the else-branch is completely commented.
             * TODO Since the best way to get the list of child questions is currently to click on delete (and choose not to
             * delete), one might consider to list the child questions in addition here.
             */

            // Area for "must"-criteria.
            $mform->addElement('static', 'mandatory', '',
                '<div class="dimmed_text">' . get_string('mandatory', 'questionnaire') . '</div>');
            $selectand = $mform->createElement('select', 'dependlogic_and', get_string('condition', 'questionnaire'),
                [get_string('answernotgiven', 'questionnaire'), get_string('answergiven', 'questionnaire')]);
            $selectand->setSelected('1');
            $groupitemsand = [];
            $groupitemsand[] =& $mform->createElement('selectgroups', 'dependquestions_and',
                get_string('parent', 'questionnaire'), $dependencies);
            $groupitemsand[] =& $selectand;
            $groupand = $mform->createElement('group', 'selectdependencies_and', get_string('dependquestion', 'questionnaire'),
                $groupitemsand, ' ', false);
            $form->repeat_elements([$groupand], $dependenciescountand + 1, [],
                'numdependencies_and', 'adddependencies_and', 2, null, true);

            // Area for "can"-criteria.
            $mform->addElement('static', 'optional', '',
                '<div class="dimmed_text">' . get_string('optional', 'questionnaire') . '</div>');
            $selector = $mform->createElement('select', 'dependlogic_or', get_string('condition', 'questionnaire'),
                [get_string('answernotgiven', 'questionnaire'), get_string('answergiven', 'questionnaire')]);
            $selector->setSelected('1');
            $groupitemsor = [];
            $groupitemsor[] =& $mform->createElement('selectgroups', 'dependquestions_or',
                get_string('parent', 'questionnaire'), $dependencies);
            $groupitemsor[] =& $selector;
            $groupor = $mform->createElement('group', 'selectdependencies_or', get_string('dependquestion', 'questionnaire'),
                $groupitemsor, ' ', false);
            $form->repeat_elements([$groupor], $dependenciescountor + 1, [], 'numdependencies_or',
                'adddependencies_or', 2, null, true);
        }
        return true;
    }

    protected function form_question_text(\MoodleQuickForm $mform, $context) {
        $editoroptions = ['maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true, 'context' => $context];
        $mform->addElement('editor', 'content', get_string('text', 'questionnaire'), null, $editoroptions);
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', null, 'required', null, 'client');
        return $mform;
    }

    protected function form_choices(\MoodleQuickForm $mform, array $choices, $helpname = '') {
        $numchoices = count($choices);
        $allchoices = '';
        foreach ($choices as $choice) {
            if (!empty($allchoices)) {
                $allchoices .= "\n";
            }
            $allchoices .= $choice->content;
        }
        if (empty($helpname)) {
            $helpname = $this->helpname();
        }

        $mform->addElement('html', '<div class="qoptcontainer">');
        $options = ['wrap' => 'virtual', 'class' => 'qopts'];
        $mform->addElement('textarea', 'allchoices', get_string('possibleanswers', 'questionnaire'), $options);
        $mform->setType('allchoices', PARAM_RAW);
        $mform->addRule('allchoices', null, 'required', null, 'client');
        $mform->addHelpButton('allchoices', $helpname, 'questionnaire');
        $mform->addElement('html', '</div>');
        $mform->addElement('hidden', 'num_choices', $numchoices);
        $mform->setType('num_choices', PARAM_INT);
        return $allchoices;
    }

    // Helper functions for commonly used editing functions.

    static public function form_length_hidden(\MoodleQuickForm $mform, $value = 0) {
        $mform->addElement('hidden', 'length', $value);
        $mform->setType('length', PARAM_INT);
        return $mform;
    }

    static public function form_length_text(\MoodleQuickForm $mform, $helpname = '', $value = 0) {
        $mform->addElement('text', 'length', get_string($helpname, 'questionnaire'), ['size' => '1'], $value);
        $mform->setType('length', PARAM_INT);
        if (!empty($helpname)) {
            $mform->addHelpButton('length', $helpname, 'questionnaire');
        }
        return $mform;
    }

    static public function form_precise_hidden(\MoodleQuickForm $mform, $value = 0) {
        $mform->addElement('hidden', 'precise', $value);
        $mform->setType('precise', PARAM_INT);
        return $mform;
    }

    static public function form_precise_text(\MoodleQuickForm $mform, $helpname = '', $value = 0) {
        $mform->addElement('text', 'precise', get_string($helpname, 'questionnaire'), ['size' => '1']);
        $mform->setType('precise', PARAM_INT);
        if (!empty($helpname)) {
            $mform->addHelpButton('precise', $helpname, 'questionnaire');
        }
        return $mform;
    }

    /**
     * Create and update question data from the forms.
     */
    public function form_update($formdata, $questionnaire) {
        global $DB;

        $this->form_preprocess_data($formdata);
        if (!empty($formdata->qid)) {

            // Update existing question.
            // Handle any attachments in the content.
            $formdata->itemid  = $formdata->content['itemid'];
            $formdata->format  = $formdata->content['format'];
            $formdata->content = $formdata->content['text'];
            $formdata->content = file_save_draft_area_files($formdata->itemid, $questionnaire->context->id, 'mod_questionnaire',
                'question', $formdata->qid, ['subdirs' => true], $formdata->content);

            $fields = ['name', 'type_id', 'length', 'precise', 'required', 'content'];
            $questionrecord = new \stdClass();
            $questionrecord->id = $formdata->qid;
            foreach ($fields as $f) {
                if (isset($formdata->$f)) {
                    $questionrecord->$f = trim($formdata->$f);
                }
            }

            $this->update($questionrecord, false);

            if ($questionnaire->has_dependencies()) {
                questionnaire_check_page_breaks($questionnaire);
            }
        } else {
            // Create new question:
            // Need to update any image content after the question is created, so create then update the content.
            $formdata->survey_id = $formdata->sid;
            $fields = ['survey_id', 'name', 'type_id', 'length', 'precise', 'required', 'position'];
            $questionrecord = new \stdClass();
            foreach ($fields as $f) {
                if (isset($formdata->$f)) {
                    $questionrecord->$f = trim($formdata->$f);
                }
            }
            $questionrecord->content = '';

            $this->add($questionrecord);

            // Handle any attachments in the content.
            $formdata->itemid  = $formdata->content['itemid'];
            $formdata->format  = $formdata->content['format'];
            $formdata->content = $formdata->content['text'];
            $content           = file_save_draft_area_files($formdata->itemid, $questionnaire->context->id, 'mod_questionnaire',
                'question', $this->qid, ['subdirs' => true], $formdata->content);
            $DB->set_field('questionnaire_question', 'content', $content, ['id' => $this->qid]);
        }

        if ($this->has_choices()) {
            // Now handle any choice updates.
            $cidx = 0;
            if (isset($this->choices) && !isset($formdata->makecopy)) {
                $oldcount = count($this->choices);
                $echoice = reset($this->choices);
                $ekey = key($this->choices);
            } else {
                $oldcount = 0;
            }

            $newchoices = explode("\n", $formdata->allchoices);
            $nidx = 0;
            $newcount = count($newchoices);

            while (($nidx < $newcount) && ($cidx < $oldcount)) {
                if ($newchoices[$nidx] != $echoice->content) {
                    $choicerecord = new \stdClass();
                    $choicerecord->id = $ekey;
                    $choicerecord->question_id = $this->qid;
                    $choicerecord->content = trim($newchoices[$nidx]);
                    $r = preg_match_all("/^(\d{1,2})(=.*)$/", $newchoices[$nidx], $matches);
                    // This choice has been attributed a "score value" OR this is a rate question type.
                    if ($r) {
                        $newscore = $matches[1][0];
                        $choicerecord->value = $newscore;
                    } else {     // No score value for this choice.
                        $choicerecord->value = null;
                    }
                    $this->update_choice($choicerecord);
                }
                $nidx++;
                $echoice = next($this->choices);
                $ekey = key($this->choices);
                $cidx++;
            }

            while ($nidx < $newcount) {
                // New choices...
                $choicerecord = new \stdClass();
                $choicerecord->question_id = $this->qid;
                $choicerecord->content = trim($newchoices[$nidx]);
                $r = preg_match_all("/^(\d{1,2})(=.*)$/", $choicerecord->content, $matches);
                // This choice has been attributed a "score value" OR this is a rate question type.
                if ($r) {
                    $choicerecord->value = $matches[1][0];
                }
                $this->add_choice($choicerecord);
                $nidx++;
            }

            while ($cidx < $oldcount) {
                end($this->choices);
                $ekey = key($this->choices);
                $this->delete_choice($ekey);
                $cidx++;
            }
        }

        // Now handle the dependencies the same way as choices.
        // Shouldn't the MOODLE-API provide this case of insert/update/delete?.
        // First handle dependendies updates.
        if (!isset($formdata->fixed_deps)) {
            if ($this->has_dependencies() && !isset($formdata->makecopy)) {
                $oldcount = count($this->dependencies);
                $edependency = reset($this->dependencies);
                $ekey = key($this->dependencies);
            } else {
                $oldcount = 0;
            }

            $cidx = 0;
            $nidx = 0;

            // All 3 arrays in this object have the same length.
            if (isset($formdata->dependquestion)) {
                $newcount = count($formdata->dependquestion);
            } else {
                $newcount = 0;
            }
            while (($nidx < $newcount) && ($cidx < $oldcount)) {
                if ($formdata->dependquestion[$nidx] != $edependency->dependquestionid ||
                    $formdata->dependchoice[$nidx] != $edependency->dependchoiceid ||
                    $formdata->dependlogic_cleaned[$nidx] != $edependency->dependlogic ||
                    $formdata->dependandor[$nidx] != $edependency->dependandor) {

                    $dependencyrecord = new \stdClass();
                    $dependencyrecord->id = $ekey;
                    $dependencyrecord->questionid = $this->qid;
                    $dependencyrecord->surveyid = $this->survey_id;
                    $dependencyrecord->dependquestionid = $formdata->dependquestion[$nidx];
                    $dependencyrecord->dependchoiceid = $formdata->dependchoice[$nidx];
                    $dependencyrecord->dependlogic = $formdata->dependlogic_cleaned[$nidx];
                    $dependencyrecord->dependandor = $formdata->dependandor[$nidx];

                    $this->update_dependency($dependencyrecord);
                }
                $nidx++;
                $edependency = next($this->dependencies);
                $ekey = key($this->dependencies);
                $cidx++;
            }

            while ($nidx < $newcount) {
                // New dependencies.
                $dependencyrecord = new \stdClass();
                $dependencyrecord->questionid = $this->qid;
                $dependencyrecord->surveyid = $formdata->sid;
                $dependencyrecord->dependquestionid = $formdata->dependquestion[$nidx];
                $dependencyrecord->dependchoiceid = $formdata->dependchoice[$nidx];
                $dependencyrecord->dependlogic = $formdata->dependlogic_cleaned[$nidx];
                $dependencyrecord->dependandor = $formdata->dependandor[$nidx];

                $this->add_dependency($dependencyrecord);
                $nidx++;
            }

            while ($cidx < $oldcount) {
                end($this->dependencies);
                $ekey = key($this->dependencies);
                $this->delete_dependency($ekey);
                $cidx++;
            }
        }
    }

    /**
     * Any preprocessing of general data.
     */
    protected function form_preprocess_data($formdata) {
        if ($this->has_choices()) {
            // Eliminate trailing blank lines.
            $formdata->allchoices = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $formdata->allchoices);
            // Trim to eliminate potential trailing carriage return.
            $formdata->allchoices = trim($formdata->allchoices);
            $this->form_preprocess_choicedata($formdata);
        }

        // Dependencies logic does not (yet) need preprocessing, might change with more complex conditions.
        // Check, if entries exist and whether they are not only 0 (form elements created but no value selected).
        if (isset($formdata->dependquestions_and) &&
            !(count(array_keys($formdata->dependquestions_and, 0, true)) == count($formdata->dependquestions_and))) {
            for ($i = 0; $i < count($formdata->dependquestions_and); $i++) {
                $dependency = explode(",", $formdata->dependquestions_and[$i]);

                if ($dependency[0] != 0) {
                    $formdata->dependquestion[] = $dependency[0];
                    $formdata->dependchoice[] = $dependency[1];
                    $formdata->dependlogic_cleaned[] = $formdata->dependlogic_and[$i];
                    $formdata->dependandor[] = "and";
                }
            }
        }

        if (isset($formdata->dependquestions_or) &&
            !(count(array_keys($formdata->dependquestions_or, 0, true)) == count($formdata->dependquestions_or))) {
            for ($i = 0; $i < count($formdata->dependquestions_or); $i++) {
                $dependency = explode(",", $formdata->dependquestions_or[$i]);

                if ($dependency[0] != 0) {
                    $formdata->dependquestion[] = $dependency[0];
                    $formdata->dependchoice[] = $dependency[1];
                    $formdata->dependlogic_cleaned[] = $formdata->dependlogic_or[$i];
                    $formdata->dependandor[] = "or";
                }
            }
        }
        return true;
    }

    /**
     * Override this function for question specific choice preprocessing.
     */
    protected function form_preprocess_choicedata($formdata) {
        if (empty($formdata->allchoices)) {
            error (get_string('enterpossibleanswers', 'questionnaire'));
        }
        return false;
    }

    /**
     * Return all the fields to be used for users in questionnaire sql.
     *
     * @author: Guy Thomas
     * @return string
     */
    protected function user_fields_sql() {
        $userfieldsarr = get_all_user_name_fields();
        $userfieldsarr = array_merge($userfieldsarr, ['username', 'department', 'institution']);
        $userfields = '';
        foreach ($userfieldsarr as $field) {
            $userfields .= $userfields === '' ? '' : ', ';
            $userfields .= 'u.'.$field;
        }
        $userfields .= ', u.id as usrid';
        return $userfields;
    }
}