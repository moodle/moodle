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
require_once($CFG->dirroot . '/badges/lib.php');

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

    public function test_add_badge_criteria_description() {
        $criteriaoverall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $this->badgeid));
        $criteriaoverall->save(array(
                'agg' => BADGE_CRITERIA_AGGREGATION_ALL,
                'description' => 'Overall description',
                'descriptionformat' => FORMAT_HTML
        ));

        $criteriaprofile = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE, 'badgeid' => $this->badgeid));
        $params = array(
                'agg' => BADGE_CRITERIA_AGGREGATION_ALL,
                'field_address' => 'address',
                'description' => 'Description',
                'descriptionformat' => FORMAT_HTML
        );
        $criteriaprofile->save($params);

        $badge = new badge($this->badgeid);
        $this->assertEquals('Overall description', $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->description);
        $this->assertEquals('Description', $badge->criteria[BADGE_CRITERIA_TYPE_PROFILE]->description);
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

    /**
     * Test the {@link badges_get_user_badges()} function in lib/badgeslib.php
     */
    public function test_badges_get_user_badges() {
        global $DB;

        // Messaging is not compatible with transactions.
        $this->preventResetByRollback();

        $badges = array();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Record the current time, we need to be precise about a couple of things.
        $now = time();
        // Create 11 badges with which to test.
        for ($i = 1; $i <= 11; $i++) {
            // Mock up a badge.
            $badge = new stdClass();
            $badge->id = null;
            $badge->name = "Test badge $i";
            $badge->description = "Testing badges $i";
            $badge->timecreated = $now - 12;
            $badge->timemodified = $now - 12;
            $badge->usercreated = $user1->id;
            $badge->usermodified = $user1->id;
            $badge->issuername = "Test issuer";
            $badge->issuerurl = "http://issuer-url.domain.co.nz";
            $badge->issuercontact = "issuer@example.com";
            $badge->expiredate = null;
            $badge->expireperiod = null;
            $badge->type = BADGE_TYPE_SITE;
            $badge->courseid = null;
            $badge->messagesubject = "Test message subject for badge $i";
            $badge->message = "Test message body for badge $i";
            $badge->attachment = 1;
            $badge->notification = 0;
            $badge->status = BADGE_STATUS_INACTIVE;

            $badgeid = $DB->insert_record('badge', $badge, true);
            $badges[$badgeid] = new badge($badgeid);
            $badges[$badgeid]->issue($user2->id, true);
            // Check it all actually worked.
            $this->assertCount(1, $badges[$badgeid]->get_awards());

            // Hack the database to adjust the time each badge was issued.
            // The alternative to this is sleep which is a no-no in unit tests.
            $DB->set_field('badge_issued', 'dateissued', $now - 11 + $i, array('userid' => $user2->id, 'badgeid' => $badgeid));
        }

        // Make sure the first user has no badges.
        $result = badges_get_user_badges($user1->id);
        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);

        // Check that the second user has the expected 11 badges.
        $result = badges_get_user_badges($user2->id);
        $this->assertCount(11, $result);

        // Test pagination.
        // Ordering is by time issued desc, so things will come out with the last awarded badge first.
        $result = badges_get_user_badges($user2->id, 0, 0, 4);
        $this->assertCount(4, $result);
        $lastbadgeissued = reset($result);
        $this->assertSame('Test badge 11', $lastbadgeissued->name);
        // Page 2. Expecting 4 results again.
        $result = badges_get_user_badges($user2->id, 0, 1, 4);
        $this->assertCount(4, $result);
        $lastbadgeissued = reset($result);
        $this->assertSame('Test badge 7', $lastbadgeissued->name);
        // Page 3. Expecting just three results here.
        $result = badges_get_user_badges($user2->id, 0, 2, 4);
        $this->assertCount(3, $result);
        $lastbadgeissued = reset($result);
        $this->assertSame('Test badge 3', $lastbadgeissued->name);
        // Page 4.... there is no page 4.
        $result = badges_get_user_badges($user2->id, 0, 3, 4);
        $this->assertCount(0, $result);

        // Test search.
        $result = badges_get_user_badges($user2->id, 0, 0, 0, 'badge 1');
        $this->assertCount(3, $result);
        $lastbadgeissued = reset($result);
        $this->assertSame('Test badge 11', $lastbadgeissued->name);
        // The term Totara doesn't appear anywhere in the badges.
        $result = badges_get_user_badges($user2->id, 0, 0, 0, 'Totara');
        $this->assertCount(0, $result);

        // Issue a user with a course badge and verify its returned based on if
        // coursebadges are enabled or disabled.
        $sitebadgeid = key($badges);
        $badges[$sitebadgeid]->issue($this->user->id, true);

        $badge = new stdClass();
        $badge->id = null;
        $badge->name = "Test course badge";
        $badge->description = "Testing course badge";
        $badge->timecreated = $now;
        $badge->timemodified = $now;
        $badge->usercreated = $user1->id;
        $badge->usermodified = $user1->id;
        $badge->issuername = "Test issuer";
        $badge->issuerurl = "http://issuer-url.domain.co.nz";
        $badge->issuercontact = "issuer@example.com";
        $badge->expiredate = null;
        $badge->expireperiod = null;
        $badge->type = BADGE_TYPE_COURSE;
        $badge->courseid = $this->course->id;
        $badge->messagesubject = "Test message subject for course badge";
        $badge->message = "Test message body for course badge";
        $badge->attachment = 1;
        $badge->notification = 0;
        $badge->status = BADGE_STATUS_ACTIVE;

        $badgeid = $DB->insert_record('badge', $badge, true);
        $badges[$badgeid] = new badge($badgeid);
        $badges[$badgeid]->issue($this->user->id, true);

        // With coursebadges off, we should only get the site badge.
        set_config('badges_allowcoursebadges', false);
        $result = badges_get_user_badges($this->user->id);
        $this->assertCount(1, $result);

        // With it on, we should get both.
        set_config('badges_allowcoursebadges', true);
        $result = badges_get_user_badges($this->user->id);
        $this->assertCount(2, $result);

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

        // Assert the badge will not be issued to the user as is.
        $badge = new badge($this->coursebadge);
        $badge->review_all_criteria();
        $this->assertFalse($badge->is_issued($this->user->id));

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

        // Assert the badge will not be issued to the user as is.
        $badge = new badge($this->coursebadge);
        $badge->review_all_criteria();
        $this->assertFalse($badge->is_issued($this->user->id));

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
        global $CFG, $DB;
        require_once($CFG->dirroot.'/user/profile/lib.php');

        // Add a custom field of textarea type.
        $customprofileid = $DB->insert_record('user_info_field', array(
            'shortname' => 'newfield', 'name' => 'Description of new field', 'categoryid' => 1,
            'datatype' => 'textarea'));

        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $badge = new badge($this->coursebadge);

        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY));
        $criteria_overall1 = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE, 'badgeid' => $badge->id));
        $criteria_overall1->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL, 'field_address' => 'address', 'field_aim' => 'aim',
            'field_' . $customprofileid => $customprofileid));

        // Assert the badge will not be issued to the user as is.
        $badge = new badge($this->coursebadge);
        $badge->review_all_criteria();
        $this->assertFalse($badge->is_issued($this->user->id));

        // Set the required fields and make sure the badge got issued.
        $this->user->address = 'Test address';
        $this->user->aim = '999999999';
        $sink = $this->redirectEmails();
        profile_save_data((object)array('id' => $this->user->id, 'profile_field_newfield' => 'X'));
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

    /**
     * Tests the core_badges_myprofile_navigation() function.
     */
    public function test_core_badges_myprofile_navigation() {
        // Set up the test.
        $tree = new \core_user\output\myprofile\tree();
        $this->setAdminUser();
        $badge = new badge($this->badgeid);
        $badge->issue($this->user->id, true);
        $iscurrentuser = true;
        $course = null;

        // Enable badges.
        set_config('enablebadges', true);

        // Check the node tree is correct.
        core_badges_myprofile_navigation($tree, $this->user, $iscurrentuser, $course);
        $reflector = new ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes->setAccessible(true);
        $this->assertArrayHasKey('localbadges', $nodes->getValue($tree));
    }

    /**
     * Tests the core_badges_myprofile_navigation() function with badges disabled..
     */
    public function test_core_badges_myprofile_navigation_badges_disabled() {
        // Set up the test.
        $tree = new \core_user\output\myprofile\tree();
        $this->setAdminUser();
        $badge = new badge($this->badgeid);
        $badge->issue($this->user->id, true);
        $iscurrentuser = false;
        $course = null;

        // Disable badges.
        set_config('enablebadges', false);

        // Check the node tree is correct.
        core_badges_myprofile_navigation($tree, $this->user, $iscurrentuser, $course);
        $reflector = new ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes->setAccessible(true);
        $this->assertArrayNotHasKey('localbadges', $nodes->getValue($tree));
    }

    /**
     * Tests the core_badges_myprofile_navigation() function with a course badge.
     */
    public function test_core_badges_myprofile_navigation_with_course_badge() {
        // Set up the test.
        $tree = new \core_user\output\myprofile\tree();
        $this->setAdminUser();
        $badge = new badge($this->coursebadge);
        $badge->issue($this->user->id, true);
        $iscurrentuser = false;

        // Check the node tree is correct.
        core_badges_myprofile_navigation($tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new ReflectionObject($tree);
        $nodes = $reflector->getProperty('nodes');
        $nodes->setAccessible(true);
        $this->assertArrayHasKey('localbadges', $nodes->getValue($tree));
    }
}
