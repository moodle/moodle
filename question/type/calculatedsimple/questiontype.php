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

/////////////////
// CALCULATED ///
/////////////////

/// QUESTION TYPE CLASS //////////////////

class question_calculatedsimple_qtype extends question_calculated_qtype {



    // Used by the function custom_generator_tools:
    public $calcgenerateidhasbeenadded = false;
    public $virtualqtype = false;
    public $wizard_pages_number = 1 ;

    function name() {
        return 'calculatedsimple';
    }

    function save_question_options($question) {
        global $CFG, $DB , $QTYPES;
        $context = $question->context;
        //$options = $question->subtypeoptions;
        // Get old answers:

        if (isset($question->answer) && !isset($question->answers)) {
            $question->answers = $question->answer;
        }

        // Get old versions of the objects
        if (!$oldanswers = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC')) {
            $oldanswers = array();
        }

        if (!$oldoptions = $DB->get_records('question_calculated', array('question' => $question->id), 'answer ASC')) {
            $oldoptions = array();
        }

        // Save the units.
        $virtualqtype = $this->get_virtual_qtype($question);
        $result = $virtualqtype->save_numerical_units($question);
        if (isset($result->error)) {
            return $result;
        } else {
            $units = &$result->units;
        }
        // Insert all the new answers
        if (isset($question->answer) && !isset($question->answers)) {
            $question->answers=$question->answer;
        }
        foreach ($question->answers as $key => $dataanswer) {
            if (is_array($dataanswer)) {
                $dataanswer = $dataanswer['text'];
            }
            if ( trim($dataanswer) != '' ) {
                $answer = new stdClass;
                $answer->question = $question->id;
                $answer->answer = trim($dataanswer);
                $answer->fraction = $question->fraction[$key];
                $answer->feedbackformat = $question->feedback[$key]['format'];
                if (isset($question->feedback[$key]['files'])) {
                    $files = $question->feedback[$key]['files'];
                }

                if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer->id = $oldanswer->id;
                    $answer->feedback = file_save_draft_area_files($question->feedback[$key]['itemid'], $context->id, 'question', 'answerfeedback', $answer->id, $this->fileoptionsa, trim($question->feedback[$key]['text']));
                    $DB->update_record("question_answers", $answer);
                } else { // This is a completely new answer
                    $answer->feedback = trim($question->feedback[$key]['text']);
                    $answer->id = $DB->insert_record("question_answers", $answer);
                    if (isset($files)) {
                        foreach ($files as $file) {
                            $this->import_file($context, 'question', 'answerfeedback', $answer->id, $file);
                        }
                    } else {
                        $answer->feedback = file_save_draft_area_files($question->feedback[$key]['itemid'], $context->id, 'question', 'answerfeedback', $answer->id, $this->fileoptionsa, trim($question->feedback[$key]['text']));
                    }
                    $DB->set_field('question_answers', 'feedback', $answer->feedback, array('id'=>$answer->id));
                }

                // Set up the options object
                if (!$options = array_shift($oldoptions)) {
                    $options = new stdClass;
                }
                $options->question  = $question->id;
                $options->answer    = $answer->id;
                $options->tolerance = trim($question->tolerance[$key]);
                $options->tolerancetype  = trim($question->tolerancetype[$key]);
                $options->correctanswerlength  = trim($question->correctanswerlength[$key]);
                $options->correctanswerformat  = trim($question->correctanswerformat[$key]);

                // Save options
                if (isset($options->id)) { // reusing existing record
                    $DB->update_record('question_calculated', $options);
                } else { // new options
                    $DB->insert_record('question_calculated', $options);
                }
            }
        }
        // delete old answer records
        if (!empty($oldanswers)) {
            foreach($oldanswers as $oa) {
                $DB->delete_records('question_answers', array('id' => $oa->id));
            }
        }

        // delete old answer records
        if (!empty($oldoptions)) {
            foreach($oldoptions as $oo) {
                $DB->delete_records('question_calculated', array('id' => $oo->id));
            }
        }

