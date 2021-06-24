<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Define all the restore steps that will be used by the restore_customcert_activity_task.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

/**
 * Define the complete customcert structure for restore, with file and id annotations.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_customcert_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the different items to restore.
     *
     * @return array the restore paths
     */
    protected function define_structure() {
        // The array used to store the path to the items we want to restore.
        $paths = array();

        // The customcert instance.
        $paths[] = new restore_path_element('customcert', '/activity/customcert');

        // The templates.
        $paths[] = new restore_path_element('customcert_template', '/activity/customcert/template');

        // The pages.
        $paths[] = new restore_path_element('customcert_page', '/activity/customcert/template/pages/page');

        // The elements.
        $paths[] = new restore_path_element('customcert_element', '/activity/customcert/template/pages/page/element');

        // Check if we want the issues as well.
        if ($this->get_setting_value('userinfo')) {
            $paths[] = new restore_path_element('customcert_issue', '/activity/customcert/issues/issue');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Handles restoring the customcert activity.
     *
     * @param stdClass $data the customcert data
     */
    protected function process_customcert($data) {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the customcert record.
        $newitemid = $DB->insert_record('customcert', $data);

        // Immediately after inserting record call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Handles restoring a customcert page.
     *
     * @param stdClass $data the customcert data
     */
    protected function process_customcert_template($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->contextid = $this->task->get_contextid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('customcert_templates', $data);
        $this->set_mapping('customcert_template', $oldid, $newitemid);

        // Update the template id for the customcert.
        $customcert = new stdClass();
        $customcert->id = $this->get_new_parentid('customcert');
        $customcert->templateid = $newitemid;
        $DB->update_record('customcert', $customcert);
    }

    /**
     * Handles restoring a customcert template.
     *
     * @param stdClass $data the customcert data
     */
    protected function process_customcert_page($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->templateid = $this->get_new_parentid('customcert_template');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('customcert_pages', $data);
        $this->set_mapping('customcert_page', $oldid, $newitemid);
    }

    /**
     * Handles restoring a customcert element.
     *
     * @param stdclass $data the customcert data
     */
    protected function process_customcert_element($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->pageid = $this->get_new_parentid('customcert_page');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('customcert_elements', $data);
        $this->set_mapping('customcert_element', $oldid, $newitemid);
    }

    /**
     * Handles restoring a customcert issue.
     *
     * @param stdClass $data the customcert data
     */
    protected function process_customcert_issue($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->customcertid = $this->get_new_parentid('customcert');
        $data->timecreated = $this->apply_date_offset($data->timecreated);

        $newitemid = $DB->insert_record('customcert_issues', $data);
        $this->set_mapping('customcert_issue', $oldid, $newitemid);
    }

    /**
     * Called immediately after all the other restore functions.
     */
    protected function after_execute() {
        parent::after_execute();

        // Add the files.
        $this->add_related_files('mod_customcert', 'intro', null);

        // Note - we can't use get_old_contextid() as it refers to the module context.
        $this->add_related_files('mod_customcert', 'image', null, $this->get_task()->get_info()->original_course_contextid);
    }
}
