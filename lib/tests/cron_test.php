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

namespace core;

/**
 * Tests for core\cron.
 *
 * @package     core
 * @copyright   2023 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\cron
 */
class cron_test extends \advanced_testcase {
    /**
     * Reset relevant caches between tests.
     */
    public function setUp(): void {
        parent::setUp();
        cron::reset_user_cache();
    }

    /**
     * Test the setup_user function.
     */
    public function test_setup_user(): void {
        // This function uses the $GLOBALS super global. Disable the VariableNameLowerCase sniff for this function.
        // phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
        global $PAGE, $USER, $SESSION, $SITE, $CFG;
        $this->resetAfterTest();

        $admin = get_admin();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        cron::setup_user();
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertSame($CFG->timezone, $USER->timezone);
        $this->assertSame('', $USER->lang);
        $this->assertSame('', $USER->theme);
        $SESSION->test1 = true;
        $adminsession = $SESSION;
        $adminuser = $USER;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user(null, $course);
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($course->id));
        $this->assertSame($adminsession, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertObjectNotHasProperty('test1', $SESSION);
        $this->assertEmpty((array)$SESSION);
        $usersession1 = $SESSION;
        $SESSION->test2 = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertSame($usersession1, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user2);
        $this->assertSame($user2->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertEmpty((array)$SESSION);
        $usersession2 = $SESSION;
        $usersession2->test3 = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user2, $course);
        $this->assertSame($user2->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($course->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertSame($usersession2, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertEmpty((array)$SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user();
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertSame($adminsession, $SESSION);
        $this->assertSame($adminuser, $USER);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::reset_user_cache();
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron::setup_user();
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($adminuser, $USER);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        // phpcs:enable
    }
}
