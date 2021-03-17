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
 * Unit tests for tool_health.
 *
 * @package    tool_health
 * @copyright  2013 Marko Vidberg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/health/locallib.php');

/**
 * Health lib testcase.
 *
 * @package    tool_health
 * @copyright  2013 Marko Vidberg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class healthlib_testcase extends advanced_testcase {

    /**
     * Data provider for test_tool_health_category_find_loops.
     */
    public static function provider_loop_categories() {
        return array(
            // One item loop including root.
            0 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 1)
                ),
                array(
                    '1' => (object) array('id' => 1, 'parent' => 1)
                ),
            ),
            // One item loop not including root.
            1 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 0),
                    '2' => (object) array('id' => 2, 'parent' => 2)
                ),
                array(
                    '2' => (object) array('id' => 2, 'parent' => 2)
                ),
            ),
            // Two item loop including root.
            2 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 1)
                ),
                array(
                    '2' => (object) array('id' => 2, 'parent' => 1),
                    '1' => (object) array('id' => 1, 'parent' => 2),
                )
            ),
            // Two item loop not including root.
            3 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 0),
                    '2' => (object) array('id' => 2, 'parent' => 3),
                    '3' => (object) array('id' => 3, 'parent' => 2),
                ),
                array(
                    '3' => (object) array('id' => 3, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 3),
                )
            ),
            // Three item loop including root.
            4 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 3),
                    '3' => (object) array('id' => 3, 'parent' => 1),
                ),
                array(
                    '3' => (object) array('id' => 3, 'parent' => 1),
                    '1' => (object) array('id' => 1, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 3),
                )
            ),
            // Three item loop not including root.
            5 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 0),
                    '2' => (object) array('id' => 2, 'parent' => 3),
                    '3' => (object) array('id' => 3, 'parent' => 4),
                    '4' => (object) array('id' => 4, 'parent' => 2)
                ),
                array(
                    '4' => (object) array('id' => 4, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 3),
                    '3' => (object) array('id' => 3, 'parent' => 4),
                )
            ),
            // Multi-loop.
            6 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 1),
                    '3' => (object) array('id' => 3, 'parent' => 4),
                    '4' => (object) array('id' => 4, 'parent' => 5),
                    '5' => (object) array('id' => 5, 'parent' => 3),
                    '6' => (object) array('id' => 6, 'parent' => 6),
                    '7' => (object) array('id' => 7, 'parent' => 1),
                    '8' => (object) array('id' => 8, 'parent' => 7),
                ),
                array(
                    '1' => (object) array('id' => 1, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 1),
                    '8' => (object) array('id' => 8, 'parent' => 7),
                    '7' => (object) array('id' => 7, 'parent' => 1),
                    '6' => (object) array('id' => 6, 'parent' => 6),
                    '5' => (object) array('id' => 5, 'parent' => 3),
                    '3' => (object) array('id' => 3, 'parent' => 4),
                    '4' => (object) array('id' => 4, 'parent' => 5),
                )
            ),
            // Double-loop
            7 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 1),
                    '3' => (object) array('id' => 3, 'parent' => 2),
                    '4' => (object) array('id' => 4, 'parent' => 2),
                ),
                array(
                    '4' => (object) array('id' => 4, 'parent' => 2),
                    '3' => (object) array('id' => 3, 'parent' => 2),
                    '2' => (object) array('id' => 2, 'parent' => 1),
                    '1' => (object) array('id' => 1, 'parent' => 2),
                )
            )
        );
    }

    /**
     * Data provider for test_tool_health_category_find_missing_parents.
     */
    public static function provider_missing_parent_categories() {
        return array(
           // Test for two items, both with direct ancestor (parent) missing.
            0 => array(
                array(
                    '1' => (object) array('id' => 1, 'parent' => 0),
                    '2' => (object) array('id' => 2, 'parent' => 3),
                    '4' => (object) array('id' => 4, 'parent' => 5),
                    '6' => (object) array('id' => 6, 'parent' => 2)
                ),
                array(
                    '4' => (object) array('id' => 4, 'parent' => 5),
                    '2' => (object) array('id' => 2, 'parent' => 3)
                ),
            )
        );
    }

    /**
     * Test finding loops between two items referring to each other.
     *
     * @param array $categories
     * @param array $expected
     * @dataProvider provider_loop_categories
     */
    public function test_tool_health_category_find_loops($categories, $expected) {
        $loops = tool_health_category_find_loops($categories);
        $this->assertEquals($expected, $loops);
    }

    /**
     * Test finding missing parent categories.
     *
     * @param array $categories
     * @param array $expected
     * @dataProvider provider_missing_parent_categories
     */
    public function test_tool_health_category_find_missing_parents($categories, $expected) {
        $missingparent = tool_health_category_find_missing_parents($categories);
        $this->assertEquals($expected, $missingparent);
    }

    /**
     * Test listing missing parent categories.
     */
    public function test_tool_health_category_list_missing_parents() {
        $missingparent = array((object) array('id' => 2, 'parent' => 3, 'name' => 'test'),
                               (object) array('id' => 4, 'parent' => 5, 'name' => 'test2'));
        $result = tool_health_category_list_missing_parents($missingparent);
        $this->assertMatchesRegularExpression('/Category 2: test/', $result);
        $this->assertMatchesRegularExpression('/Category 4: test2/', $result);
    }

    /**
     * Test listing loop categories.
     */
    public function test_tool_health_category_list_loops() {
        $loops = array((object) array('id' => 2, 'parent' => 3, 'name' => 'test'));
        $result = tool_health_category_list_loops($loops);
        $this->assertMatchesRegularExpression('/Category 2: test/', $result);
    }
}
