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
 * Unit tests for base_attribute.class.php
 *
 * @package    moodlecore
 * @subpackage backup-tests
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this file
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot . '/backup/util/structure/base_atom.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_attribute.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_final_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_nested_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/simpletest/fixtures/structuremocks.php');

/**
 * Unit test case the base_attribute class. Note: No really much to test here as attribute is 100%
 * atom extension without new functionality (name/value)
 */
class base_attribute_test extends UnitTestCase {

    public static $includecoverage = array('backup/util/structure/base_attribute.class.php');

    /**
     * Correct base_attribute tests
     */
    function test_base_attribute() {
        $name_with_all_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
        $value_to_test = 'Some <value> to test';

        // Create instance with correct names
        $instance = new mock_base_attribute($name_with_all_chars);
        $this->assertIsA($instance, 'base_attribute');
        $this->assertEqual($instance->get_name(), $name_with_all_chars);
        $this->assertNull($instance->get_value());

        // Set value
        $instance->set_value($value_to_test);
        $this->assertEqual($instance->get_value(), $value_to_test);

        // Get to_string() results (with values)
        $instance = new mock_base_attribute($name_with_all_chars);
        $instance->set_value($value_to_test);
        $tostring = $instance->to_string(true);
        $this->assertTrue(strpos($tostring, '@' . $name_with_all_chars) !== false);
        $this->assertTrue(strpos($tostring, ' => ') !== false);
        $this->assertTrue(strpos($tostring, $value_to_test) !== false);
    }
}
