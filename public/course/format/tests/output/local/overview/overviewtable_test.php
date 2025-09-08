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
 * Tests for courseformat
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overviewtable::class)]
final class overviewtable_test extends \advanced_testcase {
    /**
     * Test export_for_external method.
     */
    public function test_export_for_external(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Ensure the course is fully loaded.
        $course = get_course($course->id);

        $mods = [
            'assign1' => $this->getDataGenerator()->create_module('assign', ['course' => $course->id]),
            'assign2' => $this->getDataGenerator()->create_module('assign', ['course' => $course->id]),
        ];

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');
        $this->setUser($user);

        $modinfo = get_fast_modinfo($course);

        $cms = [
            'assign1' => $modinfo->get_cm($mods['assign1']->cmid),
            'assign2' => $modinfo->get_cm($mods['assign2']->cmid),
        ];

        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $overviewtable = new overviewtable($course, 'assign');

        $data = $overviewtable->export_for_external();

        $templatedata = $overviewtable->export_for_template($renderer);

        $this->assertEquals($course, $data->course);
        $this->assertEquals(true, $data->hasintegration);

        foreach ($templatedata->headers as $header) {
            $this->assertObjectHasProperty('name', $header);
            $this->assertObjectHasProperty('key', $header);
            $this->assertObjectHasProperty('textalign', $header);
            $this->assertObjectHasProperty('align', $header);
            $this->assertCount(4, get_object_vars($header));
        }

        $this->assertCount(4, get_object_vars($data));

        $this->assertCount(2, $data->activities);

        $this->assertEquals($cms['assign1'], $data->activities[0]->cm);
        $this->assertEquals(false, $data->activities[0]->haserror);
        $this->assertCount(count($data->headers), $data->activities[0]->items);

        foreach ($data->headers as $index => $header) {
            foreach ($data->activities as $activity) {
                $this->assertCount(count($data->headers), $activity->items);
                $this->assertEquals($header->name, $activity->items[$index]->get_name());
                $this->assertEquals($header->key, $activity->items[$index]->get_key());
            }
        }
    }
}
