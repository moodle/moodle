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

namespace tool_brickfield;

use tool_brickfield\local\tool\filter;

/**
 * Unit tests for {@accessibility tool_brickfield\accessibility.php}.
 *
 * @package   tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Jay Churchward (jay@brickfieldlabs.ie)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class accessibility_test extends \advanced_testcase {

    /**
     * Test get_title().
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function test_get_title(): void {
        $this->resetAfterTest();
        $object = new accessibility();
        $filter = new filter();

        // Testing the else statement.
        $output = $object->get_title($filter, 0);
        $this->assertEquals($output, 'Error details: all reviewed courses (0 courses)');
        $output = $object->get_title($filter, 5);
        $this->assertEquals($output, 'Error details: all reviewed courses (5 courses)');

        // Testing the if statement.
        $filter->courseid = 1;
        $output = $object->get_title($filter, 0);
        $this->assertEquals($output, 'Error details: course PHPUnit test site');
    }

    /**
     * Test check_ids().
     *
     * @throws \dml_exception
     */
    public function test_check_ids(): void {
        $this->resetAfterTest();
        $object = new accessibility();

        $output = $object->checkids();
        $this->assertEquals($output[1], 'a_links_dont_open_new_window');
        $this->assertEquals($output[10], 'css_text_has_contrast');

        $output = $object->checkids(2);
        $this->assertEmpty($output);
    }

    /**
     * Test get_translations().
     *
     * @throws \dml_exception
     */
    public function test_get_translations(): void {
        $this->resetAfterTest();
        $object = new accessibility();

        $output = $object->get_translations();
        $this->assertEquals($output['a_must_contain_text']['title'], 'Links should contain text');
        $this->assertStringContainsString('<p>Because many users of screen readers use links to ' .
            'navigate the page, providing links with no text (or with images that have empty \'alt\' attributes and no other ' .
            'readable text) hinders these users.</p>', $output['a_must_contain_text']['description']);
    }

    /**
     * Test get_category_courseids().
     *
     * @throws \dml_exception
     */
    public function test_get_category_courseids(): void {
        $this->resetAfterTest();
        $object = new accessibility();
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course((object)['category' => $category->id]);

        $output = $object->get_category_courseids($category->id);
        $this->assertEquals($output[0], $course->id);
    }
}
