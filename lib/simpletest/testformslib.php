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
 * Unit tests for /lib/formslib.php.
 *
 * @package   file
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/formslib.php');

class formslib_test extends UnitTestCase {

    public function test_require_rule() {
        global $CFG;

        $strictformsrequired = false;
        if (!empty($CFG->strictformsrequired)) {
            $strictformsrequired = $CFG->strictformsrequired;
        }

        $rule = new MoodleQuickForm_Rule_Required();

        // First run the tests with strictformsrequired off
        $CFG->strictformsrequired = false;
        // Passes
        $this->assertTrue($rule->validate('Something'));
        $this->assertTrue($rule->validate("Something\nmore"));
        $this->assertTrue($rule->validate("\nmore"));
        $this->assertTrue($rule->validate(" more "));
        $this->assertTrue($rule->validate("0"));
        $this->assertTrue($rule->validate(0));
        $this->assertTrue($rule->validate(true));
        $this->assertTrue($rule->validate(' '));
        $this->assertTrue($rule->validate('      '));
        $this->assertTrue($rule->validate("\t"));
        $this->assertTrue($rule->validate("\n"));
        $this->assertTrue($rule->validate("\r"));
        $this->assertTrue($rule->validate("\r\n"));
        $this->assertTrue($rule->validate(" \t  \n  \r "));
        $this->assertTrue($rule->validate('<p></p>'));
        $this->assertTrue($rule->validate('<p> </p>'));
        $this->assertTrue($rule->validate('<p>x</p>'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile" />'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile"/>'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile"></img>'));
        $this->assertTrue($rule->validate('<hr />'));
        $this->assertTrue($rule->validate('<hr/>'));
        $this->assertTrue($rule->validate('<hr>'));
        $this->assertTrue($rule->validate('<hr></hr>'));
        $this->assertTrue($rule->validate('<br />'));
        $this->assertTrue($rule->validate('<br/>'));
        $this->assertTrue($rule->validate('<br>'));
        $this->assertTrue($rule->validate('&nbsp;'));
        // Fails
        $this->assertFalse($rule->validate(''));
        $this->assertFalse($rule->validate(false));
        $this->assertFalse($rule->validate(null));

        // Now run the same tests with it on to make sure things work as expected
        $CFG->strictformsrequired = true;
        // Passes
        $this->assertTrue($rule->validate('Something'));
        $this->assertTrue($rule->validate("Something\nmore"));
        $this->assertTrue($rule->validate("\nmore"));
        $this->assertTrue($rule->validate(" more "));
        $this->assertTrue($rule->validate("0"));
        $this->assertTrue($rule->validate(0));
        $this->assertTrue($rule->validate(true));
        $this->assertTrue($rule->validate('<p>x</p>'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile" />'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile"/>'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile"></img>'));
        $this->assertTrue($rule->validate('<hr />'));
        $this->assertTrue($rule->validate('<hr/>'));
        $this->assertTrue($rule->validate('<hr>'));
        $this->assertTrue($rule->validate('<hr></hr>'));
        // Fails
        $this->assertFalse($rule->validate(' '));
        $this->assertFalse($rule->validate('      '));
        $this->assertFalse($rule->validate("\t"));
        $this->assertFalse($rule->validate("\n"));
        $this->assertFalse($rule->validate("\r"));
        $this->assertFalse($rule->validate("\r\n"));
        $this->assertFalse($rule->validate(" \t  \n  \r "));
        $this->assertFalse($rule->validate('<p></p>'));
        $this->assertFalse($rule->validate('<p> </p>'));
        $this->assertFalse($rule->validate('<br />'));
        $this->assertFalse($rule->validate('<br/>'));
        $this->assertFalse($rule->validate('<br>'));
        $this->assertFalse($rule->validate('&nbsp;'));
        $this->assertFalse($rule->validate(''));
        $this->assertFalse($rule->validate(false));
        $this->assertFalse($rule->validate(null));

        $CFG->strictformsrequired = $strictformsrequired;
    }

}