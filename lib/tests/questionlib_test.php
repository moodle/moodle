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
 * Unit tests for (some of) ../questionlib.php.
 *
 * @package    core_question
 * @category   phpunit
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/tag/lib.php');

// Get the necessary files to perform backup and restore.
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Unit tests for (some of) ../questionlib.php.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_questionlib_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Tidy up open files that may be left open.
     */
    protected function tearDown() {
        gc_collect_cycles();
    }

    public function test_question_reorder_qtypes() {
        $this->assertEquals(
            array(0 => 't2', 1 => 't1', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't1', +1));
        $this->assertEquals(
            array(0 => 't1', 1 => 't2', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't1', -1));
        $this->assertEquals(
            array(0 => 't2', 1 => 't1', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't2', -1));
        $this->assertEquals(
            array(0 => 't1', 1 => 't2', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't3', +1));
        $this->assertEquals(
            array(0 => 't1', 1 => 't2', 2 => 't3'),
            question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 'missing', +1));
    }

    public function test_match_grade_options() {
        $gradeoptions = question_bank::fraction_options_full();

        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.3333333, 'error'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.333333, 'error'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33333, 'error'));
        $this->assertFalse(match_grade_options($gradeoptions, 0.3333, 'error'));

        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.3333333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.333333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33333, 'nearest'));
        $this->assertEquals(0.3333333, match_grade_options($gradeoptions, 0.33, 'nearest'));

        $this->assertEquals(-0.1428571, match_grade_options($gradeoptions, -0.15, 'nearest'));
    }

    /**
     * This function tests that the functions responsible for moving questions to
     * different contexts also updates the tag instances associated with the questions.
     */
    public function test_altering_tag_instance_context() {
        global $CFG, $DB;

        // Set to admin user.
        $this->setAdminUser();

        // Create two course categories - we are going to delete one of these later and will expect
        // all the questions belonging to the course in the deleted category to be moved.
        $coursecat1 = $this->getDataGenerator()->create_category();
        $coursecat2 = $this->getDataGenerator()->create_category();

        // Create a couple of categories and questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $questioncat1 = $questiongenerator->create_question_category(array('contextid' =>
            context_coursecat::instance($coursecat1->id)->id));
        $questioncat2 = $questiongenerator->create_question_category(array('contextid' =>
            context_coursecat::instance($coursecat2->id)->id));
        $question1 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat1->id));
        $question2 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat1->id));
        $question3 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat2->id));
        $question4 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat2->id));

        // Now lets tag these questions.
        tag_set('question', $question1->id, array('tag 1', 'tag 2'), 'core_question', $questioncat1->contextid);
        tag_set('question', $question2->id, array('tag 3', 'tag 4'), 'core_question', $questioncat1->contextid);
        tag_set('question', $question3->id, array('tag 5', 'tag 6'), 'core_question', $questioncat2->contextid);
        tag_set('question', $question4->id, array('tag 7', 'tag 8'), 'core_question', $questioncat2->contextid);

        // Test moving the questions to another category.
        question_move_questions_to_category(array($question1->id, $question2->id), $questioncat2->id);

        // Test that all tag_instances belong to one context.
        $this->assertEquals(8, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Test moving them back.
        question_move_questions_to_category(array($question1->id, $question2->id), $questioncat1->id);

        // Test that all tag_instances are now reset to how they were initially.
        $this->assertEquals(4, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat1->contextid)));
        $this->assertEquals(4, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Now test moving a whole question category to another context.
        question_move_category_to_context($questioncat1->id, $questioncat1->contextid, $questioncat2->contextid);

        // Test that all tag_instances belong to one context.
        $this->assertEquals(8, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Now test moving them back.
        question_move_category_to_context($questioncat1->id, $questioncat2->contextid,
            context_coursecat::instance($coursecat1->id)->id);

        // Test that all tag_instances are now reset to how they were initially.
        $this->assertEquals(4, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat1->contextid)));
        $this->assertEquals(4, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Now we want to test deleting the course category and moving the questions to another category.
        question_delete_course_category($coursecat1, $coursecat2, false);

        // Test that all tag_instances belong to one context.
        $this->assertEquals(8, $DB->count_records('tag_instance', array('component' => 'core_question',
            'contextid' => $questioncat2->contextid)));

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create some question categories and questions in this course.
        $questioncat = $questiongenerator->create_question_category(array('contextid' =>
            context_course::instance($course->id)->id));
        $question1 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat->id));
        $question2 = $questiongenerator->create_question('shortanswer', null, array('category' => $questioncat->id));

        // Add some tags to these questions.
        tag_set('question', $question1->id, array('tag 1', 'tag 2'), 'core_question', $questioncat->contextid);
        tag_set('question', $question2->id, array('tag 1', 'tag 2'), 'core_question', $questioncat->contextid);

        // Create a course that we are going to restore the other course to.
        $course2 = $this->getDataGenerator()->create_course();

        // Create backup file and save it to the backup location.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2);
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();
        unset($bc);

        // Now restore the course.
        $rc = new restore_controller('test-restore-course', $course2->id, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, 2, backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();

        // Get the created question category.
        $restoredcategory = $DB->get_record('question_categories', array('contextid' => context_course::instance($course2->id)->id),
            '*', MUST_EXIST);

        // Check that there are two questions in the restored to course's context.
        $this->assertEquals(2, $DB->count_records('question', array('category' => $restoredcategory->id)));
    }
}
