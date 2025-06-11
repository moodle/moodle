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
 * Restore from backup steps.
 *
 * @package    mod_choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_choicegroup_activity_task
 */
class restore_choicegroup_activity_structure_step extends restore_activity_structure_step {

    /**
     * List of elements that can be restored
     *
     * @return mixed
     */
    protected function define_structure() {

        $paths = [];

        $paths[] = new restore_path_element('choicegroup', '/activity/choicegroup');
        $paths[] = new restore_path_element('choicegroup_option', '/activity/choicegroup/options/option');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restore a choicegroup record.
     *
     * @param stdClass $data
     * @return void
     * @throws base_step_exception
     * @throws dml_exception
     */
    protected function process_choicegroup($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the choicegroup record.
        $newitemid = $DB->insert_record('choicegroup', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restore a choicegroup option.
     *
     * @param stdClass $data
     * @return void
     * @throws base_step_exception
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_choicegroup_option($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->choicegroupid = $this->get_new_parentid('choicegroup');
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Check if the groupid exists in this course.
        $group = $DB->record_exists_sql('SELECT g.id FROM {groups} g
                            WHERE g.courseid = ? and g.id = ?', [$this->get_courseid(), $data->groupid]);
        if (!$group) {
            // It does not exist in the course already, so try to map the groupid.
            $data->groupid = $this->get_mappingid('group', $data->groupid);
            if (!$data->groupid) {
                // The group does not exist, so the option should not be added.
                return;
            }
        }

        $newitemid = $DB->insert_record('choicegroup_options', $data);
        $this->set_mapping('choicegroup_option', $oldid, $newitemid);
    }

    /**
     * Extra actions to take once restore is complete.
     *
     * @return void
     */
    protected function after_execute() {
        // Add choicegroup related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_choicegroup', 'intro', null);
    }
}
