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
 * Unit tests for core analysers.
 *
 * @package   core
 * @category  test
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../analytics/tests/fixtures/test_target_shortname.php');
require_once(__DIR__ . '/../../lib/enrollib.php');

/**
 * Unit tests for core analysers.
 *
 * @package   core
 * @category  test
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_analytics_analysers_testcase extends advanced_testcase {

    /**
     * test_courses_analyser
     *
     * @return void
     */
    public function test_courses_analyser() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $target = new test_target_shortname();
        $analyser = new \core\analytics\analyser\courses(1, $target, [], [], []);
        $analysable = new \core_analytics\course($course);

        $this->assertInstanceOf('\core_analytics\course', $analyser->get_sample_analysable($course->id));

        $this->assertInstanceOf('\context_course', $analyser->sample_access_context($course->id));

        // Just 1 sample per course.
        $class = new ReflectionClass('\core\analytics\analyser\courses');
        $method = $class->getMethod('get_all_samples');
        $method->setAccessible(true);
        list($sampleids, $samplesdata) = $method->invoke($analyser, $analysable);
        $this->assertCount(1, $sampleids);
        $sampleid = reset($sampleids);
        $this->assertEquals($course->id, $sampleid);
        $this->assertEquals($course->fullname, $samplesdata[$sampleid]['course']->fullname);
        $this->assertEquals($coursecontext, $samplesdata[$sampleid]['context']);

        // To compare it later.
        $prevsampledata = $samplesdata[$sampleid];
        list($sampleids, $samplesdata) = $analyser->get_samples(array($sampleid));
        $this->assertEquals($prevsampledata['context'], $samplesdata[$sampleid]['context']);
        $this->assertEquals($prevsampledata['course']->shortname, $samplesdata[$sampleid]['course']->shortname);
    }

    /**
     * test_site_courses_analyser
     *
     * @return void
     */
    public function test_site_courses_analyser() {
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);

        $target = new test_target_shortname();
        $analyser = new \core\analytics\analyser\site_courses(1, $target, [], [], []);
        $analysable = new \core_analytics\site();

        $this->assertInstanceOf('\core_analytics\site', $analyser->get_sample_analysable($course1->id));
        $this->assertInstanceOf('\core_analytics\site', $analyser->get_sample_analysable($course2->id));

        $this->assertInstanceOf('\context_system', $analyser->sample_access_context($course1->id));
        $this->assertInstanceOf('\context_system', $analyser->sample_access_context($course3->id));

        $class = new ReflectionClass('\core\analytics\analyser\site_courses');
        $method = $class->getMethod('get_all_samples');
        $method->setAccessible(true);
        list($sampleids, $samplesdata) = $method->invoke($analyser, $analysable);
        $this->assertCount(3, $sampleids);

        // Use course1 it does not really matter.
        $this->assertArrayHasKey($course1->id, $sampleids);
        $sampleid = $course1->id;
        $this->assertEquals($course1->id, $sampleid);
        $this->assertEquals($course1->fullname, $samplesdata[$sampleid]['course']->fullname);
        $this->assertEquals($course1context, $samplesdata[$sampleid]['context']);

        // To compare it later.
        $prevsampledata = $samplesdata[$sampleid];
        list($sampleids, $samplesdata) = $analyser->get_samples(array($sampleid));
        $this->assertEquals($prevsampledata['context'], $samplesdata[$sampleid]['context']);
        $this->assertEquals($prevsampledata['course']->shortname, $samplesdata[$sampleid]['course']->shortname);
    }

    /**
     * test_site_courses_analyser
     *
     * @return void
     */
    public function test_student_enrolments_analyser() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Checking that suspended users are also included.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student', 'manual', 0, 0, ENROL_USER_SUSPENDED);
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'editingteacher');
        $enrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
        $ue1 = $DB->get_record('user_enrolments', array('userid' => $user1->id, 'enrolid' => $enrol->id));
        $ue2 = $DB->get_record('user_enrolments', array('userid' => $user2->id, 'enrolid' => $enrol->id));

        $target = new test_target_shortname();
        $analyser = new \core\analytics\analyser\student_enrolments(1, $target, [], [], []);
        $analysable = new \core_analytics\course($course);

        $this->assertInstanceOf('\core_analytics\course', $analyser->get_sample_analysable($ue1->id));
        $this->assertInstanceOf('\context_course', $analyser->sample_access_context($ue1->id));

        $class = new ReflectionClass('\core\analytics\analyser\student_enrolments');
        $method = $class->getMethod('get_all_samples');
        $method->setAccessible(true);
        list($sampleids, $samplesdata) = $method->invoke($analyser, $analysable);
        // Only students.
        $this->assertCount(2, $sampleids);

        $this->assertArrayHasKey($ue1->id, $sampleids);
        $this->assertArrayHasKey($ue2->id, $sampleids);

        // Shouldn't matter which one we select.
        $sampleid = $ue1->id;
        $this->assertEquals($ue1, $samplesdata[$sampleid]['user_enrolments']);
        $this->assertEquals($course->fullname, $samplesdata[$sampleid]['course']->fullname);
        $this->assertEquals($coursecontext, $samplesdata[$sampleid]['context']);
        $this->assertEquals($user1->firstname, $samplesdata[$sampleid]['user']->firstname);

        // To compare it later.
        $prevsampledata = $samplesdata[$sampleid];
        list($sampleids, $samplesdata) = $analyser->get_samples(array($sampleid));
        $this->assertEquals($prevsampledata['user_enrolments'], $samplesdata[$sampleid]['user_enrolments']);
        $this->assertEquals($prevsampledata['context'], $samplesdata[$sampleid]['context']);
        $this->assertEquals($prevsampledata['course']->shortname, $samplesdata[$sampleid]['course']->shortname);
        $this->assertEquals($prevsampledata['user']->firstname, $samplesdata[$sampleid]['user']->firstname);
    }
}
