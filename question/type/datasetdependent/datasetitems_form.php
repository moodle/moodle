<?php
class question_dataset_dependent_items_form extends moodleform {
    /**
     * Question object with options and answers already loaded by get_question_options
     * Be careful how you use this it is needed sometimes to set up the structure of the
     * form in definition_inner but data is always loaded into the form with set_defaults.
     *
     * @var object
     */
    var $question;
    /**
     * Reference to question type object
     *
     * @var question_dataset_dependent_questiontype
     */
    var $qtypeobj;
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function question_dataset_dependent_items_form($submiturl, $question){
        global $QTYPES;
        $this->question = $question;
        $this->qtypeobj =& $QTYPES[$this->question->qtype];
        parent::moodleform($submiturl);
    }
    function definition() {
        $mform =& $this->_form;

        $repeated = array();
        $repeatedoptions = array();
        $repeated[] =& $mform->createElement('header', 'itemhdr', get_string('itemno', 'qtype_datasetdependent', '{no}'));
        $params = array('a', 'b', 'c');
        foreach ($params as $paramno => $param){
            $idx = $paramno +1;
            $repeated[] =& $mform->createElement('text', "number[$idx]", get_string('param', 'qtype_datasetdependent', $param));
            $repeated[] =& $mform->createElement('hidden', "itemid[$idx]");
            $repeated[] =& $mform->createElement('hidden', "definition[$idx]");

            $repeatedoptions["number[$idx]"]['type'] = PARAM_NUMBER;
            //$repeatedoptions["number[$idx]"]['rule'] = 'numeric';
            $repeatedoptions["itemid[$idx]"]['type'] = PARAM_INT;
            $repeatedoptions["definition[$idx]"]['type'] = PARAM_NOTAGS;
        }

        /*if (isset($this->question->options)){
            $countanswers = count($this->question->options->answers);
        } else {
            $countanswers = 0;
        }
        $repeatsatstart = (QUESTION_NUMANS_START > ($countanswers + QUESTION_NUMANS_ADD))?
                            QUESTION_NUMANS_START : ($countanswers + QUESTION_NUMANS_ADD);
        */
        $repeatsatstart = 3;
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'itemsno', 'itemsadd', 1, get_string('additem', 'qtype_datasetdependent'));


        if ($this->qtypeobj->supports_dataset_item_generation()){
            $radiogrp = array();
            $radiogrp[] =& $mform->createElement('radio', "forceregeneration", 0, get_string('reuseifpossible', 'quiz'));
            $radiogrp[] =& $mform->createElement('radio', "forceregeneration", 1, get_string('forceregeneration', 'quiz'));
            $mform->addGroup($radiogrp, 'forceregenerationgrp', '', null, false);
        }

        $mform->addElement('header', 'additemhdr', get_string('itemtoadd', 'qtype_datasetdependent'));
        foreach ($params as $paramno => $param){
            $idx = $paramno +1;
            $mform->addElement('text', "numbertoadd[$idx]", get_string('param', 'qtype_datasetdependent', $param));

            $minmaxgrp = array();
            $minmaxgrp[] =& $mform->createElement('text', "calcmin[$idx]", get_string('calcmin', 'qtype_datasetdependent'), 'size="3"');
            $minmaxgrp[] =& $mform->createElement('text', "calcmax[$idx]", get_string('calcmax', 'qtype_datasetdependent'), 'size="3"');
            $mform->addGroup($minmaxgrp, 'minmaxgrp', get_string('minmax', 'qtype_datasetdependent'), ' - ', false);

            $precisionoptions = range(0, 10);
            $mform->addElement('select', "calclength[$idx]", get_string('calclength', 'qtype_datasetdependent'), $precisionoptions);

            $distriboptions = array('uniform' => get_string('uniform', 'qtype_datasetdependent'), 'loguniform' => get_string('loguniform', 'qtype_datasetdependent'));
            $mform->addElement('select', "calcdistribution[$idx]", get_string('calcdistribution', 'qtype_datasetdependent'), $distriboptions);


            $mform->addElement('submit', "generate[$idx]", get_string('generate', 'qtype_datasetdependent'));
            $mform->addElement('hidden', "definition[$idx]");

            $repeatedoptions["number[$idx]"]['type'] = PARAM_NUMBER;
            //$repeatedoptions["number[$idx]"]['rule'] = 'numeric';
            $repeatedoptions["itemid[$idx]"]['type'] = PARAM_INT;
            $repeatedoptions["definition[$idx]"]['type'] = PARAM_NOTAGS;
        }
        $mform->addElement('hidden', 'wizardpage', 'datasetitems');
        $mform->setType('wizardpage', PARAM_ALPHA);
        $this->add_action_buttons(true);
    }

}
?>