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

namespace core\moodlenet;

use context_course;
use stdClass;
use testing_data_generator;

/**
 * Unit tests for {@see utilities}.
 *
 * @coversDefaultClass \core\moodlenet\utilities
 * @package core
 * @copyright 2023 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utilities_test extends \advanced_testcase {

    /** @var testing_data_generator Data generator. */
    private testing_data_generator $generator;

    /** @var stdClass Activity object, */
    private stdClass $course;

    /** @var context_course Course context instance. */
    private context_course $coursecontext;

    /**
     * Set up function for tests.
     */
    protected function setUp(): void {
        parent::setUp();

        $this->resetAfterTest();
        $this->generator = $this->getDataGenerator();
        $this->course = $this->generator->create_course();
        $this->coursecontext = context_course::instance($this->course->id);
    }

    /**
     * Test is_valid_instance method.
     *
     * @covers ::is_valid_instance
     * @return void
     */
    public function test_is_valid_instance() {
        global $CFG;
        $this->setAdminUser();

        // Create dummy issuer.
        $issuer = new \core\oauth2\issuer(0);
        $issuer->set('enabled', 0);
        $issuer->set('servicetype', 'google');

        // Can not share if the experimental flag it set to false.
        $CFG->enablesharingtomoodlenet = false;
        $this->assertFalse(utilities::is_valid_instance($issuer));

        // Enable the experimental flag.
        $CFG->enablesharingtomoodlenet = true;

        // Can not share if the OAuth 2 service in the outbound setting is not matched the given one.
        set_config('oauthservice', random_int(1, 30), 'moodlenet');
        $this->assertFalse(utilities::is_valid_instance($issuer));

        // Can not share if the OAuth 2 service in the outbound setting is not enabled.
        set_config('oauthservice', $issuer->get('id'), 'moodlenet');
        $this->assertFalse(utilities::is_valid_instance($issuer));

        // Can not share if the OAuth 2 service type is not moodlenet.
        $issuer->set('enabled', 1);
        $this->assertFalse(utilities::is_valid_instance($issuer));

        // All good now.
        $issuer->set('servicetype', 'moodlenet');
        $this->assertTrue(utilities::is_valid_instance($issuer));
    }


    /**
     * Test can_user_share method.
     *
     * @covers ::can_user_share
     * @return void
     */
    public function test_can_user_share() {
        global $DB;

        // Generate data.
        $student1 = $this->generator->create_user();
        $teacher1 = $this->generator->create_user();
        $teacher2 = $this->generator->create_user();
        $manager1 = $this->generator->create_user();

        // Enrol users.
        $this->generator->enrol_user($student1->id, $this->course->id, 'student');
        $this->generator->enrol_user($teacher1->id, $this->course->id, 'teacher');
        $this->generator->enrol_user($teacher2->id, $this->course->id, 'editingteacher');
        $this->generator->enrol_user($manager1->id, $this->course->id, 'manager');

        // Get roles.
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher'], 'id', MUST_EXIST);
        $editingteacherrole = $DB->get_record('role', ['shortname' => 'editingteacher'], 'id', MUST_EXIST);

        // Test with default settings.
        // Student and Teacher cannot share the activity.
        $this->assertFalse(utilities::can_user_share($this->coursecontext, $student1->id));
        $this->assertFalse(utilities::can_user_share($this->coursecontext, $teacher1->id));
        // Editing-teacher and Manager can share the activity.
        $this->assertTrue(utilities::can_user_share($this->coursecontext, $teacher2->id));
        $this->assertTrue(utilities::can_user_share($this->coursecontext, $manager1->id));

        // Teacher who has the capabilities can share the activity.
        assign_capability('moodle/moodlenet:shareactivity', CAP_ALLOW, $teacherrole->id, $this->coursecontext);
        assign_capability('moodle/backup:backupactivity', CAP_ALLOW, $teacherrole->id, $this->coursecontext);
        $this->assertTrue(utilities::can_user_share($this->coursecontext, $teacher1->id));

        // Editing-teacher who does not have the capabilities can not share the activity.
        assign_capability('moodle/moodlenet:shareactivity', CAP_PROHIBIT, $editingteacherrole->id, $this->coursecontext);
        $this->assertFalse(utilities::can_user_share($this->coursecontext, $teacher2->id));
    }
}
