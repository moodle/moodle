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

        // Adding users to cohort that already exist in plans.
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);

        $task->execute();
        $this->assertEquals(4, plan::count_records(array('templateid' => $tpl->get_id())));
    }
}
