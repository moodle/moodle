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
 * Data provider tests.
 *
 * @package    core_auth
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_auth\privacy\provider;

/**
 * Data provider testcase class.
 *
 * @package    core_auth
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_auth_privacy_testcase extends provider_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_export_user_preferences() {
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $sysctx = context_system::instance();
        $now = time();

        // Check nothing is there.
        writer::reset();
        provider::export_user_preferences($u1->id);
        $prefs = writer::with_context($sysctx)->get_user_preferences('core_auth');
        $this->assertEmpty((array) $prefs);

        // Set some preferences.
        set_user_preference('auth_forcepasswordchange', 1, $u1);
        set_user_preference('create_password', 1, $u1);
        set_user_preference('login_failed_count', 18, $u1);
        set_user_preference('login_failed_count_since_success', 7, $u1);
        set_user_preference('login_failed_last', $now - DAYSECS, $u1);
        set_user_preference('login_lockout', $now - HOURSECS, $u1);
        set_user_preference('login_lockout_ignored', 0, $u1);
        set_user_preference('login_lockout_secret', 'Hello world!', $u1);

        set_user_preference('auth_forcepasswordchange', 0, $u2);
        set_user_preference('create_password', 0, $u2);
        set_user_preference('login_lockout_ignored', 1, $u2);

        // Check user 1.
        writer::reset();
        provider::export_user_preferences($u1->id);
        $prefs = writer::with_context($sysctx)->get_user_preferences('core_auth');
        $this->assertEquals(transform::yesno(true), $prefs->auth_forcepasswordchange->value);
        $this->assertEquals(transform::yesno(true), $prefs->create_password->value);
        $this->assertEquals(18, $prefs->login_failed_count->value);
        $this->assertEquals(7, $prefs->login_failed_count_since_success->value);
        $this->assertEquals(transform::datetime($now - DAYSECS), $prefs->login_failed_last->value);
        $this->assertEquals(transform::datetime($now - HOURSECS), $prefs->login_lockout->value);
        $this->assertEquals(transform::yesno(false), $prefs->login_lockout_ignored->value);
        $this->assertEquals('Hello world!', $prefs->login_lockout_secret->value);

        // Check user 2.
        writer::reset();
        provider::export_user_preferences($u2->id);
        $prefs = writer::with_context($sysctx)->get_user_preferences('core_auth');
        $this->assertEquals(transform::yesno(false), $prefs->auth_forcepasswordchange->value);
        $this->assertEquals(transform::yesno(false), $prefs->create_password->value);
        $this->assertObjectNotHasAttribute('login_failed_count', $prefs);
        $this->assertObjectNotHasAttribute('login_failed_count_since_success', $prefs);
        $this->assertObjectNotHasAttribute('login_failed_last', $prefs);
        $this->assertObjectNotHasAttribute('login_lockout', $prefs);
        $this->assertEquals(transform::yesno(true), $prefs->login_lockout_ignored->value);
        $this->assertObjectNotHasAttribute('login_lockout_secret', $prefs);
    }
}
