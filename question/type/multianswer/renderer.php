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
 * Multianswer question renderer classes.
 * Handle shortanswer, numerical and various multichoice subquestions
 *
 * @package qtype_multianswer
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Base class for generating the bits of output common to multianswer
 * (Cloze) questions.
 * This render the main question text and transfer to the subquestions
 * the task of display their input elements and status 
 * feedback, grade, correct answer(s)
 *
 * @copyright © 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 class qtype_multianswer_renderer extends qtype_renderer {

    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();

        $result = '';

        $qtextremaining = $question->format_questiontext();

        $strfeedback = get_string('feedback', 'quiz');

        // The regex will recognize text snippets of type {#X}
        // where the X can be any text not containg } or white-space characters.

        while (ereg('\{#([^[:space:]}]*)}', $qtextremaining, $regs)) {
            $qtextsplits = explode($regs[0], $qtextremaining, 2);
            $result .= $qtextsplits[0];
           // $result .= "<label>"; // MDL-7497
            $qtextremaining = $qtextsplits[1];

            $positionkey = $regs[1];
       // transfer to the specific subquestion renderer                 
            if (isset($question->subquestions[$positionkey]) && $question->subquestions[$positionkey] != ''){
                $subquestion = &$question->subquestions[$positionkey];
                $qout = $subquestion->get_renderer();
                $qa->subquestionindex = $positionkey ;
                $result .= $qout->formulation_and_controls($qa,$options); //
     
             //  $result .= "</label>"; // MDL-7497
               
            } else {
                if(!  isset($question->subquestions[$positionkey])){
                    $result .= $regs[0]; //."</label>";
                }else { //</label>
                    $result .= '<div class="error" >'.get_string('questionnotfound','qtype_multianswer',$positionkey).'</div>';
                }
           }
        }  // end while 

        // Print the final piece of question text:
        $result .= $qtextremaining;

        return $result;
    }


    public function correct_response(question_attempt $qa) {
        return '' ;
    }

}


