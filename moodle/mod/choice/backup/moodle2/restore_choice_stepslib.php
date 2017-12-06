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
 * @package    mod_choice
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_choice_activity_task
 */

/**
 * Structure step to restore one choice activity
 */
class restore_choice_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('choice', '/activity/choice');
        $paths[] = new restore_path_element('choice_option', '/activity/choice/options/option');
        if ($userinfo) {
            $paths[] = new restore_path_element('choice_answer', '/activity/choice/answers/answer');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_choice($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the choice record
        $newitemid = $DB->insert_record('choice', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_choice_option($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->choiceid = $this->get_new_parentid('choice');
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('choice_options', $data);
        $this->set_mapping('choice_option', $oldid, $newitemid);
    }

    protected function process_choice_answer($data) {
        global $DB;

        $data = (object)$data;

        $data->choiceid = $this->get_new_parentid('choice');
        $data->optionid = $this->get_mappingid('choice_option', $data->optionid);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('choice_answers', $data);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }

    protected function after_execute() {
        // Add choice related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_choice', 'intro', null);
    }
}
