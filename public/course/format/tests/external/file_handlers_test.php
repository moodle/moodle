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

namespace core_courseformat\external;

use core_course\dndupload_handler;
use core_external\external_api;

/**
 * Tests for the file_hanlders class.
 *
 * @package    core_course
 * @category   test
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(file_handlers::class)]
final class file_handlers_test extends \core_external\tests\externallib_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void { // phpcs:ignore
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
    }

    /**
     * Test the behaviour of get_state::execute().
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 3, 'format' => 'topics']);
        $this->setAdminUser();

        $result = file_handlers::execute($course->id);
        $result = external_api::clean_returnvalue(file_handlers::execute_returns(), $result);

        $handlers = new dndupload_handler($course, null);
        $expected = $handlers->get_js_data();

        $this->assertCount(count($expected->filehandlers), $result);
        foreach ($expected->filehandlers as $key => $handler) {
            $tocompare = $result[$key];
            $this->assertEquals($handler->extension, $tocompare['extension']);
        }
    }

    /**
     * Test the behaviour of get_state::execute() in a wrong course.
     */
    public function test_execute_wrong_course(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 3, 'format' => 'topics']);
        $this->setAdminUser();

        $this->expectException('dml_missing_record_exception');
        $result = file_handlers::execute(-1);
        $result = external_api::clean_returnvalue(file_handlers::execute_returns(), $result);
    }
}
