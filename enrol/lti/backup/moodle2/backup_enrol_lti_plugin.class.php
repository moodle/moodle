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
 * Defines the backup_enrol_lti_plugin class.
 *
 * @package   enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define all the backup steps.
 *
 * @package   enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_enrol_lti_plugin extends backup_enrol_plugin {

    /**
     * Defines the other LTI enrolment structures to append.
     *
     * @return backup_plugin_element
     */
    public function define_enrol_plugin_structure() {
        // Get the parent we will be adding these elements to.
        $plugin = $this->get_plugin_element();

        // Define our elements.
        $tool = new backup_nested_element('tool', array('id'), array(
            'enrolid', 'contextid', 'institution', 'lang', 'timezone', 'maxenrolled', 'maildisplay', 'city',
            'country', 'gradesync', 'gradesynccompletion', 'membersync', 'membersyncmode',  'roleinstructor',
            'rolelearner', 'secret', 'ltiversion', 'timecreated', 'timemodified'));

        $users = new backup_nested_element('users');

        $user = new backup_nested_element('user', array('id'), array(
            'userid', 'toolid', 'serviceurl', 'sourceid', 'consumerkey', 'consumersecret', 'membershipurl',
            'membershipsid'));

        // Build elements hierarchy.
        $plugin->add_child($tool);
        $tool->add_child($users);
        $users->add_child($user);

        // Set sources to populate the data.
        $tool->set_source_table('enrol_lti_tools',
            array('enrolid' => backup::VAR_PARENTID));

        // Users are only added only if users included.
        if ($this->task->get_setting_value('users')) {
            $user->set_source_table('enrol_lti_users', array('toolid' => backup::VAR_PARENTID));
        }
    }
}
