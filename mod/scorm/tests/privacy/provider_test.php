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
 * Base class for unit tests for mod_scorm.
 *
 * @package    mod_scorm
 * @category   test
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_scorm\privacy;

defined('MOODLE_INTERNAL') || die();

use mod_scorm\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;

/**
 * Unit tests for mod\scorm\classes\privacy\provider.php
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /** @var stdClass User without any AICC/SCORM attempt. */
    protected $student0;

    /** @var stdClass User with some AICC/SCORM attempt. */
    protected $student1;

    /** @var stdClass User with some AICC/SCORM attempt. */
    protected $student2;

    /** @var context context_module of the SCORM activity. */
    protected $context;

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->scorm_setup_test_scenario_data();

        // The student0 hasn't any attempt.
        $contextlist = provider::get_contexts_for_userid($this->student0->id);
        $this->assertCount(0, (array) $contextlist->get_contextids());

        // The student1 has data in the SCORM context.
        $contextlist = provider::get_contexts_for_userid($this->student1->id);
        $this->assertCount(1, (array) $contextlist->get_contextids());
        $this->assertContainsEquals($this->context->id, $contextlist->get_contextids());
    }

    /**
     * Test getting the user IDs for the context related to this plugin.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->scorm_setup_test_scenario_data();
        $component = 'mod_scorm';

        $userlist = new \core_privacy\local\request\userlist($this->context, $component);
        provider::get_users_in_context($userlist);

        // Students 1 and 2 have attempts in the SCORM context, student 0 does not.
        $this->assertCount(2, $userlist);

        $expected = [$this->student1->id, $this->student2->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->scorm_setup_test_scenario_data();

        // Validate exported data for student0 (without any AICC/SCORM attempt).
        $this->setUser($this->student0);
        $writer = writer::with_context($this->context);

        $this->export_context_data_for_user($this->student0->id, $this->context, 'mod_scorm');
        $subcontextattempt1 = [
            get_string('myattempts', 'scorm'),
            get_string('attempt', 'scorm'). " 1"
        ];
        $subcontextaicc = [
            get_string('myaiccsessions', 'scorm')
        ];
        $data = $writer->get_data($subcontextattempt1);
        $this->assertEmpty($data);
        $data = $writer->get_data($subcontextaicc);
        $this->assertEmpty($data);

        // Validate exported data for student1.
        writer::reset();
        $this->setUser($this->student1);
        $writer = writer::with_context($this->context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($this->student1->id, $this->context, 'mod_scorm');

        $data = $writer->get_data([]);
        $this->assertEquals('SCORM1', $data->name);

        $data = $writer->get_data($subcontextattempt1);
        $this->assertCount(1, (array) $data);
        $this->assertCount(2, (array) reset($data));
        $subcontextattempt2 = [
            get_string('myattempts', 'scorm'),
            get_string('attempt', 'scorm'). " 2"
        ];
        $data = $writer->get_data($subcontextattempt2);
        $this->assertCount(2, (array) reset($data));
        // The student1 has only 2 scoes_track attempts.
        $subcontextattempt3 = [
            get_string('myattempts', 'scorm'),
            get_string('attempt', 'scorm'). " 3"
        ];
        $data = $writer->get_data($subcontextattempt3);
        $this->assertEmpty($data);
        // The student1 has only 1 aicc_session.
        $data = $writer->get_data($subcontextaicc);
        $this->assertCount(1, (array) $data);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->scorm_setup_test_scenario_data();

        // Before deletion, we should have 8 entries in the scorm_scoes_track table.
        $count = $DB->count_records('scorm_scoes_track');
        $this->assertEquals(8, $count);
        // Before deletion, we should have 4 entries in the scorm_aicc_session table.
        $count = $DB->count_records('scorm_aicc_session');
        $this->assertEquals(4, $count);

        // Delete data based on the context.
        provider::delete_data_for_all_users_in_context($this->context);

        // After deletion, the scorm_scoes_track entries should have been deleted.
        $count = $DB->count_records('scorm_scoes_track');
        $this->assertEquals(0, $count);
        // After deletion, the scorm_aicc_session entries should have been deleted.
        $count = $DB->count_records('scorm_aicc_session');
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->scorm_setup_test_scenario_data();

        // Before deletion, we should have 8 entries in the scorm_scoes_track table.
        $count = $DB->count_records('scorm_scoes_track');
        $this->assertEquals(8, $count);
        // Before deletion, we should have 4 entries in the scorm_aicc_session table.
        $count = $DB->count_records('scorm_aicc_session');
        $this->assertEquals(4, $count);

        $approvedcontextlist = new approved_contextlist($this->student1, 'scorm', [$this->context->id]);
        provider::delete_data_for_user($approvedcontextlist);

        // After deletion, the scorm_scoes_track entries for the first student should have been deleted.
        $count = $DB->count_records('scorm_scoes_track', ['userid' => $this->student1->id]);
        $this->assertEquals(0, $count);
        $count = $DB->count_records('scorm_scoes_track');
        $this->assertEquals(4, $count);
        // After deletion, the scorm_aicc_session entries for the first student should have been deleted.
        $count = $DB->count_records('scorm_aicc_session', ['userid' => $this->student1->id]);
        $this->assertEquals(0, $count);
        $count = $DB->count_records('scorm_aicc_session');
        $this->assertEquals(2, $count);

        // Confirm that the SCORM hasn't been removed.
        $scormcount = $DB->get_records('scorm');
        $this->assertCount(1, (array) $scormcount);

        // Delete scoes_track for student0 (nothing has to be removed).
        $approvedcontextlist = new approved_contextlist($this->student0, 'scorm', [$this->context->id]);
        provider::delete_data_for_user($approvedcontextlist);
        $count = $DB->count_records('scorm_scoes_track');
        $this->assertEquals(4, $count);
        $count = $DB->count_records('scorm_aicc_session');
        $this->assertEquals(2, $count);
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;
        $component = 'mod_scorm';

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->scorm_setup_test_scenario_data();

        // Before deletion, we should have 8 entries in the scorm_scoes_track table.
        $count = $DB->count_records('scorm_scoes_track');
        $this->assertEquals(8, $count);
        // Before deletion, we should have 4 entries in the scorm_aicc_session table.
        $count = $DB->count_records('scorm_aicc_session');
        $this->assertEquals(4, $count);

        // Delete only student 1's data, retain student 2's data.
        $approveduserids = [$this->student1->id];
        $approvedlist = new approved_userlist($this->context, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // After deletion, the scorm_scoes_track entries for the first student should have been deleted.
        $count = $DB->count_records('scorm_scoes_track', ['userid' => $this->student1->id]);
        $this->assertEquals(0, $count);
        $count = $DB->count_records('scorm_scoes_track');
        $this->assertEquals(4, $count);

        // After deletion, the scorm_aicc_session entries for the first student should have been deleted.
        $count = $DB->count_records('scorm_aicc_session', ['userid' => $this->student1->id]);
        $this->assertEquals(0, $count);
        $count = $DB->count_records('scorm_aicc_session');
        $this->assertEquals(2, $count);

        // Confirm that the SCORM hasn't been removed.
        $scormcount = $DB->get_records('scorm');
        $this->assertCount(1, (array) $scormcount);

        // Delete scoes_track for student0 (nothing has to be removed).
        $approveduserids = [$this->student0->id];
        $approvedlist = new approved_userlist($this->context, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        $count = $DB->count_records('scorm_scoes_track');
        $this->assertEquals(4, $count);
        $count = $DB->count_records('scorm_aicc_session');
        $this->assertEquals(2, $count);
    }

    /**
     * Helper function to setup 3 users and 2 SCORM attempts for student1 and student2.
     * $this->student0 is always created without any attempt.
     */
    protected function scorm_setup_test_scenario_data() {
        global $DB;

        set_config('allowaicchacp', 1, 'scorm');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $params = array('course' => $course->id, 'name' => 'SCORM1');
        $scorm = $this->getDataGenerator()->create_module('scorm', $params);
        $this->context = \context_module::instance($scorm->cmid);

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Create student0 withot any SCORM attempt.
        $this->student0 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($this->student0->id, $course->id, $studentrole->id, 'manual');

        // Create student1 with 2 SCORM attempts and 1 AICC session.
        $this->student1 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($this->student1->id, $course->id, $studentrole->id, 'manual');
        static::scorm_insert_attempt($scorm, $this->student1->id, 1);
        static::scorm_insert_attempt($scorm, $this->student1->id, 2);

        // Create student2 with 2 SCORM attempts and 1 AICC session.
        $this->student2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($this->student2->id, $course->id, $studentrole->id, 'manual');
        static::scorm_insert_attempt($scorm, $this->student2->id, 1);
        static::scorm_insert_attempt($scorm, $this->student2->id, 2);
    }

    /**
     * Create a SCORM attempt.
     *
     * @param  object $scorm SCORM activity.
     * @param  int $userid  Userid who is doing the attempt.
     * @param  int $attempt Number of attempt.
     */
    protected function scorm_insert_attempt($scorm, $userid, $attempt) {
        global $DB;

        $newattempt = 'on';
        $mode = 'normal';
        scorm_check_mode($scorm, $newattempt, $attempt, $userid, $mode);
        $scoes = scorm_get_scoes($scorm->id);
        $sco = array_pop($scoes);
        scorm_insert_track($userid, $scorm->id, $sco->id, $attempt, 'cmi.core.lesson_status', 'completed');
        scorm_insert_track($userid, $scorm->id, $sco->id, $attempt, 'cmi.score.min', '0');
        $now = time();
        $hacpsession = [
            'scormid' => $scorm->id,
            'attempt' => $attempt,
            'hacpsession' => random_string(20),
            'userid' => $userid,
            'timecreated' => $now,
            'timemodified' => $now
        ];
        $DB->insert_record('scorm_aicc_session', $hacpsession);
    }
}
