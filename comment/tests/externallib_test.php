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
 * External comment functions unit tests
 *
 * @package    core_comment
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External comment functions unit tests
 *
 * @package    core_comment
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class core_comment_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Helper used to set up a course, with a module, a teacher and two students.
     *
     * @return array the array of records corresponding to the course, teacher, and students.
     */
    protected function setup_course_and_users_basic() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/comment/lib.php');

        $CFG->usecomments = true;

        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $teacher1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course(array('enablecomment' => 1));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student1->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($teacher1->id, $course1->id, $teacherrole->id);

        // Create a database module instance.
        $record = new stdClass();
        $record->course = $course1->id;
        $record->name = "Mod data test";
        $record->intro = "Some intro of some sort";
        $record->comments = 1;

        $module1 = $this->getDataGenerator()->create_module('data', $record);
        $field = data_get_field_new('text', $module1);

        $fielddetail = new stdClass();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module1);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';
        $DB->insert_record('data_content', $datacontent);

        return [$module1, $recordid, $teacher1, $student1, $student2];
    }

    /**
     * Test get_comments
     */
    public function test_get_comments() {
        global $CFG;
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Create some comments as student 1.
        $this->setUser($student1);
        $inputdata = [
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ],
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'def',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ]
        ];
        $result = core_comment_external::add_comments($inputdata);
        $result = external_api::clean_returnvalue(core_comment_external::add_comments_returns(), $result);
        $ids = array_column($result, 'id');

        // Verify we can get the comments.
        $contextlevel = 'module';
        $instanceid = $module1->cmid;
        $component = 'mod_data';
        $itemid = $recordid;
        $area = 'database_entry';
        $page = 0;
        $result = core_comment_external::get_comments($contextlevel, $instanceid, $component, $itemid, $area, $page);
        $result = external_api::clean_returnvalue(core_comment_external::get_comments_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['comments']);
        $this->assertEquals(2, $result['count']);
        $this->assertEquals(15, $result['perpage']);
        $this->assertTrue($result['canpost']);

        $this->assertEquals($student1->id, $result['comments'][0]['userid']);
        $this->assertEquals($student1->id, $result['comments'][1]['userid']);

        $this->assertEquals($ids[1], $result['comments'][0]['id']); // Default ordering newer first.
        $this->assertEquals($ids[0], $result['comments'][1]['id']);

        // Test sort direction and pagination.
        $CFG->commentsperpage = 1;
        $result = core_comment_external::get_comments($contextlevel, $instanceid, $component, $itemid, $area, $page, 'ASC');
        $result = external_api::clean_returnvalue(core_comment_external::get_comments_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['comments']); // Only one per page.
        $this->assertEquals(2, $result['count']);
        $this->assertEquals($CFG->commentsperpage, $result['perpage']);
        $this->assertEquals($ids[0], $result['comments'][0]['id']); // Comments order older first.

        // Next page.
        $result = core_comment_external::get_comments($contextlevel, $instanceid, $component, $itemid, $area, $page + 1, 'ASC');
        $result = external_api::clean_returnvalue(core_comment_external::get_comments_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['comments']);
        $this->assertEquals(2, $result['count']);
        $this->assertEquals($CFG->commentsperpage, $result['perpage']);
        $this->assertEquals($ids[1], $result['comments'][0]['id']);
    }

    /**
     * Test add_comments not enabled site level
     */
    public function test_add_comments_not_enabled_site_level() {
        global $CFG;
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Try to add a comment, as student 1, when comments is disabled at site level.
        $this->setUser($student1);
        $CFG->usecomments = false;

        $this->expectException(comment_exception::class);
        core_comment_external::add_comments([
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ]
        ]);
    }

    /**
     * Test add_comments not enabled module level
     */
    public function test_add_comments_not_enabled_module_level() {
        global $DB;
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Disable comments for the module.
        $DB->set_field('data', 'comments', 0, array('id' => $module1->id));

        // Verify we can't add a comment.
        $this->setUser($student1);
        $this->expectException(comment_exception::class);
        core_comment_external::add_comments([
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ]
        ]);
    }

    /**
     * Test add_comments
     */
    public function test_add_comments_single() {
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Add a comment as student 1.
        $this->setUser($student1);
        $result = core_comment_external::add_comments([
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ]
        ]);
        $result = external_api::clean_returnvalue(core_comment_external::add_comments_returns(), $result);

        // Verify the result contains 1 result having the correct structure.
        $this->assertCount(1, $result);

        $expectedkeys = [
            'id',
            'content',
            'format',
            'timecreated',
            'strftimeformat',
            'profileurl',
            'fullname',
            'time',
            'avatar',
            'userid',
            'delete',
        ];
        foreach ($expectedkeys as $key) {
            $this->assertArrayHasKey($key, $result[0]);
        }
    }

    /**
     * Test add_comments when one of the comments contains invalid data and cannot be created.
     *
     * This simply verifies that the entire operation fails.
     */
    public function test_add_comments_multiple_contains_invalid() {
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Try to create some comments as student 1, but provide a bad area for the second comment.
        $this->setUser($student1);
        $this->expectException(comment_exception::class);
        core_comment_external::add_comments([
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ],
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'def',
                'itemid' => $recordid,
                'area' => 'badarea'
            ],
        ]);
    }

    /**
     * Test add_comments when one of the comments contains invalid data and cannot be created.
     *
     * This simply verifies that the entire operation fails.
     */
    public function test_add_comments_multiple_all_valid() {
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Try to create some comments as student 1.
        $this->setUser($student1);
        $inputdata = [
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ],
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'def',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ]
        ];
        $result = core_comment_external::add_comments($inputdata);
        $result = external_api::clean_returnvalue(core_comment_external::add_comments_returns(), $result);

        // Two comments should have been created.
        $this->assertCount(2, $result);

        // The content for each comment should come back formatted.
        foreach ($result as $index => $comment) {
            $formatoptions = array('overflowdiv' => true, 'blanktarget' => true);
            $expectedcontent = format_text($inputdata[$index]['content'], FORMAT_MOODLE, $formatoptions);
            $this->assertEquals($expectedcontent, $comment['content']);
        }
    }

    /**
     * Test add_comments invalid area
     */
    public function test_add_comments_invalid_area() {
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Try to create a comment with an invalid area, verifying failure.
        $this->setUser($student1);
        $comments = [
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'spaghetti'
            ]
        ];
        $this->expectException(comment_exception::class);
        core_comment_external::add_comments($comments);
    }

    /**
     * Test delete_comment invalid comment.
     */
    public function test_delete_comments_invalid_comment_id() {
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();
        $this->setUser($student1);

        $this->expectException(comment_exception::class);
        core_comment_external::delete_comments([-1, 0]);
    }

    /**
     * Test delete_comment own user.
     */
    public function test_delete_comments_own_user() {
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Create a few comments as student 1.
        $this->setUser($student1);
        $result = core_comment_external::add_comments([
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ],
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'def',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ]
        ]);
        $result = external_api::clean_returnvalue(core_comment_external::add_comments_returns(), $result);

        // Delete those comments we just created.
        $result = core_comment_external::delete_comments([
            $result[0]['id'],
            $result[1]['id']
        ]);
        $result = external_api::clean_returnvalue(core_comment_external::delete_comments_returns(), $result);
        $this->assertEquals([], $result);
    }

    /**
     * Test delete_comment other student.
     */
    public function test_delete_comment_other_student() {
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Create a comment as the student.
        $this->setUser($student1);
        $result = core_comment_external::add_comments([
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ]
        ]);
        $result = external_api::clean_returnvalue(core_comment_external::add_comments_returns(), $result);

        // Now, as student 2, try to delete the comment made by student 1. Verify we can't.
        $this->setUser($student2);
        $this->expectException(comment_exception::class);
        core_comment_external::delete_comments([$result[0]['id']]);
    }

    /**
     * Test delete_comment as teacher.
     */
    public function test_delete_comments_as_teacher() {
        [$module1, $recordid, $teacher1, $student1, $student2] = $this->setup_course_and_users_basic();

        // Create a comment as the student.
        $this->setUser($student1);
        $result = core_comment_external::add_comments([
            [
                'contextlevel' => 'module',
                'instanceid' => $module1->cmid,
                'component' => 'mod_data',
                'content' => 'abc',
                'itemid' => $recordid,
                'area' => 'database_entry'
            ]
        ]);
        $result = external_api::clean_returnvalue(core_comment_external::add_comments_returns(), $result);

        // Verify teachers can delete the comment.
        $this->setUser($teacher1);
        $result = core_comment_external::delete_comments([$result[0]['id']]);
        $result = external_api::clean_returnvalue(core_comment_external::delete_comments_returns(), $result);
        $this->assertEquals([], $result);
    }
}
