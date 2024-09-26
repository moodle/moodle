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

namespace core_files\redactor;

/**
 * Tests for file redactor manager class.
 *
 * @package   core_files
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_files\redactor\manager
 */
final class manager_test extends \advanced_testcase {
    /**
     * Helper to get a manager with a dummy file service.
     *
     * @return \core_files\redactor\manager
     */
    private function get_manager_with_dummy_file_service(): manager {
        $manager = $this->getMockBuilder(\core_files\redactor\manager::class)
            ->onlyMethods(['get_service_classnames'])
            ->getMock();

        $manager->method('get_service_classnames')
            ->willReturn([\core_files\tests\redactor\services\dummy_file_service::class]);

        return $manager;
    }

    /**
     * Test file redaction by path.
     */
    public function test_redact_file(): void {

        $manager = $this->get_manager_with_dummy_file_service();

        // Test redaction for a binary (not supported).
        $redactedfile = $manager->redact_file("application/binary", "/path/to/binary");
        $this->assertNull($redactedfile);
        $redactedfile = $manager->redact_file_content("application/binary", "Binary content here");
        $this->assertNull($redactedfile);

        // Test redaction for an image.
        $redactedfile = $manager->redact_file("image/jpeg", "/path/to/image.jpg");
        $this->assertEquals('/redacted/path/to/image.jpg', $redactedfile);
        $redactedfile = $manager->redact_file_content("image/jpeg", "Example picture goes here");
        $this->assertEquals('redacted:Example picture goes here', $redactedfile);
    }
}
