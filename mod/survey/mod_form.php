<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_survey_mod_form extends moodleform_mod {

    function definition() {

        global $CFG;
        $mform =& $this->_form;

        $strrequired = get_string('required');

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        if (!$options = get_records_menu("survey", "template", 0, "name", "id, name")) {
            error('No survey templates found!');
        }

        foreach ($options as $id => $name) {
            $options[$id] = get_string($name, "survey");
        }
        $options = array(''=>get_string('choose').'...') + $options;
        $mform->addElement('select', 'template', get_string("surveytype", "survey"), $options);
        $mform->addRule('template', $strrequired, 'required', null, 'client');
        $mform->setHelpButton('template', array('surveys', get_string('helpsurveys', 'survey')));


        $mform->addElement('textarea', 'intro', get_string('customintro', 'survey'), 'wrap="virtual" rows="20" cols="75"');
        $mform->setType('intro', PARAM_RAW);

        $features = new stdClass;
        $features->groups = true;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }


}
?>