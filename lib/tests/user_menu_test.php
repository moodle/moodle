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
 * Tests user menu functionality.
 *
 * @package    core
 * @copyright  2015 Jetha Chan <jetha@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user_menu_testcase extends advanced_testcase {

    public function test_custom_user_menu() {

        global $CFG, $OUTPUT, $USER, $PAGE;

        $this->resetAfterTest(true);
        $this->expectOutputString('');

        // Test using an admin user at the root of Moodle; this way we
        // don't have to create a test user with avatar.
        $this->setAdminUser();
        $PAGE->set_url('/');

        // Build a test string for the custom user menu items setting.
        $usermenuitems = 'messages,message|/message/index.php|message
myfiles,moodle|/user/files.php|download
###
mybadges,badges|/badges/mybadges.php|award
-|-|-
test
-
#####
#f234|2';
        set_config('customusermenuitems', $usermenuitems);

        // Fail the test and dump output if an exception is thrown
        // during user menu creation.
        $valid = true;
        try {
            $usermenu = $OUTPUT->user_menu($USER);
        } catch (moodle_exception $me) {
            $valid = false;
            printf(
                "[%s] %s: %s\n%s\n",
                $me->module,
                $me->errorcode,
                $me->message,
                $me->debuginfo
            );
        }
        $this->assertTrue($valid);

    }

}
