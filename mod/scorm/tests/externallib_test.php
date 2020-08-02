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
    public function setUp(): void {
        global $DB, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;
        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $this->scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $this->course->id),
            array('completion' => 2, 'completionview' => 1));
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

    /**
     * @expectedException required_capability_exception
     */
    public function test_mod_scorm_get_scorm_attempt_count_others_as_student() {
        // Create a second student.
        $student2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $this->course->id, $this->studentrole->id, 'manual');

        // As a student.
        self::setUser($student2);

        // I should not be able to view the attempts of another student.
        mod_scorm_external::get_scorm_attempt_count($this->scorm->id, $this->student->id);
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_mod_scorm_get_scorm_attempt_count_invalid_instanceid() {
        // As student.
        self::setUser($this->student);

        // Test invalid instance id.
        mod_scorm_external::get_scorm_attempt_count(0, $this->student->id);
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_mod_scorm_get_scorm_attempt_count_invalid_userid() {
        // As student.
        self::setUser($this->student);

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

        // Create courses to add the modules.
        $course = self::getDataGenerator()->create_course();

        // First scorm, dates restriction.
        $record = new stdClass();
        $record->course = $course->id;
        $record->timeopen = time() + DAYSECS;
        $record->timeclose = $record->timeopen + DAYSECS;
        $scorm = self::getDataGenerator()->create_module('scorm', $record);

        // Set to the student user.
        self::setUser($student);

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
        $sco->extradata = array();
        $this->assertEquals((array) $sco, $result['scoes'][0]);

        $sco = array_shift($scoes);
        $sco->extradata = array();
        $sco->extradata[] = array(
            'element' => 'isvisible',
            'value' => $sco->isvisible
        );
        $sco->extradata[] = array(
            'element' => 'parameters',
            'value' => $sco->parameters
        );
        unset($sco->isvisible);
        unset($sco->parameters);

        // Sort the array (if we don't sort tests will fails for Postgres).
        usort($result['scoes'][1]['extradata'], function($a, $b) {
            return strcmp($a['element'], $b['element']);
        });

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

    /**
     * Test get scorm scoes (with a complex SCORM package)
     */
    public function test_mod_scorm_get_scorm_scoes_complex_package() {
        global $CFG;

        // As student.
        self::setUser($this->student);

        $record = new stdClass();
        $record->course = $this->course->id;
        $record->packagefilepath = $CFG->dirroot.'/mod/scorm/tests/packages/complexscorm.zip';
        $scorm = self::getDataGenerator()->create_module('scorm', $record);

        $result = mod_scorm_external::get_scorm_scoes($scorm->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_scoes_returns(), $result);
        $this->assertCount(9, $result['scoes']);
        $this->assertCount(0, $result['warnings']);

        $expectedscoes = array();
        $scoreturnstructure = mod_scorm_external::get_scorm_scoes_returns();
        $scoes = scorm_get_scoes($scorm->id);
        foreach ($scoes as $sco) {
            $sco->extradata = array();
            foreach ($sco as $element => $value) {
                // Add the extra data to the extradata array and remove the object element.
                if (!isset($scoreturnstructure->keys['scoes']->content->keys[$element])) {
                    $sco->extradata[] = array(
                        'element' => $element,
                        'value' => $value
                    );
                    unset($sco->{$element});
                }
            }
            $expectedscoes[] = (array) $sco;
        }

        $this->assertEquals($expectedscoes, $result['scoes']);
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

        // Set to the student user.
        self::setUser($student);

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
        $this->assertCount(0, $result['warnings']);
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

        // Try invalid attempt.
        $result = mod_scorm_external::get_scorm_sco_tracks($sco->id, $student->id, 10);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_sco_tracks_returns(), $result);
        $this->assertCount(0, $result['data']['tracks']);
        $this->assertEquals(10, $result['data']['attempt']);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('notattempted', $result['warnings'][0]['warningcode']);

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

    /*
     * Test get scorms by courses
     */
    public function test_mod_scorm_get_scorms_by_courses() {
        global $DB;

        $this->resetAfterTest(true);

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Set to the student user.
        self::setUser($student);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // First scorm.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $record->hidetoc = 2;
        $record->displayattemptstatus = 2;
        $record->skipview = 2;
        $scorm1 = self::getDataGenerator()->create_module('scorm', $record);

        // Second scorm.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course2->id;
        $scorm2 = self::getDataGenerator()->create_module('scorm', $record);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        // Users enrolments.
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course1->id, $teacherrole->id, 'manual');

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $student->id, $studentrole->id);

        $returndescription = mod_scorm_external::get_scorms_by_courses_returns();

        // Test open/close dates.

        $timenow = time();
        $scorm1->timeopen = $timenow - DAYSECS;
        $scorm1->timeclose = $timenow - HOURSECS;
        $DB->update_record('scorm', $scorm1);

        $result = mod_scorm_external::get_scorms_by_courses(array($course1->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertCount(1, $result['warnings']);
        // Only 'id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles'.
        $this->assertCount(7, $result['scorms'][0]);
        $this->assertEquals('expired', $result['warnings'][0]['warningcode']);

        $scorm1->timeopen = $timenow + DAYSECS;
        $scorm1->timeclose = $scorm1->timeopen + DAYSECS;
        $DB->update_record('scorm', $scorm1);

        $result = mod_scorm_external::get_scorms_by_courses(array($course1->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertCount(1, $result['warnings']);
        // Only 'id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles'.
        $this->assertCount(7, $result['scorms'][0]);
        $this->assertEquals('notopenyet', $result['warnings'][0]['warningcode']);

        // Reset times.
        $scorm1->timeopen = 0;
        $scorm1->timeclose = 0;
        $DB->update_record('scorm', $scorm1);

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'version', 'maxgrade',
                                'grademethod', 'whatgrade', 'maxattempt', 'forcecompleted', 'forcenewattempt', 'lastattemptlock',
                                'displayattemptstatus', 'displaycoursestructure', 'sha1hash', 'md5hash', 'revision', 'launch',
                                'skipview', 'hidebrowse', 'hidetoc', 'nav', 'navpositionleft', 'navpositiontop', 'auto',
                                'popup', 'width', 'height', 'timeopen', 'timeclose', 'displayactivityname', 'packagesize',
                                'packageurl', 'scormtype', 'reference');

        // Add expected coursemodule and data.
        $scorm1->coursemodule = $scorm1->cmid;
        $scorm1->section = 0;
        $scorm1->visible = true;
        $scorm1->groupmode = 0;
        $scorm1->groupingid = 0;

        $scorm2->coursemodule = $scorm2->cmid;
        $scorm2->section = 0;
        $scorm2->visible = true;
        $scorm2->groupmode = 0;
        $scorm2->groupingid = 0;

        // SCORM size. The same package is used in both SCORMs.
        $scormcontext1 = context_module::instance($scorm1->cmid);
        $scormcontext2 = context_module::instance($scorm2->cmid);
        $fs = get_file_storage();
        $packagefile = $fs->get_file($scormcontext1->id, 'mod_scorm', 'package', 0, '/', $scorm1->reference);
        $packagesize = $packagefile->get_filesize();

        $packageurl1 = moodle_url::make_webservice_pluginfile_url(
                            $scormcontext1->id, 'mod_scorm', 'package', 0, '/', $scorm1->reference)->out(false);
        $packageurl2 = moodle_url::make_webservice_pluginfile_url(
                            $scormcontext2->id, 'mod_scorm', 'package', 0, '/', $scorm2->reference)->out(false);

        $scorm1->packagesize = $packagesize;
        $scorm1->packageurl = $packageurl1;
        $scorm2->packagesize = $packagesize;
        $scorm2->packageurl = $packageurl2;

        // Forced to boolean as it is returned as PARAM_BOOL.
        $protectpackages = (bool)get_config('scorm', 'protectpackagedownloads');
        $expected1 = array('protectpackagedownloads' => $protectpackages);
        $expected2 = array('protectpackagedownloads' => $protectpackages);
        foreach ($expectedfields as $field) {

            // Since we return the fields used as boolean as PARAM_BOOL instead PARAM_INT we need to force casting here.
            // From the returned fields definition we obtain the type expected for the field.
            if (empty($returndescription->keys['scorms']->content->keys[$field]->type)) {
                continue;
            }
            $fieldtype = $returndescription->keys['scorms']->content->keys[$field]->type;
            if ($fieldtype == PARAM_BOOL) {
                $expected1[$field] = (bool) $scorm1->{$field};
                $expected2[$field] = (bool) $scorm2->{$field};
            } else {
                $expected1[$field] = $scorm1->{$field};
                $expected2[$field] = $scorm2->{$field};
            }
        }
        $expected1['introfiles'] = [];
        $expected2['introfiles'] = [];

        $expectedscorms = array();
        $expectedscorms[] = $expected2;
        $expectedscorms[] = $expected1;

        // Call the external function passing course ids.
        $result = mod_scorm_external::get_scorms_by_courses(array($course2->id, $course1->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedscorms, $result['scorms']);

        // Call the external function without passing course id.
        $result = mod_scorm_external::get_scorms_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedscorms, $result['scorms']);

        // Unenrol user from second course and alter expected scorms.
        $enrol->unenrol_user($instance2, $student->id);
        array_shift($expectedscorms);

        // Call the external function without passing course id.
        $result = mod_scorm_external::get_scorms_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedscorms, $result['scorms']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_scorm_external::get_scorms_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($teacher);

        $additionalfields = array('updatefreq', 'timemodified', 'options',
                                    'completionstatusrequired', 'completionscorerequired', 'completionstatusallscos',
                                    'autocommit', 'section', 'visible', 'groupmode', 'groupingid');

        foreach ($additionalfields as $field) {
            $fieldtype = $returndescription->keys['scorms']->content->keys[$field]->type;

            if ($fieldtype == PARAM_BOOL) {
                $expectedscorms[0][$field] = (bool) $scorm1->{$field};
            } else {
                $expectedscorms[0][$field] = $scorm1->{$field};
            }
        }

        $result = mod_scorm_external::get_scorms_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedscorms, $result['scorms']);

        // Even with the SCORM closed in time teacher should retrieve the info.
        $scorm1->timeopen = $timenow - DAYSECS;
        $scorm1->timeclose = $timenow - HOURSECS;
        $DB->update_record('scorm', $scorm1);

        $expectedscorms[0]['timeopen'] = $scorm1->timeopen;
        $expectedscorms[0]['timeclose'] = $scorm1->timeclose;

        $result = mod_scorm_external::get_scorms_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedscorms, $result['scorms']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_scorm_external::get_scorms_by_courses(array($course1->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedscorms, $result['scorms']);
    }

    /**
     * Test launch_sco
     */
    public function test_launch_sco() {
        global $DB;

        // Test invalid instance id.
        try {
            mod_scorm_external::launch_sco(0);
            $this->fail('Exception expected due to invalid mod_scorm instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            mod_scorm_external::launch_sco($this->scorm->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $scoes = scorm_get_scoes($this->scorm->id);
        foreach ($scoes as $sco) {
            // Find launchable SCO.
            if ($sco->launch != '') {
                break;
            }
        }

        $result = mod_scorm_external::launch_sco($this->scorm->id, $sco->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::launch_sco_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(3, $events);
        $event = array_pop($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_scorm\event\sco_launched', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/scorm/player.php', array('cm' => $this->cm->id, 'scoid' => $sco->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\course_module_completion_updated', $event);

        // Check completion status.
        $completion = new completion_info($this->course);
        $completiondata = $completion->get_data($this->cm);
        $this->assertEquals(COMPLETION_VIEWED, $completiondata->completionstate);

        // Invalid SCO.
        try {
            mod_scorm_external::launch_sco($this->scorm->id, -1);
            $this->fail('Exception expected due to invalid SCO id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotfindsco', $e->errorcode);
        }
    }

    /**
     * Test mod_scorm_get_scorm_access_information.
     */
    public function test_mod_scorm_get_scorm_access_information() {
        global $DB;

        $this->resetAfterTest(true);

        $student = self::getDataGenerator()->create_user();
        $course = self::getDataGenerator()->create_course();
        // Create the scorm.
        $record = new stdClass();
        $record->course = $course->id;
        $scorm = self::getDataGenerator()->create_module('scorm', $record);
        $context = context_module::instance($scorm->cmid);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');

        self::setUser($student);
        $result = mod_scorm_external::get_scorm_access_information($scorm->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_access_information_returns(), $result);

        // Check default values for capabilities.
        $enabledcaps = array('canskipview', 'cansavetrack', 'canviewscores');

        unset($result['warnings']);
        foreach ($result as $capname => $capvalue) {
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }
        // Now, unassign one capability.
        unassign_capability('mod/scorm:viewscores', $studentrole->id);
        array_pop($enabledcaps);
        accesslib_clear_all_caches_for_unit_testing();

        $result = mod_scorm_external::get_scorm_access_information($scorm->id);
        $result = external_api::clean_returnvalue(mod_scorm_external::get_scorm_access_information_returns(), $result);
        unset($result['warnings']);
        foreach ($result as $capname => $capvalue) {
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }
    }
}
