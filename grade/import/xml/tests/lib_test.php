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

namespace gradeimport_xml;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/import/xml/lib.php');

/**
 * Unit tests for XML grade import helper functions.
 *
 * @package    gradeimport_xml
 * @category   test
 * @copyright  2026 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {

    /**
     * Ensure inaccessible URL download throws a Moodle exception.
     *
     * @covers ::gradeimport_xml_fetch_and_commit
     */
    public function test_grade_import_xml_throws_on_inaccessible_url(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $missingurl = 'file://example.com/fake.xml';

        try {
            gradeimport_xml_fetch_and_commit($course, $missingurl, false, false);
            $this->fail();
        } catch (\moodle_exception $e) {
            $this->assertSame('cannotreadfile', $e->errorcode);
        }
    }

    /**
     * Ensure invalid XML content throws import failure exception.
     *
     * @covers ::gradeimport_xml_fetch_and_commit
     */
    public function test_grade_import_xml_throws_on_unexpected_xml_structure(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        // Valid XML response over HTTP, but not in the expected grade import format.
        $xmlurl = $this->getExternalTestFileUrl('/rsstest.xml');

        try {
            gradeimport_xml_fetch_and_commit($course, $xmlurl, false, false);
            $this->fail();
        } catch (\moodle_exception $e) {
            $this->assertSame('errorduringimport', $e->errorcode);
        }
    }
}
