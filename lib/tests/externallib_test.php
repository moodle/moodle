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


class core_externallib_testcase extends advanced_testcase {
    public function test_validate_params() {
        $params = array('text'=>'aaa', 'someid'=>'6');
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value'),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertCount(2, $result);
        reset($result);
        $this->assertSame('someid', key($result));
        $this->assertSame(6, $result['someid']);
        $this->assertSame('aaa', $result['text']);

        $params = array('someids'=>array('1', 2, 'a'=>'3'), 'scalar'=>666);
        $description = new external_function_parameters(array('someids' => new external_multiple_structure(new external_value(PARAM_INT, 'Some ID')),
            'scalar'  => new external_value(PARAM_ALPHANUM, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertCount(2, $result);
        reset($result);
        $this->assertSame('someids', key($result));
        $this->assertEquals(array(0=>1, 1=>2, 2=>3), $result['someids']);
        $this->assertSame('666', $result['scalar']);

        $params = array('text'=>'aaa');
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value', false),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertCount(2, $result);
        reset($result);
        $this->assertSame('someid', key($result));
        $this->assertNull($result['someid']);
        $this->assertSame('aaa', $result['text']);

        $params = array('text'=>'aaa');
        $description = new external_function_parameters(array('someid' => new external_value(PARAM_INT, 'Some int value', false, 6),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value')));
        $result = external_api::validate_parameters($description, $params);
        $this->assertCount(2, $result);
        reset($result);
        $this->assertSame('someid', key($result));
        $this->assertSame(6, $result['someid']);
        $this->assertSame('aaa', $result['text']);
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
        $this->assertSame($object->value1, $cleanedsinglestructure['object']['value1']);
        $this->assertSame($singlestructure['value2'], $cleanedsinglestructure['value2']);

        // Missing VALUE_OPTIONAL.
        $object = new stdClass();
        $object->value1 = 1;
        $singlestructure = new stdClass();
        $singlestructure->object = $object;
        $testdata = array($singlestructure);
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $cleanedsinglestructure = array_pop($cleanedvalue);
        $this->assertSame($object->value1, $cleanedsinglestructure['object']['value1']);
        $this->assertArrayNotHasKey('value2', $cleanedsinglestructure);

        // Unknown attribute (the value should be ignored).
        $object = array();
        $object['value1'] = 1;
        $singlestructure = array();
        $singlestructure['object'] = $object;
        $singlestructure['value2'] = 'Some text';
        $singlestructure['unknownvalue'] = 'Some text to ignore';
        $testdata = array($singlestructure);
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $cleanedsinglestructure = array_pop($cleanedvalue);
        $this->assertSame($object['value1'], $cleanedsinglestructure['object']['value1']);
        $this->assertSame($singlestructure['value2'], $cleanedsinglestructure['value2']);
        $this->assertArrayNotHasKey('unknownvalue', $cleanedsinglestructure);

        // Missing required value (an exception is thrown).
        $object = array();
        $singlestructure = array();
        $singlestructure['object'] = $object;
        $singlestructure['value2'] = 'Some text';
        $testdata = array($singlestructure);
        $this->setExpectedException('invalid_response_exception');
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
    }
    /*
     * Test external_api::get_context_from_params().
     */
    public function test_get_context_from_params() {
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $realcontext = context_course::instance($course->id);

        // Use context id.
        $fetchedcontext = test_exernal_api::get_context_wrapper(array("contextid" => $realcontext->id));
        $this->assertEquals($realcontext, $fetchedcontext);

        // Use context level and instance id.
        $fetchedcontext = test_exernal_api::get_context_wrapper(array("contextlevel" => "course", "instanceid" => $course->id));
        $this->assertEquals($realcontext, $fetchedcontext);

        // Passing wrong level.
        $this->setExpectedException('invalid_parameter_exception');
        $fetchedcontext = test_exernal_api::get_context_wrapper(array("contextlevel" => "random", "instanceid" => $course->id));
    }

    /*
     * Test external_api::get_context()_from_params parameter validation.
     */
    public function test_get_context_params() {
        global $USER;

        // Call without correct context details.
        $this->setExpectedException('invalid_parameter_exception');
        test_exernal_api::get_context_wrapper(array('roleid' => 3, 'userid' => $USER->id));
    }

    /*
     * Test external_api::get_context()_from_params parameter validation.
     */
    public function test_get_context_params2() {
        global $USER;

        // Call without correct context details.
        $this->setExpectedException('invalid_parameter_exception');
        test_exernal_api::get_context_wrapper(array('roleid' => 3, 'userid' => $USER->id, 'contextlevel' => "course"));
    }

    /*
     * Test external_api::get_context()_from_params parameter validation.
     */
    public function test_get_context_params3() {
        global $USER;

        // Call without correct context details.
        $this->resetAfterTest(true);
        $course = self::getDataGenerator()->create_course();
        $this->setExpectedException('invalid_parameter_exception');
        test_exernal_api::get_context_wrapper(array('roleid' => 3, 'userid' => $USER->id, 'instanceid' => $course->id));
    }
}

/*
 * Just a wrapper to access protected apis for testing
 */
class test_exernal_api extends external_api {

    public static function get_context_wrapper($params) {
        return self::get_context_from_params($params);
    }
}
