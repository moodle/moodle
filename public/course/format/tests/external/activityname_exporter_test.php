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

use core_courseformat\output\local\overview\overviewtable;

/**
 * Tests for activityname_exporter.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(activityname_exporter::class)]
final class activityname_exporter_test extends \advanced_testcase {
    /**
     * Test export method.
     */
    public function test_export(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');

        $mod = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        $this->setUser($user);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($mod->cmid);

        $format = course_get_format($course);
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $source = new \core_courseformat\output\local\overview\activityname($cm);

        $exporter = new activityname_exporter($source, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('activityname', $data);
        $this->assertObjectHasProperty('activityurl', $data);
        $this->assertObjectHasProperty('hidden', $data);
        $this->assertObjectHasProperty('stealth', $data);
        $this->assertObjectHasProperty('sectiontitle', $data);
        $this->assertObjectHasProperty('errormessages', $data);
        $this->assertObjectHasProperty('available', $data);
        $this->assertCount(7, get_object_vars($data));

        $expected = [
            'activityname' => \core_external\util::format_string($cm->name, $cm->context, true),
            'activityurl' => $cm->url->out(false),
            'hidden' => empty($cm->visible),
            'stealth' => $cm->is_stealth(),
            'sectiontitle' => $format->get_section_name($cm->get_section_info()),
            'errormessages' => [],
            'available' => overviewtable::is_cm_available($cm),
        ];

        foreach ($expected as $property => $value) {
            $this->assertEquals($value, $data->$property, "Property '$property' does not match expected value.");
        }
    }

    /**
     * Test export with no groups error.
     */
    public function test_export_nogroup_message(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');

        $mod = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        $this->setUser($user);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($mod->cmid);

        $format = course_get_format($course);
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $source = new \core_courseformat\output\local\overview\activityname($cm);
        $source->set_nogroupserror(true);

        $exporter = new activityname_exporter($source, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $expected = [
            'activityname' => \core_external\util::format_string($cm->name, $cm->context, true),
            'activityurl' => $cm->url->out(false),
            'hidden' => empty($cm->visible),
            'stealth' => $cm->is_stealth(),
            'sectiontitle' => $format->get_section_name($cm->get_section_info()),
            'errormessages' => [
                get_string('overview_nogroups_error', 'course'),
            ],
        ];

        foreach ($expected as $property => $value) {
            $this->assertEquals($value, $data->$property, "Property '$property' does not match expected value.");
        }
    }
}
