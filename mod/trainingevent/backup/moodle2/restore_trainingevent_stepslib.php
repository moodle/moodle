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
 * @package   mod_trainingevent
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_url_activity_task
 */

/**
 * Structure step to restore one trainingevent activity
 */
class restore_trainingevent_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('trainingevent', '/activity/trainingevent');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    protected function process_trainingevent($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
echo "DATA = <pre>";
print_r($data);
echo "</pre></br>";
        $newtrainingevent = array('course' => $this->get_courseid(),
                                  'name' => $data->name,
                                  'intro' => $data->intro,
                                  'introformat' => $data->introformat,
                                  'timemodified' => $data->timemodified,
                                  'startdatetime' => $data->startdatetime,
                                  'enddatetime' => $data->enddatetime,
                                  'classroomid' => $data->classroomid,
                                  'approvaltype' => $data->approvaltype);
        $newtrainingeventid = $DB->insert_record('trainingevent', $newtrainingevent);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newtrainingeventid);

        // Insert the trainingevent record.
        // Iterate over all the feed elements, creating them if needed
        if (isset($data->trainingevent_user['trainingevent_users'])) {
            foreach ($data->trainingevent_user['trainingevent_users'] as $trainingeventuser) {
                $trainingeventuser = (object)$trainingeventuser;
                $trainingeventuser->trainingeventid = $newtrainingeventid;
                $catcourse_catid = $DB->insert_record('trainingevent_users', $trainingeventuser);
            }
        }
    }

    protected function after_execute() {
        // Add trainingevent related files, no need to match by itemname (just internally handled context).
        //$this->add_related_files('mod_trainingevent', 'intro', null);
    }

}
