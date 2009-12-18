<?php  // $Id$
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

    function get_per_answer_fields(&$mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions, $repeatedoptions, $answersoption);
        $mform->setType('answer', PARAM_NOTAGS);

        $addrepeated = array();
        $addrepeated[] =& $mform->createElement('text', 'tolerance', get_string('tolerance', 'qtype_calculated'));
        $repeatedoptions['tolerance']['type'] = PARAM_NUMBER;
        $repeatedoptions['tolerance']['default'] = 0.01;
        $addrepeated[] =& $mform->createElement('select', 'tolerancetype', get_string('tolerancetype', 'quiz'), $this->qtypeobj->tolerance_types());

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
        $label = get_string("sharedwildcards", "qtype_datasetdependent");
        $mform->addElement('hidden', 'initialcategory', 1);
        $mform->setType('initialcategory', PARAM_INT);
        $html2 = $this->qtypeobj->print_dataset_definitions_category($this->question);
        $mform->insertElementBefore($mform->createElement('static','listcategory',$label,$html2),'name');
        $addfieldsname='updatecategory';
        $addstring=get_string("updatecategory", "qtype_calculated");
                $mform->registerNoSubmitButton($addfieldsname);

        $mform->insertElementBefore(    $mform->createElement('submit', $addfieldsname, $addstring),'listcategory');

        $creategrades = get_grade_options();
        $this->add_per_answer_fields($mform, get_string('answerhdr', 'qtype_calculated', '{no}'),
                $creategrades->gradeoptions, 1, 1);

        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'unithdr', get_string('unithdr', 'qtype_numerical', '{no}'));

        $repeated[] =& $mform->createElement('text', 'unit', get_string('unit', 'quiz'));
        $mform->setType('unit', PARAM_NOTAGS);

        $repeated[] =& $mform->createElement('text', 'multiplier', get_string('multiplier', 'quiz'));
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

        if ($mform->elementExists('multiplier[0]')){
            $firstunit =& $mform->getElement('multiplier[0]');
            $firstunit->freeze();
            $firstunit->setValue('1.0');
            $firstunit->setPersistantFreeze(true);
        }
        //hidden elements
        $mform->addElement('hidden', 'wizard', 'datasetdefinitions');
        $mform->setType('wizard', PARAM_ALPHA);


    }

    function set_data($question) {
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
        $errors = parent::validation($data, $files);
        //verifying for errors in {=...} in question text;
        $qtext = "";
        $qtextremaining = $data['questiontext'] ;
        $possibledatasets = $this->qtypeobj->find_dataset_names($data['questiontext']);
            foreach ($possibledatasets as $name => $value) {
            $qtextremaining = str_replace('{'.$name.'}', '1', $qtextremaining);
        }
    //     echo "numericalquestion qtextremaining <pre>";print_r($possibledatasets);
        while  (ereg('\{=([^[:space:]}]*)}', $qtextremaining, $regs1)) {
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

        return $errors;
    }
}
?>