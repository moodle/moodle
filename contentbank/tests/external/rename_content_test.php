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
 * Core content bank external functions tests.
 *
 * @package    core_contentbank
 * @category   external
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.9
 */

namespace core_contentbank\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use external_api;

/**
 * Core content bank external functions tests.
 *
 * @package    core_contentbank
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_contentbank\external
 */
class rename_content_testcase extends \externallib_advanced_testcase {

    /**
     * Data provider for test_rename_content.
     *
     * @return  array
     */
    public function rename_content_provider() {
        return [
            'Standard name' => ['New name', 'New name', true],
            'Name with digits' => ['Today is 17/04/2017', 'Today is 17/04/2017', true],
            'Name with symbols' => ['Follow us: @moodle', 'Follow us: @moodle', true],
            'Name with tags' => ['This is <b>bold</b>', 'This is bold', true],
            'Long name' => [str_repeat('a', 100), str_repeat('a', 100), true],
            'Too long name' => [str_repeat('a', 300), str_repeat('a', 255), true],
            'Empty name' => ['', 'Test content ', false],
            'Blanks only' => ['  ', 'Test content ', false],
            'Zero name' => ['0', '0', true],
        ];
    }

    /**
     * Test the behaviour of rename_content() for users with permission.
     *
     * @dataProvider    rename_content_provider
     * @param   string  $newname    The name to set
     * @param   string   $expectedname   The name result
     * @param   bool   $expectedresult   The bolean result expected when renaming
     *
     * @covers ::execute
     */
    public function test_rename_content_with_permission(string $newname, string $expectedname, bool $expectedresult) {
        global $DB;
        $this->resetAfterTest();

        // Create users.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        $teacher = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->role_assign($roleid, $teacher->id);
        $this->setUser($teacher);

        // Add some content to the content bank as teacher.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 1, $teacher->id);
        $content = array_shift($contents);

        $oldname = $content->get_name();

        // Call the WS and check the content is renamed as expected.
        $result = rename_content::execute($content->get_id(), $newname);
        $result = external_api::clean_returnvalue(rename_content::execute_returns(), $result);
        $this->assertEquals($expectedresult, $result['result']);
        $record = $DB->get_record('contentbank_content', ['id' => $content->get_id()]);
        $this->assertEquals($expectedname, $record->name);

        // Call the WS using an unexisting contentid and check an error is thrown.
        $this->expectException(\invalid_response_exception::class);
        $result = rename_content::execute_returns($content->get_id() + 1, $oldname);
        $result = external_api::clean_returnvalue(rename_content::execute_returns(), $result);
        $this->assertFalse($result['result']);
    }

    /**
     * Test the behaviour of rename_content() for users with permission.
     *
     * @covers ::execute
     */
    public function test_rename_content_without_permission() {
        global $DB;
        $this->resetAfterTest();

        // Create users.
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Add some content to the content bank as teacher.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 1, $teacher->id);
        $content = array_shift($contents);

        $oldname = $content->get_name();
        $newname = 'New name';

        // Call the WS and check the content has not been renamed by the student.
        $this->setUser($student);
        $result = rename_content::execute($content->get_id(), $newname);
        $result = external_api::clean_returnvalue(rename_content::execute_returns(), $result);
        $this->assertFalse($result['result']);
        $record = $DB->get_record('contentbank_content', ['id' => $content->get_id()]);
        $this->assertEquals($oldname, $record->name);
        $this->assertNotEquals($newname, $record->name);
    }
}
