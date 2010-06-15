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
        global $CFG, $DB, $QTYPES, $OUTPUT ;
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
           // i.e records is true so test coherence
           $coherence = true ;
                $a = new stdClass ;
                $a->qid = $question->id ;
                $a->qcat = $question->category ;
           foreach($records as $def ){
                if ($def->category != $question->category){
                    $a->name = $def->name;
                    $a->sharedcat = $def->category ;
                    $coherence = false ;
                    break;
                }
            }
            if(!$coherence){
                         echo $OUTPUT->notification(get_string('nocoherencequestionsdatyasetcategory','qtype_calculated',$a));
          } 
            
            $synchronize_calculated  = true ; 
        }    

        // Choose a random dataset
        // maxnumber sould not be breater than 100
        if ($maxnumber > CALCULATEDQUESTIONMAXITEMNUMBER ){
            $maxnumber = CALCULATEDQUESTIONMAXITEMNUMBER ;
        }
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
        $question = default_questiontype::create_runtime_question($question, $form);
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
                        $qtext = $qtext.$str ;
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

    function get_virtual_qtype() {
        global $QTYPES;
    //    if ( isset($question->options->multichoice) && $question->options->multichoice == '1'){
            $this->virtualqtype =& $QTYPES['multichoice'];
     //   }else {
     //       $this->virtualqtype =& $QTYPES['numerical'];
     //   }
        return $this->virtualqtype;
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

if ( ! defined ("CALCULATEDMULTI")) {
    define("CALCULATEDMULTI",    "calculatedmulti");
}
