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
 * Task tests.
 *
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use tool_lp\api;
use tool_lp\plan;

/**
 * Task tests.
 *
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_task_testcase extends advanced_testcase {

    public function test_sync_plans_from_cohorts_task() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $user1 = $dg->create_user();
        $user2 = $dg->create_user();
        $user3 = $dg->create_user();
        $user4 = $dg->create_user();
        $user5 = $dg->create_user();

        $cohort = $dg->create_cohort();
        $tpl = $lpg->create_template();

        // Add 2 users to the cohort.
        cohort_add_member($cohort->id, $user1->id);
        cohort_add_member($cohort->id, $user2->id);

        // Creating plans from template cohort.
        $templatecohort = api::create_template_cohort($tpl->get_id(), $cohort->id);
        $created = api::create_plans_from_template_cohort($tpl->get_id(), $cohort->id);

        $this->assertEquals(2, $created);

        $task = \core\task\manager::get_scheduled_task('\\tool_lp\\task\\sync_plans_from_template_cohorts_task');
        $this->assertInstanceOf('\tool_lp\task\sync_plans_from_template_cohorts_task', $task);

        // Add two more users to the cohort.
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);

        $task->execute();

        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));

        // Test if remove user from cohort will affect plans.
        cohort_remove_member($cohort->id, $user3->id);
        cohort_remove_member($cohort->id, $user4->id);

        $task->execute();
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));

        // The template is now hidden, and I've added a user with a missing plan. Nothing should happen.
        $tpl->set_visible(false);
        $tpl->update();
        cohort_add_member($cohort->id, $user5->id);
        $this->assertFalse(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));
        $task->execute();
        $this->assertFalse(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));

        // Now I set the template as visible again, the plan is created.
        $tpl->set_visible(true);
        $tpl->update();
        $task->execute();
        $this->assertTrue(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(5, plan::count_records(array('templateid' => $tpl->get_id())));

        // Let's unlink the plan and run the task again, it should not be recreated.
        $plan = plan::get_record(array('userid' => $user5->id, 'templateid' => $tpl->get_id()));
        \tool_lp\api::unlink_plan_from_template($plan);
        $this->assertTrue(plan::record_exists_select('userid = ?', array($user5->id)));
        $this->assertFalse(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));
        $task->execute();
        $this->assertTrue(plan::record_exists_select('userid = ?', array($user5->id)));
        $this->assertFalse(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));

        // Adding users to cohort that already exist in plans.
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);

        $task->execute();
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));
    }

    public function test_sync_plans_from_cohorts_with_templateduedate_task() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $user1 = $dg->create_user();
        $user2 = $dg->create_user();
        $user3 = $dg->create_user();
        $user4 = $dg->create_user();
        $user5 = $dg->create_user();

        $cohort = $dg->create_cohort();
        $tpl = $lpg->create_template(array('duedate' => time() + 400));

        // Add 2 users to the cohort.
        cohort_add_member($cohort->id, $user1->id);
        cohort_add_member($cohort->id, $user2->id);

        // Creating plans from template cohort.
        $templatecohort = api::create_template_cohort($tpl->get_id(), $cohort->id);
        $created = api::create_plans_from_template_cohort($tpl->get_id(), $cohort->id);

        $this->assertEquals(2, $created);

        $task = \core\task\manager::get_scheduled_task('\\tool_lp\\task\\sync_plans_from_template_cohorts_task');
        $this->assertInstanceOf('\tool_lp\task\sync_plans_from_template_cohorts_task', $task);

        // Add two more users to the cohort.
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);

        $task->execute();

        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));

        // Test if remove user from cohort will affect plans.
        cohort_remove_member($cohort->id, $user3->id);
        cohort_remove_member($cohort->id, $user4->id);

        $task->execute();
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));

        // The template is now hidden, and I've added a user with a missing plan. Nothing should happen.
        $tpl->set_visible(false);
        $tpl->update();
        cohort_add_member($cohort->id, $user5->id);
        $this->assertFalse(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));
        $task->execute();
        $this->assertFalse(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));

        // Now I set the template as visible again, the plan is created.
        $tpl->set_visible(true);
        $tpl->update();
        $task->execute();
        $this->assertTrue(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(5, plan::count_records(array('templateid' => $tpl->get_id())));

        // Let's unlink the plan and run the task again, it should not be recreated.
        $plan = plan::get_record(array('userid' => $user5->id, 'templateid' => $tpl->get_id()));
        \tool_lp\api::unlink_plan_from_template($plan);
        $this->assertTrue(plan::record_exists_select('userid = ?', array($user5->id)));
        $this->assertFalse(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));
        $task->execute();
        $this->assertTrue(plan::record_exists_select('userid = ?', array($user5->id)));
        $this->assertFalse(plan::record_exists_select('userid = ? AND templateid = ?', array($user5->id, $tpl->get_id())));
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));

        // Adding users to cohort that already exist in plans.
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);

        $task->execute();
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));
    }

    public function test_sync_plans_from_cohorts_with_passed_duedate() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $user1 = $dg->create_user();
        $user2 = $dg->create_user();
        $cohort = $dg->create_cohort();
        $tpl = $lpg->create_template(array('duedate' => time() + 1000));
        $templatecohort = api::create_template_cohort($tpl->get_id(), $cohort->id);
        $task = \core\task\manager::get_scheduled_task('\\tool_lp\\task\\sync_plans_from_template_cohorts_task');

        // Add 1 user to the cohort.
        cohort_add_member($cohort->id, $user1->id);

        // Creating plans from template cohort.
        $task->execute();
        $this->assertEquals(1, \tool_lp\plan::count_records());

        // Now add another user, but this time the template will be expired.
        cohort_add_member($cohort->id, $user2->id);
        $record = $tpl->to_record();
        $record->duedate = time() - 10000;
        $DB->update_record(\tool_lp\template::TABLE, $record);
        $tpl->read();
        $task->execute();
        $this->assertEquals(1, \tool_lp\plan::count_records()); // Still only one plan.

        // Pretend it wasn't expired.
        $tpl->set_duedate(time() + 100);
        $tpl->update();
        $task->execute();
        $this->assertEquals(2, \tool_lp\plan::count_records()); // Now there is two.
    }

    public function test_complete_plans_task() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');

        $user = $dg->create_user();

        $up1 = $lpg->create_plan(array('userid' => $user->id,
                                        'status' => \tool_lp\plan::STATUS_DRAFT));
        $up2 = $lpg->create_plan(array('userid' => $user->id,
                                        'status' => \tool_lp\plan::STATUS_ACTIVE));
        // Set duedate in the past.
        $date = new \DateTime('yesterday');
        $record1 = $up1->to_record();
        $record2 = $up2->to_record();

        $record1->duedate = $date->getTimestamp();
        $record2->duedate = $date->getTimestamp();
        $DB->update_record(plan::TABLE, $record1);
        $DB->update_record(plan::TABLE, $record2);

        $task = \core\task\manager::get_scheduled_task('\\tool_lp\\task\\complete_plans_task');
        $this->assertInstanceOf('\\tool_lp\\task\\complete_plans_task', $task);

        // Test that draft plan can not be completed on running task.
        $task->execute();

        $plandraft = api::read_plan($up1->get_id());
        $this->assertEquals(\tool_lp\plan::STATUS_DRAFT, $plandraft->get_status());

        // Test that active plan can be completed on running task.
        $task->execute();

        $planactive = api::read_plan($up2->get_id());
        $this->assertEquals(\tool_lp\plan::STATUS_COMPLETE, $planactive->get_status());
    }
}
