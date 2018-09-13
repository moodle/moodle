<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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
global $CFG;
require_once($CFG->dirroot.'/mod/checklist/backup/moodle2/restore_checklist_stepslib.php'); // Because it exists (must).

/**
 * checklist restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_checklist_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step.
        $this->add_step(new restore_checklist_activity_structure_step('checklist_structure', 'checklist.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('checklist', array('intro'), 'checklist');
        $contents[] = new restore_decode_content('checklist_item', array('linkurl'),  'checklist_item');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        // List of checklists in course.
        $rules[] = new restore_decode_rule('CHECKLISTINDEX', '/mod/checklist/index.php?id=$1', 'course');
        // Checklist by cm->id and forum->id.
        $rules[] = new restore_decode_rule('CHECKLISTVIEWBYID', '/mod/checklist/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('CHECKLISTVIEWBYCHECKLIST', '/mod/checklist/view.php?checklist=$1', 'checklist');
        // Checklist report by cm->id and forum->id.
        $rules[] = new restore_decode_rule('CHECKLISTREPORTBYID', '/mod/checklist/report.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('CHECKLISTREPORTBYCHECKLIST', '/mod/checklist/report.php?checklist=$1', 'checklist');
        // Checklist edit by cm->id and forum->id.
        $rules[] = new restore_decode_rule('CHECKLISTEDITBYID', '/mod/checklist/edit.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('CHECKLISTEDITBYCHECKLIST', '/mod/checklist/edit.php?checklist=$1', 'checklist');

        return $rules;
    }

    public function after_restore() {
        global $DB;

        // Find all the items that have a 'moduleid' but are not headings and match them up to the newly-restored activities.
        $items = $DB->get_records_select('checklist_item', 'checklist = ? AND moduleid > 0 AND itemoptional <> 2',
                                         array($this->get_activityid()));

        foreach ($items as $item) {
            $moduleid = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'course_module', $item->moduleid);
            if ($moduleid) {
                // Match up the moduleid to the restored activity module.
                $DB->set_field('checklist_item', 'moduleid', $moduleid->newitemid, array('id' => $item->id));
            } else {
                // Does not match up to a restored activity module => delete the item + associated user data.
                $DB->delete_records('checklist_check', array('item' => $item->id));
                $DB->delete_records('checklist_comment', array('itemid' => $item->id));
                $DB->delete_records('checklist_item', array('id' => $item->id));
            }
        }
    }


    /**
     * Added fix from https://tracker.moodle.org/browse/MDL-34172
     */

    /**
     * Define the restore log rules that will be applied by the
     * {@link restore_logs_processor} when restoring
     * folder logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('checklist', 'add', 'view.php?id={course_module}', '{folder}');
        $rules[] = new restore_log_rule('checklist', 'edit', 'edit.php?id={course_module}', '{folder}');
        $rules[] = new restore_log_rule('checklist', 'view', 'view.php?id={course_module}', '{folder}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array of
     * {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('checklist', 'view all', 'index.php?id={course}', null);

        return $rules;
    }

}
