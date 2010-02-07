<?php

/////////////////
// CALCULATED ///
/////////////////

/// QUESTION TYPE CLASS //////////////////



class question_calculatedmulti_qtype extends question_calculated_qtype {

    // Used by the function custom_generator_tools:
    var $calcgenerateidhasbeenadded = false;
    public $virtualqtype = false;

    function name() {
        return 'calculatedmulti';
    }

    function has_wildcards_in_responses($question, $subqid) {
        return true;
    }

    function requires_qtypes() {
        return array('multichoice');
    }
/*
    function get_question_options(&$question) {
        // First get the datasets and default options
         global $CFG, $DB, $OUTPUT, $QTYPES;
        if (!$question->options = $DB->get_record('question_calculated_options', array('question' => $question->id))) {
          //  echo $OUTPUT->notification('Error: Missing question options for calculated question'.$question->id.'!');
          //  return false;
          $question->options->synchronize = 0;
        //  $question->options->multichoice = 1;
          
        }
    //    echo "<p> questionoptions <pre>";print_r($question);echo "</pre></p>";
     //   $QTYPES['numerical']->get_numerical_options($question);
         /* $question->options->unitgradingtype = 0;
          $question->options->unitpenalty = 0;
          $question->options->showunits = 0 ;
          $question->options->unitsleft = 0 ;
          $question->options->instructions = '' ;
   //     echo "<p> questionoptions <pre>";print_r($question);echo "</pre></p>";

        if (!$question->options->answers = $DB->get_records_sql(
                                "SELECT a.*, c.tolerance, c.tolerancetype, c.correctanswerlength, c.correctanswerformat " .
                                "FROM {question_answers} a, " .
                                "     {question_calculated} c " .
                                "WHERE a.question = ? " .
                                "AND   a.id = c.answer ".
                                "ORDER BY a.id ASC", array($question->id))) {
            echo $OUTPUT->notification('Error: Missing question answer for calculated question ' . $question->id . '!');
            return false;
        }


       if(false === parent::get_question_options($question)) {
            return false;
        }

        if (!$options = $DB->get_records('question_calculated', array('question' =>  $question->id))) {
            notify("No options were found for calculated question
             #{$question->id}! Proceeding with defaults.");
        //     $options = new Array();
            $options= new stdClass;
            $options->tolerance           = 0.01;
            $options->tolerancetype       = 1; // relative
            $options->correctanswerlength = 2;
            $options->correctanswerformat = 1; // decimals
        }

        // For historic reasons we also need these fields in the answer objects.
        // This should eventually be removed and related code changed to use
        // the values in $question->options instead.
         foreach ($question->options->answers as $key => $answer) {
            $answer = &$question->options->answers[$key]; // for PHP 4.x
           $answer->calcid              = $options->id;
            $answer->tolerance           = $options->tolerance;
            $answer->tolerancetype       = $options->tolerancetype;
            $answer->correctanswerlength = $options->correctanswerlength;
            $answer->correctanswerformat = $options->correctanswerformat;
        }

        //$virtualqtype = $this->get_virtual_qtype( $question);
      //  $QTYPES['numerical']->get_numerical_units($question);

        if( isset($question->export_process)&&$question->export_process){
            $question->options->datasets = $this->get_datasets_for_export($question);
        }
        return true;
    }
*/

