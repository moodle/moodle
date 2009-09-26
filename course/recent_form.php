<?php //$$

require_once($CFG->libdir.'/formslib.php');

class recent_form extends moodleform {
    function definition() {
        global $CFG, $COURSE, $USER;

        $mform =& $this->_form;
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $modinfo = get_fast_modinfo($COURSE);

        $mform->addElement('header', 'filters', get_string('managefilters')); //TODO: add better string

        if ($COURSE->id == SITEID) {
            $viewparticipants = has_capability('moodle/site:viewparticipants', get_context_instance(CONTEXT_SYSTEM));
        } else {
            $viewparticipants = has_capability('moodle/course:viewparticipants', $context);
        }

        $viewfullnames = has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $COURSE->id));

        if ($viewparticipants) {
            $options = array();
            $options[0] = get_string('allparticipants');
            if ($guest = get_guest()) {
                $options[$guest->id] = fullname($guest);
            }

            if (groups_get_course_groupmode($COURSE) == SEPARATEGROUPS) {
                $groups = groups_get_user_groups($COURSE->id);
                $groups = $groups[0];
            } else {
                $groups = '';
            }
            
            if ($courseusers = get_users_by_capability($context, 'moodle/course:view', 'u.id, u.firstname, u.lastname', 'lastname ASC, firstname DESC', '', '', $groups)) {
                foreach ($courseusers as $courseuser) {
                    $options[$courseuser->id] = fullname($courseuser, $viewfullnames);
                }
            }
            $mform->addElement('select', 'user', get_string('participants'), $options);
            $mform->setAdvanced('user');
        }

        switch ($COURSE->format) {
            case 'weeks':  $sectiontitle = get_string('week'); break;
            case 'topics': $sectiontitle = get_string('topic'); break;
            default: $sectiontitle = get_string('section'); break;
        }

        $options = array(''=>get_string('allactivities'));
        $modsused = array();

        foreach($modinfo->cms as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            $modsused[$cm->modname] = true;
        }

        foreach ($modsused as $modname=>$unused) {
            $libfile = "$CFG->dirroot/mod/$modname/lib.php";
            if (!file_exists($libfile)) {
                unset($modsused[$modname]);
                continue;
            }
            include_once($libfile);
            $libfunction = $modname."_get_recent_mod_activity";
            if (!function_exists($libfunction)) {
                unset($modsused[$modname]);
                continue;
            }
            $options["mod/$modname"] = get_string('allmods', '', get_string('modulenameplural', $modname));
        }

        foreach ($modinfo->sections as $section=>$cmids) {
            $options["section/$section"] = "-- $sectiontitle $section --";
            foreach ($cmids as $cmid) {
                $cm = $modinfo->cms[$cmid];
                if (empty($modsused[$cm->modname]) or !$cm->uservisible) {
                    continue;
                }
                $options[$cm->id] = format_string($cm->name);
            }
        }
        $mform->addElement('select', 'modid', get_string('activities'), $options);
        $mform->setAdvanced('modid');


        if (has_capability('moodle/site:accessallgroups', $context)) {
            if ($groups = groups_get_all_groups($COURSE->id)) {
                $options = array('0'=>get_string('allgroups'));
                foreach($groups as $group) {
                    $options[$group->id] = format_string($group->name);
                }
                $mform->addElement('select', 'group', get_string('groups'), $options);
                $mform->setAdvanced('group');
            }
        } else {
            $mform->addElement('hidden','group');
            $mform->setType('group', PARAM_INT);
            $mform->setConstants(array('group'=>0));
        }

        $options = array('default'  => get_string('bycourseorder'),
                         'dateasc'  => get_string('datemostrecentlast'),
                         'datedesc' => get_string('datemostrecentfirst'));
        $mform->addElement('select', 'sortby', get_string('sortby'), $options);
        $mform->setAdvanced('sortby');

        $mform->addElement('date_time_selector', 'date', get_string('since'), array('optional'=>true));

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(false, get_string('showrecent'));
    }
}