/**
 * Subclass for generating the bits of output specific to shortanswer
 * subquestions.
 *
 * @copyright © 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 require_once($CFG->dirroot . '/question/type/shortanswer/renderer.php');
 class qtype_multianswer_shortanswer_renderer extends qtype_shortanswer_renderer {
    /**
    * function normally part of core_question_renderer
    * that is copy here 
    */
    
    public function correct_response(question_attempt $qa) {
        $questiontot = $qa->get_question();
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];
        $answer = reset($subquestion->get_answers());
        if (!$answer) {
            return '';
        }
        return get_string('correctansweris', 'qtype_multianswer', s($answer->answer));
    }

    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        $questiontot = $qa->get_question();
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];
        $answername = $subquestion->fieldid.'answer' ;
        $response = $qa->get_last_qt_var($answername);
        $inputname = $qa->get_qt_field_name($answername);
        $size = 1 ;
        foreach ($subquestion->answers as $answer) {
            if (strlen(trim($answer->answer)) > $size ){
                $size = strlen(trim($answer->answer));
            }
            
        }
        if (strlen(trim($response))> $size ){
                $size = strlen(trim($response))+1;
        }
        $size = round($size + rand(0,$size*0.15));
        $size > 60 ? $size = 60 : $size = $size;
        $inputattributes = array(
            'type' => 'text',
            'name' => $inputname,
            'value' => $response,
            'id' => $inputname,
            'size' => $size,
        );
        // readonly cannot by put in input 
        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
            
        }
        $class = '';
        $feedbackimg = '';
                    // Determine feedback popup if any
        $popup = '';
        $feedback = '' ;
        $fraction = 0 ;

        if ($options->feedback) {
            $answer = $subquestion->get_matching_answer(array('answer' => $response));
            if ($answer) {
                $inputattributes['class'] = question_get_feedback_class($answer->fraction);
                $feedbackimg = question_get_feedback_image($answer->fraction);
                $fraction = $answer->fraction ;
                if ($answer->feedback) {
                  //  $feedback .= $subquestion->format_text(htmlspecialchars($answer->feedback, ENT_QUOTES ));
                    $feedback .= $subquestion->format_text($answer->feedback );
                }
            } else {
                $inputattributes['class'] = question_get_feedback_class(0);
                $feedbackimg = question_get_feedback_image(0);
            }
        }
        $readonly ='';
        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
            $readonly = 'readonly="readonly"';
        }
        // determine popup
        // answer feedback (specific)i.e if options->feedback already set
        // subquestion status correctness or Finished validator if correctness
        // Correct response
        // marks
        if ($options->feedback) {
            $strfeedbackwrapped  = 'Response Status';
            $subfraction = '' ;
                if ($options->correctness ) {
                    if ( ! $answer ){
                        $state = $qa->get_state();
                        $state = question_state::$invalid;
                        $strfeedbackwrapped .= ":<font color=red >".$state->default_string()."</font>" ;
                        $feedback =  "<font color=red >".$subquestion->get_validation_error(array('answer' => $response)) ."</font>"; 
                    }else {
                        $state = $qa->get_state();
                        $state = question_state::graded_state_for_fraction($fraction);
                        $strfeedbackwrapped .= ":".$state->default_string();
                    }
                }
           
            if ($options->correctresponse ) {
                $feedback  .= "<br />".$this->correct_response( $qa);//
            }
            if ($options->marks ) {
                $res = $subquestion->grade_response(array('answer'=>$response)); // fraction=>state 
                $subfraction = $res[0];
                $subgrade= $subfraction * $subquestion->defaultmark ;
                $feedback .= "<br />".$questiontot->mark_summary($options, $subquestion->defaultmark , $subgrade );
    
            }
            $feedback = str_replace("'","\'",$feedback);
            $feedback = str_replace('"',"\'",$feedback);
            $strfeedbackwrapped = str_replace("'"," ",$strfeedbackwrapped);
            $strfeedbackwrapped = str_replace('"',"\'",$strfeedbackwrapped);
            $popup = " onmouseover=\"return overlib('$feedback', STICKY, MOUSEOFF, CAPTION, '$strfeedbackwrapped', FGCOLOR, '#FFFFFF');\" ".
                                 " onmouseout=\"return nd();\" ";
        } //if feedback
 
        $result = '';
        $result .= "<label>"; // MDL-7497
        $classes = 'control';
        $result .="<span $popup >" ;
        $input = html_writer::empty_tag('input', $inputattributes) ;
        $result .= $input;
        if (!empty($feedback) && !empty($USER->screenreader)) {
            $result .= "<img src=\"$CFG->pixpath/i/feedback.gif\" alt=\"$feedback\" />";
        }
        $result .= $feedbackimg."</span>";
        $result .= "</label>"; // MDL-7497

        return $result;
    }

}

/**
 * As multianswer have specific display requirements for multichoice display
 * a new class was defined although largely following the multichoice one
 */

abstract class  qtype_multianswer_multichoice_renderer_base extends qtype_renderer {
    abstract protected function get_input_type();

    abstract protected function get_input_name(question_attempt $qa, $value);

    abstract protected function get_input_value($value);

    abstract protected function get_input_id(question_attempt $qa, $value);

    abstract protected function is_choice_selected($response, $value);

    abstract protected function is_right(question_answer $ans);

    abstract protected function get_response(question_attempt $qa);
    


