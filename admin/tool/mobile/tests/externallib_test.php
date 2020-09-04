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
 * Moodle Mobile admin tool external functions tests.
 *
 * @package    tool_mobile
 * @category   external
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/admin/tool/mobile/tests/fixtures/output/mobile.php');
require_once($CFG->dirroot . '/webservice/lib.php');

use tool_mobile\external;
use tool_mobile\api;

/**
 * Moodle Mobile admin tool external functions tests.
 *
 * @package     tool_mobile
 * @copyright   2016 Juan Leyva
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.1
 */
class tool_mobile_external_testcase extends externallib_advanced_testcase {

    /**
     * Test get_plugins_supporting_mobile.
     * This is a very basic test because currently there aren't plugins supporting Mobile in core.
     */
    public function test_get_plugins_supporting_mobile() {
        $result = external::get_plugins_supporting_mobile();
        $result = external_api::clean_returnvalue(external::get_plugins_supporting_mobile_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertArrayHasKey('plugins', $result);
        $this->assertTrue(is_array($result['plugins']));
    }

    public function test_get_public_config() {
        global $CFG, $SITE, $OUTPUT;

        $this->resetAfterTest(true);
        $result = external::get_public_config();
        $result = external_api::clean_returnvalue(external::get_public_config_returns(), $result);

        // Test default values.
        $context = context_system::instance();
        list($authinstructions, $notusedformat) = external_format_text($CFG->auth_instructions, FORMAT_MOODLE, $context->id);
        list($maintenancemessage, $notusedformat) = external_format_text($CFG->maintenance_message, FORMAT_MOODLE, $context->id);

        $expected = array(
            'wwwroot' => $CFG->wwwroot,
            'httpswwwroot' => $CFG->wwwroot,
            'sitename' => external_format_string($SITE->fullname, $context->id, true),
            'guestlogin' => $CFG->guestloginbutton,
            'rememberusername' => $CFG->rememberusername,
            'authloginviaemail' => $CFG->authloginviaemail,
            'registerauth' => $CFG->registerauth,
            'forgottenpasswordurl' => $CFG->forgottenpasswordurl,
            'authinstructions' => $authinstructions,
            'authnoneenabled' => (int) is_enabled_auth('none'),
            'enablewebservices' => $CFG->enablewebservices,
            'enablemobilewebservice' => $CFG->enablemobilewebservice,
            'maintenanceenabled' => $CFG->maintenance_enabled,
            'maintenancemessage' => $maintenancemessage,
            'typeoflogin' => api::LOGIN_VIA_APP,
            'mobilecssurl' => '',
            'tool_mobile_disabledfeatures' => '',
            'launchurl' => "$CFG->wwwroot/$CFG->admin/tool/mobile/launch.php",
            'country' => $CFG->country,
            'agedigitalconsentverification' => \core_auth\digital_consent::is_age_digital_consent_verification_enabled(),
            'autolang' => $CFG->autolang,
            'lang' => $CFG->lang,
            'langmenu' => $CFG->langmenu,
            'langlist' => $CFG->langlist,
            'locale' => $CFG->locale,
            'tool_mobile_minimumversion' => '',
            'tool_mobile_iosappid' => get_config('tool_mobile', 'iosappid'),
            'tool_mobile_androidappid' => get_config('tool_mobile', 'androidappid'),
            'tool_mobile_setuplink' => get_config('tool_mobile', 'setuplink'),
            'warnings' => array()
        );
        $this->assertEquals($expected, $result);

        $this->setAdminUser();
        // Change some values.
        set_config('registerauth', 'email');
        $authinstructions = 'Something with <b>html tags</b>';
        set_config('auth_instructions', $authinstructions);
        set_config('typeoflogin', api::LOGIN_VIA_BROWSER, 'tool_mobile');
        set_config('logo', 'mock.png', 'core_admin');
        set_config('logocompact', 'mock.png', 'core_admin');
        set_config('forgottenpasswordurl', 'mailto:fake@email.zy'); // Test old hack.
        set_config('agedigitalconsentverification', 1);
        set_config('autolang', 1);
        set_config('lang', 'a_b');  // Set invalid lang.
        set_config('disabledfeatures', 'myoverview', 'tool_mobile');
        set_config('minimumversion', '3.8.0', 'tool_mobile');

        // Enable couple of issuers.
        $issuer = \core\oauth2\api::create_standard_issuer('google');
        $irecord = $issuer->to_record();
        $irecord->clientid = 'mock';
        $irecord->clientsecret = 'mock';
        core\oauth2\api::update_issuer($irecord);

        set_config('hostname', 'localhost', 'auth_cas');
        set_config('auth_logo', 'http://invalidurl.com//invalid/', 'auth_cas');
        set_config('auth_name', 'CAS', 'auth_cas');
        set_config('auth', 'oauth2,cas');

        list($authinstructions, $notusedformat) = external_format_text($authinstructions, FORMAT_MOODLE, $context->id);
        $expected['registerauth'] = 'email';
        $expected['authinstructions'] = $authinstructions;
        $expected['typeoflogin'] = api::LOGIN_VIA_BROWSER;
        $expected['forgottenpasswordurl'] = ''; // Expect empty when it's not an URL.
        $expected['agedigitalconsentverification'] = true;
        $expected['supportname'] = $CFG->supportname;
        $expected['supportemail'] = $CFG->supportemail;
        $expected['autolang'] = '1';
        $expected['lang'] = ''; // Expect empty because it was set to an invalid lang.
        $expected['tool_mobile_disabledfeatures'] = 'myoverview';
        $expected['tool_mobile_minimumversion'] = '3.8.0';

        if ($logourl = $OUTPUT->get_logo_url()) {
            $expected['logourl'] = $logourl->out(false);
        }
        if ($compactlogourl = $OUTPUT->get_compact_logo_url()) {
            $expected['compactlogourl'] = $compactlogourl->out(false);
        }

        $result = external::get_public_config();
        $result = external_api::clean_returnvalue(external::get_public_config_returns(), $result);
        // First check providers.
        $identityproviders = $result['identityproviders'];
        unset($result['identityproviders']);

        $this->assertEquals('Google', $identityproviders[0]['name']);
        $this->assertEquals($irecord->image, $identityproviders[0]['iconurl']);
        $this->assertContains($CFG->wwwroot, $identityproviders[0]['url']);

        $this->assertEquals('CAS', $identityproviders[1]['name']);
        $this->assertEmpty($identityproviders[1]['iconurl']);
        $this->assertContains($CFG->wwwroot, $identityproviders[1]['url']);

        $this->assertEquals($expected, $result);

        // Change providers img.
        $newurl = 'validimage.png';
        set_config('auth_logo', $newurl, 'auth_cas');
        $result = external::get_public_config();
        $result = external_api::clean_returnvalue(external::get_public_config_returns(), $result);
        $this->assertContains($newurl, $result['identityproviders'][1]['iconurl']);
    }

    /**
     * Test get_config
     */
    public function test_get_config() {
        global $CFG, $SITE;
        require_once($CFG->dirroot . '/course/format/lib.php');

        $this->resetAfterTest(true);

        $mysitepolicy = 'http://mysite.is/policy/';
        set_config('sitepolicy', $mysitepolicy);

        $result = external::get_config();
        $result = external_api::clean_returnvalue(external::get_config_returns(), $result);

        // SITE summary is null in phpunit which gets transformed to an empty string by format_text.
        list($sitesummary, $unused) = external_format_text($SITE->summary, $SITE->summaryformat, context_system::instance()->id);

        // Test default values.
        $context = context_system::instance();
        $expected = array(
            array('name' => 'fullname', 'value' => $SITE->fullname),
            array('name' => 'shortname', 'value' => $SITE->shortname),
            array('name' => 'summary', 'value' => $sitesummary),
            array('name' => 'summaryformat', 'value' => FORMAT_HTML),
            array('name' => 'frontpage', 'value' => $CFG->frontpage),
            array('name' => 'frontpageloggedin', 'value' => $CFG->frontpageloggedin),
            array('name' => 'maxcategorydepth', 'value' => $CFG->maxcategorydepth),
            array('name' => 'frontpagecourselimit', 'value' => $CFG->frontpagecourselimit),
            array('name' => 'numsections', 'value' => course_get_format($SITE)->get_last_section_number()),
            array('name' => 'newsitems', 'value' => $SITE->newsitems),
            array('name' => 'commentsperpage', 'value' => $CFG->commentsperpage),
            array('name' => 'sitepolicy', 'value' => $mysitepolicy),
            array('name' => 'sitepolicyhandler', 'value' => ''),
            array('name' => 'disableuserimages', 'value' => $CFG->disableuserimages),
            array('name' => 'mygradesurl', 'value' => user_mygrades_url()->out(false)),
            array('name' => 'tool_mobile_forcelogout', 'value' => 0),
            array('name' => 'tool_mobile_customlangstrings', 'value' => ''),
            array('name' => 'tool_mobile_disabledfeatures', 'value' => ''),
            array('name' => 'tool_mobile_filetypeexclusionlist', 'value' => ''),
            array('name' => 'tool_mobile_custommenuitems', 'value' => ''),
            array('name' => 'tool_mobile_apppolicy', 'value' => ''),
            array('name' => 'calendartype', 'value' => $CFG->calendartype),
            array('name' => 'calendar_site_timeformat', 'value' => $CFG->calendar_site_timeformat),
            array('name' => 'calendar_startwday', 'value' => $CFG->calendar_startwday),
            array('name' => 'calendar_adminseesall', 'value' => $CFG->calendar_adminseesall),
            array('name' => 'calendar_lookahead', 'value' => $CFG->calendar_lookahead),
            array('name' => 'calendar_maxevents', 'value' => $CFG->calendar_maxevents),
        );
        $colornumbers = range(1, 10);
        foreach ($colornumbers as $number) {
            $expected[] = [
                'name' => 'core_admin_coursecolor' . $number,
                'value' => get_config('core_admin', 'coursecolor' . $number)
            ];
        }
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals($expected, $result['settings']);

        // Change a value and retrieve filtering by section.
        set_config('commentsperpage', 1);
        $expected[10]['value'] = 1;
        // Remove not expected elements.
        array_splice($expected, 11);

        $result = external::get_config('frontpagesettings');
        $result = external_api::clean_returnvalue(external::get_config_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals($expected, $result['settings']);
    }

    /*
     * Test get_autologin_key.
     */
    public function test_get_autologin_key() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE));

