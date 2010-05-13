<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_label_mod_form extends moodleform_mod {

    function definition() {

        $mform    =& $this->_form;

        $this->add_intro_editor(true, get_string('labeltext', 'label'));

        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons(true, false, null);

    }

}
