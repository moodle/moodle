<?php

/**
 * Create//edit group form.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');

/// get url variables
class group_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $mform =& $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];

        $mform->addElement('text','name', get_string('groupname', 'group'),'maxlength="254" size="50"');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('editor', 'description_editor', get_string('groupdescription', 'group'), null, $editoroptions);
        $mform->setType('description_editor', PARAM_RAW);

        $mform->addElement('passwordunmask', 'enrolmentkey', get_string('enrolmentkey', 'group'), 'maxlength="254" size="24"', get_string('enrolmentkey', 'group'));
        $mform->addHelpButton('enrolmentkey', 'enrolmentkey', 'group');
        $mform->setType('enrolmentkey', PARAM_RAW);

        if (!empty($CFG->gdversion)) {
            $options = array(get_string('no'), get_string('yes'));
            $mform->addElement('select', 'hidepicture', get_string('hidepicture'), $options);

            $mform->addElement('filepicker', 'imagefile', get_string('newpicture', 'group'));
            $mform->addHelpButton('imagefile', 'newpicture', 'group');
        }

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden','courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        global $COURSE, $DB, $CFG;

        $errors = parent::validation($data, $files);

        $textlib = textlib_get_instance();

        $name = trim($data['name']);
        if ($data['id'] and $group = $DB->get_record('groups', array('id'=>$data['id']))) {
            if ($textlib->strtolower($group->name) != $textlib->strtolower($name)) {
                if (groups_get_group_by_name($COURSE->id,  $name)) {
                    $errors['name'] = get_string('groupnameexists', 'group', $name);
                }
            }

            if (!empty($CFG->groupenrolmentkeypolicy) and $data['enrolmentkey'] != '' and $group->enrolmentkey !== $data['enrolmentkey']) {
                // enforce password policy only if changing password
                $errmsg = '';
                if (!check_password_policy($data['enrolmentkey'], $errmsg)) {
                    $errors['enrolmentkey'] = $errmsg;
                }
            }

        } else if (groups_get_group_by_name($COURSE->id, $name)) {
            $errors['name'] = get_string('groupnameexists', 'group', $name);
        }

        return $errors;
    }

    function get_editor_options() {
        return $this->_customdata['editoroptions'];
    }
}
