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
 * Class for the structure used for restore BigBlueButtonBN.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

/**
 * Define all the restore steps that will be used by the restore_url_activity_task.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_bigbluebuttonbn_activity_structure_step extends restore_activity_structure_step {
    /**
     * Structure step to restore one bigbluebuttonbn activity.
     *
     * @return array
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('bigbluebuttonbn', '/activity/bigbluebuttonbn');
        $paths[] = new restore_path_element('bigbluebuttonbn_logs', '/activity/bigbluebuttonbn/logs/log');
        $paths[] = new restore_path_element('bigbluebuttonbn_recordings', '/activity/bigbluebuttonbn/recordings/recording');
        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process a bigbluebuttonbn restore.
     *
     * @param array $data The data in object form
     * @return void
     */
    protected function process_bigbluebuttonbn(array $data) {
        global $DB;
        $data = (object) $data;
        $data->course = $this->get_courseid();
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        // Insert the bigbluebuttonbn record.
        $newitemid = $DB->insert_record('bigbluebuttonbn', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process a bigbluebuttonbn_logs restore (additional table).
     *
     * @param array $data The data in object form
     * @return void
     */
    protected function process_bigbluebuttonbn_logs(array $data) {
        global $DB;
        $data = (object) $data;
        // Apply modifications.
        $data->courseid = $this->get_mappingid('course', $data->courseid);
        $data->bigbluebuttonbnid = $this->get_new_parentid('bigbluebuttonbn');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        // Insert the bigbluebuttonbn_logs record.
        $newitemid = $DB->insert_record('bigbluebuttonbn_logs', $data);
        // Immediately after inserting associated record, call this.
        $this->set_mapping('bigbluebuttonbn_logs', $data->id, $newitemid);
    }

    /**
     * Process a bigbluebuttonbn_recordings restore (additional table).
     *
     * @param array $data The data in object form
     * @return void
     */
    protected function process_bigbluebuttonbn_recordings(array $data) {
        global $DB;
        $data = (object) $data;
        // Apply modifications.
        $data->courseid = $this->get_mappingid('course', $data->courseid);
        $data->bigbluebuttonbnid = $this->get_new_parentid('bigbluebuttonbn');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        // Insert the bigbluebuttonbn_recordings record.
        $newitemid = $DB->insert_record('bigbluebuttonbn_recordings', $data);
        // Immediately after inserting associated record, call this.
        $this->set_mapping('bigbluebuttonbn_recordings', $data->id, $newitemid);
    }

    /**
     * Actions to be executed after the restore is completed
     *
     * @return void
     */
    protected function after_execute() {
        // Add bigbluebuttonbn related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_bigbluebuttonbn', 'intro', null);
    }
}
