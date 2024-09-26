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

namespace core_files\hook;
use coding_exception;

/**
 * Tests for before_file_created hook.
 *
 * @package    core_files
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_files\hook\before_file_created
 */
final class before_file_created_test extends \advanced_testcase {
    public function test_init_with_file_and_content_throws_exception(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only one of $filepath or $filecontent can be set');
        new before_file_created(new \stdClass(), 'path', 'content');
    }

    public function test_init_with_no_file_and_no_content_throws_exception(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Either $filepath or $filecontent must be set');
        new before_file_created(new \stdClass());
    }

    public function test_content_updated(): void {
        $hook = new before_file_created(new \stdClass(), filecontent: 'data');
        $this->assertFalse($hook->has_changed());
        $this->assertTrue($hook->has_filecontent());
        $this->assertFalse($hook->has_filepath());

        $hook->update_filecontent('data');
        $this->assertEquals('data', $hook->get_filecontent());
        $this->assertFalse($hook->has_changed());
        $this->assertNull($hook->get_filepath());

        $hook->update_filecontent('new data');
        $this->assertEquals('new data', $hook->get_filecontent());
        $this->assertTrue($hook->has_changed());
        $this->assertNull($hook->get_filepath());
    }

    public function test_file_updated(): void {
        $initialdata = self::get_fixture_path('core_files', 'hook/before_file_created_hooks.php');
        $newdata = __FILE__;

        $hook = new before_file_created(
            new \stdClass(),
            filepath: $initialdata,
        );
        $this->assertFalse($hook->has_changed());
        $this->assertFalse($hook->has_filecontent());
        $this->assertTrue($hook->has_filepath());

        $hook->update_filepath($initialdata);
        $this->assertNull($hook->get_filecontent());
        $this->assertEquals($initialdata, $hook->get_filepath());
        $this->assertFalse($hook->has_changed());

        $hook->update_filepath($newdata);
        $this->assertNull($hook->get_filecontent());
        $this->assertEquals($newdata, $hook->get_filepath());
        $this->assertTrue($hook->has_changed());
    }

    public function test_cannot_update_file_when_content_set(): void {
        $hook = new before_file_created(new \stdClass(), filecontent: 'data');
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Cannot update file path when the file path is not set');
        $hook->update_filepath('new path');
    }

    public function test_cannot_update_content_when_file_ste(): void {
        $hook = new before_file_created(new \stdClass(), filepath: __FILE__);
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Cannot update file content when the file content is not set');
        $hook->update_filecontent('new path');
    }
}