    public function specific_feedback(question_attempt $qa) {
                return '';
    }
    
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        
        $questiontot = $qa->get_question();
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];
        $order = $subquestion->get_order($qa); //array_keys($question->answers); //
        $response = $this->get_response($qa);
        $inputattributes = array(
            'type' => $this->get_input_type(),
         );           

        if ($options->readonly) {
            $inputattributes['disabled'] = 'disabled';
        }
        $radiobuttons = array();
        $feedbackimg = array();
        $feedback = array();
        $classes = array();
        $totfraction = 0 ;
        $nullresponse = true ;
        foreach ($order as $value => $ansid) {
            $ans = $subquestion->answers[$ansid];
            $inputattributes['name'] = $this->get_input_name($qa, $value);
            //  echo "<p>name $value name".$inputattributes['name']." </p>";
            $inputattributes['value'] = $this->get_input_value($value);
            $inputattributes['id'] = $this->get_input_id($qa, $value);
            if ($subquestion->single) {
                $isselected = $this->is_choice_selected($response, $value);
            }    else {
                $isselected = $this->is_choice_selected($response,$value) ;  //$subquestion->field( $value));
            }
            if ($isselected) {
                $inputattributes['checked'] = 'checked';
                $totfraction += $ans->fraction ;
                $nullresponse = false ;
            } else {
                unset($inputattributes['checked']);
            }
            $radiobuttons[] = html_writer::empty_tag('input', $inputattributes) .
                    html_writer::tag('label', $subquestion->format_text($ans->answer), array('for' => $inputattributes['id']));

            if (($options->feedback || $options->correctresponse) && $response !== -1) {
                $feedbackimg[] = question_get_feedback_image($this->is_right($ans), $isselected && $options->feedback);
            } else {
                $feedbackimg[] = '';
            }
            if (($options->feedback || $options->correctresponse) && $isselected) {
                $feedback[] = $subquestion->format_text($ans->feedback);
            } else {
                $feedback[] = '';
            }
            $class = 'r' . ($value % 2);
            if ($options->correctresponse && $ans->fraction > 0) {
                $class .= ' ' . question_get_feedback_class($ans->fraction);
            }
            $classes[] = $class;
        }

        $result = '' ;
        
        $answername = 'answer' ;
        if ($subquestion->layout == 1 ){
            $result .= html_writer::start_tag('div', array('class' => 'ablock'));
    
            $result .= html_writer::start_tag('table', array('class' => $answername));
            foreach ($radiobuttons as $key => $radio) {
                $result .= html_writer::start_tag('tr', array('class' => $answername));
                $result .= html_writer::start_tag('td', array('class' => $answername));
                    $result .= html_writer::tag('span',$radio . $feedbackimg[$key] . $feedback[$key], array('class' => $classes[$key])) . "\n";
                $result .= html_writer::end_tag('td');
                $result .= html_writer::end_tag('tr');
            }
            $result .= html_writer::end_tag('table'); // answer
    
            $result .= html_writer::end_tag('div'); // ablock
        }
        if ($subquestion->layout == 2 ){
            $result .= html_writer::start_tag('div', array('class' => 'ablock'));    
            $result .= html_writer::start_tag('table', array('class' => $answername));
            $result .= html_writer::start_tag('tr', array('class' => $answername));
            foreach ($radiobuttons as $key => $radio) {
                 $result .= html_writer::start_tag('td', array('class' => $answername));
                    $result .= html_writer::tag('span',$radio . $feedbackimg[$key] . $feedback[$key]
                            , array('class' => $classes[$key])) . "\n";
                $result .= html_writer::end_tag('td');
            }
            $result .= html_writer::end_tag('tr');
            $result .= html_writer::end_tag('table'); // answer
    
            $result .= html_writer::end_tag('div'); // ablock
            
           }
        if ($options->feedback ) {
            $result .= html_writer::start_tag('div', array('class' => 'outcome'));

            if ($options->correctness ) {
                if ( $nullresponse ){
                    $state = $qa->get_state();
                    $state = question_state::$invalid;
                    $result1 = $state->default_string();
                    $result .= html_writer::nonempty_tag('div',$result1,
                     array('class' => 'validationerror'));
                    $result1 = ($subquestion->single) ? get_string('singleanswer', 'quiz') : get_string('multipleanswers', 'quiz'); 
                    $result .= html_writer::nonempty_tag('div', $result1,
                    array('class' => 'validationerror'))
                    ;
                }else {
                    $state = $qa->get_state();
                    $state = question_state::graded_state_for_fraction($totfraction);
                    $result1 = $state->default_string();
                    $result .= html_writer::nonempty_tag('div', $result1,
                    array('class' => 'outcome'));
                }
            }
        
           
           if ($options->correctresponse ) {
                    $result1 = $this->correct_response($qa);
                    $result .= html_writer::nonempty_tag('div',$result1, array('class' => 'outcome'))
                    ;
           }
         if ($options->marks  ) { 
            $subgrade= $totfraction * $subquestion->defaultmark ;
            $result .= $questiontot->mark_summary($options, $subquestion->defaultmark , $subgrade );            
        }

       if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div', array('class' => 'validationerror'),
                    $subquestion->get_validation_error($qa->get_last_qt_data()));
        }
                    $result .= html_writer::end_tag('div');

   }
        return $result;
    }


}


