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

namespace tool_monitor;

/**
 * Unit tests for the tool_monitor clean events task.
 * @since 3.2.0
 *
 * @package    tool_monitor
 * @category   test
 * @copyright  2016 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_check_subscriptions_test extends \advanced_testcase {

    private $course;
    private $user;
    private $rule;
    private $subscription;
    private $teacherrole;
    private $studentrole;

    /**
     * Test set up.
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();
        set_config('enablemonitor', 1, 'tool_monitor');
        $this->resetAfterTest(true);

        // All tests defined herein need a user, course, rule and subscription, so set these up.
        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();

        $rule = new \stdClass();
        $rule->userid = 2; // Rule created by admin.
        $rule->courseid = $this->course->id;
        $rule->plugin = 'mod_book';
        $rule->eventname = '\mod_book\event\course_module_viewed';
        $rule->timewindow = 500;
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $this->rule = $monitorgenerator->create_rule($rule);

        $sub = new \stdClass();
        $sub->courseid = $this->course->id;
        $sub->userid = $this->user->id;
        $sub->ruleid = $this->rule->id;
        $this->subscription = $monitorgenerator->create_subscription($sub);

        // Also set up a student and a teacher role for use in some tests.
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
    }

    /**
     * Reloads the subscription object from the DB.
     *
     * @return void.
     */
    private function reload_subscription() {
        global $DB;
        $sub = $DB->get_record('tool_monitor_subscriptions', array('id' => $this->subscription->id));
        $this->subscription = new \tool_monitor\subscription($sub);
    }

    /**
     * Test to confirm the task is named correctly.
     */
    public function test_task_name(): void {
        $task = new \tool_monitor\task\check_subscriptions();
        $this->assertEquals(get_string('taskchecksubscriptions', 'tool_monitor'), $task->get_name());
    }

    /**
     * Test to confirm that site level subscriptions are activated and deactivated according to system capabilities.
     */
    public function test_site_level_subscription(): void {
        // Create a site level subscription.
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $sub = new \stdClass();
        $sub->userid = $this->user->id;
        $sub->ruleid = $this->rule->id;
        $this->subscription = $monitorgenerator->create_subscription($sub);

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should be inactive as the user doesn't have the capability. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(false, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Now, assign the user as a teacher role at system context.
        $this->getDataGenerator()->role_assign($this->teacherrole->id, $this->user->id, \context_system::instance());

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should be active now. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }

    /**
     * Test to confirm that if the module is disabled, no changes are made to active subscriptions.
     */
    public function test_module_disabled(): void {
        set_config('enablemonitor', 0, 'tool_monitor');

        // Subscription should be active to start with.
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Run the task. Note, we never enrolled the user.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should still be active. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }

    /**
     * Test to confirm an active, valid subscription stays active once the scheduled task is run.
     */
    public function test_active_unaffected(): void {
        // Enrol the user as a teacher. This role should have the required capability.
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id, $this->teacherrole->id);

        // Subscription should be active to start with.
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should still be active. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }

    /**
     * Test to confirm that a subscription for a user without an enrolment to the course is made inactive.
     */
    public function test_course_enrolment(): void {
        // Subscription should be active until deactivated by the scheduled task. Remember, by default the test setup
        // doesn't enrol the user, so the first run of the task should deactivate it.
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should NOT be active. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(false, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Enrol the user.
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id, $this->teacherrole->id);

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // Subscription should now be active again.
        $this->reload_subscription();
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }

    /**
     * Test to confirm that subscriptions for enrolled users without the required capability are made inactive.
     */
    public function test_enrolled_user_with_no_capability(): void {
        // Enrol the user. By default, students won't have the required capability.
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id, $this->studentrole->id);

        // The subscription should be active to start with. Pass in the id only to refetch the data.
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should NOT be active. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(false, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }

    /**
     * Test to confirm that subscriptions for users who fail can_access_course(), are deactivated.
     */
    public function test_can_access_course(): void {
        // Enrol the user as a teacher. This role should have the required capability.
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id, $this->teacherrole->id);

        // Strip the ability to see hidden courses, so we'll fail the check_subscriptions->user_can_access_course call.
        $context = \context_course::instance($this->course->id);
        assign_capability('moodle/course:viewhiddencourses', CAP_PROHIBIT, $this->teacherrole->id, $context);

        // Subscription should be active to start with.
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Hide the course.
        course_change_visibility($this->course->id, false);

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should be inactive. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(false, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }

    /**
     * Test to confirm that subscriptions for enrolled users who don't have CM access, are deactivated.
     */
    public function test_cm_access(): void {
        // Enrol the user as a student but grant to ability to subscribe. Students cannot view hidden activities.
        $context = \context_course::instance($this->course->id);
        assign_capability('tool/monitor:subscribe', CAP_ALLOW, $this->studentrole->id, $context);
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id, $this->studentrole->id);

        // Generate a course module.
        $book = $this->getDataGenerator()->create_module('book', array('course' => $this->course->id));

        // And add a subscription to it.
        $sub = new \stdClass();
        $sub->courseid = $this->course->id;
        $sub->userid = $this->user->id;
        $sub->ruleid = $this->rule->id;
        $sub->cmid = $book->cmid;
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $this->subscription = $monitorgenerator->create_subscription($sub);

        // The subscription should be active to start with. Pass in the id only to refetch the data.
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should still be active. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Make the course module invisible, which should in turn make the subscription inactive.
        set_coursemodule_visible($book->cmid, false);

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should NOT be active. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(false, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Make the course module visible again.
        set_coursemodule_visible($book->cmid, true);

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should be active. Pass in the id only to refetch the data.
        $this->reload_subscription();
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }

    /**
     * Test to confirm that long term inactive subscriptions are removed entirely.
     */
    public function test_stale_subscription_removal(): void {
        global $DB;
        // Manually set the inactivedate to 1 day older than the limit allowed.
        $daysold = 1 + \tool_monitor\subscription_manager::INACTIVE_SUBSCRIPTION_LIFESPAN_IN_DAYS;

        $inactivedate = strtotime("-$daysold days", time());
        $DB->set_field('tool_monitor_subscriptions', 'inactivedate', $inactivedate, array('id' => $this->subscription->id));

        // Subscription should be inactive to start with.
        $this->reload_subscription();
        $this->assertEquals(false, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // Subscription should now not exist at all.
        $this->assertEquals(false, $DB->record_exists('tool_monitor_subscriptions', array('id' => $this->subscription->id)));
    }

    /**
     * Test to confirm that subscriptions for a partially set up user are deactivated.
     */
    public function test_user_not_fully_set_up(): void {
        global $DB;

        // Enrol the user as a teacher.
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id, $this->teacherrole->id);

        // The subscription should be active to start.
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Unset the user's email address, so we fail the check_subscriptions->is_user_setup() call.
        $DB->set_field('user', 'email', '', array('id' => $this->user->id));

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should now be inactive.
        $this->reload_subscription();
        $this->assertEquals(false, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }

    /**
     * Test to confirm that a suspended user's subscriptions are deactivated properly.
     */
    public function test_suspended_user(): void {
        global $DB;

        // Enrol the user as a teacher. This role should have the required capability.
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id, $this->teacherrole->id);

        // Subscription should be active to start with.
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Suspend the user.
        $DB->set_field('user', 'suspended', '1', array('id' => $this->user->id));

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should now be inactive.
        $this->reload_subscription();
        $this->assertEquals(false, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));

        // Unsuspend the user.
        $DB->set_field('user', 'suspended', '0', array('id' => $this->user->id));

        // Run the task.
        $task = new \tool_monitor\task\check_subscriptions();
        $task->execute();

        // The subscription should now be active again.
        $this->reload_subscription();
        $this->assertEquals(true, \tool_monitor\subscription_manager::subscription_is_active($this->subscription));
    }
}
