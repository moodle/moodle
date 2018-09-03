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

defined('MOODLE_INTERNAL') || die();

/**
 * Class report_stats_privacy_testcase
 *
 * @package    report_stats
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class report_stats_privacy_testcase extends advanced_testcase {

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

        $context1 = context_course::instance($course1->id);
        $context2 = context_course::instance($course2->id);
        $context3 = context_course::instance($course3->id);

        $this->create_stats($course1->id, $user1->id, 'stats_user_daily');
        $this->create_stats($course2->id, $user1->id, 'stats_user_monthly');
        $this->create_stats($course1->id, $user2->id, 'stats_user_weekly');

        $contextlist = \report_stats\privacy\provider::get_contexts_for_userid($user1->id);
        $this->assertCount(2, $contextlist->get_contextids());
        foreach ($contextlist->get_contexts() as $context) {
            $this->assertEquals(CONTEXT_COURSE, $context->contextlevel);
            $this->assertNotEquals($context3, $context);
        }
        $contextlist = \report_stats\privacy\provider::get_contexts_for_userid($user2->id);
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
        $context1 = context_course::instance($course1->id);
        $context2 = context_course::instance($course2->id);
        $this->create_stats($course1->id, $user->id, 'stats_user_daily');
        $this->create_stats($course1->id, $user->id, 'stats_user_daily');
        $this->create_stats($course2->id, $user->id, 'stats_user_weekly');
        $this->create_stats($course2->id, $user->id, 'stats_user_monthly');
        $this->create_stats($course1->id, $user->id, 'stats_user_monthly');

        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'report_stats', [$context1->id, $context2->id]);

        \report_stats\privacy\provider::export_user_data($approvedlist);
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
        $context1 = context_course::instance($course1->id);
        $context2 = context_course::instance($course2->id);
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
        \report_stats\privacy\provider::delete_data_for_all_users_in_context($context1);
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
        $context1 = context_course::instance($course1->id);
        $context2 = context_course::instance($course2->id);
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
        \report_stats\privacy\provider::delete_data_for_user($approvedlist);
        $dailyrecords = $DB->get_records('stats_user_daily');
        $this->assertCount(1, $dailyrecords);
        $weeklyrecords = $DB->get_records('stats_user_weekly');
        $this->assertCount(2, $weeklyrecords);
        $monthlyrecords = $DB->get_records('stats_user_monthly');
        $this->assertCount(1, $monthlyrecords);
    }
}
