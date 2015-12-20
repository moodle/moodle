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
 * Kaltura local library phpunit tests.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/local/kaltura/locallib.php');
require_once($CFG->dirroot.'/local/kaltura/API/KalturaTypes.php');

/**
 * @group local_kaltura
 */
class local_kaltura_locallib_testcase extends advanced_testcase {
    /**
     * A Dataprovider method, providing invalid data.
     */
    public function mymedia_test_required_param_fail() {
        $data = array(
                array(
                        array(
                            // 'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            // 'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            // 'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        ),
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            // 'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            // 'module' => '',
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            // 'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            // 'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            // 'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            // Non-numeric.
                            'id' => 'string',
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            // Non-numeric.
                            'width' => 'string',
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            // Non-numeric.
                            'height' => 'string',
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            // Not an object.
                            'course' => 'string',
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MYMEDIA_MODULE,
                            'course' => new stdClass(),
                            // Non-numeric.
                            'cmid' => 'string',
                            'custom_publishdata' => '',
                        )
                ),
        );
        return $data;
    }

    /**
     * This function tests whether the parameters contain all required fields.
     * @param array $data An array of parameters that are invalid.
     * @dataProvider mymedia_test_required_param_fail
     */
    public function test_local_kaltura_validate_mymedia_required_params_fail($data) {
        $this->resetAfterTest(true);
        $result = local_kaltura_validate_mymedia_required_params($data);
        $this->assertFalse($result);
    }

    /**
     * A Dataprovider method, providing invalid data.
     */
    public function mediagallery_test_required_param_fail() {
        $data = array(
                array(
                        array(
                            // 'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            // 'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            // 'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        ),
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            // 'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            // 'module' => '',
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            // 'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            // 'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            // 'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            // Non-numeric.
                            'id' => 'string',
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            // Non-numeric.
                            'width' => 'string',
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            // Non-numeric.
                            'height' => 'string',
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            // Not an object.
                            'course' => 'string',
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_MEDIAGALLERY_MODULE,
                            'course' => new stdClass(),
                            // Non-numeric.
                            'cmid' => 'string',
                            'custom_publishdata' => '',
                        )
                ),
        );
        return $data;
    }

    /**
     * This function tests whether the parameters contain all required fields.
     * @param array $data An array of parameters that are invalid.
     * @dataProvider mediagallery_test_required_param_fail
     */
    public function test_local_kaltura_validate_coursegallery_required_params_fail($data) {
        $this->resetAfterTest(true);
        $result = local_kaltura_validate_mediagallery_required_params($data);
        $this->assertFalse($result);
    }

    /**
     * This function tests whether the parameters contain all required fields.
     */
    public function test_local_kaltura_validate_coursegallery_required_params() {
        $this->resetAfterTest(true);
        $data = array(
            'id' => 1,
            'title' => 'title',
            'width' => 100,
            'height' => 100,
            'module' => KAF_MEDIAGALLERY_MODULE,
            'course' => new stdClass(),
            'cmid' => 0,
            'custom_publishdata' => ''
        );

        $result = local_kaltura_validate_mediagallery_required_params($data);
        $this->assertTrue($result);
    }

    /**
     * A Dataprovider method, providing invalid data.
     */
    public function browseembed_test_required_param_fail() {
        $data = array(
                array(
                        array(
                            // 'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            // 'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            // 'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        ),
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            // 'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            // 'module' => '',
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            // 'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            // 'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            // 'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            // Non-numeric.
                            'id' => 'string',
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            // Non-numeric.
                            'width' => 'string',
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            // Non-numeric.
                            'height' => 'string',
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            // Not an object.
                            'course' => 'string',
                            'cmid' => 0,
                            'custom_publishdata' => '',
                        )
                ),
                array(
                        array(
                            'id' => 1,
                            'title' => 'title',
                            'width' => 100,
                            'height' => 100,
                            'module' => KAF_BROWSE_EMBED_MODULE,
                            'course' => new stdClass(),
                            // Non-numeric.
                            'cmid' => 'string',
                            'custom_publishdata' => '',
                        )
                ),
        );
        return $data;
    }

