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
 * Define all the restore steps that will be used by the restore_tincanlaunch_activity_task
 *
 * @package    mod_tincanlaunch
 * @copyright  2016 onward Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one tincanlaunch activity
 *
 * @package    mod_tincanlaunch
 * @copyright  2016 onward Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_tincanlaunch_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the activity to be restored.
     *
     * @return array Return the paths wrapped into standard activity structure.
     */
    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('tincanlaunch', '/activity/tincanlaunch');
        $paths[] = new restore_path_element('tincanlaunchlrs', '/activity/tincanlaunch/tincanlaunchlrs');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process tincanlaunch tag information
     * @param array $data information
     */
    protected function process_tincanlaunch($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();

        $newitemid = $DB->insert_record('tincanlaunch', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process overridden LRS details.
     *
     * @param array $data information
     */
    protected function process_tincanlaunchlrs($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->tincanlaunchid = $this->get_new_parentid('tincanlaunch');

        $newitemid = $DB->insert_record('tincanlaunch_lrs', $data);
        $this->set_mapping('tincanlaunch_lrs', $oldid, $newitemid);
    }

    /**
     * After restore process has finished, match files.
     *
     */
    protected function after_execute() {
        // Add tincanlaunch related files.
        $this->add_related_files('mod_tincanlaunch', 'intro', null);
        $this->add_related_files('mod_tincanlaunch', 'package', null);
        $this->add_related_files('mod_tincanlaunch', 'content', null);
    }
}
