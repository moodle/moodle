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
 * Badge events tests.
 *
 * @package    core_badges
 * @copyright  2015 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/badges/tests/badgeslib_test.php');

/**
 * Badge events tests class.
 *
 * @package    core_badges
 * @copyright  2015 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends badgeslib_test {

    /**
     * Test badge awarded event.
     */
    public function test_badge_awarded() {

        $systemcontext = context_system::instance();

        $sink = $this->redirectEvents();

        $badge = new badge($this->badgeid);
        $badge->issue($this->user->id, true);
        $badge->is_issued($this->user->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\core\event\badge_awarded', $event);
        $this->assertEquals($this->badgeid, $event->objectid);
        $this->assertEquals($this->user->id, $event->relateduserid);
        $this->assertEquals($systemcontext, $event->get_context());

        $sink->close();
    }

    /**
     * Test the badge created event.
     *
     * There is no external API for creating a badge, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_badge_created() {

        $badge = new badge($this->badgeid);
        // Trigger an event: badge created.
        $eventparams = array(
            'userid' => $badge->usercreated,
            'objectid' => $badge->id,
            'context' => $badge->get_context(),
        );

        $event = \core\event\badge_created::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_created', $event);
        $this->assertEquals($badge->usercreated, $event->userid);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge archived event.
     *
     */
    public function test_badge_archived() {
        $badge = new badge($this->badgeid);
        $sink = $this->redirectEvents();

        // Trigger and capture the event.
        $badge->delete(true);
        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = $events[1];

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_archived', $event);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }


    /**
     * Test the badge updated event.
     *
     */
    public function test_badge_updated() {
        $badge = new badge($this->badgeid);
        $sink = $this->redirectEvents();

        // Trigger and capture the event.
        $badge->save();
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertCount(1, $events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_updated', $event);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }
    /**
     * Test the badge deleted event.
     */
    public function test_badge_deleted() {
        $badge = new badge($this->badgeid);
        $sink = $this->redirectEvents();

        // Trigger and capture the event.
        $badge->delete(false);
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertCount(1, $events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_deleted', $event);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge duplicated event.
     *
     */
    public function test_badge_duplicated() {
        $badge = new badge($this->badgeid);
        $sink = $this->redirectEvents();

        // Trigger and capture the event.
        $newid = $badge->make_clone();
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertCount(1, $events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_duplicated', $event);
        $this->assertEquals($newid, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge disabled event.
     *
     */
    public function test_badge_disabled() {
        $badge = new badge($this->badgeid);
        $sink = $this->redirectEvents();

        // Trigger and capture the event.
        $badge->set_status(BADGE_STATUS_INACTIVE);
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertCount(2, $events);
        $event = $events[1];

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_disabled', $event);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge enabled event.
     *
     */
    public function test_badge_enabled() {
        $badge = new badge($this->badgeid);
        $sink = $this->redirectEvents();

        // Trigger and capture the event.
        $badge->set_status(BADGE_STATUS_ACTIVE);
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertCount(2, $events);
        $event = $events[1];

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_enabled', $event);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge criteria created event.
     *
     * There is no external API for this, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_badge_criteria_created() {

        $badge = new badge($this->badgeid);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $criteriaoverall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteriaoverall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL));
        $criteriaprofile = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE, 'badgeid' => $badge->id));
        $params = array('agg' => BADGE_CRITERIA_AGGREGATION_ALL, 'field_address' => 'address');
        $criteriaprofile->save($params);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\badge_criteria_created', $event);
        $this->assertEquals($criteriaprofile->id, $event->objectid);
        $this->assertEquals($criteriaprofile->badgeid, $event->other['badgeid']);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge criteria updated event.
     *
     * There is no external API for this, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_badge_criteria_updated() {

        $criteriaoverall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $this->badgeid));
        $criteriaoverall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL));
        $criteriaprofile = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE, 'badgeid' => $this->badgeid));
        $params = array('agg' => BADGE_CRITERIA_AGGREGATION_ALL, 'field_address' => 'address');
        $criteriaprofile->save($params);
        $badge = new badge($this->badgeid);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $criteria = $badge->criteria[BADGE_CRITERIA_TYPE_PROFILE];
        $params2 = array('agg' => BADGE_CRITERIA_AGGREGATION_ALL, 'field_address' => 'address', 'id' => $criteria->id);
        $criteria->save((array)$params2);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\badge_criteria_updated', $event);
        $this->assertEquals($criteria->id, $event->objectid);
        $this->assertEquals($this->badgeid, $event->other['badgeid']);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge criteria deleted event.
     *
     * There is no external API for this, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_badge_criteria_deleted() {

        $criteriaoverall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $this->badgeid));
        $criteriaoverall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL));
        $badge = new badge($this->badgeid);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->delete();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\badge_criteria_deleted', $event);
        $this->assertEquals($criteriaoverall->badgeid, $event->other['badgeid']);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge viewed event.
     *
     * There is no external API for viewing a badge, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_badge_viewed() {

        $badge = new badge($this->badgeid);
        // Trigger an event: badge viewed.
        $other = array('badgeid' => $badge->id, 'badgehash' => '12345678');
        $eventparams = array(
            'context' => $badge->get_context(),
            'other' => $other,
        );

        $event = \core\event\badge_viewed::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_viewed', $event);
        $this->assertEquals('12345678', $event->other['badgehash']);
        $this->assertEquals($badge->id, $event->other['badgeid']);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }

    /**
     * Test the badge listing viewed event.
     *
     * There is no external API for viewing a badge, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_badge_listing_viewed() {

        // Trigger an event: badge listing viewed.
        $context = context_system::instance();
        $eventparams = array(
            'context' => $context,
            'other' => array('badgetype' => BADGE_TYPE_SITE)
        );

        $event = \core\event\badge_listing_viewed::create($eventparams);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_listing_viewed', $event);
        $this->assertEquals(BADGE_TYPE_SITE, $event->other['badgetype']);
        $this->assertDebuggingNotCalled();
        $sink->close();

    }
}
