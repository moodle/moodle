<?php  // $Id$

///////////////////////////////////////////////////////////////
/// ABSTRACT SUPERCLASS FOR QUSTION TYPES THAT USE DATASETS ///
///////////////////////////////////////////////////////////////

require_once($CFG->libdir.'/filelib.php');

define("LITERAL", "1");
define("FILE", "2");
define("LINK", "3");

class question_dataset_dependent_questiontype extends default_questiontype {

    var $virtualqtype = false;

    function name() {
        return 'datasetdependent';
    }

    function save_question_options($question) {
        // Default does nothing...
        return true;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        // Find out how many datasets are available
        global $CFG;
        if(!$maxnumber = (int)get_field_sql(
                            "SELECT MIN(a.itemcount)
                            FROM {$CFG->prefix}question_dataset_definitions a,
                                 {$CFG->prefix}question_datasets b
                            WHERE b.question = $question->id
                            AND   a.id = b.datasetdefinition")) {
            error("Couldn't get the specified dataset for a calculated " .
                  "question! (question: {$question->id}");
        }

        // Choose a random dataset
        $state->options->datasetitem = rand(1, $maxnumber);
        $state->options->dataset =
         $this->pick_question_dataset($question,$state->options->datasetitem);
        $state->responses = array('' => '');
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        if (!ereg('^dataset([0-9]+)[^-]*-(.*)$',
                $state->responses[''], $regs)) {
            notify ("Wrongly formatted raw response answer " .
                   "{$state->responses['']}! Could not restore session for " .
                   " question #{$question->id}.");
            $state->options->datasetitem = 1;
            $state->options->dataset = array();
            $state->responses = array('' => '');
            return false;
        }

        // Restore the chosen dataset
        $state->options->datasetitem = $regs[1];
        $state->options->dataset =
         $this->pick_question_dataset($question,$state->options->datasetitem);
        $state->responses = array('' => $regs[2]);
        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        $responses = 'dataset'.$state->options->datasetitem.'-'.
         $state->responses[''];

        // Set the legacy answer field
        if (!set_field('question_states', 'answer', $responses, 'id',
         $state->id)) {
            return false;
        }
        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions,
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
         $state, $cmoptions, $options);
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        // Forward the grading to the virtual qtype
        foreach ($question->options->answers as $answer) {
            $answer->answer = $this->substitute_variables($answer->answer,
             $state->options->dataset);
        }
        $virtualqtype = $this->get_virtual_qtype();
        return $virtualqtype->grade_responses($question, $state, $cmoptions) ;
    }

