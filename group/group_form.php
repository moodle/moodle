<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

/// get url variables
class group_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $strrequired = get_string('required');

        $mform =& $this->_form;

        $mform->addElement('text','name', get_string('groupname', 'group'),'maxlength="254" size="50"');
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('htmleditor', 'description', get_string('groupdescription', 'group'), array('rows'=> '15', 'course' => $COURSE->id, 'cols'=>'45'));
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('passwordunmask', 'enrolmentkey', get_string('enrolmentkey', 'group'), 'maxlength="254" size="24"', get_string('enrolmentkey'));
        $mform->setHelpButton('enrolmentkey', array('groupenrolmentkey', get_string('enrolmentkey', 'group')), true);
        $mform->setType('enrolmentkey', PARAM_RAW);

        $maxbytes = get_max_upload_file_size($CFG->maxbytes, $COURSE->maxbytes);

        if (!empty($CFG->gdversion) and $maxbytes) {
            $options = array(get_string('no'), get_string('yes'));
            $mform->addElement('select', 'hidepicture', get_string('hidepicture'), $options);

            $this->set_upload_manager(new upload_manager('imagefile', false, false, null, false, 0, true, true, false));
            $mform->addElement('file', 'imagefile', get_string('newpicture', 'group'));
            $mform->setHelpButton('imagefile', array ('picture', get_string('helppicture')), true);
        }

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden','courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true);
    }

    function definition_after_data() {
        global $USER, $CFG;
    }

    function validation($data) {
        global $COURSE;

        $errors = array();

        $name = $data['name'];
        if ($data['id'] and $group = get_record('groups', 'id', $data['id'])) {
            if ($group->name != stripslashes($name)) {
                if (groups_get_group_by_name($COURSE->id,  $name)) {
                    $errors['name'] = get_string('groupnameexists', 'group', stripslashes($name));
                }
            }

        } else {
            if (groups_get_group_by_name($COURSE->id, $name)) {
                $errors['name'] = get_string('groupnameexists', 'group', stripslashes($name));
            }
        }

        if (count($errors) > 0) {
            return $errors;
        } else {
            return true;
        }
    }

    function get_um() {
        return $this->_upload_manager;
    }
}

?>
