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

namespace core\router\parameters;

use core\exception\not_found_exception;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use stdClass;

/**
 * Tests for the Section Path parameter.
 *
 * @package    core
 * @copyright  Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\parameters\path_section
 */
final class path_section_test extends route_testcase {
    public function test_section_id(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(1);
        $context = \context_course::instance($course->id);

        $param = new path_section();
        $request = new ServerRequest('GET', '/course/sections/' . $section->id . '/restricted');
        $newrequest = $param->add_attributes_for_parameter_value($request, $section->id);

        $this->assertInstanceOf(stdClass::class, $newrequest->getAttribute('section'));
        $this->assertInstanceOf(\core\context\course::class, $newrequest->getAttribute('coursecontext'));

        $this->assertEquals($section->id, $newrequest->getAttribute('section')->id);
        $this->assertEquals($context->id, $newrequest->getAttribute('coursecontext')->id);
    }

    /**
     * Tests for when a section was not found.
     */
    public function test_section_not_found(): void {
        $this->resetAfterTest();

        $param = new path_section();

        $course = $this->getDataGenerator()->create_course();
        $sectionid = 9999;

        $request = new ServerRequest('GET', '/course/sections/' . $sectionid . '/restricted');

        $this->expectException(not_found_exception::class);
        $param->add_attributes_for_parameter_value($request, $sectionid);
    }
}
