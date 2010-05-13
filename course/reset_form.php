<?php // $Id$
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class course_reset_form extends moodleform {
    function definition (){
        global $CFG, $COURSE;

        $mform =& $this->_form;

        $mform->addElement('header', 'generalheader', get_string('general'));

        $mform->addElement('date_selector', 'reset_start_date', get_string('startdate'), array('optional'=>true));
        $mform->setHelpButton('reset_start_date', array('coursestartdate', get_string('startdate')));
        $mform->addElement('checkbox', 'reset_events', get_string('deleteevents', 'calendar'));
        $mform->addElement('checkbox', 'reset_logs', get_string('deletelogs'));
        $mform->addElement('checkbox', 'reset_notes', get_string('deletenotes', 'notes'));


        $mform->addElement('header', 'rolesheader', get_string('roles'));

        $roles = get_assignable_roles(get_context_instance(CONTEXT_COURSE, $COURSE->id));
        $mform->addElement('select', 'reset_roles', get_string('unenrolroleusers'), $roles, array('multiple' => 'multiple'));
        $mform->addElement('checkbox', 'reset_roles_overrides', get_string('deletecourseoverrides', 'role'));
        $mform->setAdvanced('reset_roles_overrides');
        $mform->addElement('checkbox', 'reset_roles_local', get_string('deletelocalroles', 'role'));


        $mform->addElement('header', 'gradebookheader', get_string('gradebook', 'grades'));

        $mform->addElement('checkbox', 'reset_gradebook_items', get_string('removeallcourseitems', 'grades'));
        $mform->addElement('checkbox', 'reset_gradebook_grades', get_string('removeallcoursegrades', 'grades'));
        $mform->disabledIf('reset_gradebook_grades', 'reset_gradebook_items', 'checked');


        $mform->addElement('header', 'groupheader', get_string('groups'));

        $mform->addElement('checkbox', 'reset_groups_remove', get_string('deleteallgroups', 'group'));
        $mform->setAdvanced('reset_groups_remove');
        $mform->addElement('checkbox', 'reset_groups_members', get_string('removegroupsmembers', 'group'));
        $mform->setAdvanced('reset_groups_members');
        $mform->disabledIf('reset_groups_members', 'reset_groups_remove', 'checked');

        if (!empty($CFG->enablegroupings)) {
            $mform->addElement('checkbox', 'reset_groupings_remove', get_string('deleteallgroupings', 'group'));
            $mform->setAdvanced('reset_groupings_remove');
            $mform->addElement('checkbox', 'reset_groupings_members', get_string('removegroupingsmembers', 'group'));
            $mform->setAdvanced('reset_groupings_members');
            $mform->disabledIf('reset_groupings_members', 'reset_groupings_remove', 'checked');
        }

        $unsupported_mods = array();
        if ($allmods = get_records('modules') ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                if (!count_records($modname, 'course', $COURSE->id)) {
                    continue; // skip mods with no instances
                }
                $modfile = $CFG->dirroot."/mod/$modname/lib.php";
                $mod_reset_course_form_definition = $modname.'_reset_course_form_definition';
                $mod_reset__userdata = $modname.'_reset_userdata';
                if (file_exists($modfile)) {
                    include_once($modfile);
                    if (function_exists($mod_reset_course_form_definition)) {
                        $mod_reset_course_form_definition($mform);
                    } else if (!function_exists($mod_reset__userdata)) {
                        $unsupported_mods[] = $mod;
                    }
                } else {
                    debugging('Missing lib.php in '.$modname.' module');
                }
            }
        }
        // mention unsupported mods
        if (!empty($unsupported_mods)) {
            $mform->addElement('header', 'unsupportedheader', get_string('resetnotimplemented'));
            foreach($unsupported_mods as $mod) {
                $mform->addElement('static', 'unsup'.$mod->name, get_string('modulenameplural', $mod->name));
                $mform->setAdvanced('unsup'.$mod->name);
            }
        }

        $mform->addElement('hidden', 'id', $COURSE->id);
        $mform->setType('id', PARAM_INT);

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('resetcourse'));
        $buttonarray[] = &$mform->createElement('submit', 'selectdefault', get_string('selectdefault'));
        $buttonarray[] = &$mform->createElement('submit', 'deselectall', get_string('deselectall'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    function load_defaults() {
        global $CFG, $COURSE;

        $mform =& $this->_form;

        $defaults = array ('reset_events'=>1, 'reset_logs'=>1, 'reset_roles_local'=>1, 'reset_gradebook_grades'=>1, 'reset_notes'=>1);

        if (!empty($COURSE->defaultrole)) {
            $defaults['reset_roles'] = array($COURSE->defaultrole);
        } else {
            $defaults['reset_roles'] = array($CFG->defaultcourseroleid);
        }

        if ($allmods = get_records('modules') ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = $CFG->dirroot."/mod/$modname/lib.php";
                $mod_reset_course_form_defaults = $modname.'_reset_course_form_defaults';
                if (file_exists($modfile)) {
                    @include_once($modfile);
                    if (function_exists($mod_reset_course_form_defaults)) {
                        if ($moddefs = $mod_reset_course_form_defaults($COURSE)) {
                            $defaults = $defaults + $moddefs;
                        }
                    }
                }
            }
        }

        foreach ($defaults as $element=>$default) {
            $mform->setDefault($element, $default);
        }
    }
}
