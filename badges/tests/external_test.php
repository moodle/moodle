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
 * Badges external functions tests.
 *
 * @package    core_badges
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Badges external functions tests
 *
 * @package    core_badges
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class core_badges_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');

        // Mock up a site badge.
        $now = time();
        $badge = new stdClass();
        $badge->id = null;
        $badge->name = "Test badge site";
        $badge->description = "Testing badges site";
        $badge->timecreated = $now - 12;
        $badge->timemodified = $now - 12;
        $badge->usercreated = $this->teacher->id;
        $badge->usermodified = $this->teacher->id;
        $badge->issuername = "Test issuer";
        $badge->issuerurl = "http://issuer-url.domain.co.nz";
        $badge->issuercontact = "issuer@example.com";
        $badge->expiredate = null;
        $badge->expireperiod = null;
        $badge->type = BADGE_TYPE_SITE;
        $badge->courseid = null;
        $badge->messagesubject = "Test message subject for badge";
        $badge->message = "Test message body for badge";
        $badge->attachment = 1;
        $badge->notification = 0;
        $badge->status = BADGE_STATUS_ACTIVE;

        $badgeid = $DB->insert_record('badge', $badge, true);
        $badge = new badge($badgeid);
        $badge->issue($this->student->id, true);

        // Hack the database to adjust the time each badge was issued.
        $DB->set_field('badge_issued', 'dateissued', $now - 11, array('userid' => $this->student->id, 'badgeid' => $badgeid));

        // Now a course badge.
        $badge->id = null;
        $badge->name = "Test badge course";
        $badge->description = "Testing badges course";
        $badge->type = BADGE_TYPE_COURSE;
        $badge->courseid = $this->course->id;

        $badgeid = $DB->insert_record('badge', $badge, true);
        $badge = new badge($badgeid);
        $badge->issue($this->student->id, true);

        // Hack the database to adjust the time each badge was issued.
        $DB->set_field('badge_issued', 'dateissued', $now - 11, array('userid' => $this->student->id, 'badgeid' => $badgeid));
    }

    /**
     * Test get user badges.
     * These is a basic test since the badges_get_my_user_badges used by the external function already has unit tests.
     */
    public function test_get_my_user_badges() {

        $this->setUser($this->student);

        $result = core_badges_external::get_user_badges();
        $result = external_api::clean_returnvalue(core_badges_external::get_user_badges_returns(), $result);
        $this->assertCount(2, $result['badges']);

        // Pagination and filtering.
        $result = core_badges_external::get_user_badges(0, $this->course->id, 0, 1, '', true);
        $result = external_api::clean_returnvalue(core_badges_external::get_user_badges_returns(), $result);
        $this->assertCount(1, $result['badges']);
        $this->assertEquals($this->course->id, $result['badges'][0]['courseid']);
    }

    /**
     * Test get user badges.
     */
    public function test_get_other_user_badges() {

        $this->setUser($this->teacher);

        $result = core_badges_external::get_user_badges($this->student->id);
        $result = external_api::clean_returnvalue(core_badges_external::get_user_badges_returns(), $result);

        $this->assertCount(2, $result['badges']);

        // Check that we don't have permissions for view the complete information for site badges.
        foreach ($result['badges'] as $badge) {
            if (isset($badge['type']) and $badge['type'] == BADGE_TYPE_COURSE) {
                $this->assertTrue(isset($badge['message']));
            } else {
                $this->assertFalse(isset($badge['message']));
            }
        }
    }
}