    /**
     * This function tests whether the parameters contain all required fields.
     * @param array $data An array of parameters that are invalid.
     * @dataProvider browseembed_test_required_param_fail
     */
    public function test_local_kaltura_validate_browseembed_required_params_fail($data) {
        $result = local_kaltura_validate_browseembed_required_params($data);
        $this->assertFalse($result);
    }

    /**
     * This function tests whether the parameters contain all required fields.
     */
    public function test_local_kaltura_validate_browseembed_required_params() {
        $data = array(
            'id' => 1,
            'title' => 'title',
            'width' => 100,
            'height' => 100,
            'module' => KAF_BROWSE_EMBED_MODULE,
            'course' => new stdClass(),
            'cmid' => 0,
            'custom_publishdata' => ''
        );

        $result = local_kaltura_validate_browseembed_required_params($data);
        $this->assertTrue($result);
    }

    /**
     * This function tests the return values for @see local_kaltura_get_lti_launch_container().
     */
    public function test_local_kaltura_get_lti_launch_container() {
        global $CFG;

        $this->resetAfterTest(true);
        $result = local_kaltura_get_lti_launch_container(true);
        $this->assertEquals(LTI_LAUNCH_CONTAINER_EMBED, $result);

        $result = local_kaltura_get_lti_launch_container(false);
        $this->assertEquals(LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS, $result);
    }

    /**
     * Data provider for different KAF service names.
     */
    public function module_name_test_fail() {
        $data = array(
                array('nothing'),
                array(''),
                array(1234)
        );
        return $data;
    }

    /**
     * Test validating available KAF services.
     * @param array $data An array of parameters that are invalid.
     * @dataProvider module_name_test_fail
     */
    public function test_local_kaltura_validate_kaf_module_request_fail($data) {
        $this->resetAfterTest(true);
        $result = local_kaltura_validate_kaf_module_request($data);
        $this->assertFalse($result);
    }

    /**
     * Data provider for different KAF service names.
     */
    public function module_name_test() {
        $data = array(
                array('mymedia'),
                array('coursegallery'),
        );
        return $data;
    }

    /**
     * Test validating available KAF services.
     * @param array $data An array of parameters that is valid.
     * @dataProvider module_name_test
     */
    public function test_local_kaltura_validate_kaf_module_request($data) {
        $this->resetAfterTest(true);
        $result = local_kaltura_validate_kaf_module_request($data);
        $this->assertTrue($result);
    }

    /**
     * Test that the correct end point is returned.
     */
    public function test_local_kaltura_get_endpoint() {
        $this->resetAfterTest(true);
        $result = local_kaltura_get_endpoint(KAF_MYMEDIA_MODULE);
        $this->assertEquals(KAF_MYMEDIA_ENDPOINT, $result);
        $result = local_kaltura_get_endpoint(KAF_MEDIAGALLERY_MODULE);
        $this->assertEquals(KAF_MEDIAGALLERY_ENDPOINT, $result);
        $result = local_kaltura_get_endpoint(KAF_BROWSE_EMBED_MODULE);
        $this->assertEquals(KAF_BROWSE_EMBED_ENDPOINT, $result);
    }

