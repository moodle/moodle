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
 * Auth external functions tests.
 *
 * @package    core_auth
 * @category   external
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.2
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External auth API tests.
 *
 * @package     core_auth
 * @copyright   2016 Juan Leyva
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.2
 */
class core_auth_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->registerauth = 'email';
    }

    /**
     * Test confirm_user
     */
    public function test_confirm_user() {
        global $DB;

        $username = 'pepe';
        $password = 'abcdefAª.ªª!!3';
        $firstname = 'Pepe';
        $lastname = 'Pérez';
        $email = 'myemail@no.zbc';

        // Create new user.
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email);
        $result = external_api::clean_returnvalue(auth_email_external::signup_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);
        $secret = $DB->get_field('user', 'secret', array('username' => $username));

        // Confirm the user.
        $result = core_auth_external::confirm_user($username, $secret);
        $result = external_api::clean_returnvalue(core_auth_external::confirm_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);
        $confirmed = $DB->get_field('user', 'confirmed', array('username' => $username));
        $this->assertEquals(1, $confirmed);

        // Try to confirm the user again.
        $result = core_auth_external::confirm_user($username, $secret);
        $result = external_api::clean_returnvalue(core_auth_external::confirm_user_returns(), $result);
        $this->assertFalse($result['success']);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('alreadyconfirmed', $result['warnings'][0]['warningcode']);

        // Try to use an invalid secret.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('invalidconfirmdata', 'error'));
        $result = core_auth_external::confirm_user($username, 'zzZZzz');
    }
}
