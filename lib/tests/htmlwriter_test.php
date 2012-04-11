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
 * Unit tests for the html_writer class.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2010 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/outputcomponents.php');


/**
 * Unit tests for the html_writer class.
 *
 * @copyright 2010 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class html_writer_testcase extends basic_testcase {

    public function test_start_tag() {
        $this->assertEquals('<div>', html_writer::start_tag('div'));
    }

    public function test_start_tag_with_attr() {
        $this->assertEquals('<div class="frog">',
            html_writer::start_tag('div', array('class' => 'frog')));
    }

    public function test_start_tag_with_attrs() {
        $this->assertEquals('<div class="frog" id="mydiv">',
            html_writer::start_tag('div', array('class' => 'frog', 'id' => 'mydiv')));
    }

    public function test_end_tag() {
        $this->assertEquals('</div>', html_writer::end_tag('div'));
    }

    public function test_empty_tag() {
        $this->assertEquals('<br />', html_writer::empty_tag('br'));
    }

    public function test_empty_tag_with_attrs() {
        $this->assertEquals('<input type="submit" value="frog" />',
            html_writer::empty_tag('input', array('type' => 'submit', 'value' => 'frog')));
    }

    public function test_nonempty_tag_with_content() {
        $this->assertEquals('<div>Hello world!</div>',
            html_writer::nonempty_tag('div', 'Hello world!'));
    }

    public function test_nonempty_tag_empty() {
        $this->assertEquals('',
            html_writer::nonempty_tag('div', ''));
    }

    public function test_nonempty_tag_null() {
        $this->assertEquals('',
            html_writer::nonempty_tag('div', null));
    }

    public function test_nonempty_tag_zero() {
        $this->assertEquals('<div class="score">0</div>',
            html_writer::nonempty_tag('div', 0, array('class' => 'score')));
    }

    public function test_nonempty_tag_zero_string() {
        $this->assertEquals('<div class="score">0</div>',
            html_writer::nonempty_tag('div', '0', array('class' => 'score')));
    }
}
