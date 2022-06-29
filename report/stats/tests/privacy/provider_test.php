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
 * Tests for privacy functions.
 *
 * @package    report_stats
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace report_stats\privacy;

use report_stats\privacy\provider;
use core_privacy\local\request\approved_userlist;
use core_privacy\tests\provider_testcase;

/**
 * Privacy provider test for report_stats.
 *
 * @package    report_stats
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class provider_test extends provider_testcase {

    /**
     * Convenience function to create stats.
     *
     * @param int $courseid Course ID for this record.
     * @param int $userid User ID for this record.
     * @param string $table Stat table to insert into.
     */
    protected function create_stats($courseid, $userid, $table) {
        global $DB;

        $data = (object) [
            'courseid' => $courseid,
            'userid' => $userid,
            'roleid' => 0,
            'timeend' => time(),
            'statsreads' => rand(1, 50),
            'statswrites' => rand(1, 50),
            'stattype' => 'activity'
        ];
        $DB->insert_record($table, $data);
    }

    /**
     * Get all of the contexts related to a user and stat tables.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $context3 = \context_course::instance($course3->id);

        $this->create_stats($course1->id, $user1->id, 'stats_user_daily');
        $this->create_stats($course2->id, $user1->id, 'stats_user_monthly');
        $this->create_stats($course1->id, $user2->id, 'stats_user_weekly');

        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(2, $contextlist->get_contextids());
        foreach ($contextlist->get_contexts() as $context) {
            $this->assertEquals(CONTEXT_COURSE, $context->contextlevel);
            $this->assertNotEquals($context3, $context);
        }
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(1, $contextlist->get_contextids());
        $this->assertEquals($context1, $contextlist->current());
    }

    /**
     * Test that stat data is exported as required.
     */
    public function test_export_user_data() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $this->create_stats($course1->id, $user->id, 'stats_user_daily');
        $this->create_stats($course1->id, $user->id, 'stats_user_daily');
        $this->create_stats($course2->id, $user->id, 'stats_user_weekly');
        $this->create_stats($course2->id, $user->id, 'stats_user_monthly');
        $this->create_stats($course1->id, $user->id, 'stats_user_monthly');

        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'report_stats', [$context1->id, $context2->id]);

        provider::export_user_data($approvedlist);
        $writer = \core_privacy\local\request\writer::with_context($context1);
        $dailystats = (array) $writer->get_data([get_string('privacy:dailypath', 'report_stats')]);
        $this->assertCount(2, $dailystats);
        $monthlystats = (array) $writer->get_data([get_string('privacy:monthlypath', 'report_stats')]);
        $this->assertCount(1, $monthlystats);
        $data = array_shift($monthlystats);
        $this->assertEquals($course1->fullname, $data['course']);
        $writer = \core_privacy\local\request\writer::with_context($context2);
        $monthlystats = (array) $writer->get_data([get_string('privacy:monthlypath', 'report_stats')]);
        $this->assertCount(1, $monthlystats);
        $data = array_shift($monthlystats);
        $this->assertEquals($course2->fullname, $data['course']);
        $weeklystats = (array) $writer->get_data([get_string('privacy:weeklypath', 'report_stats')]);
        $this->assertCount(1, $weeklystats);
        $data = array_shift($weeklystats);
        $this->assertEquals($course2->fullname, $data['course']);
    }

    /**
     * Test that stat data is deleted for a whole context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $this->create_stats($course1->id, $user1->id, 'stats_user_daily');
        $this->create_stats($course1->id, $user1->id, 'stats_user_daily');
        $this->create_stats($course1->id, $user1->id, 'stats_user_monthly');
        $this->create_stats($course1->id, $user2->id, 'stats_user_weekly');
        $this->create_stats($course2->id, $user2->id, 'stats_user_daily');
        $this->create_stats($course2->id, $user2->id, 'stats_user_weekly');
        $this->create_stats($course2->id, $user2->id, 'stats_user_monthly');

        $dailyrecords = $DB->get_records('stats_user_daily');
        $this->assertCount(3, $dailyrecords);
        $weeklyrecords = $DB->get_records('stats_user_weekly');
        $this->assertCount(2, $weeklyrecords);
        $monthlyrecords = $DB->get_records('stats_user_monthly');
        $this->assertCount(2, $monthlyrecords);

        // Delete all user data for course 1.
        provider::delete_data_for_all_users_in_context($context1);
        $dailyrecords = $DB->get_records('stats_user_daily');
        $this->assertCount(1, $dailyrecords);
        $weeklyrecords = $DB->get_records('stats_user_weekly');
        $this->assertCount(1, $weeklyrecords);
        $monthlyrecords = $DB->get_records('stats_user_monthly');
        $this->assertCount(1, $monthlyrecords);
    }

    /**
     * Test that stats are deleted for one user.
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $this->create_stats($course1->id, $user1->id, 'stats_user_daily');
        $this->create_stats($course1->id, $user1->id, 'stats_user_daily');
        $this->create_stats($course1->id, $user1->id, 'stats_user_monthly');
        $this->create_stats($course1->id, $user2->id, 'stats_user_weekly');
        $this->create_stats($course2->id, $user2->id, 'stats_user_daily');
        $this->create_stats($course2->id, $user2->id, 'stats_user_weekly');
        $this->create_stats($course2->id, $user2->id, 'stats_user_monthly');

        $dailyrecords = $DB->get_records('stats_user_daily');
        $this->assertCount(3, $dailyrecords);
        $weeklyrecords = $DB->get_records('stats_user_weekly');
        $this->assertCount(2, $weeklyrecords);
        $monthlyrecords = $DB->get_records('stats_user_monthly');
        $this->assertCount(2, $monthlyrecords);

        // Delete all user data for course 1.
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user1, 'report_stats', [$context1->id]);
        provider::delete_data_for_user($approvedlist);
        $dailyrecords = $DB->get_records('stats_user_daily');
        $this->assertCount(1, $dailyrecords);
        $weeklyrecords = $DB->get_records('stats_user_weekly');
        $this->assertCount(2, $weeklyrecords);
        $monthlyrecords = $DB->get_records('stats_user_monthly');
        $this->assertCount(1, $monthlyrecords);
    }

    /**
     * Test that only users within a course context are fetched.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $component = 'report_stats';

        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        // Create course1.
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        // Create course2.
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext2 = \context_course::instance($course2->id);

        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);

        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);

        $this->create_stats($course1->id, $user1->id, 'stats_user_daily');
        $this->create_stats($course2->id, $user1->id, 'stats_user_monthly');
        $this->create_stats($course1->id, $user2->id, 'stats_user_weekly');

        // The list of users within the course context should contain users.
        provider::get_users_in_context($userlist1);
        $this->assertCount(2, $userlist1);
        $this->assertTrue(in_array($user1->id, $userlist1->get_userids()));
        $this->assertTrue(in_array($user2->id, $userlist1->get_userids()));

        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $this->assertTrue(in_array($user1->id, $userlist2->get_userids()));

        // The list of users within other contexts than course should be empty.
        $systemcontext = \context_system::instance();
        $userlist3 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $this->resetAfterTest();

        $component = 'report_stats';

        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        // Create user3.
        $user3 = $this->getDataGenerator()->create_user();
        // Create course1.
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        // Create course2.
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext2 = \context_course::instance($course2->id);

        $this->create_stats($course1->id, $user1->id, 'stats_user_daily');
        $this->create_stats($course2->id, $user1->id, 'stats_user_monthly');
        $this->create_stats($course1->id, $user2->id, 'stats_user_weekly');
        $this->create_stats($course1->id, $user3->id, 'stats_user_weekly');

        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(3, $userlist1);

        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($coursecontext1, $component, [$user1->id, $user2->id]);
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist1);

        // Re-fetch users in coursecontext1.
        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist1);
        // The approved user data in coursecontext1 should be deleted.
        // The user list should still return user3.
        $this->assertCount(1, $userlist1);
        $this->assertTrue(in_array($user3->id, $userlist1->get_userids()));
        // Re-fetch users in coursecontext2.
        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        // The user data in coursecontext2 should be still present.
        $this->assertCount(1, $userlist2);

        // Convert $userlist2 into an approved_contextlist in the system context.
        $systemcontext = \context_system::instance();
        $approvedlist2 = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist2);
        // Re-fetch users in coursecontext2.
        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        // The user data in systemcontext should not be deleted.
        $this->assertCount(1, $userlist2);
    }
}
