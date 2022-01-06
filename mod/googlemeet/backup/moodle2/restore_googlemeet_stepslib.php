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
 * All the steps to restore mod_googlemeet are defined here.
 *
 * @package     mod_googlemeet
 * @subpackage  backup-moodle2
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the structure step to restore one mod_googlemeet activity.
 */
class restore_googlemeet_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('googlemeet', '/activity/googlemeet');

        $paths[] = new restore_path_element('googlemeet_event',
            '/activity/googlemeet/events/event');

        $paths[] = new restore_path_element('googlemeet_recording',
            '/activity/googlemeet/recordings/recording');

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process a googlemeet restore.
     *
     * @param object $data The data in object form
     * @return void
     */
    protected function process_googlemeet($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        $data->eventdate = $this->apply_date_offset($data->eventdate);
        $data->eventenddate = $this->apply_date_offset($data->eventenddate);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the googlemeet record.
        $newitemid = $DB->insert_record('googlemeet', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process a event restore.
     *
     * @param object $data The data in object form
     * @return void
     */
    protected function process_googlemeet_event($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->googlemeetid = $this->get_new_parentid('googlemeet');
        $data->eventdate = $this->apply_date_offset($data->eventdate);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('googlemeet_events', $data);
        $this->set_mapping('googlemeet_event', $oldid, $newitemid);
    }

    /**
     * Process a recording restore.
     *
     * @param object $data The data in object form
     * @return void
     */
    protected function process_googlemeet_recording($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->googlemeetid = $this->get_new_parentid('googlemeet');
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('googlemeet_recordings', $data);
        $this->set_mapping('googlemeet_recording', $oldid, $newitemid);
    }

    /**
     * Defines post-execution actions.
     */
    protected function after_execute() {
        $this->add_related_files('mod_googlemeet', 'intro', null);
    }
}
