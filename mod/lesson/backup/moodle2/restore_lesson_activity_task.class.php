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
 * @package mod_lesson
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/lesson/backup/moodle2/restore_lesson_stepslib.php'); // Because it exists (must)

/**
 * lesson restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_lesson_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // lesson only has one structure step
        $this->add_step(new restore_lesson_activity_structure_step('lesson_structure', 'lesson.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('lesson', array('intro'), 'lesson');
        $contents[] = new restore_decode_content('lesson_pages', array('contents'), 'lesson_page');
        $contents[] = new restore_decode_content('lesson_answers', array('answer', 'response'), 'lesson_answer');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('LESSONEDIT', '/mod/lesson/edit.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('LESSONESAY', '/mod/lesson/essay.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('LESSONREPORT', '/mod/lesson/report.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('LESSONMEDIAFILE', '/mod/lesson/mediafile.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('LESSONVIEWBYID', '/mod/lesson/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('LESSONINDEX', '/mod/lesson/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('LESSONVIEWPAGE', '/mod/lesson/view.php?id=$1&pageid=$2', array('course_module', 'lesson_page'));
        $rules[] = new restore_decode_rule('LESSONEDITPAGE', '/mod/lesson/edit.php?id=$1&pageid=$2', array('course_module', 'lesson_page'));

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * lesson logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('lesson', 'add', 'view.php?id={course_module}', '{lesson}');
        $rules[] = new restore_log_rule('lesson', 'update', 'view.php?id={course_module}', '{lesson}');
        $rules[] = new restore_log_rule('lesson', 'view', 'view.php?id={course_module}', '{lesson}');
        $rules[] = new restore_log_rule('lesson', 'start', 'view.php?id={course_module}', '{lesson}');
        $rules[] = new restore_log_rule('lesson', 'end', 'view.php?id={course_module}', '{lesson}');
        $rules[] = new restore_log_rule('lesson', 'view grade', 'essay.php?id={course_module}', '[name]');
        $rules[] = new restore_log_rule('lesson', 'update grade', 'essay.php?id={course_module}', '[name]');
        $rules[] = new restore_log_rule('lesson', 'update email essay grade', 'essay.php?id={course_module}', '[name]');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    public static function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('lesson', 'view all', 'index.php?id={course}', null);

        return $rules;
    }


    /**
     * Re-map the dependency and activitylink information
     * If a depency or activitylink has no mapping in the backup data then it could either be a duplication of a
     * lesson, or a backup/restore of a single lesson. We have no way to determine which and whether this is the
     * same site and/or course. Therefore we try and retrieve a mapping, but fallback to the original value if one
     * was not found. We then test to see whether the value found is valid for the course being restored into.
     */
    public function after_restore() {
        global $DB;

        $lesson = $DB->get_record('lesson', array('id' => $this->get_activityid()), 'id, course, dependency, activitylink');
        $updaterequired = false;

        if (!empty($lesson->dependency)) {
            $updaterequired = true;
            if ($newitem = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'lesson', $lesson->dependency)) {
                $lesson->dependency = $newitem->newitemid;
            }
            if (!$DB->record_exists('lesson', array('id' => $lesson->dependency, 'course' => $lesson->course))) {
                $lesson->dependency = 0;
            }
        }

        if (!empty($lesson->activitylink)) {
            $updaterequired = true;
            if ($newitem = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'course_module', $lesson->activitylink)) {
                $lesson->activitylink = $newitem->newitemid;
            }
            if (!$DB->record_exists('course_modules', array('id' => $lesson->activitylink, 'course' => $lesson->course))) {
                $lesson->activitylink = 0;
            }
        }

        if ($updaterequired) {
            $DB->update_record('lesson', $lesson);
        }
    }
}