    // ULPGC ecastro
    function check_response(&$question, &$state) {
        // Forward the checking to the virtual qtype
        foreach ($question->options->answers as $answer) {
            $answer->answer = $this->substitute_variables($answer->answer,
             $state->options->dataset);
        }
        $virtualqtype = $this->get_virtual_qtype();
        return $virtualqtype->check_response($question, $state) ;
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
                 FROM {$CFG->prefix}question_dataset_items i,
                      {$CFG->prefix}question_dataset_definitions d,
                      {$CFG->prefix}question_datasets q
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

    // This gets called by editquestion.php after the standard question is saved
    function print_next_wizard_page(&$question, &$form, $course) {
        global $CFG, $USER, $SESSION, $COURSE;

        // Catch invalid navigation & reloads
        if (empty($question->id) && empty($SESSION->datasetdependent)) {
            redirect('edit.php?courseid='.$COURSE->id, 'The page you are loading has expired.', 3);
        }

        // See where we're coming from
        switch($form->wizardpage) {
            case 'question':
                require("$CFG->dirroot/question/type/datasetdependent/datasetdefinitions.php");
                break;
            case 'datasetdefinitions':
            case 'datasetitems':
                require("$CFG->dirroot/question/type/datasetdependent/datasetitems.php");
                break;
            default:
                error('Incorrect or no wizard page specified!');
                break;
        }
    }

    // This gets called by question2.php after the standard question is saved
    function &next_wizard_form($submiturl, $question, $wizardnow){
        global $CFG, $SESSION, $COURSE;

        // Catch invalid navigation & reloads
        if (empty($question->id) && empty($SESSION->datasetdependent)) {
            redirect('edit.php?courseid='.$COURSE->id, 'The page you are loading has expired. Cannot get next wizard form.', 3);
        }
        if (empty($question->id)){
            $question =& $SESSION->datasetdependent->questionform;
        }

        // See where we're coming from
        switch($wizardnow) {
            case 'datasetdefinitions':
                require("$CFG->dirroot/question/type/datasetdependent/datasetdefinitions_form.php");
                $mform =& new question_dataset_dependent_definitions_form("$submiturl?wizardnow=datasetdefinitions", $question);
                break;
            case 'datasetitems':
                require("$CFG->dirroot/question/type/datasetdependent/datasetitems_form.php");
                $regenerate = optional_param('forceregeneration', 0, PARAM_BOOL);
                $mform =& new question_dataset_dependent_items_form("$submiturl?wizardnow=datasetitems", $question, $regenerate);
                break;
            default:
                error('Incorrect or no wizard page specified!');
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
    function display_question_editing_page(&$mform, $question, $wizardnow){
        switch ($wizardnow){
            case '':
                //on first page default display is fine
                parent::display_question_editing_page($mform, $question, $wizardnow);
                return;
                break;
            case 'datasetdefinitions':
                print_heading_with_help(get_string("choosedatasetproperties", "quiz"), "questiondatasets", "quiz");
                break;
            case 'datasetitems':
                print_heading_with_help(get_string("editdatasets", "quiz"), 'questiondatasets', "quiz");
                break;
        }


        $mform->display();

    }

    function save_question($question, $form, $course) {
        // For dataset dependent questions a wizard is used for editing
        // questions. Therefore saving the question is delayed until
        // we're through with the whole wizard.
        global $SESSION;
        $wizardnow =  optional_param('wizardnow', '', PARAM_ALPHA);


        // We need to return a valid question object (incl. id) if we're in the
        // edit case. If it is a new question we don't necessarily need to
        // return a valid question object

        // See where we're coming from
        switch($wizardnow) {
            case '' :
            case 'question': // coming from the first page, creating the second
                if (empty($form->id)) {
                    $form->options = new stdClass;
                    $form->options->answers = array();
                    foreach (array_keys($form->answers) as $i){
                        $form->options->answers[$i] = new stdClass;
                        $form->options->answers[$i]->answer = $form->answers[$i];
                        $form->options->answers[$i]->tolerance = $form->tolerance[$i];
                        $form->options->answers[$i]->tolerancetype = $form->tolerancetype[$i];
                        $form->options->answers[$i]->correctanswerlength = $form->correctanswerlength[$i];
                        $form->options->answers[$i]->correctanswerformat = $form->correctanswerformat[$i];
                        $form->options->answers[$i]->feedback = $form->feedback[$i];
                    }

                    $SESSION->datasetdependent = new stdClass;
                    $SESSION->datasetdependent->questionform = $form;
                } else {
                    $question = parent::save_question($question, $form, $course);
                }
                break;
            case 'datasetdefinitions':
                if (empty($question->id)) {
                    if (isset($SESSION->datasetdependent->questionform)) {
                        $SESSION->datasetdependent->definitionform = $form;
                    } else {
                        // Something went wrong, go back to the first page
                        redirect("question2.php?category={$question->category}" ."&qtype={$question->qtype}");
                    }
                } else {
                    $this->save_dataset_definitions($form);
                }
                break;
            case 'datasetitems':
                if (empty($form->id) && isset($form->addbutton)) {
                    $question = parent::save_question($question, $SESSION->datasetdependent->questionform, $course);
                    $SESSION->datasetdependent->definitionform->id = $form->id = $question->id;
                    $this->save_dataset_definitions($SESSION->datasetdependent->definitionform);
                    unset($SESSION->datasetdependent);
                }
                $this->save_dataset_items($question, $form);
                break;
            default:
                error('Incorrect or no wizard page specified!');
                break;
        }
        return $question;
    }

    function save_dataset_items($question, $fromform){
        //overridden in child classes
    }

    function get_dataset_definitions($questionid, $newdatasets) {
        //get the existing datasets for this question
        $datasetdefs = array();
        if (!empty($questionid)) {
            global $CFG;
            $sql = "SELECT i.*
                    FROM {$CFG->prefix}question_datasets d,
                         {$CFG->prefix}question_dataset_definitions i
                    WHERE d.question = '$questionid'
                    AND   d.datasetdefinition = i.id
                   ";
            if ($records = get_records_sql($sql)) {
                foreach ($records as $r) {
                    $datasetdefs["$r->type-$r->category-$r->name"] = $r;
                }
            }
        }

        foreach ($newdatasets as $dataset) {
            if (!$dataset) {
                continue; // The no dataset case...
            }

            if (!isset($datasetdefs[$dataset])) {
                //make new datasetdef
                list($type, $category, $name) = explode('-', $dataset, 3);
                $datasetdef = new stdClass;
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

    function save_dataset_definitions($form) {
        // Save datasets
        $datasetdefinitions = $this->get_dataset_definitions($form->id, $form->dataset);
        $tmpdatasets = array_flip($form->dataset);
        $defids = array_keys($datasetdefinitions);
        foreach ($defids as $defid) {
            $datasetdef = &$datasetdefinitions[$defid];
            if (isset($datasetdef->id)) {
                // This dataset is not used any more, delete it
                if (!isset($tmpdatasets[$defid])) {
                    delete_records('question_datasets',
                               'question', $form->id,
                               'datasetdefinition', $datasetdef->id);
                    if ($datasetdef->category == 0) { // Question local dataset
                        delete_records('question_dataset_definitions',
                         'id', $datasetdef->id);
                        delete_records('question_dataset_items',
                         'definition', $datasetdef->id);
                    }
                }
                // This has already been saved or just got deleted
                unset($datasetdefinitions[$defid]);
                continue;
            }

            if (!$datasetdef->id = insert_record(
                    'question_dataset_definitions', $datasetdef)) {
                error("Unable to create dataset $defid");
            }

            if (0 != $datasetdef->category) {
                // We need to look for already existing
                // datasets in the category.
                // By first creating the datasetdefinition above we
                // can manage to automatically take care of
                // some possible realtime concurrence
                if ($olderdatasetdefs = get_records_select(
                        'question_dataset_definitions',
                        "type = '$datasetdef->type'
                        AND name = '$datasetdef->name'
                        AND category = '$datasetdef->category'
                        AND id < $datasetdef->id
                        ORDER BY id DESC")) {

                    while ($olderdatasetdef = array_shift($olderdatasetdefs)) {
                        delete_records('question_dataset_definitions',
                                   'id', $datasetdef->id);
                        $datasetdef = $olderdatasetdef;
                    }
                }
            }

            // Create relation to this dataset:
            $questiondataset = new stdClass;
            $questiondataset->question = $form->id;
            $questiondataset->datasetdefinition = $datasetdef->id;
            if (!insert_record('question_datasets',
                               $questiondataset)) {
                error("Unable to create relation to dataset $name");
            }
            unset($datasetdefinitions[$defid]);
        }

        // Remove local obsolete datasets as well as relations
        // to datasets in other categories:
        if (!empty($datasetdefinitions)) {
            foreach ($datasetdefinitions as $def) {
                delete_records('question_datasets',
                               'question', $form->id,
                               'datasetdefinition', $def->id);

                if ($def->category == 0) { // Question local dataset
                    delete_records('question_dataset_definitions', 'id', $def->id);
                    delete_records('question_dataset_items',
                                   'definition', $def->id);
                }
            }
        }
    }



/*

    function save_question($question, &$form, $course) {
        // For dataset dependent questions a wizard is used for editing
        // questions. Therefore saving the question is delayed until
        // we're through with the whole wizard.
        global $SESSION;
        $this->validate_form($form);

        // See where we're coming from
        switch($form->wizardpage) {
            case 'question':
                unset($SESSION->datasetdependent); // delete any remaining data
                                                   // from previous wizards
                if (empty($form->id)) {
                    $SESSION->datasetdependent->question = $form;
                    $question = $this->create_runtime_question($question, $form);
                } else {
                    $question = parent::save_question($question, $form, $course);
                }
                break;
            case 'datasetdefinitions':
                $SESSION->datasetdependent->datasetdefinitions = $form;
                if (empty($form->id)) {
                    $question = $this->create_runtime_question($question, $SESSION->datasetdependent->question);
                } else {
                    $this->save_dataset_definitions($form);
                    $this->get_question_options($question);
                    //unset($SESSION->datasetdependent->datasetdefinitions);
                }
                //$this->get_question_options($question);
                break;
            case 'datasets':
                if (!empty($form->addbutton) && isset($SESSION->datasetdependent->question)) {
                    echo "saving";
                    $question = parent::save_question($question, $SESSION->datasetdependent->question, $course);
                    $SESSION->datasetdependent->datasetdefinitions->id = $question->id;
                    $this->save_dataset_definitions($SESSION->datasetdependent->datasetdefinitions);
                    //$this->get_dataset_definitions($question);
                    unset($SESSION->datasetdependent);
                }
                dump($question);
                if (empty($question->id)) {
                    $question = $this->create_runtime_question($question, $SESSION->datasetdependent->question);
                } else {
                    $this->get_question_options($question);
                }

                break;
            default:
                error('Incorrect or no wizard page specified!');
                break;
        }
        return $question;
    }

*/

/*
    function create_runtime_question($question, $form) {
        $question->name               = trim($form->name);
        $question->questiontext       = trim($form->questiontext);
        $question->questiontextformat = $form->questiontextformat;
        $question->parent             = isset($form->parent)? $form->parent : 0;
        $question->length = $this->actual_number_of_questions($question);
        $question->penalty = isset($form->penalty) ? $form->penalty : 0;

        if (empty($form->image)) {
            $question->image = "";
        } else {
            $question->image = $form->image;
        }

        if (empty($question->name)) {
            $question->name = strip_tags($question->questiontext);
            if (empty($question->name)) {
                $question->name = '-';
            }
        }

        if ($question->penalty > 1 or $question->penalty < 0) {
            $question->errors['penalty'] = get_string('invalidpenalty', 'quiz');
        }

        if (isset($form->defaultgrade)) {
            $question->defaultgrade = $form->defaultgrade;
        }
        return $question;
    }
*/

/*
    function validate_form($form) {
        switch($form->wizardpage) {
            case 'question':
                error('Validation for the wizardpage "question" needs to be ' .
                      'defined in the inheriting questiontype.');
            case 'datasetdefinitions':
                // Nothing to validate
                $SESSION->datasetdependent->datasetdefinitions = $form;
                break;
            case 'datasets':
                break;
            default:
                error('Incorrect or no wizard page specified!');
                break;
        }
        return true;
    }
*/
/// Dataset functionality
    function pick_question_dataset($question, $datasetitem) {
        // Select a dataset in the following format:
        // An array indexed by the variable names (d.name) pointing to the value
        // to be substituted
        global $CFG;
        if (!$dataset = get_records_sql(
                        "SELECT d.name, i.value
                        FROM {$CFG->prefix}question_dataset_definitions d,
                             {$CFG->prefix}question_dataset_items i,
                             {$CFG->prefix}question_datasets q
                        WHERE q.question = $question->id
                        AND q.datasetdefinition = d.id
                        AND d.id = i.definition
                        AND i.itemnumber = $datasetitem")) {
            error("Couldn't get the specified dataset for a dataset dependent " .
                  "question! (question: {$question->id}, " .
                  "datasetitem: {$datasetitem})");
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

    function dataset_options($form, $name) {

        // First options - it is not a dataset...
        $options['0'] = get_string('nodataset', 'quiz');

        // Construct question local options
        global $CFG;
        $currentdatasetdef = get_record_sql(
                "SELECT a.*
                   FROM {$CFG->prefix}question_dataset_definitions a,
                        {$CFG->prefix}question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND b.question = '$form->id'
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
                   FROM {$CFG->prefix}question_dataset_definitions a,
                        {$CFG->prefix}question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND a.category = '$form->category'
                    AND a.name = '$name'");
        foreach(array( LITERAL, FILE, LINK) as $type) {
            $key = "$type-$form->category-$name";
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

    function create_virtual_nameprefix($nameprefix, $datasetinput) {
        if (!ereg('([0-9]+)' . $this->name() . '$', $nameprefix, $regs)) {
            error("Wrongly formatted nameprefix $nameprefix");
        }
        $virtualqtype = $this->get_virtual_qtype();
        return $nameprefix . $regs[1] . $virtualqtype->name();
    }

}
//// END OF CLASS ////
?>
