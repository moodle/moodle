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
 * Question type class for the simple calculated question type.
 *
 * @package    qtype
 * @subpackage calculatedsimple
 * @copyright  2009 Pierre Pichet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/calculated/questiontype.php');


/**
 * The simple calculated question type.
 *
 * @copyright  2009 Pierre Pichet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculatedsimple extends qtype_calculated {

    // Used by the function custom_generator_tools.
    public $wizard_pages_number = 1;

    public function save_question_options($question) {
        global $CFG, $DB;
        $context = $question->context;
        // Get old answers.

        if (isset($question->answer) && !isset($question->answers)) {
            $question->answers = $question->answer;
        }

        // Get old versions of the objects.
        if (!$oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC')) {
            $oldanswers = array();
        }

        if (!$oldoptions = $DB->get_records('question_calculated',
                array('question' => $question->id), 'answer ASC')) {
            $oldoptions = array();
        }

        // Save the units.
        $virtualqtype = $this->get_virtual_qtype();
        $result = $virtualqtype->save_units($question);
        if (isset($result->error)) {
            return $result;
        } else {
            $units = &$result->units;
        }
        // Insert all the new answers.
        if (isset($question->answer) && !isset($question->answers)) {
            $question->answers = $question->answer;
        }
        foreach ($question->answers as $key => $answerdata) {
            if (is_array($answerdata)) {
                $answerdata = $answerdata['text'];
            }
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

        if (isset($question->import_process) && $question->import_process) {
            $this->import_datasets($question);
        } else {
            // Save datasets and datatitems from form i.e in question.
            $question->dataset = $question->datasetdef;

            // Save datasets.
            $datasetdefinitions = $this->get_dataset_definitions($question->id, $question->dataset);
            $tmpdatasets = array_flip($question->dataset);
            $defids = array_keys($datasetdefinitions);
            $datasetdefs = array();
            foreach ($defids as $defid) {
                $datasetdef = &$datasetdefinitions[$defid];
                if (isset($datasetdef->id)) {
                    if (!isset($tmpdatasets[$defid])) {
                        // This dataset is not used any more, delete it.
                        $DB->delete_records('question_datasets', array('question' => $question->id,
                                'datasetdefinition' => $datasetdef->id));
                        $DB->delete_records('question_dataset_definitions',
                                array('id' => $datasetdef->id));
                        $DB->delete_records('question_dataset_items',
                                array('definition' => $datasetdef->id));
                    }
                    // This has already been saved or just got deleted.
                    unset($datasetdefinitions[$defid]);
                    continue;
                }
                $datasetdef->id = $DB->insert_record('question_dataset_definitions', $datasetdef);
                $datasetdefs[] = clone($datasetdef);
                $questiondataset = new stdClass();
                $questiondataset->question = $question->id;
                $questiondataset->datasetdefinition = $datasetdef->id;
                $DB->insert_record('question_datasets', $questiondataset);
                unset($datasetdefinitions[$defid]);
            }
            // Remove local obsolete datasets as well as relations
            // to datasets in other categories.
            if (!empty($datasetdefinitions)) {
                foreach ($datasetdefinitions as $def) {
                    $DB->delete_records('question_datasets', array('question' => $question->id,
                            'datasetdefinition' => $def->id));
                    if ($def->category == 0) { // Question local dataset.
                        $DB->delete_records('question_dataset_definitions',
                                array('id' => $def->id));
                        $DB->delete_records('question_dataset_items',
                                array('definition' => $def->id));
                    }
                }
            }
            $datasetdefs = $this->get_dataset_definitions($question->id, $question->dataset);
            // Handle adding and removing of dataset items.
            $i = 1;
            ksort($question->definition);
            foreach ($question->definition as $key => $defid) {
                $addeditem = new stdClass();
                $addeditem->definition = $datasetdefs[$defid]->id;
                $addeditem->value = $question->number[$i];
                $addeditem->itemnumber = ceil($i / count($datasetdefs));
                if (empty($question->makecopy) && $question->itemid[$i]) {
                    // Reuse any previously used record.
                    $addeditem->id = $question->itemid[$i];
                    $DB->update_record('question_dataset_items', $addeditem);
                } else {
                    $DB->insert_record('question_dataset_items', $addeditem);
                }
                $i++;
            }
            $maxnumber = -1;
            if (isset($addeditem->itemnumber) && $maxnumber < $addeditem->itemnumber) {
                $maxnumber = $addeditem->itemnumber;
                foreach ($datasetdefs as $key => $newdef) {
                    if (isset($newdef->id) && $newdef->itemcount <= $maxnumber) {
                        $newdef->itemcount = $maxnumber;
                        // Save the new value for options.
                        $DB->update_record('question_dataset_definitions', $newdef);
                    }
                }
            }
        }

        $this->save_hints($question);

        // Report any problems.
        if (!empty($question->makecopy) && !empty($question->convert)) {
            $DB->set_field('question', 'qtype', 'calculated', array('id' => $question->id));
        }

        $result = $virtualqtype->save_unit_options($question);
        if (isset($result->error)) {
            return $result;
        }

        if (!empty($result->notice)) {
            return $result;
        }

        return true;
    }

    public function finished_edit_wizard($form) {
        return true;
    }

    public function wizard_pages_number() {
        return 1;
    }

    public function custom_generator_tools_part($mform, $idx, $j) {

        $minmaxgrp = array();
        $minmaxgrp[] = $mform->createElement('text', "calcmin[$idx]",
                get_string('calcmin', 'qtype_calculated'));
        $minmaxgrp[] = $mform->createElement('text', "calcmax[$idx]",
                get_string('calcmax', 'qtype_calculated'));
        $mform->addGroup($minmaxgrp, 'minmaxgrp',
                get_string('minmax', 'qtype_calculated'), ' - ', false);
        $mform->setType("calcmin[$idx]", PARAM_FLOAT);
        $mform->setType("calcmax[$idx]", PARAM_FLOAT);

        $precisionoptions = range(0, 10);
        $mform->addElement('select', "calclength[$idx]",
                get_string('calclength', 'qtype_calculated'), $precisionoptions);

        $distriboptions = array('uniform' => get_string('uniform', 'qtype_calculated'),
                'loguniform' => get_string('loguniform', 'qtype_calculated'));
        $mform->addElement('hidden', "calcdistribution[$idx]", 'uniform');
        $mform->setType("calcdistribution[$idx]", PARAM_INT);
    }

    public function comment_header($answers) {
        $strheader = "";
        $delimiter = '';

        foreach ($answers as $key => $answer) {
            $ans = shorten_text($answer->answer, 17, true);
            $strheader .= $delimiter.$ans;
            $delimiter = '<br/><br/><br/>';
        }
        return $strheader;
    }

    public function tolerance_types() {
        return array(
            '1'  => get_string('relative', 'qtype_numerical'),
            '2'  => get_string('nominal', 'qtype_numerical'),
        );
    }

    public function dataset_options($form, $name, $mandatory = true, $renameabledatasets = false) {
        // Takes datasets from the parent implementation but
        // filters options that are currently not accepted by calculated.
        // It also determines a default selection
        // $renameabledatasets not implemented anywhere.
        list($options, $selected) = $this->dataset_options_from_database(
                $form, $name, '', 'qtype_calculated');

        foreach ($options as $key => $whatever) {
            if (!preg_match('~^1-~', $key) && $key != '0') {
                unset($options[$key]);
            }
        }
        if (!$selected) {
            if ($mandatory) {
                $selected =  "1-0-$name"; // Default.
            } else {
                $selected = "0"; // Default.
            }
        }
        return array($options, $selected);
    }
}
