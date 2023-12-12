<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_survey_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB;

        $mform =& $this->_form;

        $strrequired = get_string('required');

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        if (!$options = $DB->get_records_menu("survey", array("template"=>0), "name", "id, name")) {
            throw new \moodle_exception('cannotfindsurveytmpt', 'survey');
        }

        foreach ($options as $id => $name) {
            $options[$id] = get_string($name, "survey");
        }
        $options = array(''=>get_string('choose').'...') + $options;
        $mform->addElement('select', 'template', get_string("surveytype", "survey"), $options);
        $mform->addRule('template', $strrequired, 'required', null, 'client');
        $mform->addHelpButton('template', 'surveytype', 'survey');

        $this->standard_intro_elements(get_string('customintro', 'survey'));

        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        if (!empty($data->completionunlocked)) {
            // Turn off completion settings if the checkboxes aren't ticked.
            $suffix = $this->get_suffix();
            $completion = $data->{'completion' . $suffix};
            $autocompletion = !empty($completion) && $completion == COMPLETION_TRACKING_AUTOMATIC;
            if (!$autocompletion || empty($data->{'completionsubmit' . $suffix})) {
                $data->{'completionsubmit' . $suffix} = 0;
            }
        }
    }

    /**
     * Add completion rules to form.
     * @return array
     */
    public function add_completion_rules() {
        $mform =& $this->_form;
        $suffix = $this->get_suffix();
        $completionsubmitel = 'completionsubmit' . $suffix;
        $mform->addElement('checkbox', $completionsubmitel, '', get_string('completionsubmit', 'survey'));
        // Enable this completion rule by default.
        $mform->setDefault($completionsubmitel, 1);
        return [$completionsubmitel];
    }

    /**
     * Enable completion rules
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        $suffix = $this->get_suffix();
        return !empty($data['completionsubmit' . $suffix]);
    }
}
