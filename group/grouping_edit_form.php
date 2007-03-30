<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

/// get url variables
class grouping_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $strrequired = get_string('required');
        $buttonstr   = get_string('creategrouping', 'group');
        
        if (!empty($this->_customdata['grouping'])) {
            $grouping = $this->_customdata['grouping'];
            $id = $grouping->id;
        }
        
        $courseid = $this->_customdata['courseid'];
       
        $mform =& $this->_form;
        
        $mform->addElement('text','name', get_string('groupingname', 'group'),'maxlength="254" size="50"');
        $mform->setDefault('name', get_string('defaultgroupingname', 'group'));
        $mform->addRule('name', get_string('missingname'), 'required', null, 'server');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('htmleditor', 'description', get_string('groupdescription', 'group'), array('rows'=> '15', 'course' => $courseid, 'cols'=>'45'));
        $mform->setType('description', PARAM_RAW);
        
        if (!empty($id)) {
            $buttonstr = get_string('save', 'group');
            $mform->addElement('hidden','id', null);
            $mform->setType('id', PARAM_INT);
        }
        $this->add_action_buttons(true, $buttonstr);
        $mform->addElement('hidden', 'courseid', $courseid);
    }
}
?>
