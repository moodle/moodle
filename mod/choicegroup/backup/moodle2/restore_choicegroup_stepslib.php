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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_choicegroup_activity_task
 */

/**
 * Structure step to restore one choicegroup activity
 */

defined('MOODLE_INTERNAL') || die();

class restore_choicegroup_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('choicegroup', '/activity/choicegroup');
        $paths[] = new restore_path_element('choicegroup_option', '/activity/choicegroup/options/option');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_choicegroup($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the choicegroup record
        $newitemid = $DB->insert_record('choicegroup', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_choicegroup_option($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->choicegroupid = $this->get_new_parentid('choicegroup');
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->groupid = $this->get_mappingid('group', $data->groupid);

        $newitemid = $DB->insert_record('choicegroup_options', $data);
        $this->set_mapping('choicegroup_option', $oldid, $newitemid);
    }

    protected function after_execute() {
        // Add choicegroup related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_choicegroup', 'intro', null);
    }
}
