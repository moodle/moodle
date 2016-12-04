<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides the course_reset_form class.
 *
 * @package     core
 * @copyright   2007 Petr Skoda
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/formslib.php';
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Defines the course reset settings form.
 *
 * @copyright   2007 Petr Skoda
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_reset_form extends moodleform {
    function definition (){
        global $CFG, $COURSE, $DB;

        $mform =& $this->_form;

        $mform->addElement('header', 'generalheader', get_string('general'));

        $mform->addElement('date_selector', 'reset_start_date', get_string('startdate'), array('optional'=>true));
        $mform->addHelpButton('reset_start_date', 'startdate');
        $mform->addElement('date_selector', 'reset_end_date', get_string('enddate'), array('optional' => true));
        $mform->addHelpButton('reset_end_date', 'enddate');
        $mform->addElement('checkbox', 'reset_events', get_string('deleteevents', 'calendar'));
        $mform->addElement('checkbox', 'reset_notes', get_string('deletenotes', 'notes'));
        $mform->addElement('checkbox', 'reset_comments', get_string('deleteallcomments', 'moodle'));
        $mform->addElement('checkbox', 'reset_completion', get_string('deletecompletiondata', 'completion'));
        $mform->addElement('checkbox', 'delete_blog_associations', get_string('deleteblogassociations', 'blog'));
        $mform->addHelpButton('delete_blog_associations', 'deleteblogassociations', 'blog');
        $mform->addElement('checkbox', 'reset_competency_ratings', get_string('deletecompetencyratings', 'core_competency'));

        $mform->addElement('header', 'rolesheader', get_string('roles'));

        $roles = get_assignable_roles(context_course::instance($COURSE->id));
        $roles[0] = get_string('noroles', 'role');
        $roles = array_reverse($roles, true);

        $mform->addElement('select', 'unenrol_users', get_string('unenrolroleusers', 'enrol'), $roles, array('multiple' => 'multiple'));
        $mform->addElement('checkbox', 'reset_roles_overrides', get_string('deletecourseoverrides', 'role'));
        $mform->setAdvanced('reset_roles_overrides');
        $mform->addElement('checkbox', 'reset_roles_local', get_string('deletelocalroles', 'role'));


        $mform->addElement('header', 'gradebookheader', get_string('gradebook', 'grades'));

        $mform->addElement('checkbox', 'reset_gradebook_items', get_string('removeallcourseitems', 'grades'));
        $mform->addHelpButton('reset_gradebook_items', 'removeallcourseitems', 'grades');
        $mform->addElement('checkbox', 'reset_gradebook_grades', get_string('removeallcoursegrades', 'grades'));
        $mform->addHelpButton('reset_gradebook_grades', 'removeallcoursegrades', 'grades');
        $mform->disabledIf('reset_gradebook_grades', 'reset_gradebook_items', 'checked');


        $mform->addElement('header', 'groupheader', get_string('groups'));

        $mform->addElement('checkbox', 'reset_groups_remove', get_string('deleteallgroups', 'group'));
        $mform->setAdvanced('reset_groups_remove');
        $mform->addElement('checkbox', 'reset_groups_members', get_string('removegroupsmembers', 'group'));
        $mform->setAdvanced('reset_groups_members');
        $mform->disabledIf('reset_groups_members', 'reset_groups_remove', 'checked');

        $mform->addElement('checkbox', 'reset_groupings_remove', get_string('deleteallgroupings', 'group'));
        $mform->setAdvanced('reset_groupings_remove');
        $mform->addElement('checkbox', 'reset_groupings_members', get_string('removegroupingsmembers', 'group'));
        $mform->setAdvanced('reset_groupings_members');
        $mform->disabledIf('reset_groupings_members', 'reset_groupings_remove', 'checked');

        $unsupported_mods = array();
        if ($allmods = $DB->get_records('modules') ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = $CFG->dirroot."/mod/$modname/lib.php";
                $mod_reset_course_form_definition = $modname.'_reset_course_form_definition';
                $mod_reset__userdata = $modname.'_reset_userdata';
                if (file_exists($modfile)) {
                    if (!$DB->count_records($modname, array('course'=>$COURSE->id))) {
                        continue; // Skip mods with no instances
                    }
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
        global $CFG, $COURSE, $DB;

        $mform =& $this->_form;

        $defaults = array ('reset_events'=>1, 'reset_roles_local'=>1, 'reset_gradebook_grades'=>1, 'reset_notes'=>1);

        // Set student as default in unenrol user list, if role with student archetype exist.
        if ($studentrole = get_archetype_roles('student')) {
            $defaults['unenrol_users'] = array_keys($studentrole);
        }

        if ($allmods = $DB->get_records('modules') ) {
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

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    public function validation($data, $files) {
        global $DB;

        $course = get_course($data['id']);

        $errors = parent::validation($data, $files);

        // We check the values that would be used as start and end.
        if ($data['reset_start_date'] != 0) {
            $coursedata['startdate'] = $data['reset_start_date'];
        } else {
            $coursedata['startdate'] = $course->startdate;
        }

        if ($data['reset_end_date'] != 0) {
            // End date set by the user has preference.
            $coursedata['enddate'] = $data['reset_end_date'];
        } else if ($data['reset_start_date'] > 0 && $course->enddate != 0) {
            // Otherwise, if the current course enddate is set, reset_course_userdata will add the start date time shift to it.
            $timeshift = $data['reset_start_date'] - usergetmidnight($course->startdate);
            $coursedata['enddate'] = $course->enddate + $timeshift;
        } else {
            $coursedata['enddate'] = $course->enddate;
        }

        if ($errorcode = course_validate_dates($coursedata)) {
            $errors['reset_end_date'] = get_string($errorcode, 'error');
        }

        return $errors;
    }

}