    /**
     * This functions tests properties of the mod_lti object returned by local_kaltura_format_lti_instance_object().
     */
    public function test_local_kaltura_format_lti_instance_object() {
        $this->resetAfterTest(true);

        set_config('partner_id', 12345, 'local_kaltura');
        set_config('adminsecret', 54321, 'local_kaltura');
        set_config('kaf_uri', 'http://phpunit.tests/local_kaltura/tests/', 'local_kaltura');
        $expecteduri = 'phpunit.tests/local_kaltura/tests';

        $configsettings = get_config('local_kaltura');

        $course = new stdClass();
        $course->id = 1;
        $param = array(
            'id' => 1,
            'module' => 'mymedia',
            'course' => $course,
            'title' => 'phpunit test',
            'width' => 100,
            'height' => 100,
            'cmid' => 0,
            'intro' => 'phpunitintro',
        );

        $expected = new stdClass();

        $result = local_kaltura_format_lti_instance_object($param);

        $this->assertObjectHasAttribute('course', $result);
        $this->assertObjectHasAttribute('id', $result);
        $this->assertObjectHasAttribute('name', $result);
        $this->assertObjectHasAttribute('intro', $result);
        $this->assertObjectHasAttribute('instructorchoicesendname', $result);
        $this->assertObjectHasAttribute('instructorchoicesendemailaddr', $result);
        $this->assertObjectHasAttribute('instructorcustomparameters', $result);
        $this->assertObjectHasAttribute('instructorchoiceacceptgrades', $result);
        $this->assertObjectHasAttribute('instructorchoiceallowroster', $result);
        $this->assertObjectHasAttribute('resourcekey', $result);
        $this->assertObjectHasAttribute('password', $result);
        $this->assertObjectHasAttribute('toolurl', $result);
        $this->assertObjectHasAttribute('securetool', $result);
        $this->assertObjectHasAttribute('forcessl', $result);
        $this->assertObjectHasAttribute('cmid', $result);

        $this->assertEquals(1, $result->course);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('phpunit test', $result->name);
        $this->assertEquals('phpunitintro', $result->intro);
        $this->assertEquals(LTI_SETTING_ALWAYS, $result->instructorchoicesendname);
        $this->assertEquals(LTI_SETTING_ALWAYS, $result->instructorchoicesendemailaddr);
        $this->assertEquals('', $result->instructorcustomparameters);
        $this->assertEquals(LTI_SETTING_NEVER, $result->instructorchoiceacceptgrades);
        $this->assertEquals(LTI_SETTING_NEVER, $result->instructorchoiceallowroster);
        $this->assertEquals($configsettings->partner_id, $result->resourcekey);
        $this->assertEquals($configsettings->adminsecret, $result->password);
        $this->assertEquals('http://phpunit.tests/local_kaltura/tests/'.KAF_MYMEDIA_ENDPOINT, $result->toolurl);
        $this->assertEquals('https://phpunit.tests/local_kaltura/tests/'.KAF_MYMEDIA_ENDPOINT, $result->securetool);
        $this->assertEquals(0, $result->cmid);
    }

