<?php
class question_dataset_dependent_definitions_form extends moodleform {
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
    function question_dataset_dependent_definitions_form($submiturl, $question){
        global $QTYPES;
        $this->question = $question;
        $this->qtypeobj =& $QTYPES[$this->question->qtype];
        parent::moodleform($submiturl);
    }
    function definition() {
        $mform =& $this->_form;

        $possibledatasets = $this->qtypeobj->find_dataset_names($this->question->questiontext);
        $mandatorydatasets = array();
        foreach ($this->question->answers as $answer) {
            $mandatorydatasets += $this->qtypeobj->find_dataset_names($answer);
        }
        $key = 0;
        $datasetmenus = array();
        foreach ($mandatorydatasets as $datasetname) {
            if (!isset($datasetmenus[$datasetname])) {
                list($options, $selected) =
                        $this->qtypeobj->dataset_options($this->question, $datasetname);
                unset($options['0']); // Mandatory...
                $label = get_string("wildcard", "quiz"). " <strong>$datasetname</strong> ". get_string("substitutedby", "quiz");
                $mform->addElement('select', "dataset[$key]", $label, $options);
                $mform->setDefault("dataset[$key]", $selected);
                $datasetmenus[$datasetname]='';
                $key++;
            }
        }
        foreach ($possibledatasets as $datasetname) {
            if (!isset($datasetmenus[$datasetname])) {
                list($options, $selected) =
                        $this->qtypeobj->dataset_options($this->question, $datasetname);
                $label = get_string("wildcard", "quiz"). " <strong>$datasetname</strong> ". get_string("substitutedby", "quiz");
                $mform->addElement('select', "dataset[$key]", $label, $options);
                $mform->setDefault("dataset[$key]", $selected);
                $datasetmenus[$datasetname]='';
                $key++;
            }
        }
        //hidden elements
        $mform->addElement('hidden', 'wizardpage', 'datasetdefinitions');
        $mform->setType('wizardpage', PARAM_ALPHA);
        $this->add_action_buttons(true, get_string('nextpage', 'qtype_calculated'));
    }

}
?>