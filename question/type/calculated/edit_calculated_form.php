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
     //   if ($this->editasmultichoice == 1){
        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'), array('size' => 50));
        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeated[] =& $mform->createElement('htmleditor', 'feedback', get_string('feedback', 'quiz'),
                                array('course' => $this->coursefilesid));
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';

        $mform->setType('answer', PARAM_NOTAGS);

        $addrepeated = array();
        if ($this->editasmultichoice == 1){
            $addrepeated[] =& $mform->createElement('hidden', 'tolerance');
            $addrepeated[] =& $mform->createElement('hidden', 'tolerancetype',1);
        }else {
            $addrepeated[] =& $mform->createElement('text', 'tolerance', get_string('tolerance', 'qtype_calculated'));
            $addrepeated[] =& $mform->createElement('select', 'tolerancetype', get_string('tolerancetype', 'quiz'), $this->qtypeobj->tolerance_types());
        }
        $repeatedoptions['tolerance']['type'] = PARAM_NUMBER;
        $repeatedoptions['tolerance']['default'] = 0.01;

        $addrepeated[] =&  $mform->createElement('select', 'correctanswerlength', get_string('correctanswershows', 'qtype_calculated'), range(0, 9));
        $repeatedoptions['correctanswerlength']['default'] = 2;

        $answerlengthformats = array('1' => get_string('decimalformat', 'quiz'), '2' => get_string('significantfiguresformat', 'quiz'));
        $addrepeated[] =&  $mform->createElement('select', 'correctanswerformat', get_string('correctanswershowsformat', 'qtype_calculated'), $answerlengthformats);
        array_splice($repeated, 3, 0, $addrepeated);
        if ($this->editasmultichoice == 1){
             $repeated[1]->setLabel('...<strong>{={x}+..}</strong>...');
        }else {
            $repeated[1]->setLabel(get_string('correctanswerformula', 'quiz').'=');

        }

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
        $label = get_string("sharedwildcards", "qtype_datasetdependent");
        $mform->addElement('hidden', 'initialcategory', 1);
        $mform->setType('initialcategory', PARAM_INT);
        $html2 = $this->qtypeobj->print_dataset_definitions_category($this->question);
        $mform->insertElementBefore($mform->createElement('static','listcategory',$label,$html2),'name');
        $addfieldsname='updatecategory';
        $addstring=get_string("updatecategory", "qtype_calculated");
                $mform->registerNoSubmitButton($addfieldsname);
        $this->editasmultichoice =  0 ;
        if ( isset($this->question->options->multichoice) && $this->question->options->multichoice == '1'){
            $this->editasmultichoice = 1 ;
        }else {
            if ( !isset($this->question->id ) && 1 ==  optional_param('multichoice', '', PARAM_RAW )){
                $this->editasmultichoice = 1 ;
            }else {
                $this->editasmultichoice = 0 ;
            }
            if ( !isset($this->question->id ) && '' != optional_param('createoptionbutton', '', PARAM_RAW) && 1 ==  optional_param('multichoice', '', PARAM_RAW)){
                $this->editasmultichoice = 1 ;
            }
            if ( !isset($this->question->id )== 0 && '' != optional_param('createoptionbutton', '', PARAM_RAW) && 0 ==  optional_param('multichoice', '', PARAM_RAW)){
                $this->editasmultichoice = 0 ;
            }
        }

   /*      if ( '' !=  optional_param('changetomultichoice', '', PARAM_RAW)){
           $this->editasmultichoice = 1 ;
        }
         if ( '' !=  optional_param('changetocalculated', '', PARAM_RAW)){
            $this->editasmultichoice = 0 ;
       }*/

        $mform->insertElementBefore(    $mform->createElement('submit', $addfieldsname, $addstring),'listcategory');
        $mform->registerNoSubmitButton('createoptionbutton');

        if(!isset($this->question->id ) ){
            $mform->addElement('header', 'choicehdr',get_string('Choosingcreationmode', 'qtype_calculated'));
            $createoptions = Array();

            $createoptions['0']=get_string('Regularcalculated', 'qtype_calculated');
            $createoptions['1']=get_string('Multiplechoicecalculated', 'qtype_calculated');
            $addgrp1 = array();
            $addgrp1[] =& $mform->createElement('submit', 'createoptionbutton', get_string('Createas', 'qtype_calculatedsimple'));
            $addgrp1[] =& $mform->createElement('select', "multichoice",'' , $createoptions);
            $mform->addGroup($addgrp1, 'addgrp1', '', '   ', false);
        }else {
            $mform->addElement('hidden', 'multichoice',$this->editasmultichoice);
            $mform->setType('multichoice', PARAM_INT);
        }

        if ($this->editasmultichoice == 1){
            $mform->addElement('header', 'choicehdr',get_string('multichoicecalculatedquestion', 'qtype_calculated'));
            $menu = array(get_string('answersingleno', 'qtype_multichoice'), get_string('answersingleyes', 'qtype_multichoice'));
            $mform->addElement('select', 'single', get_string('answerhowmany', 'qtype_multichoice'), $menu);
            $mform->setDefault('single', 1);

            $mform->addElement('advcheckbox', 'shuffleanswers', get_string('shuffleanswers', 'qtype_multichoice'), null, null, array(0,1));
            $mform->setHelpButton('shuffleanswers', array('multichoiceshuffle', get_string('shuffleanswers','qtype_multichoice'), 'qtype_multichoice'));
            $mform->setDefault('shuffleanswers', 1);

            $numberingoptions = $QTYPES['multichoice']->get_numbering_styles();
            $menu = array();
            foreach ($numberingoptions as $numberingoption) {
                $menu[$numberingoption] = get_string('answernumbering' . $numberingoption, 'qtype_multichoice');
            }
            $mform->addElement('select', 'answernumbering', get_string('answernumbering', 'qtype_multichoice'), $menu);
            $mform->setDefault('answernumbering', 'abc');
        }else { //editing as regular
            $mform->addElement('header', 'choicehdr', get_string('regularcalculatedquestion', 'qtype_calculated'));
            $mform->addElement('hidden','single', '1');
            $mform->setType('single', PARAM_INT);

            $mform->addElement('hidden','shuffleanswers', '1');
            $mform->setType('shuffleanswers', PARAM_INT);
            $mform->addElement('hidden','answernumbering', 'abc');
            $mform->setType('answernumbering', PARAM_SAFEDIR);
        }

        $creategrades = get_grade_options();
        if ($this->editasmultichoice == 1){
            $this->add_per_answer_fields($mform, get_string('choiceno', 'qtype_multichoice', '{no}'),
                $creategrades->gradeoptionsfull, max(5, QUESTION_NUMANS_START));
        }else{
            $this->add_per_answer_fields($mform, get_string('answerhdr', 'qtype_calculated', '{no}'),
                $creategrades->gradeoptions, 1, 1);
        }


        $repeated = array();
        if ($this->editasmultichoice == 1){
            $nounits = optional_param('nounits', 1, PARAM_INT);
            $mform->addElement('hidden', 'nounits', $nounits);
            $mform->setType('nounits', PARAM_INT);
            $mform->setConstants(array('nounits'=>$nounits));
            for ($i=0; $i< $nounits; $i++) {
                $mform->addElement('hidden','unit'."[$i]", optional_param('unit'."[$i]", '', PARAM_NOTAGS));
                $mform->setType('unit'."[$i]", PARAM_NOTAGS);
                $mform->addElement('hidden', 'multiplier'."[$i]", optional_param('multiplier'."[$i]", '', PARAM_NUMBER));
                $mform->setType('multiplier'."[$i]", PARAM_NUMBER);
            }
          $mform->addElement('hidden','unitgradingtype',optional_param('unitgradingtype', '', PARAM_INT)) ;
          $mform->addElement('hidden','unitpenalty',optional_param('unitpenalty', '', PARAM_NUMBER)) ;
          $mform->addElement('hidden','showunits',optional_param('showunits', '', PARAM_INT)) ;
          $mform->addElement('hidden','unitsleft',optional_param('unitsleft', '', PARAM_INT)) ;
          $mform->addElement('hidden','instructions',optional_param('instructions', '', PARAM_RAW)) ;

        }else {
        $QTYPES['numerical']->edit_numerical_options($mform,$this);
            $repeated[] =& $mform->createElement('header', 'unithdr', get_string('unithdr', 'qtype_numerical', '{no}'));
            $repeated[] =& $mform->createElement('text', 'unit', get_string('unit', 'quiz'));
            $repeated[] =& $mform->createElement('text', 'multiplier', get_string('multiplier', 'quiz'));

        $mform->setType('unit', PARAM_NOTAGS);

        $mform->setType('multiplier', PARAM_NUMBER);

        if (isset($this->question->options)){
            $countunits = count($this->question->options->units);
        } else {
            $countunits = 0;
        }
        if ($this->question->formoptions->repeatelements){
            $repeatsatstart = $countunits + 1;
        } else {
            $repeatsatstart = $countunits;
        }
        $this->repeat_elements($repeated, $repeatsatstart, array(), 'nounits', 'addunits', 2, get_string('addmoreunitblanks', 'qtype_calculated', '{no}'));

        }
        if ($mform->elementExists('multiplier[0]')){
            $firstunit =& $mform->getElement('multiplier[0]');
            $firstunit->freeze();
            $firstunit->setValue('1.0');
            $firstunit->setPersistantFreeze(true);
        }
        if ($this->editasmultichoice == 1){
            $mform->setType('addunits','hidden');
            $mform->addElement('header', 'overallfeedbackhdr', get_string('overallfeedback', 'qtype_multichoice'));

            foreach (array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback') as $feedbackname) {
                $mform->addElement('htmleditor', $feedbackname, get_string($feedbackname, 'qtype_multichoice'),
                                    array('course' => $this->coursefilesid));
                $mform->setType($feedbackname, PARAM_RAW);
            }
        }else {
            foreach (array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback') as $feedbackname) {
                $mform->addElement('hidden', $feedbackname);
                $mform->setType($feedbackname, PARAM_RAW);
            }
        }
        //hidden elements
        $mform->addElement('hidden', 'synchronize', '');
        $mform->setType('synchronize', PARAM_INT);
        if (isset($this->question->options)&& isset($this->question->options->synchronize) ){
            $mform->setDefault("synchronize", $this->question->options->synchronize);
        } else {
            $mform->setDefault("synchronize", 0 );
        }
        $mform->addElement('hidden', 'wizard', 'datasetdefinitions');
        $mform->setType('wizard', PARAM_ALPHA);


    }

    function set_data($question) {
        if (isset($this->editasmultichoice)){
             $default_values['multichoice']= $this->editasmultichoice ; //$this->editasmultichoice ;
        }
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
          $default_values['unitgradingtype'] = $question->options->unitgradingtype ;
          $default_values['unitpenalty'] = $question->options->unitpenalty ;
          $default_values['showunits'] = $question->options->showunits ;
          $default_values['unitsleft'] = $question->options->unitsleft ;
          $default_values['instructions'] = $question->options->instructions  ;

            if (isset($question->options->units)){
                $units  = array_values($question->options->units);
                // make sure the default unit is at index 0
                usort($units, create_function('$a, $b',
                'if (1.0 === (float)$a->multiplier) { return -1; } else '.
                'if (1.0 === (float)$b->multiplier) { return 1; } else { return 0; }'));
                if (count($units)) {
                    $key = 0;
                    foreach ($units as $unit){
                        $default_values['unit['.$key.']'] = $unit->unit;
                        $default_values['multiplier['.$key.']'] = $unit->multiplier;
                        $key++;
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
        if ($data['multichoice']== 1 ){
            foreach ($answers as $key => $answer){
                $trimmedanswer = trim($answer);
                if (($trimmedanswer!='')||$answercount==0){
                    //verifying for errors in {=...} in answer text;
                    $qanswer = "";
                    $qanswerremaining =  $trimmedanswer ;
                    $possibledatasets = $this->qtypeobj->find_dataset_names($trimmedanswer);
                        foreach ($possibledatasets as $name => $value) {
                        $qanswerremaining = str_replace('{'.$name.'}', '1', $qanswerremaining);
                    }
                //     echo "numericalquestion qanswerremaining <pre>";print_r($possibledatasets);
                    while  (preg_match('~\{=([^[:space:]}]*)}~', $qanswerremaining, $regs1)) {
                        $qanswersplits = explode($regs1[0], $qanswerremaining, 2);
                        $qanswer =$qanswer.$qanswersplits[0];
                        $qanswerremaining = $qanswersplits[1];
                        if (!empty($regs1[1]) && $formulaerrors = qtype_calculated_find_formula_errors($regs1[1])) {
                            if(!isset($errors['answer['.$key.']'])){
                                $errors['answer['.$key.']'] = $formulaerrors.':'.$regs1[1] ;
                            }else {
                                $errors['answer['.$key.']'] .= '<br/>'.$formulaerrors.':'.$regs1[1];
                            }
                        }
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
                if ($answer != '') {
                    if ($data['fraction'][$key] > 0) {
                        $totalfraction += $data['fraction'][$key];
                    }
                    if ($data['fraction'][$key] > $maxfraction) {
                        $maxfraction = $data['fraction'][$key];
                    }
                }
            }
            if ($answercount==0){
                $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
                $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
            } elseif ($answercount==1){
                $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);

            }

            /// Perform sanity checks on fractional grades
            if ($data['single']) {
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
        }else{ // regular calculated
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
            }*/
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
            }
            if ($answercount==0){
                $errors['answer[0]'] = get_string('atleastoneanswer', 'qtype_calculated');
            }
            if ($maxgrade == false) {
                $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
            }
        }

        return $errors;
    }
}

