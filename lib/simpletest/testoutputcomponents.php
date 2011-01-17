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
 * Unit tests for lib/outputcomponents.php.
 *
 * @package   core
 * @copyright 2011 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/outputcomponents.php');

/**
 * Unit tests for the user_picture class
 */
class user_picture_test extends UnitTestCase {

    public static $includecoverage = array('lib/outputcomponents.php');

    public function test_user_picture_fields_aliasing() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));
        $this->assertTrue(in_array('id', $fields));

        $aliased = array();
        foreach ($fields as $field) {
            if ($field === 'id') {
                $aliased['id'] = 'aliasedid';
            } else {
                $aliased[$field] = 'prefix'.$field;
            }
        }

        $returned = user_picture::fields('', array('custom1', 'id'), 'aliasedid', 'prefix');
        $returned = array_map('trim', explode(',', $returned));
        $this->assertEqual(count($returned), count($fields) + 1); // only one extra field added

        foreach ($fields as $field) {
            if ($field === 'id') {
                $expected = "id AS aliasedid";
            } else {
                $expected = "$field AS prefix$field";
            }
            $this->assertTrue(in_array($expected, $returned), "Expected pattern '$expected' not returned");
        }
        $this->assertTrue(in_array("custom1 AS prefixcustom1", $returned), "Expected pattern 'custom1 AS prefixcustom1' not returned");
    }

    public function test_user_picture_fields_unaliasing() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));

        $fakerecord = new stdClass();
        $fakerecord->aliasedid = 42;
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $fakerecord->{'prefix'.$field} = "Value of $field";
            }
        }
        $fakerecord->prefixcustom1 = 'Value of custom1';

        $returned = user_picture::unalias($fakerecord, array('custom1'), 'aliasedid', 'prefix');

        $this->assertEqual($returned->id, 42);
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $this->assertEqual($returned->{$field}, "Value of $field");
            }
        }
        $this->assertEqual($returned->custom1, 'Value of custom1');
    }

    public function test_user_picture_fields_unaliasing_null() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));

        $fakerecord = new stdClass();
        $fakerecord->aliasedid = 42;
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $fakerecord->{'prefix'.$field} = "Value of $field";
            }
        }
        $fakerecord->prefixcustom1 = 'Value of custom1';
        $fakerecord->prefiximagealt = null;

        $returned = user_picture::unalias($fakerecord, array('custom1'), 'aliasedid', 'prefix');

        $this->assertEqual($returned->id, 42);
        $this->assertEqual($returned->imagealt, null);
        foreach ($fields as $field) {
            if ($field !== 'id' and $field !== 'imagealt') {
                $this->assertEqual($returned->{$field}, "Value of $field");
            }
        }
        $this->assertEqual($returned->custom1, 'Value of custom1');
    }
}
