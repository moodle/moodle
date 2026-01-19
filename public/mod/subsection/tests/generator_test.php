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

namespace mod_subsection;

/**
 * Generator tests class for mod_subsection.
 *
 * @package    mod_subsection
 * @category   test
 * @copyright  2026 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(mod_subsection_generator::class)]
final class generator_test extends \advanced_testcase {
    /**
     * Test on subsection creation.
     */
    public function test_create_instance(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Create one subsection activity (empty summary by default).
        $this->assertFalse($DB->record_exists('subsection', ['course' => $course->id]));
        $activity1 = $this->getDataGenerator()->create_module('subsection', ['course' => $course]);
        $records = $DB->get_records('subsection', ['course' => $course->id], 'id');
        $this->assertEquals(1, count($records));
        $this->assertTrue(array_key_exists($activity1->id, $records));
        $section = $DB->get_record('course_sections', [
            'component' => 'mod_subsection',
            'itemid' => $activity1->id,
        ]);
        $this->assertEquals('', $section->summary);

        // Create another subsection activity with a specific summary.
        $summarytext = 'This is a test summary';
        $activity2 = $this->getDataGenerator()->create_module('subsection', [
            'course' => $course,
            'summary' => $summarytext,
        ]);
        $records = $DB->get_records('subsection', ['course' => $course->id], 'id');
        $this->assertEquals(2, count($records));
        $this->assertTrue(array_key_exists($activity2->id, $records));
        // Check that the delegated section summary has been correctly set.
        $section = $DB->get_record('course_sections', [
            'component' => 'mod_subsection',
            'itemid' => $activity2->id,
        ]);
        $this->assertEquals($summarytext, $section->summary);
    }
}
