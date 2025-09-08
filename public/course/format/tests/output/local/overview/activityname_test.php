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

namespace core_courseformat\output\local\overview;

/**
 * Tests for the activityname class in the course format output.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(activityname::class)]
final class activityname_test extends \advanced_testcase {
    /**
     * Test the exportable interface implementation.
     * @return void
     */
    public function test_get_exporter(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');

        $mod = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($mod->cmid);

        $this->setUser($user);

        $source = new activityname($cm);
        $expectedclass = \core_courseformat\external\activityname_exporter::class;

        $exporter = $source->get_exporter();
        $this->assertInstanceOf($expectedclass, $exporter);

        $structure = activityname::get_read_structure();
        $this->assertInstanceOf(\core_external\external_single_structure::class, $structure);
        $this->assertEquals(
            $expectedclass::get_read_structure(),
            $structure,
        );

        $structure = activityname::read_properties_definition();
        $this->assertEquals(
            $expectedclass::read_properties_definition(),
            $structure,
        );
    }

    /**
     * Test the get_error_messages method.
     */
    public function test_get_error_messages(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');

        $mod = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($mod->cmid);

        $this->setUser($user);

        $source = new activityname($cm);
        $source->set_nogroupserror(true);
        $errormessages = $source->get_error_messages();
        $this->assertEquals(
            [get_string('overview_nogroups_error', 'course')],
            $errormessages,
        );

        $source = new activityname($cm);
        $errormessages = $source->get_error_messages();
        $this->assertEquals(
            [],
            $errormessages,
        );
    }
}
