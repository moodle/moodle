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
 * Tests for parsedfeature class.
 *
 * @package tool_generator
 * @copyright 2023 Ferran Recio <ferran@moodel.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_generator\local\testscenario\parsedfeature
 */
class parsedfeature_test extends \advanced_testcase {
    /**
     * Get a parsed feature from a content.
     * @param string $content the feature content.
     * @return parsedfeature the parsed feature.
     */
    private function get_feature_from_content(string $content): parsedfeature {
        $runner = new runner();
        $runner->init();
        return $runner->parse_feature($content);
    }

    /**
     * Test for parse_feature.
     * @covers ::get_general_error
     * @covers ::add_scenario
     * @covers ::add_step
     */
    public function test_general_error(): void {
        $nosteps = get_string('testscenario_nosteps', 'tool_generator');
        $invalidfile = get_string('testscenario_invalidfile', 'tool_generator');

        $parsedfeature = new parsedfeature();
        $this->assertEquals($nosteps, $parsedfeature->get_general_error());

        $parsedfeature->add_scenario('Scenario', 'Test scenario');
        $this->assertEquals($nosteps, $parsedfeature->get_general_error());

        // Add some valid step.
        $extrafeature = $this->get_feature_from_content('Feature: Test feature
            Scenario: Create users
                Given the following "users" exist:
                    | username | firstname  | lastname | email              |
                    | teacher1 | Teacher    | Test1    | sample@example.com |
        ');
        $step = $extrafeature->get_all_steps()[0];
        $parsedfeature->add_step($step);
        $this->assertEquals('', $parsedfeature->get_general_error());

        // Only generator methods are allowed.
        $extrafeature = $this->get_feature_from_content('Feature: Test feature
            Scenario: Not generator
                Given I am in a course
        ');
        $step = $extrafeature->get_all_steps()[0];
        $parsedfeature->add_step($step);
        $this->assertEquals($invalidfile, $parsedfeature->get_general_error());
    }

    /**
     * Test for parse_feature.
     * @covers ::is_valid
     * @covers ::add_scenario
     * @covers ::add_step
     */
    public function test_is_valid(): void {
        $parsedfeature = new parsedfeature();
        $this->assertFalse($parsedfeature->is_valid());

        $parsedfeature->add_scenario('Scenario', 'Test scenario');
        $this->assertFalse($parsedfeature->is_valid());

        // Add some valid step.
        $extrafeature = $this->get_feature_from_content('Feature: Test feature
            Scenario: Create users
                Given the following "users" exist:
                    | username | firstname  | lastname | email              |
                    | teacher1 | Teacher    | Test1    | sample@example.com |
        ');
        $step = $extrafeature->get_all_steps()[0];
        $parsedfeature->add_step($step);
        $this->assertTrue($parsedfeature->is_valid());

        // Only generator methods are allowed.
        $extrafeature = $this->get_feature_from_content('Feature: Test feature
            Scenario: Not generator
                Given I am in a course
        ');
        $step = $extrafeature->get_all_steps()[0];
        $parsedfeature->add_step($step);
        $this->assertFalse($parsedfeature->is_valid());
    }

    /**
     * Test for ading steps into scenarios.
     * @covers ::add_step
     * @covers ::add_scenario
     * @covers ::get_all_steps
     * @covers ::get_scenarios
     */
    public function test_add_step(): void {
        $parsedfeature = new parsedfeature();
        $this->assertEquals(0, count($parsedfeature->get_all_steps()));

        $extrafeature = $this->get_feature_from_content('Feature: Test feature
            Scenario: Create users
                Given the following "users" exist:
                    | username | firstname  | lastname | email              |
                    | teacher1 | Teacher    | Test1    | sample@example.com |
        ');
        $step = $extrafeature->get_all_steps()[0];
        $parsedfeature->add_step($step);
        $this->assertEquals(1, count($parsedfeature->get_all_steps()));

        // Should create a default scenario.
        $scenarios = $parsedfeature->get_scenarios();
        $this->assertEquals(1, count($scenarios));
        $this->assertEquals('scenario', $scenarios[0]->type);
        $this->assertEquals('', $scenarios[0]->name);
        $this->assertEquals(1, count($scenarios[0]->steps));
        $this->assertEquals($step, $scenarios[0]->steps[0]);
        $this->assertEquals('', $scenarios[0]->error);

        // Add a second step.
        $extrafeature = $this->get_feature_from_content('Feature: Test feature
            Scenario: Not generator
                Given I am in a course
        ');
        $step2 = $extrafeature->get_all_steps()[0];
        $parsedfeature->add_step($step2);
        $scenarios = $parsedfeature->get_scenarios();
        $this->assertEquals(1, count($scenarios));
        $this->assertEquals('scenario', $scenarios[0]->type);
        $this->assertEquals('', $scenarios[0]->name);
        $this->assertEquals(2, count($scenarios[0]->steps));
        $this->assertEquals($step, $scenarios[0]->steps[0]);
        $this->assertEquals($step2, $scenarios[0]->steps[1]);
        $this->assertEquals('', $scenarios[0]->error);

        // Create a new scenario.
        $parsedfeature->add_scenario('scenario', 'Test scenario 2');
        $parsedfeature->add_step($step2);
        $scenarios = $parsedfeature->get_scenarios();
        $this->assertEquals(2, count($scenarios));
        // Scenario 1.
        $this->assertEquals('scenario', $scenarios[0]->type);
        $this->assertEquals('', $scenarios[0]->name);
        $this->assertEquals(2, count($scenarios[0]->steps));
        $this->assertEquals($step, $scenarios[0]->steps[0]);
        $this->assertEquals($step2, $scenarios[0]->steps[1]);
        $this->assertEquals('', $scenarios[0]->error);
        // Scenario 2.
        $this->assertEquals('scenario', $scenarios[1]->type);
        $this->assertEquals('Test scenario 2', $scenarios[1]->name);
        $this->assertEquals(1, count($scenarios[1]->steps));
        $this->assertEquals($step2, $scenarios[1]->steps[0]);
        $this->assertEquals('', $scenarios[1]->error);
    }

    /**
     * Test for ading errors into scenarios.
     * @covers ::add_error
     * @covers ::add_scenario
     * @covers ::add_step
     * @covers ::get_scenarios
     */
    public function test_add_error(): void {
        $parsedfeature = new parsedfeature();

        // Add some valid step.
        $extrafeature = $this->get_feature_from_content('Feature: Test feature
            Scenario: Create users
                Given the following "users" exist:
                    | username | firstname  | lastname | email              |
                    | teacher1 | Teacher    | Test1    | sample@example.com |
        ');
        $step = $extrafeature->get_all_steps()[0];

        $parsedfeature->add_scenario('scenario', 'Test scenario 1');
        $parsedfeature->add_step($step);
        $parsedfeature->add_error('Error message');
        $parsedfeature->add_scenario('scenario', 'Test scenario 2');
        $parsedfeature->add_step($step);

        $scenarios = $parsedfeature->get_scenarios();
        $this->assertEquals(2, count($scenarios));
        // Scenario 1.
        $this->assertEquals('scenario', $scenarios[0]->type);
        $this->assertEquals('Test scenario 1', $scenarios[0]->name);
        $this->assertEquals(1, count($scenarios[0]->steps));
        $this->assertEquals($step, $scenarios[0]->steps[0]);
        $this->assertEquals('Error message', $scenarios[0]->error);
        // Scenario 2.
        $this->assertEquals('scenario', $scenarios[1]->type);
        $this->assertEquals('Test scenario 2', $scenarios[1]->name);
        $this->assertEquals(1, count($scenarios[1]->steps));
        $this->assertEquals($step, $scenarios[1]->steps[0]);
        $this->assertEquals('', $scenarios[1]->error);
    }
}
