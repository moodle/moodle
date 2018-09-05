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
 * Description of book restore task
 *
 * @package    mod_book
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/book/backup/moodle2/restore_book_stepslib.php'); // Because it exists (must)

class restore_book_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     *
     * @return void
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     *
     * @return void
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_book_activity_structure_step('book_structure', 'book.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     *
     * @return array
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('book', array('intro'), 'book');
        $contents[] = new restore_decode_content('book_chapters', array('content'), 'book_chapter');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     *
     * @return array
     */
    static public function define_decode_rules() {
        $rules = array();

        // List of books in course
        $rules[] = new restore_decode_rule('BOOKINDEX', '/mod/book/index.php?id=$1', 'course');

        // book by cm->id
        $rules[] = new restore_decode_rule('BOOKVIEWBYID', '/mod/book/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('BOOKVIEWBYIDCH', '/mod/book/view.php?id=$1&amp;chapterid=$2', array('course_module', 'book_chapter'));

        // book by book->id
        $rules[] = new restore_decode_rule('BOOKVIEWBYB', '/mod/book/view.php?b=$1', 'book');
        $rules[] = new restore_decode_rule('BOOKVIEWBYBCH', '/mod/book/view.php?b=$1&amp;chapterid=$2', array('book', 'book_chapter'));

        // Convert old book links MDL-33362 & MDL-35007
        $rules[] = new restore_decode_rule('BOOKSTART', '/mod/book/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('BOOKCHAPTER', '/mod/book/view.php?id=$1&amp;chapterid=$2', array('course_module', 'book_chapter'));

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * book logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * @return array
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('book', 'add', 'view.php?id={course_module}', '{book}');
        $rules[] = new restore_log_rule('book', 'update', 'view.php?id={course_module}&chapterid={book_chapter}', '{book}');
        $rules[] = new restore_log_rule('book', 'update', 'view.php?id={course_module}', '{book}');
        $rules[] = new restore_log_rule('book', 'view', 'view.php?id={course_module}&chapterid={book_chapter}', '{book}');
        $rules[] = new restore_log_rule('book', 'view', 'view.php?id={course_module}', '{book}');
        $rules[] = new restore_log_rule('book', 'print', 'tool/print/index.php?id={course_module}&chapterid={book_chapter}', '{book}');
        $rules[] = new restore_log_rule('book', 'print', 'tool/print/index.php?id={course_module}', '{book}');
        $rules[] = new restore_log_rule('book', 'exportimscp', 'tool/exportimscp/index.php?id={course_module}', '{book}');
        // To convert old 'generateimscp' log entries
        $rules[] = new restore_log_rule('book', 'generateimscp', 'tool/generateimscp/index.php?id={course_module}', '{book}',
                'book', 'exportimscp', 'tool/exportimscp/index.php?id={course_module}', '{book}');
        $rules[] = new restore_log_rule('book', 'print chapter', 'tool/print/index.php?id={course_module}&chapterid={book_chapter}', '{book_chapter}');
        $rules[] = new restore_log_rule('book', 'update chapter', 'view.php?id={course_module}&chapterid={book_chapter}', '{book_chapter}');
        $rules[] = new restore_log_rule('book', 'add chapter', 'view.php?id={course_module}&chapterid={book_chapter}', '{book_chapter}');
        $rules[] = new restore_log_rule('book', 'view chapter', 'view.php?id={course_module}&chapterid={book_chapter}', '{book_chapter}');

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
     *
     * @return array
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('book', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
