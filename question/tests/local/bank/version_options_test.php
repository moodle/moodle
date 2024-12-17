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

namespace core_question\local\bank;

/**
 * Unit tests for the version_options class and associated methods.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 * @covers     \core_question\local\bank\version_options
 */
final class version_options_test extends \advanced_testcase {


    /**
     * Tests the retrieval and correctness of version selection menu options for a given question.
     *
     * This method verifies that the correct version options are available for a specific question
     * within a course, ensuring that the options are accurately labeled and ordered. It includes
     * checks for version labeling, the presence of an "Always latest" option, and the correct count
     * and sequence of version numbers after creating multiple versions of a question.
     *
     * @return void
     */
    public function test_get_version_options(): void {

        // Reset everything after this test completes.
        $this->resetAfterTest();

        // Load the generators we need.
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');

        // Create a test course we can use the question bank on.
        $category = self::getDataGenerator()->create_category();
        $course = self::getDataGenerator()->create_course(['category' => $category->id]);
        $coursecontext = \core\context\course::instance($course->id);

        // Create a question category on the course.
        $cat = $questiongenerator->create_question_category(['contextid' => $coursecontext->id]);

        // Create a question within that category.
        $question = $questiongenerator->create_question('essay', null,
            ['category' => $cat->id, 'name' => 'This is the first version']);

        // Get the select menu options for the question versions.
        $options = version_options::get_version_menu_options($question->id);

        // Assert that there are 2 and that they "Always latest" is first.
        $this->assertCount(2, $options);
        $this->assertEquals('Always latest', $options[0]);
        $this->assertEquals('v1 (latest)', $options[1]);

        // Create some new versions of the question.
        $questiongenerator->update_question($question, null, ['name' => 'This is the second version']);
        $questiongenerator->update_question($question, null, ['name' => 'This is the third version']);

        // Get the options which would be used for the select menu.
        $options = version_options::get_version_menu_options($question->id);

        // Assert that there are now 4.
        $this->assertCount(4, $options);

        // Assert that they are keyed to their verison numbers.
        $this->assertEquals('Always latest', $options[0]);
        $this->assertEquals('v1', $options[1]);
        $this->assertEquals('v2', $options[2]);
        $this->assertEquals('v3 (latest)', $options[3]);

        // Assert that they are in the correct order of "Always latest" first, then descending versions.
        $this->assertEquals(array_keys($options), [0, 3, 2, 1]);

    }

}
