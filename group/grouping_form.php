<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

/// get url variables
class grouping_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $mform =& $this->_form;

        $mform->addElement('text','name', get_string('groupingname', 'group'),'maxlength="254" size="50"');
        $mform->addRule('name', get_string('required'), 'required', null, 'server');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('htmleditor', 'description', get_string('groupingdescription', 'group'), array('rows'=> '15', 'course' => $COURSE->id, 'cols'=>'45'));
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        global $COURSE;

        $errors = parent::validation($data, $files);

        $textlib = textlib_get_instance();

        $name = trim(stripslashes($data['name']));
        if ($data['id'] and $grouping = get_record('groupings', 'id', $data['id'])) {
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
?>
