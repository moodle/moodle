<?php
/**
 * Create/Edit grouping form.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

/// get url variables
class grouping_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $mform =& $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];

        $mform->addElement('text','name', get_string('groupingname', 'group'),'maxlength="254" size="50"');
        $mform->addRule('name', get_string('required'), 'required', null, 'server');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('editor', 'description_editor', get_string('groupingdescription', 'group'), null, $editoroptions);
        $mform->setType('description_editor', PARAM_RAW);

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        global $COURSE, $DB;

        $errors = parent::validation($data, $files);

        $textlib = textlib_get_instance();

        $name = trim($data['name']);
        if ($data['id'] and $grouping = $DB->get_record('groupings', array('id'=>$data['id']))) {
            if ($textlib->strtolower($grouping->name) != $textlib->strtolower($name)) {
                if (groups_get_grouping_by_name($COURSE->id,  $name)) {
                    $errors['name'] = get_string('groupingnameexists', 'group', $name);
                }
            }

        } else if (groups_get_grouping_by_name($COURSE->id, $name)) {
            $errors['name'] = get_string('groupingnameexists', 'group', $name);
        }

        return $errors;
    }

}