    /**
     * Test the formatting of an array to be used by mod_lti.
     */
    public function test_local_kaltura_format_typeconfig() {
        $this->resetAfterTest(true);

        $param = new stdClass();
        $param->instructorchoicesendname = 0;
        $param->instructorchoicesendemailaddr = 'a@a.com';
        $param->instructorcustomparameters = '';
        $param->instructorchoiceacceptgrades = 0;
        $param->instructorchoiceallowroster = 3;

        $expected = array(
            'sendname' => 0,
            'sendemailaddr' => 'a@a.com',
            'customparameters' => '',
            'acceptgrades' => 0,
            'allowroster' => 3,
            'launchcontainer' => 2,
        );

        $result = local_kaltura_format_typeconfig($param);

        ksort($result);
        ksort($expected);
        $this->assertEquals($expected, $result);

        // call function specifying no blocks to be displayed
        $expected['launchcontainer'] = 3;
        $result = local_kaltura_format_typeconfig($param, false);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for different KAF service names.
     */
    public function invalid_logging_data() {
        $data = array(
                array(
                        'string'
                ),
                array(
                        1
                ),
                array(
                        new stdClass()
                )
        );
        return $data;
    }

    /**
     * Test logging.
     * @param array $data Sample data from data provider method.
     * @dataProvider module_name_test
     */
    public function test_local_kaltura_log_data_invalid_logging_data($data) {
        $this->resetAfterTest(true);

        $result = local_kaltura_log_data('mymedia', 'http://localhost', $data, true);
        $this->assertFalse($result);
    }

    /**
     * Test logging.
     */
    public function test_local_kaltura_log_data_invalid_module() {
        $this->resetAfterTest(true);

        $json = '{"courses":[{"courseId":123,"courseName":"something","roles":"ltirole1,ltirole2"},{"courseId":456,"courseName":"else","roles":"ltirole3,ltirole4"}]';
        $data = array();
        $data['test1'] = 'test2';
        $data['test3'] = 'test4';
        $data['json'] = $json;

        $result = local_kaltura_log_data('invalidmodule', 'http://localhost', $data, true);
        $this->assertFalse($result);
    }

    /**
     * Test logging.
     */
    public function test_local_kaltura_log_data_logging_request_data() {
        global $DB;

        $this->resetAfterTest(true);

        $json = '{"courses":[{"courseId":123,"courseName":"something","roles":"ltirole1,ltirole2"},{"courseId":456,"courseName":"else","roles":"ltirole3,ltirole4"}]';
        $data = array();
        $data['test1'] = 'test2';
        $data['test3'] = 'test4';
        $data['json'] = $json;

        $result = local_kaltura_log_data(KAF_MYMEDIA_MODULE, 'http://localhost', $data, true);
        $this->assertTrue($result);
        $record = $DB->get_record('local_kaltura_log', array('module'=> 'mymedia'));

        $this->assertObjectHasAttribute('id', $record);

        $this->assertObjectHasAttribute('module', $record);
        $this->assertEquals(KAF_MYMEDIA_MODULE, $record->module);

        $this->assertObjectHasAttribute('type', $record);
        $this->assertEquals(KALTURA_LOG_REQUEST, $record->type);

        $this->assertObjectHasAttribute('endpoint', $record);
        $this->assertEquals('http://localhost', $record->endpoint);

        $this->assertObjectHasAttribute('data', $record);
        $this->assertEquals(serialize($data), $record->data);

        $this->assertObjectHasAttribute('timecreated', $record);
        $this->assertNotEquals(0, $record);

        $result = local_kaltura_log_data(KAF_MEDIAGALLERY_MODULE, 'http://localhost', $data, true);
        $this->assertTrue($result);

        $record = $DB->get_record('local_kaltura_log', array('module'=> 'coursegallery'));

        $this->assertObjectHasAttribute('id', $record);

        $this->assertObjectHasAttribute('module', $record);
        $this->assertEquals(KAF_MEDIAGALLERY_MODULE, $record->module);

        $this->assertObjectHasAttribute('type', $record);
        $this->assertEquals(KALTURA_LOG_REQUEST, $record->type);

        $this->assertObjectHasAttribute('endpoint', $record);
        $this->assertEquals('http://localhost', $record->endpoint);

        $this->assertObjectHasAttribute('data', $record);
        $this->assertEquals(serialize($data), $record->data);

        $this->assertObjectHasAttribute('timecreated', $record);
        $this->assertNotEquals(0, $record);
    }

    /**
     * Test logging.
     */
    public function test_local_kaltura_log_data_logging_response_data() {
        global $DB;

        $this->resetAfterTest(true);

        $json = '{"courses":[{"courseId":123,"courseName":"something","roles":"ltirole1,ltirole2"},{"courseId":456,"courseName":"else","roles":"ltirole3,ltirole4"}]';
        $data = array();
        $data['test1'] = 'test2';
        $data['test3'] = 'test4';
        $data['json'] = $json;

        $result = local_kaltura_log_data('phpunit response', 'http://localhost', $data, false);
        $this->assertTrue($result);

        $record = $DB->get_record('local_kaltura_log', array('module'=> 'phpunit response'));

        $this->assertObjectHasAttribute('id', $record);

        $this->assertObjectHasAttribute('module', $record);
        $this->assertEquals('phpunit response', $record->module);

        $this->assertObjectHasAttribute('type', $record);
        $this->assertEquals(KALTURA_LOG_RESPONSE, $record->type);

        $this->assertObjectHasAttribute('endpoint', $record);
        $this->assertEquals('http://localhost', $record->endpoint);

        $this->assertObjectHasAttribute('data', $record);
        $this->assertEquals(serialize($data), $record->data);

        $this->assertObjectHasAttribute('timecreated', $record);
        $this->assertNotEquals(0, $record);
    }

    /**
     * Data provider for test_local_kaltura_format_uri().
     */
    public function uri_format_test() {
        return array(
                array('http://phpunit.tests/local_kaltura/tests'),
                array('http://phpunit.tests/local_kaltura/tests/'),
                array('https://phpunit.tests/local_kaltura/tests'),
                array('https://phpunit.tests/local_kaltura/tests/'),
                array('https://www.phpunit.tests/local_kaltura/tests/'),
        );
    }

    /**
     * Test local_kaltura_format_uri().
     * @param string $url differnt URI formats.
     * @dataProvider uri_format_test
     */
    public function test_local_kaltura_format_uri($uri) {
        $result = local_kaltura_format_uri($uri);
        $this->assertEquals('phpunit.tests/local_kaltura/tests', $result);
    }

    /**
     * Test local_kaltura_get_kaf_publishing_data().  This test creates 4 coures.  Enrolls the user as an editing teacher in coures 1 and 4,
     * then enrolls the user as a student in course 2.
     */
    public function test_local_kaltura_get_kaf_publishing_data_for_non_admin() {
        global $DB;

        $this->resetAfterTest(true);

        // Get the roles.
        $sql = "SELECT shortname,id
                  FROM {role}";
        $role = (array) $DB->get_records_sql($sql);
        // Create a test user.
        $user = $this->getDataGenerator()->create_user();

        // Create test courses and assign the user roles.
        $coursedata = array(
            'fullname' => 'Test 1',
            'shortname' => 'T1'
        );
        $courseone = $this->getDataGenerator()->create_course($coursedata);

        $this->getDataGenerator()->enrol_user($user->id, $courseone->id, $role['editingteacher']->id, 'manual');

        $coursedata = array(
            'fullname' => 'Test 2',
            'shortname' => 'T2'
        );
        $coursetwo = $this->getDataGenerator()->create_course($coursedata);

        $this->getDataGenerator()->enrol_user($user->id, $coursetwo->id, $role['student']->id, 'manual');

        $coursedata = array(
            'fullname' => 'Test 3',
            'shortname' => 'T3'
        );
        $coursethree = $this->getDataGenerator()->create_course($coursedata);

        $coursedata = array(
            'fullname' => 'Test 4',
            'shortname' => 'T4'
        );
        $coursefour = $this->getDataGenerator()->create_course($coursedata);

        $this->getDataGenerator()->enrol_user($user->id, $coursefour->id, $role['editingteacher']->id, 'manual');

        // Set the current user to the test user.
        advanced_testcase::setuser($user->id);

        $result = local_kaltura_get_kaf_publishing_data();

        $json = '{"courses":[{"courseId":"'.$courseone->id.'","courseName":"'.$courseone->fullname.'","roles":"Instructor"}';
        $json .= ',{"courseId":"'.$coursetwo->id.'","courseName":"'.$coursetwo->fullname.'","roles":"Learner"}';
        $json .= ',{"courseId":"'.$coursefour->id.'","courseName":"'.$coursefour->fullname.'","roles":"Instructor"}]}';

        $this->assertEquals(base64_encode($json), $result);
    }

    /**
     * Test local_kaltura_get_kaf_publishing_data().  This test creates 4 coures.  Enrolls the user as an editing teacher in coures 1 and 4,
     * then enrolls the user as a student in course 2.
     */
    public function test_local_kaltura_get_kaf_publishing_data_for_admin() {
        $this->resetAfterTest(true);

        // Create test courses and assign the user roles.
        $coursedata = array(
            'fullname' => 'Test 1',
            'shortname' => 'T1'
        );
        $courseone = $this->getDataGenerator()->create_course($coursedata);

        $coursedata = array(
            'fullname' => 'Test 2',
            'shortname' => 'T2'
        );
        $coursetwo = $this->getDataGenerator()->create_course($coursedata);

        $coursedata = array(
            'fullname' => 'Test 3',
            'shortname' => 'T3'
        );
        $coursethree = $this->getDataGenerator()->create_course($coursedata);

        $coursedata = array(
            'fullname' => 'Test 4',
            'shortname' => 'T4'
        );
        $coursefour = $this->getDataGenerator()->create_course($coursedata);

        advanced_testcase::setAdminUser();

        $result = local_kaltura_get_kaf_publishing_data();

        $json = '{"courses":[{"courseId":"'.$courseone->id.'","courseName":"'.$courseone->fullname.'","roles":"urn:lti:sysrole:ims\/lis\/Administrator"}';
        $json .= ',{"courseId":"'.$coursetwo->id.'","courseName":"'.$coursetwo->fullname.'","roles":"urn:lti:sysrole:ims\/lis\/Administrator"}';
        $json .= ',{"courseId":"'.$coursethree->id.'","courseName":"'.$coursethree->fullname.'","roles":"urn:lti:sysrole:ims\/lis\/Administrator"}';
        $json .= ',{"courseId":"'.$coursefour->id.'","courseName":"'.$coursefour->fullname.'","roles":"urn:lti:sysrole:ims\/lis\/Administrator"}]}';

        $this->assertEquals(base64_encode($json), $result);
    }

    /**
     * Data provider for test_local_kaltura_url_contains_configured_hostname_fail().
     */
    public function uri_hostname_tests_invalid() {
        return array(
                array('http://phpunit1.tests/local_kaltura/tests'),
                array('http://phpunit.2tests/local_kaltura/tests/'),
                array('http://tests.phpunit/local_kaltura/tests/'),
        );
    }

    /**
     * Test test_local_kaltura_url_contains_configured_hostname_fail().
     * @param string $url differnt URI formats.
     * @dataProvider uri_hostname_tests_invalid
     */
    public function test_local_kaltura_url_contains_configured_hostname_fail($url) {
        $this->resetAfterTest(true);

        set_config('kaf_uri', 'phpunit.tests', 'local_kaltura');

        $result = local_kaltura_url_contains_configured_hostname($url);
        $this->assertFalse($result);
    }

    /**
     * Data provider for test_local_kaltura_url_contains_configured_hostname().
     */
    public function uri_hostname_tests_valid() {
        return array(
                array('http://phpunit.tests/local_kaltura/'),
                array('https://phpunit.tests/local_kaltura/tests/'),
        );
    }

    /**
     * Test test_local_kaltura_url_contains_configured_hostname().
     * @param string $url differnt URI formats.
     * @dataProvider uri_hostname_tests_valid
     */
    public function test_local_kaltura_url_contains_configured_hostname($url) {
        $this->resetAfterTest(true);

        set_config('kaf_uri', 'phpunit.tests', 'local_kaltura');

        $result = local_kaltura_url_contains_configured_hostname($url);
        $this->assertTrue($result);
    }

    /**
     * Test local_kaltura_add_protocol_to_url().
     */
    public function test_local_kaltura_add_protocol_to_url() {
        $expected = 'http://example.com';
        $url = local_kaltura_add_protocol_to_url($expected);
        $this->assertEquals($expected, $url);

        $expected = 'https://example.com';
        $url = local_kaltura_add_protocol_to_url($expected);
        $this->assertEquals($expected, $url);

        $expected = 'http://example.com';
        $url = local_kaltura_add_protocol_to_url('example.com');
        $this->assertEquals($expected, $url);

        $url = local_kaltura_add_protocol_to_url('htdddtp://example.com');
        $this->assertEmpty($url);
    }

    /**
     * Test local_kaltura_add_kaf_uri_token().
     */
    public function test_local_kaltura_add_kaf_uri_token() {
        $this->resetAfterTest(true);

        // Set KAF URI to HTTP.
        $url = 'http://this-is-a-test-with-phpunit.com';
        set_config('kaf_uri', $url, 'local_kaltura');

        $path = '/phpunit/testing/test1/';
        $expected = $url.$path;

        // Test HTTP returns with the confgirued URL in HTTP.
        $actual = 'http://'.KALTURA_URI_TOKEN.$path;
        $result = local_kaltura_add_kaf_uri_token($actual);

        $this->assertEquals($expected, $result);

        // Test HTTPS returns with the configured URL in HTTP.
        $actual = 'https://'.KALTURA_URI_TOKEN.$path;
        $result = local_kaltura_add_kaf_uri_token($actual);

        $this->assertEquals($expected, $result);

        // Set KAF URI to HTTPS.
        $url = 'https://this-is-a-test-with-phpunit.com';
        set_config('kaf_uri', $url, 'local_kaltura');
        $expected = $url.$path;

        // Test HTTP returns with the confgirued URL in HTTPS.
        $actual = 'http://'.KALTURA_URI_TOKEN.$path;
        $result = local_kaltura_add_kaf_uri_token($actual);

        $this->assertEquals($expected, $result);

        // Test HTTPS returns with the confgirued URL in HTTPS.
        $actual = 'https://'.KALTURA_URI_TOKEN.$path;
        $result = local_kaltura_add_kaf_uri_token($actual);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test local_kaltura_encode_object_for_storage()
     */
    public function test_local_kaltura_encode_object_for_storage() {
        $data = array();
        $result = local_kaltura_encode_object_for_storage($data);
        $this->assertEquals('', $result);

        $data = new stdClass();
        $result = local_kaltura_encode_object_for_storage($data);
        $this->assertEquals('', $result);

        $data = 'hello';
        $result = local_kaltura_encode_object_for_storage($data);
        $this->assertEquals('', $result);

        $data = '';
        $result = local_kaltura_encode_object_for_storage($data);
        $this->assertEquals('', $result);

        $data = new stdClass();
        $data->one = 'abc';
        $data->two = 'def';
        $result = local_kaltura_encode_object_for_storage($data);
        $expected = base64_encode(serialize($data));
        $this->assertEquals($expected, $result);

        $data = array('one' => 'abc', 'two' => 'def');
        $result = local_kaltura_encode_object_for_storage($data);
        $expected = base64_encode(serialize($data));
        $this->assertEquals($expected, $result);
    }

    /**
     * Test local_kaltura_decode_object_for_storage()
     */
    public function test_local_kaltura_decode_object_for_storage() {
        $result = local_kaltura_decode_object_for_storage('');
        $this->assertEquals('', $result);

        $expected = new stdClass();
        $expected->one = 'abc';
        $expected->two = 'def';
        $data = base64_encode(serialize($expected));
        $result = local_kaltura_decode_object_for_storage($data);
        $this->assertEquals($expected, $result);

        $expected = array('one' => 'abc', 'two' => 'def');
        $data = base64_encode(serialize($expected));
        $result = local_kaltura_decode_object_for_storage($data);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test local_kaltura_convert_kaltura_base_entry_object()
     */
    public function test_local_kaltura_convert_kaltura_base_entry_object() {
        $result = local_kaltura_convert_kaltura_base_entry_object(new stdclass());
        $this->assertFalse($result);

        // Test converting a video entry.
        $time = time();
        $base = new KalturaMediaEntry();
        $base->dataUrl = 'http:/phpunittest.com';
        $base->width = 100;
        $base->height = 200;
        $base->id = 'phpunit';
        $base->name = 'phpunit title';
        $base->thumbnailUrl = 'http://phpunittest.com/thumb';
        $base->duration = 300;
        $base->description = 'phpunit description';
        $base->createdAt = $time;
        $base->creatorId = 'phpunit user';
        $base->tags = '';

        $expected = new stdClass();
        $expected->url = '';
        $expected->dataurl = 'http:/phpunittest.com';
        $expected->width = 100;
        $expected->height = 200;
        $expected->entryid = 'phpunit';
        $expected->title = 'phpunit title';
        $expected->thumbnailurl = 'http://phpunittest.com/thumb';
        $expected->duration = 300;
        $expected->description = 'phpunit description';
        $expected->createdat = $time;
        $expected->owner = 'phpunit user';
        $expected->tags = '';
        $expected->showtitle = 'on';
        $expected->showdescription = 'on';
        $expected->showowner = 'on';
        $expected->player = '';
        $expected->size = '';

        $result = local_kaltura_convert_kaltura_base_entry_object($base);
        $this->assertEquals($expected, $result);

        // Test converting a video presentation entry.
        $base = new KalturaDataEntry();
        $base->id = 'phpunit';
        $base->name = 'phpunit title';
        $base->description = 'phpunit description';
        $base->creatorId = 'phpunit creator';
        $base->tags = 'phpunit tags';
        $base->createdAt = $time;
        $base->thumbnailUrl = 'http://phpunittest.com/thumb';

        $expected = new stdClass();
        $expected->url = '';
        $expected->dataurl = '';
        $expected->width = 0;
        $expected->height = 0;
        $expected->entryid = 'phpunit';
        $expected->title = 'phpunit title';
        $expected->thumbnailurl = 'http://phpunittest.com/thumb';
        $expected->duration = 0;
        $expected->description = 'phpunit description';
        $expected->createdat = $time;
        $expected->owner = 'phpunit creator';
        $expected->tags = 'phpunit tags';
        $expected->showtitle = 'on';
        $expected->showdescription = 'on';
        $expected->showowner = 'on';
        $expected->player = '';
        $expected->size = '';

        $result = local_kaltura_convert_kaltura_base_entry_object($base);
        $this->assertEquals($expected, $result);
    }
}
