<?php // $Id$

require_once($CFG->dirroot.'/lib/formslib.php');

/// get url variables
class autogroup_form extends moodleform {

    // Define the form
    function definition() {
        global $CFG, $COURSE;

        $mform =& $this->_form;

        $mform->addElement('header', 'autogroup', get_string('autocreategroups', 'group'));

        $options = array(0=>get_string('all'));
        $options += $this->_customdata['roles'];
        $mform->addElement('select', 'roleid', get_string('selectfromrole', 'group'), $options);
        if (!empty($COURSE->defaultrole) and array_key_exists($COURSE->defaultrole, $options)) {
            $mform->setDefault('roleid', $COURSE->defaultrole);
        } else if (!empty($CFG->defaultcourseroleid) and array_key_exists($CFG->defaultcourseroleid, $options)) {
            $mform->setDefault('roleid', $CFG->defaultcourseroleid);
        }

        $options = array('groups' => get_string('numgroups', 'group'),
                         'members' => get_string('nummembers', 'group'));
        $mform->addElement('select', 'groupby', get_string('groupby', 'group'), $options);

        $mform->addElement('text', 'number', get_string('number', 'group'),'maxlength="4" size="4"');
        $mform->setType('number', PARAM_INT);
        $mform->addRule('number', null, 'numeric', null, 'client');
        $mform->addRule('number', get_string('required'), 'required', null, 'client');

        $mform->addElement('checkbox', 'nosmallgroups', get_string('nosmallgroups', 'group'));
        $mform->disabledIf('nosmallgroups', 'groupby', 'noteq', 'members');
        $mform->setAdvanced('nosmallgroups');

        $options = array('no'        => get_string('noallocation', 'group'),
                         'random'    => get_string('random', 'group'),
                         'firstname' => get_string('byfirstname', 'group'),
                         'lastname'  => get_string('bylastname', 'group'),
                         'idnumber'  => get_string('byidnumber', 'group'));
        $mform->addElement('select', 'allocateby', get_string('allocateby', 'group'), $options);
        $mform->setDefault('allocateby', 'random');
        $mform->setAdvanced('allocateby');

        $mform->addElement('text', 'namingscheme', get_string('namingscheme', 'group'));
        $mform->setHelpButton('namingscheme', array(false, get_string('namingschemehelp', 'group'),
                false, true, false, get_string('namingschemehelp', 'group')));
        $mform->addRule('namingscheme', get_string('required'), 'required', null, 'client');
        $mform->setType('namingscheme', PARAM_MULTILANG);
        // there must not be duplicate group names in course
        $template = get_string('grouptemplate', 'group');
        $gname = groups_parse_name($template, 0);
        if (!groups_get_group_by_name($COURSE->id, $gname)) {
            $mform->setDefault('namingscheme', $template);
        }

        if (!empty($CFG->enablegroupings)) {
            $options = array('0' => get_string('no'),
                             '-1'=> get_string('newgrouping', 'group'));
            if ($groupings = groups_get_all_groupings($COURSE->id)) {
                foreach ($groupings as $grouping) {
                    $options[$grouping->id] = strip_tags(format_string($grouping->name));
                }
            }
            $mform->addElement('select', 'grouping', get_string('createingrouping', 'group'), $options);
            if ($groupings) {
                $mform->setDefault('grouping', '-1');
            }

            $mform->addElement('text', 'groupingname', get_string('groupingname', 'group'), $options);
            $mform->setType('groupingname', PARAM_MULTILANG);
            $mform->disabledIf('groupingname', 'grouping', 'noteq', '-1');
        }

        $mform->addElement('hidden','courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden','seed');
        $mform->setType('seed', PARAM_INT);

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'preview', get_string('preview'), 'xx');
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }


    function validation($data, $files) {
    	global $CFG, $COURSE;
        $errors = parent::validation($data, $files);

        if ($data['allocateby'] != 'no') {
            if (!$users = groups_get_potential_members($data['courseid'], $data['roleid'])) {
                $errors['roleid'] = get_string('nousersinrole', 'group');
            }

           /// Check the number entered is sane
            if ($data['groupby'] == 'groups') {
                $usercnt = count($users);

                if ($data['number'] > $usercnt || $data['number'] < 1) {
                	$errors['number'] = get_string('toomanygroups', 'group', $usercnt);
                }
            }
        }

        //try to detect group name duplicates
        $name = groups_parse_name(stripslashes(trim($data['namingscheme'])), 0);
        if (groups_get_group_by_name($COURSE->id, $name)) {
            $errors['namingscheme'] = get_string('groupnameexists', 'group', $name);
        }

        // check grouping name duplicates
        if ( isset($data['grouping']) && $data['grouping'] == '-1') {
            $name = trim(stripslashes($data['groupingname']));
            if (empty($name)) {
                $errors['groupingname'] = get_string('required');
            } else if (groups_get_grouping_by_name($COURSE->id, $name)) {
                $errors['groupingname'] = get_string('groupingnameexists', 'group', $name);
            }
        }

       /// Check the naming scheme
        $matchcnt = preg_match_all('/[#@]{1,1}/', $data['namingscheme'], $matches);

        if ($matchcnt != 1) {
            $errors['namingscheme'] = get_string('badnamingscheme', 'group');
        }

        return $errors;
    }
}

?>