class qtype_multianswer_multichoice_single_renderer extends qtype_multianswer_multichoice_renderer_base {
   protected function get_input_type() {
        return 'radio';
   }

   protected function is_choice_selected($response, $value) {
       return $response == $value ;
   }
   protected function is_right(question_answer $ans) {
       return $ans->fraction > 0.9999999;
   }
   protected function get_input_name(question_attempt $qa, $value) {
       $questiontot = $qa->get_question();
       $subquestion = $questiontot->subquestions[$qa->subquestionindex];
       $answername = $subquestion->fieldid.'answer';
       return $qa->get_qt_field_name($answername);
   }
   protected function get_input_value($value) {
       return $value;
   }

   protected function get_input_id(question_attempt $qa, $value) {
        $questiontot = $qa->get_question();
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];
        $answername = $subquestion->fieldid.'answer';
        return $qa->get_qt_field_name($answername);
    }

    protected function get_response(question_attempt $qa) {
        $questiontot = $qa->get_question();
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];
        return $qa->get_last_qt_var($subquestion->fieldid.'answer', -1);
        
    }
    public function correct_response(question_attempt $qa) {
        $questiontot = $qa->get_question();
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];
        
        foreach ($subquestion->answers as $ans) {
            if ($ans->fraction > 0.9999999) {
                return get_string('correctansweris', 'qtype_multichoice',
                        $subquestion->format_text($ans->answer));
            }
        }

    return '';
    }

}
class qtype_multianswer_multichoice_single_inline_renderer extends qtype_multianswer_multichoice_single_renderer {
    protected function get_input_type() {
        return 'select';
    }
  
    public function formulation_and_controls(question_attempt $qa,
        question_display_options $options) {
        $questiontot = $qa->get_question();        
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];
        $answers = $subquestion->answers;
        $correctanswers = $subquestion->get_correct_response();
        foreach($correctanswers as $key=> $value){
                $correct = $value ;
        }
        $order = $subquestion->get_order($qa);
        $response = $this->get_response($qa);
        $currentanswer = $response ;
        $answername = $subquestion->fieldid.'answer';
        $inputname = $qa->get_qt_field_name($answername);
        $inputattributes = array(
            'type' => $this->get_input_type(),
            'name' => $inputname,
        );

        if ($options->readonly) {
            $inputattributes['disabled'] = 'disabled';
            $readonly = 'disabled ="disabled"';
        }
        $choices = array();
        $popup = '';
        $feedback = '' ;
        $answer = '' ;
        $classes = 'control';
        $feedbackimage = '';
        $fraction = 0 ;
        $chosen = 0 ;

        foreach ($order as $value => $ansid) {
            $mcanswer = $subquestion->answers[$ansid];
            $choices[$value] = strip_tags($mcanswer->answer);
            $selected = '';
            $isselected = false ;
            if( $response != ''){
                 $isselected = $this->is_choice_selected($response, $value);
            }
            if ($isselected) {
                 $chosen = $value ;
                 $answer = $mcanswer ;
                 $fraction = $mcanswer->fraction ;
                 $selected = ' selected="selected"';
            }
        }
        if ($options->feedback) {
            if ($answer) {
                $classes .= ' ' . question_get_feedback_class($fraction);
                $feedbackimage = question_get_feedback_image($answer->fraction);
                if ($answer->feedback) {
                    $feedback .= $subquestion->format_text($answer->feedback);
                }
            } else {
                $classes .= ' ' .  question_get_feedback_class(0);
                $feedbackimage = question_get_feedback_image(0);
            }
        }
        // determine popup
        // answer feedback (specific)i.e if options->feedback already set
        // subquestion status correctness or Finished validator if correctness
        // Correct response
        // marks
       $strfeedbackwrapped  = 'Response Status';
       if ($options->feedback ) {
          $feedback = get_string('feedback', 'quiz').":".$feedback."<br />";

            if ($options->correctness ) {
                if ( ! $answer ){
                    $state = $qa->get_state();
                    $state = question_state::$invalid;
                    $strfeedbackwrapped .= ":<font color=red >".$state->default_string()."</font>" ;
                    $feedback =  "<font color=red >".get_string('singleanswer', 'quiz') ."</font><br />"; 
                }else {
                    $state = $qa->get_state();
                    $state = question_state::graded_state_for_fraction($fraction);
                    $strfeedbackwrapped .= ":".$state->default_string();
                }
            }
        
           
            if ($options->correctresponse ) {
                $feedback .= $this->correct_response($qa)."<br />";
            }
            if ($options->marks  ) { 
                $subgrade= $fraction * $subquestion->defaultmark ;
                $feedback .= $questiontot->mark_summary($options, $subquestion->defaultmark , $subgrade );            
            }

            $feedback .= '</div>';
        }

