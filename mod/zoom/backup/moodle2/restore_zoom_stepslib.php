<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Define all the restore steps that will be used by the restore_zoom_activity_task
 *
 * @package   mod_zoom
 * @category  backup
 * @copyright 2015 UC Regents
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/zoom/locallib.php');
require_once($CFG->dirroot.'/mod/zoom/classes/webservice.php');

/**
 * Structure step to restore one zoom activity
 *
 * @package   mod_zoom
 * @category  backup
 * @copyright 2015 UC Regents
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_zoom_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of restore_path_element
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('zoom', '/activity/zoom');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_zoom($data) {
        global $DB;

        $data = (object)$data;
        $service = new mod_zoom_webservice();

        // Update start_time before attempting to create a new meeting.
        $data->start_time = $this->apply_date_offset($data->start_time);

        // Either create a new meeting or set meeting as expired.
        $updateddata = $service->create_meeting($data);
        if (!$updateddata) {
            $updateddata = new stdClass;
            $updateddata->status = ZOOM_MEETING_EXPIRED;
        }

        $data->start_url = $updateddata->start_url;
        $data->join_url = $updateddata->join_url;
        $data->meeting_id = $updateddata->id;
        $data->course = $this->get_courseid();

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        if ($data->grade < 0) {
            // Scale found, get mapping.
            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        }

        // Create the zoom instance.
        $newitemid = $DB->insert_record('zoom', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add zoom related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_zoom', 'intro', null);
    }
}
