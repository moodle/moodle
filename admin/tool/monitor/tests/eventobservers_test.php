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
 * Unit tests for event observers.
 *
 * @package    tool_monitor
 * @category   phpunit
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class tool_monitor_eventobservers_testcase extends advanced_testcase {

    /**
     * Test observer for course delete event.
     */
    public function test_course_deleted() {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $rule = new stdClass();
        $rule->userid = $user->id;
        $rule->courseid = $course->id;
        $rule->plugin = 'test';

        $sub = new stdClass();
        $sub->courseid = $course->id;
        $sub->userid = $user->id;

        // Add 10 rules for this course with subscriptions.
        for ($i = 0; $i < 10; $i++) {
            $createdrule = $monitorgenerator->create_rule($rule);
            $sub->ruleid = $createdrule->id;
            $monitorgenerator->create_subscription($sub);
        }

        // Add 10 random rules for random courses.
        for ($i = 0; $i < 10; $i++) {
            $rule->courseid = rand(10000000, 50000000);
            $createdrule = $monitorgenerator->create_rule($rule);
            $sub->courseid = $rule->courseid;
            $sub->ruleid = $createdrule->id;
            $monitorgenerator->create_subscription($sub);
        }

        // Verify data before course delete.
        $totalrules = \tool_monitor\rule_manager::get_rules_by_plugin('test');
        $this->assertCount(20, $totalrules);
        $courserules = \tool_monitor\rule_manager::get_rules_by_courseid($course->id);
        $this->assertCount(10, $courserules);
        $totalsubs = $DB->get_records('tool_monitor_subscriptions');
        $this->assertCount(20, $totalsubs);
        $coursesubs = \tool_monitor\subscription_manager::get_user_subscriptions_for_course($course->id, 0, 0, $user->id);
        $this->assertCount(10, $coursesubs);

        // Let us delete the course now.
        delete_course($course->id, false);

        // Verify data after course delete.
        $totalrules = \tool_monitor\rule_manager::get_rules_by_plugin('test');
        $this->assertCount(10, $totalrules);
        $courserules = \tool_monitor\rule_manager::get_rules_by_courseid($course->id);
        $this->assertCount(0, $courserules); // Making sure all rules are deleted.
        $totalsubs = $DB->get_records('tool_monitor_subscriptions');
        $this->assertCount(10, $totalsubs);
        $coursesubs = \tool_monitor\subscription_manager::get_user_subscriptions_for_course($course->id, 0, 0, $user->id);
        $this->assertCount(0, $coursesubs); // Making sure all subscriptions are deleted.
    }
}
