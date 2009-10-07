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
 * Unit tests for /lib/externallib.php.
 *
 * @package   webservices
 * @copyright 2009 Pwetr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/externallib.php');

class externallib_test extends UnitTestCase {
    public function test_validate_params() {
        $params = array('text'=>'aaa', 'someid'=>'6',);
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value'),
                                                              'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertEqual(count($result), 2);
        reset($result);
        $this->assertTrue(key($result) === 'someid');
        $this->assertTrue($result['someid'] === 6);
        $this->assertTrue($result['text'] === 'aaa');


        $params = array('someids'=>array('1', 2, 'a'=>'3'), 'scalar'=>666);
        $description = new external_function_parameters(array('someids' => new external_multiple_structure(new external_value(PARAM_INT, 'Some ID')),
                                                              'scalar'  => new external_value(PARAM_ALPHANUM, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertEqual(count($result), 2);
        reset($result);
        $this->assertTrue(key($result) === 'someids');
        $this->assertTrue($result['someids'] == array(0=>1, 1=>2, 2=>3));
        $this->assertTrue($result['scalar'] === '666');


        $params = array('text'=>'aaa');
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value', false),
                                                              'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertEqual(count($result), 2);
        reset($result);
        $this->assertTrue(key($result) === 'someid');
        $this->assertTrue($result['someid'] === null);
        $this->assertTrue($result['text'] === 'aaa');


        $params = array('text'=>'aaa');
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value', false, 6),
                                                              'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertEqual(count($result), 2);
        reset($result);
        $this->assertTrue(key($result) === 'someid');
        $this->assertTrue($result['someid'] === 6);
        $this->assertTrue($result['text'] === 'aaa');
    }
}
