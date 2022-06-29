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

use core_badges\helper;

class badgeslib_test extends advanced_testcase {
    protected $badgeid;
    protected $course;
    protected $user;
    protected $module;
    protected $coursebadge;
    protected $assertion;

    /** @var $assertion2 to define json format for Open badge version 2 */
    protected $assertion2;

    protected function setUp(): void {
        global $DB, $CFG;
        $this->resetAfterTest(true);
        $CFG->enablecompletion = true;
        $user = $this->getDataGenerator()->create_user();
        $fordb = new stdClass();
        $fordb->id = null;
        $fordb->name = "Test badge with 'apostrophe' and other friends (<>&@#)";
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
        $fordb->version = 1;
        $fordb->language = 'en';
        $fordb->courseid = null;
        $fordb->messagesubject = "Test message subject";
        $fordb->message = "Test message body";
        $fordb->attachment = 1;
        $fordb->notification = 0;
        $fordb->imageauthorname = "Image Author 1";
        $fordb->imageauthoremail = "author@example.com";
        $fordb->imageauthorurl = "http://author-url.example.com";
        $fordb->imagecaption = "Test caption image";
        $fordb->status = BADGE_STATUS_INACTIVE;

        $this->badgeid = $DB->insert_record('badge', $fordb, true);

        // Set the default Issuer (because OBv2 needs them).
        set_config('badges_defaultissuername', $fordb->issuername);
        set_config('badges_defaultissuercontact', $fordb->issuercontact);

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

        // Insert Endorsement.
        $endorsement = new stdClass();
        $endorsement->badgeid = $this->coursebadge;
        $endorsement->issuername = "Issuer 123";
        $endorsement->issueremail = "issuer123@email.com";
        $endorsement->issuerurl = "https://example.org/issuer-123";
        $endorsement->dateissued = 1524567747;
        $endorsement->claimid = "https://example.org/robotics-badge.json";
        $endorsement->claimcomment = "Test endorser comment";
        $DB->insert_record('badge_endorsement', $endorsement, true);

        // Insert related badges.
        $badge = new badge($this->coursebadge);
        $clonedid = $badge->make_clone();
        $badgeclone = new badge($clonedid);
        $badgeclone->status = BADGE_STATUS_ACTIVE;
        $badgeclone->save();

        $relatebadge = new stdClass();
        $relatebadge->badgeid = $this->coursebadge;
        $relatebadge->relatedbadgeid = $clonedid;
        $relatebadge->relatedid = $DB->insert_record('badge_related', $relatebadge, true);

        // Insert a aligment.
        $alignment = new stdClass();
        $alignment->badgeid = $this->coursebadge;
        $alignment->targetname = 'CCSS.ELA-Literacy.RST.11-12.3';
        $alignment->targeturl = 'http://www.corestandards.org/ELA-Literacy/RST/11-12/3';
        $alignment->targetdescription = 'Test target description';
        $alignment->targetframework = 'CCSS.RST.11-12.3';
        $alignment->targetcode = 'CCSS.RST.11-12.3';
        $DB->insert_record('badge_alignment', $alignment, true);

        $this->assertion = new stdClass();
        $this->assertion->badge = '{"uid":"%s","recipient":{"identity":"%s","type":"email","hashed":true,"salt":"%s"},"badge":"%s","verify":{"type":"hosted","url":"%s"},"issuedOn":"%d","evidence":"%s"}';
        $this->assertion->class = '{"name":"%s","description":"%s","image":"%s","criteria":"%s","issuer":"%s"}';
        $this->assertion->issuer = '{"name":"%s","url":"%s","email":"%s"}';
        // Format JSON-LD for Openbadge specification version 2.0.
        $this->assertion2 = new stdClass();
        $this->assertion2->badge = '{"recipient":{"identity":"%s","type":"email","hashed":true,"salt":"%s"},' .
            '"badge":{"name":"%s","description":"%s","image":"%s",' .
            '"criteria":{"id":"%s","narrative":"%s"},"issuer":{"name":"%s","url":"%s","email":"%s",' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"Issuer"},' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"BadgeClass","version":"%s",' .
            '"@language":"en","related":[{"id":"%s","version":"%s","@language":"%s"}],"endorsement":"%s",' .
            '"alignments":[{"targetName":"%s","targetUrl":"%s","targetDescription":"%s","targetFramework":"%s",' .
            '"targetCode":"%s"}]},"verify":{"type":"hosted","url":"%s"},"issuedOn":"%s","evidence":"%s",' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","type":"Assertion","id":"%s"}';

