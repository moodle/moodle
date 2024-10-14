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
 * Generator testcase for the gradingforum_rubric generator.
 *
 * @package    core_grading
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_grading;

use advanced_testcase;
use context_module;
use gradingform_controller;
use gradingform_rubric_controller;

/**
 * Generator testcase for the core_grading generator.
 *
 * @package    core_grading
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator_test extends advanced_testcase {

    /**
     * Test gradingform controller creation.
     */
    public function test_create_instance(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $gradinggenerator = $generator->get_plugin_generator('core_grading');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // The assignment module has an itemumber 0 which is an advanced grading area called 'submissions'.
        $component = 'mod_assign';
        $area = 'submissions';
        $controller = $gradinggenerator->create_instance($context, $component, $area, 'rubric');

        // This should be a rubric.
        $this->assertInstanceOf(gradingform_controller::class, $controller);
        $this->assertInstanceOf(gradingform_rubric_controller::class, $controller);

        $this->assertEquals($context, $controller->get_context());
        $this->assertEquals($component, $controller->get_component());
        $this->assertEquals($area, $controller->get_area());
    }
}
