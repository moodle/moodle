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
}
