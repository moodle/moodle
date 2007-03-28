<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

class group_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $strrequired = get_string('required');

        $group       = $this->_customdata['group'];
        $groupingid  = $this->_customdata['groupingid'];
        $newgrouping = $this->_customdata['newgrouping'];

        $mform =& $this->_form;

        $mform->addElement('text','name', get_string('name'),'maxlength="254" size="50"');
        $mform->setDefault('name', get_string('defaultgroupname'));
        $mform->addRule('name', get_string('missingname'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('htmleditor', 'description', get_string('description'), array('rows'=> '5', 'cols'=>'45'));
        $mform->setType('description', PARAM_RAW);
        
        $mform->addElement('text', 'enrolmentkey', get_string('enrolmentkey', 'group'), 'maxlength="254" size="40"', get_string('enrolmentkey'));
        $mform->setHelpButton('enrolmentkey', array('groupenrolmentkey', get_string('enrolmentkey', 'group')), true);
        $mform->setType('enrolmentkey', PARAM_RAW);
        
        $options = array(get_string('no'), get_string('yes'));
        $mform->addElement('select', 'hidepicture', get_string('hidepicture'), $options);
        
        $maxbytes = get_max_upload_file_size($CFG->maxbytes, $COURSE->maxbytes);
        if (!empty($CFG->gdversion) and $maxbytes and !empty($CFG->disableuserimages)) {
            $mform->addElement('file', 'imagefile', get_string('newpicture', 'group')); 
            $mform->setHelpButton('imagefile', array('picture', get_string('helppicture')), true);
        }

        /// Add some extra hidden fields
        if ($group) {
            $mform->addElement('hidden', 'group', $group->id);
        }
        
        if ($groupingid) {
            $mform->addElement('hidden', 'groupingid', s($groupingid)); 
        } else {

        }

        $mform->addElement('hidden', 'group');
        $mform->addElement('hidden', 'course', $COURSE->id);

        $this->set_upload_manager(new upload_manager('imagefile', false, false, null, false, 0, true, true, false));

        $this->add_action_buttons(false, get_string('updatemyprofile'));
    }

    function definition_after_data() {
        global $USER, $CFG;

        $mform =& $this->_form;
        $userid = $mform->getElementValue('id');

        if ($user = get_record('user', 'id', $userid)) {

            // print picture
            if (!empty($CFG->gdversion)) {
                $image_el = $mform->getElement('currentpicture');
                if ($user and $user->picture) {
                    $image_el->setValue(print_user_picture($user->id, SITEID, $user->picture, 64,true,false,'',true));
                } else {
                    $image_el->setValue(get_string('none'));
                }
            }

            /// disable fields that are locked by auth plugins
            $fields = get_user_fieldnames();
            $freezefields = array();
            $authplugin = get_auth_plugin($user->auth);
            foreach ($fields as $field) {
                if (!$mform->elementExists($field)) {
                    continue;
                }
                $configvariable = 'field_lock_' . $field;
                if (isset($authplugin->config->{$configvariable})) {
                    if ($authplugin->config->{$configvariable} === 'locked') {
                        $freezefields[] = $field;
                    } else if ($authplugin->config->{$configvariable} === 'unlockedifempty' and $user->$field != '') {
                        $freezefields[] = $field;
                    }
                }
            }
            $mform->hardFreeze($freezefields);
        }
    }

    function validation ($usernew) {
        global $CFG;

        $usernew = (object)$usernew;
        $user    = get_record('user', 'id', $usernew->id);
        $err     = array();

        // validate email
        if (!validate_email($usernew->email)) {
            $err['email'] = get_string('invalidemail');
        } else if (($usernew->email !== $user->email) and record_exists('user', 'email', $usernew->email, 'mnethostid', $CFG->mnet_localhost_id)) {
            $err['email'] = get_string('emailexists');
        }

        if ($usernew->email === $user->email and over_bounce_threshold($user)) {
            $err['email'] = get_string('toomanybounces');
        }

        if (count($err) == 0){
            return true;
        } else {
            return $err;
        }
    }

    function get_um() {
        return $this->_upload_manager;
    }
}

?>
