<?php
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
 * Kaltura video presentation stepslib file.
 *
 * @package    mod_kalvidpres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

/**
 * Define all the restore steps that will be used by the restore_kalvidpres_activity_task
 */

/**
 * Structure step to restore one kalvidpres activity
 */
class restore_kalvidpres_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('kalvidpres', '/activity/kalvidpres');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_kalvidpres($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the kalvidpres record
        $newitemid = $DB->insert_record('kalvidpres', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add kalvidpres related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_kalvidpres', 'intro', null);
    }
}
