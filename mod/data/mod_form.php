<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_data_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB;

        $mform =& $this->_form;

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements(get_string('intro', 'data'));

        // ----------------------------------------------------------------------
        $mform->addElement('header', 'entrieshdr', get_string('entries', 'data'));

        $mform->addElement('selectyesno', 'approval', get_string('requireapproval', 'data'));
        $mform->addHelpButton('approval', 'requireapproval', 'data');

        $mform->addElement('selectyesno', 'manageapproved', get_string('manageapproved', 'data'));
        $mform->addHelpButton('manageapproved', 'manageapproved', 'data');
        $mform->setDefault('manageapproved', 1);
        $mform->disabledIf('manageapproved', 'approval', 'eq', 0);

        $mform->addElement('selectyesno', 'comments', get_string('allowcomments', 'data'));

        $countoptions = array(0=>get_string('none'))+
                        (array_combine(range(1, DATA_MAX_ENTRIES), // Keys.
                                        range(1, DATA_MAX_ENTRIES))); // Values.
        $mform->addElement('select', 'requiredentries', get_string('requiredentries', 'data'), $countoptions);
        $mform->addHelpButton('requiredentries', 'requiredentries', 'data');

        $mform->addElement('select', 'requiredentriestoview', get_string('requiredentriestoview', 'data'), $countoptions);
        $mform->addHelpButton('requiredentriestoview', 'requiredentriestoview', 'data');

        $mform->addElement('select', 'maxentries', get_string('maxentries', 'data'), $countoptions);
        $mform->addHelpButton('maxentries', 'maxentries', 'data');

        // ----------------------------------------------------------------------
        $mform->addElement('header', 'availibilityhdr', get_string('availability'));

        $mform->addElement('date_time_selector', 'timeavailablefrom', get_string('availablefromdate', 'data'),
                           array('optional' => true));

        $mform->addElement('date_time_selector', 'timeavailableto', get_string('availabletodate', 'data'),
                           array('optional' => true));

        $mform->addElement('date_time_selector', 'timeviewfrom', get_string('viewfromdate', 'data'),
                           array('optional' => true));

        $mform->addElement('date_time_selector', 'timeviewto', get_string('viewtodate', 'data'),
                           array('optional' => true));

        // ----------------------------------------------------------------------
        if ($CFG->enablerssfeeds && $CFG->data_enablerssfeeds) {
            $mform->addElement('header', 'rsshdr', get_string('rss'));
            $mform->addElement('select', 'rssarticles', get_string('numberrssarticles', 'data') , $countoptions);
        }

        $this->standard_grading_coursemodule_elements();

        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values){
        parent::data_preprocessing($default_values);
    }

}

