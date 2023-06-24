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

namespace core_external;

/**
 * Unit tests for core_external\external_api.
 *
 * @package     core_external
 * @category    test
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @covers      \core_external\external_api
 */
class external_api_test extends \advanced_testcase {
    /**
     * Test the validate_parameters method.
     *
     * @covers \core_external\external_api::validate_parameters
     */
    public function test_validate_params(): void {
        $params = ['text' => 'aaa', 'someid' => '6'];
        $description = new external_function_parameters([
            'someid' => new external_value(PARAM_INT, 'Some int value'),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value'),
        ]);
        $result = external_api::validate_parameters($description, $params);
        $this->assertCount(2, $result);
        reset($result);
        $this->assertSame('someid', key($result));
        $this->assertSame(6, $result['someid']);
        $this->assertSame('aaa', $result['text']);

        $params = [
            'someids' => ['1', 2, 'a' => '3'],
            'scalar' => 666,
        ];
        $description = new external_function_parameters([
            'someids' => new external_multiple_structure(new external_value(PARAM_INT, 'Some ID')),
            'scalar'  => new external_value(PARAM_ALPHANUM, 'Some text value'),
        ]);
        $result = external_api::validate_parameters($description, $params);
        $this->assertCount(2, $result);
        reset($result);
        $this->assertSame('someids', key($result));
        $this->assertEquals([0 => 1, 1 => 2, 2 => 3], $result['someids']);
        $this->assertSame('666', $result['scalar']);

        $params = ['text' => 'aaa'];
        $description = new external_function_parameters([
            'someid' => new external_value(PARAM_INT, 'Some int value', VALUE_DEFAULT),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value'),
        ]);
        $result = external_api::validate_parameters($description, $params);
        $this->assertCount(2, $result);
        reset($result);
        $this->assertSame('someid', key($result));
        $this->assertNull($result['someid']);
        $this->assertSame('aaa', $result['text']);

        $params = ['text' => 'aaa'];
        $description = new external_function_parameters([
            'someid' => new external_value(PARAM_INT, 'Some int value', VALUE_DEFAULT, 6),
            'text'   => new external_value(PARAM_ALPHA, 'Some text value'),
        ]);
        $result = external_api::validate_parameters($description, $params);
        $this->assertCount(2, $result);
        reset($result);
        $this->assertSame('someid', key($result));
        $this->assertSame(6, $result['someid']);
        $this->assertSame('aaa', $result['text']);

        // Missing required value (an exception is thrown).
        $testdata = [];
        try {
            external_api::clean_returnvalue($description, $testdata);
            $this->fail('Exception expected');
        } catch (\moodle_exception $ex) {
            $this->assertInstanceOf(\invalid_response_exception::class, $ex);
            $this->assertSame('Invalid response value detected (Error in response - '
                . 'Missing following required key in a single structure: text)', $ex->getMessage());
        }

        // Test nullable external_value may optionally return data.
        $description = new external_function_parameters([
            'value' => new external_value(PARAM_INT, '', VALUE_REQUIRED, null, NULL_ALLOWED)
        ]);
        $testdata = ['value' => null];
        $cleanedvalue = external_api::clean_returnvalue($description, $testdata);
        $this->assertSame($testdata, $cleanedvalue);
        $testdata = ['value' => 1];
        $cleanedvalue = external_api::clean_returnvalue($description, $testdata);
        $this->assertSame($testdata, $cleanedvalue);

        // Test nullable external_single_structure may optionally return data.
        $description = new external_function_parameters([
            'value' => new external_single_structure(['value2' => new external_value(PARAM_INT)],
                '', VALUE_REQUIRED, null, NULL_ALLOWED)
        ]);
        $testdata = ['value' => null];
        $cleanedvalue = external_api::clean_returnvalue($description, $testdata);
        $this->assertSame($testdata, $cleanedvalue);
        $testdata = ['value' => ['value2' => 1]];
        $cleanedvalue = external_api::clean_returnvalue($description, $testdata);
        $this->assertSame($testdata, $cleanedvalue);

        // Test nullable external_multiple_structure may optionally return data.
        $description = new external_function_parameters([
            'value' => new external_multiple_structure(
                new external_value(PARAM_INT), '', VALUE_REQUIRED, null, NULL_ALLOWED)
        ]);
        $testdata = ['value' => null];
        $cleanedvalue = external_api::clean_returnvalue($description, $testdata);
        $this->assertSame($testdata, $cleanedvalue);
        $testdata = ['value' => [1]];
        $cleanedvalue = external_api::clean_returnvalue($description, $testdata);
        $this->assertSame($testdata, $cleanedvalue);
    }

