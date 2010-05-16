<?php
/**
* prints the forms to choose an xml-template file to import items
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class feedback_import_form extends moodleform {
    function definition() {
        global $CFG;
        $mform =& $this->_form;

        //headline
        $mform->addElement('header', 'general', '');
        $strdeleteolditmes = get_string('delete_old_items', 'feedback').' ('.get_string('oldvalueswillbedeleted','feedback').')';
        $strnodeleteolditmes = get_string('append_new_items', 'feedback').' ('.get_string('oldvaluespreserved','feedback').')';
        
        $deleteolditemsarray = array();
        $mform->addElement('radio', 'deleteolditems', '', $strdeleteolditmes, true);
        $mform->addElement('radio', 'deleteolditems', '', $strnodeleteolditmes);
        $mform->addGroup($deleteolditemsarray, 'deleteolditemsarray', '', array(''), false);

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('filepicker', 'choosefile', get_string('file'), null, array('maxbytes' => $CFG->maxbytes, 'filetypes' => '*'));

        //-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons(true, get_string('yes'));

    }
}
