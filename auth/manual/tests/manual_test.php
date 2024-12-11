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

namespace auth_manual;

use auth_plugin_manual;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/auth/manual/auth.php');

/**
 * Manual authentication tests class.
 *
 * @package    auth_manual
 * @category   test
 * @copyright  2014 Gilles-Philippe Leblanc <gilles-philippe.leblanc@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manual_test extends \advanced_testcase {

    /** @var auth_plugin_manual Keeps the authentication plugin. */
    protected $authplugin;

    /**
     * Setup test data.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->authplugin = new auth_plugin_manual();
        set_config('expiration', '1', 'auth_manual');
        set_config('expiration_warning', '2', 'auth_manual');
        set_config('expirationtime', '30', 'auth_manual');
        $this->authplugin->config = get_config(auth_plugin_manual::COMPONENT_NAME);
    }

    /**
     * Test user_update_password method.
     */
    public function test_user_update_password(): void {
        $user = $this->getDataGenerator()->create_user();
        $expectedtime = time();
        $passwordisupdated = $this->authplugin->user_update_password($user, 'MyNewPassword*');

        // Assert that the actual time should be equal or a little greater than the expected time.
        $this->assertGreaterThanOrEqual($expectedtime, get_user_preferences('auth_manual_passwordupdatetime', 0, $user->id));

        // Assert that the password was successfully updated.
        $this->assertTrue($passwordisupdated);
    }

    /**
     * Test test_password_expire method.
     */
    public function test_password_expire(): void {
        $userrecord = array();
        $expirationtime = 31 * DAYSECS;
        $userrecord['timecreated'] = time() - $expirationtime;
        $user1 = $this->getDataGenerator()->create_user($userrecord);
        $user2 = $this->getDataGenerator()->create_user();

        // The user 1 was created 31 days ago and has not changed his password yet, so the password has expirated.
        $this->assertLessThanOrEqual(-1, $this->authplugin->password_expire($user1->username));

        // The user 2 just came to be created and has not changed his password yet, so the password has not expirated.
        $this->assertEquals(30, $this->authplugin->password_expire($user2->username));

        $this->authplugin->user_update_password($user1, 'MyNewPassword*');

        // The user 1 just updated his password so the password has not expirated.
        $this->assertEquals(30, $this->authplugin->password_expire($user1->username));
    }

}
