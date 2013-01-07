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
 * @package    core
 * @subpackage phpunit
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');


class externallib_testcase extends basic_testcase {
    public function test_validate_params() {
        $params = array('text'=>'aaa', 'someid'=>'6',);
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value'),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertEquals(count($result), 2);
        reset($result);
        $this->assertTrue(key($result) === 'someid');
        $this->assertTrue($result['someid'] === 6);
        $this->assertTrue($result['text'] === 'aaa');


        $params = array('someids'=>array('1', 2, 'a'=>'3'), 'scalar'=>666);
        $description = new external_function_parameters(array('someids' => new external_multiple_structure(new external_value(PARAM_INT, 'Some ID')),
            'scalar'  => new external_value(PARAM_ALPHANUM, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertEquals(count($result), 2);
        reset($result);
        $this->assertTrue(key($result) === 'someids');
        $this->assertTrue($result['someids'] == array(0=>1, 1=>2, 2=>3));
        $this->assertTrue($result['scalar'] === '666');


        $params = array('text'=>'aaa');
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value', false),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertEquals(count($result), 2);
        reset($result);
        $this->assertTrue(key($result) === 'someid');
        $this->assertTrue($result['someid'] === null);
        $this->assertTrue($result['text'] === 'aaa');


        $params = array('text'=>'aaa');
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value', false, 6),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertEquals(count($result), 2);
        reset($result);
        $this->assertTrue(key($result) === 'someid');
        $this->assertTrue($result['someid'] === 6);
        $this->assertTrue($result['text'] === 'aaa');
    }

    /**
     * Test for clean_returnvalue().
     */
    public function test_clean_returnvalue() {

        // Build some return value decription.
        $returndesc = new external_multiple_structure(
            new external_single_structure(
                array(
                    'object' => new external_single_structure(
                                array('value1' => new external_value(PARAM_INT, 'this is a int'))),
                    'value2' => new external_value(PARAM_TEXT, 'some text', VALUE_OPTIONAL))
            ));

        // Clean an object (it should be cast into an array).
        $object = new stdClass();
        $object->value1 = 1;
        $singlestructure['object'] = $object;
        $singlestructure['value2'] = 'Some text';
        $testdata = array($singlestructure);
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $cleanedsinglestructure = array_pop($cleanedvalue);
        $this->assertEquals($object->value1, $cleanedsinglestructure['object']['value1']);
        $this->assertEquals($singlestructure['value2'], $cleanedsinglestructure['value2']);

        // Missing VALUE_OPTIONAL.
        $object = new stdClass();
        $object->value1 = 1;
        $singlestructure = new stdClass();
        $singlestructure->object = $object;
        $testdata = array($singlestructure);
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $cleanedsinglestructure = array_pop($cleanedvalue);
        $this->assertEquals($object->value1, $cleanedsinglestructure['object']['value1']);
        $this->assertEquals(false, array_key_exists('value2', $cleanedsinglestructure));

        // Unknown attribut (the value should be ignored).
        $object = array();
        $object['value1'] = 1;
        $singlestructure = array();
        $singlestructure['object'] = $object;
        $singlestructure['value2'] = 'Some text';
        $singlestructure['unknownvalue'] = 'Some text to ignore';
        $testdata = array($singlestructure);
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $cleanedsinglestructure = array_pop($cleanedvalue);
        $this->assertEquals($object['value1'], $cleanedsinglestructure['object']['value1']);
        $this->assertEquals($singlestructure['value2'], $cleanedsinglestructure['value2']);
        $this->assertEquals(false, array_key_exists('unknownvalue', $cleanedsinglestructure));


        // Missing required value (an exception is thrown).
        $object = array();
        $singlestructure = array();
        $singlestructure['object'] = $object;
        $singlestructure['value2'] = 'Some text';
        $testdata = array($singlestructure);
        $this->setExpectedException('invalid_response_exception');
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
    }
}