        $token = external_generate_token_for_current_user($service);

        // Check we got the private token.
        $this->assertTrue(isset($token->privatetoken));

        // Enable requeriments.
        $_GET['wstoken'] = $token->token;   // Mock parameters.

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
                'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        // Even if we force the password change for the current user we should be able to retrieve the key.
        set_user_preference('auth_forcepasswordchange', 1, $user->id);

        $this->setCurrentTimeStart();
        $result = external::get_autologin_key($token->privatetoken);
        $result = external_api::clean_returnvalue(external::get_autologin_key_returns(), $result);
        // Validate the key.
        $this->assertEquals(32, core_text::strlen($result['key']));
        $key = $DB->get_record('user_private_key', array('value' => $result['key']));
        $this->assertEquals($USER->id, $key->userid);
        $this->assertTimeCurrent($key->validuntil - api::LOGIN_KEY_TTL);

        // Now, try with an invalid private token.
        set_user_preference('tool_mobile_autologin_request_last', time() - HOURSECS, $USER);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('invalidprivatetoken', 'tool_mobile'));
        $result = external::get_autologin_key(random_string('64'));
    }

    /**
     * Test get_autologin_key missing ws.
     */
    public function test_get_autologin_key_missing_ws() {
        global $CFG;
        $this->resetAfterTest(true);

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        // Need to disable webservices to verify that's checked.
        $CFG->enablewebservices = 0;
        $CFG->enablemobilewebservice = 0;

        $this->setAdminUser();
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('enablewsdescription', 'webservice'));
        $result = external::get_autologin_key('');
    }

    /**
     * Test get_autologin_key missing https.
     */
    public function test_get_autologin_key_missing_https() {
        global $CFG;

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        // Need to simulate a non HTTPS site here.
        $CFG->wwwroot = str_replace('https:', 'http:', $CFG->wwwroot);

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('httpsrequired', 'tool_mobile'));
        $result = external::get_autologin_key('');
    }

    /**
     * Test get_autologin_key missing admin.
     */
    public function test_get_autologin_key_missing_admin() {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('autologinnotallowedtoadmins', 'tool_mobile'));
        $result = external::get_autologin_key('');
    }

    /**
     * Test get_autologin_key locked.
     */
    public function test_get_autologin_key_missing_locked() {
        global $CFG, $DB, $USER;

        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE));

        $token = external_generate_token_for_current_user($service);
        $_GET['wstoken'] = $token->token;   // Mock parameters.

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        $result = external::get_autologin_key($token->privatetoken);
        $result = external_api::clean_returnvalue(external::get_autologin_key_returns(), $result);

        // Mock last time request.
        $mocktime = time() - 7 * MINSECS;
        set_user_preference('tool_mobile_autologin_request_last', $mocktime, $USER);
        $result = external::get_autologin_key($token->privatetoken);
        $result = external_api::clean_returnvalue(external::get_autologin_key_returns(), $result);

        // We just requested one token, we must wait.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('autologinkeygenerationlockout', 'tool_mobile'));
        $result = external::get_autologin_key($token->privatetoken);
    }

    /**
     * Test get_autologin_key missing app_request.
     */
    public function test_get_autologin_key_missing_app_request() {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('apprequired', 'tool_mobile'));
        $result = external::get_autologin_key('');
    }

    /**
     * Test get_content.
     */
    public function test_get_content() {

        $paramval = 16;
        $result = external::get_content('tool_mobile', 'test_view', array(array('name' => 'param1', 'value' => $paramval)));
        $result = external_api::clean_returnvalue(external::get_content_returns(), $result);
        $this->assertCount(1, $result['templates']);
        $this->assertCount(1, $result['otherdata']);
        $this->assertCount(2, $result['restrict']['users']);
        $this->assertCount(2, $result['restrict']['courses']);
        $this->assertEquals('alert();', $result['javascript']);
        $this->assertEquals('main', $result['templates'][0]['id']);
        $this->assertEquals('The HTML code', $result['templates'][0]['html']);
        $this->assertEquals('otherdata1', $result['otherdata'][0]['name']);
        $this->assertEquals($paramval, $result['otherdata'][0]['value']);
        $this->assertEquals(array(1, 2), $result['restrict']['users']);
        $this->assertEquals(array(3, 4), $result['restrict']['courses']);
        $this->assertEmpty($result['files']);
        $this->assertFalse($result['disabled']);
    }

    /**
     * Test get_content disabled.
     */
    public function test_get_content_disabled() {

        $paramval = 16;
        $result = external::get_content('tool_mobile', 'test_view_disabled',
            array(array('name' => 'param1', 'value' => $paramval)));
        $result = external_api::clean_returnvalue(external::get_content_returns(), $result);
        $this->assertTrue($result['disabled']);
    }

    /**
     * Test get_content non existent function in valid component.
     */
    public function test_get_content_non_existent_function() {

        $this->expectException('coding_exception');
        $result = external::get_content('tool_mobile', 'test_blahblah');
    }

    /**
     * Test get_content incorrect component.
     */
    public function test_get_content_invalid_component() {

        $this->expectException('moodle_exception');
        $result = external::get_content('tool_mobile\hack', 'test_view');
    }

    /**
     * Test get_content non existent component.
     */
    public function test_get_content_non_existent_component() {

        $this->expectException('moodle_exception');
        $result = external::get_content('tool_blahblahblah', 'test_view');
    }

    public function test_call_external_functions() {
        global $SESSION;

        $this->resetAfterTest(true);

        $category = self::getDataGenerator()->create_category(array('name' => 'Category 1'));
        $course = self::getDataGenerator()->create_course([
            'category' => $category->id,
            'shortname' => 'c1',
            'summary' => '<span lang="en" class="multilang">Course summary</span>'
                . '<span lang="eo" class="multilang">Kurso resumo</span>'
                . '@@PLUGINFILE@@/filename.txt'
                . '<!-- Comment stripped when formatting text -->',
            'summaryformat' => FORMAT_MOODLE
        ]);
        $user1 = self::getDataGenerator()->create_user(['username' => 'user1', 'lastaccess' => time()]);
        $user2 = self::getDataGenerator()->create_user(['username' => 'user2', 'lastaccess' => time()]);

        self::setUser($user1);

        // Setup WS token.
        $webservicemanager = new \webservice;
        $service = $webservicemanager->get_external_service_by_shortname(MOODLE_OFFICIAL_MOBILE_SERVICE);
        $token = external_generate_token_for_current_user($service);
        $_POST['wstoken'] = $token->token;

        // Workaround for external_api::call_external_function requiring sesskey.
        $_POST['sesskey'] = sesskey();

        // Call some functions.

        $requests = [
            [
                'function' => 'core_course_get_courses_by_field',
                'arguments' => json_encode(['field' => 'id', 'value' => $course->id])
            ],
            [
                'function' => 'core_user_get_users_by_field',
                'arguments' => json_encode(['field' => 'id', 'values' => [$user1->id]])
            ],
            [
                'function' => 'core_user_get_user_preferences',
                'arguments' => json_encode(['name' => 'some_setting', 'userid' => $user2->id])
            ],
            [
                'function' => 'core_course_get_courses_by_field',
                'arguments' => json_encode(['field' => 'shortname', 'value' => $course->shortname])
            ],
        ];
        $result = external::call_external_functions($requests);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(external::call_external_functions_returns(), $result);

        // Only 3 responses, the 4th request is not executed because the 3rd throws an exception.
        $this->assertCount(3, $result['responses']);

        $this->assertFalse($result['responses'][0]['error']);
        $coursedata = external_api::clean_returnvalue(
            core_course_external::get_courses_by_field_returns(),
            core_course_external::get_courses_by_field('id', $course->id));
         $this->assertEquals(json_encode($coursedata), $result['responses'][0]['data']);

        $this->assertFalse($result['responses'][1]['error']);
        $userdata = external_api::clean_returnvalue(
            core_user_external::get_users_by_field_returns(),
            core_user_external::get_users_by_field('id', [$user1->id]));
        $this->assertEquals(json_encode($userdata), $result['responses'][1]['data']);

        $this->assertTrue($result['responses'][2]['error']);
        $exception = json_decode($result['responses'][2]['exception'], true);
        $this->assertEquals('nopermissions', $exception['errorcode']);

        // Call a function not included in the external service.

        $_POST['wstoken'] = $token->token;
        $functions = $webservicemanager->get_not_associated_external_functions($service->id);
        $requests = [['function' => current($functions)->name]];
        $result = external::call_external_functions($requests);

        $this->assertTrue($result['responses'][0]['error']);
        $exception = json_decode($result['responses'][0]['exception'], true);
        $this->assertEquals('accessexception', $exception['errorcode']);
        $this->assertEquals('webservice', $exception['module']);

        // Call a function with different external settings.

        filter_set_global_state('multilang', TEXTFILTER_ON);
        $_POST['wstoken'] = $token->token;
        $SESSION->lang = 'eo'; // Change default language, so we can test changing it to "en".
        $requests = [
            [
                'function' => 'core_course_get_courses_by_field',
                'arguments' => json_encode(['field' => 'id', 'value' => $course->id]),
            ],
            [
                'function' => 'core_course_get_courses_by_field',
                'arguments' => json_encode(['field' => 'id', 'value' => $course->id]),
                'settingraw' => '1'
            ],
            [
                'function' => 'core_course_get_courses_by_field',
                'arguments' => json_encode(['field' => 'id', 'value' => $course->id]),
                'settingraw' => '1',
                'settingfileurl' => '0'
            ],
            [
                'function' => 'core_course_get_courses_by_field',
                'arguments' => json_encode(['field' => 'id', 'value' => $course->id]),
                'settingfilter' => '1',
                'settinglang' => 'en'
            ],
        ];
        $result = external::call_external_functions($requests);

        $this->assertCount(4, $result['responses']);

        $context = \context_course::instance($course->id);
        $pluginfile = 'webservice/pluginfile.php';

        $this->assertFalse($result['responses'][0]['error']);
        $data = json_decode($result['responses'][0]['data']);
        $expected = file_rewrite_pluginfile_urls($course->summary, $pluginfile, $context->id, 'course', 'summary', null);
        $expected = format_text($expected, $course->summaryformat, ['para' => false, 'filter' => false]);
        $this->assertEquals($expected, $data->courses[0]->summary);

        $this->assertFalse($result['responses'][1]['error']);
        $data = json_decode($result['responses'][1]['data']);
        $expected = file_rewrite_pluginfile_urls($course->summary, $pluginfile, $context->id, 'course', 'summary', null);
        $this->assertEquals($expected, $data->courses[0]->summary);

        $this->assertFalse($result['responses'][2]['error']);
        $data = json_decode($result['responses'][2]['data']);
        $this->assertEquals($course->summary, $data->courses[0]->summary);

        $this->assertFalse($result['responses'][3]['error']);
        $data = json_decode($result['responses'][3]['data']);
        $expected = file_rewrite_pluginfile_urls($course->summary, $pluginfile, $context->id, 'course', 'summary', null);
        $SESSION->lang = 'en'; // We expect filtered text in english.
        $expected = format_text($expected, $course->summaryformat, ['para' => false, 'filter' => true]);
        $this->assertEquals($expected, $data->courses[0]->summary);
    }

    /*
     * Test get_tokens_for_qr_login.
     */
    public function test_get_tokens_for_qr_login() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $qrloginkey = api::get_qrlogin_key();

        // Generate new tokens, the ones we expect to receive.
        $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE));
        $token = external_generate_token_for_current_user($service);

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
                'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        $result = external::get_tokens_for_qr_login($qrloginkey, $USER->id);
        $result = external_api::clean_returnvalue(external::get_tokens_for_qr_login_returns(), $result);

        $this->assertEmpty($result['warnings']);
        $this->assertEquals($token->token, $result['token']);
        $this->assertEquals($token->privatetoken, $result['privatetoken']);

        // Now, try with an invalid key.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('invalidkey', 'error'));
        $result = external::get_tokens_for_qr_login(random_string('64'), $user->id);
    }

    /**
     * Test get_tokens_for_qr_login missing QR code enabled.
     */
    public function test_get_tokens_for_qr_login_missing_enableqr() {
        global $CFG, $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        set_config('qrcodetype', tool_mobile\api::QR_CODE_DISABLED, 'tool_mobile');

        $this->expectExceptionMessage(get_string('qrcodedisabled', 'tool_mobile'));
        $result = external::get_tokens_for_qr_login('', $USER->id);
    }

    /**
     * Test get_tokens_for_qr_login missing ws.
     */
    public function test_get_tokens_for_qr_login_missing_ws() {
        global $CFG;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        // Need to disable webservices to verify that's checked.
        $CFG->enablewebservices = 0;
        $CFG->enablemobilewebservice = 0;

        $this->setAdminUser();
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('enablewsdescription', 'webservice'));
        $result = external::get_tokens_for_qr_login('', $user->id);
    }

    /**
     * Test get_tokens_for_qr_login missing https.
     */
    public function test_get_tokens_for_qr_login_missing_https() {
        global $CFG, $USER;

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        // Need to simulate a non HTTPS site here.
        $CFG->wwwroot = str_replace('https:', 'http:', $CFG->wwwroot);

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('httpsrequired', 'tool_mobile'));
        $result = external::get_tokens_for_qr_login('', $USER->id);
    }

    /**
     * Test get_tokens_for_qr_login missing admin.
     */
    public function test_get_tokens_for_qr_login_missing_admin() {
        global $CFG, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Fake the app.
        core_useragent::instance(true, 'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2; wv) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/71.0.3578.99 Mobile Safari/537.36 MoodleMobile');

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('autologinnotallowedtoadmins', 'tool_mobile'));
        $result = external::get_tokens_for_qr_login('', $USER->id);
    }

    /**
     * Test get_tokens_for_qr_login missing app_request.
     */
    public function test_get_tokens_for_qr_login_missing_app_request() {
        global $CFG, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('apprequired', 'tool_mobile'));
        $result = external::get_tokens_for_qr_login('', $USER->id);
    }

    /**
     * Test validate subscription key.
     */
    public function test_validate_subscription_key_valid() {
        $this->resetAfterTest(true);

        $sitesubscriptionkey = ['validuntil' => time() + MINSECS, 'key' => complex_random_string(32)];
        set_config('sitesubscriptionkey', json_encode($sitesubscriptionkey), 'tool_mobile');

        $result = external::validate_subscription_key($sitesubscriptionkey['key']);
        $result = external_api::clean_returnvalue(external::validate_subscription_key_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertTrue($result['validated']);
    }

    /**
     * Test validate subscription key invalid first and then a valid one.
     */
    public function test_validate_subscription_key_invalid_key_first() {
        $this->resetAfterTest(true);

        $sitesubscriptionkey = ['validuntil' => time() + MINSECS, 'key' => complex_random_string(32)];
        set_config('sitesubscriptionkey', json_encode($sitesubscriptionkey), 'tool_mobile');

        $result = external::validate_subscription_key('fakekey');
        $result = external_api::clean_returnvalue(external::validate_subscription_key_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertFalse($result['validated']);

        // The valid one has been invalidated because the previous attempt.
        $result = external::validate_subscription_key($sitesubscriptionkey['key']);
        $result = external_api::clean_returnvalue(external::validate_subscription_key_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertFalse($result['validated']);
    }

    /**
     * Test validate subscription key invalid.
     */
    public function test_validate_subscription_key_invalid_key() {
        $this->resetAfterTest(true);

        $result = external::validate_subscription_key('fakekey');
        $result = external_api::clean_returnvalue(external::validate_subscription_key_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertFalse($result['validated']);
    }

    /**
     * Test validate subscription key invalid.
     */
    public function test_validate_subscription_key_outdated() {
        $this->resetAfterTest(true);

        $sitesubscriptionkey = ['validuntil' => time() - MINSECS, 'key' => complex_random_string(32)];
        set_config('sitesubscriptionkey', json_encode($sitesubscriptionkey), 'tool_mobile');

        $result = external::validate_subscription_key($sitesubscriptionkey['key']);
        $result = external_api::clean_returnvalue(external::validate_subscription_key_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertFalse($result['validated']);
    }
}