        if(isset($question->import_process)&&$question->import_process) {
            $this->import_datasets($question);
        } else {
            //save datasets and datatitems from form i.e in question
            //  $datasetdefs = $this->get_dataset_definitions($question->id, array());
            $question->dataset = $question->datasetdef ;
            //       $this->save_dataset_definitions($question);
            // Save datasets
            $datasetdefinitions = $this->get_dataset_definitions($question->id, $question->dataset);
            $tmpdatasets = array_flip($question->dataset);
            $defids = array_keys($datasetdefinitions);
            $datasetdefs = array();
            foreach ($defids as $defid) {
                $datasetdef = &$datasetdefinitions[$defid];
                if (isset($datasetdef->id)) {
                    if (!isset($tmpdatasets[$defid])) {
                        // This dataset is not used any more, delete it
                        $DB->delete_records('question_datasets', array('question' => $question->id, 'datasetdefinition' => $datasetdef->id));
                        // if ($datasetdef->category == 0) { // Question local dataset
                        $DB->delete_records('question_dataset_definitions', array('id' => $datasetdef->id));
                        $DB->delete_records('question_dataset_items', array('definition' => $datasetdef->id));
                        // }
                    }
                    // This has already been saved or just got deleted
                    unset($datasetdefinitions[$defid]);
                    continue;
                }
                $datasetdef->id = $DB->insert_record('question_dataset_definitions', $datasetdef);
                $datasetdefs[]= clone($datasetdef);
                $questiondataset = new stdClass;
                $questiondataset->question = $question->id;
                $questiondataset->datasetdefinition = $datasetdef->id;
                $DB->insert_record('question_datasets', $questiondataset);
                unset($datasetdefinitions[$defid]);
            }
            // Remove local obsolete datasets as well as relations
            // to datasets in other categories:
            if (!empty($datasetdefinitions)) {
                foreach ($datasetdefinitions as $def) {
                    $DB->delete_records('question_datasets', array('question' => $question->id, 'datasetdefinition' => $def->id));
                    if ($def->category == 0) { // Question local dataset
                        $DB->delete_records('question_dataset_definitions', array('id' => $def->id));
                        $DB->delete_records('question_dataset_items', array('definition' => $def->id));
                    }
                }
            }
            $datasetdefs = $this->get_dataset_definitions($question->id, $question->dataset);
            // Handle adding and removing of dataset items
            $i = 1;
            ksort($question->definition);
            foreach ($question->definition as $key => $defid) {
                $addeditem = new stdClass();
                $addeditem->definition = $datasetdefs[$defid]->id;
                $addeditem->value = $question->number[$i];
                $addeditem->itemnumber = ceil($i / count($datasetdefs));
                if (empty($question->makecopy) && $question->itemid[$i]) {
                    // Reuse any previously used record
                    $addeditem->id = $question->itemid[$i];
                    $DB->update_record('question_dataset_items', $addeditem);
                } else {
                    $DB->insert_record('question_dataset_items', $addeditem);
                }
                $i++;
            }
            $maxnumber = -1;
            if (isset($addeditem->itemnumber) && $maxnumber < $addeditem->itemnumber){
                $maxnumber = $addeditem->itemnumber;
                foreach ($datasetdefs as $key => $newdef) {
                    if (isset($newdef->id) && $newdef->itemcount <= $maxnumber) {
                        $newdef->itemcount = $maxnumber;
                        // Save the new value for options
                        $DB->update_record('question_dataset_definitions', $newdef);
                    }
                }
            }
        }
        // Report any problems.
        //convert to calculated
        if(!empty($question->makecopy) && !empty($question->convert)) {
            $DB->set_field('question', 'qtype', 'calculated', array('id'=> $question->id));
        }
        $result = $QTYPES['numerical']->save_numerical_options($question);
        if (isset($result->error)) {
            return $result;
        }

