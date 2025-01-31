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

namespace core_badges\tests;

use badge;
use core_tag_tag;
use stdClass;

/**
 * Unit tests for badges
 *
 * @package    core_badges
 * @copyright  2013 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */
abstract class badges_testcase extends \advanced_testcase {
    protected $badgeid;
    protected $course;
    protected $user;
    protected $module;
    protected $coursebadge;
    protected $assertion;

    /** @var $assertion2 to define json format for Open badge version 2 */
    protected $assertion2;

    #[\Override]
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        static::load_requirements();
    }

    /**
     * Helper to load class dependencies.
     *
     * Note: This must be called in any data providers.
     */
    protected static function load_requirements(): void {
        global $CFG;

        require_once($CFG->libdir . '/badgeslib.php');
        require_once($CFG->dirroot . '/badges/lib.php');
    }

    #[\Override]
    protected function setUp(): void {
        global $DB, $CFG;
        parent::setUp();
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

        // Insert tags.
        core_tag_tag::set_item_tags('core_badges', 'badge', $badge->id, $badge->get_context(), ['tag1', 'tag2']);

        $this->assertion = new stdClass();
        $this->assertion->badge = '{"uid":"%s","recipient":{"identity":"%s","type":"email","hashed":true,"salt":"%s"},' .
            '"badge":"%s","verify":{"type":"hosted","url":"%s"},"issuedOn":"%d","evidence":"%s","tags":%s}';
        $this->assertion->class = '{"name":"%s","description":"%s","image":"%s","criteria":"%s","issuer":"%s","tags":%s}';
        $this->assertion->issuer = '{"name":"%s","url":"%s","email":"%s"}';
        // Format JSON-LD for Openbadge specification version 2.0.
        $this->assertion2 = new stdClass();
        $this->assertion2->badge = '{"recipient":{"identity":"%s","type":"email","hashed":true,"salt":"%s"},' .
            '"badge":{"name":"%s","description":"%s","image":"%s",' .
            '"criteria":{"id":"%s","narrative":"%s"},"issuer":{"name":"%s","url":"%s","email":"%s",' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"Issuer"},' .
            '"tags":%s,"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"BadgeClass","version":"%s",' .
            '"@language":"en","related":[{"id":"%s","version":"%s","@language":"%s"}],' .
            '"alignments":[{"targetName":"%s","targetUrl":"%s","targetDescription":"%s","targetFramework":"%s",' .
            '"targetCode":"%s"}]},"verify":{"type":"hosted","url":"%s"},"issuedOn":"%s","evidence":"%s","tags":%s,' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","type":"Assertion","id":"%s"}';

        $this->assertion2->class = '{"name":"%s","description":"%s","image":"%s",' .
            '"criteria":{"id":"%s","narrative":"%s"},"issuer":{"name":"%s","url":"%s","email":"%s",' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"Issuer"},' .
            '"tags":%s,"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"BadgeClass","version":"%s",' .
            '"@language":"%s","related":[{"id":"%s","version":"%s","@language":"%s"}],' .
            '"alignments":[{"targetName":"%s","targetUrl":"%s","targetDescription":"%s","targetFramework":"%s",' .
            '"targetCode":"%s"}]}';
        $this->assertion2->issuer = '{"name":"%s","url":"%s","email":"%s",' .
            '"@context":"https:\/\/w3id.org\/openbadges\/v2","id":"%s","type":"Issuer"}';
    }
}
