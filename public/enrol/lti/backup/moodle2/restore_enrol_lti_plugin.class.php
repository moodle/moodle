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
 * Defines the restore_enrol_lti_plugin class.
 *
 * @package   enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define all the restore steps.
 *
 * @package   enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_enrol_lti_plugin extends restore_enrol_plugin {

    /**
     * @var array $tools Stores the IDs of the newly created tools.
     */
    protected $tools = array();

    /**
     * Declares the enrol LTI XML paths attached to the enrol element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_enrol_plugin_structure() {

        $paths = array();
        $paths[] = new restore_path_element('enrol_lti_tool', $this->connectionpoint->get_path() . '/tool');
        $paths[] = new restore_path_element('enrol_lti_users', $this->connectionpoint->get_path() . '/tool/users/user');

        return $paths;
    }

    /**
     * Processes LTI tools element data
     *
     * @param array|stdClass $data
     */
    public function process_enrol_lti_tool($data) {
        global $DB;

        $data = (object) $data;

        // Store the old id.
        $oldid = $data->id;

        // Change the values before we insert it.
        $data->timecreated = time();
        $data->timemodified = $data->timecreated;

        // Set the correct legacy ltiversion when restoring old tools.
        if (empty($data->ltiversion)) {
            $data->ltiversion = 'LTI-1p0/LTI-2p0';
        }

        // Generate a new uuid for LTI Advantage restores.
        if ($data->ltiversion == 'LTI-1p3') {
            $data->uuid = \core\uuid::generate();
        }

        // Now we can insert the new record.
        $data->id = $DB->insert_record('enrol_lti_tools', $data);

        // Add the array of tools we need to process later.
        $this->tools[$data->id] = $data;

        // Set up the mapping.
        $this->set_mapping('enrol_lti_tool', $oldid, $data->id);
    }

    /**
     * Processes LTI users element data
     *
     * @param array|stdClass $data The data to insert as a comment
     */
    public function process_enrol_lti_users($data) {
        global $DB;

        $data = (object) $data;

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->toolid = $this->get_mappingid('enrol_lti_tool', $data->toolid);
        $data->timecreated = time();

        $DB->insert_record('enrol_lti_users', $data);
    }

    /**
     * This function is executed after all the tasks in the plan have been finished.
     * This must be done here because the activities have not been restored yet.
     */
    public function after_restore_enrol() {
        global $DB;

        // Need to go through and change the values.
        foreach ($this->tools as $tool) {
            $updatetool = new stdClass();
            $updatetool->id = $tool->id;
            $updatetool->enrolid = $this->get_mappingid('enrol', $tool->enrolid);
            $updatetool->contextid = $this->get_mappingid('context', $tool->contextid);
            $DB->update_record('enrol_lti_tools', $updatetool);
        }
    }
}
