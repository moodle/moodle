<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * All the steps to restore mod_subsection are defined here.
 *
 * @package     mod_subsection
 * @category    backup
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_subsection\manager;

// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Defines the structure step to restore one mod_subsection activity.
 */
class restore_subsection_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('subsection', '/activity/subsection');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the subsection element.
     *
     * @param \stdClass $data the data to be processed.
     */
    protected function process_subsection($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Insert the subsection record.
        $newitemid = $DB->insert_record('subsection', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
        $this->set_delegated_section_mapping(manager::PLUGINNAME, $oldid, $newitemid);
    }

    /**
     * Defines post-execution actions.
     */
    protected function after_execute() {
        return;
    }
}