        $this->assertion2->class = '{"name":"%s","description":"%s","image":"%s",' .
            '"criteria":{"id":"%s","narrative":"%s"},"issuer":{"name":"%s","url":"%s","email":"%s",' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"Issuer"},' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"BadgeClass","version":"%s",' .
            '"@language":"%s","related":[{"id":"%s","version":"%s","@language":"%s"}],"endorsement":"%s",' .
            '"alignments":[{"targetName":"%s","targetUrl":"%s","targetDescription":"%s","targetFramework":"%s",' .
            '"targetCode":"%s"}]}';
        $this->assertion2->issuer = '{"name":"%s","url":"%s","email":"%s",' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"Issuer"}';
    }

    public function test_create_badge() {
        $badge = new badge($this->badgeid);

        $this->assertInstanceOf('badge', $badge);
        $this->assertEquals($this->badgeid, $badge->id);
    }

    public function test_clone_badge() {
        $badge = new badge($this->badgeid);
        $newid = $badge->make_clone();
        $clonedbadge = new badge($newid);

        $this->assertEquals($badge->description, $clonedbadge->description);
        $this->assertEquals($badge->issuercontact, $clonedbadge->issuercontact);
        $this->assertEquals($badge->issuername, $clonedbadge->issuername);
        $this->assertEquals($badge->issuercontact, $clonedbadge->issuercontact);
        $this->assertEquals($badge->issuerurl, $clonedbadge->issuerurl);
        $this->assertEquals($badge->expiredate, $clonedbadge->expiredate);
        $this->assertEquals($badge->expireperiod, $clonedbadge->expireperiod);
        $this->assertEquals($badge->type, $clonedbadge->type);
        $this->assertEquals($badge->courseid, $clonedbadge->courseid);
        $this->assertEquals($badge->message, $clonedbadge->message);
        $this->assertEquals($badge->messagesubject, $clonedbadge->messagesubject);
        $this->assertEquals($badge->attachment, $clonedbadge->attachment);
        $this->assertEquals($badge->notification, $clonedbadge->notification);
        $this->assertEquals($badge->version, $clonedbadge->version);
        $this->assertEquals($badge->language, $clonedbadge->language);
        $this->assertEquals($badge->imagecaption, $clonedbadge->imagecaption);
        $this->assertEquals($badge->imageauthorname, $clonedbadge->imageauthorname);
        $this->assertEquals($badge->imageauthoremail, $clonedbadge->imageauthoremail);
        $this->assertEquals($badge->imageauthorurl, $clonedbadge->imageauthorurl);
    }

    public function test_badge_status() {
        $badge = new badge($this->badgeid);
        $old_status = $badge->status;
        $badge->set_status(BADGE_STATUS_ACTIVE);
        $this->assertNotEquals($old_status, $badge->status);
        $this->assertEquals(BADGE_STATUS_ACTIVE, $badge->status);
    }

    public function test_delete_badge() {
        $badge = new badge($this->badgeid);
        $badge->delete();
        // We don't actually delete badges. We archive them.
        $this->assertEquals(BADGE_STATUS_ARCHIVED, $badge->status);
    }

    /**
     * Really delete the badge.
     */
    public function test_delete_badge_for_real() {
        global $DB;

        $badge = new badge($this->badgeid);

        $newid1 = $badge->make_clone();
        $newid2 = $badge->make_clone();
        $newid3 = $badge->make_clone();

        // Insert related badges to badge 1.
        $badge->add_related_badges([$newid1, $newid2, $newid3]);

        // Another badge.
        $badge2 = new badge($newid2);
        // Make badge 1 related for badge 2.
        $badge2->add_related_badges([$this->badgeid]);

        // Confirm that the records about this badge about its relations have been removed as well.
        $relatedsql = 'badgeid = :badgeid OR relatedbadgeid = :relatedbadgeid';
        $relatedparams = array(
            'badgeid' => $this->badgeid,
            'relatedbadgeid' => $this->badgeid
        );
        // Badge 1 has 4 related records. 3 where it's the badgeid, 1 where it's the relatedbadgeid.
        $this->assertEquals(4, $DB->count_records_select('badge_related', $relatedsql, $relatedparams));

        // Delete the badge for real.
        $badge->delete(false);

        // Confirm that the badge itself has been removed.
        $this->assertFalse($DB->record_exists('badge', ['id' => $this->badgeid]));

        // Confirm that the records about this badge about its relations have been removed as well.
        $this->assertFalse($DB->record_exists_select('badge_related', $relatedsql, $relatedparams));
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
        global $DB;
        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $badge = new badge($this->badgeid);
        $user1 = $this->getDataGenerator()->create_user();

        $sink = $this->redirectMessages();

        $DB->set_field_select('message_processors', 'enabled', 0, "name <> 'email'");
        set_user_preference('message_provider_moodle_badgerecipientnotice_enabled', 'email', $user1);

        $badge->issue($user1->id, false);
        $this->assertDebuggingCalled(); // Expect debugging while baking a badge via phpunit.
        $this->assertTrue($badge->is_issued($user1->id));

        $messages = $sink->get_messages();
        $sink->close();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);
        // Check we have the expected data.
        $customdata = json_decode($message->customdata);
        $this->assertObjectHasAttribute('notificationiconurl', $customdata);
        $this->assertObjectHasAttribute('hash', $customdata);

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
            $badge->version = "Version $i";
            $badge->language = "en";
            $badge->imagecaption = "Image caption $i";
            $badge->imageauthorname = "Image author's name $i";
            $badge->imageauthoremail = "author$i@example.com";
            $badge->imageauthorname = "Image author's name $i";

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
        $this->assertIsArray($result);
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
        $badge->version = "Version $i";
        $badge->language = "en";
        $badge->imagecaption = "Image caption";
        $badge->imageauthorname = "Image author's name";
        $badge->imageauthoremail = "author@example.com";
        $badge->imageauthorname = "Image author's name";

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
        $sink = $this->redirectMessages();
        $ccompletion->mark_complete();
        // Two messages are generated: One for the course completed and the other one for the badge awarded.
        $messages = $sink->get_messages();
        $this->assertCount(2, $messages);
        $this->assertEquals('badgerecipientnotice', $messages[0]->eventtype);
        $this->assertEquals('coursecompleted', $messages[1]->eventtype);
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
        $customprofileid = $this->getDataGenerator()->create_custom_profile_field(array(
            'shortname' => 'newfield', 'name' => 'Description of new field',
            'datatype' => 'textarea'))->id;

        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $badge = new badge($this->coursebadge);

        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY));
        $criteria_overall1 = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE, 'badgeid' => $badge->id));
        $criteria_overall1->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL, 'field_address' => 'address',
            'field_department' => 'department', 'field_' . $customprofileid => $customprofileid));

        // Assert the badge will not be issued to the user as is.
        $badge = new badge($this->coursebadge);
        $badge->review_all_criteria();
        $this->assertFalse($badge->is_issued($this->user->id));

        // Set the required fields and make sure the badge got issued.
        $this->user->address = 'Test address';
        $this->user->department = 'sillywalks';
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
     * Test badges observer when cohort_member_added event is fired and user required to belong to any cohort.
     *
     * @covers \award_criteria_cohort
     */
    public function test_badges_observer_any_cohort_criteria_review() {
        global $CFG;

        require_once("$CFG->dirroot/cohort/lib.php");

        $cohort1 = $this->getDataGenerator()->create_cohort();
        $cohort2 = $this->getDataGenerator()->create_cohort();

        $this->preventResetByRollback(); // Messaging is not compatible with transactions.

        $badge = new badge($this->badgeid);
        $this->assertFalse($badge->is_issued($this->user->id));
        $this->assertSame(0, $badge->review_all_criteria()); // Verify award_criteria_cohort->get_completed_criteria_sql().

        // Set up the badge criteria.
        $criteriaoverall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteriaoverall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY));
        $criteriaoverall1 = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_COHORT, 'badgeid' => $badge->id));
        $criteriaoverall1->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY,
            'cohort_cohorts' => array('0' => $cohort1->id, '1' => $cohort2->id)));
        $badge->set_status(BADGE_STATUS_ACTIVE);

        // Reload it to contain criteria.
        $badge = new badge($this->badgeid);
        $this->assertFalse($badge->is_issued($this->user->id));
        $this->assertSame(0, $badge->review_all_criteria()); // Verify award_criteria_cohort->get_completed_criteria_sql().

        // Add the user to the cohort.
        cohort_add_member($cohort2->id, $this->user->id);
        $this->assertDebuggingCalled();

        // Verify that the badge was awarded.
        $this->assertTrue($badge->is_issued($this->user->id));
        // As the badge has been awarded to user because core_badges_observer been called when the member has been added to the
        // cohort, there are no other users that can award this badge.
        $this->assertSame(0, $badge->review_all_criteria()); // Verify award_criteria_cohort->get_completed_criteria_sql().
    }

    /**
     * Test badges observer when cohort_member_added event is fired and user required to belong to multiple (all) cohorts.
     *
     * @covers \award_criteria_cohort
     */
    public function test_badges_observer_all_cohort_criteria_review() {
        global $CFG;

        require_once("$CFG->dirroot/cohort/lib.php");

        $cohort1 = $this->getDataGenerator()->create_cohort();
        $cohort2 = $this->getDataGenerator()->create_cohort();
        $cohort3 = $this->getDataGenerator()->create_cohort();

        // Add user2 to cohort1 and cohort3.
        $user2 = $this->getDataGenerator()->create_user();
        cohort_add_member($cohort3->id, $user2->id);
        cohort_add_member($cohort1->id, $user2->id);

        // Add user3 to cohort1, cohort2 and cohort3.
        $user3 = $this->getDataGenerator()->create_user();
        cohort_add_member($cohort1->id, $user3->id);
        cohort_add_member($cohort2->id, $user3->id);
        cohort_add_member($cohort3->id, $user3->id);

        $this->preventResetByRollback(); // Messaging is not compatible with transactions.

        // Cohort criteria are used in site badges.
        $badge = new badge($this->badgeid);

        $this->assertFalse($badge->is_issued($this->user->id));
        $this->assertSame(0, $badge->review_all_criteria()); // Verify award_criteria_cohort->get_completed_criteria_sql().

        // Set up the badge criteria.
        $criteriaoverall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteriaoverall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ANY));
        $criteriaoverall1 = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_COHORT, 'badgeid' => $badge->id));
        $criteriaoverall1->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL,
            'cohort_cohorts' => array('0' => $cohort1->id, '1' => $cohort2->id, '2' => $cohort3->id)));
        $badge->set_status(BADGE_STATUS_ACTIVE);

        // Reload it to contain criteria.
        $badge = new badge($this->badgeid);

        // Verify that the badge was not awarded yet (ALL cohorts are needed and review_all_criteria has to be called).
        $this->assertFalse($badge->is_issued($this->user->id));
        $this->assertFalse($badge->is_issued($user2->id));
        $this->assertFalse($badge->is_issued($user3->id));

        // Verify that after calling review_all_criteria, users with the criteria (user3) award the badge instantly.
        $this->assertSame(1, $badge->review_all_criteria()); // Verify award_criteria_cohort->get_completed_criteria_sql().
        $this->assertFalse($badge->is_issued($this->user->id));
        $this->assertFalse($badge->is_issued($user2->id));
        $this->assertTrue($badge->is_issued($user3->id));
        $this->assertDebuggingCalled();

        // Add the user to the cohort1.
        cohort_add_member($cohort1->id, $this->user->id);

        // Verify that the badge was not awarded yet (ALL cohorts are needed).
        $this->assertFalse($badge->is_issued($this->user->id));
        $this->assertSame(0, $badge->review_all_criteria()); // Verify award_criteria_cohort->get_completed_criteria_sql().

        // Add the user to the cohort3.
        cohort_add_member($cohort3->id, $this->user->id);

        // Verify that the badge was not awarded yet (ALL cohorts are needed).
        $this->assertFalse($badge->is_issued($this->user->id));
        $this->assertSame(0, $badge->review_all_criteria()); // Verify award_criteria_cohort->get_completed_criteria_sql().

        // Add user to cohort2.
        cohort_add_member($cohort2->id, $this->user->id);
        $this->assertDebuggingCalled();

        // Verify that the badge was awarded (ALL cohorts).
        $this->assertTrue($badge->is_issued($this->user->id));
        // As the badge has been awarded to user because core_badges_observer been called when the member has been added to the
        // cohort, there are no other users that can award this badge.
        $this->assertSame(0, $badge->review_all_criteria()); // Verify award_criteria_cohort->get_completed_criteria_sql().
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
        $assertion = new core_badges_assertion($award->uniquehash, OPEN_BADGES_V1);
        $testassertion = $this->assertion;

        // Make sure JSON strings have the same structure.
        $this->assertStringMatchesFormat($testassertion->badge, json_encode($assertion->get_badge_assertion()));
        $this->assertStringMatchesFormat($testassertion->class, json_encode($assertion->get_badge_class()));
        $this->assertStringMatchesFormat($testassertion->issuer, json_encode($assertion->get_issuer()));

        // Test Openbadge specification version 2.
        // Get assertion version 2.
        $award = reset($awards);
        $assertion2 = new core_badges_assertion($award->uniquehash, OPEN_BADGES_V2);
        $testassertion2 = $this->assertion2;

        // Make sure JSON strings have the same structure.
        $this->assertStringMatchesFormat($testassertion2->badge, json_encode($assertion2->get_badge_assertion()));
        $this->assertStringMatchesFormat($testassertion2->class, json_encode($assertion2->get_badge_class()));
        $this->assertStringMatchesFormat($testassertion2->issuer, json_encode($assertion2->get_issuer()));

        // Test Openbadge specification version 2.1. It has the same format as OBv2.0.
        // Get assertion version 2.1.
        $award = reset($awards);
        $assertion2 = new core_badges_assertion($award->uniquehash, OPEN_BADGES_V2P1);

        // Make sure JSON strings have the same structure.
        $this->assertStringMatchesFormat($testassertion2->badge, json_encode($assertion2->get_badge_assertion()));
        $this->assertStringMatchesFormat($testassertion2->class, json_encode($assertion2->get_badge_class()));
        $this->assertStringMatchesFormat($testassertion2->issuer, json_encode($assertion2->get_issuer()));
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

    /**
     * Test insert and update endorsement with a site badge.
     */
    public function test_badge_endorsement() {
        $badge = new badge($this->badgeid);

        // Insert Endorsement.
        $endorsement = new stdClass();
        $endorsement->badgeid = $this->badgeid;
        $endorsement->issuername = "Issuer 123";
        $endorsement->issueremail = "issuer123@email.com";
        $endorsement->issuerurl = "https://example.org/issuer-123";
        $endorsement->dateissued = 1524567747;
        $endorsement->claimid = "https://example.org/robotics-badge.json";
        $endorsement->claimcomment = "Test endorser comment";

        $badge->save_endorsement($endorsement);
        $endorsement1 = $badge->get_endorsement();
        $this->assertEquals($endorsement->badgeid, $endorsement1->badgeid);
        $this->assertEquals($endorsement->issuername, $endorsement1->issuername);
        $this->assertEquals($endorsement->issueremail, $endorsement1->issueremail);
        $this->assertEquals($endorsement->issuerurl, $endorsement1->issuerurl);
        $this->assertEquals($endorsement->dateissued, $endorsement1->dateissued);
        $this->assertEquals($endorsement->claimid, $endorsement1->claimid);
        $this->assertEquals($endorsement->claimcomment, $endorsement1->claimcomment);

        // Update Endorsement.
        $endorsement1->issuername = "Issuer update";
        $badge->save_endorsement($endorsement1);
        $endorsement2 = $badge->get_endorsement();
        $this->assertEquals($endorsement1->id, $endorsement2->id);
        $this->assertEquals($endorsement1->issuername, $endorsement2->issuername);
    }

    /**
     * Test insert and delete related badge with a site badge.
     */
    public function test_badge_related() {
        $badge = new badge($this->badgeid);
        $newid1 = $badge->make_clone();
        $newid2 = $badge->make_clone();
        $newid3 = $badge->make_clone();

        // Insert an related badge.
        $badge->add_related_badges([$newid1, $newid2, $newid3]);
        $this->assertCount(3, $badge->get_related_badges());

        // Only get related is active.
        $clonedbage1 = new badge($newid1);
        $clonedbage1->status = BADGE_STATUS_ACTIVE;
        $clonedbage1->save();
        $this->assertCount(1, $badge->get_related_badges(true));

        // Delete an related badge.
        $badge->delete_related_badge($newid2);
        $this->assertCount(2, $badge->get_related_badges());
    }

    /**
     * Test insert, update, delete alignment with a site badge.
     */
    public function test_alignments() {
        $badge = new badge($this->badgeid);

        // Insert a alignment.
        $alignment1 = new stdClass();
        $alignment1->badgeid = $this->badgeid;
        $alignment1->targetname = 'CCSS.ELA-Literacy.RST.11-12.3';
        $alignment1->targeturl = 'http://www.corestandards.org/ELA-Literacy/RST/11-12/3';
        $alignment1->targetdescription = 'Test target description';
        $alignment1->targetframework = 'CCSS.RST.11-12.3';
        $alignment1->targetcode = 'CCSS.RST.11-12.3';
        $alignment2 = clone $alignment1;
        $newid1 = $badge->save_alignment($alignment1);
        $newid2 = $badge->save_alignment($alignment2);
        $alignments1 = $badge->get_alignments();
        $this->assertCount(2, $alignments1);

        $this->assertEquals($alignment1->badgeid, $alignments1[$newid1]->badgeid);
        $this->assertEquals($alignment1->targetname, $alignments1[$newid1]->targetname);
        $this->assertEquals($alignment1->targeturl, $alignments1[$newid1]->targeturl);
        $this->assertEquals($alignment1->targetdescription, $alignments1[$newid1]->targetdescription);
        $this->assertEquals($alignment1->targetframework, $alignments1[$newid1]->targetframework);
        $this->assertEquals($alignment1->targetcode, $alignments1[$newid1]->targetcode);

        // Update aligment.
        $alignments1[$newid1]->targetname = 'CCSS.ELA-Literacy.RST.11-12.3 update';
        $badge->save_alignment($alignments1[$newid1], $alignments1[$newid1]->id);
        $alignments2 = $badge->get_alignments();
        $this->assertEquals($alignments1[$newid1]->id, $alignments2[$newid1]->id);
        $this->assertEquals($alignments1[$newid1]->targetname, $alignments2[$newid1]->targetname);

        // Delete alignment.
        $badge->delete_alignment($alignments1[$newid2]->id);
        $this->assertCount(1, $badge->get_alignments());
    }

    /**
     * Test badges_delete_site_backpack().
     *
     */
    public function test_badges_delete_site_backpack(): void {
        global $DB;

        $this->setAdminUser();

        // Create one backpack.
        $total = $DB->count_records('badge_external_backpack');
        $this->assertEquals(1, $total);

        $data = new \stdClass();
        $data->apiversion = OPEN_BADGES_V2P1;
        $data->backpackapiurl = 'https://dc.imsglobal.org/obchost/ims/ob/v2p1';
        $data->backpackweburl = 'https://dc.imsglobal.org';
        badges_create_site_backpack($data);
        $backpack = $DB->get_record('badge_external_backpack', ['backpackweburl' => $data->backpackweburl]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        // User1 is connected to the backpack to be removed and has 2 collections.
        $backpackuser1 = helper::create_fake_backpack(['userid' => $user1->id, 'externalbackpackid' => $backpack->id]);
        helper::create_fake_backpack_collection(['backpackid' => $backpackuser1->id]);
        helper::create_fake_backpack_collection(['backpackid' => $backpackuser1->id]);
        // User2 is connected to a different backpack and has 1 collection.
        $backpackuser2 = helper::create_fake_backpack(['userid' => $user2->id]);
        helper::create_fake_backpack_collection(['backpackid' => $backpackuser2->id]);

        $total = $DB->count_records('badge_external_backpack');
        $this->assertEquals(2, $total);
        $total = $DB->count_records('badge_backpack');
        $this->assertEquals(2, $total);
        $total = $DB->count_records('badge_external');
        $this->assertEquals(3, $total);

        // Remove the backpack created previously.
        $result = badges_delete_site_backpack($backpack->id);
        $this->assertTrue($result);

        $total = $DB->count_records('badge_external_backpack');
        $this->assertEquals(1, $total);

        $total = $DB->count_records('badge_backpack');
        $this->assertEquals(1, $total);

        $total = $DB->count_records('badge_external');
        $this->assertEquals(1, $total);

        // Try to remove an non-existent backpack.
        $result = badges_delete_site_backpack($backpack->id);
        $this->assertFalse($result);
    }

    /**
     * Test to validate badges_save_backpack_credentials.
     *
     * @dataProvider save_backpack_credentials_provider
     * @param  bool $addbackpack True if backpack data has to be created; false otherwise (empty data will be used then).
     * @param  string|null  $mail  Backpack mail address.
     * @param  string|null  $password  Backpack password.
     */
    public function test_save_backpack_credentials(bool $addbackpack = true, ?string $mail = null, ?string $password = null) {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $data = [];
        if ($addbackpack) {
            $data = new \stdClass();
            $data->apiversion = OPEN_BADGES_V2P1;
            $data->backpackapiurl = 'https://dc.imsglobal.org/obchost/ims/ob/v2p1';
            $data->backpackweburl = 'https://dc.imsglobal.org';
            badges_create_site_backpack($data);
            $backpack = $DB->get_record('badge_external_backpack', ['backpackweburl' => $data->backpackweburl]);
            $user = $this->getDataGenerator()->create_user();

            $data = [
                'externalbackpackid' => $backpack->id,
                'userid' => $user->id,
            ];

            if (!empty($mail)) {
                $data['backpackemail'] = $mail;
            }
            if (!empty($password)) {
                $data['password'] = $password;
            }
        }

        $return = badges_save_backpack_credentials((object) $data);
        if (array_key_exists('userid', $data)) {
            $record = $DB->get_record('badge_backpack', ['userid' => $user->id]);
        } else {
            $record = $DB->get_records('badge_backpack');
        }

        if (!empty($mail) && !empty($password)) {
            // The backpack credentials are created if the given information is right.
            $this->assertNotEmpty($record);
            $this->assertEquals($data['externalbackpackid'], $return);
        } else if ($addbackpack) {
            // If no email and password are given, no backpack is created/modified.
            $this->assertEmpty($record);
            $this->assertEquals($data['externalbackpackid'], $return);
        } else {
            // There weren't fields to add to the backpack so no DB change is expected.
            $this->assertEmpty($record);
            $this->assertEquals(0, $return);
        }

        // Confirm the existing backpack credential can be updated (if it has been created).
        if (!empty($record)) {
            $data['backpackemail'] = 'modified_' . $mail;
            $data['id'] = $record->id;
            $return = badges_save_backpack_credentials((object) $data);
            $record = $DB->get_record('badge_backpack', ['userid' => $user->id]);

            $this->assertNotEmpty($record);
            $this->assertEquals($data['backpackemail'], $record->email);
            $this->assertEquals($data['externalbackpackid'], $return);
        }
    }

    /**
     * Data provider for test_create_backpack_credentials().
     *
     * @return array
     */
    public function save_backpack_credentials_provider(): array {
        return [
            'Empty fields' => [
                false,
            ],
            'No backpack mail or password are defined' => [
                true,
            ],
            'Both backpack mail and password are defined' => [
                true, 'test@test.com', '1234',
            ],
            'Only backpack mail is defined (no password is given)' => [
                true, 'test@test.com', null,
            ],
            'Only backpack password is defined (no mail is given)' => [
                true, null, '1234'
            ],
        ];
    }

    /**
     * Test badges_save_external_backpack.
     *
     * @dataProvider badges_save_external_backpack_provider
     * @param  array $data  Backpack data to save.
     * @param  bool $adduser True if a real user has to be used for creating the backpack; false otherwise.
     * @param  bool $duplicates True if duplicates has to be tested too; false otherwise.
     */
    public function test_badges_save_external_backpack(array $data, bool $adduser, bool $duplicates) {
        global $DB;

        $this->resetAfterTest();

        $userid = 0;
        if ($adduser) {
            $user = $this->getDataGenerator()->create_user();
            $userid = $user->id;
            $data['userid'] = $user->id;
        }

        $result = badges_save_external_backpack((object) $data);
        $this->assertNotEquals(0, $result);
        $record = $DB->get_record('badge_external_backpack', ['id' => $result]);
        $this->assertEquals($record->backpackweburl, $data['backpackweburl']);
        $this->assertEquals($record->backpackapiurl, $data['backpackapiurl']);

        $record = $DB->get_record('badge_backpack', ['externalbackpackid' => $result]);
        if (!array_key_exists('backpackemail', $data) && !array_key_exists('password', $data)) {
            $this->assertEmpty($record);
            $total = $DB->count_records('badge_backpack');
            $this->assertEquals(0, $total);
        } else {
            $this->assertNotEmpty($record);
            $this->assertEquals($record->userid, $userid);
        }

        if ($duplicates) {
            // We shouldn't be able to insert multiple external_backpacks with the same values.
            $this->expectException('dml_write_exception');
            $result = badges_save_external_backpack((object)$data);
        }
    }

    /**
     * Provider for test_badges_save_external_backpack
     *
     * @return array
     */
    public function badges_save_external_backpack_provider() {
        $data = [
            'apiversion' => 2,
            'backpackapiurl' => 'https://api.ca.badgr.io/v2',
            'backpackweburl' => 'https://ca.badgr.io',
        ];
        return [
            'Test without user and auth details. Check duplicates too' => [
                'data' => $data,
                'adduser' => false,
                'duplicates' => true,
            ],
            'Test without user and auth details. No duplicates' => [
                'data' => $data,
                'adduser' => false,
                'duplicates' => false,
            ],
            'Test with user and without auth details' => [
                'data' => $data,
                'adduser' => true,
                'duplicates' => false,
            ],
            'Test with user and without auth details. Check duplicates too' => [
                'data' => $data,
                'adduser' => true,
                'duplicates' => true,
            ],
            'Test with empty backpackemail, password and id' => [
                'data' => array_merge($data, [
                    'backpackemail' => '',
                    'password' => '',
                    'id' => 0,
                ]),
                'adduser' => false,
                'duplicates' => false,
            ],
            'Test with empty backpackemail, password and id but with user' => [
                'data' => array_merge($data, [
                    'backpackemail' => '',
                    'password' => '',
                    'id' => 0,
                ]),
                'adduser' => true,
                'duplicates' => false,
            ],
            'Test with auth details but without user' => [
                'data' => array_merge($data, [
                    'backpackemail' => 'test@test.com',
                    'password' => 'test',
                ]),
                'adduser' => false,
                'duplicates' => false,
            ],
            'Test with auth details and user' => [
                'data' => array_merge($data, [
                    'backpackemail' => 'test@test.com',
                    'password' => 'test',
                ]),
                'adduser' => true,
                'duplicates' => false,
            ],
        ];
    }

    /**
     * Test backpack creation/update with auth details provided
     *
     * @param boolean $isadmin
     * @param boolean $updatetest
     * @dataProvider badges_create_site_backpack_provider
     */
    public function test_badges_create_site_backpack($isadmin, $updatetest) {
        global $DB;
        $this->resetAfterTest();

        $data = [
            'apiversion' => 2,
            'backpackapiurl' => 'https://api.ca.badgr.io/v2',
            'backpackweburl' => 'https://ca.badgr.io',
        ];

        $data['backpackemail'] = 'test@test.com';
        $data['password'] = 'test';
        if ($isadmin || $updatetest) {
            $this->setAdminUser();
            $lastmax = $DB->get_field_sql('SELECT MAX(sortorder) FROM {badge_external_backpack}');
            $backpack = badges_create_site_backpack((object) $data);
        }

        if ($isadmin) {
            if ($updatetest) {
                $record = $DB->get_record('badge_backpack', ['userid' => 0]);
                $data['badgebackpack'] = $record->id;
                $data['backpackapiurl'] = 'https://api.ca.badgr.io/v3';
                badges_update_site_backpack($backpack, (object)$data);
            }
            $record = $DB->get_record('badge_external_backpack', ['id' => $backpack]);
            $this->assertEquals($data['backpackweburl'], $record->backpackweburl);
            $this->assertEquals($data['backpackapiurl'], $record->backpackapiurl);
            $this->assertEquals($lastmax + 1, $record->sortorder);
            $record = $DB->get_record('badge_backpack', ['userid' => 0]);
            $this->assertNotEmpty($record);
        } else {
            $user = $this->getDataGenerator()->create_user();
            $this->setUser($user);
            $this->expectException('required_capability_exception');
            if ($updatetest) {
                $result = badges_update_site_backpack($backpack, (object) $data);
            } else {
                $result = badges_create_site_backpack((object)$data);
            }
        }
    }

    /**
     * Provider for test_badges_(create/update)_site_backpack
     */
    public function badges_create_site_backpack_provider() {
        return [
            "Test as admin user - creation test" => [true, true],
            "Test as admin user - update test" => [true, false],
            "Test as normal user - creation test" => [false, true],
            "Test as normal user - update test" => [false, false],
        ];
    }

    /**
     * Test the badges_open_badges_backpack_api with different backpacks
     */
    public function test_badges_open_badges_backpack_api() {
        $this->resetAfterTest();

        $data = [
            'apiversion' => 2,
            'backpackapiurl' => 'https://api.ca.badgr.io/v2',
            'backpackweburl' => 'https://ca.badgr.io',
            'sortorder' => 2,
        ];

        // Given a complete set of unique data, a new backpack and auth records should exist in the tables.
        $data['backpackemail'] = 'test@test.com';
        $data['password'] = 'test';
        $backpack1 = badges_save_external_backpack((object) $data);
        $data['backpackweburl'] = 'https://eu.badgr.io';
        $data['backpackapiurl'] = 'https://api.eu.badgr.io/v2';
        $data['apiversion'] = '2.1';
        $data['sortorder'] = 3;
        $backpack2 = badges_save_external_backpack((object) $data);

        // Move backpack2 to the first position to set it as primary site backpack.
        $this->move_backpack_to_first_position($backpack2);

        // The default response should check the default site backpack api version.
        $this->assertEquals(2.1, badges_open_badges_backpack_api());
        // Check the api version for the other backpack created.
        $this->assertEquals(2, badges_open_badges_backpack_api($backpack1));
        $this->assertEquals(2.1, badges_open_badges_backpack_api($backpack2));
    }

    /**
     * Test the badges_get_site_backpack function
     */
    public function test_badges_get_site_backpack() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $data = [
            'apiversion' => '2',
            'backpackapiurl' => 'https://api.ca.badgr.io/v2',
            'backpackweburl' => 'https://ca.badgr.io',
        ];
        $backpack1 = badges_save_external_backpack((object) $data);
        $data2 = array_merge($data, [
            'backpackapiurl' => 'https://api.eu.badgr.io/v2',
            'backpackweburl' => 'https://eu.badgr.io',
            'backpackemail' => 'test@test.com',
            'password' => 'test',
        ]);
        $backpack2 = badges_save_external_backpack((object) $data2);
        $data3 = array_merge($data2, [
            'userid' => $user->id,
            'externalbackpackid' => $backpack2,
            'backpackemail' => 'test2@test.com'
        ]);
        // In the following case, the id returned below equals backpack2. So we aren't storing it.
        badges_save_backpack_credentials((object) $data3);
        unset($data3['userid']);

        // Get a site back based on the id returned from creation and no user id provided.
        $this->assertEquals($data, array_intersect($data, (array) badges_get_site_backpack($backpack1)));
        $this->assertEquals($data2, array_intersect($data2, (array) badges_get_site_backpack($backpack2)));
        $this->assertEquals($data2, array_intersect($data2, (array) badges_get_site_backpack($backpack2, 0)));
        $this->assertEquals($data3, array_intersect($data3, (array) badges_get_site_backpack($backpack2, $user->id)));

        // Non-existent user backpack should return only configuration details and not auth details.
        $userbackpack = badges_get_site_backpack($backpack1, $user->id);
        $this->assertNull($userbackpack->badgebackpack);
        $this->assertNull($userbackpack->password);
        $this->assertNull($userbackpack->backpackemail);
    }

    /**
     * Test the badges_get_user_backpack function
     */
    public function test_badges_get_user_backpack() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $data = [
            'apiversion' => '2',
            'backpackapiurl' => 'https://api.ca.badgr.io/v2',
            'backpackweburl' => 'https://ca.badgr.io',
        ];
        $backpack1 = badges_save_external_backpack((object) $data);
        $data2 = array_merge($data, [
            'backpackapiurl' => 'https://api.eu.badgr.io/v2',
            'backpackweburl' => 'https://eu.badgr.io',
            'backpackemail' => 'test@test.com',
            'password' => 'test',
        ]);
        $backpack2 = badges_save_external_backpack((object) $data2);
        $data3 = array_merge($data2, [
            'userid' => $user->id,
            'externalbackpackid' => $backpack2,
            'backpackemail' => 'test2@test.com'
        ]);
        // In the following case, the id returned below equals backpack2. So we aren't storing it.
        badges_save_backpack_credentials((object) $data3);
        unset($data3['userid']);

        // Currently logged in as admin.
        $this->assertEquals($data2, array_intersect($data2, (array) badges_get_user_backpack()));
        $this->assertEquals($data2, array_intersect($data2, (array) badges_get_user_backpack(0)));
        $this->assertEquals($data3, array_intersect($data3, (array) badges_get_user_backpack($user->id)));

        // Non-existent user backpack should return nothing.
        $this->assertFalse(badges_get_user_backpack($backpack1, $user->id));

        // Login as user.
        $this->setUser($user);
        $this->assertEquals($data3, array_intersect($data3, (array) badges_get_user_backpack()));
    }

    /**
     * Test the badges_get_site_primary_backpack function
     *
     * @param boolean $withauth Testing with authentication or not.
     * @dataProvider badges_get_site_primary_backpack_provider
     */
    public function test_badges_get_site_primary_backpack($withauth) {
        $data = [
            'apiversion' => '2',
            'backpackapiurl' => 'https://api.ca.badgr.io/v2',
            'backpackweburl' => 'https://ca.badgr.io',
            'sortorder' => '2',
        ];
        if ($withauth) {
            $data = array_merge($data, [
                'backpackemail' => 'test@test.com',
                'password' => 'test',
            ]);
        }
        $backpack = badges_save_external_backpack((object) $data);

        // Check the backpack created is not the primary one.
        $sitebackpack = badges_get_site_primary_backpack();
        $this->assertNotEquals($backpack, $sitebackpack->id);

        // Move backpack to the first position to set it as primary site backpack.
        $this->move_backpack_to_first_position($backpack);

        $sitebackpack = badges_get_site_primary_backpack();
        $this->assertEquals($backpack, $sitebackpack->id);

        if ($withauth) {
            $this->assertEquals($data, array_intersect($data, (array) $sitebackpack));
            $this->assertEquals($data['password'], $sitebackpack->password);
            $this->assertEquals($data['backpackemail'], $sitebackpack->backpackemail);
        } else {
            $this->assertNull($sitebackpack->badgebackpack);
            $this->assertNull($sitebackpack->password);
            $this->assertNull($sitebackpack->backpackemail);
        }
    }

    /**
     * Test the test_badges_get_site_primary_backpack function.
     *
     * @return array
     */
    public function badges_get_site_primary_backpack_provider() {
        return [
            "Test with auth details" => [true],
            "Test without auth details" => [false],
        ];
    }

    /**
     * Test badges_change_sortorder_backpacks().
     *
     * @dataProvider badges_change_sortorder_backpacks_provider
     * @covers ::badges_change_sortorder_backpacks
     *
     * @param int $backpacktomove Backpack index to move (from 0 to 5).
     * @param int $direction Direction to move the backpack.
     * @param int|null $expectedsortorder Expected sortorder or null if an exception is expected.
     */
    public function test_badges_change_sortorder_backpacks(int $backpacktomove, int $direction, ?int $expectedsortorder): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create 5 more backpacks.
        for ($i = 0; $i < 5; $i++) {
            $data = new \stdClass();
            $data->apiversion = OPEN_BADGES_V2P1;
            $data->backpackapiurl = "https://myurl$i.cat/ob/v2p1";
            $data->backpackweburl = "https://myurl$i.cat";
            badges_create_site_backpack($data);
        }

        // Check there are 6 backpacks (1 pre-existing + 5 news).
        $total = $DB->count_records('badge_external_backpack');
        $this->assertEquals(6, $total);
        $backpacks = array_values(badges_get_site_backpacks());

        if (is_null($expectedsortorder)) {
            $this->expectException('moodle_exception');
        }

        // Move the backpack.
        badges_change_sortorder_backpacks($backpacks[$backpacktomove]->id, $direction);

        if (!is_null($expectedsortorder)) {
            $backpack = badges_get_site_backpack($backpacks[$backpacktomove]->id);
            $this->assertEquals($expectedsortorder, $backpack->sortorder);
        }
    }

    /**
     * Provider for test_badges_change_sortorder_backpacks.
     *
     * @return array
     */
    public function badges_change_sortorder_backpacks_provider(): array {
        return [
            "Test up" => [
                'backpacktomove' => 1,
                'direction' => BACKPACK_MOVE_UP,
                'expectedsortorder' => 1,
            ],
            "Test down" => [
                'backpacktomove' => 1,
                'direction' => BACKPACK_MOVE_DOWN,
                'expectedsortorder' => 3,
            ],
            "Test up the very first element" => [
                'backpacktomove' => 0,
                'direction' => BACKPACK_MOVE_UP,
                'expectedsortorder' => 1,
            ],
            "Test down the very last element" => [
                'backpacktomove' => 5,
                'direction' => BACKPACK_MOVE_DOWN,
                'expectedsortorder' => 6,
            ],
            "Test with an invalid direction value" => [
                'backpacktomove' => 1,
                'direction' => 10,
                'expectedsortorder' => null,
            ],
        ];
    }

    /**
     * Test the Badgr URL generator function
     *
     * @param mixed $type Type corresponding to the badge entites
     * @param string $expected Expected string result
     * @dataProvider badgr_open_url_generator
     */
    public function test_badges_generate_badgr_open_url($type, $expected) {
        $data = [
            'apiversion' => '2',
            'backpackapiurl' => 'https://api.ca.badgr.io/v2',
            'backpackweburl' => 'https://ca.badgr.io',
            'backpackemail' => 'test@test.com',
            'password' => 'test',
        ];
        $backpack2 = badges_save_external_backpack((object) $data);
        $backpack = badges_get_site_backpack($backpack2);
        $this->assertEquals($expected, badges_generate_badgr_open_url($backpack, $type, 123455));
    }

    /**
     * Data provider for test_badges_generate_badgr_open_url
     * @return array
     */
    public function badgr_open_url_generator() {
        return [
            'Badgr Assertion URL test' => [
                OPEN_BADGES_V2_TYPE_ASSERTION, "https://api.ca.badgr.io/public/assertions/123455"
            ],
            'Badgr Issuer URL test' => [
                OPEN_BADGES_V2_TYPE_ISSUER, "https://api.ca.badgr.io/public/issuers/123455"
            ],
            'Badgr Badge URL test' => [
                OPEN_BADGES_V2_TYPE_BADGE, "https://api.ca.badgr.io/public/badges/123455"
            ]
        ];
    }

    /**
     * Test badges_external_get_mapping function
     *
     * @param int $internalid The internal id of the mapping
     * @param int $externalid The external / remote ref to the mapping
     * @param mixed $expected The expected result from the function
     * @param string|null $field The field we are passing to the function. Null if we don't want to pass anything.ss
     *
     * @dataProvider badges_external_get_mapping_provider
     */
    public function test_badges_external_get_mapping($internalid, $externalid, $expected, $field = null) {
        $data = [
            'apiversion' => '2',
            'backpackapiurl' => 'https://api.ca.badgr.io/v2',
            'backpackweburl' => 'https://ca.badgr.io',
            'backpackemail' => 'test@test.com',
            'password' => 'test',
        ];
        $backpack2 = badges_save_external_backpack((object) $data);
        badges_external_create_mapping($backpack2, OPEN_BADGES_V2_TYPE_BADGE, $internalid, $externalid);
        $expected = $expected == "id" ? $backpack2 : $expected;
        if ($field) {
            $this->assertEquals($expected, badges_external_get_mapping($backpack2, OPEN_BADGES_V2_TYPE_BADGE, $internalid, $field));
        } else {
            $this->assertEquals($expected, badges_external_get_mapping($backpack2, OPEN_BADGES_V2_TYPE_BADGE, $internalid));
        }
    }

    /**
     * Data provider for badges_external_get_mapping_provider
     *
     * @return array
     */
    public function badges_external_get_mapping_provider() {
        return [
            "Get the site backpack value" => [
                1234, 4321, 'id', 'sitebackpackid'
            ],
            "Get the type of the mapping" => [
                1234, 4321, OPEN_BADGES_V2_TYPE_BADGE, 'type'
            ],
            "Get the externalid of the mapping" => [
                1234, 4321, 4321, 'externalid'
            ],
            "Get the externalid of the mapping without providing a param" => [
                1234, 4321, 4321, null
            ],
            "Get the internalid of the mapping" => [
                1234, 4321, 1234, 'internalid'
            ]
        ];
    }

    /**
     * Move the backpack to the first position, to set it as primary site backpack.
     *
     * @param int $backpackid The backpack identifier.
     */
    private function move_backpack_to_first_position(int $backpackid): void {
        $backpack = badges_get_site_backpack($backpackid);
        while ($backpack->sortorder > 1) {
            badges_change_sortorder_backpacks($backpackid, BACKPACK_MOVE_UP);
            $backpack = badges_get_site_backpack($backpackid);
        }
    }
}
