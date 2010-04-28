<?php
/**
 * Defines the editing form for the calculated question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * calculated editing form definition.
 */
class question_edit_calculated_form extends question_edit_form {
    /**
     * Handle to the question type for this question.
     *
     * @var question_calculated_qtype
     */
    var $qtypeobj;

    /**
     * Get the list of form elements to repeat, one for each answer.
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $repeatedoptions reference to array of repeated options to fill
     * @param $answersoption reference to return the name of $question->options field holding an array of answers
     * @return array of form fields.
     */
 /*   function get_per_answer_fields(&$mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'answerhdr', $label);
        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'), array('size' => 50));
        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeated[] =& $mform->createElement('htmleditor', 'feedback', get_string('feedback', 'quiz'),
                                array('course' => $this->coursefilesid));
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        return $repeated;
    }*/
    function get_per_answer_fields(&$mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
   //     $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions, $repeatedoptions, $answersoption);
           $repeated = array();
        $repeated[] =& $mform->createElement('header', 'answerhdr', $label);
        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'), array('size' => 50));
        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeated[] =& $mform->createElement('htmleditor', 'feedback', get_string('feedback', 'quiz'),
                                array('course' => $this->coursefilesid));
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';

        $mform->setType('answer', PARAM_NOTAGS);

        $addrepeated = array();
            $addrepeated[] =& $mform->createElement('text', 'tolerance', get_string('tolerance', 'qtype_calculated'));
            $addrepeated[] =& $mform->createElement('select', 'tolerancetype', get_string('tolerancetype', 'quiz'), $this->qtypeobj->tolerance_types());
        $repeatedoptions['tolerance']['type'] = PARAM_NUMBER;
        $repeatedoptions['tolerance']['default'] = 0.01;

        $addrepeated[] =&  $mform->createElement('select', 'correctanswerlength', get_string('correctanswershows', 'qtype_calculated'), range(0, 9));
        $repeatedoptions['correctanswerlength']['default'] = 2;

        $answerlengthformats = array('1' => get_string('decimalformat', 'quiz'), '2' => get_string('significantfiguresformat', 'quiz'));
        $addrepeated[] =&  $mform->createElement('select', 'correctanswerformat', get_string('correctanswershowsformat', 'qtype_calculated'), $answerlengthformats);
        array_splice($repeated, 3, 0, $addrepeated);
            $repeated[1]->setLabel(get_string('correctanswerformula', 'quiz').'=');

        return $repeated;
    }

    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {
        global $QTYPES;
        $this->qtypeobj =& $QTYPES[$this->qtype()];
      // echo code left for testing period
      //  echo "<p>question ".optional_param('multichoice', '', PARAM_RAW)." optional<pre>";print_r($this->question);echo "</pre></p>";
        $label = get_string('sharedwildcards', 'qtype_calculated');
        $mform->addElement('hidden', 'initialcategory', 1);
        $mform->setType('initialcategory', PARAM_INT);
        $html2 = $this->qtypeobj->print_dataset_definitions_category($this->question);
        $mform->insertElementBefore($mform->createElement('static','listcategory',$label,$html2),'name');
        $addfieldsname='updatecategory';
        $addstring=get_string("updatecategory", "qtype_calculated");
                $mform->registerNoSubmitButton($addfieldsname);

        $mform->insertElementBefore(    $mform->createElement('submit', $addfieldsname, $addstring),'listcategory');
        $mform->registerNoSubmitButton('createoptionbutton');

        //editing as regular
            $mform->setType('single', PARAM_INT);

            $mform->addElement('hidden','shuffleanswers', '1');
            $mform->setType('shuffleanswers', PARAM_INT);
            $mform->addElement('hidden','answernumbering', 'abc');
            $mform->setType('answernumbering', PARAM_SAFEDIR);

        $creategrades = get_grade_options();

            $this->add_per_answer_fields($mform, get_string('answerhdr', 'qtype_calculated', '{no}'),
                $creategrades->gradeoptions, 1, 1);


        $repeated = array();

        $QTYPES['numerical']->add_units_options($mform,$this);
        $QTYPES['numerical']->add_units_elements($mform,$this);
        foreach (array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback') as $feedbackname) {
            $mform->addElement('hidden', $feedbackname);
            $mform->setType($feedbackname, PARAM_RAW);
        }
        //hidden elements
        $mform->addElement('hidden', 'synchronize', '');
        $mform->setType('synchronize', PARAM_INT);
        $mform->addElement('hidden', 'wizard', 'datasetdefinitions');
        $mform->setType('wizard', PARAM_ALPHA);


    }

