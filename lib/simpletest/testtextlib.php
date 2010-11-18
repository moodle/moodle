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
 * Functions to support installation process
 *
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for our utf-8 aware text processing
 *
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class textlib_test extends UnitTestCase {

    public static $includecoverage = array('lib/textlib.class.php');

    public function test_asort() {
        global $SESSION;
        $SESSION->lang = 'en'; // make sure we test en language to get consistent results, hopefully all systems have this locale

        $arr = array('b'=>'ab', 1=>'aa', 0=>'cc');
        textlib_get_instance()->asort($arr);
        $this->assertIdentical(array_keys($arr), array(1, 'b', 0));
        $this->assertIdentical(array_values($arr), array('aa', 'ab', 'cc'));

        if (extension_loaded('intl')) {
            $error = 'Collation aware sorting not supported';
        } else {
            $error = 'Collation aware sorting not supported, PHP extension "intl" is not available.';
        }

        $arr = array('a'=>'áb', 'b'=>'ab', 1=>'aa', 0=>'cc');
        textlib_get_instance()->asort($arr);
        $this->assertIdentical(array_keys($arr), array(1, 'b', 'a', 0), $error);

        unset($SESSION->lang);
    }

}
