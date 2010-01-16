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
 * Unit tests for ../deprecatedlib.php.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/deprecatedlib.php');

class outputlib_methods_test extends UnitTestCase {
    public function test_print_single_button() {
        global $PAGE, $CFG;

        // Basic params, absolute URL
        $link = 'http://www.test.com/index.php';
        $options = array('param1' => 'value1');
        $label = 'OK';
        $method = 'get';
        $return = true;
        $tooltip = '';
        $disabled = false;
        $jsconfirmmessage = '';
        $formid = '';

        $html = print_single_button($link, $options, $label, $method, '', $return, $tooltip, $disabled, $jsconfirmmessage, $formid);
        $this->assert(new ContainsTagWithAttributes('form', array('method' => $method, 'action' => $link)), $html);
        $this->assert(new ContainsTagWithAttributes('input', array('type' => 'hidden', 'name' => 'param1', 'value' => 'value1')), $html);
        $this->assert(new ContainsTagWithAttributes('input', array('type' => 'submit', 'value' => 'OK')), $html);

        // URL with &amp; params
        $newlink = $link . '?param1=value1&amp;param2=value2';
        $html = print_single_button($newlink, $options, $label, $method, '', $return, $tooltip, $disabled, $jsconfirmmessage, $formid);
        $this->assert(new ContainsTagWithAttributes('form', array('method' => $method, 'action' => $link)), $html);
        $this->assert(new ContainsTagWithAttributes('input', array('type' => 'hidden', 'name' => 'param1', 'value' => 'value1')), $html);
        $this->assert(new ContainsTagWithAttributes('input', array('type' => 'submit', 'value' => 'OK')), $html);

        // URL with & params
        $newlink = $link . '?param1=value1&param2=value2';
        $html = print_single_button($newlink, $options, $label, $method, '', $return, $tooltip, $disabled, $jsconfirmmessage, $formid);
        $this->assert(new ContainsTagWithAttributes('form', array('method' => $method, 'action' => $link)), $html);
        $this->assert(new ContainsTagWithAttributes('input', array('type' => 'hidden', 'name' => 'param1', 'value' => 'value1')), $html);
        $this->assert(new ContainsTagWithAttributes('input', array('type' => 'submit', 'value' => 'OK')), $html);

        // relative URL with & params
        $newlink = 'index.php?param1=value1&param2=value2';

        $oldurl = $PAGE->url;

        $PAGE->set_url('/index.php');
        $html = print_single_button($newlink, $options, $label, $method, '', $return, $tooltip, $disabled, $jsconfirmmessage, $formid);
        $this->assert(new ContainsTagWithAttributes('form', array('method' => $method, 'action' => $CFG->wwwroot . '/index.php')), $html);
        $this->assert(new ContainsTagWithAttributes('input', array('type' => 'hidden', 'name' => 'param1', 'value' => 'value1')), $html);
        $this->assert(new ContainsTagWithAttributes('input', array('type' => 'submit', 'value' => 'OK')), $html);

        $PAGE->set_url($oldurl);
    }
}
