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
 * Question type class for the calculated question type.
 *
 * @package    qtype
 * @subpackage calculated
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questiontypebase.php');
require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/type/numerical/question.php');


/**
 * The calculated question type.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated extends question_type {
    /** Regular expression that finds the formulas in content. */
    const FORMULAS_IN_TEXT_REGEX = '~\{=([^{}]*(?:\{[^{}]+}[^{}]*)*)\}~';

    const MAX_DATASET_ITEMS = 100;

    public $wizardpagesnumber = 3;

    public function get_question_options($question) {
        // First get the datasets and default options.
        // The code is used for calculated, calculatedsimple and calculatedmulti qtypes.
        global $CFG, $DB, $OUTPUT;
        if (!$question->options = $DB->get_record('question_calculated_options',
                array('question' => $question->id))) {
            $question->options = new stdClass();
            $question->options->synchronize = 0;
            $question->options->single = 0;
            $question->options->answernumbering = 'abc';
            $question->options->shuffleanswers = 0;
            $question->options->correctfeedback = '';
            $question->options->partiallycorrectfeedback = '';
            $question->options->incorrectfeedback = '';
            $question->options->correctfeedbackformat = 0;
            $question->options->partiallycorrectfeedbackformat = 0;
            $question->options->incorrectfeedbackformat = 0;
        }

        if (!$question->options->answers = $DB->get_records_sql("
            SELECT a.*, c.tolerance, c.tolerancetype, c.correctanswerlength, c.correctanswerformat
            FROM {question_answers} a,
                 {question_calculated} c
            WHERE a.question = ?
            AND   a.id = c.answer
            ORDER BY a.id ASC", array($question->id))) {
                return false;
        }

        if ($this->get_virtual_qtype()->name() == 'numerical') {
            $this->get_virtual_qtype()->get_numerical_units($question);
            $this->get_virtual_qtype()->get_numerical_options($question);
        }

        $question->hints = $DB->get_records('question_hints',
                array('questionid' => $question->id), 'id ASC');

        if (isset($question->export_process)&&$question->export_process) {
            $question->options->datasets = $this->get_datasets_for_export($question);
        }
        return true;
    }

    public function get_datasets_for_export($question) {
        global $DB, $CFG;
        $datasetdefs = array();
        if (!empty($question->id)) {
            $sql = "SELECT i.*
                      FROM {question_datasets} d, {question_dataset_definitions} i
                     WHERE d.question = ? AND d.datasetdefinition = i.id";
            if ($records = $DB->get_records_sql($sql, array($question->id))) {
                foreach ($records as $r) {
                    $def = $r;
                    if ($def->category == '0') {
                        $def->status = 'private';
                    } else {
                        $def->status = 'shared';
                    }
                    $def->type = 'calculated';
                    list($distribution, $min, $max, $dec) = explode(':', $def->options, 4);
                    $def->distribution = $distribution;
                    $def->minimum = $min;
                    $def->maximum = $max;
                    $def->decimals = $dec;
                    if ($def->itemcount > 0) {
                        // Get the datasetitems.
                        $def->items = array();
                        if ($items = $this->get_database_dataset_items($def->id)) {
                            $n = 0;
                            foreach ($items as $ii) {
                                $n++;
                                $def->items[$n] = new stdClass();
                                $def->items[$n]->itemnumber = $ii->itemnumber;
                                $def->items[$n]->value = $ii->value;
                            }
                            $def->number_of_items = $n;
                        }
                    }
                    $datasetdefs["1-{$r->category}-{$r->name}"] = $def;
                }
            }
        }
        return $datasetdefs;
    }

    public function save_question_options($question) {
        global $CFG, $DB;

        // Make it impossible to save bad formulas anywhere.
        $this->validate_question_data($question);

        // The code is used for calculated, calculatedsimple and calculatedmulti qtypes.
        $context = $question->context;

        // Calculated options.
        $update = true;
        $options = $DB->get_record('question_calculated_options',
                array('question' => $question->id));
        if (!$options) {
            $update = false;
            $options = new stdClass();
            $options->question = $question->id;
        }
        // As used only by calculated.
        if (isset($question->synchronize)) {
            $options->synchronize = $question->synchronize;
        } else {
            $options->synchronize = 0;
        }
        $options->single = 0;
        $options->answernumbering =  $question->answernumbering;
        $options->shuffleanswers = $question->shuffleanswers;

        foreach (array('correctfeedback', 'partiallycorrectfeedback',
                'incorrectfeedback') as $feedbackname) {
            $options->$feedbackname = '';
            $feedbackformat = $feedbackname . 'format';
            $options->$feedbackformat = 0;
        }

        if ($update) {
            $DB->update_record('question_calculated_options', $options);
        } else {
            $DB->insert_record('question_calculated_options', $options);
        }

        // Get old versions of the objects.
        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        $oldoptions = $DB->get_records('question_calculated',
                array('question' => $question->id), 'answer ASC');

        // Save the units.
        $virtualqtype = $this->get_virtual_qtype();

        $result = $virtualqtype->save_units($question);
        if (isset($result->error)) {
            return $result;
        } else {
            $units = $result->units;
        }

        foreach ($question->answer as $key => $answerdata) {
            if (trim($answerdata) == '') {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer   = '';
                $answer->feedback = '';
                $answer->id       = $DB->insert_record('question_answers', $answer);
            }

            $answer->answer   = trim($answerdata);
            $answer->fraction = $question->fraction[$key];
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];

            $DB->update_record("question_answers", $answer);

            // Set up the options object.
            if (!$options = array_shift($oldoptions)) {
                $options = new stdClass();
            }
            $options->question            = $question->id;
            $options->answer              = $answer->id;
            $options->tolerance           = trim($question->tolerance[$key]);
            $options->tolerancetype       = trim($question->tolerancetype[$key]);
            $options->correctanswerlength = trim($question->correctanswerlength[$key]);
            $options->correctanswerformat = trim($question->correctanswerformat[$key]);

            // Save options.
            if (isset($options->id)) {
                // Reusing existing record.
                $DB->update_record('question_calculated', $options);
            } else {
                // New options.
                $DB->insert_record('question_calculated', $options);
            }
        }

        // Delete old answer records.
        if (!empty($oldanswers)) {
            foreach ($oldanswers as $oa) {
                $DB->delete_records('question_answers', array('id' => $oa->id));
            }
        }

        // Delete old answer records.
        if (!empty($oldoptions)) {
            foreach ($oldoptions as $oo) {
                $DB->delete_records('question_calculated', array('id' => $oo->id));
            }
        }

        $result = $virtualqtype->save_unit_options($question);
        if (isset($result->error)) {
            return $result;
        }

        $this->save_hints($question);

        if (isset($question->import_process)&&$question->import_process) {
            $this->import_datasets($question);
        }
        // Report any problems.
        if (!empty($result->notice)) {
            return $result;
        }
        return true;
    }

    public function import_datasets($question) {
        global $DB;
        $n = count($question->dataset);
        foreach ($question->dataset as $dataset) {
            // Name, type, option.
            $datasetdef = new stdClass();
            $datasetdef->name = $dataset->name;
            $datasetdef->type = 1;
            $datasetdef->options =  $dataset->distribution . ':' . $dataset->min . ':' .
                    $dataset->max . ':' . $dataset->length;
            $datasetdef->itemcount = $dataset->itemcount;
            if ($dataset->status == 'private') {
                $datasetdef->category = 0;
                $todo = 'create';
            } else if ($dataset->status == 'shared') {
                if ($sharedatasetdefs = $DB->get_records_select(
                    'question_dataset_definitions',
                    "type = '1'
                    AND " . $DB->sql_equal('name', '?') . "
                    AND category = ?
                    ORDER BY id DESC ", array($dataset->name, $question->category)
                )) { // So there is at least one.
                    $sharedatasetdef = array_shift($sharedatasetdefs);
                    if ($sharedatasetdef->options ==  $datasetdef->options) {// Identical so use it.
                        $todo = 'useit';
                        $datasetdef = $sharedatasetdef;
                    } else { // Different so create a private one.
                        $datasetdef->category = 0;
                        $todo = 'create';
                    }
                } else { // No so create one.
                    $datasetdef->category = $question->category;
                    $todo = 'create';
                }
            }
            if ($todo == 'create') {
                $datasetdef->id = $DB->insert_record('question_dataset_definitions', $datasetdef);
            }
            // Create relation to the dataset.
            $questiondataset = new stdClass();
            $questiondataset->question = $question->id;
            $questiondataset->datasetdefinition = $datasetdef->id;
            $DB->insert_record('question_datasets', $questiondataset);
            if ($todo == 'create') {
                // Add the items.
                foreach ($dataset->datasetitem as $dataitem) {
                    $datasetitem = new stdClass();
                    $datasetitem->definition = $datasetdef->id;
                    $datasetitem->itemnumber = $dataitem->itemnumber;
                    $datasetitem->value = $dataitem->value;
                    $DB->insert_record('question_dataset_items', $datasetitem);
                }
            }
        }
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);

        question_bank::get_qtype('numerical')->initialise_numerical_answers(
                $question, $questiondata);
        foreach ($questiondata->options->answers as $a) {
            $question->answers[$a->id]->tolerancetype = $a->tolerancetype;
            $question->answers[$a->id]->correctanswerlength = $a->correctanswerlength;
            $question->answers[$a->id]->correctanswerformat = $a->correctanswerformat;
        }

        $question->synchronised = $questiondata->options->synchronize;

        $question->unitdisplay = $questiondata->options->showunits;
        $question->unitgradingtype = $questiondata->options->unitgradingtype;
        $question->unitpenalty = $questiondata->options->unitpenalty;
        $question->ap = question_bank::get_qtype(
                'numerical')->make_answer_processor(
                $questiondata->options->units, $questiondata->options->unitsleft);

        $question->datasetloader = new qtype_calculated_dataset_loader($questiondata->id);
    }

    public function finished_edit_wizard($form) {
        return isset($form->savechanges);
    }
    public function wizardpagesnumber() {
        return 3;
    }
    // This gets called by editquestion.php after the standard question is saved.
    public function print_next_wizard_page($question, $form, $course) {
        global $CFG, $SESSION, $COURSE;

        // Catch invalid navigation & reloads.
        if (empty($question->id) && empty($SESSION->calculated)) {
            redirect('edit.php?courseid='.$COURSE->id, 'The page you are loading has expired.', 3);
        }

        // See where we're coming from.
        switch($form->wizardpage) {
            case 'question':
                require("{$CFG->dirroot}/question/type/calculated/datasetdefinitions.php");
                break;
            case 'datasetdefinitions':
            case 'datasetitems':
                require("{$CFG->dirroot}/question/type/calculated/datasetitems.php");
                break;
            default:
                print_error('invalidwizardpage', 'question');
                break;
        }
    }

    // This gets called by question2.php after the standard question is saved.
    public function &next_wizard_form($submiturl, $question, $wizardnow) {
        global $CFG, $SESSION, $COURSE;

        // Catch invalid navigation & reloads.
        if (empty($question->id) && empty($SESSION->calculated)) {
            redirect('edit.php?courseid=' . $COURSE->id,
                    'The page you are loading has expired. Cannot get next wizard form.', 3);
        }
        if (empty($question->id)) {
            $question = $SESSION->calculated->questionform;
        }

        // See where we're coming from.
        switch($wizardnow) {
            case 'datasetdefinitions':
                require("{$CFG->dirroot}/question/type/calculated/datasetdefinitions_form.php");
                $mform = new question_dataset_dependent_definitions_form(
                        "{$submiturl}?wizardnow=datasetdefinitions", $question);
                break;
            case 'datasetitems':
                require("{$CFG->dirroot}/question/type/calculated/datasetitems_form.php");
                $regenerate = optional_param('forceregeneration', false, PARAM_BOOL);
                $mform = new question_dataset_dependent_items_form(
                        "{$submiturl}?wizardnow=datasetitems", $question, $regenerate);
                break;
            default:
                print_error('invalidwizardpage', 'question');
                break;
        }

        return $mform;
    }

    /**
     * This method should be overriden if you want to include a special heading or some other
     * html on a question editing page besides the question editing form.
     *
     * @param question_edit_form $mform a child of question_edit_form
     * @param object $question
     * @param string $wizardnow is '' for first page.
     */
    public function display_question_editing_page($mform, $question, $wizardnow) {
        global $OUTPUT;
        switch ($wizardnow) {
            case '':
                // On the first page, the default display is fine.
                parent::display_question_editing_page($mform, $question, $wizardnow);
                return;

            case 'datasetdefinitions':
                echo $OUTPUT->heading_with_help(
                        get_string('choosedatasetproperties', 'qtype_calculated'),
                        'questiondatasets', 'qtype_calculated');
                break;

            case 'datasetitems':
                echo $OUTPUT->heading_with_help(get_string('editdatasets', 'qtype_calculated'),
                        'questiondatasets', 'qtype_calculated');
                break;
        }

        $mform->display();
    }

    /**
     * Verify that the equations in part of the question are OK.
     * We throw an exception here because this should have already been validated
     * by the form. This is just a last line of defence to prevent a question
     * being stored in the database if it has bad formulas. This saves us from,
     * for example, malicious imports.
     * @param string $text containing equations.
     */
    protected function validate_text($text) {
        $error = qtype_calculated_find_formula_errors_in_text($text);
        if ($error) {
            throw new coding_exception($error);
        }
    }

    /**
     * Verify that an answer is OK.
     * We throw an exception here because this should have already been validated
     * by the form. This is just a last line of defence to prevent a question
     * being stored in the database if it has bad formulas. This saves us from,
     * for example, malicious imports.
     * @param string $text containing equations.
     */
    protected function validate_answer($answer) {
        $error = qtype_calculated_find_formula_errors($answer);
        if ($error) {
            throw new coding_exception($error);
        }
    }

    /**
     * Validate data before save.
     * @param stdClass $question data from the form / import file.
     */
    protected function validate_question_data($question) {
        $this->validate_text($question->questiontext); // Yes, really no ['text'].

        if (isset($question->generalfeedback['text'])) {
            $this->validate_text($question->generalfeedback['text']);
        } else if (isset($question->generalfeedback)) {
            $this->validate_text($question->generalfeedback); // Because question import is weird.
        }

        foreach ($question->answer as $key => $answer) {
            $this->validate_answer($answer);
            $this->validate_text($question->feedback[$key]['text']);
        }
    }

    /**
     * This method prepare the $datasets in a format similar to dadatesetdefinitions_form.php
     * so that they can be saved
     * using the function save_dataset_definitions($form)
     * when creating a new calculated question or
     * when editing an already existing calculated question
     * or by  function save_as_new_dataset_definitions($form, $initialid)
     * when saving as new an already existing calculated question.
     *
     * @param object $form
     * @param int $questionfromid default = '0'
     */
    public function preparedatasets($form, $questionfromid = '0') {

        // The dataset names present in the edit_question_form and edit_calculated_form
        // are retrieved.
        $possibledatasets = $this->find_dataset_names($form->questiontext);
        $mandatorydatasets = array();
        foreach ($form->answer as $key => $answer) {
            $mandatorydatasets += $this->find_dataset_names($answer);
        }
        // If there are identical datasetdefs already saved in the original question
        // either when editing a question or saving as new,
        // they are retrieved using $questionfromid.
        if ($questionfromid != '0') {
            $form->id = $questionfromid;
        }
        $datasets = array();
        $key = 0;
        // Always prepare the mandatorydatasets present in the answers.
        // The $options are not used here.
        foreach ($mandatorydatasets as $datasetname) {
            if (!isset($datasets[$datasetname])) {
                list($options, $selected) =
                    $this->dataset_options($form, $datasetname);
                $datasets[$datasetname] = '';
                $form->dataset[$key] = $selected;
                $key++;
            }
        }
        // Do not prepare possibledatasets when creating a question.
        // They will defined and stored with datasetdefinitions_form.php.
        // The $options are not used here.
        if ($questionfromid != '0') {

            foreach ($possibledatasets as $datasetname) {
                if (!isset($datasets[$datasetname])) {
                    list($options, $selected) =
                        $this->dataset_options($form, $datasetname, false);
                    $datasets[$datasetname] = '';
                    $form->dataset[$key] = $selected;
                    $key++;
                }
            }
        }
        return $datasets;
    }
    public function addnamecategory(&$question) {
        global $DB;
        $categorydatasetdefs = $DB->get_records_sql(
            "SELECT  a.*
               FROM {question_datasets} b, {question_dataset_definitions} a
              WHERE a.id = b.datasetdefinition
                AND a.type = '1'
                AND a.category != 0
                AND b.question = ?
           ORDER BY a.name ", array($question->id));
        $questionname = $question->name;
        $regs= array();
        if (preg_match('~#\{([^[:space:]]*)#~', $questionname , $regs)) {
            $questionname = str_replace($regs[0], '', $questionname);
        };

        if (!empty($categorydatasetdefs)) {
            // There is at least one with the same name.
            $questionname = '#' . $questionname;
            foreach ($categorydatasetdefs as $def) {
                if (strlen($def->name) + strlen($questionname) < 250) {
                    $questionname = '{' . $def->name . '}' . $questionname;
                }
            }
            $questionname = '#' . $questionname;
        }
        $DB->set_field('question', 'name', $questionname, array('id' => $question->id));
    }

    /**
     * this version save the available data at the different steps of the question editing process
     * without using global $SESSION as storage between steps
     * at the first step $wizardnow = 'question'
     *  when creating a new question
     *  when modifying a question
     *  when copying as a new question
     *  the general parameters and answers are saved using parent::save_question
     *  then the datasets are prepared and saved
     * at the second step $wizardnow = 'datasetdefinitions'
     *  the datadefs final type are defined as private, category or not a datadef
     * at the third step $wizardnow = 'datasetitems'
     *  the datadefs parameters and the data items are created or defined
     *
     * @param object question
     * @param object $form
     * @param int $course
     * @param PARAM_ALPHA $wizardnow should be added as we are coming from question2.php
     */
    public function save_question($question, $form) {
        global $DB;

        if ($this->wizardpagesnumber() == 1 || $question->qtype == 'calculatedsimple') {
            $question = parent::save_question($question, $form);
            return $question;
        }

        $wizardnow =  optional_param('wizardnow', '', PARAM_ALPHA);
        $id = optional_param('id', 0, PARAM_INT); // Question id.
        // In case 'question':
        // For a new question $form->id is empty
        // when saving as new question.
        // The $question->id = 0, $form is $data from question2.php
        // and $data->makecopy is defined as $data->id is the initial question id.
        // Edit case. If it is a new question we don't necessarily need to
        // return a valid question object.

        // See where we're coming from.
        switch($wizardnow) {
            case '' :
            case 'question': // Coming from the first page, creating the second.
                if (empty($form->id)) { // or a new question $form->id is empty.
                    $question = parent::save_question($question, $form);
                    // Prepare the datasets using default $questionfromid.
                    $this->preparedatasets($form);
                    $form->id = $question->id;
                    $this->save_dataset_definitions($form);
                    if (isset($form->synchronize) && $form->synchronize == 2) {
                        $this->addnamecategory($question);
                    }
                } else if (!empty($form->makecopy)) {
                    $questionfromid =  $form->id;
                    $question = parent::save_question($question, $form);
                    // Prepare the datasets.
                    $this->preparedatasets($form, $questionfromid);
                    $form->id = $question->id;
                    $this->save_as_new_dataset_definitions($form, $questionfromid);
                    if (isset($form->synchronize) && $form->synchronize == 2) {
                        $this->addnamecategory($question);
                    }
                } else {
                    // Editing a question.
                    $question = parent::save_question($question, $form);
                    // Prepare the datasets.
                    $this->preparedatasets($form, $question->id);
                    $form->id = $question->id;
                    $this->save_dataset_definitions($form);
                    if (isset($form->synchronize) && $form->synchronize == 2) {
                        $this->addnamecategory($question);
                    }
                }
                break;
            case 'datasetdefinitions':
                // Calculated options.
                // It cannot go here without having done the first page,
                // so the question_calculated_options should exist.
                // We only need to update the synchronize field.
                if (isset($form->synchronize)) {
                    $optionssynchronize = $form->synchronize;
                } else {
                    $optionssynchronize = 0;
                }
                $DB->set_field('question_calculated_options', 'synchronize', $optionssynchronize,
                        array('question' => $question->id));
                if (isset($form->synchronize) && $form->synchronize == 2) {
                    $this->addnamecategory($question);
                }

                $this->save_dataset_definitions($form);
                break;
            case 'datasetitems':
                $this->save_dataset_items($question, $form);
                $this->save_question_calculated($question, $form);
                break;
            default:
                print_error('invalidwizardpage', 'question');
                break;
        }
        return $question;
    }

    public function delete_question($questionid, $contextid) {
        global $DB;

        $DB->delete_records('question_calculated', array('question' => $questionid));
        $DB->delete_records('question_calculated_options', array('question' => $questionid));
        $DB->delete_records('question_numerical_units', array('question' => $questionid));
        if ($datasets = $DB->get_records('question_datasets', array('question' => $questionid))) {
            foreach ($datasets as $dataset) {
                if (!$DB->get_records_select('question_datasets',
                        "question != ? AND datasetdefinition = ? ",
                        array($questionid, $dataset->datasetdefinition))) {
                    $DB->delete_records('question_dataset_definitions',
                            array('id' => $dataset->datasetdefinition));
                    $DB->delete_records('question_dataset_items',
                            array('definition' => $dataset->datasetdefinition));
                }
            }
        }
        $DB->delete_records('question_datasets', array('question' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    public function get_random_guess_score($questiondata) {
        foreach ($questiondata->options->answers as $aid => $answer) {
            if ('*' == trim($answer->answer)) {
                return max($answer->fraction - $questiondata->options->unitpenalty, 0);
            }
        }
        return 0;
    }

    public function supports_dataset_item_generation() {
        // Calculated support generation of randomly distributed number data.
        return true;
    }

    public function custom_generator_tools_part($mform, $idx, $j) {

        $minmaxgrp = array();
        $minmaxgrp[] = $mform->createElement('text', "calcmin[{$idx}]",
                get_string('calcmin', 'qtype_calculated'));
        $minmaxgrp[] = $mform->createElement('text', "calcmax[{$idx}]",
                get_string('calcmax', 'qtype_calculated'));
        $mform->addGroup($minmaxgrp, 'minmaxgrp',
                get_string('minmax', 'qtype_calculated'), ' - ', false);
        $mform->setType("calcmin[{$idx}]", PARAM_FLOAT);
        $mform->setType("calcmax[{$idx}]", PARAM_FLOAT);

        $precisionoptions = range(0, 10);
        $mform->addElement('select', "calclength[{$idx}]",
                get_string('calclength', 'qtype_calculated'), $precisionoptions);

        $distriboptions = array('uniform' => get_string('uniform', 'qtype_calculated'),
                'loguniform' => get_string('loguniform', 'qtype_calculated'));
        $mform->addElement('select', "calcdistribution[{$idx}]",
                get_string('calcdistribution', 'qtype_calculated'), $distriboptions);
    }

    public function custom_generator_set_data($datasetdefs, $formdata) {
        $idx = 1;
        foreach ($datasetdefs as $datasetdef) {
            if (preg_match('~^(uniform|loguniform):([^:]*):([^:]*):([0-9]*)$~',
                    $datasetdef->options, $regs)) {
                $defid = "{$datasetdef->type}-{$datasetdef->category}-{$datasetdef->name}";
                $formdata["calcdistribution[{$idx}]"] = $regs[1];
                $formdata["calcmin[{$idx}]"] = $regs[2];
                $formdata["calcmax[{$idx}]"] = $regs[3];
                $formdata["calclength[{$idx}]"] = $regs[4];
            }
            $idx++;
        }
        return $formdata;
    }

    public function custom_generator_tools($datasetdef) {
        global $OUTPUT;
        if (preg_match('~^(uniform|loguniform):([^:]*):([^:]*):([0-9]*)$~',
                $datasetdef->options, $regs)) {
            $defid = "{$datasetdef->type}-{$datasetdef->category}-{$datasetdef->name}";
            for ($i = 0; $i<10; ++$i) {
                $lengthoptions[$i] = get_string(($regs[1] == 'uniform'
                    ? 'decimals'
                    : 'significantfigures'), 'qtype_calculated', $i);
            }
            $menu1 = html_writer::label(get_string('lengthoption', 'qtype_calculated'),
                'menucalclength', false, array('class' => 'accesshide'));
            $menu1 .= html_writer::select($lengthoptions, 'calclength[]', $regs[4], null, array('class' => 'custom-select'));

            $options = array('uniform' => get_string('uniformbit', 'qtype_calculated'),
                'loguniform' => get_string('loguniformbit', 'qtype_calculated'));
            $menu2 = html_writer::label(get_string('distributionoption', 'qtype_calculated'),
                'menucalcdistribution', false, array('class' => 'accesshide'));
            $menu2 .= html_writer::select($options, 'calcdistribution[]', $regs[1], null, array('class' => 'custom-select'));
            return '<input type="submit" class="btn btn-secondary" onclick="'
                . "getElementById('addform').regenerateddefid.value='{$defid}'; return true;"
                .'" value="'. get_string('generatevalue', 'qtype_calculated') . '"/><br/>'
                . '<input type="text" class="form-control" size="3" name="calcmin[]" '
                . " value=\"{$regs[2]}\"/> &amp; <input name=\"calcmax[]\" "
                . ' type="text" class="form-control" size="3" value="' . $regs[3] .'"/> '
                . $menu1 . '<br/>'
                . $menu2;
        } else {
            return '';
        }
    }


    public function update_dataset_options($datasetdefs, $form) {
        global $OUTPUT;
        // Do we have information about new options ?
        if (empty($form->definition) || empty($form->calcmin)
                ||empty($form->calcmax) || empty($form->calclength)
                || empty($form->calcdistribution)) {
            // I guess not.

        } else {
            // Looks like we just could have some new information here.
            $uniquedefs = array_values(array_unique($form->definition));
            foreach ($uniquedefs as $key => $defid) {
                if (isset($datasetdefs[$defid])
                        && is_numeric($form->calcmin[$key+1])
                        && is_numeric($form->calcmax[$key+1])
                        && is_numeric($form->calclength[$key+1])) {
                    switch     ($form->calcdistribution[$key+1]) {
                        case 'uniform': case 'loguniform':
                            $datasetdefs[$defid]->options =
                                $form->calcdistribution[$key+1] . ':'
                                . $form->calcmin[$key+1] . ':'
                                . $form->calcmax[$key+1] . ':'
                                . $form->calclength[$key+1];
                            break;
                        default:
                            echo $OUTPUT->notification(
                                    "Unexpected distribution ".$form->calcdistribution[$key+1]);
                    }
                }
            }
        }

        // Look for empty options, on which we set default values.
        foreach ($datasetdefs as $defid => $def) {
            if (empty($def->options)) {
                $datasetdefs[$defid]->options = 'uniform:1.0:10.0:1';
            }
        }
        return $datasetdefs;
    }

    public function save_question_calculated($question, $fromform) {
        global $DB;

        foreach ($question->options->answers as $key => $answer) {
            if ($options = $DB->get_record('question_calculated', array('answer' => $key))) {
                $options->tolerance = trim($fromform->tolerance[$key]);
                $options->tolerancetype  = trim($fromform->tolerancetype[$key]);
                $options->correctanswerlength  = trim($fromform->correctanswerlength[$key]);
                $options->correctanswerformat  = trim($fromform->correctanswerformat[$key]);
                $DB->update_record('question_calculated', $options);
            }
        }
    }

    /**
     * This function get the dataset items using id as unique parameter and return an
     * array with itemnumber as index sorted ascendant
     * If the multiple records with the same itemnumber exist, only the newest one
     * i.e with the greatest id is used, the others are ignored but not deleted.
     * MDL-19210
     */
    public function get_database_dataset_items($definition) {
        global $CFG, $DB;
        $databasedataitems = $DB->get_records_sql(// Use number as key!!
            " SELECT id , itemnumber, definition,  value
            FROM {question_dataset_items}
            WHERE definition = $definition order by id DESC ", array($definition));
        $dataitems = Array();
        foreach ($databasedataitems as $id => $dataitem) {
            if (!isset($dataitems[$dataitem->itemnumber])) {
                $dataitems[$dataitem->itemnumber] = $dataitem;
            }
        }
        ksort($dataitems);
        return $dataitems;
    }

    public function save_dataset_items($question, $fromform) {
        global $CFG, $DB;
        $synchronize = false;
        if (isset($fromform->nextpageparam['forceregeneration'])) {
            $regenerate = $fromform->nextpageparam['forceregeneration'];
        } else {
            $regenerate = 0;
        }
        if (empty($question->options)) {
            $this->get_question_options($question);
        }
        if (!empty($question->options->synchronize)) {
            $synchronize = true;
        }

        // Get the old datasets for this question.
        $datasetdefs = $this->get_dataset_definitions($question->id, array());
        // Handle generator options...
        $olddatasetdefs = fullclone($datasetdefs);
        $datasetdefs = $this->update_dataset_options($datasetdefs, $fromform);
        $maxnumber = -1;
        foreach ($datasetdefs as $defid => $datasetdef) {
            if (isset($datasetdef->id)
                    && $datasetdef->options != $olddatasetdefs[$defid]->options) {
                // Save the new value for options.
                $DB->update_record('question_dataset_definitions', $datasetdef);

            }
            // Get maxnumber.
            if ($maxnumber == -1 || $datasetdef->itemcount < $maxnumber) {
                $maxnumber = $datasetdef->itemcount;
            }
        }
        // Handle adding and removing of dataset items.
        $i = 1;
        if ($maxnumber > self::MAX_DATASET_ITEMS) {
            $maxnumber = self::MAX_DATASET_ITEMS;
        }

        ksort($fromform->definition);
        foreach ($fromform->definition as $key => $defid) {
            // If the delete button has not been pressed then skip the datasetitems
            // in the 'add item' part of the form.
            if ($i > count($datasetdefs)*$maxnumber) {
                break;
            }
            $addeditem = new stdClass();
            $addeditem->definition = $datasetdefs[$defid]->id;
            $addeditem->value = $fromform->number[$i];
            $addeditem->itemnumber = ceil($i / count($datasetdefs));

            if ($fromform->itemid[$i]) {
                // Reuse any previously used record.
                $addeditem->id = $fromform->itemid[$i];
                $DB->update_record('question_dataset_items', $addeditem);
            } else {
                $DB->insert_record('question_dataset_items', $addeditem);
            }

            $i++;
        }
        if (isset($addeditem->itemnumber) && $maxnumber < $addeditem->itemnumber
                && $addeditem->itemnumber < self::MAX_DATASET_ITEMS) {
            $maxnumber = $addeditem->itemnumber;
            foreach ($datasetdefs as $key => $newdef) {
                if (isset($newdef->id) && $newdef->itemcount <= $maxnumber) {
                    $newdef->itemcount = $maxnumber;
                    // Save the new value for options.
                    $DB->update_record('question_dataset_definitions', $newdef);
                }
            }
        }
        // Adding supplementary items.
        $numbertoadd = 0;
        if (isset($fromform->addbutton) && $fromform->selectadd > 0 &&
                $maxnumber < self::MAX_DATASET_ITEMS) {
            $numbertoadd = $fromform->selectadd;
            if (self::MAX_DATASET_ITEMS - $maxnumber < $numbertoadd) {
                $numbertoadd = self::MAX_DATASET_ITEMS - $maxnumber;
            }
            // Add the other items.
            // Generate a new dataset item (or reuse an old one).
            foreach ($datasetdefs as $defid => $datasetdef) {
                // In case that for category datasets some new items has been added,
                // get actual values.
                // Fix regenerate for this datadefs.
                $defregenerate = 0;
                if ($synchronize &&
                        !empty ($fromform->nextpageparam["datasetregenerate[{$datasetdef->name}"])) {
                    $defregenerate = 1;
                } else if (!$synchronize &&
                        (($regenerate == 1 && $datasetdef->category == 0) ||$regenerate == 2)) {
                    $defregenerate = 1;
                }
                if (isset($datasetdef->id)) {
                    $datasetdefs[$defid]->items =
                            $this->get_database_dataset_items($datasetdef->id);
                }
                for ($numberadded = $maxnumber+1; $numberadded <= $maxnumber + $numbertoadd; $numberadded++) {
                    if (isset($datasetdefs[$defid]->items[$numberadded])) {
                        // In case of regenerate it modifies the already existing record.
                        if ($defregenerate) {
                            $datasetitem = new stdClass();
                            $datasetitem->id = $datasetdefs[$defid]->items[$numberadded]->id;
                            $datasetitem->definition = $datasetdef->id;
                            $datasetitem->itemnumber = $numberadded;
                            $datasetitem->value =
                                    $this->generate_dataset_item($datasetdef->options);
                            $DB->update_record('question_dataset_items', $datasetitem);
                        }
                        // If not regenerate do nothing as there is already a record.
                    } else {
                        $datasetitem = new stdClass();
                        $datasetitem->definition = $datasetdef->id;
                        $datasetitem->itemnumber = $numberadded;
                        if ($this->supports_dataset_item_generation()) {
                            $datasetitem->value =
                                    $this->generate_dataset_item($datasetdef->options);
                        } else {
                            $datasetitem->value = '';
                        }
                        $DB->insert_record('question_dataset_items', $datasetitem);
                    }
                }// For number added.
            }// Datasetsdefs end.
            $maxnumber += $numbertoadd;
            foreach ($datasetdefs as $key => $newdef) {
                if (isset($newdef->id) && $newdef->itemcount <= $maxnumber) {
                    $newdef->itemcount = $maxnumber;
                    // Save the new value for options.
                    $DB->update_record('question_dataset_definitions', $newdef);
                }
            }
        }

        if (isset($fromform->deletebutton)) {
            if (isset($fromform->selectdelete)) {
                $newmaxnumber = $maxnumber-$fromform->selectdelete;
            } else {
                $newmaxnumber = $maxnumber-1;
            }
            if ($newmaxnumber < 0) {
                $newmaxnumber = 0;
            }
            foreach ($datasetdefs as $datasetdef) {
                if ($datasetdef->itemcount == $maxnumber) {
                    $datasetdef->itemcount= $newmaxnumber;
                    $DB->update_record('question_dataset_definitions', $datasetdef);
                }
            }
        }
    }
    public function generate_dataset_item($options) {
        if (!preg_match('~^(uniform|loguniform):([^:]*):([^:]*):([0-9]*)$~',
                $options, $regs)) {
            // Unknown options...
            return false;
        }
        if ($regs[1] == 'uniform') {
            $nbr = $regs[2] + ($regs[3]-$regs[2])*mt_rand()/mt_getrandmax();
            return sprintf("%.".$regs[4].'f', $nbr);

        } else if ($regs[1] == 'loguniform') {
            $log0 = log(abs($regs[2])); // It would have worked the other way to.
            $nbr = exp($log0 + (log(abs($regs[3])) - $log0)*mt_rand()/mt_getrandmax());
            return sprintf("%.".$regs[4].'f', $nbr);

        } else {
            print_error('disterror', 'question', '', $regs[1]);
        }
        return '';
    }

    public function comment_header($question) {
        $strheader = '';
        $delimiter = '';

        $answers = $question->options->answers;

        foreach ($answers as $key => $answer) {
            $ans = shorten_text($answer->answer, 17, true);
            $strheader .= $delimiter.$ans;
            $delimiter = '<br/><br/><br/>';
        }
        return $strheader;
    }

    public function comment_on_datasetitems($qtypeobj, $questionid, $questiontext,
            $answers, $data, $number) {
        global $DB;
        $comment = new stdClass();
        $comment->stranswers = array();
        $comment->outsidelimit = false;
        $comment->answers = array();
        // Find a default unit.
        $unit = '';
        if (!empty($questionid)) {
            $units = $DB->get_records('question_numerical_units',
                array('question' => $questionid, 'multiplier' => 1.0),
                'id ASC', '*', 0, 1);
            if ($units) {
                $unit = reset($units);
                $unit = $unit->unit;
            }
        }

        $answers = fullclone($answers);
        $delimiter = ': ';
        $virtualqtype =  $qtypeobj->get_virtual_qtype();
        foreach ($answers as $key => $answer) {
            $error = qtype_calculated_find_formula_errors($answer->answer);
            if ($error) {
                $comment->stranswers[$key] = $error;
                continue;
            }
            $formula = $this->substitute_variables($answer->answer, $data);
            $formattedanswer = qtype_calculated_calculate_answer(
                $answer->answer, $data, $answer->tolerance,
                $answer->tolerancetype, $answer->correctanswerlength,
                $answer->correctanswerformat, $unit);
            if ($formula === '*') {
                $answer->min = ' ';
                $formattedanswer->answer = $answer->answer;
            } else {
                eval('$ansvalue = '.$formula.';');
                $ans = new qtype_numerical_answer(0, $ansvalue, 0, '', 0, $answer->tolerance);
                $ans->tolerancetype = $answer->tolerancetype;
                list($answer->min, $answer->max) = $ans->get_tolerance_interval($answer);
            }
            if ($answer->min === '') {
                // This should mean that something is wrong.
                $comment->stranswers[$key] = " {$formattedanswer->answer}".'<br/><br/>';
            } else if ($formula === '*') {
                $comment->stranswers[$key] = $formula . ' = ' .
                        get_string('anyvalue', 'qtype_calculated') . '<br/><br/><br/>';
            } else {
                $formula = shorten_text($formula, 57, true);
                $comment->stranswers[$key] = $formula . ' = ' . $formattedanswer->answer . '<br/>';
                $correcttrue = new stdClass();
                $correcttrue->correct = $formattedanswer->answer;
                $correcttrue->true = '';
                if ($formattedanswer->answer < $answer->min ||
                        $formattedanswer->answer > $answer->max) {
                    $comment->outsidelimit = true;
                    $comment->answers[$key] = $key;
                    $comment->stranswers[$key] .=
                            get_string('trueansweroutsidelimits', 'qtype_calculated', $correcttrue);
                } else {
                    $comment->stranswers[$key] .=
                            get_string('trueanswerinsidelimits', 'qtype_calculated', $correcttrue);
                }
                $comment->stranswers[$key] .= '<br/>';
                $comment->stranswers[$key] .= get_string('min', 'qtype_calculated') .
                        $delimiter . $answer->min . ' --- ';
                $comment->stranswers[$key] .= get_string('max', 'qtype_calculated') .
                        $delimiter . $answer->max;
            }
        }
        return fullclone($comment);
    }

    public function tolerance_types() {
        return array(
            '1' => get_string('relative', 'qtype_numerical'),
            '2' => get_string('nominal', 'qtype_numerical'),
            '3' => get_string('geometric', 'qtype_numerical')
        );
    }

    public function dataset_options($form, $name, $mandatory = true,
            $renameabledatasets = false) {
        // Takes datasets from the parent implementation but
        // filters options that are currently not accepted by calculated.
        // It also determines a default selection.
        // Param $renameabledatasets not implemented anywhere.

        list($options, $selected) = $this->dataset_options_from_database(
                $form, $name, '', 'qtype_calculated');

        foreach ($options as $key => $whatever) {
            if (!preg_match('~^1-~', $key) && $key != '0') {
                unset($options[$key]);
            }
        }
        if (!$selected) {
            if ($mandatory) {
                $selected =  "1-0-{$name}"; // Default.
            } else {
                $selected = '0'; // Default.
            }
        }
        return array($options, $selected);
    }

    public function construct_dataset_menus($form, $mandatorydatasets,
            $optionaldatasets) {
        global $OUTPUT;
        $datasetmenus = array();
        foreach ($mandatorydatasets as $datasetname) {
            if (!isset($datasetmenus[$datasetname])) {
                list($options, $selected) =
                    $this->dataset_options($form, $datasetname);
                unset($options['0']); // Mandatory...
                $datasetmenus[$datasetname] = html_writer::select(
                        $options, 'dataset[]', $selected, null);
            }
        }
        foreach ($optionaldatasets as $datasetname) {
            if (!isset($datasetmenus[$datasetname])) {
                list($options, $selected) =
                    $this->dataset_options($form, $datasetname);
                $datasetmenus[$datasetname] = html_writer::select(
                        $options, 'dataset[]', $selected, null);
            }
        }
        return $datasetmenus;
    }

    public function substitute_variables($str, $dataset) {
        global $OUTPUT;
        // Testing for wrong numerical values.
        // All calculations used this function so testing here should be OK.

        foreach ($dataset as $name => $value) {
            $val = $value;
            if (! is_numeric($val)) {
                $a = new stdClass();
                $a->name = '{'.$name.'}';
                $a->value = $value;
                echo $OUTPUT->notification(get_string('notvalidnumber', 'qtype_calculated', $a));
                $val = 1.0;
            }
            if ($val <= 0) { // MDL-36025 Use parentheses for "-0" .
                $str = str_replace('{'.$name.'}', '('.$val.')', $str);
            } else {
                $str = str_replace('{'.$name.'}', $val, $str);
            }
        }
        return $str;
    }

    public function evaluate_equations($str, $dataset) {
        $formula = $this->substitute_variables($str, $dataset);
        if ($error = qtype_calculated_find_formula_errors($formula)) {
            return $error;
        }
        return $str;
    }

    public function substitute_variables_and_eval($str, $dataset) {
        $formula = $this->substitute_variables($str, $dataset);
        if ($error = qtype_calculated_find_formula_errors($formula)) {
            return $error;
        }
        // Calculate the correct answer.
        if (empty($formula)) {
            $str = '';
        } else if ($formula === '*') {
            $str = '*';
        } else {
            $str = null;
            eval('$str = '.$formula.';');
        }
        return $str;
    }

    public function get_dataset_definitions($questionid, $newdatasets) {
        global $DB;
        // Get the existing datasets for this question.
        $datasetdefs = array();
        if (!empty($questionid)) {
            global $CFG;
            $sql = "SELECT i.*
                      FROM {question_datasets} d, {question_dataset_definitions} i
                     WHERE d.question = ? AND d.datasetdefinition = i.id
                  ORDER BY i.id";
            if ($records = $DB->get_records_sql($sql, array($questionid))) {
                foreach ($records as $r) {
                    $datasetdefs["{$r->type}-{$r->category}-{$r->name}"] = $r;
                }
            }
        }

        foreach ($newdatasets as $dataset) {
            if (!$dataset) {
                continue; // The no dataset case...
            }

            if (!isset($datasetdefs[$dataset])) {
                // Make new datasetdef.
                list($type, $category, $name) = explode('-', $dataset, 3);
                $datasetdef = new stdClass();
                $datasetdef->type = $type;
                $datasetdef->name = $name;
                $datasetdef->category  = $category;
                $datasetdef->itemcount = 0;
                $datasetdef->options   = 'uniform:1.0:10.0:1';
                $datasetdefs[$dataset] = clone($datasetdef);
            }
        }
        return $datasetdefs;
    }

    public function save_dataset_definitions($form) {
        global $DB;
        // Save synchronize.

        if (empty($form->dataset)) {
            $form->dataset = array();
        }
        // Save datasets.
        $datasetdefinitions = $this->get_dataset_definitions($form->id, $form->dataset);
        $tmpdatasets = array_flip($form->dataset);
        $defids = array_keys($datasetdefinitions);
        foreach ($defids as $defid) {
            $datasetdef = &$datasetdefinitions[$defid];
            if (isset($datasetdef->id)) {
                if (!isset($tmpdatasets[$defid])) {
                    // This dataset is not used any more, delete it.
                    $DB->delete_records('question_datasets',
                            array('question' => $form->id, 'datasetdefinition' => $datasetdef->id));
                    if ($datasetdef->category == 0) {
                        // Question local dataset.
                        $DB->delete_records('question_dataset_definitions',
                                array('id' => $datasetdef->id));
                        $DB->delete_records('question_dataset_items',
                                array('definition' => $datasetdef->id));
                    }
                }
                // This has already been saved or just got deleted.
                unset($datasetdefinitions[$defid]);
                continue;
            }

            $datasetdef->id = $DB->insert_record('question_dataset_definitions', $datasetdef);

            if (0 != $datasetdef->category) {
                // We need to look for already existing datasets in the category.
                // First creating the datasetdefinition above
                // then we can manage to automatically take care of some possible realtime concurrence.

                if ($olderdatasetdefs = $DB->get_records_select('question_dataset_definitions',
                        'type = ? AND name = ? AND category = ? AND id < ?
                        ORDER BY id DESC',
                        array($datasetdef->type, $datasetdef->name,
                                $datasetdef->category, $datasetdef->id))) {

                    while ($olderdatasetdef = array_shift($olderdatasetdefs)) {
                        $DB->delete_records('question_dataset_definitions',
                                array('id' => $datasetdef->id));
                        $datasetdef = $olderdatasetdef;
                    }
                }
            }

            // Create relation to this dataset.
            $questiondataset = new stdClass();
            $questiondataset->question = $form->id;
            $questiondataset->datasetdefinition = $datasetdef->id;
            $DB->insert_record('question_datasets', $questiondataset);
            unset($datasetdefinitions[$defid]);
        }

        // Remove local obsolete datasets as well as relations
        // to datasets in other categories.
        if (!empty($datasetdefinitions)) {
            foreach ($datasetdefinitions as $def) {
                $DB->delete_records('question_datasets',
                        array('question' => $form->id, 'datasetdefinition' => $def->id));

                if ($def->category == 0) { // Question local dataset.
                    $DB->delete_records('question_dataset_definitions',
                            array('id' => $def->id));
                    $DB->delete_records('question_dataset_items',
                            array('definition' => $def->id));
                }
            }
        }
    }
    /** This function create a copy of the datasets (definition and dataitems)
     * from the preceding question if they remain in the new question
     * otherwise its create the datasets that have been added as in the
     * save_dataset_definitions()
     */
    public function save_as_new_dataset_definitions($form, $initialid) {
        global $CFG, $DB;
        // Get the datasets from the intial question.
        $datasetdefinitions = $this->get_dataset_definitions($initialid, $form->dataset);
        // Param $tmpdatasets contains those of the new question.
        $tmpdatasets = array_flip($form->dataset);
        $defids = array_keys($datasetdefinitions);// New datasets.
        foreach ($defids as $defid) {
            $datasetdef = &$datasetdefinitions[$defid];
            if (isset($datasetdef->id)) {
                // This dataset exist in the initial question.
                if (!isset($tmpdatasets[$defid])) {
                    // Do not exist in the new question so ignore.
                    unset($datasetdefinitions[$defid]);
                    continue;
                }
                // Create a copy but not for category one.
                if (0 == $datasetdef->category) {
                    $olddatasetid = $datasetdef->id;
                    $olditemcount = $datasetdef->itemcount;
                    $datasetdef->itemcount = 0;
                    $datasetdef->id = $DB->insert_record('question_dataset_definitions',
                            $datasetdef);
                    // Copy the dataitems.
                    $olditems = $this->get_database_dataset_items($olddatasetid);
                    if (count($olditems) > 0) {
                        $itemcount = 0;
                        foreach ($olditems as $item) {
                            $item->definition = $datasetdef->id;
                            $DB->insert_record('question_dataset_items', $item);
                            $itemcount++;
                        }
                        // Update item count to olditemcount if
                        // at least this number of items has been recover from the database.
                        if ($olditemcount <= $itemcount) {
                            $datasetdef->itemcount = $olditemcount;
                        } else {
                            $datasetdef->itemcount = $itemcount;
                        }
                        $DB->update_record('question_dataset_definitions', $datasetdef);
                    } // End of  copy the dataitems.
                }// End of  copy the datasetdef.
                // Create relation to the new question with this
                // copy as new datasetdef from the initial question.
                $questiondataset = new stdClass();
                $questiondataset->question = $form->id;
                $questiondataset->datasetdefinition = $datasetdef->id;
                $DB->insert_record('question_datasets', $questiondataset);
                unset($datasetdefinitions[$defid]);
                continue;
            }// End of datasetdefs from the initial question.
            // Really new one code similar to save_dataset_definitions().
            $datasetdef->id = $DB->insert_record('question_dataset_definitions', $datasetdef);

            if (0 != $datasetdef->category) {
                // We need to look for already existing
                // datasets in the category.
                // By first creating the datasetdefinition above we
                // can manage to automatically take care of
                // some possible realtime concurrence.
                if ($olderdatasetdefs = $DB->get_records_select('question_dataset_definitions',
                        "type = ? AND " . $DB->sql_equal('name', '?') . " AND category = ? AND id < ?
                        ORDER BY id DESC",
                        array($datasetdef->type, $datasetdef->name,
                                $datasetdef->category, $datasetdef->id))) {

                    while ($olderdatasetdef = array_shift($olderdatasetdefs)) {
                        $DB->delete_records('question_dataset_definitions',
                                array('id' => $datasetdef->id));
                        $datasetdef = $olderdatasetdef;
                    }
                }
            }

            // Create relation to this dataset.
            $questiondataset = new stdClass();
            $questiondataset->question = $form->id;
            $questiondataset->datasetdefinition = $datasetdef->id;
            $DB->insert_record('question_datasets', $questiondataset);
            unset($datasetdefinitions[$defid]);
        }

        // Remove local obsolete datasets as well as relations
        // to datasets in other categories.
        if (!empty($datasetdefinitions)) {
            foreach ($datasetdefinitions as $def) {
                $DB->delete_records('question_datasets',
                        array('question' => $form->id, 'datasetdefinition' => $def->id));

                if ($def->category == 0) { // Question local dataset.
                    $DB->delete_records('question_dataset_definitions',
                            array('id' => $def->id));
                    $DB->delete_records('question_dataset_items',
                            array('definition' => $def->id));
                }
            }
        }
    }

    // Dataset functionality.
    public function pick_question_dataset($question, $datasetitem) {
        // Select a dataset in the following format:
        // an array indexed by the variable names (d.name) pointing to the value
        // to be substituted.
        global $CFG, $DB;
        if (!$dataitems = $DB->get_records_sql(
                "SELECT i.id, d.name, i.value
                   FROM {question_dataset_definitions} d,
                        {question_dataset_items} i,
                        {question_datasets} q
                  WHERE q.question = ?
                    AND q.datasetdefinition = d.id
                    AND d.id = i.definition
                    AND i.itemnumber = ?
               ORDER BY i.id DESC ", array($question->id, $datasetitem))) {
            $a = new stdClass();
            $a->id = $question->id;
            $a->item = $datasetitem;
            print_error('cannotgetdsfordependent', 'question', '', $a);
        }
        $dataset = Array();
        foreach ($dataitems as $id => $dataitem) {
            if (!isset($dataset[$dataitem->name])) {
                $dataset[$dataitem->name] = $dataitem->value;
            }
        }
        return $dataset;
    }

    public function dataset_options_from_database($form, $name, $prefix = '',
            $langfile = 'qtype_calculated') {
        global $CFG, $DB;
        $type = 1; // Only type = 1 (i.e. old 'LITERAL') has ever been used.
        // First options - it is not a dataset...
        $options['0'] = get_string($prefix.'nodataset', $langfile);
        // New question no local.
        if (!isset($form->id) || $form->id == 0) {
            $key = "{$type}-0-{$name}";
            $options[$key] = get_string($prefix."newlocal{$type}", $langfile);
            $currentdatasetdef = new stdClass();
            $currentdatasetdef->type = '0';
        } else {
            // Construct question local options.
            $sql = "SELECT a.*
                FROM {question_dataset_definitions} a, {question_datasets} b
               WHERE a.id = b.datasetdefinition AND a.type = '1' AND b.question = ? AND " . $DB->sql_equal('a.name', '?');
            $currentdatasetdef = $DB->get_record_sql($sql, array($form->id, $name));
            if (!$currentdatasetdef) {
                $currentdatasetdef = new stdClass();
                $currentdatasetdef->type = '0';
            }
            $key = "{$type}-0-{$name}";
            if ($currentdatasetdef->type == $type
                    and $currentdatasetdef->category == 0) {
                $options[$key] = get_string($prefix."keptlocal{$type}", $langfile);
            } else {
                $options[$key] = get_string($prefix."newlocal{$type}", $langfile);
            }
        }
        // Construct question category options.
        $categorydatasetdefs = $DB->get_records_sql(
            "SELECT b.question, a.*
            FROM {question_datasets} b,
            {question_dataset_definitions} a
            WHERE a.id = b.datasetdefinition
            AND a.type = '1'
            AND a.category = ?
            AND " . $DB->sql_equal('a.name', '?'), array($form->category, $name));
        $type = 1;
        $key = "{$type}-{$form->category}-{$name}";
        if (!empty($categorydatasetdefs)) {
            // There is at least one with the same name.
            if (isset($form->id) && isset($categorydatasetdefs[$form->id])) {
                // It is already used by this question.
                $options[$key] = get_string($prefix."keptcategory{$type}", $langfile);
            } else {
                $options[$key] = get_string($prefix."existingcategory{$type}", $langfile);
            }
        } else {
            $options[$key] = get_string($prefix."newcategory{$type}", $langfile);
        }
        // All done!
        return array($options, $currentdatasetdef->type
            ? "{$currentdatasetdef->type}-{$currentdatasetdef->category}-{$name}"
            : '');
    }

    public function find_dataset_names($text) {
        // Returns the possible dataset names found in the text as an array.
        // The array has the dataset name for both key and value.
        $datasetnames = array();
        while (preg_match('~\\{([[:alpha:]][^>} <{"\']*)\\}~', $text, $regs)) {
            $datasetnames[$regs[1]] = $regs[1];
            $text = str_replace($regs[0], '', $text);
        }
        return $datasetnames;
    }

    /**
     * This function retrieve the item count of the available category shareable
     * wild cards that is added as a comment displayed when a wild card with
     * the same name is displayed in datasetdefinitions_form.php
     */
    public function get_dataset_definitions_category($form) {
        global $CFG, $DB;
        $datasetdefs = array();
        $lnamemax = 30;
        if (!empty($form->category)) {
            $sql = "SELECT i.*, d.*
                      FROM {question_datasets} d, {question_dataset_definitions} i
                     WHERE i.id = d.datasetdefinition AND i.category = ?";
            if ($records = $DB->get_records_sql($sql, array($form->category))) {
                foreach ($records as $r) {
                    if (!isset ($datasetdefs["{$r->name}"])) {
                        $datasetdefs["{$r->name}"] = $r->itemcount;
                    }
                }
            }
        }
        return $datasetdefs;
    }

    /**
     * This function build a table showing the available category shareable
     * wild cards, their name, their definition (Min, Max, Decimal) , the item count
     * and the name of the question where they are used.
     * This table is intended to be add before the question text to help the user use
     * these wild cards
     */
    public function print_dataset_definitions_category($form) {
        global $CFG, $DB;
        $datasetdefs = array();
        $lnamemax = 22;
        $namestr          = get_string('name');
        $rangeofvaluestr  = get_string('minmax', 'qtype_calculated');
        $questionusingstr = get_string('usedinquestion', 'qtype_calculated');
        $itemscountstr    = get_string('itemscount', 'qtype_calculated');
        $text = '';
        if (!empty($form->category)) {
            list($category) = explode(',', $form->category);
            $sql = "SELECT i.*, d.*
                FROM {question_datasets} d,
        {question_dataset_definitions} i
        WHERE i.id = d.datasetdefinition
        AND i.category = ?";
            if ($records = $DB->get_records_sql($sql, array($category))) {
                foreach ($records as $r) {
                    $sql1 = "SELECT q.*
                               FROM {question} q
                              WHERE q.id = ?";
                    if (!isset ($datasetdefs["{$r->type}-{$r->category}-{$r->name}"])) {
                        $datasetdefs["{$r->type}-{$r->category}-{$r->name}"] = $r;
                    }
                    if ($questionb = $DB->get_records_sql($sql1, array($r->question))) {
                        if (!isset ($datasetdefs["{$r->type}-{$r->category}-{$r->name}"]->questions[$r->question])) {
                            $datasetdefs["{$r->type}-{$r->category}-{$r->name}"]->questions[$r->question] = new stdClass();
                        }
                        $datasetdefs["{$r->type}-{$r->category}-{$r->name}"]->questions[
                                $r->question]->name = $questionb[$r->question]->name;
                    }
                }
            }
        }
        if (!empty ($datasetdefs)) {

            $text = "<table width=\"100%\" border=\"1\"><tr>
                    <th style=\"white-space:nowrap;\" class=\"header\"
                            scope=\"col\">{$namestr}</th>
                    <th style=\"white-space:nowrap;\" class=\"header\"
                            scope=\"col\">{$rangeofvaluestr}</th>
                    <th style=\"white-space:nowrap;\" class=\"header\"
                            scope=\"col\">{$itemscountstr}</th>
                    <th style=\"white-space:nowrap;\" class=\"header\"
                            scope=\"col\">{$questionusingstr}</th>
                    </tr>";
            foreach ($datasetdefs as $datasetdef) {
                list($distribution, $min, $max, $dec) = explode(':', $datasetdef->options, 4);
                $text .= "<tr>
                        <td valign=\"top\" align=\"center\">{$datasetdef->name}</td>
                        <td align=\"center\" valign=\"top\">{$min} <strong>-</strong> $max</td>
                        <td align=\"right\" valign=\"top\">{$datasetdef->itemcount}&nbsp;&nbsp;</td>
                        <td align=\"left\">";
                foreach ($datasetdef->questions as $qu) {
                    // Limit the name length displayed.
                    $questionname = $this->get_short_question_name($qu->name, $lnamemax);
                    $text .= " &nbsp;&nbsp; {$questionname} <br/>";
                }
                $text .= "</td></tr>";
            }
            $text .= "</table>";
        } else {
            $text .= get_string('nosharedwildcard', 'qtype_calculated');
        }
        return $text;
    }

    /**
     * This function shortens a question name if it exceeds the character limit.
     *
     * @param string $stringtoshorten the string to be shortened.
     * @param int $characterlimit the character limit.
     * @return string
     */
    public function get_short_question_name($stringtoshorten, $characterlimit)
    {
        if (!empty($stringtoshorten)) {
            $returnstring = format_string($stringtoshorten);
            if (strlen($returnstring) > $characterlimit) {
                $returnstring = shorten_text($returnstring, $characterlimit, true);
            }
            return $returnstring;
        } else {
            return '';
        }
    }

    /**
     * This function build a table showing the available category shareable
     * wild cards, their name, their definition (Min, Max, Decimal) , the item count
     * and the name of the question where they are used.
     * This table is intended to be add before the question text to help the user use
     * these wild cards
     */

    public function print_dataset_definitions_category_shared($question, $datasetdefsq) {
        global $CFG, $DB;
        $datasetdefs = array();
        $lnamemax = 22;
        $namestr          = get_string('name', 'quiz');
        $rangeofvaluestr  = get_string('minmax', 'qtype_calculated');
        $questionusingstr = get_string('usedinquestion', 'qtype_calculated');
        $itemscountstr    = get_string('itemscount', 'qtype_calculated');
        $text = '';
        if (!empty($question->category)) {
            list($category) = explode(',', $question->category);
            $sql = "SELECT i.*, d.*
                      FROM {question_datasets} d, {question_dataset_definitions} i
                     WHERE i.id = d.datasetdefinition AND i.category = ?";
            if ($records = $DB->get_records_sql($sql, array($category))) {
                foreach ($records as $r) {
                    $key = "{$r->type}-{$r->category}-{$r->name}";
                    $sql1 = "SELECT q.*
                               FROM {question} q
                              WHERE q.id = ?";
                    if (!isset($datasetdefs[$key])) {
                        $datasetdefs[$key] = $r;
                    }
                    if ($questionb = $DB->get_records_sql($sql1, array($r->question))) {
                        $datasetdefs[$key]->questions[$r->question] = new stdClass();
                        $datasetdefs[$key]->questions[$r->question]->name =
                                $questionb[$r->question]->name;
                        $datasetdefs[$key]->questions[$r->question]->id =
                                $questionb[$r->question]->id;
                    }
                }
            }
        }
        if (!empty ($datasetdefs)) {

            $text  = "<table width=\"100%\" border=\"1\"><tr>
                    <th style=\"white-space:nowrap;\" class=\"header\"
                            scope=\"col\">{$namestr}</th>";
            $text .= "<th style=\"white-space:nowrap;\" class=\"header\"
                    scope=\"col\">{$itemscountstr}</th>";
            $text .= "<th style=\"white-space:nowrap;\" class=\"header\"
                    scope=\"col\">&nbsp;&nbsp;{$questionusingstr} &nbsp;&nbsp;</th>";
            $text .= "<th style=\"white-space:nowrap;\" class=\"header\"
                    scope=\"col\">Quiz</th>";
            $text .= "<th style=\"white-space:nowrap;\" class=\"header\"
                    scope=\"col\">Attempts</th></tr>";
            foreach ($datasetdefs as $datasetdef) {
                list($distribution, $min, $max, $dec) = explode(':', $datasetdef->options, 4);
                $count = count($datasetdef->questions);
                $text .= "<tr>
                        <td style=\"white-space:nowrap;\" valign=\"top\"
                                align=\"center\" rowspan=\"{$count}\"> {$datasetdef->name} </td>
                        <td align=\"right\" valign=\"top\"
                                rowspan=\"{$count}\">{$datasetdef->itemcount}</td>";
                $line = 0;
                foreach ($datasetdef->questions as $qu) {
                    // Limit the name length displayed.
                    $questionname = $this->get_short_question_name($qu->name, $lnamemax);
                    if ($line) {
                        $text .= "<tr>";
                    }
                    $line++;
                    $text .= "<td align=\"left\" style=\"white-space:nowrap;\">{$questionname}</td>";
                    // TODO MDL-43779 should not have quiz-specific code here.
                    $nbofquiz = $DB->count_records('quiz_slots', array('questionid' => $qu->id));
                    $nbofattempts = $DB->count_records_sql("
                            SELECT count(1)
                              FROM {quiz_slots} slot
                              JOIN {quiz_attempts} quiza ON quiza.quiz = slot.quizid
                             WHERE slot.questionid = ?
                               AND quiza.preview = 0", array($qu->id));
                    if ($nbofquiz > 0) {
                        $text .= "<td align=\"center\">{$nbofquiz}</td>";
                        $text .= "<td align=\"center\">{$nbofattempts}";
                    } else {
                        $text .= "<td align=\"center\">0</td>";
                        $text .= "<td align=\"left\"><br/>";
                    }

                    $text .= "</td></tr>";
                }
            }
            $text .= "</table>";
        } else {
            $text .= get_string('nosharedwildcard', 'qtype_calculated');
        }
        return $text;
    }

    public function find_math_equations($text) {
        // Returns the possible dataset names found in the text as an array.
        // The array has the dataset name for both key and value.
        $equations = array();
        while (preg_match('~\{=([^[:space:]}]*)}~', $text, $regs)) {
            $equations[] = $regs[1];
            $text = str_replace($regs[0], '', $text);
        }
        return $equations;
    }

    public function get_virtual_qtype() {
        return question_bank::get_qtype('numerical');
    }

    public function get_possible_responses($questiondata) {
        $responses = array();

        $virtualqtype = $this->get_virtual_qtype();
        $unit = $virtualqtype->get_default_numerical_unit($questiondata);

        $tolerancetypes = $this->tolerance_types();

        $starfound = false;
        foreach ($questiondata->options->answers as $aid => $answer) {
            $responseclass = $answer->answer;

            if ($responseclass === '*') {
                $starfound = true;
            } else {
                $a = new stdClass();
                $a->answer = $virtualqtype->add_unit($questiondata, $responseclass, $unit);
                $a->tolerance = $answer->tolerance;
                $a->tolerancetype = $tolerancetypes[$answer->tolerancetype];

                $responseclass = get_string('answerwithtolerance', 'qtype_calculated', $a);
            }

            $responses[$aid] = new question_possible_response($responseclass,
                    $answer->fraction);
        }

        if (!$starfound) {
            $responses[0] = new question_possible_response(
            get_string('didnotmatchanyanswer', 'question'), 0);
        }

        $responses[null] = question_possible_response::no_response();

        return array($questiondata->id => $responses);
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }
}


function qtype_calculated_calculate_answer($formula, $individualdata,
    $tolerance, $tolerancetype, $answerlength, $answerformat = '1', $unit = '') {
    // The return value has these properties: .
    // ->answer    the correct answer
    // ->min       the lower bound for an acceptable response
    // ->max       the upper bound for an accetpable response.
    $calculated = new stdClass();
    // Exchange formula variables with the correct values...
    $answer = question_bank::get_qtype('calculated')->substitute_variables_and_eval(
            $formula, $individualdata);
    if (!is_numeric($answer)) {
        // Something went wrong, so just return NaN.
        $calculated->answer = NAN;
        return $calculated;
    }
    if ('1' == $answerformat) { // Answer is to have $answerlength decimals.
        // Decimal places.
        $calculated->answer = sprintf('%.' . $answerlength . 'F', $answer);

    } else if ($answer) { // Significant figures does only apply if the result is non-zero.

        // Convert to positive answer...
        if ($answer < 0) {
            $answer = -$answer;
            $sign = '-';
        } else {
            $sign = '';
        }

        // Determine the format 0.[1-9][0-9]* for the answer...
        $p10 = 0;
        while ($answer < 1) {
            --$p10;
            $answer *= 10;
        }
        while ($answer >= 1) {
            ++$p10;
            $answer /= 10;
        }
        // ... and have the answer rounded of to the correct length.
        $answer = round($answer, $answerlength);

        // If we rounded up to 1.0, place the answer back into 0.[1-9][0-9]* format.
        if ($answer >= 1) {
            ++$p10;
            $answer /= 10;
        }

        // Have the answer written on a suitable format:
        // either scientific or plain numeric.
        if (-2 > $p10 || 4 < $p10) {
            // Use scientific format.
            $exponent = 'e'.--$p10;
            $answer *= 10;
            if (1 == $answerlength) {
                $calculated->answer = $sign.$answer.$exponent;
            } else {
                // Attach additional zeros at the end of $answer.
                $answer .= (1 == strlen($answer) ? '.' : '')
                    . '00000000000000000000000000000000000000000x';
                $calculated->answer = $sign
                    .substr($answer, 0, $answerlength +1).$exponent;
            }
        } else {
            // Stick to plain numeric format.
            $answer *= "1e{$p10}";
            if (0.1 <= $answer / "1e{$answerlength}") {
                $calculated->answer = $sign.$answer;
            } else {
                // Could be an idea to add some zeros here.
                $answer .= (preg_match('~^[0-9]*$~', $answer) ? '.' : '')
                    . '00000000000000000000000000000000000000000x';
                $oklen = $answerlength + ($p10 < 1 ? 2-$p10 : 1);
                $calculated->answer = $sign.substr($answer, 0, $oklen);
            }
        }

    } else {
        $calculated->answer = 0.0;
    }
    if ($unit != '') {
            $calculated->answer = $calculated->answer . ' ' . $unit;
    }

    // Return the result.
    return $calculated;
}


/**
 * Validate a forumula.
 * @param string $formula the formula to validate.
 * @return string|boolean false if there are no problems. Otherwise a string error message.
 */
function qtype_calculated_find_formula_errors($formula) {
    // Validates the formula submitted from the question edit page.
    // Returns false if everything is alright
    // otherwise it constructs an error message.
    // Strip away dataset names.
    while (preg_match('~\\{[[:alpha:]][^>} <{"\']*\\}~', $formula, $regs)) {
        $formula = str_replace($regs[0], '1', $formula);
    }

    // Strip away empty space and lowercase it.
    $formula = strtolower(str_replace(' ', '', $formula));

    $safeoperatorchar = '-+/*%>:^\~<?=&|!'; /* */
    $operatorornumber = "[{$safeoperatorchar}.0-9eE]";

    while (preg_match("~(^|[{$safeoperatorchar},(])([a-z0-9_]*)" .
            "\\(({$operatorornumber}+(,{$operatorornumber}+((,{$operatorornumber}+)+)?)?)?\\)~",
            $formula, $regs)) {
        switch ($regs[2]) {
            // Simple parenthesis.
            case '':
                if ((isset($regs[4]) && $regs[4]) || strlen($regs[3]) == 0) {
                    return get_string('illegalformulasyntax', 'qtype_calculated', $regs[0]);
                }
                break;

                // Zero argument functions.
            case 'pi':
                if (array_key_exists(3, $regs)) {
                    return get_string('functiontakesnoargs', 'qtype_calculated', $regs[2]);
                }
                break;

                // Single argument functions (the most common case).
            case 'abs': case 'acos': case 'acosh': case 'asin': case 'asinh':
            case 'atan': case 'atanh': case 'bindec': case 'ceil': case 'cos':
            case 'cosh': case 'decbin': case 'decoct': case 'deg2rad':
            case 'exp': case 'expm1': case 'floor': case 'is_finite':
            case 'is_infinite': case 'is_nan': case 'log10': case 'log1p':
            case 'octdec': case 'rad2deg': case 'sin': case 'sinh': case 'sqrt':
            case 'tan': case 'tanh':
                if (!empty($regs[4]) || empty($regs[3])) {
                    return get_string('functiontakesonearg', 'qtype_calculated', $regs[2]);
                }
                break;

                // Functions that take one or two arguments.
            case 'log': case 'round':
                if (!empty($regs[5]) || empty($regs[3])) {
                    return get_string('functiontakesoneortwoargs', 'qtype_calculated', $regs[2]);
                }
                break;

                // Functions that must have two arguments.
            case 'atan2': case 'fmod': case 'pow':
                if (!empty($regs[5]) || empty($regs[4])) {
                    return get_string('functiontakestwoargs', 'qtype_calculated', $regs[2]);
                }
                break;

                // Functions that take two or more arguments.
            case 'min': case 'max':
                if (empty($regs[4])) {
                    return get_string('functiontakesatleasttwo', 'qtype_calculated', $regs[2]);
                }
                break;

            default:
                return get_string('unsupportedformulafunction', 'qtype_calculated', $regs[2]);
        }

        // Exchange the function call with '1' and then check for
        // another function call...
        if ($regs[1]) {
            // The function call is proceeded by an operator.
            $formula = str_replace($regs[0], $regs[1] . '1', $formula);
        } else {
            // The function call starts the formula.
            $formula = preg_replace("~^{$regs[2]}\\([^)]*\\)~", '1', $formula);
        }
    }

    if (preg_match("~[^{$safeoperatorchar}.0-9eE]+~", $formula, $regs)) {
        return get_string('illegalformulasyntax', 'qtype_calculated', $regs[0]);
    } else {
        // Formula just might be valid.
        return false;
    }
}

/**
 * Validate all the forumulas in a bit of text.
 * @param string $text the text in which to validate the formulas.
 * @return string|boolean false if there are no problems. Otherwise a string error message.
 */
function qtype_calculated_find_formula_errors_in_text($text) {
    preg_match_all(qtype_calculated::FORMULAS_IN_TEXT_REGEX, $text, $matches);

    $errors = array();
    foreach ($matches[1] as $match) {
        $error = qtype_calculated_find_formula_errors($match);
        if ($error) {
            $errors[] = $error;
        }
    }

    if ($errors) {
        return implode(' ', $errors);
    }

    return false;
}