        if (!empty($result->notice)) {
            return $result;
        }
        return true;
    }
    function finished_edit_wizard(&$form) {
        return true ; //isset($form->backtoquiz);
    }
    function wizard_pages_number() {
        return 1 ;
    }


    function custom_generator_tools_part(&$mform, $idx, $j){

        $minmaxgrp = array();
        $minmaxgrp[] =& $mform->createElement('text', "calcmin[$idx]", get_string('calcmin', 'qtype_calculated'));
        $minmaxgrp[] =& $mform->createElement('text', "calcmax[$idx]", get_string('calcmax', 'qtype_calculated'));
        $mform->addGroup($minmaxgrp, 'minmaxgrp', get_string('minmax', 'qtype_calculated'), ' - ', false);
        $mform->setType("calcmin[$idx]", PARAM_NUMBER);
        $mform->setType("calcmax[$idx]", PARAM_NUMBER);

        $precisionoptions = range(0, 10);
        $mform->addElement('select', "calclength[$idx]", get_string('calclength', 'qtype_calculated'), $precisionoptions);

        $distriboptions = array('uniform' => get_string('uniform', 'qtype_calculated'), 'loguniform' => get_string('loguniform', 'qtype_calculated'));
        $mform->addElement('hidden', "calcdistribution[$idx]", 'uniform');
        $mform->setType("calcdistribution[$idx]", PARAM_INT);


    }

    function comment_header($answers) {
        //$this->get_question_options($question);
        $strheader = "";
        $delimiter = '';

        // $answers = $question->options->answers;

        foreach ($answers as $key => $answer) {
         /*   if (is_string($answer)) {
                $strheader .= $delimiter.$answer;
         } else {*/
            $strheader .= $delimiter.$answer->answer;
            // }
            $delimiter = '<br/><br/><br/>';
        }
        return $strheader;
    }

    function tolerance_types() {
        return array('1'  => get_string('relative', 'quiz'),
            '2'  => get_string('nominal', 'quiz'),
            //        '3'  => get_string('geometric', 'quiz')
        );
    }

    function dataset_options($form, $name, $mandatory=true,$renameabledatasets=false) {
        // Takes datasets from the parent implementation but
        // filters options that are currently not accepted by calculated
        // It also determines a default selection...
        //$renameabledatasets not implemented anmywhere
        list($options, $selected) = $this->dataset_options_from_database($form, $name,'','qtype_calculated');
        //  list($options, $selected) = $this->dataset_optionsa($form, $name);

        foreach ($options as $key => $whatever) {
            if (!preg_match('~^1-~', $key) && $key != '0') {
                unset($options[$key]);
            }
        }
        if (!$selected) {
            if ($mandatory){
                $selected =  "1-0-$name"; // Default
            }else {
                $selected = "0"; // Default
            }
        }
        return array($options, $selected);
    }


    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        global $DB;
        list($form, $question) = parent::generate_test($name, $courseid);
        $form->feedback = 1;
        $form->multiplier = array(1, 1);
        $form->shuffleanswers = 1;
        $form->noanswers = 1;
        $form->qtype ='calculatedsimple';
        $question->qtype ='calculatedsimple';
        $form->answers = array('{a} + {b}');
        $form->fraction = array(1);
        $form->tolerance = array(0.01);
        $form->tolerancetype = array(1);
        $form->correctanswerlength = array(2);
        $form->correctanswerformat = array(1);
        $form->questiontext = "What is {a} + {b}?";

        if ($courseid) {
            $course = $DB->get_record('course', array('id'=> $courseid));
        }

        $new_question = $this->save_question($question, $form);

        $dataset_form = new stdClass();
        $dataset_form->nextpageparam["forceregeneration"]= 1;
        $dataset_form->calcmin = array(1 => 1.0, 2 => 1.0);
        $dataset_form->calcmax = array(1 => 10.0, 2 => 10.0);
        $dataset_form->calclength = array(1 => 1, 2 => 1);
        $dataset_form->number = array(1 => 5.4 , 2 => 4.9);
        $dataset_form->itemid = array(1 => '' , 2 => '');
        $dataset_form->calcdistribution = array(1 => 'uniform', 2 => 'uniform');
        $dataset_form->definition = array(1 => "1-0-a",
            2 => "1-0-b");
        $dataset_form->nextpageparam = array('forceregeneration' => false);
        $dataset_form->addbutton = 1;
        $dataset_form->selectadd = 1;
        $dataset_form->courseid = $courseid;
        $dataset_form->cmid = 0;
        $dataset_form->id = $new_question->id;
        $this->save_dataset_items($new_question, $dataset_form);

        return $new_question;
    }

    function move_files($questionid, $oldcontextid, $newcontextid) {
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);

        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'qtype_calculatedsimple', 'instruction', $questionid);
    }

    protected function delete_files($questionid, $contextid) {
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
        $fs->delete_area_files($contextid, 'qtype_calculatedsimple', 'instruction', $questionid);
    }

    function check_file_access($question, $state, $options, $contextid, $component,
            $filearea, $args) {
        $itemid = reset($args);
        if ($component == 'question' && $filearea == 'answerfeedback') {

            // check if answer id exists
            $result = $options->feedback && array_key_exists($itemid, $question->options->answers);
            if (!$result) {
                return false;
            }
            // check response
            if (!$this->check_response($question, $state)) {
                return false;
            }
            return true;
        } else if ($filearea == 'instruction') {
            // TODO: should it be display all the time like questiontext?
            // check if question id exists
            if ($itemid != $question->id) {
                return false;
            } else {
                return true;
            }
        } else {
            return parent::check_file_access($question, $state, $options, $contextid, $component,
                    $filearea, $args);
        }
        return true;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_calculatedsimple_qtype());

if ( ! defined ("CALCULATEDSIMPLE")) {
    define("CALCULATEDSIMPLE",    "calculatedsimple");
}
