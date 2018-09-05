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
            'httpswwwroot' => $CFG->httpswwwroot,
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
            'warnings' => array()
        );
        $this->assertEquals($expected, $result);

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

        if ($logourl = $OUTPUT->get_logo_url()) {
            $expected['logourl'] = $logourl->out(false);
        }
        if ($compactlogourl = $OUTPUT->get_compact_logo_url()) {
            $expected['compactlogourl'] = $compactlogourl->out(false);
        }

        $result = external::get_public_config();
        $result = external_api::clean_returnvalue(external::get_public_config_returns(), $result);
        $this->assertEquals($expected, $result);
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
            array('name' => 'tool_mobile_custommenuitems', 'value' => ''),
            array('name' => 'tool_mobile_apppolicy', 'value' => ''),
        );
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
}
