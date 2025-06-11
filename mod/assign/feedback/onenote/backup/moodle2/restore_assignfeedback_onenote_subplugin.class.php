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
 * This file contains the restore code for the feedback_file plugin.
 *
 * @package   assignfeedback_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc. (based on files by NetSpot {@link http://www.netspot.com.au})
 */

/**
 * Restore subplugin class.
 *
 * Provides the necessary information needed
 * to restore one assign_feedback subplugin.
 *
 * @package   assignfeedback_onenote
 */
class restore_assignfeedback_onenote_subplugin extends restore_subplugin {

    /**
     * Returns the paths to be handled by the subplugin at assignment level
     *
     * @return array
     */
    protected function define_grade_subplugin_structure() {

        $paths = [];

        $elename = $this->get_namefor('grade');
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/feedback_onenote');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths;
    }

    /**
     * Processes one feedback_onenote element
     *
     * @param mixed $data
     */
    public function process_assignfeedback_onenote_grade($data) {
        global $DB;

        $data = (object) $data;
        $data->assignment = $this->get_new_parentid('assign');
        $oldgradeid = $data->grade;
        // The mapping is set in the restore for the core assign activity
        // when a grade node is processed.
        $data->grade = $this->get_mappingid('grade', $data->grade);

        $DB->insert_record('assignfeedback_onenote', $data);

        $this->add_related_files('assignfeedback_onenote', 'feedback_files', 'grade', null, $oldgradeid);
    }
}
