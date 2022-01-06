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
 * Defines the editing form for the calculated question data set definitions.
 *
 * @package    qtype
 * @subpackage calculated
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/edit_question_form.php');


/**
 * Calculated question data set definitions editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_dataset_dependent_definitions_form extends question_wizard_form {
    /**
     * Question object with options and answers already loaded by get_question_options
     * Be careful how you use this it is needed sometimes to set up the structure of the
     * form in definition_inner but data is always loaded into the form with set_defaults.
     *
     * @var object
     */
    protected $question;
    /**
     * Reference to question type object
     *
     * @var question_dataset_dependent_questiontype
     */
    protected $qtypeobj;
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    public function __construct($submiturl, $question) {
        global $DB;
        $this->question = $question;
        $this->qtypeobj = question_bank::get_qtype($this->question->qtype);
        // Validate the question category.
        if (!$category = $DB->get_record('question_categories',
                array('id' => $question->category))) {
            print_error('categorydoesnotexist', 'question', $returnurl);
        }
        $this->category = $category;
        $this->categorycontext = context::instance_by_id($category->contextid);
        parent::__construct($submiturl);
    }

    protected function definition() {
        global $SESSION;

        $mform = $this->_form;
        $mform->setDisableShortforms();

        $possibledatasets = $this->qtypeobj->find_dataset_names($this->question->questiontext);
        $mandatorydatasets = array();
        if (isset($this->question->options->answers)) {
            foreach ($this->question->options->answers as $answer) {
                $mandatorydatasets += $this->qtypeobj->find_dataset_names($answer->answer);
            }
        } else {
            foreach ($SESSION->calculated->questionform->answers as $answer) {
                $mandatorydatasets += $this->qtypeobj->find_dataset_names($answer);
            }
        }

        $key = 0;
        $datadefscat= array();
        $datadefscat  = $this->qtypeobj->get_dataset_definitions_category($this->question);
        $datasetmenus = array();
        $label = "<div class='mdl-align'>".get_string('datasetrole', 'qtype_calculated')."</div>";
        // Explaining the role of datasets so other strings can be shortened.
        $mform->addElement('html', $label);
        $mform->addElement('header', 'mandatoryhdr',
                get_string('mandatoryhdr', 'qtype_calculated'));
        $labelsharedwildcard = get_string('sharedwildcard', 'qtype_calculated');

        foreach ($mandatorydatasets as $datasetname) {
            if (!isset($datasetmenus[$datasetname])) {
                list($options, $selected) =
                        $this->qtypeobj->dataset_options($this->question, $datasetname);
                unset($options['0']); // Mandatory...
                $label = get_string('wildcard', 'qtype_calculated', $datasetname);
                $mform->addElement('select', "dataset[{$key}]", $label, $options);
                if (isset($datadefscat[$datasetname])) {
                    $mform->addElement('static', "there is a category",
                            get_string('sharedwildcard', 'qtype_calculated', $datasetname),
                            get_string('dataitemdefined', 'qtype_calculated',
                            $datadefscat[$datasetname]));
                }
                $mform->setDefault("dataset[{$key}]", $selected);
                $datasetmenus[$datasetname] = '';
                $key++;
            }
        }
        $mform->addElement('header', 'possiblehdr', get_string('possiblehdr', 'qtype_calculated'));

        foreach ($possibledatasets as $datasetname) {
            if (!isset($datasetmenus[$datasetname])) {
                list($options, $selected) = $this->qtypeobj->dataset_options(
                        $this->question, $datasetname, false);
                $label = get_string('wildcard', 'qtype_calculated', $datasetname);
                $mform->addElement('select', "dataset[{$key}]", $label, $options);
                if (isset($datadefscat[$datasetname])) {
                    $mform->addElement('static', "there is a category",
                            get_string('sharedwildcard', 'qtype_calculated', $datasetname),
                            get_string('dataitemdefined', 'qtype_calculated',
                                    $datadefscat[$datasetname]));
                }

                $mform->setDefault("dataset[{$key}]", $selected);
                $datasetmenus[$datasetname] = '';
                $key++;
            }
        }
        // Temporary strings.
        $mform->addElement('header', 'synchronizehdr',
                get_string('synchronize', 'qtype_calculated'));
        $mform->addElement('radio', 'synchronize', '',
                get_string('synchronizeno', 'qtype_calculated'), 0);
        $mform->addElement('radio', 'synchronize', '',
                get_string('synchronizeyes', 'qtype_calculated'), 1);
        $mform->addElement('radio', 'synchronize', '',
                get_string('synchronizeyesdisplay', 'qtype_calculated'), 2);
        if (isset($this->question->options) &&
                isset($this->question->options->synchronize)) {
            $mform->setDefault('synchronize', $this->question->options->synchronize);
        } else {
            $mform->setDefault('synchronize', 0);
        }

        $this->add_action_buttons(false, get_string('nextpage', 'qtype_calculated'));

        $this->add_hidden_fields();

        $mform->addElement('hidden', 'category');
        $mform->setType('category', PARAM_SEQUENCE);

        $mform->addElement('hidden', 'wizard', 'datasetitems');
        $mform->setType('wizard', PARAM_ALPHA);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $datasets = $data['dataset'];
        $countvalid = 0;
        foreach ($datasets as $key => $dataset) {
            if ($dataset != '0') {
                $countvalid++;
            }
        }
        if (!$countvalid) {
            foreach ($datasets as $key => $dataset) {
                $errors['dataset['.$key.']'] =
                        get_string('atleastonerealdataset', 'qtype_calculated');
            }
        }
        return $errors;
    }
}
