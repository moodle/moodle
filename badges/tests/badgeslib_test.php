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
 * Unit tests for badges
 *
 * @package    core
 * @subpackage badges
 * @copyright  2013 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/badgeslib.php');

class core_badges_badgeslib_testcase extends advanced_testcase {
    protected $badgeid;
    protected $course;
    protected $user;
    protected $module;
    protected $coursebadge;
    protected $assertion;

    protected function setUp() {
        global $DB, $CFG;
        $this->resetAfterTest(true);

        unset_config('noemailever');

        $CFG->enablecompletion = true;

        $user = $this->getDataGenerator()->create_user();

        $fordb = new stdClass();
        $fordb->id = null;
        $fordb->name = "Test badge";
        $fordb->description = "Testing badges";
        $fordb->timecreated = time();
        $fordb->timemodified = time();
        $fordb->usercreated = $user->id;
        $fordb->usermodified = $user->id;
        $fordb->issuername = "Test issuer";
        $fordb->issuerurl = "http://issuer-url.domain.co.nz";
        $fordb->issuercontact = "issuer@example.com";
        $fordb->expiredate = null;
        $fordb->expireperiod = null;
        $fordb->type = BADGE_TYPE_SITE;
        $fordb->courseid = null;
        $fordb->messagesubject = "Test message subject";
        $fordb->message = "Test message body";
        $fordb->attachment = 1;
        $fordb->notification = 0;
        $fordb->status = BADGE_STATUS_INACTIVE;

        $this->badgeid = $DB->insert_record('badge', $fordb, true);

        // Create a course with activity and auto completion tracking.
        $this->course = $this->getDataGenerator()->create_course(array('enablecompletion' => true));
        $this->user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->assertNotEmpty($studentrole);

        // Get manual enrolment plugin and enrol user.
        require_once($CFG->dirroot.'/enrol/manual/locallib.php');
        $manplugin = enrol_get_plugin('manual');
        $maninstance = $DB->get_record('enrol', array('courseid' => $this->course->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $manplugin->enrol_user($maninstance, $this->user->id, $studentrole->id);
        $this->assertEquals(1, $DB->count_records('user_enrolments'));

        $completionauto = array('completion' => COMPLETION_TRACKING_AUTOMATIC);
        $this->module = $this->getDataGenerator()->create_module('forum', array('course' => $this->course->id), $completionauto);

        // Build badge and criteria.
        $fordb->type = BADGE_TYPE_COURSE;
        $fordb->courseid = $this->course->id;
        $fordb->status = BADGE_STATUS_ACTIVE;

        $this->coursebadge = $DB->insert_record('badge', $fordb, true);
        $this->assertion = new stdClass();
        $this->assertion->badge = '{"uid":"%s","recipient":{"identity":"%s","type":"email","hashed":true,"salt":"%s"},"badge":"%s","verify":{"type":"hosted","url":"%s"},"issuedOn":"%d","evidence":"%s"}';
        $this->assertion->class = '{"name":"%s","description":"%s","image":"%s","criteria":"%s","issuer":"%s"}';
        $this->assertion->issuer = '{"name":"%s","url":"%s","email":"%s"}';
    }

    public function test_create_badge() {
        $badge = new badge($this->badgeid);

        $this->assertInstanceOf('badge', $badge);
        $this->assertEquals($this->badgeid, $badge->id);
    }

    public function test_clone_badge() {
        $badge = new badge($this->badgeid);
        $newid = $badge->make_clone();
        $cloned_badge = new badge($newid);

        $this->assertEquals($badge->description, $cloned_badge->description);
        $this->assertEquals($badge->issuercontact, $cloned_badge->issuercontact);
        $this->assertEquals($badge->issuername, $cloned_badge->issuername);
        $this->assertEquals($badge->issuercontact, $cloned_badge->issuercontact);
        $this->assertEquals($badge->issuerurl, $cloned_badge->issuerurl);
        $this->assertEquals($badge->expiredate, $cloned_badge->expiredate);
        $this->assertEquals($badge->expireperiod, $cloned_badge->expireperiod);
        $this->assertEquals($badge->type, $cloned_badge->type);
        $this->assertEquals($badge->courseid, $cloned_badge->courseid);
        $this->assertEquals($badge->message, $cloned_badge->message);
        $this->assertEquals($badge->messagesubject, $cloned_badge->messagesubject);
        $this->assertEquals($badge->attachment, $cloned_badge->attachment);
        $this->assertEquals($badge->notification, $cloned_badge->notification);
    }

    public function test_badge_status() {
        $badge = new badge($this->badgeid);
        $old_status = $badge->status;
        $badge->set_status(BADGE_STATUS_ACTIVE);
        $this->assertAttributeNotEquals($old_status, 'status', $badge);
        $this->assertAttributeEquals(BADGE_STATUS_ACTIVE, 'status', $badge);
    }

    public function test_delete_badge() {
        $badge = new badge($this->badgeid);
        $badge->delete();
        // We don't actually delete badges. We archive them.
        $this->assertAttributeEquals(BADGE_STATUS_ARCHIVED, 'status', $badge);
    }

    public function test_create_badge_criteria() {
        $badge = new badge($this->badgeid);
        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL));

        $this->assertCount(1, $badge->get_criteria());

        $criteria_profile = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE, 'badgeid' => $badge->id));
        $params = array('agg' => BADGE_CRITERIA_AGGREGATION_ALL, 'field_address' => 'address');
        $criteria_profile->save($params);

        $this->assertCount(2, $badge->get_criteria());
    }

    public function test_delete_badge_criteria() {
        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $this->badgeid));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL));
        $badge = new badge($this->badgeid);

        $this->assertInstanceOf('award_criteria_overall', $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]);

        $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->delete();
        $this->assertEmpty($badge->get_criteria());
    }

    public function test_badge_awards() {
        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $badge = new badge($this->badgeid);
        $user1 = $this->getDataGenerator()->create_user();

        $badge->issue($user1->id, true);
        $this->assertTrue($badge->is_issued($user1->id));

        $user2 = $this->getDataGenerator()->create_user();
        $badge->issue($user2->id, true);
        $this->assertTrue($badge->is_issued($user2->id));

        $this->assertCount(2, $badge->get_awards());
    }

    public function data_for_message_from_template() {
        return array(
            array(
                'This is a message with no variables',
                array(), // no params
                'This is a message with no variables'
            ),
            array(
                'This is a message with %amissing% variables',
                array(), // no params
                'This is a message with %amissing% variables'
            ),
            array(
                'This is a message with %one% variable',
                array('one' => 'a single'),
                'This is a message with a single variable'
            ),
            array(
                'This is a message with %one% %two% %three% variables',
                array('one' => 'more', 'two' => 'than', 'three' => 'one'),
                'This is a message with more than one variables'
            ),
            array(
                'This is a message with %three% %two% %one%',
                array('one' => 'variables', 'two' => 'ordered', 'three' => 'randomly'),
                'This is a message with randomly ordered variables'
            ),
            array(
                'This is a message with %repeated% %one% %repeated% of variables',
                array('one' => 'and', 'repeated' => 'lots'),
                'This is a message with lots and lots of variables'
            ),
        );
    }

    /**
     * @dataProvider data_for_message_from_template
     */
    public function test_badge_message_from_template($message, $params, $result) {
        $this->assertEquals(badge_message_from_template($message, $params), $result);
    }

    /**
     * Test badges observer when course module completion event id fired.
     */
    public function test_badges_observer_course_module_criteria_review() {
        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $badge = new badge($this->coursebadge);
        $this->assertFalse($badge->is_issued($this->user->id));

        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY));
        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_ACTIVITY, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY, 'module_'.$this->module->cmid => $this->module->cmid));

        // Set completion for forum activity.
        $c = new completion_info($this->course);
        $activities = $c->get_activities();
        $this->assertEquals(1, count($activities));
        $this->assertTrue(isset($activities[$this->module->cmid]));
        $this->assertEquals($activities[$this->module->cmid]->name, $this->module->name);

        $current = $c->get_data($activities[$this->module->cmid], false, $this->user->id);
        $current->completionstate = COMPLETION_COMPLETE;
        $current->timemodified = time();
        $sink = $this->redirectEmails();
        $c->internal_set_data($activities[$this->module->cmid], $current);
        $this->assertCount(1, $sink->get_messages());
        $sink->close();

        // Check if badge is awarded.
        $this->assertDebuggingCalled('Error baking badge image!');
        $this->assertTrue($badge->is_issued($this->user->id));
    }

    /**
     * Test badges observer when course_completed event is fired.
     */
    public function test_badges_observer_course_criteria_review() {
        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $badge = new badge($this->coursebadge);
        $this->assertFalse($badge->is_issued($this->user->id));

        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY));
        $criteria_overall1 = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_COURSE, 'badgeid' => $badge->id));
        $criteria_overall1->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY, 'course_'.$this->course->id => $this->course->id));

        $ccompletion = new completion_completion(array('course' => $this->course->id, 'userid' => $this->user->id));

        // Mark course as complete.
        $sink = $this->redirectEmails();
        $ccompletion->mark_complete();
        $this->assertCount(1, $sink->get_messages());
        $sink->close();

        // Check if badge is awarded.
        $this->assertDebuggingCalled('Error baking badge image!');
        $this->assertTrue($badge->is_issued($this->user->id));
    }

    /**
     * Test badges observer when user_updated event is fired.
     */
    public function test_badges_observer_profile_criteria_review() {
        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $badge = new badge($this->coursebadge);
        $this->assertFalse($badge->is_issued($this->user->id));

        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY));
        $criteria_overall1 = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE, 'badgeid' => $badge->id));
        $criteria_overall1->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL, 'field_address' => 'address'));

        $this->user->address = 'Test address';
        $sink = $this->redirectEmails();
        user_update_user($this->user, false);
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
        // Check if badge is awarded.
        $this->assertDebuggingCalled('Error baking badge image!');
        $this->assertTrue($badge->is_issued($this->user->id));
    }

    /**
     * Test badges assertion generated when a badge is issued.
     */
    public function test_badges_assertion() {
        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $badge = new badge($this->coursebadge);
        $this->assertFalse($badge->is_issued($this->user->id));

        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY));
        $criteria_overall1 = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE, 'badgeid' => $badge->id));
        $criteria_overall1->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL, 'field_address' => 'address'));

        $this->user->address = 'Test address';
        $sink = $this->redirectEmails();
        user_update_user($this->user, false);
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
        // Check if badge is awarded.
        $this->assertDebuggingCalled('Error baking badge image!');
        $awards = $badge->get_awards();
        $this->assertCount(1, $awards);

        // Get assertion.
        $award = reset($awards);
        $assertion = new core_badges_assertion($award->uniquehash);
        $testassertion = $this->assertion;

        // Make sure JSON strings have the same structure.
        $this->assertStringMatchesFormat($testassertion->badge, json_encode($assertion->get_badge_assertion()));
        $this->assertStringMatchesFormat($testassertion->class, json_encode($assertion->get_badge_class()));
        $this->assertStringMatchesFormat($testassertion->issuer, json_encode($assertion->get_issuer()));
    }
}
