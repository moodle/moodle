<?php  // $Id$
/**
 * Defines the editing form for the multianswer question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * multianswer editing form definition.
 */
class question_edit_multianswer_form extends question_edit_form {

    //  $questiondisplay will contain the qtype_multianswer_extract_question from the questiontext
    var $questiondisplay ; 

    function definition_inner(&$mform) {
        $question_type_names = question_type_menu();
        $mform->addRule('questiontext', null, 'required', null, 'client');
        
        // Remove meaningless defaultgrade field.
        $mform->removeElement('defaultgrade');
     
         // display the questions from questiontext;
        if  (  "" != optional_param('questiontext','', PARAM_RAW)) {
        
            $this->questiondisplay = fullclone(qtype_multianswer_extract_question(optional_param('questiontext','', PARAM_RAW))) ;
            
        }else {
            $this->questiondisplay = "";
        }       

        if ( isset($this->questiondisplay->options->questions) && is_array($this->questiondisplay->options->questions) ) {
            $countsubquestions =0;
            foreach($this->questiondisplay->options->questions as $subquestion){
                if (!empty($subquestion)){
                   $countsubquestions++;
                }
            } 
        } else {
            $countsubquestions =0;
        }

        $mform->addElement('submit', 'analyzequestion', get_string('decodeverifyquestiontext','qtype_multianswer'));
        $mform->registerNoSubmitButton('analyzequestion');

        for ($sub =1;$sub <=$countsubquestions ;$sub++) {
            $this->editas[$sub] =  'unknown type';
            if (isset( $this->questiondisplay->options->questions[$sub]->qtype) ) {
                $this->editas[$sub] =  $this->questiondisplay->options->questions[$sub]->qtype ; 
            } else if (optional_param('sub_'.$sub."_".'qtype', '', PARAM_RAW) != '') {
                $this->editas[$sub] = optional_param('sub_'.$sub."_".'qtype', '', PARAM_RAW);
            }
            $mform->addElement('header', 'subhdr', get_string('questionno', 'quiz',
                 '{#'.$sub.'}').'&nbsp;'.$question_type_names[$this->questiondisplay->options->questions[$sub]->qtype]);

            $mform->addElement('static', 'sub_'.$sub."_".'questiontext', get_string('questiondefinition','qtype_multianswer'),array('cols'=>60, 'rows'=>3));

            if (isset ( $this->questiondisplay->options->questions[$sub]->questiontext)) {
                $mform->setDefault('sub_'.$sub."_".'questiontext', $this->questiondisplay->options->questions[$sub]->questiontext);
    }

            $mform->addElement('static', 'sub_'.$sub."_".'defaultgrade', get_string('defaultgrade', 'quiz'));
            $mform->setDefault('sub_'.$sub."_".'defaultgrade',$this->questiondisplay->options->questions[$sub]->defaultgrade);

                if ($this->questiondisplay->options->questions[$sub]->qtype =='shortanswer'   ) {
                    $mform->addElement('static', 'sub_'.$sub."_".'usecase', get_string('casesensitive', 'quiz'));
                }
                if ($this->questiondisplay->options->questions[$sub]->qtype =='multichoice'   ) {
                    $mform->addElement('static', 'sub_'.$sub."_".'layout', get_string('layout', 'qtype_multianswer'),array('cols'=>60, 'rows'=>1)) ;//, $gradeoptions);
                }
            foreach ($this->questiondisplay->options->questions[$sub]->answer as $key =>$ans) {

               $mform->addElement('static', 'sub_'.$sub."_".'answer['.$key.']', get_string('answer', 'quiz'), array('cols'=>60, 'rows'=>1));
                
                if ($this->questiondisplay->options->questions[$sub]->qtype =='numerical' && $key == 0 ) {
                    $mform->addElement('static', 'sub_'.$sub."_".'tolerance['.$key.']', get_string('acceptederror', 'quiz')) ;//, $gradeoptions);
                }    

                $mform->addElement('static', 'sub_'.$sub."_".'fraction['.$key.']', get_string('grade')) ;//, $gradeoptions);

                $mform->addElement('static', 'sub_'.$sub."_".'feedback['.$key.']', get_string('feedback', 'quiz'));
            } 

        }

    }

        
    function set_data($question) {
        $default_values =array();
        if (isset($question->id) and $question->id and $question->qtype and $question->questiontext) {

            foreach ($question->options->questions as $key => $wrapped) {
                if(!empty($wrapped)){
                // The old way of restoring the definitions is kept to gradually
                // update all multianswer questions
                if (empty($wrapped->questiontext)) {
                    $parsableanswerdef = '{' . $wrapped->defaultgrade . ':';
                    switch ($wrapped->qtype) {
                        case 'multichoice':
                            $parsableanswerdef .= 'MULTICHOICE:';
                            break;
                        case 'shortanswer':
                            $parsableanswerdef .= 'SHORTANSWER:';
                            break;
                        case 'numerical':
                            $parsableanswerdef .= 'NUMERICAL:';
                            break;
                        default:
                            print_error('unknownquestiontype', 'question', '', $wrapped->qtype);
                    }
                    $separator= '';
                    foreach ($wrapped->options->answers as $subanswer) {
                        $parsableanswerdef .= $separator
                                . '%' . round(100*$subanswer->fraction) . '%';
                        $parsableanswerdef .= $subanswer->answer;
                        if (!empty($wrapped->options->tolerance)) {
                            // Special for numerical answers:
                            $parsableanswerdef .= ":{$wrapped->options->tolerance}";
                            // We only want tolerance for the first alternative, it will
                            // be applied to all of the alternatives.
                            unset($wrapped->options->tolerance);
                        }
                        if ($subanswer->feedback) {
                            $parsableanswerdef .= "#$subanswer->feedback";
                        }
                        $separator = '~';
                    }
                    $parsableanswerdef .= '}';
                    // Fix the questiontext fields of old questions
                    set_field('question', 'questiontext', addslashes($parsableanswerdef), 'id', $wrapped->id);
                } else {
                    $parsableanswerdef = str_replace('&#', '&\#', $wrapped->questiontext);
                }
                $question->questiontext = str_replace("{#$key}", $parsableanswerdef, $question->questiontext);
            }
        }
        }
                
        // set default to $questiondisplay questions elements
        if (isset($this->questiondisplay->options->questions)) {                
            $subquestions = fullclone($this->questiondisplay->options->questions) ;           
            if (count($subquestions)) {
                $sub =1; 
                foreach ($subquestions as $subquestion) {          
                    $prefix = 'sub_'.$sub.'_' ;

                    // validate parameters
                    $answercount = 0;
                    $maxgrade = false;
                    $maxfraction = -1;
                    if ($subquestion->qtype =='shortanswer'   ) {
                        switch ($subquestion->usecase) {
                            case '1':
                                $default_values[$prefix.'usecase']= get_string('caseyes', 'quiz');
                                break;                                   
                            case '0':
                            default :
                                $default_values[$prefix.'usecase']= get_string('caseno', 'quiz');                               
                        }
                    }
                    if ($subquestion->qtype == 'multichoice' ) {
                        $default_values[$prefix.'layout']  = $subquestion->layout ;
                        switch ($subquestion->layout) {
                            case '0':
                                $default_values[$prefix.'layout']= get_string('layoutselectinline', 'qtype_multianswer');
                                break;
                            case '1':
                                $default_values[$prefix.'layout']= get_string('layoutvertical', 'qtype_multianswer');
                                break;                         
                            case '2':
                                $default_values[$prefix.'layout']= get_string('layouthorizontal', 'qtype_multianswer');
                                break;
                            default:
                                $default_values[$prefix.'layout']= get_string('layoutundefined', 'qtype_multianswer');
                        } 
                    }
                    foreach ($subquestion->answer as $key=>$answer) {
                        if ( $subquestion->qtype == 'numerical' && $key == 0 ) {
                            $default_values[$prefix.'tolerance['.$key.']']  = $subquestion->tolerance[0] ;
                        }
                        $trimmedanswer = trim($answer);
                        if ($trimmedanswer !== '') {
                            $answercount++;
                            if ($subquestion->qtype == 'numerical' && !(is_numeric($trimmedanswer) || $trimmedanswer == '*')) {
                                $this->_form->setElementError($prefix.'answer['.$key.']' , get_string('answermustbenumberorstar', 'qtype_numerical'));
                            }
                            if ($subquestion->fraction[$key] == 1) {
                                $maxgrade = true;
                            }                       
                            if ($subquestion->fraction[$key] > $maxfraction) {
                                $maxfraction = $subquestion->fraction[$key] ;
                            }
                        }
                                        
                        $default_values[$prefix.'answer['.$key.']']  = $answer;                         
                    }                                                     
                    if ($answercount == 0) {
                        if ($subquestion->qtype == 'multichoice' ) {
                            $this->_form->setElementError($prefix.'answer[0]' ,  get_string('notenoughanswers', 'qtype_multichoice', 2));
                        } else {
                            $this->_form->setElementError($prefix.'answer[0]' , get_string('notenoughanswers', 'quiz', 1));
                        }
                    }
                    if ($maxgrade == false) {
                        $this->_form->setElementError($prefix.'fraction[0]' ,get_string('fractionsnomax', 'question'));
                    }   
                    foreach ($subquestion->feedback as $key=>$answer) {
                        
                        $default_values[$prefix.'feedback['.$key.']']  = $answer;
                    }                                  
                       foreach ( $subquestion->fraction as $key=>$answer) {
                        $default_values[$prefix.'fraction['.$key.']']  = $answer;
                    }       
  
                 
                     $sub++;                     
                }
            }
        }
           if( $default_values != "")   { 
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
                 
        if (isset($this->questiondisplay->options->questions)) {
                
           $subquestions = fullclone($this->questiondisplay->options->questions) ;           
            if (count($subquestions)) {
                $sub =1; 
                foreach ($subquestions as $subquestion) {          
                    $prefix = 'sub_'.$sub.'_' ;
                    $answercount = 0;
                    $maxgrade = false;
                    $maxfraction = -1;
                    foreach ( $subquestion->answer as $key=>$answer) {
                        $trimmedanswer = trim($answer);
                        if ($trimmedanswer !== '') {
                            $answercount++;
                            if ($subquestion->qtype =='numerical' && !(is_numeric($trimmedanswer) || $trimmedanswer == '*')) {
                                $errors[$prefix.'answer['.$key.']']=  get_string('answermustbenumberorstar', 'qtype_numerical');
        }
                            if ($subquestion->fraction[$key] == 1) {
                                $maxgrade = true;
        }
                            if ($subquestion->fraction[$key] > $maxfraction) {
                                $maxfraction = $subquestion->fraction[$key] ;
                            }
                        }                                        
                    }                                                     
                    if ($answercount==0) {
                        if ( $subquestion->qtype =='multichoice' ) {
                            $errors[$prefix.'answer[0]']= get_string('notenoughanswers', 'qtype_multichoice', 2);
                        }else {
                            $errors[$prefix.'answer[0]'] = get_string('notenoughanswers', 'quiz', 1);
                        }
                    }
                    if ($maxgrade == false) {
                        $errors[$prefix.'fraction[0]']=get_string('fractionsnomax', 'question');
                    }   
                    $sub++;                     
                }
            } else {
                $errors['questiontext']=get_string('questionsmissing', 'qtype_multianswer');  
            }
        }

        return $errors;
    }

    function qtype() {
        return 'multianswer';
    }
}
?>