    /**
     * Test for clean_returnvalue() for testing that returns the PHP type.
     *
     * @covers \core_external\external_api::clean_returnvalue
     */
    public function test_clean_returnvalue_return_php_type(): void {
        $returndesc = new external_single_structure([
            'value' => new external_value(PARAM_RAW, 'Some text', VALUE_OPTIONAL, null, NULL_NOT_ALLOWED),
        ]);

        // Check return type on exception because the external values does not allow NULL values.
        $testdata = ['value' => null];
        try {
            $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf(\invalid_response_exception::class, $e);
            $this->assertStringContainsString('of PHP type "NULL"', $e->debuginfo);
        }
    }

    /**
     * Test for clean_returnvalue().
     *
     * @covers \core_external\external_api::clean_returnvalue
     */
    public function test_clean_returnvalue(): void {
        // Build some return value decription.
        $returndesc = new external_multiple_structure(
            new external_single_structure(
                [
                    'object' => new external_single_structure(
                                ['value1' => new external_value(PARAM_INT, 'this is a int')]),
                    'value2' => new external_value(PARAM_TEXT, 'some text', VALUE_OPTIONAL),
                ]
            ));

        // Clean an object (it should be cast into an array).
        $object = new \stdClass();
        $object->value1 = 1;
        $singlestructure['object'] = $object;
        $singlestructure['value2'] = 'Some text';
        $testdata = [$singlestructure];
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $cleanedsinglestructure = array_pop($cleanedvalue);
        $this->assertSame($object->value1, $cleanedsinglestructure['object']['value1']);
        $this->assertSame($singlestructure['value2'], $cleanedsinglestructure['value2']);

        // Missing VALUE_OPTIONAL.
        $object = new \stdClass();
        $object->value1 = 1;
        $singlestructure = new \stdClass();
        $singlestructure->object = $object;
        $testdata = [$singlestructure];
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $cleanedsinglestructure = array_pop($cleanedvalue);
        $this->assertSame($object->value1, $cleanedsinglestructure['object']['value1']);
        $this->assertArrayNotHasKey('value2', $cleanedsinglestructure);

        // Unknown attribute (the value should be ignored).
        $object = [];
        $object['value1'] = 1;
        $singlestructure = [];
        $singlestructure['object'] = $object;
        $singlestructure['value2'] = 'Some text';
        $singlestructure['unknownvalue'] = 'Some text to ignore';
        $testdata = [$singlestructure];
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $cleanedsinglestructure = array_pop($cleanedvalue);
        $this->assertSame($object['value1'], $cleanedsinglestructure['object']['value1']);
        $this->assertSame($singlestructure['value2'], $cleanedsinglestructure['value2']);
        $this->assertArrayNotHasKey('unknownvalue', $cleanedsinglestructure);

        // Missing required value (an exception is thrown).
        $object = [];
        $singlestructure = [];
        $singlestructure['object'] = $object;
        $singlestructure['value2'] = 'Some text';
        $testdata = [$singlestructure];
        try {
            external_api::clean_returnvalue($returndesc, $testdata);
            $this->fail('Exception expected');
        } catch (\moodle_exception $ex) {
            $this->assertInstanceOf(\invalid_response_exception::class, $ex);
            $this->assertSame('Invalid response value detected (object => Invalid response value detected '
                . '(Error in response - Missing following required key in a single structure: value1): Error in response - '
                . 'Missing following required key in a single structure: value1)', $ex->getMessage());
        }

        // Fail if no data provided when value required.
        $testdata = null;
        try {
            external_api::clean_returnvalue($returndesc, $testdata);
            $this->fail('Exception expected');
        } catch (\moodle_exception $ex) {
            $this->assertInstanceOf(\invalid_response_exception::class, $ex);
            $this->assertSame('Invalid response value detected (Only arrays accepted. The bad value is: \'\')',
                $ex->getMessage());
        }

        // Test nullable external_multiple_structure may optionally return data.
        $returndesc = new external_multiple_structure(
            new external_value(PARAM_INT),
            '', VALUE_REQUIRED, null, NULL_ALLOWED);
        $testdata = null;
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $this->assertSame($testdata, $cleanedvalue);
        $testdata = [1];
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $this->assertSame($testdata, $cleanedvalue);

        // Test nullable external_single_structure may optionally return data.
        $returndesc = new external_single_structure(['value' => new external_value(PARAM_INT)],
            '', VALUE_REQUIRED, null, NULL_ALLOWED);
        $testdata = null;
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $this->assertSame($testdata, $cleanedvalue);
        $testdata = ['value' => 1];
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $this->assertSame($testdata, $cleanedvalue);

        // Test nullable external_value may optionally return data.
        $returndesc = new external_value(PARAM_INT, '', VALUE_REQUIRED, null, NULL_ALLOWED);
        $testdata = null;
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $this->assertSame($testdata, $cleanedvalue);
        $testdata = 1;
        $cleanedvalue = external_api::clean_returnvalue($returndesc, $testdata);
        $this->assertSame($testdata, $cleanedvalue);
    }

