<?php // $Id$

require_once($CFG->dirroot.'/lib/formslib.php');

/// get url variables
class autogroup_form extends moodleform {

    // Define the form
    function definition() {
        global $CFG, $COURSE;

        $mform =& $this->_form;

        $mform->addElement('header', 'autogroup', get_string('autocreategroups', 'group'));

        $options = array(get_string('all'));
        $options += $this->_customdata['roles'];
        $mform->addElement('select', 'roleid', get_string('selectfromrole', 'group'), $options);
        $mform->addRule('roleid', get_string('required'), 'required', null, 'client');

        
        $options = array('groups' => get_string('groups', 'group'),
                         'members' => get_string('members', 'group'));
        $mform->addElement('select', 'groupby', get_string('groupby', 'group'), $options);
        $mform->addRule('groupby', get_string('required'), 'required', null, 'client');
        
        $mform->addElement('text', 'number', get_string('number', 'group'),'maxlength="4" size="4"');
        $mform->setType('number', PARAM_INT);
        $mform->addRule('number', null, 'numeric', null, 'client');
        $mform->addRule('number', get_string('required'), 'required', null, 'client');
        
        $options = array('random' => get_string('random', 'group'),
                         'firstname' => get_string('firstname', 'group'),
                         'lastname' => get_string('lastname', 'group'));
                         
        $mform->addElement('select', 'allocateby', get_string('allocateby', 'group'), $options);
        $mform->addRule('allocateby', get_string('required'), 'required', null, 'client');
        
        $grp[] = $mform->createElement('text', 'namingscheme');
        $grp[] = $mform->createElement('static', 'namingschemehelp', null, get_string('namingschemehelp', 'group'));
        $mform->addGroup($grp, 'namingschemegrp', get_string('namingscheme', 'group'), '<br />');
        
        $mform->setType('namingschemegrp[namingscheme]', PARAM_RAW);
        $mform->setDefault('namingschemegrp[namingscheme]', get_string('group', 'group').' @');
        $mform->addRule('namingschemegrp', get_string('required'), 'required', null, 'client');
        $mform->setAdvanced('namingschemegrp');
        

        $mform->addElement('hidden','courseid');
        $mform->setType('courseid', PARAM_INT);
        
        $mform->addElement('hidden','seed');
        $mform->setType('seed', PARAM_INT);
        
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'preview', get_string('preview'), 'xx');
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }


    function validation($data) {
    	global $CFG, $COURSE;
        $errors = array();

        if (!$users = groups_get_potental_members($data['courseid'], $data['roleid'])) {         
            $errors['roleid'] = get_string('nousersinrole', 'group');
        }
        $usercnt = count($users);
        
       /// Check the number entered is sane     
        if ($data['groupby'] == 'groups') {

            if ($data['number'] > $usercnt || $data['number'] < 1) {
            	$errors['number'] = get_string('toomanygroups', 'group', $usercnt);
            }
        }
        
       /// Check the naming scheme 
        $matchcnt = preg_match_all('/[#@]{1,1}/', $data['namingschemegrp']['namingscheme'], $matches);
        
        if ($matchcnt != 1) {
            $errors['namingschemegrp'] = get_string('badnamingscheme', 'group');
        }
        
        
        if (count($errors) > 0) {
            return $errors;
        } else {
            return true;
        }
        
    }

}

?>
