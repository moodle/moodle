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

    public function test_external_format_text() {
        $settings = external_settings::get_instance();

        $currentraw = $settings->get_raw();
        $currentfilter = $settings->get_filter();

        $settings->set_raw(true);
        $settings->set_filter(false);
        $context = context_system::instance();

        $test = '$$ \pi $$';
        $testformat = FORMAT_MARKDOWN;
        $correct = array($test, $testformat);
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0), $correct);

        $settings->set_raw(false);
        $settings->set_filter(true);

        $test = '$$ \pi $$';
        $testformat = FORMAT_MARKDOWN;
        $correct = array('<span class="nolink"><span class="filter_mathjaxloader_equation"><p>$$ \pi $$</p>
</span></span>', FORMAT_HTML);
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0), $correct);

        // Filters can be opted out from by the developer.
        $test = '$$ \pi $$';
        $testformat = FORMAT_MARKDOWN;
        $correct = array('<p>$$ \pi $$</p>
', FORMAT_HTML);
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, ['filter' => false]), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>';
        $testformat = FORMAT_HTML;
        $correct = array($test, FORMAT_HTML);
        $options = array('allowid' => true);
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>';
        $testformat = FORMAT_HTML;
        $correct = array('<p><a></a><a href="#test">Text</a></p>', FORMAT_HTML);
        $options = new StdClass();
        $options->allowid = false;
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>'."\n".'Newline';
        $testformat = FORMAT_MOODLE;
        $correct = array('<p><a id="test"></a><a href="#test">Text</a></p> Newline', FORMAT_HTML);
        $options = new StdClass();
        $options->newlines = false;
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>';
        $testformat = FORMAT_MOODLE;
        $correct = array('<div class="text_to_html">'.$test.'</div>', FORMAT_HTML);
        $options = new StdClass();
        $options->para = true;
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);

        $test = '<p><a id="test"></a><a href="#test">Text</a></p>';
        $testformat = FORMAT_MOODLE;
        $correct = array($test, FORMAT_HTML);
        $options = new StdClass();
        $options->context = $context;
        $this->assertSame(external_format_text($test, $testformat, $context->id, 'core', '', 0, $options), $correct);

        $settings->set_raw($currentraw);
        $settings->set_filter($currentfilter);
    }

    public function test_external_format_string() {
        $this->resetAfterTest();
        $settings = external_settings::get_instance();
        $currentraw = $settings->get_raw();
        $currentfilter = $settings->get_filter();

        // Enable multilang filter to on content and heading.
        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', 1);
        $filtermanager = filter_manager::instance();
        $filtermanager->reset_caches();

        $settings->set_raw(true);
        $settings->set_filter(true);
        $context = context_system::instance();

        $test = '<span lang="en" class="multilang">EN</span><span lang="fr" class="multilang">FR</span> ' .
            '<script>hi</script> <h3>there</h3>!';
        $correct = $test;
        $this->assertSame($correct, external_format_string($test, $context->id));

        $settings->set_raw(false);
        $settings->set_filter(false);

        $test = '<span lang="en" class="multilang">EN</span><span lang="fr" class="multilang">FR</span> ' .
            '<script>hi</script> <h3>there</h3>?';
        $correct = 'ENFR hi there?';
        $this->assertSame($correct, external_format_string($test, $context->id));

        $settings->set_filter(true);

        $test = '<span lang="en" class="multilang">EN</span><span lang="fr" class="multilang">FR</span> ' .
            '<script>hi</script> <h3>there</h3>@';
        $correct = 'EN hi there@';
        $this->assertSame($correct, external_format_string($test, $context->id));

        // Filters can be opted out.
        $test = '<span lang="en" class="multilang">EN</span><span lang="fr" class="multilang">FR</span> ' .
            '<script>hi</script> <h3>there</h3>%';
        $correct = 'ENFR hi there%';
        $this->assertSame($correct, external_format_string($test, $context->id, false, ['filter' => false]));


        $settings->set_raw($currentraw);
        $settings->set_filter($currentfilter);
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

        // Passing empty values.
        try {
            $fetchedcontext = test_exernal_api::get_context_wrapper(array("contextid" => 0));
            $this->fail('Exception expected from get_context_wrapper()');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('invalid_parameter_exception', $e);
        }

        try {
            $fetchedcontext = test_exernal_api::get_context_wrapper(array("instanceid" => 0));
            $this->fail('Exception expected from get_context_wrapper()');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('invalid_parameter_exception', $e);
        }

        try {
            $fetchedcontext = test_exernal_api::get_context_wrapper(array("contextid" => null));
            $this->fail('Exception expected from get_context_wrapper()');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('invalid_parameter_exception', $e);
        }

        // Tests for context with instanceid equal to 0 (System context).
        $realcontext = context_system::instance();
        $fetchedcontext = test_exernal_api::get_context_wrapper(array("contextlevel" => "system", "instanceid" => 0));
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

    public function all_external_info_provider() {
        global $DB;

        // We are testing here that all the external function descriptions can be generated without
        // producing warnings. E.g. misusing optional params will generate a debugging message which
        // will fail this test.
        $functions = $DB->get_records('external_functions', array(), 'name');
        $return = array();
        foreach ($functions as $f) {
            $return[$f->name] = array($f);
        }
        return $return;
    }

    /**
     * @dataProvider all_external_info_provider
     */
    public function test_all_external_info($f) {
        $desc = external_api::external_function_info($f);
        $this->assertNotEmpty($desc->name);
        $this->assertNotEmpty($desc->classname);
        $this->assertNotEmpty($desc->methodname);
        $this->assertEquals($desc->component, clean_param($desc->component, PARAM_COMPONENT));
        $this->assertInstanceOf('external_function_parameters', $desc->parameters_desc);
        if ($desc->returns_desc != null) {
            $this->assertInstanceOf('external_description', $desc->returns_desc);
        }
    }

    public function test_validate_courses() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $courseids = array($c1->id, $c2->id, $c3->id);

        $this->setAdminUser();
        list($courses, $warnings) = external_util::validate_courses($courseids);
        $this->assertEmpty($warnings);
        $this->assertCount(3, $courses);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertArrayHasKey($c2->id, $courses);
        $this->assertArrayHasKey($c3->id, $courses);
        $this->assertEquals($c1->id, $courses[$c1->id]->id);
        $this->assertEquals($c2->id, $courses[$c2->id]->id);
        $this->assertEquals($c3->id, $courses[$c3->id]->id);

        $this->setUser($u1);
        list($courses, $warnings) = external_util::validate_courses($courseids);
        $this->assertCount(2, $warnings);
        $this->assertEquals($c2->id, $warnings[0]['itemid']);
        $this->assertEquals($c3->id, $warnings[1]['itemid']);
        $this->assertCount(1, $courses);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertArrayNotHasKey($c2->id, $courses);
        $this->assertArrayNotHasKey($c3->id, $courses);
        $this->assertEquals($c1->id, $courses[$c1->id]->id);
    }

    /**
     * Validate courses can re-use an array of prefetched courses.
     */
    public function test_validate_courses_prefetch() {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $c4 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u1->id, $c2->id);

        $courseids = array($c1->id, $c2->id, $c3->id);
        $courses = array($c2->id => $c2, $c3->id => $c3, $c4->id => $c4);

        $this->setUser($u1);
        list($courses, $warnings) = external_util::validate_courses($courseids, $courses);
        $this->assertCount(2, $courses);
        $this->assertCount(1, $warnings);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertSame($c2, $courses[$c2->id]);
        $this->assertArrayNotHasKey($c3->id, $courses);
        // The extra course passed is not returned.
        $this->assertArrayNotHasKey($c4->id, $courses);
    }


    public function test_call_external_function() {
        global $PAGE, $COURSE;

        $this->resetAfterTest(true);

        // Call some webservice functions and verify they are correctly handling $PAGE and $COURSE.
        // First test a function that calls validate_context outside a course.
        $this->setAdminUser();
        $category = $this->getDataGenerator()->create_category();
        $params = array(
            'contextid' => context_coursecat::instance($category->id)->id,
            'name' => 'aaagrrryyy',
            'idnumber' => '',
            'description' => ''
        );
        $cohort1 = $this->getDataGenerator()->create_cohort($params);
        $cohort2 = $this->getDataGenerator()->create_cohort();

        $beforepage = $PAGE;
        $beforecourse = $COURSE;
        $params = array('cohortids' => array($cohort1->id, $cohort2->id));
        $result = external_api::call_external_function('core_cohort_get_cohorts', $params);

        $this->assertSame($beforepage, $PAGE);
        $this->assertSame($beforecourse, $COURSE);

        // Now test a function that calls validate_context inside a course.
        $course = $this->getDataGenerator()->create_course();

        $beforepage = $PAGE;
        $beforecourse = $COURSE;
        $params = array('courseid' => $course->id, 'options' => array());
        $result = external_api::call_external_function('core_enrol_get_enrolled_users', $params);

        $this->assertSame($beforepage, $PAGE);
        $this->assertSame($beforecourse, $COURSE);
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
