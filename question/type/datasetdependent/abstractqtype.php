<?php  // $Id$

///////////////////////////////////////////////////////////////
/// ABSTRACT SUPERCLASS FOR QUSTION TYPES THAT USE DATASETS ///
///////////////////////////////////////////////////////////////
/**
 * @package questionbank
 * @subpackage questiontypes
 */

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


    function substitute_variables($str, $dataset) {
        foreach ($dataset as $name => $value) {
            if($value < 0 ){
                $str = str_replace('{'.$name.'}', '('.$value.')', $str);
            } else {
            $str = str_replace('{'.$name.'}', $value, $str);
        }
        }
        return $str;
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

     /**
     * This method prepare the $datasets in a format similar to dadatesetdefinitions_form.php
     * so that they can be saved
     * using the function save_dataset_definitions($form)
     *  when creating a new calculated question or
     *  whenediting an already existing calculated question
     * or by  function save_as_new_dataset_definitions($form, $initialid)
     *  when saving as new an already existing calculated question
     *
     * @param object $form
     * @param int $questionfromid default = '0'
     */
    function preparedatasets(&$form , $questionfromid='0'){
        // the dataset names present in the edit_question_form and edit_calculated_form are retrieved
        $possibledatasets = $this->find_dataset_names($form->questiontext);
        $mandatorydatasets = array();
            foreach ($form->answers as $answer) {
                $mandatorydatasets += $this->find_dataset_names($answer);
            }
        // if there are identical datasetdefs already saved in the original question.
        // either when editing a question or saving as new
        // they are retrieved using $questionfromid
        if ($questionfromid!='0'){
            $form->id = $questionfromid ;
        }
        $datasets = array();
        $key = 0 ;
        // always prepare the mandatorydatasets present in the answers
        // the $options are not used here
        foreach ($mandatorydatasets as $datasetname) {
            if (!isset($datasets[$datasetname])) {
                list($options, $selected) =
                        $this->dataset_options($form, $datasetname);
                $datasets[$datasetname]='';
                 $form->dataset[$key]=$selected ;
                $key++;
            }
        }
        // do not prepare possibledatasets when creating a question
        // they will defined and stored with datasetdefinitions_form.php
        // the $options are not used here
        if ($questionfromid!='0'){

        foreach ($possibledatasets as $datasetname) {
            if (!isset($datasets[$datasetname])) {
                list($options, $selected) =
                        $this->dataset_options($form, $datasetname,false);
                $datasets[$datasetname]='';
                 $form->dataset[$key]=$selected ;
                $key++;
            }
        }
        }
     return $datasets ;
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
    function save_question($question, $form, $course) {
        $wizardnow =  optional_param('wizardnow', '', PARAM_ALPHA);
        $id = optional_param('id', 0, PARAM_INT); // question id
        // in case 'question'
        // for a new question $form->id is empty
        // when saving as new question
        //   $question->id = 0, $form is $data from question2.php
        //   and $data->makecopy is defined as $data->id is the initial question id
        // edit case. If it is a new question we don't necessarily need to
        // return a valid question object

        // See where we're coming from
        switch($wizardnow) {
            case '' :
            case 'question': // coming from the first page, creating the second
                if (empty($form->id)) { // for a new question $form->id is empty
                    $question = parent::save_question($question, $form, $course);
                    //prepare the datasets using default $questionfromid
                    $this->preparedatasets($form);
                    $form->id = $question->id;
                    $this->save_dataset_definitions($form);
                } else if (!empty($form->makecopy)){
                    $questionfromid =  $form->id ;
                    $question = parent::save_question($question, $form, $course);
                    //prepare the datasets
                    $this->preparedatasets($form,$questionfromid);
                    $form->id = $question->id;
                    $this->save_as_new_dataset_definitions($form,$questionfromid );
                }  else {// editing a question
                    $question = parent::save_question($question, $form, $course);
                    //prepare the datasets
                    $this->preparedatasets($form,$question->id);
                    $form->id = $question->id;
                    $this->save_dataset_definitions($form);
                }
                break;
            case 'datasetdefinitions':

                $this->save_dataset_definitions($form);
                break;
            case 'datasetitems':
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
                if (!isset($tmpdatasets[$defid])) {
                // This dataset is not used any more, delete it
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
    /** This function create a copy of the datasets ( definition and dataitems)
    * from the preceding question if they remain in the new question
    * otherwise its create the datasets that have been added as in the
    * save_dataset_definitions()
    */
    function save_as_new_dataset_definitions($form, $initialid) {
    global $CFG ;
        // Get the datasets from the intial question
        $datasetdefinitions = $this->get_dataset_definitions($initialid, $form->dataset);
        // $tmpdatasets contains those of the new question
        $tmpdatasets = array_flip($form->dataset);
        $defids = array_keys($datasetdefinitions);// new datasets
        foreach ($defids as $defid) {
            $datasetdef = &$datasetdefinitions[$defid];
            if (isset($datasetdef->id)) {
                // This dataset exist in the initial question
                if (!isset($tmpdatasets[$defid])) {
                    // do not exist in the new question so ignore
                    unset($datasetdefinitions[$defid]);
                    continue;
                }
                // create a copy but not for category one
                if (0 == $datasetdef->category) {
                   $olddatasetid = $datasetdef->id ;
                   $olditemcount = $datasetdef->itemcount ;
                   $datasetdef->itemcount =0;
                   if (!$datasetdef->id = insert_record(
                        'question_dataset_definitions', $datasetdef)) {
                        error("Unable to create dataset $defid");
                   }
                   //copy the dataitems
                   $olditems = get_records_sql( // Use number as key!!
                        " SELECT itemnumber, value
                          FROM {$CFG->prefix}question_dataset_items
                          WHERE definition =  $olddatasetid ");
                   if (count($olditems) > 0 ) {
                        $itemcount = 0;
                        foreach($olditems as $item ){
                            $item->definition = $datasetdef->id;
                        if (!insert_record('question_dataset_items', $item)) {
                            error("Unable to insert dataset item $item->itemnumber with $item->value for $datasetdef->name");
                        }
                        $itemcount++;
                        }
                        //update item count
                        $datasetdef->itemcount =$itemcount;
                        update_record('question_dataset_definitions', $datasetdef);
                    } // end of  copy the dataitems
                }// end of  copy the datasetdef
                // Create relation to the new question with this
                // copy as new datasetdef from the initial question
                $questiondataset = new stdClass;
                $questiondataset->question = $form->id;
                $questiondataset->datasetdefinition = $datasetdef->id;
                if (!insert_record('question_datasets',
                                   $questiondataset)) {
                    error("Unable to create relation to dataset $name");
                }
                unset($datasetdefinitions[$defid]);
                continue;
            }// end of datasetdefs from the initial question
            // really new one code similar to save_dataset_definitions()
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
        error("No virtual question type for question type ".$this->name());
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

    function dataset_options($form, $name,$prefix='',$langfile='quiz') {

        // First options - it is not a dataset...
        $options['0'] = get_string($prefix.'nodataset', $langfile);

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
                $options[$key] = get_string($prefix."keptlocal$type", $langfile);
            } else {
                $options[$key] = get_string($prefix."newlocal$type", $langfile);
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
                    $options[$key] = get_string($prefix."keptcategory$type", $langfile);
                } else {
                    $options[$key] = get_string($prefix."existingcategory$type", $langfile);
                }
            } else {
                $options[$key] = get_string($prefix."newcategory$type", $langfile);
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
