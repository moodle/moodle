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

namespace core_contentbank\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_external\external_api;

/**
 * Content bank's copy content external function tests.
 *
 * @package    core_contentbank
 * @copyright  2023 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_contentbank\external\copy_content
 */
class copy_content_test extends \externallib_advanced_testcase {

    /**
     * Test the behaviour of copy_content() for users with permission.
     *
     * @covers ::execute
     */
    public function test_copy_content_with_permission(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create users.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        $teacher = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->role_assign($roleid, $teacher->id);
        $this->setUser($teacher);

        // Add some content to the content bank as teacher.
        $filename = 'filltheblanks.h5p';
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/' . $filename;
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_h5p', 1, $teacher->id, null, true, $filepath);
        $content = array_shift($contents);

        $oldname = $content->get_name();

        $newname = 'New name';

        // Call the WS and check the content is copied as expected.
        $result = copy_content::execute($content->get_id(), $newname);
        $result = external_api::clean_returnvalue(copy_content::execute_returns(), $result);
        $this->assertNotEmpty($result['id']);
        $record = $DB->get_record('contentbank_content', ['id' => $result['id']]);
        $this->assertEquals($newname, $record->name);

        $record = $DB->get_record('contentbank_content', ['id' => $content->get_id()]);
        $this->assertEquals($oldname, $record->name);

        // Call the WS using an unexisting contentid and check an error is thrown.
        $this->expectException(\invalid_response_exception::class);
        $result = copy_content::execute_returns($content->get_id() + 1, $oldname);
        $result = external_api::clean_returnvalue(copy_content::execute_returns(), $result);
        $this->assertNotEmpty($result['warnings']);
    }

    /**
     * Test the behaviour of copy_content() for users with and without permission.
     *
     * @covers ::execute
     */
    public function test_copy_content(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create users.
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $teacher2 = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $manager = $this->getDataGenerator()->create_and_enrol($course, 'manager');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Add some content to the content bank as teacher.
        $coursecontext = \context_course::instance($course->id);
        $filename = 'filltheblanks.h5p';
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/' . $filename;
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_h5p', 1, $teacher->id, $coursecontext, true, $filepath);
        $content = array_shift($contents);

        $oldname = $content->get_name();
        $newname = 'New name';

        // Call the WS and check the teacher can copy his/her own content.
        $this->setUser($teacher);
        $result = copy_content::execute($content->get_id(), $newname);
        $result = external_api::clean_returnvalue(copy_content::execute_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $record = $DB->get_record('contentbank_content', ['id' => $result['id']]);
        $this->assertEquals($newname, $record->name);

        // Call the WS and check the content has not been copied by the student.
        $this->setUser($student);
        $result = copy_content::execute($content->get_id(), $newname);
        $result = external_api::clean_returnvalue(copy_content::execute_returns(), $result);
        $this->assertNotEmpty($result['warnings']);
        $record = $DB->get_record('contentbank_content', ['id' => $content->get_id()]);
        $this->assertEquals($oldname, $record->name);
        $this->assertNotEquals($newname, $record->name);

        // Call the WS an check the content with empty name is not copied by the teacher.
        $this->setUser($teacher);
        $result = copy_content::execute($content->get_id(), '');
        $result = external_api::clean_returnvalue(copy_content::execute_returns(), $result);
        $this->assertNotEmpty($result['warnings']);

        // Call the WS and check a teacher cannot copy content from another teacher by default.
        $this->setUser($teacher2);
        $result = copy_content::execute($content->get_id(), 'New name 2');
        $result = external_api::clean_returnvalue(copy_content::execute_returns(), $result);
        $this->assertNotEmpty($result['warnings']);

        // Call the WS and check a manager can copy content from a teacher by default.
        $this->setUser($manager);
        $result = copy_content::execute($content->get_id(), $newname);
        $result = external_api::clean_returnvalue(copy_content::execute_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $record = $DB->get_record('contentbank_content', ['id' => $result['id']]);
        $this->assertEquals($newname, $record->name);
    }
}
