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
 * @package   core_backup
 * @category  phpunit
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
require_once(__DIR__.'/fixtures/structure_fixtures.php');


/**
 * Unit test case the base_atom class. Note: as it's abstract we are testing
 * mock_base_atom instantiable class instead
 */
class backup_base_atom_testcase extends basic_testcase {

    /**
     * Correct base_atom_tests
     */
    function test_base_atom() {
        $name_with_all_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
        $value_to_test = 'Some <value> to test';

        // Create instance with correct names
        $instance = new mock_base_atom($name_with_all_chars);
        $this->assertInstanceOf('base_atom', $instance);
        $this->assertEquals($instance->get_name(), $name_with_all_chars);
        $this->assertFalse($instance->is_set());
        $this->assertNull($instance->get_value());

        // Set value
        $instance->set_value($value_to_test);
        $this->assertEquals($instance->get_value(), $value_to_test);
        $this->assertTrue($instance->is_set());

        // Clean value
        $instance->clean_value();
        $this->assertFalse($instance->is_set());
        $this->assertNull($instance->get_value());

        // Get to_string() results (with values)
        $instance = new mock_base_atom($name_with_all_chars);
        $instance->set_value($value_to_test);
        $tostring = $instance->to_string(true);
        $this->assertTrue(strpos($tostring, $name_with_all_chars) !== false);
        $this->assertTrue(strpos($tostring, ' => ') !== false);
        $this->assertTrue(strpos($tostring, $value_to_test) !== false);

        // Get to_string() results (without values)
        $tostring = $instance->to_string(false);
        $this->assertTrue(strpos($tostring, $name_with_all_chars) !== false);
        $this->assertFalse(strpos($tostring, ' => '));
        $this->assertFalse(strpos($tostring, $value_to_test));
    }

    /**
     * Throwing exception base_atom tests
     */
    function test_base_atom_exceptions() {
        // empty names
        try {
            $instance = new mock_base_atom('');
            $this->fail("Expecting base_atom_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_atom_struct_exception);
        }

        // whitespace names
        try {
            $instance = new mock_base_atom('TESTING ATOM');
            $this->fail("Expecting base_atom_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_atom_struct_exception);
        }

        // ascii names
        try {
            $instance = new mock_base_atom('TESTING-ATOM');
            $this->fail("Expecting base_atom_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_atom_struct_exception);
        }
        try {
            $instance = new mock_base_atom('TESTING_ATOM_Á');
            $this->fail("Expecting base_atom_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_atom_struct_exception);
        }

        // setting already set value
        $instance = new mock_base_atom('TEST');
        $instance->set_value('test');
        try {
            $instance->set_value('test');
            $this->fail("Expecting base_atom_content_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_atom_content_exception);
        }
    }
}
