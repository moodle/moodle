<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

/// get url variables
class group_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $strrequired = get_string('required');
        $buttonstr   = get_string('creategroup', 'group');
        
        if (isset($this->_customdata['group'])) {
            $group = $this->_customdata['group'];
        } else {
            $group = false;
        }

        $groupingid  = $this->_customdata['groupingid'];
        $newgrouping = $this->_customdata['newgrouping'];
        $courseid    = $this->_customdata['courseid'];
       

        $id = $group->id;
        $mform =& $this->_form;
        
        $mform->addElement('text','name', get_string('groupname', 'group'),'maxlength="254" size="50"');
        $mform->setDefault('name', get_string('defaultgroupname', 'group'));
        $mform->addRule('name', get_string('missingname'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('htmleditor', 'description', get_string('groupdescription', 'group'), array('rows'=> '5', 'cols'=>'45'));
        $mform->setType('description', PARAM_RAW);
        
        $mform->addElement('text', 'enrolmentkey', get_string('enrolmentkey', 'group'), 'maxlength="254" size="24"', get_string('enrolmentkey'));
        $mform->setHelpButton('enrolmentkey', array('groupenrolmentkey', get_string('enrolmentkey', 'group')), true);
        $mform->setType('enrolmentkey', PARAM_RAW);
        
        $options = array(get_string('no'), get_string('yes'));
        $mform->addElement('select', 'hidepicture', get_string('hidepicture'), $options);
        
        $maxbytes = get_max_upload_file_size($CFG->maxbytes, $COURSE->maxbytes);
        
        if (!empty($CFG->gdversion) and $maxbytes) {
            $this->set_upload_manager(new upload_manager('imagefile', false, false, null, false, 0, true, true, false));
            $mform->addElement('file', 'imagefile', get_string('newpicture', 'group'));
            $mform->setHelpButton('imagefile', array ('picture', get_string('helppicture')), true);
        }

        
        if ($group) {
            $buttonstr = get_string('save', 'group');
            $mform->addElement('hidden','id', null);
            $mform->setType('id', PARAM_INT);
        }

        $this->add_action_buttons(true, $buttonstr);
        $mform->addElement('hidden', 'courseid', $courseid);
    }

    function definition_after_data() {
        global $USER, $CFG;
    }

    function get_um() {
        return $this->_upload_manager;
    }
}

?>
