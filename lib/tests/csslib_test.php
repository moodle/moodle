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

namespace core;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/csslib.php');

/**
 * CSS optimiser test class.
 *
 * @package core
 * @category test
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csslib_test extends \advanced_testcase {

    /**
     * Test that css_is_colour function throws an exception.
     */
    public function test_css_is_colour() {
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('css_is_colour() can not be used anymore.');
        css_is_colour();
    }

    /**
     * Test that css_is_width function throws an exception.
     */
    public function test_css_is_width() {
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('css_is_width() can not be used anymore.');
        css_is_width();
    }
}
