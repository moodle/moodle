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
 * Auth oauth2 auth functions tests.
 *
 * @package    auth_oauth2
 * @category   test
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Tests for the \auth_oauth2\auth class.
 *
 * @copyright  2019 Shamim Rezaie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_oauth2_auth_testcase extends advanced_testcase {

    public function test_get_password_change_info() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['auth' => 'oauth2']);
        $auth = get_auth_plugin($user->auth);
        $info = $auth->get_password_change_info($user);

        $this->assertEquals(
                ['subject', 'message'],
                array_keys($info),
                '', 0.0, 10, true);
        $this->assertContains(
                'your password cannot be reset because you are using your account on another site to log in',
                $info['message']);
    }
}