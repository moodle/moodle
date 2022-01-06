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
 * Switch roles form.
 *
 * @package     core_course
 * @copyright   2016 Damyon Wiese
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Defines the course completion settings form.
 */
class switchrole_form extends moodleform {

    /**
     * Determine whether the user is assuming another role
     *
     * This function checks to see if the user is assuming another role by means of
     * role switching. In doing this we compare each RSW key (context path) against
     * the current context path. This ensures that we can provide the switching
     * options against both the course and any page shown under the course.
     *
     * @param context $context
     * @return bool|int The role(int) if the user is in another role, false otherwise
     */
    protected function in_alternative_role($context) {
        global $USER, $PAGE;
        if (!empty($USER->access['rsw']) && is_array($USER->access['rsw'])) {
            if (!empty($PAGE->context) && !empty($USER->access['rsw'][$PAGE->context->path])) {
                return $USER->access['rsw'][$PAGE->context->path];
            }
            foreach ($USER->access['rsw'] as $key=>$role) {
                if (strpos($context->path, $key)===0) {
                    return $role;
                }
            }
        }
        return false;
    }

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $USER, $CFG, $DB;

        $mform = $this->_form;
        $course = $this->_customdata['course'];

        // Overall criteria aggregation.
        $context = context_course::instance($course->id);
        $roles = array();
        $assumedrole = -1;
        if (is_role_switched($course->id)) {
            $roles[0] = get_string('switchrolereturn');
            $assumedrole = $USER->access['rsw'][$context->path];
        }
        $availableroles = get_switchable_roles($context);
        if (is_array($availableroles)) {
            foreach ($availableroles as $key=>$role) {
                if ($assumedrole == (int)$key) {
                    continue;
                }
                $roles[$key] = $role;
            }
        }
        $mform->addElement('select', 'switchrole', get_string('role'), $roles);

        // Add common action buttons.
        $this->add_action_buttons();

        // Add hidden fields.
        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);
    }
}
