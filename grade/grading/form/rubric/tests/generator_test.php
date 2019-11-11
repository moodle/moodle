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
 * @package    gradingform_rubric
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tests\gradingform_rubric;

use advanced_testcase;
use context_module;
use gradingform_rubric_controller;
use gradingform_controller;

/**
 * Generator testcase for the gradingforum_rubric generator.
 *
 * @package    gradingform_rubric
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_testcase extends advanced_testcase {

    /**
     * Test rubric creation.
     */
    public function test_rubric_creation(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $rubricgenerator = $generator->get_plugin_generator('gradingform_rubric');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // Data for testing.
        $name = 'myfirstrubric';
        $description = 'My first rubric';
        $criteria = [
            'Alphabet' => [
                'Not known' => 0,
                'Letters known but out of order' => 1,
                'Letters known in order ascending' => 2,
                'Letters known and can recite forwards and backwards' => 4,
            ],
            'Times tables' => [
                'Not known' => 0,
                '2 times table known' => 2,
                '2 and 5 times table known' => 4,
                '2, 5, and 10 times table known' => 8,
            ],
        ];

        // Unit under test.
        $this->setUser($user);
        $controller = $rubricgenerator->create_instance($context, 'mod_assign', 'submission', $name, $description, $criteria);

        $this->assertInstanceOf(gradingform_rubric_controller::class, $controller);

        $definition = $controller->get_definition();
        $this->assertNotEmpty($definition->id);
        $this->assertEquals($name, $definition->name);
        $this->assertEquals($description, $definition->description);
        $this->assertEquals(gradingform_controller::DEFINITION_STATUS_READY, $definition->status);
        $this->assertNotEmpty($definition->timecreated);
        $this->assertNotEmpty($definition->timemodified);
        $this->assertEquals($user->id, $definition->usercreated);

        $this->assertNotEmpty($definition->rubric_criteria);
        $this->assertCount(2, $definition->rubric_criteria);

        // Check the alphabet criteria.
        $criteriaids = array_keys($definition->rubric_criteria);

        $alphabet = $definition->rubric_criteria[$criteriaids[0]];
        $this->assertNotEmpty($alphabet['id']);
        $this->assertEquals(1, $alphabet['sortorder']);
        $this->assertEquals('Alphabet', $alphabet['description']);

        $this->assertNotEmpty($alphabet['levels']);
        $levels = $alphabet['levels'];
        $levelids = array_keys($levels);

        $level = $levels[$levelids[0]];
        $this->assertEquals(0, $level['score']);
        $this->assertEquals('Not known', $level['definition']);

        $level = $levels[$levelids[1]];
        $this->assertEquals(1, $level['score']);
        $this->assertEquals('Letters known but out of order', $level['definition']);

        $level = $levels[$levelids[2]];
        $this->assertEquals(2, $level['score']);
        $this->assertEquals('Letters known in order ascending', $level['definition']);

        $level = $levels[$levelids[3]];
        $this->assertEquals(4, $level['score']);
        $this->assertEquals('Letters known and can recite forwards and backwards', $level['definition']);

        // Check the times tables criteria.
        $tables = $definition->rubric_criteria[$criteriaids[1]];
        $this->assertNotEmpty($tables['id']);
        $this->assertEquals(2, $tables['sortorder']);
        $this->assertEquals('Times tables', $tables['description']);

        $this->assertNotEmpty($tables['levels']);
        $levels = $tables['levels'];
        $levelids = array_keys($levels);

        $level = $levels[$levelids[0]];
        $this->assertEquals(0, $level['score']);
        $this->assertEquals('Not known', $level['definition']);

        $level = $levels[$levelids[1]];
        $this->assertEquals(2, $level['score']);
        $this->assertEquals('2 times table known', $level['definition']);

        $level = $levels[$levelids[2]];
        $this->assertEquals(4, $level['score']);
        $this->assertEquals('2 and 5 times table known', $level['definition']);

        $level = $levels[$levelids[3]];
        $this->assertEquals(8, $level['score']);
        $this->assertEquals('2, 5, and 10 times table known', $level['definition']);
    }

    /**
     * Test the get_level_and_criterion_for_values function.
     * This is used for finding criterion and level information within a rubric.
     */
    public function test_get_level_and_criterion_for_values(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $rubricgenerator = $generator->get_plugin_generator('gradingform_rubric');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // Data for testing.
        $description = 'My first rubric';
        $criteria = [
            'Alphabet' => [
                'Not known' => 0,
                'Letters known but out of order' => 1,
                'Letters known in order ascending' => 2,
                'Letters known and can recite forwards and backwards' => 4,
            ],
            'Times tables' => [
                'Not known' => 0,
                '2 times table known' => 2,
                '2 and 5 times table known' => 4,
                '2, 5, and 10 times table known' => 8,
            ],
        ];

        $this->setUser($user);
        $controller = $rubricgenerator->create_instance($context, 'mod_assign', 'submission', 'rubric', $description, $criteria);

        // Valid criterion and level.
        $result = $rubricgenerator->get_level_and_criterion_for_values($controller, 'Alphabet', 2);
        $this->assertEquals('Alphabet', $result['criterion']->description);
        $this->assertEquals('2', $result['level']->score);
        $this->assertEquals('Letters known in order ascending', $result['level']->definition);

        // Valid criterion. Invalid level.
        $result = $rubricgenerator->get_level_and_criterion_for_values($controller, 'Alphabet', 3);
        $this->assertEquals('Alphabet', $result['criterion']->description);
        $this->assertNull($result['level']);

        // Invalid criterion.
        $result = $rubricgenerator->get_level_and_criterion_for_values($controller, 'Foo', 0);
        $this->assertNull($result['criterion']);
    }

    /**
     * Tests for the get_test_rubric function.
     */
    public function test_get_test_rubric(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $rubricgenerator = $generator->get_plugin_generator('gradingform_rubric');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $rubric = $rubricgenerator->get_test_rubric($context, 'assign', 'submissions');
        $definition = $rubric->get_definition();

        $this->assertEquals('testrubric', $definition->name);
        $this->assertEquals('Description text', $definition->description);
        $this->assertEquals(gradingform_controller::DEFINITION_STATUS_READY, $definition->status);

        // Should create a rubric with 2 criterion.
        $this->assertCount(2, $definition->rubric_criteria);
    }

    /**
     * Test the get_submitted_form_data function.
     */
    public function test_get_submitted_form_data(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $rubricgenerator = $generator->get_plugin_generator('gradingform_rubric');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $rubricgenerator->get_test_rubric($context, 'assign', 'submissions');

        $result = $rubricgenerator->get_submitted_form_data($controller, 93, [
            'Spelling is important' => [
                'score' => 1,
                'remark' => 'Good speeling',
            ],
            'Pictures' => [
                'score' => 2,
                'remark' => 'Lots of nice pictures!',
            ]
        ]);

        $this->assertIsArray($result);
        $this->assertEquals(93, $result['itemid']);
        $this->assertIsArray($result['criteria']);
        $this->assertCount(2, $result['criteria']);

        $spelling = $rubricgenerator->get_level_and_criterion_for_values($controller, 'Spelling is important', 1);
        $this->assertIsArray($result['criteria'][$spelling['criterion']->id]);
        $this->assertEquals($spelling['level']->id, $result['criteria'][$spelling['criterion']->id]['levelid']);
        $this->assertEquals('Good speeling', $result['criteria'][$spelling['criterion']->id]['remark']);

        $pictures = $rubricgenerator->get_level_and_criterion_for_values($controller, 'Pictures', 2);
        $this->assertIsArray($result['criteria'][$pictures['criterion']->id]);
        $this->assertEquals($pictures['level']->id, $result['criteria'][$pictures['criterion']->id]['levelid']);
        $this->assertEquals('Lots of nice pictures!', $result['criteria'][$pictures['criterion']->id]['remark']);
    }

    /**
     * Test the get_test_form_data function.
     */
    public function test_get_test_form_data(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $rubricgenerator = $generator->get_plugin_generator('gradingform_rubric');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $rubricgenerator->get_test_rubric($context, 'assign', 'submissions');

        // Unit under test.
        $result = $rubricgenerator->get_test_form_data(
            $controller,
            1839,
            1, 'Propper good speling',
            0, 'ASCII art is not a picture'
        );

        $this->assertIsArray($result);
        $this->assertEquals(1839, $result['itemid']);
        $this->assertIsArray($result['criteria']);
        $this->assertCount(2, $result['criteria']);

        $spelling = $rubricgenerator->get_level_and_criterion_for_values($controller, 'Spelling is important', 1);
        $this->assertIsArray($result['criteria'][$spelling['criterion']->id]);
        $this->assertEquals($spelling['level']->id, $result['criteria'][$spelling['criterion']->id]['levelid']);
        $this->assertEquals('Propper good speling', $result['criteria'][$spelling['criterion']->id]['remark']);

        $pictures = $rubricgenerator->get_level_and_criterion_for_values($controller, 'Pictures', 0);
        $this->assertIsArray($result['criteria'][$pictures['criterion']->id]);
        $this->assertEquals($pictures['level']->id, $result['criteria'][$pictures['criterion']->id]['levelid']);
        $this->assertEquals('ASCII art is not a picture', $result['criteria'][$pictures['criterion']->id]['remark']);
    }
}