    /**
     * Test \core_external\external_api::get_context_from_params().
     *
     * @covers \core_external\external_api::get_context_from_params
     */
    public function test_get_context_from_params(): void {
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $realcontext = \context_course::instance($course->id);

        // Use context id.
        $fetchedcontext = $this->get_context_from_params(["contextid" => $realcontext->id]);
        $this->assertEquals($realcontext, $fetchedcontext);

        // Use context level and instance id.
        $fetchedcontext = $this->get_context_from_params(["contextlevel" => "course", "instanceid" => $course->id]);
        $this->assertEquals($realcontext, $fetchedcontext);

        // Use context level numbers instead of legacy short level names.
        $fetchedcontext = $this->get_context_from_params(
            ["contextlevel" => \core\context\course::LEVEL, "instanceid" => $course->id]);
        $this->assertEquals($realcontext, $fetchedcontext);

        // Passing empty values.
        try {
            $fetchedcontext = $this->get_context_from_params(["contextid" => 0]);
            $this->fail('Exception expected from get_context_wrapper()');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf(\invalid_parameter_exception::class, $e);
        }

        try {
            $fetchedcontext = $this->get_context_from_params(["instanceid" => 0]);
            $this->fail('Exception expected from get_context_wrapper()');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf(\invalid_parameter_exception::class, $e);
        }

        try {
            $fetchedcontext = $this->get_context_from_params(["contextid" => null]);
            $this->fail('Exception expected from get_context_wrapper()');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf(\invalid_parameter_exception::class, $e);
        }

        // Tests for context with instanceid equal to 0 (System context).
        $realcontext = \context_system::instance();
        $fetchedcontext = $this->get_context_from_params(["contextlevel" => "system", "instanceid" => 0]);
        $this->assertEquals($realcontext, $fetchedcontext);

        // Passing wrong level name.
        try {
            $fetchedcontext = $this->get_context_from_params(["contextlevel" => "random", "instanceid" => $course->id]);
            $this->fail('exception expected when level name is invalid');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('invalid_parameter_exception', $e);
            $this->assertSame('Invalid parameter value detected (Invalid context level = random)', $e->getMessage());
        }

        // Passing wrong level number.
        try {
            $fetchedcontext = $this->get_context_from_params(["contextlevel" => -10, "instanceid" => $course->id]);
            $this->fail('exception expected when level name is invalid');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('invalid_parameter_exception', $e);
            $this->assertSame('Invalid parameter value detected (Invalid context level = -10)', $e->getMessage());
        }
    }

    /**
     * Test \core_external\external_api::get_context()_from_params parameter validation.
     *
     * @covers \core_external\external_api::get_context
     */
    public function test_get_context_params(): void {
        global $USER;

        // Call without correct context details.
        $this->expectException('invalid_parameter_exception');
        $this->get_context_from_params(['roleid' => 3, 'userid' => $USER->id]);
    }

    /**
     * Test \core_external\external_api::get_context()_from_params parameter validation.
     *
     * @covers \core_external\external_api::get_context
     */
    public function test_get_context_params2(): void {
        global $USER;

        // Call without correct context details.
        $this->expectException('invalid_parameter_exception');
        $this->get_context_from_params(['roleid' => 3, 'userid' => $USER->id, 'contextlevel' => "course"]);
    }

