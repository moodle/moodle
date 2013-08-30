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
 * Tests core_user class.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Test core_user class.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user_testcase extends advanced_testcase {

    public function test_get_user() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create user and try fetach it with api.
        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals($user, core_user::get_user($user->id, '*', MUST_EXIST));

        // Test noreply user.
        $CFG->noreplyuserid = null;
        $noreplyuser = core_user::get_noreply_user();
        $this->assertEquals(1, $noreplyuser->emailstop);
        $this->assertFalse(core_user::is_real_user($noreplyuser->id));
        $this->assertEquals($CFG->noreplyaddress, $noreplyuser->email);
        $this->assertEquals(get_string('noreplyname'), $noreplyuser->firstname);

        // Set user as noreply user and make sure noreply propery is set.
        core_user::reset_internal_users();
        $CFG->noreplyuserid = $user->id;
        $noreplyuser = core_user::get_noreply_user();
        $this->assertEquals(1, $noreplyuser->emailstop);
        $this->assertTrue(core_user::is_real_user($noreplyuser->id));

        // Test support user.
        core_user::reset_internal_users();
        $CFG->supportemail = null;
        $CFG->noreplyuserid = null;
        $supportuser = core_user::get_support_user();
        $adminuser = get_admin();
        $this->assertEquals($adminuser, $supportuser);
        $this->assertTrue(core_user::is_real_user($supportuser->id));

        // When supportemail is set.
        core_user::reset_internal_users();
        $CFG->supportemail = 'test@support.moodle.test';
        $supportuser = core_user::get_support_user();
        $this->assertEquals(core_user::SUPPORT_USER, $supportuser->id);
        $this->assertFalse(core_user::is_real_user($supportuser->id));

        // Set user as support user and make sure noreply propery is set.
        core_user::reset_internal_users();
        $CFG->supportuserid = $user->id;
        $supportuser = core_user::get_support_user();
        $this->assertEquals($user, $supportuser);
        $this->assertTrue(core_user::is_real_user($supportuser->id));
    }
}
