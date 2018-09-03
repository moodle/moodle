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
 * Moodle Mobile admin tool api tests.
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

use tool_mobile\api;

/**
 * Moodle Mobile admin tool api tests.
 *
 * @package     tool_mobile
 * @copyright   2016 Juan Leyva
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.1
 */
class tool_mobile_api_testcase extends externallib_advanced_testcase {

    /**
     * Test get_autologin_key.
     */
    public function test_get_autologin_key() {
        global $USER, $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Set server timezone for test.
        $this->setTimezone('UTC');
        // SEt user to GMT+5.
        $USER->timezone = 5;

        $timenow = $this->setCurrentTimeStart();
        $key = api::get_autologin_key();

        $key = $DB->get_record('user_private_key', array('value' => $key), '*', MUST_EXIST);
        $this->assertTimeCurrent($key->validuntil - api::LOGIN_KEY_TTL);
        $this->assertEquals('0.0.0.0', $key->iprestriction);
    }

    /**
     * Test get_potential_config_issues.
     */
    public function test_get_potential_config_issues() {
        global $CFG;
        require_once($CFG->dirroot . '/message/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $CFG->userquota = '73289234723498234723423489273423497234234';
        $CFG->debugdisplay = 1;
        set_config('debugauthdb', 1, 'auth_db');
        set_config('debugdb', 1, 'enrol_database');
        $expectedissues = array('nohttpsformobilewarning', 'invaliduserquotawarning', 'adodbdebugwarning', 'displayerrorswarning',
            'mobilenotificationsdisabledwarning');

        $processors = get_message_processors();
        foreach ($processors as $processor => $status) {
            if ($processor == 'airnotifier' && $status->enabled) {
                unset($expectedissues['mobilenotificationsdisabledwarning']);
            }
        }

        $issues = api::get_potential_config_issues();
        $this->assertCount(count($expectedissues), $issues);
        foreach ($issues as $issue) {
            $this->assertTrue(in_array($issue[0], $expectedissues));
        }
    }
}