    function save_question_options($question) {
        //$options = $question->subtypeoptions;
        // Get old answers:
        global $CFG, $DB, $QTYPES ;
        if (isset($question->answer) && !isset($question->answers)) {
            $question->answers = $question->answer;
        }
        // calculated options
        $update = true ; 
        $options = $DB->get_record("question_calculated_options", array("question" => $question->id));
        if (!$options) {
            $update = false;
            $options = new stdClass;
            $options->question = $question->id;
        }
        $options->synchronize = $question->synchronize;
       // $options->multichoice = $question->multichoice;
        $options->single = $question->single;
        $options->answernumbering = $question->answernumbering;
        $options->shuffleanswers = $question->shuffleanswers;
        $options->correctfeedback = trim($question->correctfeedback);
        $options->partiallycorrectfeedback = trim($question->partiallycorrectfeedback);
        $options->incorrectfeedback = trim($question->incorrectfeedback);
        if ($update) {
            if (!$DB->update_record("question_calculated_options", $options)) {
                $result->error = "Could not update calculated question options! (id=$options->id)";
                return $result;
            }
        } else {
            if (!$DB->insert_record("question_calculated_options", $options)) {
                $result->error = "Could not insert calculated question options!";
                return $result;
            }
        }

        // Get old versions of the objects
        if (!$oldanswers = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC')) {
            $oldanswers = array();
        }

        if (!$oldoptions = $DB->get_records('question_calculated', array('question' => $question->id), 'answer ASC')) {
            $oldoptions = array();
        }

        // Save the units.
        $virtualqtype = $this->get_virtual_qtype( $question);
       // $result = $virtualqtype->save_numerical_units($question);
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
            if (  trim($dataanswer) != '' ) {
                $answer = new stdClass;
                $answer->question = $question->id;
                $answer->answer = trim($dataanswer);
                $answer->fraction = $question->fraction[$key];
                $answer->feedback = trim($question->feedback[$key]);

                if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer->id = $oldanswer->id;
                    $DB->update_record("question_answers", $answer);
                } else { // This is a completely new answer
                    $answer->id = $DB->insert_record("question_answers", $answer);
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
      //  $result = $QTYPES['numerical']->save_numerical_options($question);
      //  if (isset($result->error)) {
      //      return $result;
      //  }


        if( isset($question->import_process)&&$question->import_process){
            $this->import_datasets($question);
         }
        // Report any problems.
        if (!empty($result->notice)) {
            return $result;
        }
        return true;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        // Find out how many datasets are available
        global $CFG, $DB, $QTYPES;
        if(!$maxnumber = (int)$DB->get_field_sql(
                            "SELECT MIN(a.itemcount)
                            FROM {question_dataset_definitions} a,
                                 {question_datasets} b
                            WHERE b.question = ?
                            AND   a.id = b.datasetdefinition", array($question->id))) {
            print_error('cannotgetdsforquestion', 'question', '', $question->id);
        }
                    $sql = "SELECT i.*
                    FROM {question_datasets} d,
                         {question_dataset_definitions} i
                    WHERE d.question = ?
                    AND   d.datasetdefinition = i.id  
                    AND   i.category != 0 
                   ";
        if (!$question->options->synchronize || !$records = $DB->get_records_sql($sql, array($question->id))) {
            $synchronize_calculated  =  false ; 
        }else {
            $synchronize_calculated  = true ; 
        }    

        // Choose a random dataset
        if ( $synchronize_calculated === false ) {
            $state->options->datasetitem = rand(1, $maxnumber);
        }else{
            $state->options->datasetitem = intval( $maxnumber * substr($attempt->timestart,-2) /100 ) ;            
            if ($state->options->datasetitem < 1) {
                $state->options->datasetitem =1 ;
            } else if ($state->options->datasetitem > $maxnumber){
                $state->options->datasetitem = $maxnumber ;
            }
           
        };  
        $state->options->dataset =
         $this->pick_question_dataset($question,$state->options->datasetitem);
                    // create an array of answerids ??? why so complicated ???
            $answerids = array_values(array_map(create_function('$val',
             'return $val->id;'), $question->options->answers));
            // Shuffle the answers if required
            if (!empty($cmoptions->shuffleanswers) and !empty($question->options->shuffleanswers)) {
               $answerids = swapshuffle($answerids);
            }
            $state->options->order = $answerids;
            // Create empty responses
            if ($question->options->single) {
                $state->responses = array('' => '');
            } else {
                $state->responses = array();
            }
            return true;
        
    }
    
    function save_session_and_responses(&$question, &$state) {
        global $DB;
        $responses = 'dataset'.$state->options->datasetitem.'-' ;       
        $responses .= implode(',', $state->options->order) . ':';
        $responses .= implode(',', $state->responses);
         
        // Set the legacy answer field        
        if (!$DB->set_field('question_states', 'answer', $responses, array('id'=> $state->id))) {
            return false;
        }
        return true;
    }

    function create_runtime_question($question, $form) {
        $question = parent::create_runtime_question($question, $form);
        $question->options->answers = array();
        foreach ($form->answers as $key => $answer) {
            $a->answer              = trim($form->answer[$key]);
            $a->fraction              = $form->fraction[$key];//new
           $a->tolerance           = $form->tolerance[$key];
            $a->tolerancetype       = $form->tolerancetype[$key];
            $a->correctanswerlength = $form->correctanswerlength[$key];
            $a->correctanswerformat = $form->correctanswerformat[$key];
            $question->options->answers[] = clone($a);
        }

        return $question;
    }

    function validate_form($form) {
        switch($form->wizardpage) {
            case 'question':
                $calculatedmessages = array();
                if (empty($form->name)) {
                    $calculatedmessages[] = get_string('missingname', 'quiz');
                }
                if (empty($form->questiontext)) {
                    $calculatedmessages[] = get_string('missingquestiontext', 'quiz');
                }
                // Verify formulas
                foreach ($form->answers as $key => $answer) {
                    if ('' === trim($answer)) {
                        $calculatedmessages[] =
                            get_string('missingformula', 'quiz');
                    }
                    if ($formulaerrors =
                     qtype_calculated_find_formula_errors($answer)) {
                        $calculatedmessages[] = $formulaerrors;
                    }
                    if (! isset($form->tolerance[$key])) {
                        $form->tolerance[$key] = 0.0;
                    }
                    if (! is_numeric($form->tolerance[$key])) {
                        $calculatedmessages[] =
                            get_string('tolerancemustbenumeric', 'quiz');
                    }
                }

                if (!empty($calculatedmessages)) {
                    $errorstring = "The following errors were found:<br />";
                    foreach ($calculatedmessages as $msg) {
                        $errorstring .= $msg . '<br />';
                    }
                    print_error($errorstring);
                }

                break;
            default:
                return parent::validate_form($form);
                break;
        }
        return true;
    }
    function finished_edit_wizard(&$form) {
        return isset($form->backtoquiz);
    }
    // This gets called by editquestion.php after the standard question is saved
    function print_next_wizard_page(&$question, &$form, $course) {
        global $CFG, $USER, $SESSION, $COURSE;

        // Catch invalid navigation & reloads
        if (empty($question->id) && empty($SESSION->calculated)) {
            redirect('edit.php?courseid='.$COURSE->id, 'The page you are loading has expired.', 3);
        }

        // See where we're coming from
        switch($form->wizardpage) {
            case 'question':
                require("$CFG->dirroot/question/type/calculated/datasetdefinitions.php");
                break;
            case 'datasetdefinitions':
            case 'datasetitems':
                require("$CFG->dirroot/question/type/calculated/datasetitems.php");
                break;
            default:
                print_error('invalidwizardpage', 'question');
                break;
        }
    }

    // This gets called by question2.php after the standard question is saved
    function &next_wizard_form($submiturl, $question, $wizardnow){
        global $CFG, $SESSION, $COURSE;

        // Catch invalid navigation & reloads
        if (empty($question->id) && empty($SESSION->calculated)) {
            redirect('edit.php?courseid='.$COURSE->id, 'The page you are loading has expired. Cannot get next wizard form.', 3);
        }
        if (empty($question->id)){
            $question =& $SESSION->calculated->questionform;
        }

        // See where we're coming from
        switch($wizardnow) {
            case 'datasetdefinitions':
                require("$CFG->dirroot/question/type/calculated/datasetdefinitions_form.php");
                $mform =& new question_dataset_dependent_definitions_form("$submiturl?wizardnow=datasetdefinitions", $question);
                break;
            case 'datasetitems':
                require("$CFG->dirroot/question/type/calculated/datasetitems_form.php");
                $regenerate = optional_param('forceregeneration', 0, PARAM_BOOL);
                $mform =& new question_dataset_dependent_items_form("$submiturl?wizardnow=datasetitems", $question, $regenerate);
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
    function display_question_editing_page(&$mform, $question, $wizardnow){
        global $OUTPUT ;
        switch ($wizardnow){
            case '':
                //on first page default display is fine
                parent::display_question_editing_page($mform, $question, $wizardnow);
                return;
                break;
            case 'datasetdefinitions':
                 echo $OUTPUT->heading_with_help(get_string("choosedatasetproperties", "quiz"), 'questiondatasets', 'quiz');

             /*   $heading = get_string("question", "quiz").": ".$question->name;
                $helpicon = new moodle_help_icon();
                $helpicon->text = get_string("choosedatasetproperties", "quiz");
                $helpicon->page = 'questiondatasets';
                $helpicon->module = 'quiz';
                echo $OUTPUT->heading($heading);
                echo $OUTPUT->heading_with_help($helpicon);*/
                break;
            case 'datasetitems':
               echo $OUTPUT->heading_with_help(get_string("editdatasets", "quiz"), 'questiondatasets', 'quiz');

         /*       $heading = get_string("question", "quiz").": ".$question->name;
                $helpicon = new moodle_help_icon();
                $helpicon->text = get_string("editdatasets", "quiz");
                $helpicon->page = 'questiondatasets';
                $helpicon->module = 'quiz';
                echo $OUTPUT->heading($heading);
                echo $OUTPUT->heading_with_help($helpicon);*/
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
    function convert_answers (&$question, &$state){
            foreach ($question->options->answers as $key => $answer) {
                $answer->answer = $this->substitute_variables($answer->answer, $state->options->dataset);
                //evaluate the equations i.e {=5+4)
                $qtext = "";
                $qtextremaining = $answer->answer ;
             //   while  (preg_match('~\{(=)|%[[:digit]]\.=([^[:space:]}]*)}~', $qtextremaining, $regs1)) {
                while  (preg_match('~\{=([^[:space:]}]*)}~', $qtextremaining, $regs1)) {

                    $qtextsplits = explode($regs1[0], $qtextremaining, 2);
                    $qtext =$qtext.$qtextsplits[0];
                    $qtextremaining = $qtextsplits[1];
                    if (empty($regs1[1])) {
                            $str = '';
                        } else {
                            if( $formulaerrors = qtype_calculated_find_formula_errors($regs1[1])){
                                $str=$formulaerrors ;
                            }else {
                                eval('$str = '.$regs1[1].';');
                       $texteval= qtype_calculated_calculate_answer(
                     $str, $state->options->dataset, $answer->tolerance,
                     $answer->tolerancetype, $answer->correctanswerlength,
                        $answer->correctanswerformat, '');
                        $str = $texteval->answer;
                            }
                        }
                        $qtext = $qtext.$regs1[0].$str ;
                }
                $answer->answer = $qtext.$qtextremaining ; ;
            }
        }

    function get_default_numerical_unit($question,$virtualqtype){
                $unit = '';
            return $unit ;        
    }    
    function grade_responses(&$question, &$state, $cmoptions) {
        // Forward the grading to the virtual qtype
        // We modify the question to look like a multichoice question
        // for grading nothing to do 
/*        $numericalquestion = fullclone($question);
       foreach ($numericalquestion->options->answers as $key => $answer) {
            $answer = $numericalquestion->options->answers[$key]->answer; // for PHP 4.x
          $numericalquestion->options->answers[$key]->answer = $this->substitute_variables_and_eval($answer,
             $state->options->dataset);
       }*/
         $virtualqtype = $this->get_virtual_qtype( $question);
        return $virtualqtype->grade_responses($question, $state, $cmoptions) ;
    }

    function response_summary($question, $state, $length=80, $formatting=true) {
        // The actual response is the bit after the hyphen
        return substr($state->answer, strpos($state->answer, '-')+1, $length);
    }

    // ULPGC ecastro
    function check_response(&$question, &$state) {
        // Forward the checking to the virtual qtype
        // We modify the question to look like a numerical question
        $numericalquestion = clone($question);
        $numericalquestion->options = clone($question->options);
        foreach ($question->options->answers as $key => $answer) {
            $numericalquestion->options->answers[$key] = clone($answer);
        }
        foreach ($numericalquestion->options->answers as $key => $answer) {
            $answer = &$numericalquestion->options->answers[$key]; // for PHP 4.x
            $answer->answer = $this->substitute_variables_and_eval($answer->answer,
             $state->options->dataset);
        }
        $virtualqtype = $this->get_virtual_qtype( $question);
        return $virtualqtype->check_response($numericalquestion, $state) ;
    }

    // ULPGC ecastro
    function get_actual_response(&$question, &$state) {
        // Substitute variables in questiontext before giving the data to the
        // virtual type
        $virtualqtype = $this->get_virtual_qtype( $question);
        $unit = '' ;//$virtualqtype->get_default_numerical_unit($question);

        // We modify the question to look like a multichoice question
        $numericalquestion = clone($question);
        $this->convert_answers ($numericalquestion, $state);
        $this->convert_questiontext ($numericalquestion, $state);
     /*   $numericalquestion->questiontext = $this->substitute_variables_and_eval(
                                  $numericalquestion->questiontext, $state->options->dataset);*/
        $responses = $virtualqtype->get_all_responses($numericalquestion, $state);
        $response = reset($responses->responses);
        $correct = $response->answer.' : ';

        $responses = $virtualqtype->get_actual_response($numericalquestion, $state);

        foreach ($responses as $key=>$response){
            $responses[$key] = $correct.$response;
        }

        return $responses;
    }

    function create_virtual_qtype() {
        global $CFG;
            require_once("$CFG->dirroot/question/type/multichoice/questiontype.php");
            return new question_multichoice_qtype();
    }


    function comment_header($question) {
        //$this->get_question_options($question);
        $strheader = '';
        $delimiter = '';

        $answers = $question->options->answers;

        foreach ($answers as $key => $answer) {
            if (is_string($answer)) {
                $strheader .= $delimiter.$answer;
            } else {
                $strheader .= $delimiter.$answer->answer;
            }
                $delimiter = '<br/>';            
        }
        return $strheader;
    }

    function comment_on_datasetitems($qtypeobj,$questionid,$questiontext, $answers,$data, $number) { //multichoice_
        global $DB;
        $comment = new stdClass;
        $comment->stranswers = array();
        $comment->outsidelimit = false ;
        $comment->answers = array();
        /// Find a default unit:
    /*    if (!empty($questionid) && $unit = $DB->get_record('question_numerical_units', array('question'=> $questionid, 'multiplier' => 1.0))) {
            $unit = $unit->unit;
        } else {
            $unit = '';
        }*/

        $answers = fullclone($answers);
        $strmin = get_string('min', 'quiz');
        $strmax = get_string('max', 'quiz');
        $errors = '';
        $delimiter = ': ';
        foreach ($answers as $key => $answer) {
                $answer->answer = $this->substitute_variables($answer->answer, $data);
                //evaluate the equations i.e {=5+4)
                $qtext = "";
                $qtextremaining = $answer->answer ;
                while  (preg_match('~\{=([^[:space:]}]*)}~', $qtextremaining, $regs1)) {
                    $qtextsplits = explode($regs1[0], $qtextremaining, 2);
                    $qtext =$qtext.$qtextsplits[0];
                    $qtextremaining = $qtextsplits[1];
                    if (empty($regs1[1])) {
                            $str = '';
                        } else {
                            if( $formulaerrors = qtype_calculated_find_formula_errors($regs1[1])){
                                $str=$formulaerrors ;
                            }else {
                                eval('$str = '.$regs1[1].';');
                            }
                        }
                        $qtext = $qtext.$str ;
                }
                $answer->answer = $qtext.$qtextremaining ; ;
                $comment->stranswers[$key]= $answer->answer ;
            
            
          /*  $formula = $this->substitute_variables($answer->answer,$data);
            $formattedanswer = qtype_calculated_calculate_answer(
                    $answer->answer, $data, $answer->tolerance,
                    $answer->tolerancetype, $answer->correctanswerlength,
                    $answer->correctanswerformat, $unit);
                    if ( $formula === '*'){
                        $answer->min = ' ';
                        $formattedanswer->answer = $answer->answer ;
                    }else {
                        eval('$answer->answer = '.$formula.';') ;
                        $virtualqtype->get_tolerance_interval($answer);
                    } 
            if ($answer->min === '') {
                // This should mean that something is wrong
                $comment->stranswers[$key] = " $formattedanswer->answer".'<br/><br/>';
            } else if ($formula === '*'){
                $comment->stranswers[$key] = $formula.' = '.get_string('anyvalue','qtype_calculated').'<br/><br/><br/>';
            }else{
                $comment->stranswers[$key]= $formula.' = '.$formattedanswer->answer.'<br/>' ;
                $comment->stranswers[$key] .= $strmin. $delimiter.$answer->min.'---';
                $comment->stranswers[$key] .= $strmax.$delimiter.$answer->max;
                $comment->stranswers[$key] .='<br/>';
                $correcttrue->correct = $formattedanswer->answer ;
                $correcttrue->true = $answer->answer ;
                if ($formattedanswer->answer < $answer->min || $formattedanswer->answer > $answer->max){
                    $comment->outsidelimit = true ;
                    $comment->answers[$key] = $key;
                    $comment->stranswers[$key] .=get_string('trueansweroutsidelimits','qtype_calculated',$correcttrue);//<span class="error">ERROR True answer '..' outside limits</span>';
                }else {
                    $comment->stranswers[$key] .=get_string('trueanswerinsidelimits','qtype_calculated',$correcttrue);//' True answer :'.$calculated->trueanswer.' inside limits';
                }
                $comment->stranswers[$key] .='';
            }*/
        }
        return fullclone($comment);
    }





    function get_correct_responses1(&$question, &$state) {
        $virtualqtype = $this->get_virtual_qtype( $question);
    /*    if ($question->options->multichoice != 1 ) {
            if($unit = $virtualqtype->get_default_numerical_unit($question)){
                 $unit = $unit->unit;
            } else {
                $unit = '';
            }
            foreach ($question->options->answers as $answer) {
                if (((int) $answer->fraction) === 1) {
                    $answernumerical = qtype_calculated_calculate_answer(
                     $answer->answer, $state->options->dataset, $answer->tolerance,
                     $answer->tolerancetype, $answer->correctanswerlength,
                        $answer->correctanswerformat, ''); // remove unit
                        $correct = array('' => $answernumerical->answer);
                        $correct['answer']= $correct[''];
                    if (isset($correct['']) && $correct[''] != '*' && $unit ) {
                            $correct[''] .= ' '.$unit;
                            $correct['unit']= $unit;
                    }
                    return $correct;
                }
            }
        }else{**/
            return $virtualqtype->get_correct_responses($question, $state) ;
       // }
        return null;
    }

    function substitute_variables($str, $dataset) {
          //  testing for wrong numerical values
          // all calculations used this function so testing here should be OK 

        foreach ($dataset as $name => $value) {
            $val = $value ;
            if(! is_numeric($val)){
                $a = new stdClass;
                $a->name = '{'.$name.'}' ;
                $a->value = $value ;
                    echo $OUTPUT->notification(get_string('notvalidnumber','qtype_calculated',$a));
                    $val = 1.0 ;
            }                   
            if($val < 0 ){
                $str = str_replace('{'.$name.'}', '('.$val.')', $str);
            } else {
                $str = str_replace('{'.$name.'}', $val, $str);
            }
        }
        return $str;
    }
    function evaluate_equations($str, $dataset){
        $formula = $this->substitute_variables($str, $dataset) ;
       if ($error = qtype_calculated_find_formula_errors($formula)) {
            return $error;
        }
        return $str;
    }
         

    function substitute_variables_and_eval($str, $dataset) {
        $formula = $this->substitute_variables($str, $dataset) ;
       if ($error = qtype_calculated_find_formula_errors($formula)) {
            return $error;
        }
        /// Calculate the correct answer
        if (empty($formula)) {
            $str = '';
        } else if ($formula === '*'){
            $str = '*';
        } else {
            eval('$str = '.$formula.';');
        }
        return $str;
    }

    function get_dataset_definitions($questionid, $newdatasets) {
        global $DB;
        //get the existing datasets for this question
        $datasetdefs = array();
        if (!empty($questionid)) {
            global $CFG;
            $sql = "SELECT i.*
                    FROM {question_datasets} d,
                         {question_dataset_definitions} i
                    WHERE d.question = ?
                    AND   d.datasetdefinition = i.id
                   ";
            if ($records = $DB->get_records_sql($sql, array($questionid))) {
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
        global $DB;
        // save synchronize
        
        // Save datasets
        $datasetdefinitions = $this->get_dataset_definitions($form->id, $form->dataset);
        $tmpdatasets = array_flip($form->dataset);
        $defids = array_keys($datasetdefinitions);
        foreach ($defids as $defid) {
            $datasetdef = &$datasetdefinitions[$defid];
            if (isset($datasetdef->id)) {
                if (!isset($tmpdatasets[$defid])) {
                // This dataset is not used any more, delete it
                    $DB->delete_records('question_datasets', array('question' => $form->id, 'datasetdefinition' => $datasetdef->id));
                    if ($datasetdef->category == 0) { // Question local dataset
                        $DB->delete_records('question_dataset_definitions', array('id' => $datasetdef->id));
                        $DB->delete_records('question_dataset_items', array('definition' => $datasetdef->id));
                    }
                }
                // This has already been saved or just got deleted
                unset($datasetdefinitions[$defid]);
                continue;
            }

            $datasetdef->id = $DB->insert_record('question_dataset_definitions', $datasetdef);

            if (0 != $datasetdef->category) {
                // We need to look for already existing
                // datasets in the category.
                // By first creating the datasetdefinition above we
                // can manage to automatically take care of
                // some possible realtime concurrence
                if ($olderdatasetdefs = $DB->get_records_select( 'question_dataset_definitions',
                        "type = ?
                        AND name = ?
                        AND category = ?
                        AND id < ?
                        ORDER BY id DESC", array($datasetdef->type, $datasetdef->name, $datasetdef->category, $datasetdef->id))) {

                    while ($olderdatasetdef = array_shift($olderdatasetdefs)) {
                        $DB->delete_records('question_dataset_definitions', array('id' => $datasetdef->id));
                        $datasetdef = $olderdatasetdef;
                    }
                }
            }

            // Create relation to this dataset:
            $questiondataset = new stdClass;
            $questiondataset->question = $form->id;
            $questiondataset->datasetdefinition = $datasetdef->id;
            $DB->insert_record('question_datasets', $questiondataset);
            unset($datasetdefinitions[$defid]);
        }

        // Remove local obsolete datasets as well as relations
        // to datasets in other categories:
        if (!empty($datasetdefinitions)) {
            foreach ($datasetdefinitions as $def) {
                $DB->delete_records('question_datasets', array('question' => $form->id, 'datasetdefinition' => $def->id));

                if ($def->category == 0) { // Question local dataset
                    $DB->delete_records('question_dataset_definitions', array('id' => $def->id));
                    $DB->delete_records('question_dataset_items', array('definition' => $def->id));
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
    global $CFG, $DB;
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
                   $datasetdef->id = $DB->insert_record('question_dataset_definitions', $datasetdef);
                   //copy the dataitems
                   $olditems = $this->get_database_dataset_items($olddatasetid);
                   if (count($olditems) > 0 ) {
                        $itemcount = 0;
                        foreach($olditems as $item ){
                            $item->definition = $datasetdef->id;
                            $DB->insert_record('question_dataset_items', $item);
                            $itemcount++;
                        }
                        //update item count
                        $datasetdef->itemcount =$itemcount;
                        $DB->update_record('question_dataset_definitions', $datasetdef);
                    } // end of  copy the dataitems
                }// end of  copy the datasetdef
                // Create relation to the new question with this
                // copy as new datasetdef from the initial question
                $questiondataset = new stdClass;
                $questiondataset->question = $form->id;
                $questiondataset->datasetdefinition = $datasetdef->id;
                $DB->insert_record('question_datasets', $questiondataset);
                unset($datasetdefinitions[$defid]);
                continue;
            }// end of datasetdefs from the initial question
            // really new one code similar to save_dataset_definitions()
            $datasetdef->id = $DB->insert_record('question_dataset_definitions', $datasetdef);

            if (0 != $datasetdef->category) {
                // We need to look for already existing
                // datasets in the category.
                // By first creating the datasetdefinition above we
                // can manage to automatically take care of
                // some possible realtime concurrence
                if ($olderdatasetdefs = $DB->get_records_select(
                        'question_dataset_definitions',
                        "type = ?
                        AND name = ?
                        AND category = ?
                        AND id < ?
                        ORDER BY id DESC", array($datasetdef->type, $datasetdef->name, $datasetdef->category, $datasetdef->id))) {

                    while ($olderdatasetdef = array_shift($olderdatasetdefs)) {
                        $DB->delete_records('question_dataset_definitions', array('id' => $datasetdef->id));
                        $datasetdef = $olderdatasetdef;
                    }
                }
            }

            // Create relation to this dataset:
            $questiondataset = new stdClass;
            $questiondataset->question = $form->id;
            $questiondataset->datasetdefinition = $datasetdef->id;
            $DB->insert_record('question_datasets', $questiondataset);
            unset($datasetdefinitions[$defid]);
        }

        // Remove local obsolete datasets as well as relations
        // to datasets in other categories:
        if (!empty($datasetdefinitions)) {
            foreach ($datasetdefinitions as $def) {
                $DB->delete_records('question_datasets', array('question' => $form->id, 'datasetdefinition' => $def->id));

                if ($def->category == 0) { // Question local dataset
                    $DB->delete_records('question_dataset_definitions', array('id' => $def->id));
                    $DB->delete_records('question_dataset_items', array('definition' => $def->id));
                }
            }
        }
    }

/// Dataset functionality
    function pick_question_dataset($question, $datasetitem) {
        // Select a dataset in the following format:
        // An array indexed by the variable names (d.name) pointing to the value
        // to be substituted
        global $CFG, $DB;
        if (!$dataitems = $DB->get_records_sql(
                        "SELECT i.id, d.name, i.value
                        FROM {question_dataset_definitions} d,
                             {question_dataset_items} i,
                             {question_datasets} q
                        WHERE q.question = ?
                        AND q.datasetdefinition = d.id
                        AND d.id = i.definition
                        AND i.itemnumber = ? ORDER by i.id DESC ", array($question->id, $datasetitem))) {
            print_error('cannotgetdsfordependent', 'question', '', array($question->id, $datasetitem));
        }
        $dataset = Array();
       	foreach($dataitems as $id => $dataitem  ){
	       	if (!isset($dataset[$dataitem->name])){
	       		    $dataset[$dataitem->name] = $dataitem->value ;
	       		  }else {
	       		  	// deleting the unused records could be added here
	       		  }
       	}
        return $dataset;
    }
    
    function dataset_options_from_database($form, $name,$prefix='',$langfile='quiz') {

        // First options - it is not a dataset...
        $options['0'] = get_string($prefix.'nodataset', $langfile);

        // Construct question local options
        global $CFG, $DB;
        $type = 1 ; // only type = 1 (i.e. old 'LITERAL') has ever been used 
        if ( ! $currentdatasetdef = $DB->get_record_sql(
                "SELECT a.*
                   FROM {question_dataset_definitions} a,
                        {question_datasets} b
                  WHERE a.id = b.datasetdefinition
                    AND a.type = '1'
                    AND b.question = ?
                    AND a.name = ?", array($form->id, $name))){
            $currentdatasetdef->type = '0';
         };
        $key = "$type-0-$name";
        if ($currentdatasetdef->type == $type
                and $currentdatasetdef->category == 0) {
            $options[$key] = get_string($prefix."keptlocal$type", $langfile);
        } else {
            $options[$key] = get_string($prefix."newlocal$type", $langfile);
        }
        // Construct question category options
        $categorydatasetdefs = $DB->get_records_sql(
                "SELECT b.question, a.* 
                   FROM {question_datasets} b,
                        {question_dataset_definitions} a                       
                  WHERE a.id = b.datasetdefinition
                    AND a.type = '1'
                    AND a.category = ?
                    AND a.name = ?", array($form->category, $name));
        $type = 1 ;
        $key = "$type-$form->category-$name";
        if (!empty($categorydatasetdefs)){ // there is at least one with the same name
            if (isset($categorydatasetdefs[$form->id])) {// it is already used by this question
                    $options[$key] = get_string($prefix."keptcategory$type", $langfile);
                } else {
                    $options[$key] = get_string($prefix."existingcategory$type", $langfile);
                }
        } else {
            $options[$key] = get_string($prefix."newcategory$type", $langfile);
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
    function get_dataset_definitions_category($form) {
        global $CFG, $DB;
        $datasetdefs = array();
        $lnamemax = 30;
        if (!empty($form->category)) {
            $sql = "SELECT i.*,d.*
                    FROM {question_datasets} d,
                         {question_dataset_definitions} i
                  WHERE i.id = d.datasetdefinition
                    AND i.category = ?
                    ;
                   ";
             if ($records = $DB->get_records_sql($sql, array($form->category))) {
                   foreach ($records as $r) {
                       if ( !isset ($datasetdefs["$r->name"])) $datasetdefs["$r->name"] = $r->itemcount;
                    }
                }
        }
        return  $datasetdefs ;
    }

    /**
    * This function build a table showing the available category shareable
    * wild cards, their name, their definition (Min, Max, Decimal) , the item count
    * and the name of the question where they are used.
    * This table is intended to be add before the question text to help the user use
    * these wild cards
    */

    function print_dataset_definitions_category($form) {
        global $CFG, $DB;
        $datasetdefs = array();
        $lnamemax = 22;
        $namestr =get_string('name', 'quiz');
        $minstr=get_string('min', 'quiz');
        $maxstr=get_string('max', 'quiz');
        $rangeofvaluestr=get_string('minmax','qtype_datasetdependent');
        $questionusingstr = get_string('usedinquestion','qtype_calculated');
        $itemscountstr = get_string('itemscount','qtype_datasetdependent');
       $text ='';
        if (!empty($form->category)) {
            list($category) = explode(',', $form->category);
            $sql = "SELECT i.*,d.*
                    FROM {question_datasets} d,
                         {question_dataset_definitions} i
                    WHERE i.id = d.datasetdefinition
                    AND i.category = ?;
                    " ;
            if ($records = $DB->get_records_sql($sql, array($category))) {
                foreach ($records as $r) {
                    $sql1 = "SELECT q.*
                        FROM  {question} q
                             WHERE q.id = ?
                    ";
                    if ( !isset ($datasetdefs["$r->type-$r->category-$r->name"])){
                        $datasetdefs["$r->type-$r->category-$r->name"]= $r;
                    }
                    if ($questionb = $DB->get_records_sql($sql1, array($r->question))) {
                        $datasetdefs["$r->type-$r->category-$r->name"]->questions[$r->question]->name =$questionb[$r->question]->name ;
                    }
                }
            }
        }
        if (!empty ($datasetdefs)){

            $text ="<table width=\"100%\" border=\"1\"><tr><th  style=\"white-space:nowrap;\" class=\"header\" scope=\"col\" >$namestr</th><th   style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$rangeofvaluestr</th><th  style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$itemscountstr</th><th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$questionusingstr</th></tr>";
            foreach ($datasetdefs as $datasetdef){
                list($distribution, $min, $max,$dec) = explode(':', $datasetdef->options, 4);
                $text .="<tr><td valign=\"top\" align=\"center\"> $datasetdef->name </td><td align=\"center\" valign=\"top\"> $min <strong>-</strong> $max </td><td align=\"right\" valign=\"top\">$datasetdef->itemcount&nbsp;&nbsp;</td><td align=\"left\">";
                foreach ($datasetdef->questions as $qu) {
                    //limit the name length displayed
                    if (!empty($qu->name)) {
                        $qu->name = (strlen($qu->name) > $lnamemax) ?
                        substr($qu->name, 0, $lnamemax).'...' : $qu->name;
                    } else {
                        $qu->name = '';
                    }
                    $text .=" &nbsp;&nbsp; $qu->name <br/>";
                }
                $text .="</td></tr>";
            }
            $text .="</table>";
        }else{
             $text .=get_string('nosharedwildcard', 'qtype_calculated');
        }
        return  $text ;
    }
    function get_virtual_qtype() {
        global $QTYPES;
    //    if ( isset($question->options->multichoice) && $question->options->multichoice == '1'){
            $this->virtualqtype =& $QTYPES['multichoice'];
     //   }else {
     //       $this->virtualqtype =& $QTYPES['numerical'];
     //   }
        return $this->virtualqtype;
    }


/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {
        global $DB;
        $status = true;

        $calculateds = $DB->get_records("question_calculated",array("question" =>$question,"id"));
        //If there are calculated-s
        if ($calculateds) {
            //Iterate over each calculateds
            foreach ($calculateds as $calculated) {
                $status = $status &&fwrite ($bf,start_tag("CALCULATED",$level,true));
                //Print calculated contents
                fwrite ($bf,full_tag("ANSWER",$level+1,false,$calculated->answer));
                fwrite ($bf,full_tag("TOLERANCE",$level+1,false,$calculated->tolerance));
                fwrite ($bf,full_tag("TOLERANCETYPE",$level+1,false,$calculated->tolerancetype));
                fwrite ($bf,full_tag("CORRECTANSWERLENGTH",$level+1,false,$calculated->correctanswerlength));
                fwrite ($bf,full_tag("CORRECTANSWERFORMAT",$level+1,false,$calculated->correctanswerformat));
                //Now backup numerical_units
                $status = question_backup_numerical_units($bf,$preferences,$question,7);
                //Now backup required dataset definitions and items...
                $status = question_backup_datasets($bf,$preferences,$question,7);
                //End calculated data
                $status = $status &&fwrite ($bf,end_tag("CALCULATED",$level,true));
            }
            $calculated_options = $DB->get_records("question_calculated_options",array("questionid" => $question),"id");
            if ($calculated_options) {
                //Iterate over each calculated_option
                foreach ($calculated_options as $calculated_option) {
                    $status = fwrite ($bf,start_tag("CALCULATED_OPTIONS",$level,true));
                    //Print calculated_option contents
                    fwrite ($bf,full_tag("SYNCHRONIZE",$level+1,false,$calculated_option->synchronize));
                    fwrite ($bf,full_tag("MULTIPLECHOICE",$level+1,false,$calculated_option->multiplechoice));
                    fwrite ($bf,full_tag("SINGLE",$level+1,false,$calculated_option->single));
                    fwrite ($bf,full_tag("SHUFFLEANSWERS",$level+1,false,$calculated_option->shuffleanswers));
                    fwrite ($bf,full_tag("CORRECTFEEDBACK",$level+1,false,$calculated_option->correctfeedback));
                    fwrite ($bf,full_tag("PARTIALLYCORRECTFEEDBACK",$level+1,false,$calculated_option->partiallycorrectfeedback));
                    fwrite ($bf,full_tag("INCORRECTFEEDBACK",$level+1,false,$calculated_option->incorrectfeedback));
                    fwrite ($bf,full_tag("ANSWERNUMBERING",$level+1,false,$calculated_option->answernumbering));
                    $status = fwrite ($bf,end_tag("CALCULATED_OPTIONS",$level,true));
                }
            }
            $status = question_backup_numerical_options($bf,$preferences,$question,$level);

        }
        return $status;
    }

/// RESTORE FUNCTIONS /////////////////

    /*
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    function restore($old_question_id,$new_question_id,$info,$restore) {
        global $DB;

        $status = true;

        //Get the calculated-s array
        $calculateds = $info['#']['CALCULATED'];

        //Iterate over calculateds
        for($i = 0; $i < sizeof($calculateds); $i++) {
            $cal_info = $calculateds[$i];
            //traverse_xmlize($cal_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_calculated record structure
            $calculated->question = $new_question_id;
            $calculated->answer = backup_todb($cal_info['#']['ANSWER']['0']['#']);
            $calculated->tolerance = backup_todb($cal_info['#']['TOLERANCE']['0']['#']);
            $calculated->tolerancetype = backup_todb($cal_info['#']['TOLERANCETYPE']['0']['#']);
            $calculated->correctanswerlength = backup_todb($cal_info['#']['CORRECTANSWERLENGTH']['0']['#']);
            $calculated->correctanswerformat = backup_todb($cal_info['#']['CORRECTANSWERFORMAT']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$calculated->answer);
            if ($answer) {
                $calculated->answer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_calculated
            $newid = $DB->insert_record ("question_calculated",$calculated);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        //Get the calculated_options array
        // need to check as old questions don't have calculated_options record
        if(isset($info['#']['CALCULATED_OPTIONS'])){
            $calculatedoptions = $info['#']['CALCULATED_OPTIONS'];
    
            //Iterate over calculated_options
            for($i = 0; $i < sizeof($calculatedoptions); $i++){
                $cal_info = $calculatedoptions[$i];
                //traverse_xmlize($cal_info);                                                                 //Debug
                //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                //$GLOBALS['traverse_array']="";                                                              //Debug
    
                //Now, build the question_calculated_options record structure
                $calculated_options->questionid = $new_question_id;
                $calculated_options->synchronize = backup_todb($cal_info['#']['SYNCHRONIZE']['0']['#']);
             //   $calculated_options->multichoice = backup_todb($cal_info['#']['MULTICHOICE']['0']['#']);
                $calculated_options->single = backup_todb($cal_info['#']['SINGLE']['0']['#']);
                $calculated_options->shuffleanswers = isset($cal_info['#']['SHUFFLEANSWERS']['0']['#'])?backup_todb($mul_info['#']['SHUFFLEANSWERS']['0']['#']):'';
                $calculated_options->correctfeedback = backup_todb($cal_info['#']['CORRECTFEEDBACK']['0']['#']);
                $calculated_options->partiallycorrectfeedback = backup_todb($cal_info['#']['PARTIALLYCORRECTFEEDBACK']['0']['#']);
                $calculated_options->incorrectfeedback = backup_todb($cal_info['#']['INCORRECTFEEDBACK']['0']['#']);
                $calculated_options->answernumbering = backup_todb($cal_info['#']['ANSWERNUMBERING']['0']['#']);
    
                //The structure is equal to the db, so insert the question_calculated_options
                $newid = $DB->insert_record ("question_calculated_options",$calculated_options);
    
                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
            }
            //Now restore numerical_units
            $status = question_restore_numerical_units ($old_question_id,$new_question_id,$cal_info,$restore);
            $status = question_restore_numerical_options($old_question_id,$new_question_id,$info,$restore);
            //Now restore dataset_definitions
            if ($status && $newid) {
                $status = question_restore_dataset_definitions ($old_question_id,$new_question_id,$cal_info,$restore);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
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
      $form->qtype ='calculatedmulti';
      $question->qtype ='calculatedmulti';
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

      $new_question = $this->save_question($question, $form, $course);

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
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_calculatedmulti_qtype());

function qtype_calculatedmulti_calculate_answer($formula, $individualdata,
        $tolerance, $tolerancetype, $answerlength, $answerformat='1', $unit='') {
/// The return value has these properties:
/// ->answer    the correct answer
/// ->min       the lower bound for an acceptable response
/// ->max       the upper bound for an accetpable response

    /// Exchange formula variables with the correct values...
    global $QTYPES;
    $answer = $QTYPES['calculated']->substitute_variables_and_eval($formula, $individualdata);
    if ('1' == $answerformat) { /* Answer is to have $answerlength decimals */
        /*** Adjust to the correct number of decimals ***/
        if (stripos($answer,'e')>0 ){
            $answerlengthadd = strlen($answer)-stripos($answer,'e');
        }else {
            $answerlengthadd = 0 ;
        }
        $calculated->answer = round(floatval($answer), $answerlength+$answerlengthadd);

        if ($answerlength) {
            /* Try to include missing zeros at the end */

            if (preg_match('~^(.*\\.)(.*)$~', $calculated->answer, $regs)) {
                $calculated->answer = $regs[1] . substr(
                        $regs[2] . '00000000000000000000000000000000000000000x',
                        0, $answerlength)
                        . $unit;
            } else {
                $calculated->answer .=
                        substr('.00000000000000000000000000000000000000000x',
                        0, $answerlength + 1) . $unit;
            }
        } else {
            /* Attach unit */
            $calculated->answer .= $unit;
        }

    } else if ($answer) { // Significant figures does only apply if the result is non-zero

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
        // ... and have the answer rounded of to the correct length
        $answer = round($answer, $answerlength);

        // Have the answer written on a suitable format,
        // Either scientific or plain numeric
        if (-2 > $p10 || 4 < $p10) {
            // Use scientific format:
            $eX = 'e'.--$p10;
            $answer *= 10;
            if (1 == $answerlength) {
                $calculated->answer = $sign.$answer.$eX.$unit;
            } else {
                // Attach additional zeros at the end of $answer,
                $answer .= (1==strlen($answer) ? '.' : '')
                        . '00000000000000000000000000000000000000000x';
                $calculated->answer = $sign
                        .substr($answer, 0, $answerlength +1).$eX.$unit;
            }
        } else {
            // Stick to plain numeric format
            $answer *= "1e$p10";
            if (0.1 <= $answer / "1e$answerlength") {
                $calculated->answer = $sign.$answer.$unit;
            } else {
                // Could be an idea to add some zeros here
                $answer .= (preg_match('~^[0-9]*$~', $answer) ? '.' : '')
                        . '00000000000000000000000000000000000000000x';
                $oklen = $answerlength + ($p10 < 1 ? 2-$p10 : 1);
                $calculated->answer = $sign.substr($answer, 0, $oklen).$unit;
            }
        }

    } else {
        $calculated->answer = 0.0;
    }

    /// Return the result
    return $calculated;
}


function qtype_calculatedmulti_find_formula_errors($formula) {
/// Validates the formula submitted from the question edit page.
/// Returns false if everything is alright.
/// Otherwise it constructs an error message
    // Strip away dataset names
    while (preg_match('~\\{[[:alpha:]][^>} <{"\']*\\}~', $formula, $regs)) {
        $formula = str_replace($regs[0], '1', $formula);
    }

    // Strip away empty space and lowercase it
    $formula = strtolower(str_replace(' ', '', $formula));

    $safeoperatorchar = '-+/*%>:^\~<?=&|!'; /* */
    $operatorornumber = "[$safeoperatorchar.0-9eE]";

    while ( preg_match("~(^|[$safeoperatorchar,(])([a-z0-9_]*)\\(($operatorornumber+(,$operatorornumber+((,$operatorornumber+)+)?)?)?\\)~",
            $formula, $regs)) {

        switch ($regs[2]) {
            // Simple parenthesis
            case '':
                if ($regs[4] || strlen($regs[3])==0) {
                    return get_string('illegalformulasyntax', 'quiz', $regs[0]);
                }
                break;

            // Zero argument functions
            case 'pi':
                if ($regs[3]) {
                    return get_string('functiontakesnoargs', 'quiz', $regs[2]);
                }
                break;

            // Single argument functions (the most common case)
            case 'abs': case 'acos': case 'acosh': case 'asin': case 'asinh':
            case 'atan': case 'atanh': case 'bindec': case 'ceil': case 'cos':
            case 'cosh': case 'decbin': case 'decoct': case 'deg2rad':
            case 'exp': case 'expm1': case 'floor': case 'is_finite':
            case 'is_infinite': case 'is_nan': case 'log10': case 'log1p':
            case 'octdec': case 'rad2deg': case 'sin': case 'sinh': case 'sqrt':
            case 'tan': case 'tanh':
                if (!empty($regs[4]) || empty($regs[3])) {
                    return get_string('functiontakesonearg','quiz',$regs[2]);
                }
                break;

            // Functions that take one or two arguments
            case 'log': case 'round':
                if (!empty($regs[5]) || empty($regs[3])) {
                    return get_string('functiontakesoneortwoargs','quiz',$regs[2]);
                }
                break;

            // Functions that must have two arguments
            case 'atan2': case 'fmod': case 'pow':
                if (!empty($regs[5]) || empty($regs[4])) {
                    return get_string('functiontakestwoargs', 'quiz', $regs[2]);
                }
                break;

            // Functions that take two or more arguments
            case 'min': case 'max':
                if (empty($regs[4])) {
                    return get_string('functiontakesatleasttwo','quiz',$regs[2]);
                }
                break;

            default:
                return get_string('unsupportedformulafunction','quiz',$regs[2]);
        }

        // Exchange the function call with '1' and then chack for
        // another function call...
        if ($regs[1]) {
            // The function call is proceeded by an operator
            $formula = str_replace($regs[0], $regs[1] . '1', $formula);
        } else {
            // The function call starts the formula
            $formula = preg_replace("~^$regs[2]\\([^)]*\\)~", '1', $formula);
        }
    }

    if (preg_match("~[^$safeoperatorchar.0-9eE]+~", $formula, $regs)) {
        return get_string('illegalformulasyntax', 'quiz', $regs[0]);
    } else {
        // Formula just might be valid
        return false;
    }

}



