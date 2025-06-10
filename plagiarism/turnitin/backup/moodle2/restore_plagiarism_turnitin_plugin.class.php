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

defined('MOODLE_INTERNAL') || die();


class restore_plagiarism_turnitin_plugin extends restore_plagiarism_plugin {

    /**
     * Return the paths of the course data along with the function used for restoring that data.
     */
    protected function define_course_plugin_structure() {
        $paths = array();
        $paths[] = new restore_path_element('turnitin_course', $this->get_pathfor('turnitin_courses/turnitin_course'));

        return $paths;
    }

    /**
     * Restore the Turnitin course
     * This will only be done this if the course is from the same site it was backed up from
     * and if the Turnitin course id does not currently exist in the database.
     */
    public function process_turnitin_course($data) {
        global $DB;

        if ($this->task->is_samesite()) {
            $data = (object)$data;
            $recordexists = $DB->record_exists('plagiarism_turnitin_courses', array('turnitin_cid' => $data->turnitin_cid));

            if (!$recordexists) {
                $data = (object)$data;
                $data->courseid = $this->task->get_courseid();

                // Unset course type in case the restore is from a backup taken prior to change for where the courses are stored.
                unset($data->course_type);

                $DB->insert_record('plagiarism_turnitin_courses', $data);
            }
        }
    }

    /**
     * Return the paths of the module data along with the function used for restoring that data.
     */
    protected function define_module_plugin_structure() {
        $paths = array();
        $paths[] = new restore_path_element('turnitin_config', $this->get_pathfor('turnitin_configs/turnitin_config'));
        $paths[] = new restore_path_element('turnitin_files', $this->get_pathfor('/turnitin_files/turnitin_file'));

        return $paths;
    }

    /**
     * Restore the Turnitin assignment id for this module
     * This will only be done this if the module is from the same site it was backed up from
     * and if the Turnitin assignment id does not currently exist in the database.
     */
    public function process_turnitin_config($data) {
        global $DB;

        if ($this->task->is_samesite()) {
            $data = (object)$data;
            $recordexists = ($data->name == 'turnitin_assign') ? $DB->record_exists('plagiarism_turnitin_config',
                array('name' => 'turnitin_assign', 'value' => $data->value)) : false;
            $recordexists = ($data->name == 'turnitin_assignid') ? $DB->record_exists('plagiarism_turnitin_config',
                array('name' => 'turnitin_assignid', 'value' => $data->value)) : $recordexists;

            if (!$recordexists) {
                $data = (object)$data;
                $data->name = ($data->name == 'turnitin_assign') ? 'turnitin_assignid' : $data->name;
                $data->cm = $this->task->get_moduleid();
                $data->config_hash = $data->cm."_".$data->name;

                $DB->insert_record('plagiarism_turnitin_config', $data);
            }
        }
    }

    /**
     * Restore the links to Turnitin files.
     * This will only be done this if the module is from the same site it was backed up from
     * and if the Turnitin submission does not currently exist in the database.
     */
    public function process_turnitin_files($data) {
        global $DB;

        if ($this->task->is_samesite()) {
            $data = (object)$data;
            $recordexists = (!empty($data->externalid)) ? $DB->record_exists('plagiarism_turnitin_files',
                array('externalid' => $data->externalid)) : false;

            if (!$recordexists) {
                $data->cm = $this->task->get_moduleid();
                $data->userid = $this->get_mappingid('user', $data->userid);

                $DB->insert_record('plagiarism_turnitin_files', $data);
            }
        }
    }
}