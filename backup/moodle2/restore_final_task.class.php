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
 * Defines restore_final_task class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Final task that provides all the final steps necessary in order to finish one
 * restore like gradebook, interlinks... apart from some final cleaning
 *
 * TODO: Finish phpdocs
 */
class restore_final_task extends restore_task {

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // Move all the CONTEXT_MODULE question qcats to their
        // final (newly created) module context
        $this->add_step(new restore_move_module_questions_categories('move_module_question_categories'));

        // Create all the question files now that every question is in place
        // and every category has its final contextid associated
        $this->add_step(new restore_create_question_files('create_question_files'));

        // Review all the block_position records in backup_ids in order
        // match them now that all the contexts are created populating DB
        // as needed. Only if we are restoring blocks.
        if ($this->get_setting_value('blocks')) {
            $this->add_step(new restore_review_pending_block_positions('review_block_positions'));
        }

        // Gradebook. Don't restore the gradebook unless activities are being restored.
        if ($this->get_setting_value('activities')) {
            $this->add_step(new restore_gradebook_structure_step('gradebook_step','gradebook.xml'));
            $this->add_step(new restore_grade_history_structure_step('grade_history', 'grade_history.xml'));
        }

        // Course completion.
        $this->add_step(new restore_course_completion_structure_step('course_completion', 'completion.xml'));

        // Conditionally restore course badges.
        if ($this->get_setting_value('badges')) {
            $this->add_step(new restore_badges_structure_step('course_badges', 'badges.xml'));
        }

        // Review all the legacy module_availability records in backup_ids in
        // order to match them with existing modules / grade items and convert
        // into the new system.
        $this->add_step(new restore_process_course_modules_availability('process_modules_availability'));

        // Update restored availability data to account for changes in IDs
        // during backup/restore.
        $this->add_step(new restore_update_availability('update_availability'));

        // Decode all the interlinks
        $this->add_step(new restore_decode_interlinks('decode_interlinks'));

        // Restore course logs (conditionally). They are restored here because we need all
        // the activities to be already restored
        if ($this->get_setting_value('logs')) {
            $this->add_step(new restore_course_logs_structure_step('course_logs', 'course/logs.xml'));
        }

        // Review all the executed tasks having one after_restore method
        // executing it to perform some final adjustments of information
        // not available when the task was executed.
        // This step is always the last one performing modifications on restored information
        // Don't add any new step after it. Only aliases queue, cache rebuild and clean are allowed.
        $this->add_step(new restore_execute_after_restore('executing_after_restore'));

        // All files were sent to the filepool by now. We need to process
        // the aliases yet as they were not actually created but stashed for us instead.
        // We execute this step after executing_after_restore so that there can't be no
        // more files sent to the filepool after this.
        $this->add_step(new restore_process_file_aliases_queue('process_file_aliases_queue'));

        // Rebuild course cache to see results, whoah!
        $this->add_step(new restore_rebuild_course_cache('rebuild_course_cache'));

        // Clean the temp dir (conditionally) and drop temp table
        $this->add_step(new restore_drop_and_clean_temp_stuff('drop_and_clean_temp_stuff'));

        $this->built = true;
    }

    /**
     * Special method, only available in the restore_final_task, able to invoke the
     * restore_plan execute_after_restore() method, so restore_execute_after_restore step
     * will be able to launch all the after_restore() methods of the executed tasks
     */
    public function launch_execute_after_restore() {
        $this->plan->execute_after_restore();
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note these are course logs, but are defined and restored
     * in final task because we need all the activities to be
     * restored in order to handle some log records properly
     */
    static public function define_restore_log_rules() {
        $rules = array();

        // module 'course' rules
        $rules[] = new restore_log_rule('course', 'view', 'view.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('course', 'guest', 'view.php?id={course}', null);
        $rules[] = new restore_log_rule('course', 'user report', 'user.php?id={course}&user={user}&mode=[mode]', null);
        $rules[] = new restore_log_rule('course', 'add mod', '../mod/[modname]/view.php?id={course_module}', '[modname] {[modname]}');
        $rules[] = new restore_log_rule('course', 'update mod', '../mod/[modname]/view.php?id={course_module}', '[modname] {[modname]}');
        $rules[] = new restore_log_rule('course', 'delete mod', 'view.php?id={course}', null);
        $rules[] = new restore_log_rule('course', 'update', 'view.php?id={course}', '');
        $rules[] = new restore_log_rule('course', 'enrol', 'view.php?id={course}', '{user}');
        $rules[] = new restore_log_rule('course', 'unenrol', 'view.php?id={course}', '{user}');
        $rules[] = new restore_log_rule('course', 'editsection', 'editsection.php?id={course_section}', null);
        $rules[] = new restore_log_rule('course', 'new', 'view.php?id={course}', '');
        $rules[] = new restore_log_rule('course', 'recent', 'recent.php?id={course}', '');
        $rules[] = new restore_log_rule('course', 'report log', 'report/log/index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('course', 'report live', 'report/live/index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('course', 'report outline', 'report/outline/index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('course', 'report participation', 'report/participation/index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('course', 'report stats', 'report/stats/index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('course', 'view section', 'view.php?id={course}&sectionid={course_section}', '{course_section}');

        // module 'grade' rules
        $rules[] = new restore_log_rule('grade', 'update', 'report/grader/index.php?id={course}', null);

        // module 'user' rules
        $rules[] = new restore_log_rule('user', 'view', 'view.php?id={user}&course={course}', '{user}');
        $rules[] = new restore_log_rule('user', 'change password', 'view.php?id={user}&course={course}', '{user}');
        $rules[] = new restore_log_rule('user', 'login', 'view.php?id={user}&course={course}', '{user}');
        $rules[] = new restore_log_rule('user', 'logout', 'view.php?id={user}&course={course}', '{user}');
        $rules[] = new restore_log_rule('user', 'view all', 'index.php?id={course}', '');
        $rules[] = new restore_log_rule('user', 'update', 'view.php?id={user}&course={course}', '');

        // rules from other tasks (activities) not belonging to one module instance (cmid = 0), so are restored here
        $rules = array_merge($rules, restore_logs_processor::register_log_rules_for_course());

        // Calendar rules.
        $rules[] = new restore_log_rule('calendar', 'add', 'event.php?action=edit&id={event}', '[name]');
        $rules[] = new restore_log_rule('calendar', 'edit', 'event.php?action=edit&id={event}', '[name]');
        $rules[] = new restore_log_rule('calendar', 'edit all', 'event.php?action=edit&id={event}', '[name]');

        // TODO: Other logs like 'upload'... will go here

        return $rules;
    }


// Protected API starts here

    /**
     * Define the common setting that any restore type will have
     */
    protected function define_settings() {
        // This task has not settings (could have them, like destination or so in the future, let's see)
    }
}
