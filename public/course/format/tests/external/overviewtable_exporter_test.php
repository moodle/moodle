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
 * Tests for overviewtable_exporter.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overviewtable_exporter::class)]
final class overviewtable_exporter_test extends \advanced_testcase {
    /**
     * Test export method.
     */
    public function test_export(): void {
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

        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $source = new overviewtable($course, 'assign');

        $exporter = new overviewtable_exporter($source, ['context' => \core\context\course::instance($course->id)]);
        $data = $exporter->export($renderer);

        // Get data for validations.
        $exporteddata = $source->export_for_external();

        $this->assertObjectHasProperty('headers', $data);
        $this->assertObjectHasProperty('courseid', $data);
        $this->assertObjectHasProperty('hasintegration', $data);
        $this->assertObjectHasProperty('activities', $data);
        $this->assertCount(4, get_object_vars($data));

        foreach ($exporteddata->headers as $index => $header) {
            $this->assertEquals($header->name, $data->headers[$index]->name);
            $this->assertEquals($header->key, $data->headers[$index]->key);
            $this->assertEquals($header->align, $data->headers[$index]->align);
            $this->assertCount(3, get_object_vars($data->headers[$index]));
        }
        $this->assertEquals(count($exporteddata->headers), count($data->headers));

        $this->assertEquals($exporteddata->course->id, $data->courseid);
        $this->assertEquals($exporteddata->hasintegration, $data->hasintegration);
        $this->assertEquals(count($exporteddata->activities), count($data->activities));

        foreach ($data->activities as $activity) {
            $this->assertObjectHasProperty('cmid', $activity);
            $this->assertObjectHasProperty('contextid', $activity);
            $this->assertObjectHasProperty('modname', $activity);
            $this->assertObjectHasProperty('name', $activity);
            $this->assertObjectHasProperty('url', $activity);
            $this->assertObjectHasProperty('haserror', $activity);
            $this->assertObjectHasProperty('items', $activity);
            $this->assertCount(7, get_object_vars($activity));

            foreach ($activity->items as $item) {
                $this->assertObjectHasProperty('name', $item);
                $this->assertObjectHasProperty('key', $item);
                // In the overview table the key should not be empty.
                $this->assertNotEmpty($item->key);
                $this->assertObjectHasProperty('contenttype', $item);
                $this->assertObjectHasProperty('exportertype', $item);
                $this->assertObjectHasProperty('alertlabel', $item);
                $this->assertObjectHasProperty('alertcount', $item);
                $this->assertObjectHasProperty('contentjson', $item);
                $this->assertObjectHasProperty('extrajson', $item);
                $this->assertCount(8, get_object_vars($item));
            }
        }

        $modinfo = get_fast_modinfo($course);

        $cm = $modinfo->get_cm($mods['assign1']->cmid);
        $this->assertEquals($cm->id, $data->activities[0]->cmid);
        $this->assertEquals($cm->context->id, $data->activities[0]->contextid);
        $this->assertEquals($cm->modname, $data->activities[0]->modname);
        $this->assertEquals($cm->name, $data->activities[0]->name);
        $this->assertEquals($cm->url->out(false), $data->activities[0]->url);
        $this->assertFalse($data->activities[0]->haserror);
        $this->assertCount(count($data->headers), $data->activities[0]->items);

        $cm = $modinfo->get_cm($mods['assign2']->cmid);
        $this->assertEquals($cm->id, $data->activities[1]->cmid);
        $this->assertEquals($cm->context->id, $data->activities[1]->contextid);
        $this->assertEquals($cm->modname, $data->activities[1]->modname);
        $this->assertEquals($cm->name, $data->activities[1]->name);
        $this->assertEquals($cm->url->out(false), $data->activities[1]->url);
        $this->assertFalse($data->activities[1]->haserror);
        $this->assertCount(count($data->headers), $data->activities[1]->items);
    }
}
