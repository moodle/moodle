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

namespace core_competency\event;

use core_competency\api;
use core_competency\plan;
use core_competency\url;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/comment/lib.php');

/**
 * Event tests.
 *
 * @package    core_competency
 * @copyright  2016 Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends \advanced_testcase {

    /**
     * Test the competency framework created event.
     *
     */
    public function test_competency_framework_created() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

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
        $this->assertInstanceOf('\core\event\competency_framework_created', $event);
        $this->assertEquals($framework->get('id'), $event->objectid);
        $this->assertEquals($framework->get('contextid'), $event->contextid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::delete_framework($framework->get('id'));

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_framework_deleted', $event);
        $this->assertEquals($framework->get('id'), $event->objectid);
        $this->assertEquals($framework->get('contextid'), $event->contextid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $framework->set('shortname', 'Shortname modified');
        api::update_framework($framework->to_record());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_framework_updated', $event);
        $this->assertEquals($framework->get('id'), $event->objectid);
        $this->assertEquals($framework->get('contextid'), $event->contextid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::competency_framework_viewed($framework);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_framework_viewed', $event);
        $this->assertEquals($framework->get('id'), $event->objectid);
        $this->assertEquals($framework->get('contextid'), $event->contextid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $competency = $lpg->create_competency(array('competencyframeworkid' => $framework->get('id')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::competency_viewed($competency);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_viewed', $event);
        $this->assertEquals($competency->get('id'), $event->objectid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $template = $lpg->create_template();
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::template_viewed($template);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_template_viewed', $event);
        $this->assertEquals($template->get('id'), $event->objectid);
        $this->assertEquals($template->get('contextid'), $event->contextid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

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

        $this->assertInstanceOf('\core\event\competency_template_created', $event);
        $this->assertEquals($template->get('id'), $event->objectid);
        $this->assertEquals($template->get('contextid'), $event->contextid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $template = $lpg->create_template();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::delete_template($template->get('id'));

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_template_deleted', $event);
        $this->assertEquals($template->get('id'), $event->objectid);
        $this->assertEquals($template->get('contextid'), $event->contextid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $template = $lpg->create_template();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $template->set('shortname', 'Shortname modified');
        api::update_template($template->to_record());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_template_updated', $event);
        $this->assertEquals($template->get('id'), $event->objectid);
        $this->assertEquals($template->get('contextid'), $event->contextid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();
        $competency = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c12 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));
        $c13 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $competency->set('shortname', 'Shortname modified');
        api::update_competency($competency->to_record());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_updated', $event);
        $this->assertEquals($competency->get('id'), $event->objectid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
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

        $this->assertInstanceOf('\core\event\competency_created', $event);
        $this->assertEquals($competency->get('id'), $event->objectid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c12 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Create framework should trigger a created event for competencies.
        api::duplicate_framework($f1->get('id'));

        // Get our event event.
        $events = $sink->get_events();
        $this->assertEquals(4, count($events));

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\competency_created', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\competency_created', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\competency_created', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\competency_framework_created', $event);
    }

    /**
     * Test the competency deleted event.
     *
     */
    public function test_competency_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c1id = $c1->get('id');
        $contextid = $c1->get_context()->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Delete competency should trigger a deleted event.
        api::delete_competency($c1id);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\competency_deleted', $event);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $f1 = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id')));
        $c12 = $lpg->create_competency(array('competencyframeworkid' => $f1->get('id'), 'parentid' => $c1->get('id')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Delete framework should trigger a deleted event for competencies.
        api::delete_framework($f1->get('id'));

        // Get our event event.
        $events = $sink->get_events();
        $this->assertEquals(4, count($events));

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\competency_framework_deleted', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\competency_deleted', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\competency_deleted', $event);

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\competency_deleted', $event);
    }

    /**
     * Test the plan created event.
     *
     */
    public function test_plan_created() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
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
        $this->assertInstanceOf('\core\event\competency_plan_created', $event);
        $this->assertEquals($plan->get('id'), $event->objectid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $user2 = $dg->create_user();
        $c1 = $dg->create_cohort();
        // Add 2 users to the cohort.
        cohort_add_member($c1->id, $user1->id);
        cohort_add_member($c1->id, $user2->id);
        $t1 = $lpg->create_template();
        $tc = $lpg->create_template_cohort(array(
            'templateid' => $t1->get('id'),
            'cohortid' => $c1->id
        ));
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::create_plans_from_template_cohort($t1->get('id'), $c1->id);
        // Get our event event.
        $plans = plan::get_records(array('templateid' => $t1->get('id')), 'id');
        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $this->assertCount(2, $plans);
        $event = $events[0];
        $plan = $plans[0];
        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_plan_created', $event);
        $this->assertEquals($plan->get('id'), $event->objectid);
        $this->assertEquals($plan->get_context()->id, $event->contextid);
        $event = $events[1];
        $plan = $plans[1];
        $this->assertInstanceOf('\core\event\competency_plan_created', $event);
        $this->assertEquals($plan->get('id'), $event->objectid);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        $record = $plan->to_record();
        $record->name = 'Plan updated';
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $plan = api::update_plan($record);
        $this->assertEquals('Plan updated', $plan->get('name'));

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_updated', $event);
        $this->assertEquals($plan->get('id'), $event->objectid);
        $this->assertEquals($plan->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::delete_plan($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_deleted', $event);
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
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::plan_viewed($plan);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_plan_viewed', $event);
        $this->assertEquals($plan->get('id'), $event->objectid);
        $this->assertEquals($plan->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the evidence of prior learning created event.
     *
     */
    public function test_user_evidence_created() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $user = $dg->create_user();
        // Use DataGenerator to have a user_evidence record with the right format.
        $record = $userevidence = $lpg->create_user_evidence(array('userid' => $user->id))->to_record();
        $record->id = 0;
        $record->name = "New name";

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $userevidence = api::create_user_evidence((object) $record);

         // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\competency_user_evidence_created', $event);
        $this->assertEquals($userevidence->get('id'), $event->objectid);
        $this->assertEquals($userevidence->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the evidence of prior learning  deleted event.
     *
     */
    public function test_user_evidence_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $user = $dg->create_user();
        $userevidence = $lpg->create_user_evidence(array('userid' => $user->id));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::delete_user_evidence($userevidence->get('id'));

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_user_evidence_deleted', $event);
        $this->assertEquals($userevidence->get('id'), $event->objectid);
        $this->assertEquals($userevidence->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the evidence of prior learning  updated event.
     *
     */
    public function test_user_evidence_updated() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $user = $dg->create_user();
        $userevidence = $lpg->create_user_evidence(array('userid' => $user->id));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $userevidence->set('name', 'Name modified');
        api::update_user_evidence($userevidence->to_record());

         // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_user_evidence_updated', $event);
        $this->assertEquals($userevidence->get('id'), $event->objectid);
        $this->assertEquals($userevidence->get_context()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the user competency viewed event in plan.
     *
     */
    public function test_user_competency_viewed_in_plan() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $fr->get('id')));
        $pc = $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c->get('id')));
        $uc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c->get('id')));

        // Can not log the event for user competency using completed plan.
        api::complete_plan($plan);

        try {
            api::user_competency_viewed_in_plan($uc, $plan->get('id'));
            $this->fail('To log the user competency in completed plan '
                    . 'use user_competency_plan_viewed method.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression('/To log the user competency in completed plan '
                    . 'use user_competency_plan_viewed method./', $e->getMessage());
        }

        api::reopen_plan($plan);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::user_competency_viewed_in_plan($uc, $plan->get('id'));

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_user_competency_viewed_in_plan', $event);
        $this->assertEquals($uc->get('id'), $event->objectid);
        $this->assertEquals($uc->get_context()->id, $event->contextid);
        $this->assertEquals($uc->get('userid'), $event->relateduserid);
        $this->assertEquals($plan->get('id'), $event->other['planid']);
        $this->assertEquals($c->get('id'), $event->other['competencyid']);

        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();

        // Test validation.
        $params = array (
            'objectid' => $uc->get('id'),
            'contextid' => $uc->get_context()->id,
            'other' => null
        );

        // Other value null.
        try {
            \core\event\competency_user_competency_viewed_in_plan::create($params)->trigger();
            $this->fail('The \'competencyid\' and \'planid\' values must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'competencyid' and 'planid' values must be set./", $e->getMessage());
        }

        $params['other']['anythingelse'] = '';
        // Missing competencyid.
        try {
            \core\event\competency_user_competency_viewed_in_plan::create($params)->trigger();
            $this->fail('The \'competencyid\' value must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'competencyid' value must be set./", $e->getMessage());
        }

        $params['other']['competencyid'] = $c->get('id');
        // Missing planid.
        try {
            \core\event\competency_user_competency_viewed_in_plan::create($params)->trigger();
            $this->fail('The \'planid\' value must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'planid' value must be set./", $e->getMessage());
        }
    }

    /**
     * Test the user competency viewed event in course.
     *
     */
    public function test_user_competency_viewed_in_course() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();
        $course = $dg->create_course();
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $fr->get('id')));
        $pc = $lpg->create_course_competency(array('courseid' => $course->id, 'competencyid' => $c->get('id')));
        $params = array('userid' => $user->id, 'competencyid' => $c->get('id'), 'courseid' => $course->id);
        $ucc = $lpg->create_user_competency_course($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::user_competency_viewed_in_course($ucc);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_user_competency_viewed_in_course', $event);
        $this->assertEquals($ucc->get('id'), $event->objectid);
        $this->assertEquals(\context_course::instance($course->id)->id, $event->contextid);
        $this->assertEquals($ucc->get('userid'), $event->relateduserid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($c->get('id'), $event->other['competencyid']);

        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();

        // Test validation.
        $params = array (
            'objectid' => $ucc->get('id'),
            'contextid' => $ucc->get_context()->id,
            'other' => null
        );

        // Missing courseid.
        try {
            \core\event\competency_user_competency_viewed_in_course::create($params)->trigger();
            $this->fail('The \'courseid\' value must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'courseid' value must be set./", $e->getMessage());
        }

        $params['contextid'] = \context_course::instance($course->id)->id;
        $params['courseid'] = $course->id;
        // Missing competencyid.
        try {
            \core\event\competency_user_competency_viewed_in_course::create($params)->trigger();
            $this->fail('The \'competencyid\' value must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'competencyid' value must be set./", $e->getMessage());
        }
    }

    /**
     * Test the user competency plan viewed event.
     *
     */
    public function test_user_competency_plan_viewed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $fr->get('id')));
        $ucp = $lpg->create_user_competency_plan(array(
            'userid' => $user->id,
            'competencyid' => $c->get('id'),
            'planid' => $plan->get('id')
        ));

        // Can not log the event for user competency using non completed plan.
        try {
            api::user_competency_plan_viewed($ucp);
            $this->fail('To log the user competency in non-completed plan '
                    . 'use user_competency_viewed_in_plan method.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression('/To log the user competency in non-completed plan '
                    . 'use user_competency_viewed_in_plan method./', $e->getMessage());
        }

        // Complete the plan.
        api::complete_plan($plan);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::user_competency_plan_viewed($ucp);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_user_competency_plan_viewed', $event);
        $this->assertEquals($ucp->get('id'), $event->objectid);
        $this->assertEquals($ucp->get_context()->id, $event->contextid);
        $this->assertEquals($ucp->get('userid'), $event->relateduserid);
        $this->assertEquals($plan->get('id'), $event->other['planid']);
        $this->assertEquals($c->get('id'), $event->other['competencyid']);

        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();

        // Test validation.
        $params = array (
            'objectid' => $ucp->get('id'),
            'contextid' => $ucp->get_context()->id,
            'other' => null
        );

        // Other value null.
        try {
            \core\event\competency_user_competency_plan_viewed::create($params)->trigger();
            $this->fail('The \'competencyid\' and \'planid\' values must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'competencyid' and 'planid' values must be set./", $e->getMessage());
        }

        $params['other']['anythingelse'] = '';
        // Missing competencyid.
        try {
            \core\event\competency_user_competency_plan_viewed::create($params)->trigger();
            $this->fail('The \'competencyid\' value must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'competencyid' value must be set./", $e->getMessage());
        }

        $params['other']['competencyid'] = $c->get('id');
        // Missing planid.
        try {
            \core\event\competency_user_competency_plan_viewed::create($params)->trigger();
            $this->fail('The \'planid\' value must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'planid' value must be set./", $e->getMessage());
        }
    }

    /**
     * Test the user competency viewed event.
     *
     */
    public function test_user_competency_viewed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $fr->get('id')));
        $uc = $lpg->create_user_competency(array(
            'userid' => $user->id,
            'competencyid' => $c->get('id')
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::user_competency_viewed($uc);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_user_competency_viewed', $event);
        $this->assertEquals($uc->get('id'), $event->objectid);
        $this->assertEquals($uc->get_context()->id, $event->contextid);
        $this->assertEquals($uc->get('userid'), $event->relateduserid);
        $this->assertEquals($c->get('id'), $event->other['competencyid']);

        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();

        // Test validation.
        $params = array (
            'objectid' => $uc->get('id'),
            'contextid' => $uc->get_context()->id
        );

        // Missing competencyid.
        try {
            \core\event\competency_user_competency_viewed::create($params)->trigger();
            $this->fail('The \'competencyid\' value must be set.');
        } catch (\coding_exception $e) {
            $this->assertMatchesRegularExpression("/The 'competencyid' value must be set./", $e->getMessage());
        }
    }

    /**
     * Test the plan approved event.
     *
     */
    public function test_plan_approved() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::approve_plan($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_approved', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan unapproved event.
     *
     */
    public function test_plan_unapproved() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id, 'status' => \core_competency\plan::STATUS_ACTIVE));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::unapprove_plan($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_unapproved', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan reopened event.
     *
     */
    public function test_plan_reopened() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id, 'status' => \core_competency\plan::STATUS_COMPLETE));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::reopen_plan($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_reopened', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan completed event.
     *
     */
    public function test_plan_completed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id, 'status' => \core_competency\plan::STATUS_ACTIVE));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::complete_plan($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_completed', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan unlinked event.
     *
     */
    public function test_plan_unlinked() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $template = $lpg->create_template();
        $plan = $lpg->create_plan(array(
            'userid' => $user1->id,
            'status' => \core_competency\plan::STATUS_ACTIVE,
            'templateid' => $template->get('id')
        ));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::unlink_plan_from_template($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_unlinked', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan review requested event.
     *
     */
    public function test_plan_review_requested() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::plan_request_review($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_review_requested', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan review request cancelled event.
     *
     */
    public function test_plan_review_request_cancelled() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id, 'status' => \core_competency\plan::STATUS_WAITING_FOR_REVIEW));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::plan_cancel_review_request($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_review_request_cancelled', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan review started event.
     *
     */
    public function test_plan_review_started() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id, 'status' => \core_competency\plan::STATUS_WAITING_FOR_REVIEW));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::plan_start_review($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_review_started', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the plan review stopped event.
     *
     */
    public function test_plan_review_stopped() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id, 'status' => \core_competency\plan::STATUS_IN_REVIEW));
        $planid = $plan->get('id');
        $contextid = $plan->get_context()->id;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = api::plan_stop_review($plan->get('id'));
        $this->assertTrue($result);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\competency_plan_review_stopped', $event);
        $this->assertEquals($planid, $event->objectid);
        $this->assertEquals($contextid, $event->contextid);
        $this->assertEquals($plan->get('userid'), $event->relateduserid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test plan comment created event.
     */
    public function test_plan_comment_created() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();
        $this->setUser($user);
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $context = \context_user::instance($user->id);

        $cmt = new \stdClass();
        $cmt->context = $context;
        $cmt->area = 'plan';
        $cmt->itemid = $plan->get('id');
        $cmt->component = 'competency';
        $cmt->showcount = 1;
        $manager = new \comment($cmt);
        $manager->set_post_permission(true);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $manager->add("New comment for plan");
        $events = $sink->get_events();
        // Add comment will trigger 2 other events message_viewed and message_sent.
        $this->assertCount(1, $events);
        $event = array_pop($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\core\event\competency_comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($plan->get('id'), $event->other['itemid']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test plan comment deleted event.
     */
    public function test_plan_comment_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user1 = $dg->create_user();
        $plan = $lpg->create_plan(array('userid' => $user1->id));

        $context = \context_user::instance($user1->id);

        $cmt = new \stdClass();
        $cmt->context = $context;
        $cmt->area = 'plan';
        $cmt->itemid = $plan->get('id');
        $cmt->component = 'competency';
        $manager = new \comment($cmt);
        $newcomment = $manager->add("Comment to be deleted");

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $manager->delete($newcomment->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\core\event\competency_comment_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($plan->get('id'), $event->other['itemid']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test evidence_created event.
     */
    public function test_evidence_created() {
        global $USER;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $syscontext = \context_system::instance();

        // Create a student.
        $student = $dg->create_user();

        // Create a competency for the course.
        $lpg = $dg->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $comp = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        // Add evidence.
        $recommend = false;
        $evidence = api::add_evidence($student->id, $comp, $syscontext, \core_competency\evidence::ACTION_OVERRIDE,
            'commentincontext', 'core', null, $recommend, null, 1);

        // Get event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_evidence_created', $event);
        $this->assertEquals($evidence->get('contextid'), $event->contextid);
        $this->assertEquals($evidence->get('id'), $event->objectid);
        $this->assertEquals($evidence->get('actionuserid'), $event->userid);
        $this->assertEquals($student->id, $event->relateduserid);
        $this->assertEquals($evidence->get('usercompetencyid'), $event->other['usercompetencyid']);
        $this->assertEquals($comp->get('id'), $event->other['competencyid']);
        $this->assertEquals($evidence->get('action'), $event->other['action']);
        $this->assertEquals($recommend, $event->other['recommend']);

        // Test get_name().
        $this->assertEquals(get_string('eventevidencecreated', 'core_competency'), $event->get_name());

        // Test get_description().
        $description = "The user with id '$USER->id' created an evidence with id '{$evidence->get('id')}'.";
        $this->assertEquals($description, $event->get_description());

        // Test get_url().
        $url = url::user_competency($evidence->get('usercompetencyid'));
        $this->assertEquals($url, $event->get_url());

        // Test get_objectid_mapping().
        $this->assertEquals(\core\event\base::NOT_MAPPED, $event->get_objectid_mapping());

        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test evidence_created event by linking an invalid user competency to an evidence.
     */
    public function test_evidence_created_with_invalid_user_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $syscontext = \context_system::instance();

        // Create students.
        $student = $dg->create_user();
        $student2 = $dg->create_user();

        // Create a competency for the course.
        $lpg = $dg->get_plugin_generator('core_competency');
        $framework = $lpg->create_framework();
        $comp = $lpg->create_competency(['competencyframeworkid' => $framework->get('id')]);

        // Create a different user competency.
        $otheruc = \core_competency\user_competency::create_relation($student2->id, $comp->get('id'));
        $otheruc->create();

        // Add evidence.
        $recommend = false;
        $evidence = api::add_evidence($student->id, $comp, $syscontext, \core_competency\evidence::ACTION_OVERRIDE,
            'commentincontext', 'core', null, $recommend, null, 1);

        // We expect this to fail and throw a coding exception.
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('The user competency linked with this evidence is invalid.');
        \core\event\competency_evidence_created::create_from_evidence($evidence, $otheruc, $recommend)->trigger();
    }

    /**
     * Test creation of evidence_created event with missing data.
     *
     * These data are validated by \core_competency\evidence_created::validate_data().
     */
    public function test_evidence_created_with_missing_data() {
        $eventdata = [
            'contextid'  => 1,
            'objectid' => 1,
            'userid' => 1
        ];

        // No relateduserid.
        $errormsg = 'The \'relateduserid\' must be set.';
        try {
            \core\event\competency_evidence_created::create($eventdata)->trigger();
            $this->fail('Coding exception should have been thrown: ' . $errormsg);
        } catch (\coding_exception $e) {
            $this->assertStringContainsString($errormsg, $e->getMessage());
        }
        $eventdata['relateduserid'] = 1;

        // No other['usercompetencyid'].
        $errormsg = 'The \'usercompetencyid\' data in \'other\' must be set.';
        try {
            \core\event\competency_evidence_created::create($eventdata)->trigger();
            $this->fail('Coding exception should have been thrown: ' . $errormsg);
        } catch (\coding_exception $e) {
            $this->assertStringContainsString($errormsg, $e->getMessage());
        }
        $eventdata['other']['usercompetencyid'] = 1;

        // No other['competencyid'].
        $errormsg = 'The \'competencyid\' data in \'other\' must be set.';
        try {
            \core\event\competency_evidence_created::create($eventdata)->trigger();
            $this->fail('Coding exception should have been thrown: ' . $errormsg);
        } catch (\coding_exception $e) {
            $this->assertStringContainsString($errormsg, $e->getMessage());
        }
        $eventdata['other']['competencyid'] = 1;

        // No other['action'].
        $errormsg = 'The \'action\' data in \'other\' must be set.';
        try {
            \core\event\competency_evidence_created::create($eventdata)->trigger();
            $this->fail('Coding exception should have been thrown: ' . $errormsg);
        } catch (\coding_exception $e) {
            $this->assertStringContainsString($errormsg, $e->getMessage());
        }
        $eventdata['other']['action'] = 1;

        // No other['recommend'].
        $errormsg = 'The \'recommend\' data in \'other\' must be set.';
        try {
            \core\event\competency_evidence_created::create($eventdata)->trigger();
            $this->fail('Coding exception should have been thrown: ' . $errormsg);
        } catch (\coding_exception $e) {
            $this->assertStringContainsString($errormsg, $e->getMessage());
        }
        $eventdata['other']['recommend'] = 1;

        // Event should be triggered without any problems.
        \core\event\competency_evidence_created::create($eventdata)->trigger();
    }

    /**
     * Test the user competency grade rated event.
     *
     */
    public function test_user_competency_rated() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $scale = $dg->create_scale(array('scale' => 'A,B,C,D'));
        $scaleconfig = array(array('scaleid' => $scale->id));
        $scaleconfig[] = array('name' => 'A', 'id' => 1, 'scaledefault' => 0, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'B', 'id' => 2, 'scaledefault' => 1, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'C', 'id' => 3, 'scaledefault' => 0, 'proficient' => 1);
        $scaleconfig[] = array('name' => 'D', 'id' => 4, 'scaledefault' => 0, 'proficient' => 1);
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array(
            'competencyframeworkid' => $fr->get('id'),
            'scaleid' => $scale->id,
            'scaleconfiguration' => $scaleconfig
        ));

        $user = $dg->create_user();
        $uc = $lpg->create_user_competency(array(
            'userid' => $user->id,
            'competencyid' => $c->get('id')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::grade_competency($user->id, $c->get('id'), 2, true);

        // Get our event event.
        $events = $sink->get_events();
        // Evidence created.
        $this->assertCount(2, $events);
        $evidencecreatedevent = $events[0];
        $event = $events[1];

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_evidence_created', $evidencecreatedevent);
        $this->assertInstanceOf('\core\event\competency_user_competency_rated', $event);
        $this->assertEquals($uc->get('id'), $event->objectid);
        $this->assertEquals($uc->get_context()->id, $event->contextid);
        $this->assertEquals($uc->get('userid'), $event->relateduserid);
        $this->assertEquals(2, $event->other['grade']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the user competency grade rated in course event.
     *
     */
    public function test_user_competency_rated_in_course() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $scale = $dg->create_scale(array('scale' => 'A,B,C,D'));
        $course = $dg->create_course();
        $user = $dg->create_user();
        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);
        $scaleconfig = array(array('scaleid' => $scale->id));
        $scaleconfig[] = array('name' => 'A', 'id' => 1, 'scaledefault' => 0, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'B', 'id' => 2, 'scaledefault' => 1, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'C', 'id' => 3, 'scaledefault' => 0, 'proficient' => 1);
        $scaleconfig[] = array('name' => 'D', 'id' => 4, 'scaledefault' => 0, 'proficient' => 1);
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array(
            'competencyframeworkid' => $fr->get('id'),
            'scaleid' => $scale->id,
            'scaleconfiguration' => $scaleconfig
        ));
        // Enrol the user as students in course.
        $dg->enrol_user($user->id, $course->id, $studentrole->id);
        $lpg->create_course_competency(array(
            'courseid' => $course->id,
            'competencyid' => $c->get('id')));
        $uc = $lpg->create_user_competency(array(
            'userid' => $user->id,
            'competencyid' => $c->get('id')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::grade_competency_in_course($course->id, $user->id, $c->get('id'), 2, true);

        // Get our event event.
        $events = $sink->get_events();
        // Evidence created.
        $this->assertCount(2, $events);
        $evidencecreatedevent = $events[0];
        $event = $events[1];

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_evidence_created', $evidencecreatedevent);
        $this->assertInstanceOf('\core\event\competency_user_competency_rated_in_course', $event);
        $this->assertEquals(\context_course::instance($course->id)->id, $event->contextid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($uc->get('userid'), $event->relateduserid);
        $this->assertEquals($uc->get('competencyid'), $event->other['competencyid']);
        $this->assertEquals(2, $event->other['grade']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the user competency grade rated in plan event.
     *
     */
    public function test_user_competency_rated_in_plan() {
         $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $scale = $dg->create_scale(array('scale' => 'A,B,C,D'));
        $user = $dg->create_user();
        $scaleconfig = array(array('scaleid' => $scale->id));
        $scaleconfig[] = array('name' => 'A', 'id' => 1, 'scaledefault' => 0, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'B', 'id' => 2, 'scaledefault' => 1, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'C', 'id' => 3, 'scaledefault' => 0, 'proficient' => 1);
        $scaleconfig[] = array('name' => 'D', 'id' => 4, 'scaledefault' => 0, 'proficient' => 1);
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array(
            'competencyframeworkid' => $fr->get('id'),
            'scaleid' => $scale->id,
            'scaleconfiguration' => $scaleconfig
        ));
        $pc = $lpg->create_plan_competency(array('planid' => $plan->get('id'), 'competencyid' => $c->get('id')));
        $uc = $lpg->create_user_competency(array(
            'userid' => $user->id,
            'competencyid' => $c->get('id')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::grade_competency_in_plan($plan->get('id'), $c->get('id'), 3, true);

        // Get our event event.
        $events = $sink->get_events();
        // Evidence created.
        $this->assertCount(2, $events);
        $evidencecreatedevent = $events[0];
        $event = $events[1];

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\competency_evidence_created', $evidencecreatedevent);
        $this->assertInstanceOf('\core\event\competency_user_competency_rated_in_plan', $event);
        $this->assertEquals($uc->get('id'), $event->objectid);
        $this->assertEquals($uc->get_context()->id, $event->contextid);
        $this->assertEquals($uc->get('userid'), $event->relateduserid);
        $this->assertEquals($uc->get('competencyid'), $event->other['competencyid']);
        $this->assertEquals(3, $event->other['grade']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test user competency comment created event.
     */
    public function test_user_competency_comment_created() {
        $this->resetAfterTest(true);

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();
        $this->setUser($user);
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $fr->get('id')));
        $uc = $lpg->create_user_competency(array(
            'userid' => $user->id,
            'competencyid' => $c->get('id')
        ));

        $context = \context_user::instance($user->id);
        $cmt = new \stdClass();
        $cmt->context = $context;
        $cmt->area = 'user_competency';
        $cmt->itemid = $uc->get('id');
        $cmt->component = 'competency';
        $cmt->showcount = 1;
        $manager = new \comment($cmt);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $manager->add("New comment for user competency");
        $events = $sink->get_events();
        // Add comment will trigger 2 other events message_viewed and message_sent.
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\core\event\competency_comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($uc->get('id'), $event->other['itemid']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test plan comment deleted event.
     */
    public function test_user_competency_comment_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $user = $dg->create_user();
        $fr = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $fr->get('id')));
        $uc = $lpg->create_user_competency(array(
            'userid' => $user->id,
            'competencyid' => $c->get('id')
        ));
        $context = \context_user::instance($user->id);

        $cmt = new \stdClass();
        $cmt->context = $context;
        $cmt->area = 'user_competency';
        $cmt->itemid = $uc->get('id');
        $cmt->component = 'competency';
        $manager = new \comment($cmt);
        $newcomment = $manager->add("Comment to be deleted");

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $manager->delete($newcomment->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\core\event\competency_comment_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($uc->get('id'), $event->other['itemid']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }
}