    function set_data($question) {
        $default_values = array();
        if (isset($question->options)){
            $answers = $question->options->answers;
            if (count($answers)) {
                $key = 0;
                foreach ($answers as $answer){
                    $default_values['answer['.$key.']'] = $answer->answer;
                    $default_values['fraction['.$key.']'] = $answer->fraction;
                    $default_values['tolerance['.$key.']'] = $answer->tolerance;
                    $default_values['tolerancetype['.$key.']'] = $answer->tolerancetype;
                    $default_values['correctanswerlength['.$key.']'] = $answer->correctanswerlength;
                    $default_values['correctanswerformat['.$key.']'] = $answer->correctanswerformat;
                    $default_values['feedback['.$key.']'] = $answer->feedback;
                    $key++;
                }
            }
          $default_values['synchronize'] = $question->options->synchronize ;
          $default_values['unitgradingtype'] = $question->options->unitgradingtype ;
          $default_values['unitpenalty'] = $question->options->unitpenalty ;
          switch ($question->options->showunits){
            case 'O' :
            case '1' : 
                $default_values['showunits0'] = $question->options->showunits ;
                $default_values['unitrole'] = 0 ;
                break;
            case '2' :
            case '3' : 
                $default_values['showunits1'] = $question->options->showunits ;
                $default_values['unitrole'] = 1 ;
                break;
           } 
            $default_values['unitsleft'] = $question->options->unitsleft ;
            $default_values['instructions'] = $question->options->instructions  ;

            if (isset($question->options->units)){
                $units  = array_values($question->options->units);
                if (!empty($units)) {
                    foreach ($units as $key => $unit){
                        $default_values['unit['.$key.']'] = $unit->unit;
                        $default_values['multiplier['.$key.']'] = $unit->multiplier;
                    }
                }
            }
        }
        if (isset($question->options->single)){
            $default_values['single'] =  $question->options->single;
            $default_values['answernumbering'] =  $question->options->answernumbering;
            $default_values['shuffleanswers'] =  $question->options->shuffleanswers;
            $default_values['correctfeedback'] =  $question->options->correctfeedback;
            $default_values['partiallycorrectfeedback'] =  $question->options->partiallycorrectfeedback;
            $default_values['incorrectfeedback'] =  $question->options->incorrectfeedback;
        }
        $default_values['submitbutton'] = get_string('nextpage', 'qtype_calculated');
        $default_values['makecopy'] = get_string('makecopynextpage', 'qtype_calculated');
        /* set the wild cards category display given that on loading the category element is
        unselected when processing this function but have a valid value when processing the
        update category button. The value can be obtain by
         $qu->category =$this->_form->_elements[$this->_form->_elementIndex['category']]->_values[0];
         but is coded using existing functions
        */
         $qu = new stdClass;
         $el = new stdClass;
         /* no need to call elementExists() here */
         if ($this->_form->elementExists('category')){
            $el=$this->_form->getElement('category');
         } else {
            $el=$this->_form->getElement('categorymoveto');
         }
         if($value =$el->getSelected()) {
            $qu->category =$value[0];
        }else {
            $qu->category=$question->category;// on load  $question->category is set by question.php
        }
        $html2 = $this->qtypeobj->print_dataset_definitions_category($qu);
        $this->_form->_elements[$this->_form->_elementIndex['listcategory']]->_text = $html2 ;
        $question = (object)((array)$question + $default_values);

        parent::set_data($question);
    }

    function qtype() {
        return 'calculated';
    }

