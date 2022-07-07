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
 * Unit tests for the webservice component.
 *
 * @package    core_webservice
 * @category   test
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/lib.php');

/**
 * Unit tests for the webservice component.
 *
 * @package    core_webservice
 * @category   test
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_test extends advanced_testcase {

    /**
     * Setup.
     */
    public function setUp(): void {
        // Calling parent is good, always.
        parent::setUp();

        // We always need enabled WS for this testcase.
        set_config('enablewebservices', '1');
    }

    /**
     * Test init_service_class().
     */
    public function test_init_service_class() {
        global $DB, $USER;

        $this->resetAfterTest(true);

        // Set current user.
        $this->setAdminUser();

        // Add a web service.
        $webservice = new stdClass();
        $webservice->name = 'Test web service';
        $webservice->enabled = true;
        $webservice->restrictedusers = false;
        $webservice->component = 'moodle';
        $webservice->timecreated = time();
        $webservice->downloadfiles = true;
        $webservice->uploadfiles = true;
        $externalserviceid = $DB->insert_record('external_services', $webservice);

        // Add token.
        $externaltoken = new stdClass();
        $externaltoken->token = 'testtoken';
        $externaltoken->tokentype = 0;
        $externaltoken->userid = $USER->id;
        $externaltoken->externalserviceid = $externalserviceid;
        $externaltoken->contextid = 1;
        $externaltoken->creatorid = $USER->id;
        $externaltoken->timecreated = time();
        $DB->insert_record('external_tokens', $externaltoken);

        // Add a function to the service.
        $wsmethod = new stdClass();
        $wsmethod->externalserviceid = $externalserviceid;
        $wsmethod->functionname = 'core_course_get_contents';
        $DB->insert_record('external_services_functions', $wsmethod);

        // Initialise the dummy web service.
        $dummy = new webservice_dummy(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN);
        // Set the token.
        $dummy->set_token($externaltoken->token);
        // Run the web service.
        $dummy->run();
        // Get service methods and structs.
        $servicemethods = $dummy->get_service_methods();
        $servicestructs = $dummy->get_service_structs();
        $this->assertNotEmpty($servicemethods);
        // The function core_course_get_contents should be only the only web service function in the moment.
        $this->assertEquals(1, count($servicemethods));
        // The function core_course_get_contents doesn't have a struct class, so the list of service structs should be empty.
        $this->assertEmpty($servicestructs);

        // Add other functions to the service.
        // The function core_comment_get_comments has one struct class in its output.
        $wsmethod->functionname = 'core_comment_get_comments';
        $DB->insert_record('external_services_functions', $wsmethod);
        // The function core_grades_update_grades has one struct class in its input.
        $wsmethod->functionname = 'core_grades_update_grades';
        $DB->insert_record('external_services_functions', $wsmethod);

        // Run the web service again.
        $dummy->run();
        // Get service methods and structs.
        $servicemethods = $dummy->get_service_methods();
        $servicestructs = $dummy->get_service_structs();
        $this->assertEquals(3, count($servicemethods));
        $this->assertEquals(2, count($servicestructs));

        // Check the contents of service methods.
        foreach ($servicemethods as $method) {
            // Get the external function info.
            $function = external_api::external_function_info($method->name);

            // Check input params.
            foreach ($function->parameters_desc->keys as $name => $keydesc) {
                $this->check_params($method->inputparams[$name]['type'], $keydesc, $servicestructs);
            }

            // Check output params.
            $this->check_params($method->outputparams['return']['type'], $function->returns_desc, $servicestructs);

            // Check description.
            $this->assertEquals($function->description, $method->description);
        }
    }

    /**
     * Tests update_token_lastaccess() function.
     *
     * @throws dml_exception
     */
    public function test_update_token_lastaccess() {
        global $DB;

        $this->resetAfterTest(true);

        // Set current user.
        $this->setAdminUser();

        // Add a web service.
        $webservice = new stdClass();
        $webservice->name = 'Test web service';
        $webservice->enabled = true;
        $webservice->restrictedusers = false;
        $webservice->component = 'moodle';
        $webservice->timecreated = time();
        $webservice->downloadfiles = true;
        $webservice->uploadfiles = true;
        $DB->insert_record('external_services', $webservice);

        // Add token.
        $tokenstr = external_create_service_token($webservice->name, context_system::instance()->id);
        $token = $DB->get_record('external_tokens', ['token' => $tokenstr]);

        // Trigger last access once (at current time).
        webservice::update_token_lastaccess($token);

        // Check last access.
        $token = $DB->get_record('external_tokens', ['token' => $tokenstr]);
        $this->assertLessThan(5, abs(time() - $token->lastaccess));

        // Try setting it to +1 second. This should not update yet.
        $before = (int)$token->lastaccess;
        webservice::update_token_lastaccess($token, $before + 1);
        $token = $DB->get_record('external_tokens', ['token' => $tokenstr]);
        $this->assertEquals($before, $token->lastaccess);

        // To -1000 seconds. This should not update.
        webservice::update_token_lastaccess($token, $before - 1000);
        $token = $DB->get_record('external_tokens', ['token' => $tokenstr]);
        $this->assertEquals($before, $token->lastaccess);

        // To +59 seconds. This should also not quite update.
        webservice::update_token_lastaccess($token, $before + 59);
        $token = $DB->get_record('external_tokens', ['token' => $tokenstr]);
        $this->assertEquals($before, $token->lastaccess);

        // Finally to +60 seconds, where it should update.
        webservice::update_token_lastaccess($token, $before + 60);
        $token = $DB->get_record('external_tokens', ['token' => $tokenstr]);
        $this->assertEquals($before + 60, $token->lastaccess);
    }

    /**
     * Utility method that tests the parameter type of a method info's input/output parameter.
     *
     * @param string $type The parameter type that is being evaluated.
     * @param mixed $methoddesc The method description of the WS function.
     * @param array $servicestructs The list of generated service struct classes.
     */
    private function check_params($type, $methoddesc, $servicestructs) {
        if ($methoddesc instanceof external_value) {
            // Test for simple types.
            if (in_array($methoddesc->type, [PARAM_INT, PARAM_FLOAT, PARAM_BOOL])) {
                $this->assertEquals($methoddesc->type, $type);
            } else {
                $this->assertEquals('string', $type);
            }
        } else if ($methoddesc instanceof external_single_structure) {
            // Test that the class name of the struct class is in the array of service structs.
            $structinfo = $this->get_struct_info($servicestructs, $type);
            $this->assertNotNull($structinfo);
            // Test that the properties of the struct info exist in the method description.
            foreach ($structinfo->properties as $propname => $proptype) {
                $this->assertTrue($this->in_keydesc($methoddesc, $propname));
            }
        } else if ($methoddesc instanceof external_multiple_structure) {
            // Test for array types.
            $this->assertEquals('array', $type);
        }
    }

    /**
     * Gets the struct information from the list of struct classes based on the given struct class name.
     *
     * @param array $structarray The list of generated struct classes.
     * @param string $structclass The name of the struct class.
     * @return object|null The struct class info, or null if it's not found.
     */
    private function get_struct_info($structarray, $structclass) {
        foreach ($structarray as $struct) {
            if ($struct->classname === $structclass) {
                return $struct;
            }
        }
        return null;
    }

    /**
     * Searches the keys of the given external_single_structure object if it contains a certain property name.
     *
     * @param external_single_structure $keydesc
     * @param string $propertyname The property name to be searched for.
     * @return bool True if the property name is found in $keydesc. False, otherwise.
     */
    private function in_keydesc(external_single_structure $keydesc, $propertyname) {
        foreach ($keydesc->keys as $key => $desc) {
            if ($key === $propertyname) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Class webservice_dummy.
 *
 * Dummy webservice class for testing the webservice_base_server class and enable us to expose variables we want to test.
 *
 * @package    core_webservice
 * @category   test
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_dummy extends webservice_base_server {

    /**
     * webservice_dummy constructor.
     *
     * @param int $authmethod The authentication method.
     */
    public function __construct($authmethod) {
        parent::__construct($authmethod);

        // Arbitrarily naming this as REST in order not to have to register another WS protocol and set capabilities.
        $this->wsname = 'rest';
    }

    /**
     * Token setter method.
     *
     * @param string $token The web service token.
     */
    public function set_token($token) {
        $this->token = $token;
    }

    /**
     * This method parses the request input, it needs to get:
     *  1/ user authentication - username+password or token
     *  2/ function name
     *  3/ function parameters
     */
    protected function parse_request() {
        // Just a method stub. No need to implement at the moment since it's not really being used for this test case for now.
    }

    /**
     * Send the result of function call to the WS client.
     */
    protected function send_response() {
        // Just a method stub. No need to implement at the moment since it's not really being used for this test case for now.
    }

    /**
     * Send the error information to the WS client.
     *
     * @param exception $ex
     */
    protected function send_error($ex = null) {
        // Just a method stub. No need to implement at the moment since it's not really being used for this test case for now.
    }

    /**
     * run() method implementation.
     */
    public function run() {
        $this->authenticate_user();
        $this->init_service_class();
    }

    /**
     * Getter method of servicemethods array.
     *
     * @return array
     */
    public function get_service_methods() {
        return $this->servicemethods;
    }

    /**
     * Getter method of servicestructs array.
     *
     * @return array
     */
    public function get_service_structs() {
        return $this->servicestructs;
    }
}
