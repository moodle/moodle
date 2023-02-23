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

namespace core_competency;

/**
 * Plan persistent testcase.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_test extends \advanced_testcase {

    public function test_can_manage_user() {
        $this->resetAfterTest(true);

        $manage = create_role('Manage', 'manage', 'Plan manager');
        $manageown = create_role('Manageown', 'manageown', 'Own plan manager');

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        $syscontext = \context_system::instance();
        $u1context = \context_user::instance($u1->id);
        $u2context = \context_user::instance($u2->id);
        $u3context = \context_user::instance($u3->id);

        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $manage, $syscontext->id);
        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $manageown, $u2context->id);

        role_assign($manage, $u1->id, $syscontext->id);
        role_assign($manageown, $u2->id, $syscontext->id);
        role_assign($manage, $u3->id, $u2context->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($u1);
        $this->assertTrue(plan::can_manage_user($u1->id));
        $this->assertTrue(plan::can_manage_user($u2->id));
        $this->assertTrue(plan::can_manage_user($u3->id));

        $this->setUser($u2);
        $this->assertFalse(plan::can_manage_user($u1->id));
        $this->assertTrue(plan::can_manage_user($u2->id));
        $this->assertFalse(plan::can_manage_user($u3->id));

        $this->setUser($u3);
        $this->assertFalse(plan::can_manage_user($u1->id));
        $this->assertTrue(plan::can_manage_user($u2->id));
        $this->assertFalse(plan::can_manage_user($u3->id));
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

        $syscontext = \context_system::instance();
        $u1context = \context_user::instance($u1->id);
        $u2context = \context_user::instance($u2->id);
        $u3context = \context_user::instance($u3->id);
        $u4context = \context_user::instance($u4->id);
        $u5context = \context_user::instance($u5->id);

        assign_capability('moodle/competency:planmanage', CAP_ALLOW, $manage, $syscontext->id);
        assign_capability('moodle/competency:planmanageown', CAP_ALLOW, $manageown, $syscontext->id);
        assign_capability('moodle/competency:planmanagedraft', CAP_ALLOW, $managedraft, $syscontext->id);
        assign_capability('moodle/competency:planmanageowndraft', CAP_ALLOW, $manageowndraft, $syscontext->id);

        role_assign($manage, $u1->id, $syscontext->id);
        role_assign($manageown, $u2->id, $syscontext->id);
        role_assign($managedraft, $u3->id, $syscontext->id);
        role_assign($managedraft, $u4->id, $u2context->id);
        role_assign($manageowndraft, $u5->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($u1);
        $this->assertFalse(plan::can_manage_user_draft($u1->id));
        $this->assertFalse(plan::can_manage_user_draft($u2->id));
        $this->assertFalse(plan::can_manage_user_draft($u3->id));
        $this->assertFalse(plan::can_manage_user_draft($u4->id));
        $this->assertFalse(plan::can_manage_user_draft($u5->id));

        $this->setUser($u2);
        $this->assertFalse(plan::can_manage_user_draft($u1->id));
        $this->assertFalse(plan::can_manage_user_draft($u2->id));
        $this->assertFalse(plan::can_manage_user_draft($u3->id));
        $this->assertFalse(plan::can_manage_user_draft($u4->id));
        $this->assertFalse(plan::can_manage_user_draft($u5->id));

        $this->setUser($u3);
        $this->assertTrue(plan::can_manage_user_draft($u1->id));
        $this->assertTrue(plan::can_manage_user_draft($u2->id));
        $this->assertTrue(plan::can_manage_user_draft($u3->id));
        $this->assertTrue(plan::can_manage_user_draft($u4->id));
        $this->assertTrue(plan::can_manage_user_draft($u5->id));

        $this->setUser($u4);
        $this->assertFalse(plan::can_manage_user_draft($u1->id));
        $this->assertTrue(plan::can_manage_user_draft($u2->id));
        $this->assertFalse(plan::can_manage_user_draft($u3->id));
        $this->assertFalse(plan::can_manage_user_draft($u4->id));
        $this->assertFalse(plan::can_manage_user_draft($u5->id));

        $this->setUser($u5);
        $this->assertFalse(plan::can_manage_user_draft($u1->id));
        $this->assertFalse(plan::can_manage_user_draft($u2->id));
        $this->assertFalse(plan::can_manage_user_draft($u3->id));
        $this->assertFalse(plan::can_manage_user_draft($u4->id));
        $this->assertTrue(plan::can_manage_user_draft($u5->id));
    }

    public function test_can_read_user() {
        $this->resetAfterTest(true);

        $read = create_role('Read', 'read', 'Plan reader');
        $readown = create_role('Readown', 'readown', 'Own plan reader');

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();

        $syscontext = \context_system::instance();
        $u1context = \context_user::instance($u1->id);
        $u2context = \context_user::instance($u2->id);
        $u3context = \context_user::instance($u3->id);

        assign_capability('moodle/competency:planview', CAP_ALLOW, $read, $syscontext->id);
        assign_capability('moodle/competency:planviewown', CAP_ALLOW, $readown, $u2context->id);

        role_assign($read, $u1->id, $syscontext->id);
        role_assign($readown, $u2->id, $syscontext->id);
        role_assign($read, $u3->id, $u2context->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($u1);
        $this->assertTrue(plan::can_read_user($u1->id));
        $this->assertTrue(plan::can_read_user($u2->id));
        $this->assertTrue(plan::can_read_user($u3->id));

        $this->setUser($u2);
        $this->assertFalse(plan::can_read_user($u1->id));
        $this->assertTrue(plan::can_read_user($u2->id));
        $this->assertFalse(plan::can_read_user($u3->id));

        $this->setUser($u3);
        $this->assertFalse(plan::can_read_user($u1->id));
        $this->assertTrue(plan::can_read_user($u2->id));
        $this->assertTrue(plan::can_read_user($u3->id));    // Due to the default capability.
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

        $syscontext = \context_system::instance();
        $u1context = \context_user::instance($u1->id);
        $u2context = \context_user::instance($u2->id);
        $u3context = \context_user::instance($u3->id);
        $u4context = \context_user::instance($u4->id);
        $u5context = \context_user::instance($u5->id);

        assign_capability('moodle/competency:planview', CAP_ALLOW, $read, $syscontext->id);
        assign_capability('moodle/competency:planviewown', CAP_ALLOW, $readown, $syscontext->id);
        assign_capability('moodle/competency:planviewdraft', CAP_ALLOW, $readdraft, $syscontext->id);
        assign_capability('moodle/competency:planviewowndraft', CAP_ALLOW, $readowndraft, $syscontext->id);
        assign_capability('moodle/competency:planviewown', CAP_PROHIBIT, $readowndraft, $syscontext->id);

        role_assign($read, $u1->id, $syscontext->id);
        role_assign($readown, $u2->id, $syscontext->id);
        role_assign($readdraft, $u3->id, $syscontext->id);
        role_assign($readdraft, $u4->id, $u2context->id);
        role_assign($readowndraft, $u5->id, $syscontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($u1);
        $this->assertFalse(plan::can_read_user_draft($u1->id));
        $this->assertFalse(plan::can_read_user_draft($u2->id));
        $this->assertFalse(plan::can_read_user_draft($u3->id));
        $this->assertFalse(plan::can_read_user_draft($u4->id));
        $this->assertFalse(plan::can_read_user_draft($u5->id));

        $this->setUser($u2);
        $this->assertFalse(plan::can_read_user_draft($u1->id));
        $this->assertFalse(plan::can_read_user_draft($u2->id));
        $this->assertFalse(plan::can_read_user_draft($u3->id));
        $this->assertFalse(plan::can_read_user_draft($u4->id));
        $this->assertFalse(plan::can_read_user_draft($u5->id));

        $this->setUser($u3);
        $this->assertTrue(plan::can_read_user_draft($u1->id));
        $this->assertTrue(plan::can_read_user_draft($u2->id));
        $this->assertTrue(plan::can_read_user_draft($u3->id));
        $this->assertTrue(plan::can_read_user_draft($u4->id));
        $this->assertTrue(plan::can_read_user_draft($u5->id));

        $this->setUser($u4);
        $this->assertFalse(plan::can_read_user_draft($u1->id));
        $this->assertTrue(plan::can_read_user_draft($u2->id));
        $this->assertFalse(plan::can_read_user_draft($u3->id));
        $this->assertFalse(plan::can_read_user_draft($u4->id));
        $this->assertFalse(plan::can_read_user_draft($u5->id));

        $this->setUser($u5);
        $this->assertFalse(plan::can_read_user_draft($u1->id));
        $this->assertFalse(plan::can_read_user_draft($u2->id));
        $this->assertFalse(plan::can_read_user_draft($u3->id));
        $this->assertFalse(plan::can_read_user_draft($u4->id));
        $this->assertTrue(plan::can_read_user_draft($u5->id));
    }

    public function test_validate_duedate() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();

        $record = array('userid' => $user->id,
                        'status' => plan::STATUS_DRAFT,
                        'duedate' => time() - 8000);

        // Ignore duedate validation on create/update draft plan.
        $plan = $lpg->create_plan($record);
        $this->assertInstanceOf(plan::class, $plan);

        // Passing from draft to active.
        $plan->set('status', plan::STATUS_ACTIVE);

        // Draft to active with duedate in the past.
        $expected = array(
            'duedate' => new \lang_string('errorcannotsetduedateinthepast', 'core_competency'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Draft to active: past date => past date(fail).
        $plan->set('duedate', time() - 100);
        $expected = array(
            'duedate' => new \lang_string('errorcannotsetduedateinthepast', 'core_competency'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Draft to active: past date => too soon (fail).
        $plan->set('duedate', time() + 100);
        $expected = array(
            'duedate' => new \lang_string('errorcannotsetduedatetoosoon', 'core_competency'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Draft to active: past date => future date (pass).
        $plan->set('duedate', time() + plan::DUEDATE_THRESHOLD + 10);
        $this->assertEquals(true, $plan->validate());

        // Draft to active: past date => unset date (pass).
        $plan->set('duedate', 0);
        $this->assertEquals(true, $plan->validate());

        // Updating active plan.
        $plan->update();

        // Active to active: past => same past (pass).
        $record = $plan->to_record();
        $record->duedate = 1;
        $DB->update_record(plan::TABLE, $record);
        $plan->read();
        $plan->set('description', uniqid()); // Force revalidation.
        $this->assertTrue($plan->is_valid());

        // Active to active: past => unset (pass).
        $plan->set('duedate', 0);
        $this->assertTrue($plan->is_valid());
        $plan->update();

        // Active to active: unset => unset (pass).
        $plan->set('description', uniqid()); // Force revalidation.
        $this->assertTrue($plan->is_valid());

        // Active to active: unset date => past date(fail).
        $plan->set('duedate', time() - 100);
        $expected = array(
            'duedate' => new \lang_string('errorcannotsetduedateinthepast', 'core_competency'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Active to active: unset date => too soon (fail).
        $plan->set('duedate', time() + 100);
        $expected = array(
            'duedate' => new \lang_string('errorcannotsetduedatetoosoon', 'core_competency'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Active to active: unset date => future date (pass).
        $plan->set('duedate', time() + plan::DUEDATE_THRESHOLD + 10);
        $this->assertEquals(true, $plan->validate());

        // Updating active plan with future date.
        $plan->update();

        // Active to active: future => same future (pass).
        $plan->set('description', uniqid()); // Force revalidation.
        $this->assertTrue($plan->is_valid());

        // Active to active: future date => unset date (pass).
        $plan->set('duedate', 0);
        $this->assertEquals(true, $plan->validate());

        // Active to active: future date => past date(fail).
        $plan->set('duedate', time() - 100);
        $expected = array(
            'duedate' => new \lang_string('errorcannotsetduedateinthepast', 'core_competency'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Active to active: future date => too soon (fail).
        $plan->set('duedate', time() + 100);
        $expected = array(
            'duedate' => new \lang_string('errorcannotsetduedatetoosoon', 'core_competency'),
        );
        $this->assertEquals($expected, $plan->validate());

        // Active to active: future date => future date (pass).
        $plan->set('duedate', time() + plan::DUEDATE_THRESHOLD + 10);
        $this->assertEquals(true, $plan->validate());

        // Completing plan: with due date in the past.
        $record = $plan->to_record();
        $record->status = plan::STATUS_ACTIVE;
        $record->duedate = time() - 200;
        $DB->update_record(plan::TABLE, $record);

        $success = api::complete_plan($plan->get('id'));
        $this->assertTrue($success);

        // Completing plan: with due date too soon (pass).
        $record = $plan->to_record();
        $record->status = plan::STATUS_ACTIVE;
        $record->duedate = time() + 200;
        $DB->update_record(plan::TABLE, $record);

        $success = api::complete_plan($plan->get('id'));
        $this->assertTrue($success);

        // Completing plan: with due date in the future (pass).
        $record = $plan->to_record();
        $record->status = plan::STATUS_ACTIVE;
        $record->duedate = time() + plan::DUEDATE_THRESHOLD + 10;
        $DB->update_record(plan::TABLE, $record);

        $success = api::complete_plan($plan->get('id'));
        $this->assertTrue($success);

        // Completing plan: with due date unset (pass).
        $record = $plan->to_record();
        $record->status = plan::STATUS_ACTIVE;
        $record->duedate = 0;
        $DB->update_record(plan::TABLE, $record);

        $success = api::complete_plan($plan->get('id'));
        $this->assertTrue($success);

        // Reopening plan: with due date in the past => duedate unset.
        $record = $plan->to_record();
        $record->status = plan::STATUS_COMPLETE;
        $record->duedate = time() - 200;
        $DB->update_record(plan::TABLE, $record);

        $success = api::reopen_plan($plan->get('id'));
        $this->assertTrue($success);
        $plan->read();
        $this->assertEquals(0, $plan->get('duedate'));

        // Reopening plan: with due date too soon => duedate unset.
        $record = $plan->to_record();
        $record->status = plan::STATUS_COMPLETE;
        $record->duedate = time() + 100;
        $DB->update_record(plan::TABLE, $record);

        $success = api::reopen_plan($plan->get('id'));
        $this->assertTrue($success);
        $plan->read();
        $this->assertEquals(0, $plan->get('duedate'));

        // Reopening plan: with due date in the future => duedate unchanged.
        $record = $plan->to_record();
        $record->status = plan::STATUS_COMPLETE;
        $duedate = time() + plan::DUEDATE_THRESHOLD + 10;
        $record->duedate = $duedate;
        $DB->update_record(plan::TABLE, $record);

        $success = api::reopen_plan($plan->get('id'));
        $this->assertTrue($success);
        $plan->read();

        // Check that the due date has not changed.
        $this->assertNotEquals(0, $plan->get('duedate'));
        $this->assertEquals($duedate, $plan->get('duedate'));
    }

    public function test_get_by_user_and_competency() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        $tpl1 = $lpg->create_template();
        $lpg->create_template_competency(array('competencyid' => $c1->get('id'), 'templateid' => $tpl1->get('id')));

        $p1 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p1->get('id'), 'competencyid' => $c1->get('id')));
        $p2 = $lpg->create_plan(array('userid' => $u2->id));
        $lpg->create_plan_competency(array('planid' => $p2->get('id'), 'competencyid' => $c1->get('id')));
        $p3 = $lpg->create_plan(array('userid' => $u3->id, 'templateid' => $tpl1->get('id')));
        $p4 = $lpg->create_plan(array('userid' => $u4->id, 'templateid' => $tpl1->get('id')));
        api::complete_plan($p2);
        api::complete_plan($p4);

        // Finding a plan, not completed.
        $plans = plan::get_by_user_and_competency($u1->id, $c1->get('id'));
        $this->assertCount(1, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p1->get('id'), $plan->get('id'));
        $this->assertNotEquals(plan::STATUS_COMPLETE, $plan->get('status'));

        // Finding a completed plan.
        $plans = plan::get_by_user_and_competency($u2->id, $c1->get('id'));
        $this->assertCount(1, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p2->get('id'), $plan->get('id'));
        $this->assertEquals(plan::STATUS_COMPLETE, $plan->get('status'));

        // Finding a plan based on a template, not completed.
        $plans = plan::get_by_user_and_competency($u3->id, $c1->get('id'));
        $this->assertCount(1, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p3->get('id'), $plan->get('id'));
        $this->assertTrue($plan->is_based_on_template());
        $this->assertNotEquals(plan::STATUS_COMPLETE, $plan->get('status'));

        // Finding a plan based on a template.
        $plans = plan::get_by_user_and_competency($u4->id, $c1->get('id'));
        $this->assertCount(1, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p4->get('id'), $plan->get('id'));
        $this->assertTrue($plan->is_based_on_template());
        $this->assertEquals(plan::STATUS_COMPLETE, $plan->get('status'));

        // Finding more than one plan, no template.
        $p5 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p5->get('id'), 'competencyid' => $c1->get('id')));
        $plans = plan::get_by_user_and_competency($u1->id, $c1->get('id'));
        $this->assertCount(2, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p1->get('id'), $plan->get('id'));
        $plan = array_shift($plans);
        $this->assertEquals($p5->get('id'), $plan->get('id'));

        // Finding more than one plan, with template.
        $p6 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get('id')));
        $plans = plan::get_by_user_and_competency($u1->id, $c1->get('id'));
        $this->assertCount(3, $plans);
        $plan = array_shift($plans);
        $this->assertEquals($p1->get('id'), $plan->get('id'));
        $plan = array_shift($plans);
        $this->assertEquals($p5->get('id'), $plan->get('id'));
        $plan = array_shift($plans);
        $this->assertEquals($p6->get('id'), $plan->get('id'));

        // Finding no plans.
        $plans = plan::get_by_user_and_competency($u1->id, $c2->get('id'));
        $this->assertCount(0, $plans);
    }

    public function test_get_competency() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));

        $tpl1 = $lpg->create_template();
        $p1 = $lpg->create_plan(array('userid' => $u1->id));
        $p2 = $lpg->create_plan(array('userid' => $u2->id));
        $p3 = $lpg->create_plan(array('userid' => $u3->id, 'templateid' => $tpl1->get('id')));
        $p4 = $lpg->create_plan(array('userid' => $u4->id, 'templateid' => $tpl1->get('id')));

        $lpg->create_plan_competency(array('planid' => $p1->get('id'), 'competencyid' => $c1->get('id')));
        $lpg->create_plan_competency(array('planid' => $p2->get('id'), 'competencyid' => $c2->get('id')));
        $lpg->create_template_competency(array('templateid' => $tpl1->get('id'), 'competencyid' => $c3->get('id')));
        $lpg->create_template_competency(array('templateid' => $tpl1->get('id'), 'competencyid' => $c4->get('id')));

        // Completing the plans and removing a competency from the template.
        api::complete_plan($p2);
        api::complete_plan($p4);
        api::remove_competency_from_template($tpl1->get('id'), $c4->get('id'));

        // We can find all competencies.
        $this->assertEquals($c1->to_record(), $p1->get_competency($c1->get('id'))->to_record());
        $this->assertEquals($c2->to_record(), $p2->get_competency($c2->get('id'))->to_record());
        $this->assertEquals($c3->to_record(), $p3->get_competency($c3->get('id'))->to_record());
        $this->assertEquals($c4->to_record(), $p4->get_competency($c4->get('id'))->to_record());

        // Getting the competency 4 from the non-completed plan based on a template p4, will throw an exception.
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('The competency does not belong to this template:');
        $p3->get_competency($c4->get('id'));
    }
}
