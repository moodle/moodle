<?php
/**
* prints the form to confirm delete a completed
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class mod_feedback_delete_item_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        //headline
        //$mform->addElement('header', 'general', '');

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'deleteitem');
        $mform->setType('deleteitem', PARAM_INT);
        $mform->addElement('hidden', 'confirmdelete');
        $mform->setType('confirmdelete', PARAM_INT);

        //-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons(true, get_string('yes'));

    }
}

