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
 * Unit Tests for the Moodle Content Writer.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\writer;

/**
 * Tests for the \core_privacy API's moodle_content_writer functionality.
 *
 * Note: The \core_privacy\tests\request\content_writer will be used for these tests.
 * This content writer has additional sugar methods for fetching infromation which are not part of the standard
 * content_writer interface.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\request\writer
 */
class writer_test extends advanced_testcase {
    /**
     * Ensure that the writer is cleared away as appropriate after each
     * test.
     */
    public function tearDown(): void {
        writer::reset();
    }

    /**
     * Test that calling with_context multiple times will return the same write instance.
     *
     * @covers ::with_context
     */
    public function test_with_context() {
        $writer = writer::with_context(\context_system::instance());

        $this->assertSame($writer, writer::with_context(\context_system::instance()));
    }

    /**
     * Test that calling with_context multiple times will return the same write instance.
     *
     * @covers ::with_context
     */
    public function test_with_context_different_context_same_instance() {
        $writer = writer::with_context(\context_system::instance());

        $this->assertSame($writer, writer::with_context(\context_user::instance(\core_user::get_user_by_username('admin')->id)));
    }

    /**
     * Test that calling writer::reset() causes a new copy of the writer to be returned.
     *
     * @covers ::reset
     */
    public function test_reset() {
        $writer = writer::with_context(\context_system::instance());
        writer::reset();

        $this->assertNotSame($writer, writer::with_context(\context_system::instance()));
    }

    /**
     * Test that the export_user_preference calls the writer against the system context.
     *
     * @covers ::export_user_preference
     */
    public function test_export_user_preference_sets_system_context() {
        $writer = writer::with_context(\context_user::instance(\core_user::get_user_by_username('admin')->id));

        writer::export_user_preference('core_test', 'key', 'value', 'description');

        $this->assertSame(\context_system::instance(), $writer->get_current_context());
    }
}
