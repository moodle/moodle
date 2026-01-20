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
use core\router\parameters\path_module;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use stdClass;

/**
 * Tests for the Module Path paraemter.
 *
 * @package    core
 * @copyright  Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\parameters\path_module
 */
final class path_module_test extends route_testcase {
    public function test_module_id(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $mod = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $modcontext = \context_module::instance($mod->cmid);

        $param = new path_module();
        $request = new ServerRequest('GET', '/course/' . $course->id . '/restricted/' . $mod->cmid);
        $newrequest = $param->add_attributes_for_parameter_value($request, $mod->cmid);

        $this->assertInstanceOf(stdClass::class, $newrequest->getAttribute('cm'));
        $this->assertInstanceOf(\core\context\module::class, $newrequest->getAttribute('cmcontext'));

        $this->assertEquals($mod->cmid, $newrequest->getAttribute('cm')->id);
        $this->assertEquals($modcontext->id, $newrequest->getAttribute('cmcontext')->id);
    }

    /**
     * Tests for when a module was not found.
     */
    public function test_module_not_found(): void {
        $this->resetAfterTest();

        $param = new path_module();

        $course = $this->getDataGenerator()->create_course();
        $modid = 9999;

        $request = new ServerRequest('GET', '/course/' . $course->id . '/restricted/' . $modid);

        $this->expectException(not_found_exception::class);
        $param->add_attributes_for_parameter_value($request, $modid);
    }
}