    function validation($data, $files) {
        global $QTYPES;
              // echo code left for testing period

              //  echo "<p>question <pre>";print_r($this->question);echo "</pre></p>";
              //  echo "<p>data <pre>";print_r($data);echo "</pre></p>";

        $errors = parent::validation($data, $files);
        //verifying for errors in {=...} in question text;
        $qtext = "";
        $qtextremaining = $data['questiontext'] ;
        $possibledatasets = $this->qtypeobj->find_dataset_names($data['questiontext']);
            foreach ($possibledatasets as $name => $value) {
            $qtextremaining = str_replace('{'.$name.'}', '1', $qtextremaining);
        }
    //     echo "numericalquestion qtextremaining <pre>";print_r($possibledatasets);
        while  (preg_match('~\{=([^[:space:]}]*)}~', $qtextremaining, $regs1)) {
            $qtextsplits = explode($regs1[0], $qtextremaining, 2);
            $qtext =$qtext.$qtextsplits[0];
            $qtextremaining = $qtextsplits[1];
            if (!empty($regs1[1]) && $formulaerrors = qtype_calculated_find_formula_errors($regs1[1])) {
                if(!isset($errors['questiontext'])){
                    $errors['questiontext'] = $formulaerrors.':'.$regs1[1] ;
                }else {
                    $errors['questiontext'] .= '<br/>'.$formulaerrors.':'.$regs1[1];
                }
            }
        }
        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        $possibledatasets = $this->qtypeobj->find_dataset_names($data['questiontext']);
        $mandatorydatasets = array();
        foreach ($answers as $key => $answer){
            $mandatorydatasets += $this->qtypeobj->find_dataset_names($answer);
        }
        if ( count($mandatorydatasets )==0){
          //  $errors['questiontext']=get_string('atleastonewildcard', 'qtype_datasetdependent');
            foreach ($answers as $key => $answer){
                $errors['answer['.$key.']'] = get_string('atleastonewildcard', 'qtype_datasetdependent');
            }
        }
 // regular calculated
            foreach ($answers as $key => $answer){
                //check no of choices
                // the * for everykind of answer not actually implemented
                $trimmedanswer = trim($answer);
                if (($trimmedanswer!='')||$answercount==0){
                    $eqerror = qtype_calculated_find_formula_errors($trimmedanswer);
                    if (FALSE !== $eqerror){
                        $errors['answer['.$key.']'] = $eqerror;
                    }
                }
                if ($trimmedanswer!=''){
                    if ('2' == $data['correctanswerformat'][$key]
                            && '0' == $data['correctanswerlength'][$key]) {
                        $errors['correctanswerlength['.$key.']'] = get_string('zerosignificantfiguresnotallowed','quiz');
                    }
                    if (!is_numeric($data['tolerance'][$key])){
                        $errors['tolerance['.$key.']'] = get_string('mustbenumeric', 'qtype_calculated');
                    }
                    if ($data['fraction'][$key] == 1) {
                       $maxgrade = true;
                    }

                    $answercount++;
                }
                //check grades

                //TODO how should grade checking work here??
                /*if ($answer != '') {
                    if ($data['fraction'][$key] > 0) {
                        $totalfraction += $data['fraction'][$key];
                    }
                    if ($data['fraction'][$key] > $maxfraction) {
                        $maxfraction = $data['fraction'][$key];
                    }
                }*/
            }

            //grade checking :
            /// Perform sanity checks on fractional grades
            /*if ( ) {
                if ($maxfraction != 1) {
                    $maxfraction = $maxfraction * 100;
                    $errors['fraction[0]'] = get_string('errfractionsnomax', 'qtype_multichoice', $maxfraction);
                }
            } else {
                $totalfraction = round($totalfraction,2);
                if ($totalfraction != 1) {
                    $totalfraction = $totalfraction * 100;
                    $errors['fraction[0]'] = get_string('errfractionsaddwrong', 'qtype_multichoice', $totalfraction);
                }
            }
            $units  = $data['unit'];
            if (count($units)) {
                foreach ($units as $key => $unit){
                    if (is_numeric($unit)){
                        $errors['unit['.$key.']'] = get_string('mustnotbenumeric', 'qtype_calculated');
                    }
                    $trimmedunit = trim($unit);
                    $trimmedmultiplier = trim($data['multiplier'][$key]);
                    if (!empty($trimmedunit)){
                        if (empty($trimmedmultiplier)){
                            $errors['multiplier['.$key.']'] = get_string('youmustenteramultiplierhere', 'qtype_calculated');
                        }
                        if (!is_numeric($trimmedmultiplier)){
                            $errors['multiplier['.$key.']'] = get_string('mustbenumeric', 'qtype_calculated');
                        }

                    }
                }                
            }*/
            $QTYPES['numerical']->validate_numerical_options($data, $errors) ;
       /*     if ($data['unitrole'] == 0 ){
                $showunits = $data['showunits0'];
            }else {
                $showunits = $data['showunits1'];
            }
            
            if (($showunits == 0) || ($showunits == 1) || ($showunits == 2)){
               if (trim($units[0]) == ''){
                 $errors['unit[0]'] = 'You must set a valid unit name' ;
                }
            }
            if ($showunits == 3 ){
                if (count($units)) {
                    foreach ($units as $key => $unit){
                        if ($units[$key] != ''){
                        $errors["unit[$key]"] = 'You must erase this unit name' ;
                        }
                    }
                }
            }*/
            if ($answercount==0){
                $errors['answer[0]'] = get_string('atleastoneanswer', 'qtype_calculated');
            }
            if ($maxgrade == false) {
                $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
            }
        

        return $errors;
    }
}

