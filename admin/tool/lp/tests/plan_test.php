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
 * Plan persistent class tests.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * Plan persistent testcase.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_plan_testcase extends advanced_testcase {

    public function test_can_manage_user() {
        $this->resetAfterTest(true);

        $manage = create_role('Manage', 'manage', 'Plan manager');
        $manageown = create_role('Manageown', 'manageown', 'Own plan manager');

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $u1context = context_user::instance($u1->id);
        $u2context = context_user::instance($u2->id);
        $u3context = context_user::instance($u3->id);

        assign_capability('tool/lp:planmanage', CAP_ALLOW, $manage, $syscontext->id);
        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $manageown, $u2context->id);

        role_assign($manage, $u1->id, $syscontext->id);
        role_assign($manageown, $u2->id, $syscontext->id);
        role_assign($manage, $u3->id, $u2context->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($u1);
        $this->assertTrue(\tool_lp\plan::can_manage_user($u1->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user($u2->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user($u3->id));

        $this->setUser($u2);
        $this->assertFalse(\tool_lp\plan::can_manage_user($u1->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user($u2->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user($u3->id));

        $this->setUser($u3);
        $this->assertFalse(\tool_lp\plan::can_manage_user($u1->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user($u2->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user($u3->id));
    }

    public function test_can_manage_user_draft() {
        $this->resetAfterTest(true);

        $manage = create_role('Manage', 'manage', 'Plan manager');
        $manageown = create_role('Manageown', 'manageown', 'Own plan manager');
        $managedraft = create_role('Managedraft', 'managedraft', 'Draft plan manager');
        $manageowndraft = create_role('Manageowndraft', 'manageowndraft', 'Own draft plan manager');

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $u4 = $this->getDataGenerator()->create_user();
        $u5 = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $u1context = context_user::instance($u1->id);
        $u2context = context_user::instance($u2->id);
        $u3context = context_user::instance($u3->id);
        $u4context = context_user::instance($u4->id);
        $u5context = context_user::instance($u5->id);

        assign_capability('tool/lp:planmanage', CAP_ALLOW, $manage, $syscontext->id);
        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $manageown, $syscontext->id);
        assign_capability('tool/lp:planmanagedraft', CAP_ALLOW, $managedraft, $syscontext->id);
        assign_capability('tool/lp:planmanageowndraft', CAP_ALLOW, $manageowndraft, $syscontext->id);

        role_assign($manage, $u1->id, $syscontext->id);
        role_assign($manageown, $u2->id, $syscontext->id);
        role_assign($managedraft, $u3->id, $syscontext->id);
        role_assign($managedraft, $u4->id, $u2context->id);
        role_assign($manageowndraft, $u5->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($u1);
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u1->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u2->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u3->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u4->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u5->id));

        $this->setUser($u2);
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u1->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u2->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u3->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u4->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u5->id));

        $this->setUser($u3);
        $this->assertTrue(\tool_lp\plan::can_manage_user_draft($u1->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user_draft($u2->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user_draft($u3->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user_draft($u4->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user_draft($u5->id));

        $this->setUser($u4);
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u1->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user_draft($u2->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u3->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u4->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u5->id));

        $this->setUser($u5);
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u1->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u2->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u3->id));
        $this->assertFalse(\tool_lp\plan::can_manage_user_draft($u4->id));
        $this->assertTrue(\tool_lp\plan::can_manage_user_draft($u5->id));
    }

    public function test_can_read_user() {
        $this->resetAfterTest(true);

        $read = create_role('Read', 'read', 'Plan reader');
        $readown = create_role('Readown', 'readown', 'Own plan reader');

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $u1context = context_user::instance($u1->id);
        $u2context = context_user::instance($u2->id);
        $u3context = context_user::instance($u3->id);

        assign_capability('tool/lp:planview', CAP_ALLOW, $read, $syscontext->id);
        assign_capability('tool/lp:planviewown', CAP_ALLOW, $readown, $u2context->id);

        role_assign($read, $u1->id, $syscontext->id);
        role_assign($readown, $u2->id, $syscontext->id);
        role_assign($read, $u3->id, $u2context->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($u1);
        $this->assertTrue(\tool_lp\plan::can_read_user($u1->id));
        $this->assertTrue(\tool_lp\plan::can_read_user($u2->id));
        $this->assertTrue(\tool_lp\plan::can_read_user($u3->id));

        $this->setUser($u2);
        $this->assertFalse(\tool_lp\plan::can_read_user($u1->id));
        $this->assertTrue(\tool_lp\plan::can_read_user($u2->id));
        $this->assertFalse(\tool_lp\plan::can_read_user($u3->id));

        $this->setUser($u3);
        $this->assertFalse(\tool_lp\plan::can_read_user($u1->id));
        $this->assertTrue(\tool_lp\plan::can_read_user($u2->id));
        $this->assertTrue(\tool_lp\plan::can_read_user($u3->id));    // Due to the default capability.
    }

    public function test_can_read_user_draft() {
        $this->resetAfterTest(true);

        $read = create_role('Read', 'read', 'Plan readr');
        $readown = create_role('Readown', 'readown', 'Own plan readr');
        $readdraft = create_role('Readdraft', 'readdraft', 'Draft plan readr');
        $readowndraft = create_role('Readowndraft', 'readowndraft', 'Own draft plan readr');

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $u4 = $this->getDataGenerator()->create_user();
        $u5 = $this->getDataGenerator()->create_user();

        $syscontext = context_system::instance();
        $u1context = context_user::instance($u1->id);
        $u2context = context_user::instance($u2->id);
        $u3context = context_user::instance($u3->id);
        $u4context = context_user::instance($u4->id);
        $u5context = context_user::instance($u5->id);

        assign_capability('tool/lp:planview', CAP_ALLOW, $read, $syscontext->id);
        assign_capability('tool/lp:planviewown', CAP_ALLOW, $readown, $syscontext->id);
        assign_capability('tool/lp:planviewdraft', CAP_ALLOW, $readdraft, $syscontext->id);
        assign_capability('tool/lp:planviewowndraft', CAP_ALLOW, $readowndraft, $syscontext->id);
        assign_capability('tool/lp:planviewown', CAP_PROHIBIT, $readowndraft, $syscontext->id);

        role_assign($read, $u1->id, $syscontext->id);
        role_assign($readown, $u2->id, $syscontext->id);
        role_assign($readdraft, $u3->id, $syscontext->id);
        role_assign($readdraft, $u4->id, $u2context->id);
        role_assign($readowndraft, $u5->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($u1);
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u1->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u2->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u3->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u4->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u5->id));

        $this->setUser($u2);
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u1->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u2->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u3->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u4->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u5->id));

        $this->setUser($u3);
        $this->assertTrue(\tool_lp\plan::can_read_user_draft($u1->id));
        $this->assertTrue(\tool_lp\plan::can_read_user_draft($u2->id));
        $this->assertTrue(\tool_lp\plan::can_read_user_draft($u3->id));
        $this->assertTrue(\tool_lp\plan::can_read_user_draft($u4->id));
        $this->assertTrue(\tool_lp\plan::can_read_user_draft($u5->id));

        $this->setUser($u4);
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u1->id));
        $this->assertTrue(\tool_lp\plan::can_read_user_draft($u2->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u3->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u4->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u5->id));

        $this->setUser($u5);
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u1->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u2->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u3->id));
        $this->assertFalse(\tool_lp\plan::can_read_user_draft($u4->id));
        $this->assertTrue(\tool_lp\plan::can_read_user_draft($u5->id));
    }

    public function test_validate_duedate() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user = $dg->create_user();

        $record = array('userid' => $user->id,
                        'status' => tool_lp\plan::STATUS_DRAFT,
                        'duedate' => time() - 8000);

        // Ignore duedate validation on create/update draft plan.
        $plan = $lpg->create_plan($record);
        $this->assertInstanceOf('\tool_lp\plan', $plan);

        // Passing from draft to active.
        $plan->set_status(tool_lp\plan::STATUS_ACTIVE);

        // Draft to active with duedate in the past.
        $expected = array(
            'duedate' => new lang_string('errorcannotsetduedateinthepast', 'tool_lp'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Draft to active: past date => past date(fail).
        $plan->set_duedate(time() - 100);
        $expected = array(
            'duedate' => new lang_string('errorcannotsetduedateinthepast', 'tool_lp'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Draft to active: past date => too soon (fail).
        $plan->set_duedate(time() + 100);
        $expected = array(
            'duedate' => new lang_string('errorcannotsetduedatetoosoon', 'tool_lp'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Draft to active: past date => future date (pass).
        $plan->set_duedate(time() + tool_lp\plan::DUEDATE_THRESHOLD + 10);
        $this->assertEquals(true, $plan->validate());

        // Draft to active: past date => unset date (pass).
        $plan->set_duedate(0);
        $this->assertEquals(true, $plan->validate());

        // Updating active plan.
        $plan->update();

        // Active to active: unset date => past date(fail).
        $plan->set_duedate(time() - 100);
        $expected = array(
            'duedate' => new lang_string('errorcannotsetduedateinthepast', 'tool_lp'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Active to active: unset date => too soon (fail).
        $plan->set_duedate(time() + 100);
        $expected = array(
            'duedate' => new lang_string('errorcannotsetduedatetoosoon', 'tool_lp'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Active to active: unset date => future date (pass).
        $plan->set_duedate(time() + tool_lp\plan::DUEDATE_THRESHOLD + 10);
        $this->assertEquals(true, $plan->validate());

        // Updating active plan with future date.
        $plan->update();

        // Active to active: future date => unset date (pass).
        $plan->set_duedate(0);
        $this->assertEquals(true, $plan->validate());

        // Active to active: future date => past date(fail).
        $plan->set_duedate(time() - 100);
        $expected = array(
            'duedate' => new lang_string('errorcannotsetduedateinthepast', 'tool_lp'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Active to active: future date => too soon (fail).
        $plan->set_duedate(time() + 100);
        $expected = array(
            'duedate' => new lang_string('errorcannotsetduedatetoosoon', 'tool_lp'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Active to active: future date => future date (pass).
        $plan->set_duedate(time() + tool_lp\plan::DUEDATE_THRESHOLD + 10);
        $this->assertEquals(true, $plan->validate());

        // Completing plan: with due date in the past.
        $record = $plan->to_record();
        $record->status = tool_lp\plan::STATUS_ACTIVE;
        $record->duedate = time() - 200;
        $DB->update_record(tool_lp\plan::TABLE, $record);

        $success = tool_lp\api::complete_plan($plan->get_id());
        $this->assertTrue($success);

        // Completing plan: with due date too soon (pass).
        $record = $plan->to_record();
        $record->status = tool_lp\plan::STATUS_ACTIVE;
        $record->duedate = time() + 200;
        $DB->update_record(tool_lp\plan::TABLE, $record);

        $success = tool_lp\api::complete_plan($plan->get_id());
        $this->assertTrue($success);

        // Completing plan: with due date in the future (pass).
        $record = $plan->to_record();
        $record->status = tool_lp\plan::STATUS_ACTIVE;
        $record->duedate = time() + tool_lp\plan::DUEDATE_THRESHOLD + 10;
        $DB->update_record(tool_lp\plan::TABLE, $record);

        $success = tool_lp\api::complete_plan($plan->get_id());
        $this->assertTrue($success);

        // Completing plan: with due date unset (pass).
        $record = $plan->to_record();
        $record->status = tool_lp\plan::STATUS_ACTIVE;
        $record->duedate = 0;
        $DB->update_record(tool_lp\plan::TABLE, $record);

        $success = tool_lp\api::complete_plan($plan->get_id());
        $this->assertTrue($success);

        // Reopening plan: with due date in the past => duedate unset.
        $record = $plan->to_record();
        $record->status = tool_lp\plan::STATUS_COMPLETE;
        $record->duedate = time() - 200;
        $DB->update_record(tool_lp\plan::TABLE, $record);

        $success = tool_lp\api::reopen_plan($plan->get_id());
        $this->assertTrue($success);
        $plan->read();
        $this->assertEquals(0, $plan->get_duedate());

        // Reopening plan: with due date too soon => duedate unset.
        $record = $plan->to_record();
        $record->status = tool_lp\plan::STATUS_COMPLETE;
        $record->duedate = time() + 100;
        $DB->update_record(tool_lp\plan::TABLE, $record);

        $success = tool_lp\api::reopen_plan($plan->get_id());
        $this->assertTrue($success);
        $plan->read();
        $this->assertEquals(0, $plan->get_duedate());

        // Reopening plan: with due date in the future => duedate unchanged.
        $record = $plan->to_record();
        $record->status = tool_lp\plan::STATUS_COMPLETE;
        $record->duedate = time() + tool_lp\plan::DUEDATE_THRESHOLD + 10;
        $DB->update_record(tool_lp\plan::TABLE, $record);

        $success = tool_lp\api::reopen_plan($plan->get_id());
        $this->assertTrue($success);
        $plan->read();
        $this->assertEquals(time() + tool_lp\plan::DUEDATE_THRESHOLD + 10, $plan->get_duedate());

    }
}
