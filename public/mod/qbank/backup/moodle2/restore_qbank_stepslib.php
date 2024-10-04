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
 * Defines the structure step to restore one mod_qbank activity.
 *
 * @package     mod_qbank
 * @copyright   2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author      Simon Adams <simon.adams@catalyst-eu.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qbank_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure(): array {
        $paths = [];
        $paths[] = new restore_path_element('qbank', '/activity/qbank');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Processes the qbank restore data.
     *
     * @param array $data Parsed element data.
     */
    protected function process_qbank(array $data): void {
        global $DB;

        $data['course'] = $this->get_courseid();

        // Insert the record.
        $newitemid = $DB->insert_record('qbank', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Defines post-execution actions.
     */
    protected function after_execute(): void {
        // Add qbank related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_qbank', 'intro', null);
    }
}
