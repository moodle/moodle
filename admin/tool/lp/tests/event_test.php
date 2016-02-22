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
 * Event tests.
 *
 * @package    tool_lp
 * @copyright  2016 Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use tool_lp\api;

/**
 * Event tests.
 *
 * @package    tool_lp
 * @copyright  2016 Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_event_testcase extends advanced_testcase {

    /**
     * Test the competency framework created event.
     *
     */
    public function test_competency_framework_created() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        // Use DataGenerator to have a record framework with the right format.
        $record = $lpg->create_framework()->to_record();
        $record->id = 0;
        $record->shortname = "New shortname";
        $record->idnumber = "New idnumber";

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $framework = api::create_framework((object) $record);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_framework_created', $event);
        $this->assertEquals($framework->get_id(), $event->objectid);
        $this->assertEquals($framework->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency framework deleted event.
     *
     */
    public function test_competency_framework_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::delete_framework($framework->get_id());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_framework_deleted', $event);
        $this->assertEquals($framework->get_id(), $event->objectid);
        $this->assertEquals($framework->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency framework updated event.
     *
     */
    public function test_competency_framework_updated() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $framework->set_shortname('Shortname modified');
        api::update_framework($framework->to_record());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_framework_updated', $event);
        $this->assertEquals($framework->get_id(), $event->objectid);
        $this->assertEquals($framework->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency framework viewed event.
     *
     */
    public function test_competency_framework_viewed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::competency_framework_viewed($framework);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_framework_viewed', $event);
        $this->assertEquals($framework->get_id(), $event->objectid);
        $this->assertEquals($framework->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency viewed event.
     *
     */
    public function test_competency_viewed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $framework = $lpg->create_framework();
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::competency_viewed($competency);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_viewed', $event);
        $this->assertEquals($competency->get_id(), $event->objectid);
        $this->assertEquals($competency->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the template viewed event.
     *
     */
    public function test_template_viewed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $template = $lpg->create_template();
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::template_viewed($template);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\template_viewed', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals($template->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the template created event.
     *
     */
    public function test_template_created() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        // Use DataGenerator to have a template record with the right format.
        $record = $lpg->create_template()->to_record();
        $record->id = 0;
        $record->shortname = "New shortname";

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $template = api::create_template((object) $record);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\tool_lp\event\template_created', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals($template->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the template deleted event.
     *
     */
    public function test_template_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $template = $lpg->create_template();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::delete_template($template->get_id());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\template_deleted', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals($template->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the template updated event.
     *
     */
    public function test_template_updated() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $template = $lpg->create_template();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $template->set_shortname('Shortname modified');
        api::update_template($template->to_record());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\template_updated', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals($template->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency updated event.
     *
     */
    public function test_competency_updated() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $f1 = $lpg->create_framework();
        $competency = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c12 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));
        $c13 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $competency->set_shortname('Shortname modified');
        api::update_competency($competency->to_record());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_updated', $event);
        $this->assertEquals($competency->get_id(), $event->objectid);
        $this->assertEquals($competency->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency created event.
     *
     */
    public function test_competency_created() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $record = $c1->to_record();
        $record->id = 0;
        $record->idnumber = 'comp idnumber';

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Create competency should trigger a created event.
        $competency = api::create_competency($record);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\tool_lp\event\competency_created', $event);
        $this->assertEquals($competency->get_id(), $event->objectid);
        $this->assertEquals($competency->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency created event by duplicate framework.
     *
     */
    public function test_competency_created_by_duplicateframework() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c12 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Create framework should trigger a created event for competencies.
        api::duplicate_framework($f1->get_id());

        // Get our event event.
        $events = $sink->get_events();
        $this->assertEquals(4, count($events));

        $event = array_shift($events);
        $this->assertInstanceOf('\tool_lp\event\competency_created', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\tool_lp\event\competency_created', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\tool_lp\event\competency_created', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\tool_lp\event\competency_framework_created', $event);
    }

    /**
     * Test the competency deleted event.
     *
     */
    public function test_competency_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c1id = $c1->get_id();
        $contextid = $c1->get_context()->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Delete competency should trigger a deleted event.
        api::delete_competency($c1id);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\tool_lp\event\competency_deleted', $event);
        $this->assertEquals($c1id, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency deleted event by delete framework.
     *
     */
    public function test_competency_deleted_by_deleteframework() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c12 = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id(), 'parentid' => $c1->get_id()));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Delete framework should trigger a deleted event for competencies.
        api::delete_framework($f1->get_id());

        // Get our event event.
        $events = $sink->get_events();
        $this->assertEquals(4, count($events));

        $event = array_shift($events);
        $this->assertInstanceOf('\tool_lp\event\competency_framework_deleted', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\tool_lp\event\competency_deleted', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\tool_lp\event\competency_deleted', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\tool_lp\event\competency_deleted', $event);
    }

    /**
     * Test the plan created event.
     *
     */
    public function test_plan_created() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user = $dg->create_user();
        $plan = array (
            'name' => 'plan',
            'userid' => $user->id
        );
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $plan = api::create_plan((object)$plan);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\plan_created', $event);
        $this->assertEquals($plan->get_id(), $event->objectid);
        $this->assertEquals($plan->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan created event using template_cohort.
     *
     */
    public function test_plan_created_using_templatecohort() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user1 = $dg->create_user();
        $user2 = $dg->create_user();
        $c1 = $dg->create_cohort();
        // Add 2 users to the cohort.
        cohort_add_member($c1->id, $user1->id);
        cohort_add_member($c1->id, $user2->id);
        $t1 = $lpg->create_template();
        $tc = $lpg->create_template_cohort(array(
            'templateid' => $t1->get_id(),
            'cohortid' => $c1->id
        ));
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::create_plans_from_template_cohort($t1->get_id(), $c1->id);
        // Get our event event.
        $plans = tool_lp\plan::get_records(array('templateid' => $t1->get_id()), 'id');
        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $this->assertCount(2, $plans);
        $event = $events[0];
        $plan = $plans[0];
        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\plan_created', $event);
        $this->assertEquals($plan->get_id(), $event->objectid);
        $this->assertEquals($plan->get_context()->id, $event->contextid);
        $event = $events[1];
        $plan = $plans[1];
        $this->assertInstanceOf('\tool_lp\event\plan_created', $event);
        $this->assertEquals($plan->get_id(), $event->objectid);
        $this->assertEquals($plan->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan updated event.
     *
     */
    public function test_plan_updated() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        $record = $plan->to_record();
        $record->name = 'Plan updated';
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $plan = api::update_plan($record);
        $this->assertEquals('Plan updated', $plan->get_name());
        // Complete plan.
        api::complete_plan($plan);
        // Reopen plan.
        api::reopen_plan($plan);
        // Unapprove plan.
        api::unapprove_plan($plan);
        // Approve plan.
        api::approve_plan($plan);
        // Get our event event.
        $events = $sink->get_events();
        $this->assertCount(5, $events);
        // Check that the event data is valid.
        foreach ($events as $event) {
            $this->assertInstanceOf('\tool_lp\event\plan_updated', $event);
            $this->assertEquals($plan->get_id(), $event->objectid);
            $this->assertEquals($plan->get_context()->id, $event->contextid);
            $this->assertEventContextNotUsed($event);
        }
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan updated event by unlink.
     *
     */
    public function test_plan_updated_by_unlink() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user1 = $dg->create_user();
        $t1 = $lpg->create_template();
        $plan = $lpg->create_plan(array('userid' => $user1->id, 'templateid' => $t1->get_id()));
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::unlink_plan_from_template($plan);
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\plan_updated', $event);
        $this->assertEquals($plan->get_id(), $event->objectid);
        $this->assertEquals($plan->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan updated event by review.
     *
     */
    public function test_plan_updated_by_review() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::plan_request_review($plan);
        $this->assertTrue($result);
        $result = api::plan_start_review($plan);
        $this->assertTrue($result);
        $result = api::plan_stop_review($plan);
        $this->assertTrue($result);
        $result = api::plan_request_review($plan);
        $this->assertTrue($result);
        $result = api::plan_cancel_review_request($plan);
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $this->assertCount(5, $events);
        // Check that the event data is valid.
        foreach ($events as $event) {
            $this->assertInstanceOf('\tool_lp\event\plan_updated', $event);
            $this->assertEquals($plan->get_id(), $event->objectid);
            $this->assertEquals($plan->get_context()->id, $event->contextid);
            $this->assertEventContextNotUsed($event);
        }
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan deleted event.
     *
     */
    public function test_plan_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        $planid = $plan->get_id();
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::delete_plan($plan->get_id());
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\tool_lp\event\plan_deleted', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan viewed event.
     *
     */
    public function test_plan_viewed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::plan_viewed($plan);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\plan_viewed', $event);
        $this->assertEquals($plan->get_id(), $event->objectid);
        $this->assertEquals($plan->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }
}
