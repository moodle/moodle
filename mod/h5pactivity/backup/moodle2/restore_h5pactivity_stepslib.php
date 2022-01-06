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
 * All the steps to restore mod_h5pactivity are defined here.
 *
 * @package     mod_h5pactivity
 * @copyright   2020 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the structure step to restore one mod_h5pactivity activity.
 */
class restore_h5pactivity_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure(): array {
        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');
        $paths[] = new restore_path_element('h5pactivity', '/activity/h5pactivity');
        if ($userinfo) {
            $paths[] = new restore_path_element('h5pactivity_attempt', '/activity/h5pactivity/attempts/attempt');
            $paths[] = new restore_path_element('h5pactivity_attempt_result', '/activity/h5pactivity/attempts/attempt/results/result');
        }
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Processes the h5pactivity restore data.
     *
     * @param array $data Parsed element data.
     */
    protected function process_h5pactivity(array $data): void {
        global $DB;
        $data = (object)$data;
        $data->course = $this->get_courseid();
        // Insert the record.
        $newitemid = $DB->insert_record('h5pactivity', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Processes the h5pactivity_attempts restore data.
     *
     * @param array $data Parsed element data.
     */
    protected function process_h5pactivity_attempt(array $data): void {
        global $DB;
        $data = (object)$data;

        $oldid = $data->id;
        $data->h5pactivityid = $this->get_new_parentid('h5pactivity');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('h5pactivity_attempts', $data);
        $this->set_mapping('h5pactivity_attempt', $oldid, $newitemid);
    }

    /**
     * Processes the h5pactivity_attempts_results restore data.
     *
     * @param array $data Parsed element data.
     */
    protected function process_h5pactivity_attempt_result(array $data): void {
        global $DB;
        $data = (object)$data;

        $oldid = $data->id;
        $data->attemptid = $this->get_new_parentid('h5pactivity_attempt');

        $newitemid = $DB->insert_record('h5pactivity_attempts_results', $data);
        $this->set_mapping('h5pactivity_attempt_result', $oldid, $newitemid);
    }

    /**
     * Defines post-execution actions.
     */
    protected function after_execute(): void {
        // Add related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_h5pactivity', 'intro', null);
        $this->add_related_files('mod_h5pactivity', 'package', null);
    }
}
