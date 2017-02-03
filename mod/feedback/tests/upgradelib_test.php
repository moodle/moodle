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
 * Tests for functions in db/upgradelib.php
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/feedback/db/upgradelib.php');

/**
 * Tests for functions in db/upgradelib.php
 *
 * @package    mod_feedback
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_upgradelib_testcase extends advanced_testcase {

    /** @var string  */
    protected $testsql = "SELECT COUNT(v.id) FROM {feedback_completed} c, {feedback_value} v
            WHERE c.id = v.completed AND c.courseid <> v.course_id";
    /** @var string  */
    protected $testsqltmp = "SELECT COUNT(v.id) FROM {feedback_completedtmp} c, {feedback_valuetmp} v
            WHERE c.id = v.completed AND c.courseid <> v.course_id";
    /** @var int */
    protected $course1;
    /** @var int */
    protected $course2;
    /** @var stdClass */
    protected $feedback;
    /** @var stdClass */
    protected $user;

    /**
     * Sets up the fixture
     * This method is called before a test is executed.
     */
    public function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);

        $this->course1 = $this->getDataGenerator()->create_course();
        $this->course2 = $this->getDataGenerator()->create_course();
        $this->feedback = $this->getDataGenerator()->create_module('feedback', array('course' => SITEID));

        $this->user = $this->getDataGenerator()->create_user();
    }

    public function test_upgrade_courseid_completed() {
        global $DB;

        // Case 1. No errors in the data.
        $completed1 = $DB->insert_record('feedback_completed',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
            'item' => 1, 'value' => 1]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
            'item' => 2, 'value' => 2]);

        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $this->assertEquals(2, $DB->count_records_sql($this->testsql)); // We have errors!
        mod_feedback_upgrade_courseid(true); // Running script for temp tables.
        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $this->assertEquals(2, $DB->count_records_sql($this->testsql)); // Nothing changed.
        mod_feedback_upgrade_courseid();
        $this->assertCount(1, $DB->get_records('feedback_completed')); // Number of records is the same.
        $this->assertEquals(0, $DB->count_records_sql($this->testsql)); // All errors are fixed!
    }

    public function test_upgrade_courseid_completed_with_errors() {
        global $DB;

        // Case 2. Errors in data (same feedback_completed has values for different courses).
        $completed1 = $DB->insert_record('feedback_completed',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
            'item' => 1, 'value' => 1]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course2->id,
            'item' => 1, 'value' => 2]);

        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $this->assertEquals(2, $DB->count_records_sql($this->testsql)); // We have errors!
        mod_feedback_upgrade_courseid(true); // Running script for temp tables.
        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $this->assertEquals(2, $DB->count_records_sql($this->testsql)); // Nothing changed.
        mod_feedback_upgrade_courseid();
        $this->assertCount(2, $DB->get_records('feedback_completed')); // Extra record inserted.
        $this->assertEquals(0, $DB->count_records_sql($this->testsql)); // All errors are fixed!
    }

    public function test_upgrade_courseid_completedtmp() {
        global $DB;

        // Case 1. No errors in the data.
        $completed1 = $DB->insert_record('feedback_completedtmp',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
            'item' => 1, 'value' => 1]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
            'item' => 2, 'value' => 2]);

        $this->assertCount(1, $DB->get_records('feedback_completedtmp'));
        $this->assertEquals(2, $DB->count_records_sql($this->testsqltmp)); // We have errors!
        mod_feedback_upgrade_courseid(); // Running script for non-temp tables.
        $this->assertCount(1, $DB->get_records('feedback_completedtmp'));
        $this->assertEquals(2, $DB->count_records_sql($this->testsqltmp)); // Nothing changed.
        mod_feedback_upgrade_courseid(true);
        $this->assertCount(1, $DB->get_records('feedback_completedtmp')); // Number of records is the same.
        $this->assertEquals(0, $DB->count_records_sql($this->testsqltmp)); // All errors are fixed!
    }

    public function test_upgrade_courseid_completedtmp_with_errors() {
        global $DB;

        // Case 2. Errors in data (same feedback_completed has values for different courses).
        $completed1 = $DB->insert_record('feedback_completedtmp',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
            'item' => 1, 'value' => 1]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course2->id,
            'item' => 1, 'value' => 2]);

        $this->assertCount(1, $DB->get_records('feedback_completedtmp'));
        $this->assertEquals(2, $DB->count_records_sql($this->testsqltmp)); // We have errors!
        mod_feedback_upgrade_courseid(); // Running script for non-temp tables.
        $this->assertCount(1, $DB->get_records('feedback_completedtmp'));
        $this->assertEquals(2, $DB->count_records_sql($this->testsqltmp)); // Nothing changed.
        mod_feedback_upgrade_courseid(true);
        $this->assertCount(2, $DB->get_records('feedback_completedtmp')); // Extra record inserted.
        $this->assertEquals(0, $DB->count_records_sql($this->testsqltmp)); // All errors are fixed!
    }

    public function test_upgrade_courseid_empty_completed() {
        global $DB;

        // Record in 'feedback_completed' does not have corresponding values.
        $DB->insert_record('feedback_completed',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);

        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $record1 = $DB->get_record('feedback_completed', []);
        mod_feedback_upgrade_courseid();
        $this->assertCount(1, $DB->get_records('feedback_completed')); // Number of records is the same.
        $record2 = $DB->get_record('feedback_completed', []);
        $this->assertEquals($record1, $record2);
    }

    public function test_upgrade_remove_duplicates_no_duplicates() {
        global $DB;

        $completed1 = $DB->insert_record('feedback_completed',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 1, 'value' => 1]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 2, 'value' => 2]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 3, 'value' => 1]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course2->id,
                'item' => 3, 'value' => 2]);

        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $this->assertEquals(4, $DB->count_records('feedback_value'));
        mod_feedback_upgrade_delete_duplicate_values();
        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $this->assertEquals(4, $DB->count_records('feedback_value')); // Same number of records, no changes made.
    }

    public function test_upgrade_remove_duplicates() {
        global $DB;

        // Remove the index that was added in the upgrade.php AFTER running mod_feedback_upgrade_delete_duplicate_values().
        $dbman = $DB->get_manager();
        $table = new xmldb_table('feedback_value');
        $index = new xmldb_index('completed_item', XMLDB_INDEX_UNIQUE, array('completed', 'item', 'course_id'));
        $dbman->drop_index($table, $index);

        // Insert duplicated values.
        $completed1 = $DB->insert_record('feedback_completed',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 1, 'value' => 1]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 1, 'value' => 2]); // This is a duplicate with another value.
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 3, 'value' => 1]);
        $DB->insert_record('feedback_value',
            ['completed' => $completed1, 'course_id' => $this->course2->id,
                'item' => 3, 'value' => 2]); // This is not a duplicate because course id is different.

        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $this->assertEquals(4, $DB->count_records('feedback_value'));
        mod_feedback_upgrade_delete_duplicate_values(true); // Running script for temp tables.
        $this->assertCount(1, $DB->get_records('feedback_completed'));
        $this->assertEquals(4, $DB->count_records('feedback_value')); // Nothing changed.
        mod_feedback_upgrade_delete_duplicate_values();
        $this->assertCount(1, $DB->get_records('feedback_completed')); // Number of records is the same.
        $this->assertEquals(3, $DB->count_records('feedback_value')); // Duplicate was deleted.
        $this->assertEquals(1, $DB->get_field('feedback_value', 'value', ['item' => 1]));

        $dbman->add_index($table, $index);
    }

    public function test_upgrade_remove_duplicates_no_duplicates_tmp() {
        global $DB;

        $completed1 = $DB->insert_record('feedback_completedtmp',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 1, 'value' => 1]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 2, 'value' => 2]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 3, 'value' => 1]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course2->id,
                'item' => 3, 'value' => 2]);

        $this->assertCount(1, $DB->get_records('feedback_completedtmp'));
        $this->assertEquals(4, $DB->count_records('feedback_valuetmp'));
        mod_feedback_upgrade_delete_duplicate_values(true);
        $this->assertCount(1, $DB->get_records('feedback_completedtmp'));
        $this->assertEquals(4, $DB->count_records('feedback_valuetmp')); // Same number of records, no changes made.
    }

    public function test_upgrade_remove_duplicates_tmp() {
        global $DB;

        // Remove the index that was added in the upgrade.php AFTER running mod_feedback_upgrade_delete_duplicate_values().
        $dbman = $DB->get_manager();
        $table = new xmldb_table('feedback_valuetmp');
        $index = new xmldb_index('completed_item', XMLDB_INDEX_UNIQUE, array('completed', 'item', 'course_id'));
        $dbman->drop_index($table, $index);

        // Insert duplicated values.
        $completed1 = $DB->insert_record('feedback_completedtmp',
            ['feedback' => $this->feedback->id, 'userid' => $this->user->id]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 1, 'value' => 1]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 1, 'value' => 2]); // This is a duplicate with another value.
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course1->id,
                'item' => 3, 'value' => 1]);
        $DB->insert_record('feedback_valuetmp',
            ['completed' => $completed1, 'course_id' => $this->course2->id,
                'item' => 3, 'value' => 2]); // This is not a duplicate because course id is different.

        $this->assertCount(1, $DB->get_records('feedback_completedtmp'));
        $this->assertEquals(4, $DB->count_records('feedback_valuetmp'));
        mod_feedback_upgrade_delete_duplicate_values(); // Running script for non-temp tables.
        $this->assertCount(1, $DB->get_records('feedback_completedtmp'));
        $this->assertEquals(4, $DB->count_records('feedback_valuetmp')); // Nothing changed.
        mod_feedback_upgrade_delete_duplicate_values(true);
        $this->assertCount(1, $DB->get_records('feedback_completedtmp')); // Number of records is the same.
        $this->assertEquals(3, $DB->count_records('feedback_valuetmp')); // Duplicate was deleted.
        $this->assertEquals(1, $DB->get_field('feedback_valuetmp', 'value', ['item' => 1]));

        $dbman->add_index($table, $index);
    }
}