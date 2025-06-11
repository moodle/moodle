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

namespace core\analytics;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../analytics/tests/fixtures/test_target_shortname.php');
require_once(__DIR__ . '/../../../admin/tool/log/store/standard/tests/fixtures/event.php');
require_once(__DIR__ . '/../../../lib/enrollib.php');

/**
 * Unit tests for core indicators.
 *
 * @package   core
 * @category  test
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class indicators_test extends \advanced_testcase {

    /**
     * Test all core indicators.
     *
     * Single method as it is significantly faster (from 13s to 5s) than having separate
     * methods because of preventResetByRollback.
     *
     * @return void
     */
    public function test_core_indicators(): void {
        global $DB;

        $this->preventResetByRollback();
        $this->resetAfterTest(true);
        $this->setAdminuser();

        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Test any access after end.
        $params = array(
            'startdate' => mktime(0, 0, 0, 10, 24, 2015),
            'enddate' => mktime(0, 0, 0, 10, 24, 2016)
        );
        $course = $this->getDataGenerator()->create_course($params);
        $coursecontext = \context_course::instance($course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        $indicator = new \core\analytics\indicator\any_access_after_end();

        $sampleids = array($user1->id => $user1->id, $user2->id => $user2->id);
        $data = array($user1->id => array(
            'context' => $coursecontext,
            'course' => $course,
            'user' => $user1
        ));
        $data[$user2->id] = $data[$user1->id];
        $data[$user2->id]['user'] = $user2;
        $indicator->add_sample_data($data);

        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere');
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        \logstore_standard\event\unittest_executed::create(
            array('context' => $coursecontext, 'userid' => $user1->id))->trigger();
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere');
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        // Test any access before start.
        $params = array(
            'startdate' => SQL_INT_MAX - 1,
            'enddate' => SQL_INT_MAX,
        );
        // Resetting $course var.
        $course = $this->getDataGenerator()->create_course($params);
        $coursecontext = \context_course::instance($course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        $indicator = new \core\analytics\indicator\any_access_before_start();

        $sampleids = array($user1->id => $user1->id, $user2->id => $user2->id);
        $data = array($user1->id => array(
            'context' => $coursecontext,
            'course' => $course,
            'user' => $user1
        ));
        $data[$user2->id] = $data[$user1->id];
        $data[$user2->id]['user'] = $user2;
        $indicator->add_sample_data($data);

        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere');
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        \logstore_standard\event\unittest_executed::create(
            array('context' => $coursecontext, 'userid' => $user1->id))->trigger();
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere');
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        // Test any course access.
        $course = $this->getDataGenerator()->create_course($params);
        $coursecontext = \context_course::instance($course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        $indicator = new \core\analytics\indicator\any_course_access();

        $sampleids = array($user1->id => $user1->id);
        $data = array($user1->id => array(
            'course' => $course,
            'user' => $user1
        ));
        $indicator->add_sample_data($data);
        $analysable = new \core_analytics\course($course);

        // Min value if no user_lastaccess records.
        $indicator->fill_per_analysable_caches($analysable);
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere');
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', time() - 10, time() + 10);
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);

        // Any access is enough if no time restrictions.
        $DB->insert_record('user_lastaccess', array('userid' => $user1->id,
            'courseid' => $course->id, 'timeaccess' => time() - 1));
        $indicator->fill_per_analysable_caches($analysable);
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere');
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);

        // Min value if the existing records are old.
        $indicator->fill_per_analysable_caches($analysable);
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', time(), time() + 10);
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', time());
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);

        // Max value if the existing records are prior to end.
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', time() - 10, time());
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', false, time());
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', false, time());
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);

        // Max value if no end time and existing user_lastaccess record.
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', time() - 10);
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);

        // Rely on logs if the last time access is after the end time.
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', false, time() - 10);
        // Min value if no logs are found.
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);

        \logstore_standard\event\unittest_executed::create(
            array('context' => \context_course::instance($course->id), 'userid' => $user1->id))->trigger();
        // Max value if logs are found before the end time.
        list($values, $unused) = $indicator->calculate($sampleids, 'notrelevanthere', false, time() + 10);
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);

        // Test any write action.
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext2 = \context_course::instance($course2->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);

        $indicator = new \core\analytics\indicator\any_write_action();

        $sampleids = array($user1->id => $user1->id, $user2->id => $user2->id);
        $data = array($user1->id => array(
            'context' => $coursecontext1,
            'course' => $course1,
            'user' => $user1
        ));
        $data[$user2->id] = $data[$user1->id];
        $data[$user2->id]['user'] = $user2;
        $indicator->add_sample_data($data);

        list($values, $unused) = $indicator->calculate($sampleids, 'user');
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        $beforecourseeventcreate = time();
        sleep(1);

        \logstore_standard\event\unittest_executed::create(
            array('context' => $coursecontext1, 'userid' => $user1->id))->trigger();
        list($values, $unused) = $indicator->calculate($sampleids, 'user');
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        // Now try with course-level samples where user is not available.
        $sampleids = array($course1->id => $course1->id, $course2->id => $course2->id);
        $data = array(
            $course1->id => array(
                'context' => $coursecontext1,
                'course' => $course1,
            ),
            $course2->id => array(
                'context' => $coursecontext2,
                'course' => $course2,
            )
        );
        $indicator->clear_sample_data();
        $indicator->add_sample_data($data);

        // Limited by time to avoid previous logs interfering as other logs
        // have been generated by the system.
        list($values, $unused) = $indicator->calculate($sampleids, 'course', $beforecourseeventcreate);
        $this->assertEquals($indicator::get_max_value(), $values[$course1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$course2->id][0]);

        // Test any write action in the course.
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $activity1 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id));
        $activity1context = \context_module::instance($activity1->cmid);
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext2 = \context_course::instance($course2->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);

        $indicator = new \core\analytics\indicator\any_write_action_in_course();

        $sampleids = array($user1->id => $user1->id, $user2->id => $user2->id);
        $data = array($user1->id => array(
            'context' => $coursecontext1,
            'course' => $course1,
            'user' => $user1
        ));
        $data[$user2->id] = $data[$user1->id];
        $data[$user2->id]['user'] = $user2;
        $indicator->add_sample_data($data);

        list($values, $unused) = $indicator->calculate($sampleids, 'user');
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        $beforecourseeventcreate = time();
        sleep(1);

        \logstore_standard\event\unittest_executed::create(
            array('context' => $activity1context, 'userid' => $user1->id))->trigger();
        list($values, $unused) = $indicator->calculate($sampleids, 'user');
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        // Now try with course-level samples where user is not available.
        $sampleids = array($course1->id => $course1->id, $course2->id => $course2->id);
        $data = array(
            $course1->id => array(
                'context' => $coursecontext1,
                'course' => $course1,
            ),
            $course2->id => array(
                'context' => $coursecontext2,
                'course' => $course2,
            )
        );
        $indicator->clear_sample_data();
        $indicator->add_sample_data($data);

        // Limited by time to avoid previous logs interfering as other logs
        // have been generated by the system.
        list($values, $unused) = $indicator->calculate($sampleids, 'course', $beforecourseeventcreate);
        $this->assertEquals($indicator::get_max_value(), $values[$course1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$course2->id][0]);

        // Test read actions.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        $indicator = new \core\analytics\indicator\read_actions();

        $sampleids = array($user1->id => $user1->id, $user2->id => $user2->id);
        $data = array($user1->id => array(
            'context' => $coursecontext,
            'course' => $course,
            'user' => $user1
        ));
        $data[$user2->id] = $data[$user1->id];
        $data[$user2->id]['user'] = $user2;
        $indicator->add_sample_data($data);

        // More or less 4 weeks duration.
        $startdate = time() - (WEEKSECS * 2);
        $enddate = time() + (WEEKSECS * 2);

        $this->setAdminUser();
        list($values, $unused) = $indicator->calculate($sampleids, 'user');
        $this->assertNull($values[$user1->id][0]);
        $this->assertNull($values[$user1->id][1]);
        $this->assertNull($values[$user1->id][0]);
        $this->assertNull($values[$user2->id][1]);

        // Zero score for 0 accesses.
        list($values, $unused) = $indicator->calculate($sampleids, 'user', $startdate, $enddate);
        $this->assertEquals($indicator::get_min_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        // 1/3 score for more than 0 accesses.
        \core\event\course_viewed::create(
            array('context' => $coursecontext, 'userid' => $user1->id))->trigger();
        list($values, $unused) = $indicator->calculate($sampleids, 'user', $startdate, $enddate);
        $this->assertEquals(-0.33, $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        // 2/3 score for more than 1 access per week.
        for ($i = 0; $i < 12; $i++) {
            \core\event\course_viewed::create(
                array('context' => $coursecontext, 'userid' => $user1->id))->trigger();
        }
        list($values, $unused) = $indicator->calculate($sampleids, 'user', $startdate, $enddate);
        $this->assertEquals(0.33, $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        // 100% score for tons of accesses during this period (3 logs per access * 4 weeks * 10 accesses).
        for ($i = 0; $i < (3 * 10 * 4); $i++) {
            \core\event\course_viewed::create(
                array('context' => $coursecontext, 'userid' => $user1->id))->trigger();
        }
        list($values, $unused) = $indicator->calculate($sampleids, 'user', $startdate, $enddate);
        $this->assertEquals($indicator::get_max_value(), $values[$user1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$user2->id][0]);

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }
}
