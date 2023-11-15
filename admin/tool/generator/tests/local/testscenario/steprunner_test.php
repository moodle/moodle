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

use behat_data_generators;
use Behat\Gherkin\Parser;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Keywords\ArrayKeywords;
use Behat\Gherkin\Node\StepNode;

/**
 * Tests for steprunner class.
 *
 * @package tool_generator
 * @copyright 2023 Ferran Recio <ferran@moodel.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_generator\local\testscenario\steprunner
 */
class steprunner_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        // Call the init method to include all behat libraries and attributes.
        $runner = new runner();
        $runner->init();
    }

    /**
     * Get a step node from a string.
     * @param string $step the step string.
     * @return StepNode the step node.
     */
    private function get_step(string $step): StepNode {
        $content = 'Feature: Test feature
            Scenario: Test scenario
            ' . $step . '
        ';

        $method = new \ReflectionMethod(runner::class, 'get_parser');
        $parser = $method->invoke(new runner());

        $feature = $parser->parse($content);
        $scenario = $feature->getScenarios()[0];
        $steps = $scenario->getSteps();
        return $steps[0];
    }

    /**
     * Test for parse_feature.
     * @covers ::is_valid
     * @param string $step the step to validate.
     * @param bool $expected if the step is expected to be valid.
     * @dataProvider execute_steps_provider
     */
    public function test_is_valid(string $step, bool $expected): void {
        $generator = new behat_data_generators();
        $validsteps = [
            '/^the following "(?P<element_string>(?:[^"]|\\")*)" exist:$/' => 'the_following_entities_exist',
            ':count :entitytype exist with the following data:' => 'the_following_repeated_entities_exist',
            'the following :entitytype exists:' => 'the_following_entity_exists',
        ];

        $step = $this->get_step($step);
        $steprunner = new steprunner($generator, $validsteps, $step);
        $this->assertEquals($expected, $steprunner->is_valid());
    }

    /**
     * Test for execute step.
     *
     * @covers ::is_executed
     * @covers ::execute
     * @param string $step the step to execute.
     * @param bool $expected if the step is expected to be executed.
     * @dataProvider execute_steps_provider
     */
    public function test_execute(string $step, bool $expected): void {
        global $DB;

        $this->resetAfterTest();

        $generator = new behat_data_generators();
        $validsteps = [
            '/^the following "(?P<element_string>(?:[^"]|\\")*)" exist:$/' => 'the_following_entities_exist',
            ':count :entitytype exist with the following data:' => 'the_following_repeated_entities_exist',
            'the following :entitytype exists:' => 'the_following_entity_exists',
        ];

        $step = $this->get_step($step);
        $steprunner = new steprunner($generator, $validsteps, $step);

        $this->assertFalse($steprunner->is_executed());

        $result = $steprunner->execute();

        $this->assertEquals($expected, $result);
        $this->assertEquals($expected, $steprunner->is_executed());

        if ($expected) {
            // Validate everything is created.
            $this->assertEquals(
                1,
                $DB->count_records('course', ['shortname' => 'C1'])
            );
        }
    }

    /**
     * Data provider for test_execute.
     * @return array the data.
     */
    public static function execute_steps_provider(): array {
        return [
            'Following exists' => [
                'step' => 'Given the following "course" exists:
                    | fullname         | Course test |
                    | shortname        | C1          |
                    | category         | 0           |',
                'expected' => true,
            ],
            'Following exist' => [
                'step' => 'Given the following "course" exist:
                    | fullname         | shortname | category |
                    | Course test      | C1        | 0        |',
                'expected' => true,
            ],
            'Repeated entities' => [
                'step' => 'Given "1" "courses" exist with the following data:
                    | fullname    | Course test |
                    | shortname   | C[count]    |
                    | category    | 0           |
                    | numsections | 3           |',
                'expected' => true,
            ],
            'Invalid step' => [
                'step' => 'Given I click on "Tokens filter" "link"',
                'expected' => false,
            ],
        ];
    }

    /**
     * Test for execute step.
     * @covers ::is_executed
     * @covers ::execute
     * @covers ::get_error
     */
    public function test_execute_duplicated(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = new behat_data_generators();
        $validsteps = [
            '/^the following "(?P<element_string>(?:[^"]|\\")*)" exist:$/' => 'the_following_entities_exist',
            ':count :entitytype exist with the following data:' => 'the_following_repeated_entities_exist',
            'the following :entitytype exists:' => 'the_following_entity_exists',
        ];

        $step = $this->get_step('Given the following "course" exists:
            | fullname         | Course test |
            | shortname        | C1          |
            | category         | 0           |');
        $steprunner = new steprunner($generator, $validsteps, $step);

        $this->assertFalse($steprunner->is_executed());

        $result = $steprunner->execute();

        $this->assertTrue($result);
        $this->assertTrue($steprunner->is_executed());
        $this->assertEquals('', $steprunner->get_error());

        // Validate everything is created.
        $this->assertEquals(
            1,
            $DB->count_records('course', ['shortname' => 'C1'])
        );

        // Execute the same course creation.
        $steprunner = new steprunner($generator, $validsteps, $step);
        $this->assertFalse($steprunner->is_executed());
        $result = $steprunner->execute();
        $this->assertFalse($result);
        $this->assertTrue($steprunner->is_executed());
        $this->assertEquals(get_string('shortnametaken', 'error', 'C1'), $steprunner->get_error());
    }

    /**
     * Test for parse_feature.
     * @covers ::get_text
     * @covers ::get_arguments_string
     */
    public function test_get_step_content(): void {
        $generator = new behat_data_generators();
        $validsteps = [
            '/^the following "(?P<element_string>(?:[^"]|\\")*)" exist:$/' => 'the_following_entities_exist',
            ':count :entitytype exist with the following data:' => 'the_following_repeated_entities_exist',
            'the following :entitytype exists:' => 'the_following_entity_exists',
        ];

        $step = $this->get_step('Given the following "course" exists:
            | fullname    | Course test |
            | shortname   | C1          |
            | category    | 0           |
            | numsections | 3           |');
        $steprunner = new steprunner($generator, $validsteps, $step);

        $this->assertEquals(
            'the following "course" exists:',
            $steprunner->get_text()
        );

        $data = [
            '| fullname    | Course test |',
            '| shortname   | C1          |',
            '| category    | 0           |',
            '| numsections | 3           |',
        ];
        $arguments = explode("\n", $steprunner->get_arguments_string());
        $this->assertEquals(
            $data,
            $arguments
        );
    }
}
