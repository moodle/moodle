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
 * SCORM module external functions tests
 *
 * @package    mod_scorm
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/scorm/lib.php');

/**
 * SCORM module external functions tests
 *
 * @package    mod_scorm
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_scorm_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $this->course->id));
        $this->context = context_module::instance($this->scorm->cmid);
        $this->cm = get_coursemodule_from_instance('scorm', $this->scorm->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }

    /**
     * Test view_scorm
     */
    public function test_view_scorm() {
        global $DB;

        // Test invalid instance id.
        try {
            mod_scorm_external::view_scorm(0);
            $this->fail('Exception expected due to invalid mod_scorm instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            mod_scorm_external::view_scorm($this->scorm->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $this->course->id, $this->studentrole->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_scorm_external::view_scorm($this->scorm->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::view_scorm_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_scorm\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/scorm/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test get scorm attempt count
     */
    public function test_mod_scorm_get_scorm_attempt_count_own_empty() {
        // Set to the student user.
        self::setUser($this->student);

        // Retrieve my attempts (should be 0).
        $result = mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_attempt_count_returns(), $result);
        $this->assertEquals(0, $result['attemptscount']);
    }

    public function test_mod_scorm_get_scorm_attempt_count_own_with_complete() {
        // Set to the student user.
        self::setUser($this->student);

        // Create attempts.
        $scoes = scorm_get_scoes($this->scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 2, 'cmi.core.lesson_status', 'completed');

        $result = mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_attempt_count_returns(), $result);
        $this->assertEquals(2, $result['attemptscount']);
    }

    public function test_mod_scorm_get_scorm_attempt_count_own_incomplete() {
        // Set to the student user.
        self::setUser($this->student);

        // Create a complete attempt, and an incomplete attempt.
        $scoes = scorm_get_scoes($this->scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 2, 'cmi.core.credit', '0');

        $result = mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id, true);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_attempt_count_returns(), $result);
        $this->assertEquals(1, $result['attemptscount']);
    }

    public function test_mod_scorm_get_scorm_attempt_count_others_as_teacher() {
        // As a teacher.
        self::setUser($this->teacher);

        // Create a completed attempt for student.
        $scoes = scorm_get_scoes($this->scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');

        // I should be able to view the attempts for my students.
        $result = mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_attempt_count_returns(), $result);
        $this->assertEquals(1, $result['attemptscount']);
    }

    public function test_mod_scorm_get_scorm_attempt_count_others_as_student() {
        // Create a second student.
        $student2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $this->course->id, $this->studentrole->id, 'manual');

        // As a student.
        self::setUser($student2);

        // I should not be able to view the attempts of another student.
        $this->setExpectedException('required_capability_exception');
        mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
    }

    public function test_mod_scorm_get_scorm_attempt_count_invalid_instanceid() {
        // As student.
        self::setUser($this->student);

        // Test invalid instance id.
        $this->setExpectedException('moodle_exception');
        mod_scorm_external::get_scorm_attempt_count(0, $this->student->id);
    }

    public function test_mod_scorm_get_scorm_attempt_count_invalid_userid() {
        // As student.
        self::setUser($this->student);

        $this->setExpectedException('moodle_exception');
        mod_scorm_external::get_scorm_attempt_count($this->scorm->id, -1);
    }

    /**
     * Test get scorm scoes
     */
    public function test_mod_scorm_get_scorm_scoes() {
        global $DB;

        $this->resetAfterTest(true);

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Set to the student user.
        self::setUser($student);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        // First scorm, dates restriction.
        $record = new stdClass();
        $record->course = $course->id;
        $record->timeopen = time() + DAYSECS;
        $record->timeclose = $record->timeopen + DAYSECS;
        $scorm = self::getDataGenerator()->create_module('scorm', $record);

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Retrieve my scoes, warning!.
        try {
             mod_scorm_external::get_scorm_scoes($scorm->id);
            $this->fail('Exception expected due to invalid dates.');
        } catch (moodle_exception $e) {
            $this->assertEquals('notopenyet', $e->errorcode);
        }

        $scorm->timeopen = time() - DAYSECS;
        $scorm->timeclose = time() - HOURSECS;
        $DB->update_record('scorm', $scorm);

        try {
             mod_scorm_external::get_scorm_scoes($scorm->id);
            $this->fail('Exception expected due to invalid dates.');
        } catch (moodle_exception $e) {
            $this->assertEquals('expired', $e->errorcode);
        }

        // Retrieve my scoes, user with permission.
        self::setUser($teacher);
        $result = mod_scorm_external::get_scorm_scoes($scorm->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_scoes_returns(), $result);
        $this->assertCount(2, $result['scoes']);
        $this->assertCount(0, $result['warnings']);

        $scoes = scorm_get_scoes($scorm->id);
        $sco = array_shift($scoes);
        $this->assertEquals((array) $sco, $result['scoes'][0]);

        $sco = array_shift($scoes);
        // Remove specific sco data.
        unset($sco->isvisible);
        unset($sco->parameters);
        $this->assertEquals((array) $sco, $result['scoes'][1]);

        // Use organization.
        $organization = 'golf_sample_default_org';
        $result = mod_scorm_external::get_scorm_scoes($scorm->id, $organization);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_scoes_returns(), $result);
        $this->assertCount(1, $result['scoes']);
        $this->assertEquals($organization, $result['scoes'][0]['organization']);
        $this->assertCount(0, $result['warnings']);

        // Test invalid instance id.
        try {
             mod_scorm_external::get_scorm_scoes(0);
            $this->fail('Exception expected due to invalid instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }
    }

    /*
     * Test get scorm user data
     */
    public function test_mod_scorm_get_scorm_user_data() {
        global $DB;

        $this->resetAfterTest(true);

        // Create users.
        $student1 = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Set to the student user.
        self::setUser($student1);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        // First scorm.
        $record = new stdClass();
        $record->course = $course->id;
        $scorm = self::getDataGenerator()->create_module('scorm', $record);

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Create attempts.
        $scoes = scorm_get_scoes($scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($student1->id, $scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');
        scorm_insert_track($student1->id, $scorm->id, $sco->id, 1, 'cmi.core.score.raw', '80');
        scorm_insert_track($student1->id, $scorm->id, $sco->id, 2, 'cmi.core.lesson_status', 'completed');

        $result = mod_scorm_external::get_scorm_user_data($scorm->id, 1);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_user_data_returns(), $result);
        $this->assertCount(2, $result['data']);
        // Find our tracking data.
        $found = 0;
        foreach ($result['data'] as $scodata) {
            foreach ($scodata['userdata'] as $userdata) {
                if ($userdata['element'] == 'cmi.core.lesson_status' and $userdata['value'] == 'completed') {
                    $found++;
                }
                if ($userdata['element'] == 'cmi.core.score.raw' and $userdata['value'] == '80') {
                    $found++;
                }
            }
        }
        $this->assertEquals(2, $found);

        // Test invalid instance id.
        try {
             mod_scorm_external::get_scorm_user_data(0, 1);
            $this->fail('Exception expected due to invalid instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }
    }

    /**
     * Test insert scorm tracks
     */
    public function test_mod_scorm_insert_scorm_tracks() {
        global $DB;

        $this->resetAfterTest(true);

        // Create users.
        $student = self::getDataGenerator()->create_user();

        // Set to the student user.
        self::setUser($student);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        // First scorm, dates restriction.
        $record = new stdClass();
        $record->course = $course->id;
        $record->timeopen = time() + DAYSECS;
        $record->timeclose = $record->timeopen + DAYSECS;
        $scorm = self::getDataGenerator()->create_module('scorm', $record);

        // Get a SCO.
        $scoes = scorm_get_scoes($scorm->id);
        $sco = array_shift($scoes);

        // Tracks.
        $tracks = array();
        $tracks[] = array(
            'element' => 'cmi.core.lesson_status',
            'value' => 'completed'
        );
        $tracks[] = array(
            'element' => 'cmi.core.score.raw',
            'value' => '80'
        );

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');

        // Exceptions first.
        try {
            mod_scorm_external::insert_scorm_tracks($sco->id, 1, $tracks);
            $this->fail('Exception expected due to dates');
        } catch (moodle_exception $e) {
            $this->assertEquals('notopenyet', $e->errorcode);
        }

        $scorm->timeopen = time() - DAYSECS;
        $scorm->timeclose = time() - HOURSECS;
        $DB->update_record('scorm', $scorm);

        try {
            mod_scorm_external::insert_scorm_tracks($sco->id, 1, $tracks);
            $this->fail('Exception expected due to dates');
        } catch (moodle_exception $e) {
            $this->assertEquals('expired', $e->errorcode);
        }

        // Test invalid instance id.
        try {
             mod_scorm_external::insert_scorm_tracks(0, 1, $tracks);
            $this->fail('Exception expected due to invalid sco id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotfindsco', $e->errorcode);
        }

        $scorm->timeopen = 0;
        $scorm->timeclose = 0;
        $DB->update_record('scorm', $scorm);

        // Retrieve my tracks.
        $result = mod_scorm_external::insert_scorm_tracks($sco->id, 1, $tracks);
        $result = external_api::clean_returnvalue(mod_scorm_external::insert_scorm_tracks_returns(), $result);
        $this->assertCount(0, $result['warnings']);

        $trackids = $DB->get_records('scorm_scoes_track', array('userid' => $student->id, 'scoid' => $sco->id,
                                                                'scormid' => $scorm->id, 'attempt' => 1));
        // We use asort here to prevent problems with ids ordering.
        $expectedkeys = array_keys($trackids);
        $this->assertEquals(asort($expectedkeys), asort($result['trackids']));
    }

    /**
     * Test get scorm sco tracks
     */
    public function test_mod_scorm_get_scorm_sco_tracks() {
        global $DB;

        $this->resetAfterTest(true);

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $otherstudent = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Set to the student user.
        self::setUser($student);

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        // First scorm.
        $record = new stdClass();
        $record->course = $course->id;
        $scorm = self::getDataGenerator()->create_module('scorm', $record);

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Create attempts.
        $scoes = scorm_get_scoes($scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($student->id, $scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');
        scorm_insert_track($student->id, $scorm->id, $sco->id, 1, 'cmi.core.score.raw', '80');
        scorm_insert_track($student->id, $scorm->id, $sco->id, 2, 'cmi.core.lesson_status', 'completed');

        $result = mod_scorm_external::get_scorm_sco_tracks($sco->id, $student->id, 1);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_sco_tracks_returns(), $result);
        // 7 default elements + 2 custom ones.
        $this->assertCount(9, $result['data']['tracks']);
        $this->assertEquals(1, $result['data']['attempt']);
        // Find our tracking data.
        $found = 0;
        foreach ($result['data']['tracks'] as $userdata) {
            if ($userdata['element'] == 'cmi.core.lesson_status' and $userdata['value'] == 'completed') {
                $found++;
            }
            if ($userdata['element'] == 'cmi.core.score.raw' and $userdata['value'] == '80') {
                $found++;
            }
        }
        $this->assertEquals(2, $found);

        // Capabilities check.
        try {
             mod_scorm_external::get_scorm_sco_tracks($sco->id, $otherstudent->id);
            $this->fail('Exception expected due to invalid instance id.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        self::setUser($teacher);
        // Ommit the attempt parameter, the function should calculate the last attempt.
        $result = mod_scorm_external::get_scorm_sco_tracks($sco->id, $student->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_sco_tracks_returns(), $result);
        // 7 default elements + 1 custom one.
        $this->assertCount(8, $result['data']['tracks']);
        $this->assertEquals(2, $result['data']['attempt']);

        // Test invalid instance id.
        try {
             mod_scorm_external::get_scorm_sco_tracks(0, 1);
            $this->fail('Exception expected due to invalid instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotfindsco', $e->errorcode);
        }
        // Invalid user.
        try {
             mod_scorm_external::get_scorm_sco_tracks($sco->id, 0);
            $this->fail('Exception expected due to invalid instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invaliduser', $e->errorcode);
        }
    }
}
