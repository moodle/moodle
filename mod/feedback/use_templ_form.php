<?php
/**
* prints the form to confirm use template
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class mod_feedback_use_templ_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        //headline
        $mform->addElement('header', 'general', '');

        // visible elements
        $mform->addElement('radio', 'deleteolditems', '1)', get_string('delete_old_items', 'feedback'), 1);
        $mform->addElement('radio', 'deleteolditems', '2)', get_string('append_new_items', 'feedback'), 0);
        $mform->setType('deleteolditems', PARAM_INT);

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'templateid');
        $mform->setType('templateid', PARAM_INT);
        $mform->addElement('hidden', 'do_show');
        $mform->setType('do_show', PARAM_INT);
        $mform->addElement('hidden', 'confirmadd');
        $mform->setType('confirmadd', PARAM_INT);

        //-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();

    }
}

