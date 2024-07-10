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

namespace tool_generator\local\testscenario;

/**
 * Tests for runner class.
 *
 * @package tool_generator
 * @copyright 2023 Ferran Recio <ferran@moodel.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_generator\local\testscenario\runner
 */
class runner_test extends \advanced_testcase {

    /**
     * Test for parse_feature.
     * @covers ::parse_feature
     * @covers ::execute
     */
    public function test_parse_and_execute_feature(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Call the init method to include all behat libraries and attributes.
        $runner = new runner();
        $runner->init();

        $featurefile = $CFG->dirroot . '/admin/tool/generator/tests/fixtures/testscenario/scenario.feature';
        $contents = file_get_contents($featurefile);
        $feature = $runner->parse_feature($contents);

        $this->assertEquals(2, count($feature->get_scenarios()));
        $this->assertEquals(7, count($feature->get_all_steps()));
        $this->assertTrue($feature->is_valid());

        $result = $runner->execute($feature);
        $this->assertTrue($result);

        // Validate everything is created.
        $this->assertEquals(
            1,
            $DB->count_records('course', ['shortname' => 'C1'])
        );
        $course = $DB->get_record('course', ['shortname' => 'C1']);
        $this->assertEquals(
            2,
            $DB->count_records('course_modules', ['course' => $course->id])
        );
        $this->assertEquals(
            1,
            $DB->count_records('user', ['firstname' => 'Teacher'])
        );
        $this->assertEquals(
            5,
            $DB->count_records('user', ['firstname' => 'Student'])
        );
        $context = \context_course::instance($course->id);
        $this->assertEquals(
            6,
            $DB->count_records('role_assignments', ['contextid' => $context->id])
        );
    }

    /**
     * Test for parse_feature.
     * @covers ::parse_feature
     * @covers ::execute
     */
    public function test_parse_and_execute_wrong_feature(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Call the init method to include all behat libraries and attributes.
        $runner = new runner();
        $runner->init();

        $featurefile = $CFG->dirroot . '/admin/tool/generator/tests/fixtures/testscenario/scenario_wrongstep.feature';
        $contents = file_get_contents($featurefile);
        $feature = $runner->parse_feature($contents);

        $this->assertEquals(1, count($feature->get_scenarios()));
        $this->assertEquals(3, count($feature->get_all_steps()));
        $this->assertFalse($feature->is_valid());

        $result = $runner->execute($feature);
        $this->assertFalse($result);
        $this->assertEquals(0, $DB->count_records('course', ['shortname' => 'C1']));
    }

    /**
     * Test for parse_feature.
     * @covers ::parse_feature
     * @covers ::execute
     */
    public function test_parse_and_execute_outline_feature(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Call the init method to include all behat libraries and attributes.
        $runner = new runner();
        $runner->init();

        $featurefile = $CFG->dirroot . '/admin/tool/generator/tests/fixtures/testscenario/scenario_outline.feature';
        $contents = file_get_contents($featurefile);
        $feature = $runner->parse_feature($contents);

        $this->assertEquals(3, count($feature->get_scenarios()));
        $this->assertEquals(3, count($feature->get_all_steps()));
        $this->assertTrue($feature->is_valid());

        $result = $runner->execute($feature);
        $this->assertTrue($result);

        // Validate everything is created.
        $course = $DB->get_record('course', ['shortname' => 'C1']);
        $this->assertEquals('C1', $course->shortname);
        $this->assertEquals('Course 1', $course->fullname);
        $course = $DB->get_record('course', ['shortname' => 'C2']);
        $this->assertEquals('C2', $course->shortname);
        $this->assertEquals('Course 2', $course->fullname);
        $course = $DB->get_record('course', ['shortname' => 'C3']);
        $this->assertEquals('C3', $course->shortname);
        $this->assertEquals('Course 3', $course->fullname);
    }
}
