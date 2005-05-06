<?php  // $Id$

///////////////////////////////////////////////////////////////
/// ABSTRACT SUPERCLASS FOR QUSTION TYPES THAT USE DATASETS ///
///////////////////////////////////////////////////////////////

require_once($CFG->libdir.'/filelib.php');

define("LITERAL", "1");
define("FILE", "2");
define("LINK", "3");

class quiz_dataset_dependent_questiontype extends quiz_default_questiontype {

    var $virtualqtype = false;

    function name() {
        return 'datasetdependent';
    }

    function save_question_options($question, $options) {
        // Default does nothing...
        return true;
    }

    function create_session_and_responses(&$question, &$state, $quiz, $attempt) {
        // Find out how many datasets are available
        global $CFG;
        if(!$maxnumber = (int)get_field_sql(
                            "SELECT MAX(a.itemcount)
                            FROM {$CFG->prefix}quiz_dataset_definitions a,
                                 {$CFG->prefix}quiz_question_datasets b
                            WHERE b.question = $question->id
                            AND   a.id = b.datasetdefinition")) {
            error("Couldn't get the specified dataset for a calculated " .
                  "question! (question: {$question->id}, " .
                  "datasetnumber: {$datasetnumber})");
        }

        // Choose a random dataset
        $state->options->datasetnumber = rand(1, $maxnumber);
        $state->options->dataset =
         $this->pick_question_dataset($question,$state->options->datasetnumber);
        $state->responses = array('' => '');
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        if (!ereg('^dataset([0-9]+)[^-]*-(.*)$',
                $state->responses[''], $regs)) {
            notify ("Wrongly formatted raw response answer " .
                   "{$state->responses['']}! Could not restore session for " .
                   " question #{$question->id}.");
            $state->options->datasetnumber = 1;
            $state->options->dataset = array();
            $state->responses = array('' => '');
            return false;
        }

        // Restore the chosen dataset
        $state->options->datasetnumber = $regs[1];
        $state->options->dataset =
         $this->pick_question_dataset($question,$state->options->datasetnumber);
        $state->responses = array('' => $regs[2]);
        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        $responses = 'dataset'.$state->options->datasetnumber.'-'.
         $state->responses[''];

        // Set the legacy answer field
        if (!set_field('quiz_states', 'answer', $responses, 'id',
         $state->id)) {
            return false;
        }
        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $quiz,
     $options) {
        // Substitute variables in questiontext before giving the data to the
        // virtual type for printing
        $virtualqtype = $this->get_virtual_qtype();
        $unit = $virtualqtype->get_default_numerical_unit($question);
        foreach ($question->options->answers as $answer) {
            $answer->answer = $this->substitute_variables($answer->answer,
             $state->options->dataset);
        }
        $question->questiontext = $this->substitute_variables(
         $question->questiontext, $state->options->dataset);
        $virtualqtype->print_question_formulation_and_controls($question,
         $state, $quiz, $options);
    }

    function get_correct_responses(&$question, &$state) {
        foreach ($question->options->answers as $answer) {
            $answer->answer = $this->substitute_variables(
             $answer->answer, $state->options->dataset);
            if (((float) $answer->fraction) === 1.0) {
                return array('' => $answer->answer);
            }
        }
        return null;
    }

    function grade_responses(&$question, &$state, $quiz) {
        // Forward the grading to the virtual qtype
        foreach ($question->options->answers as $answer) {
            $answer->answer = $this->substitute_variables($answer->answer,
             $state->options->dataset);
        }
        $virtualqtype = $this->get_virtual_qtype();
        return $virtualqtype->grade_responses($question, $state, $quiz) ;
    }

    function substitute_variables($str, $dataset) {
        foreach ($dataset as $name => $value) {
            $str = str_replace('{'.$name.'}', $value, $str);
        }
        return $str;
    }

    function uses_quizfile($question, $relativefilepath) {
        // Check whether the specified file is available by any
        // dataset item on this question...
        global $CFG;
        if (get_record_sql(" SELECT *
                 FROM {$CFG->prefix}quiz_dataset_items i,
                      {$CFG->prefix}quiz_dataset_definitions d,
                      {$CFG->prefix}quiz_question_datasets q
                WHERE i.value = '$relativefilepath'
                  AND d.id = i.definition AND d.type = 2
                  AND d.id = q.datasetdefinition
                  AND q.question = $question->id ")) {

            return true;
        } else {
            // Make the check of the parent:
            return parent::uses_quizfile($question, $relativefilepath);
        }
    }

    function finished_edit_wizard(&$form) {
        return isset($form->backtoquiz);
    }

    function save_question($question, $form, $course, $subtypeoptions=false) {
    // For dataset dependent questions a wizard is used for editing
    // questions. Therefore calls from question.php are ignored.
    // Instead questions are saved when this method is called by
    // editquestion.php

        if ($subtypeoptions) {
            // Let's save the question
            // We need to save the question first (in order to get the id)
            // We then save the dataset definitions and finally we
            // save the subtype options...

            // Save question
            if (!empty($question->id)) { // Question already exists
                $question->version++;  // Update version number of question
                if (!update_record("quiz_questions", $question)) {
                    error("Could not update question!");
                }
            } else {   // Question is a new one
                // Set the unique code (not to be changed)
                $question->stamp = make_unique_id_code();
                $question->version = 1;
                if (!$question->id=insert_record("quiz_questions", $question)) {
                    error("Could not insert new question!");
                }
            }

            // Save datasets
            global $CFG;
            $datasetdefinitions = get_records_sql( // Indexed by name...
                "SELECT a.name, a.id, a.type, a.category
                  FROM {$CFG->prefix}quiz_dataset_definitions a,
                       {$CFG->prefix}quiz_question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND b.question = $question->id");

            foreach ($form->dataset as $dataset) {
                if (!$dataset) {
                    continue; // The no dataset case...
                }

                list($type, $category, $name) = explode('-', $dataset, 3);

                if (isset($datasetdefinitions[$name])
                        and $datasetdefinitions[$name]->type == $type
                        and $datasetdefinitions[$name]->category == $category) {
                    // Keep this dataset as it already fulfills our dreams
                    // by preventing it from being deleted
                    unset($datasetdefinitions[$name]);
                    continue;
                }

                // We need to create a new datasetdefinition
                unset ($datasetdef);
                $datasetdef->type = $type;
                $datasetdef->name = $name;
                $datasetdef->category = $category;

                if (!$datasetdef->id = insert_record(
                        'quiz_dataset_definitions', $datasetdef)) {
                    error("Unable to create dataset $name");
                }

                if ($category) {
                    // We need to look for already existing
                    // datasets in the category.
                    // By first creating the datasetdefinition above we
                    // can manage to automatically take care of
                    // some possible realtime concurrence
                    while ($olderdatasetdef = get_record_select(
                            'quiz_dataset_definitions',
                            " type = '$type' AND name = '$name'
                            AND category = '$category'
                            AND id < $datasetdef->id ")) {
                        // Use older dataset instead:
                        delete_records('quiz_dataset_definitions',
                                       'id', $datasetdef->id);
                        $datasetdef = $olderdatasetdef;
                    }
                }

                // Create relation to this dataset:
                unset($questiondataset);
                $questiondataset->question = $question->id;
                $questiondataset->datasetdefinition = $datasetdef->id;
                if (!insert_record('quiz_question_datasets',
                                   $questiondataset)) {
                    error("Unable to create relation to dataset $name");
                }
            }

            // Remove local obsolete datasets as well as relations
            // to datasets in other categories:
            if (!empty($datasetdefinitions)) {
                foreach ($datasetdefinitions as $def) {
                    delete_records('quiz_question_datasets',
                                   'question', $question->id,
                                   'datasetdefinition', $def->id);

                    if ($def->category == 0) { // Question local dataset
                        delete_records('quiz_dataset_definitions', 'id', $def->id);
                        delete_records('quiz_dataset_items',
                                       'definition', $def->id);
                    }
                }
            }

            // Save subtype options
            $this->save_question_options($question, $subtypeoptions);
            return $question;

        } else if (empty($form->editdatasets)) {
            // Parse for common question entries and
            // continue with editquestion.php by returning the question
            $question->name               = $form->name;
            $question->questiontext       = $form->questiontext;
            $question->questiontextformat = $form->questiontextformat;
            if (empty($form->image)) {
                $question->image = "";
            } else {
                $question->image = $form->image;
            }
            if (isset($form->defaultgrade)) {
                $question->defaultgrade = $form->defaultgrade;
            }
            return $question;
        } else {
            return $question;
        }
    }

/// Dataset functionality
    function pick_question_dataset($question, $datasetnumber) {
        // Select a dataset in the following format:
        // An array indexed by the variable names (d.name) pointing to the value
        // to be substituted
        global $CFG;
        if (!$dataset = get_records_sql(
                        "SELECT d.name, i.value
                        FROM {$CFG->prefix}quiz_dataset_definitions d,
                             {$CFG->prefix}quiz_dataset_items i,
                             {$CFG->prefix}quiz_question_datasets q
                        WHERE q.question = $question->id
                        AND q.datasetdefinition = d.id
                        AND d.id = i.definition
                        AND i.number = $datasetnumber")) {
            error("Couldn't get the specified dataset for a dataset dependent " .
                  "question! (question: {$question->id}, " .
                  "datasetnumber: {$datasetnumber})");
        }
        array_walk($dataset, create_function('&$val', '$val = $val->value;'));
        return $dataset;
    }

    function create_virtual_qtype() {
        error("No vitrual question type for question type ".$this->name());
    }

    function get_virtual_qtype() {
        if (!$this->virtualqtype) {
            $this->virtualqtype = $this->create_virtual_qtype();
        }
        return $this->virtualqtype;
    }

    function comment_header($question) {
    // Used by datasetitems.php
        // Default returns nothing and thus takes away the column
        return '';
    }

    function comment_on_datasetitems($question, $data, $number) {
    // Used by datasetitems.php
        // Default returns nothing
        return '';
    }

    function supports_dataset_item_generation() {
    // Used by datasetitems.php
        // Default does not support any item generation
        return false;
    }

    function custom_generator_tools($datasetdef) {
    // Used by datasetitems.php
        // If there is no generation support,
        // there cannot possibly be any custom tools either
        return '';
    }

    function generate_dataset_item($options) {
    // Used by datasetitems.php
        // By default nothing is generated
        return '';
    }

    function update_dataset_options($datasetdefs, $form) {
    // Used by datasetitems.php
    // Returns the updated datasets
        // By default the dataset options cannot be updated
        return $datasetdefs;
    }

    function dataset_options($question, $name) {

        // First options - it is not a dataset...
        $options['0'] = get_string('nodataset', 'quiz');

        // Construct question local options
        global $CFG;
        $currentdatasetdef = get_record_sql(
                "SELECT a.*
                   FROM {$CFG->prefix}quiz_dataset_definitions a,
                        {$CFG->prefix}quiz_question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND b.question = '$question->id'
                    AND a.name = '$name'")
        or $currentdatasetdef->type = '0';
        foreach (array( LITERAL, FILE, LINK) as $type) {
            $key = "$type-0-$name";
            if ($currentdatasetdef->type == $type
                    and $currentdatasetdef->category == 0) {
                $options[$key] = get_string("keptlocal$type", 'quiz');
            } else {
                $options[$key] = get_string("newlocal$type", 'quiz');
            }
        }

        // Construct question category options
        $categorydatasetdefs = get_records_sql(
                "SELECT a.type, a.id
                   FROM {$CFG->prefix}quiz_dataset_definitions a,
                        {$CFG->prefix}quiz_question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND a.category = '$question->category'
                    AND a.name = '$name'");
        foreach(array( LITERAL, FILE, LINK) as $type) {
            $key = "$type-$question->category-$name";
            if (isset($categorydatasetdefs[$type])
                    and $categorydef = $categorydatasetdefs[$type]) {
                if ($currentdatasetdef->type == $type
                        and $currentdatasetdef->id == $categorydef->id) {
                    $options[$key] = get_string("keptcategory$type", 'quiz');
                } else {
                    $options[$key] = get_string("existingcategory$type", 'quiz');
                }
            } else {
                $options[$key] = get_string("newcategory$type", 'quiz');
            }
        }

        // All done!
        return array($options, $currentdatasetdef->type
                ? "$currentdatasetdef->type-$currentdatasetdef->category-$name"
                : '');
    }

    function find_dataset_names($text) {
    /// Returns the possible dataset names found in the text as an array
    /// The array has the dataset name for both key and value
        $datasetnames = array();
        while (ereg('\\{([[:alpha:]][^>} <{"\']*)\\}', $text, $regs)) {
            $datasetnames[$regs[1]] = $regs[1];
            $text = str_replace($regs[0], '', $text);
        }
        return $datasetnames;
    }

/// The following functions are needed for database upgrade script only
    // This is used by extract_responses, so it needs to stay
    function create_virtual_nameprefix($nameprefix, $datasetinput) {
    // This default implementation is sometimes overridden
        if (!ereg('([0-9]+)' . $this->name() . '$', $nameprefix, $regs)) {
            error("Wrongly formatted nameprefix $nameprefix");
        }
        $virtualqtype = $this->get_virtual_qtype();
        return $nameprefix . $regs[1] . $virtualqtype->name();
    }

    function extract_response($rawresponse, $nameprefix) {
        if (!ereg('^dataset([;:0-9A-Za-z+/=]+)-(.*)$',
                $rawresponse->answer, $regs)) {
            error ("Wrongly formatted raw response answer $rawresponse->answer");
        }

        // Truncate raw response to fit the virtual qtype
        $rawresponse->answer = $regs[2];

        $virtualqtype = $this->get_virtual_qtype();
        $response = $virtualqtype->extract_response($rawresponse,
                $this->create_virtual_nameprefix($nameprefix, $regs[1]));
        $response[$nameprefix] = $regs[1];
        return $response;
    }
}
//// END OF CLASS ////
?>
