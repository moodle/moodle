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
 * mod_journal backup moodle 2 structure
 *
 * @package    mod_journal
 * @copyright  2014 David Monllao <david.monllao@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The mod_journal_entry_form class.
 *
 * @package    mod_journal
 * @copyright  2022 Elearning Software SRL http://elearningsoftware.ro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_journal_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the backup
     *
     * @return void
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('journal', '/activity/journal');

        if ($this->get_setting_value('userinfo')) {
            $paths[] = new restore_path_element('journal_entry', '/activity/journal/entries/entry');
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process step
     *
     * @param array $data Journal data array
     * @return void
     */
    protected function process_journal($data) {

        global $DB;

        $data = (Object)$data;

        unset($data->id);

        $data->course = $this->get_courseid();
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newid = $DB->insert_record('journal', $data);
        $this->apply_activity_instance($newid);
    }

    /**
     * Process journal entry element
     *
     * @param array $data Data array
     * @return void
     */
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

    /**
     * Code to run after backup finished
     *
     * @return void
     */
    protected function after_execute() {
        $this->add_related_files('mod_journal', 'intro', null);
        $this->add_related_files('mod_journal_entries', 'text', null);
        $this->add_related_files('mod_journal_entries', 'entrycomment', null);
    }
}
