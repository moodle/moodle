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

class restore_journal_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('journal', '/activity/journal');

        if ($this->get_setting_value('userinfo')) {
            $paths[] = new restore_path_element('journal_entry', '/activity/journal/entries/entry');
        }

        return $this->prepare_activity_structure($paths);
    }

    protected function process_journal($data) {

        global $DB;

        $data = (Object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->course = $this->get_courseid();
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newid = $DB->insert_record('journal', $data);
        $this->apply_activity_instance($newid);
    }

    protected function process_journal_entry($data) {

        global $DB;

        $data = (Object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->journal = $this->get_new_parentid('journal');
        $data->modified = $this->apply_date_offset($data->modified);
        $data->timemarked = $this->apply_date_offset($data->timemarked);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->teacher = $this->get_mappingid('user', $data->teacher);

        $newid = $DB->insert_record('journal_entries', $data);
        $this->set_mapping('journal_entry', $oldid, $newid);
    }

    protected function after_execute() {
        $this->add_related_files('mod_journal', 'intro', null);
        $this->add_related_files('mod_journal_entries', 'text', null);
        $this->add_related_files('mod_journal_entries', 'entrycomment', null);
    }
}