    /**
     * Test \core_external\external_api::get_context()_from_params parameter validation.
     * @covers \core_external\external_api::get_context
     */
    public function test_get_context_params3(): void {
        global $USER;

        // Call without correct context details.
        $this->resetAfterTest(true);
        $course = self::getDataGenerator()->create_course();
        $this->expectException('invalid_parameter_exception');
        $this->get_context_from_params(['roleid' => 3, 'userid' => $USER->id, 'instanceid' => $course->id]);
    }

    /**
     * Data provider for the test_all_external_info test.
     *
     * @return array
     */
    public function all_external_info_provider(): array {
        global $DB;

        // We are testing here that all the external function descriptions can be generated without
        // producing warnings. E.g. misusing optional params will generate a debugging message which
        // will fail this test.
        $functions = $DB->get_records('external_functions', [], 'name');
        $return = [];
        foreach ($functions as $f) {
            $return[$f->name] = [$f];
        }
        return $return;
    }

    /**
     * Test \core_external\external_api::external_function_info.
     *
     * @runInSeparateProcess
     * @dataProvider all_external_info_provider
     * @covers \core_external\external_api::external_function_info
     * @param \stdClass $definition
     */
    public function test_all_external_info(\stdClass $definition): void {
        $desc = external_api::external_function_info($definition);
        $this->assertNotEmpty($desc->name);
        $this->assertNotEmpty($desc->classname);
        $this->assertNotEmpty($desc->methodname);
        $this->assertEquals($desc->component, clean_param($desc->component, PARAM_COMPONENT));
        $this->assertInstanceOf(external_function_parameters::class, $desc->parameters_desc);
        if ($desc->returns_desc != null) {
            $this->assertInstanceOf(external_description::class, $desc->returns_desc);
        }
    }

    /**
     * Test the \core_external\external_api::call_external_function() function.
     *
     * @covers \core_external\external_api::call_external_function
     */
    public function test_call_external_function(): void {
        global $PAGE, $COURSE, $CFG;

        $this->resetAfterTest(true);

        // Call some webservice functions and verify they are correctly handling $PAGE and $COURSE.
        // First test a function that calls validate_context outside a course.
        $this->setAdminUser();
        $category = $this->getDataGenerator()->create_category();
        $params = [
            'contextid' => \context_coursecat::instance($category->id)->id,
            'name' => 'aaagrrryyy',
            'idnumber' => '',
            'description' => '',
        ];
        $cohort1 = $this->getDataGenerator()->create_cohort($params);
        $cohort2 = $this->getDataGenerator()->create_cohort();

        $beforepage = $PAGE;
        $beforecourse = $COURSE;
        $params = ['cohortids' => [$cohort1->id, $cohort2->id]];
        $result = external_api::call_external_function('core_cohort_get_cohorts', $params);

        $this->assertSame($beforepage, $PAGE);
        $this->assertSame($beforecourse, $COURSE);

        // Now test a function that calls validate_context inside a course.
        $course = $this->getDataGenerator()->create_course();

        $beforepage = $PAGE;
        $beforecourse = $COURSE;
        $params = ['courseid' => $course->id, 'options' => []];
        $result = external_api::call_external_function('core_enrol_get_enrolled_users', $params);

        $this->assertSame($beforepage, $PAGE);
        $this->assertSame($beforecourse, $COURSE);

        // Test a function that triggers a PHP exception.
        require_once($CFG->dirroot . '/lib/tests/fixtures/test_external_function_throwable.php');

        // Call our test function.
        $result = \test_external_function_throwable::call_external_function('core_throw_exception', [], false);

        $this->assertTrue($result['error']);
        $this->assertArrayHasKey('exception', $result);
        $this->assertEquals($result['exception']->message, 'Exception - Modulo by zero');
    }

    /**
     * Call the get_contect_from_params methods on the api class.
     *
     * @return mixed
     */
    protected function get_context_from_params() {
        $rc = new \ReflectionClass(external_api::class);
        $method = $rc->getMethod('get_context_from_params');
        $method->setAccessible(true);
        return $method->invokeArgs(null, func_get_args());
    }
}
