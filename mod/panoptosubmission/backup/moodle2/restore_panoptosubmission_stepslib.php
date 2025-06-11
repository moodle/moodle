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
 * Panopto Submission restore stepslib.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Panopto Submission restore stepslib structure step class implementation
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_panoptosubmission_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines and returns the structure object
     * @return object The structure object
     */
    protected function define_structure() {
        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');
        $paths[] = new restore_path_element('panoptosubmission', '/activity/panoptosubmission');

        if ($userinfo) {
            $paths[] = new restore_path_element(
                'panoptosubmission_submission',
                '/activity/panoptosubmission/submissions/submission'
            );
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Processes data from a Panopto submission for a restore operation
     *
     * @param object $data the submission data being processed
     */
    protected function process_panoptosubmission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('panoptosubmission', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Processes data from a Panopto submission for a restore operation
     *
     * @param object $data the submission data being processed
     */
    protected function process_panoptosubmission_submission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->panactivityid = $this->get_new_parentid('panoptosubmission');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);

        $newitemid = $DB->insert_record('panoptosubmission_submission', $data);
        $this->set_mapping('panoptosubmission_submission', $oldid, $newitemid);
    }

    /**
     * Runs after restore execution
     *
     */
    protected function after_execute() {
        $this->add_related_files('mod_panoptosubmission', 'submission', 'panoptosubmission_submission');
    }
}
