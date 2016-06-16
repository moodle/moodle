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

use tool_mobile\external;

/**
 * External learning plans webservice API tests.
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
        $this->assertCount(0, $result['plugins']);
    }

    public function test_get_site_public_settings() {
        global $CFG, $SITE;

        $this->resetAfterTest(true);
        $result = external::get_site_public_settings();
        $result = external_api::clean_returnvalue(external::get_site_public_settings_returns(), $result);

        // Test default values.
        $context = context_system::instance();
        $expected = array(
            'wwwroot' => $CFG->wwwroot,
            'httpswwwroot' => $CFG->httpswwwroot,
            'sitename' => external_format_string($SITE->fullname, $context->id, true),
            'guestlogin' => $CFG->guestloginbutton,
            'rememberusername' => $CFG->rememberusername,
            'authloginviaemail' => $CFG->authloginviaemail,
            'registerauth' => $CFG->registerauth,
            'forgottenpasswordurl' => $CFG->forgottenpasswordurl,
            'authinstructions' => format_text($CFG->auth_instructions),
            'authnoneenabled' => (int) is_enabled_auth('none'),
            'enablewebservices' => $CFG->enablewebservices,
            'enablemobilewebservice' => $CFG->enablemobilewebservice,
            'maintenanceenabled' => $CFG->maintenance_enabled,
            'maintenancemessage' => format_text($CFG->maintenance_message),
            'warnings' => array()
        );
        $this->assertEquals($expected, $result);

        // Change a value.
        set_config('registerauth', 'email');
        $authinstructions = 'Something with <b>html tags</b>';
        set_config('auth_instructions', $authinstructions);

        $expected['registerauth'] = 'email';
        $expected['authinstructions'] = format_text($authinstructions);

        $result = external::get_site_public_settings();
        $result = external_api::clean_returnvalue(external::get_site_public_settings_returns(), $result);
        $this->assertEquals($expected, $result);
    }

}