        if ($options->feedback ) {
           // need to  replace ' and " as they could break the popup string
           // as the text comes from database, slashes have been removed 
           // addslashes will not work as it keeps the "
           // HTML &#039; for ' does not work 
           $feedback = str_replace("'","\'",$feedback);
           $feedback = str_replace('"',"\'",$feedback);
           $strfeedbackwrapped = str_replace("'","\'",$strfeedbackwrapped);
           $strfeedbackwrapped = str_replace('"',"\'",$strfeedbackwrapped);
    
           $popup = " onmouseover=\"return overlib('$feedback', STICKY, MOUSEOFF, CAPTION, '$strfeedbackwrapped', FGCOLOR, '#FFFFFF');\" ".
                                 " onmouseout=\"return nd();\" ";
         }
        $result = '';

          $result .= "<span  $popup >";
          $result .= html_writer::start_tag('span', array('class' => $classes), '');
          
          $result .= 
                    choose_from_menu($choices, $inputname, $chosen,
                            ' ', '', '', true, $options->readonly) . $feedbackimage ;
          $result .= html_writer::end_tag('span');
          $result .= html_writer::end_tag('span');


        return $result;
    }
    
    protected function format_choices($question) {
        $choices = array();
        foreach ($question->get_choice_order() as $key => $choiceid) {
            $choices[$key] = strip_tags($question->format_text($question->choices[$choiceid]));
        }
        return $choices;
    }


}
class qtype_multianswer_multichoice_multi_renderer extends qtype_multianswer_multichoice_renderer_base {
    protected function get_input_type() {
        return 'checkbox';
    }

    protected function get_input_name(question_attempt $qa, $value) {
        $questiontot = $qa->get_question();
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];
        return $qa->get_qt_field_name($subquestion->fieldid.'choice'. $value);
    }

    protected function get_input_value($value) {
        return 1;
    }

    protected function get_input_id(question_attempt $qa, $value) {
        return $this->get_input_name($qa, $value);
    }

    protected function get_response(question_attempt $qa) {
        $responses = $qa->get_last_qt_data();
        $questiontot = $qa->get_question();
        $subresponses =$questiontot->decode_subquestion_responses($responses);
        if( isset($subresponses[$qa->subquestionindex])) {
             return $subresponses[$qa->subquestionindex] ;
        }else{
             return '';  
        }
    }

    protected function is_choice_selected($response, $value) {        
        return isset($response['choice'.$value]);
    }

    protected function is_right(question_answer $ans) {
        return $ans->fraction > 0;
    }

    public function correct_response(question_attempt $qa) {
        $questiontot = $qa->get_question();
        $subquestion = $questiontot->subquestions[$qa->subquestionindex];

        $right = array();
        foreach ($subquestion->answers as $ans) {
            if ($ans->fraction > 0) {
                $right[] = $subquestion->format_text($ans->answer);
            }
        }

        if (!empty($right)) {
                return get_string('correctansweris', 'qtype_multichoice',
                        implode(', ', $right));
            
        }
        return '';
    }

  

}
