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

namespace core_webservice;

use core_external\external_api;
use core\tests\session\mock_handler;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/externallib.php');

/**
 * External course functions unit tests
 *
 * @package    core_webservice
 * @covers     \core_webservice_external::get_site_info
 * @category   external
 * @copyright  2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class externallib_test extends \core_external\tests\externallib_testcase {
    #[\Override]
    public function setUp(): void {
        // Calling parent is good, always
        parent::setUp();

        // We always need enabled WS for this testcase
        set_config('enablewebservices', '1');
    }

    /**
     * Test get_site_info.
     */
    public function test_get_site_info(): void {
        global $DB, $USER, $CFG, $PAGE;

        $this->resetAfterTest(true);

        $maxbytes = 10485760;
        $userquota = 5242880;
        set_config('maxbytes', $maxbytes);
        set_config('userquota', $userquota);

        // Set current user
        set_config('allowuserthemes', 1);
        $user = array();
        $user['username'] = 'johnd';
        $user['firstname'] = 'John';
        $user['lastname'] = 'Doe';
        $user['theme'] = 'boost';
        self::setUser(self::getDataGenerator()->create_user($user));

        // Add a web service and token.
        $webservice = new \stdClass();
        $webservice->name = 'Test web service';
        $webservice->enabled = true;
        $webservice->restrictedusers = false;
        $webservice->component = 'moodle';
        $webservice->timecreated = time();
        $webservice->downloadfiles = true;
        $webservice->uploadfiles = true;
        $externalserviceid = $DB->insert_record('external_services', $webservice);

        // Add a function to the service
        $DB->insert_record('external_services_functions', array('externalserviceid' => $externalserviceid,
            'functionname' => 'core_course_get_contents'));

        $_POST['wstoken'] = 'testtoken';
        $externaltoken = new \stdClass();
        $externaltoken->token = 'testtoken';
        $externaltoken->tokentype = 0;
        $externaltoken->userid = $USER->id;
        $externaltoken->externalserviceid = $externalserviceid;
        $externaltoken->contextid = 1;
        $externaltoken->creatorid = $USER->id;
        $externaltoken->timecreated = time();
        $externaltoken->name = \core_external\util::generate_token_name();
        $DB->insert_record('external_tokens', $externaltoken);

        // Add fake registration.
        $hub = new \stdClass();
        $hub->token = get_site_identifier() . date('Ymdhis');
        $hub->secret = $hub->token;
        $hub->huburl = HUB_MOODLEORGHUBURL;
        $hub->hubname = 'moodle';
        $hub->confirmed = 1;
        $hub->timemodified = time();
        $hub->id = $DB->insert_record('registration_hubs', $hub);

        $siteinfo = \core_webservice_external::get_site_info();

        // We need to execute the return values cleaning process to simulate the web service server.
        $siteinfo = external_api::clean_returnvalue(\core_webservice_external::get_site_info_returns(), $siteinfo);

        $this->assertEquals('johnd', $siteinfo['username']);
        $this->assertEquals('John', $siteinfo['firstname']);
        $this->assertEquals('Doe', $siteinfo['lastname']);
        $this->assertEquals(current_language(), $siteinfo['lang']);
        $this->assertEquals($USER->id, $siteinfo['userid']);
        $this->assertEquals(SITEID, $siteinfo['siteid']);
        $this->assertEquals(true, $siteinfo['downloadfiles']);
        $this->assertEquals($CFG->release, $siteinfo['release']);
        $this->assertEquals($CFG->version, $siteinfo['version']);
        $this->assertEquals('', $siteinfo['mobilecssurl']);
        $this->assertEquals(count($siteinfo['functions']), 1);
        $function = array_pop($siteinfo['functions']);
        $this->assertEquals($function['name'], 'core_course_get_contents');
        $this->assertEquals($function['version'], $siteinfo['version']);
        $this->assertEquals(1, $siteinfo['downloadfiles']);
        $this->assertEquals(1, $siteinfo['uploadfiles']);

        $this->assertCount(12, $siteinfo['advancedfeatures']);
        foreach ($siteinfo['advancedfeatures'] as $feature) {
            if ($feature['name'] == 'mnet_dispatcher_mode') {
                if ($CFG->mnet_dispatcher_mode == 'off') {
                    $this->assertEquals(0, $feature['value']);
                } else {
                    $this->assertEquals(1, $feature['value']);
                }
            } else if ($feature['name'] == 'enablecompetencies') {
                $expected = (!empty(get_config('core_competency', 'enabled'))) ? 1 : 0;
                $this->assertEquals($expected, $feature['value']);
            } else {
                $this->assertEquals($CFG->{$feature['name']}, $feature['value']);
            }
        }

        $this->assertEquals($userquota, $siteinfo['userquota']);
        // We can use the function for the expectation because USER_CAN_IGNORE_FILE_SIZE_LIMITS is
        // covered below for admin user. This test is for user not allowed to ignore limits.
        $this->assertEquals(get_max_upload_file_size($maxbytes), $siteinfo['usermaxuploadfilesize']);
        $this->assertEquals(true, $siteinfo['usercanmanageownfiles']);
        $userkey = get_user_key('core_files', $USER->id);
        $this->assertEquals($userkey, $siteinfo['userprivateaccesskey']);

        $this->assertEquals(HOMEPAGE_MY, $siteinfo['userhomepage']);
        $this->assertEquals($CFG->calendartype, $siteinfo['sitecalendartype']);
        if (!empty($USER->calendartype)) {
            $this->assertEquals($USER->calendartype, $siteinfo['usercalendartype']);
        } else {
            $this->assertEquals($CFG->calendartype, $siteinfo['usercalendartype']);
        }
        $this->assertFalse($siteinfo['userissiteadmin']);
        $this->assertEquals($CFG->calendartype, $siteinfo['sitecalendartype']);
        $this->assertEquals($user['theme'], $siteinfo['theme']);
        $this->assertEquals($USER->policyagreed, $siteinfo['policyagreed']);
        $this->assertFalse($siteinfo['usercanchangeconfig']);
        $this->assertFalse($siteinfo['usercanviewconfig']);
        $this->assertArrayNotHasKey('sitesecret', $siteinfo);

        // Now as admin.
        $this->setAdminUser();

        // Set a fake token for the user admin.
        $_POST['wstoken'] = 'testtoken';
        $externaltoken = new \stdClass();
        $externaltoken->token = 'testtoken';
        $externaltoken->tokentype = 0;
        $externaltoken->userid = $USER->id;
        $externaltoken->externalserviceid = $externalserviceid;
        $externaltoken->contextid = 1;
        $externaltoken->creatorid = $USER->id;
        $externaltoken->timecreated = time();
        $externaltoken->name = \core_external\util::generate_token_name();
        $DB->insert_record('external_tokens', $externaltoken);

        // Set a home page by user preferences.
        $CFG->defaulthomepage = HOMEPAGE_USER;
        set_user_preference('user_home_page_preference', HOMEPAGE_SITE);

        $siteinfo = \core_webservice_external::get_site_info();

        // We need to execute the return values cleaning process to simulate the web service server.
        $siteinfo = external_api::clean_returnvalue(\core_webservice_external::get_site_info_returns(), $siteinfo);

        $this->assertEquals(0, $siteinfo['userquota']);
        $this->assertEquals(USER_CAN_IGNORE_FILE_SIZE_LIMITS, $siteinfo['usermaxuploadfilesize']);
        $this->assertEquals(true, $siteinfo['usercanmanageownfiles']);
        $this->assertTrue($siteinfo['userissiteadmin']);
        $this->assertEmpty($USER->theme);
        $this->assertEquals($PAGE->theme->name, $siteinfo['theme']);
        $this->assertEquals($CFG->limitconcurrentlogins, $siteinfo['limitconcurrentlogins']);
        $this->assertFalse(isset($siteinfo['usersessionscount']));

        $CFG->limitconcurrentlogins = 1;
        $record = new \stdClass();
        $record->state        = 0;
        $record->sessdata     = null;
        $record->userid       = $USER->id;
        $record->timemodified = time();
        $record->firstip      = $record->lastip = '10.0.0.1';
        $record->sid = md5('hokus1');
        $record->timecreated = time();

        $mockhandler = new mock_handler();
        $mockhandler->add_test_session($record);

        $siteinfo = \core_webservice_external::get_site_info();
        $siteinfo = external_api::clean_returnvalue(\core_webservice_external::get_site_info_returns(), $siteinfo);
        $this->assertEquals($CFG->limitconcurrentlogins, $siteinfo['limitconcurrentlogins']);
        $this->assertEquals(1, $siteinfo['usersessionscount']);
        $this->assertTrue($siteinfo['usercanchangeconfig']);
        $this->assertTrue($siteinfo['usercanviewconfig']);
        $this->assertArrayHasKey('sitesecret', $siteinfo);
        $this->assertNotEmpty($siteinfo['sitesecret']);
        $this->assertEquals($hub->secret, $siteinfo['sitesecret']);
    }

    /**
     * Test get_site_info with values > PHP_INT_MAX. We check only userquota since maxbytes require PHP ini changes.
     */
    public function test_get_site_info_max_int(): void {
        $this->resetAfterTest(true);

        self::setUser(self::getDataGenerator()->create_user());

        // Check values higher than PHP_INT_MAX. This value may come from settings (as string).
        $userquota = PHP_INT_MAX . '000';
        set_config('userquota', $userquota);

        $result = \core_webservice_external::get_site_info();
        $result = external_api::clean_returnvalue(\core_webservice_external::get_site_info_returns(), $result);
        $this->assertEquals(PHP_INT_MAX, $result['userquota']);
    }

    /**
     * Test get_site_info with missing components.
     */
    public function test_get_site_missing_components(): void {
        global $USER, $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Add a web service and token.
        $webservice = new \stdClass();
        $webservice->name = 'Test web service';
        $webservice->enabled = true;
        $webservice->restrictedusers = false;
        $webservice->component = 'moodle';
        $webservice->timecreated = time();
        $webservice->downloadfiles = true;
        $webservice->uploadfiles = true;
        $externalserviceid = $DB->insert_record('external_services', $webservice);

        // Add a function to the service (missing plugin).
        $DB->insert_record('external_functions',
            [
                'component' => 'mod_random',
                'name' => 'mod_random_get_info'
            ]
        );

        // Insert one from missing component.
        $DB->insert_record('external_services_functions',
            [
                'externalserviceid' => $externalserviceid,
                'functionname' => 'mod_random_get_info'
            ]
        );
        // Insert a core one.
        $DB->insert_record('external_services_functions',
            [
                'externalserviceid' => $externalserviceid,
                'functionname' => 'core_user_get_users'
            ]
        );

        $_POST['wstoken'] = 'testtoken';
        $externaltoken = new \stdClass();
        $externaltoken->token = 'testtoken';
        $externaltoken->tokentype = 0;
        $externaltoken->userid = $USER->id;
        $externaltoken->externalserviceid = $externalserviceid;
        $externaltoken->contextid = 1;
        $externaltoken->creatorid = $USER->id;
        $externaltoken->timecreated = time();
        $externaltoken->name = \core_external\util::generate_token_name();
        $DB->insert_record('external_tokens', $externaltoken);

        // Execution should complete.
        $result = \core_webservice_external::get_site_info();
        $result = external_api::clean_returnvalue(\core_webservice_external::get_site_info_returns(), $result);
        // Check we ignore the missing component function.
        $this->assertCount(1, $result['functions']);
        $this->assertEquals('core_user_get_users', $result['functions'][0]['name']);
    }


    /**
     * Test get_site_info returns the default home page URL when needed.
     */
    public function test_get_site_info_default_home_page(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Site configuration.
        $CFG->defaulthomepage = HOMEPAGE_MY;

        $result = \core_webservice_external::get_site_info();
        $result = external_api::clean_returnvalue(\core_webservice_external::get_site_info_returns(), $result);
        $this->assertEquals(HOMEPAGE_MY, $result['userhomepage']);
        $this->assertArrayNotHasKey('userhomepageurl', $result);

        $CFG->defaulthomepage = "/home";

        $result = \core_webservice_external::get_site_info();
        $result = external_api::clean_returnvalue(\core_webservice_external::get_site_info_returns(), $result);
        $this->assertEquals(HOMEPAGE_URL, $result['userhomepage']);
        $this->assertEquals("{$CFG->wwwroot}/home", $result['userhomepageurl']);

        // User preference.
        $CFG->defaulthomepage = HOMEPAGE_USER;

        $userpreference = "/about";
        set_user_preference('user_home_page_preference', $userpreference);

        $result = \core_webservice_external::get_site_info();
        $result = external_api::clean_returnvalue(\core_webservice_external::get_site_info_returns(), $result);
        $this->assertEquals(HOMEPAGE_URL, $result['userhomepage']);
        $this->assertEquals("{$CFG->wwwroot}/about", $result['userhomepageurl']);
    }

}
