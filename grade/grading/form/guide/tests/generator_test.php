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
 * Generator testcase for the gradingforum_guide generator.
 *
 * @package    gradingform_guide
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_guide;

use context_module;
use gradingform_controller;
use gradingform_guide_controller;

/**
 * Generator testcase for the gradingforum_guide generator.
 *
 * @package    gradingform_guide
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {

    /**
     * Test guide creation.
     */
    public function test_guide_creation(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $guidegenerator = $generator->get_plugin_generator('gradingform_guide');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // Data for testing.
        $name = 'myfirstguide';
        $description = 'My first guide';
        $criteria = [
            'Alphabet' => [
                'description' => 'How well you know your alphabet',
                'descriptionmarkers' => 'Basic literacy: Alphabet',
                'maxscore' => 5,
            ],
            'Times tables' => [
                'description' => 'How well you know your times-tables',
                'descriptionmarkers' => 'Basic numeracy: Multiplication',
                'maxscore' => 10,
            ],
        ];

        // Unit under test.
        $this->setUser($user);
        $controller = $guidegenerator->create_instance($context, 'mod_assign', 'submission', $name, $description, $criteria);

        $this->assertInstanceOf(gradingform_guide_controller::class, $controller);

        $definition = $controller->get_definition();
        $this->assertEquals('guide', $definition->method);
        $this->assertNotEmpty($definition->id);
        $this->assertEquals($name, $definition->name);
        $this->assertEquals($description, $definition->description);
        $this->assertEquals(gradingform_controller::DEFINITION_STATUS_READY, $definition->status);
        $this->assertNotEmpty($definition->timecreated);
        $this->assertNotEmpty($definition->timemodified);
        $this->assertEquals($user->id, $definition->usercreated);

        $this->assertNotEmpty($definition->guide_criteria);
        $this->assertCount(2, $definition->guide_criteria);

        // Check the alphabet criteria.
        $criteriaids = array_keys($definition->guide_criteria);

        $alphabet = $definition->guide_criteria[$criteriaids[0]];
        $this->assertNotEmpty($alphabet['id']);
        $this->assertEquals(1, $alphabet['sortorder']);
        $this->assertEquals('How well you know your alphabet', $alphabet['description']);
        $this->assertEquals('Basic literacy: Alphabet', $alphabet['descriptionmarkers']);
        $this->assertEquals(5, $alphabet['maxscore']);

        // Check the times tables criteria.
        $tables = $definition->guide_criteria[$criteriaids[1]];
        $this->assertNotEmpty($tables['id']);
        $this->assertEquals(2, $tables['sortorder']);
        $this->assertEquals('How well you know your times-tables', $tables['description']);
        $this->assertEquals('Basic numeracy: Multiplication', $tables['descriptionmarkers']);
        $this->assertEquals(10, $tables['maxscore']);
    }

    /**
     * Test the get_criterion_for_values function.
     * This is used for finding criterion and level information within a guide.
     */
    public function test_get_criterion_for_values(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $guidegenerator = $generator->get_plugin_generator('gradingform_guide');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // Data for testing.
        $name = 'myfirstguide';
        $description = 'My first guide';
        $criteria = [
            'Alphabet' => [
                'description' => 'How well you know your alphabet',
                'descriptionmarkers' => 'Basic literacy: Alphabet',
                'maxscore' => 5,
            ],
            'Times tables' => [
                'description' => 'How well you know your times-tables',
                'descriptionmarkers' => 'Basic numeracy: Multiplication',
                'maxscore' => 10,
            ],
        ];

        $this->setUser($user);
        $controller = $guidegenerator->create_instance($context, 'mod_assign', 'submission', $name, $description, $criteria);

        // Valid criterion.
        $result = $guidegenerator->get_criterion_for_values($controller, 'Alphabet', 2);
        $this->assertEquals('Alphabet', $result->shortname);
        $this->assertEquals('How well you know your alphabet', $result->description);
        $this->assertEquals('Basic literacy: Alphabet', $result->descriptionmarkers);
        $this->assertEquals(5, $result->maxscore);

        // Invalid criterion.
        $result = $guidegenerator->get_criterion_for_values($controller, 'Foo', 0);
        $this->assertNull($result);
    }

    /**
     * Tests for the get_test_guide function.
     */
    public function test_get_test_guide(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $guidegenerator = $generator->get_plugin_generator('gradingform_guide');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $guide = $guidegenerator->get_test_guide($context, 'assign', 'submissions');
        $definition = $guide->get_definition();

        $this->assertEquals('testguide', $definition->name);
        $this->assertEquals('Description text', $definition->description);
        $this->assertEquals(gradingform_controller::DEFINITION_STATUS_READY, $definition->status);

        // Should create a guide with 2 criterion.
        $this->assertCount(2, $definition->guide_criteria);
    }

    /**
     * Test the get_submitted_form_data function.
     */
    public function test_get_submitted_form_data(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $guidegenerator = $generator->get_plugin_generator('gradingform_guide');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $guidegenerator->get_test_guide($context, 'assign', 'submissions');

        $result = $guidegenerator->get_submitted_form_data($controller, 93, [
            'Spelling mistakes' => [
                'score' => 10,
                'remark' => 'Pretty good but you had a couple of errors',
            ],
            'Pictures' => [
                'score' => 15,
                'remark' => 'Lots of nice pictures!',
            ]
        ]);

        $this->assertIsArray($result);
        $this->assertEquals(93, $result['itemid']);
        $this->assertIsArray($result['criteria']);
        $this->assertCount(2, $result['criteria']);

        $spelling = $guidegenerator->get_criterion_for_values($controller, 'Spelling mistakes');
        $this->assertIsArray($result['criteria'][$spelling->id]);

        $this->assertEquals(10, $result['criteria'][$spelling->id]['score']);
        $this->assertEquals('Pretty good but you had a couple of errors', $result['criteria'][$spelling->id]['remark']);

        $pictures = $guidegenerator->get_criterion_for_values($controller, 'Pictures', 2);
        $this->assertIsArray($result['criteria'][$pictures->id]);
        $this->assertEquals(15, $result['criteria'][$pictures->id]['score']);
        $this->assertEquals('Lots of nice pictures!', $result['criteria'][$pictures->id]['remark']);
    }

    /**
     * Test the get_test_form_data function.
     */
    public function test_get_test_form_data(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $guidegenerator = $generator->get_plugin_generator('gradingform_guide');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $guidegenerator->get_test_guide($context, 'assign', 'submissions');

        // Unit under test.
        $result = $guidegenerator->get_test_form_data(
            $controller,
            1839,
            10, 'Propper good speling',
            0, 'ASCII art is not a picture'
        );

        $this->assertIsArray($result);
        $this->assertEquals(1839, $result['itemid']);
        $this->assertIsArray($result['criteria']);
        $this->assertCount(2, $result['criteria']);

        $spelling = $guidegenerator->get_criterion_for_values($controller, 'Spelling mistakes');
        $this->assertIsArray($result['criteria'][$spelling->id]);
        $this->assertEquals(10, $result['criteria'][$spelling->id]['score']);
        $this->assertEquals('Propper good speling', $result['criteria'][$spelling->id]['remark']);

        $pictures = $guidegenerator->get_criterion_for_values($controller, 'Pictures');
        $this->assertIsArray($result['criteria'][$pictures->id]);
        $this->assertEquals(0, $result['criteria'][$pictures->id]['score']);
        $this->assertEquals('ASCII art is not a picture', $result['criteria'][$pictures->id]['remark']);
    }
}
